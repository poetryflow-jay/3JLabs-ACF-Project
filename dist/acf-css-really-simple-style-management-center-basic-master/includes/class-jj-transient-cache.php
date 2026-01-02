<?php
/**
 * Transient Cache Helper
 * 
 * Phase 8.1: 성능 최적화 - 자주 읽히는 옵션을 Transient로 캐싱
 * 
 * @package JJ_Style_Guide
 * @since 8.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Transient 기반 캐싱 헬퍼
 * 자주 읽히지만 자주 변경되지 않는 데이터를 Transient로 캐싱
 */
class JJ_Transient_Cache {
    
    /**
     * 옵션을 Transient로 가져오기
     * 
     * @param string $option_name 옵션 이름
     * @param mixed $default 기본값
     * @param int $expiration 만료 시간 (초), 기본 1시간
     * @return mixed 옵션 값
     */
    public static function get_option( $option_name, $default = false, $expiration = 3600 ) {
        if ( ! function_exists( 'get_transient' ) || ! function_exists( 'set_transient' ) ) {
            return get_option( $option_name, $default );
        }
        
        $transient_key = 'jj_opt_' . md5( $option_name );
        $value = get_transient( $transient_key );
        
        if ( false === $value ) {
            // Transient에 없으면 옵션에서 가져오기
            $value = get_option( $option_name, $default );
            
            // Transient에 저장 (자주 읽히는 옵션만)
            if ( self::should_cache( $option_name ) ) {
                set_transient( $transient_key, $value, $expiration );
            }
        }
        
        return $value;
    }
    
    /**
     * 옵션 업데이트 시 Transient 무효화
     * 
     * @param string $option_name 옵션 이름
     * @param mixed $value 옵션 값
     * @return bool 성공 여부
     */
    public static function update_option( $option_name, $value ) {
        if ( ! function_exists( 'delete_transient' ) ) {
            return update_option( $option_name, $value );
        }
        
        // Transient 무효화
        $transient_key = 'jj_opt_' . md5( $option_name );
        delete_transient( $transient_key );
        
        // 옵션 업데이트
        return update_option( $option_name, $value );
    }
    
    /**
     * 캐싱해야 하는 옵션인지 판단
     * 
     * @param string $option_name 옵션 이름
     * @return bool 캐싱 여부
     */
    private static function should_cache( $option_name ) {
        // 자주 읽히지만 자주 변경되지 않는 옵션 목록
        $cacheable_options = array(
            'jj_style_guide_options',
            'jj_style_guide_update_settings',
            'jj_style_guide_admin_texts',
            'jj_style_guide_section_layout',
            'jj_style_guide_admin_menu_layout',
            'jj_style_guide_admin_menu_colors',
        );
        
        // 접두사 기반 캐싱 (특정 패턴)
        foreach ( $cacheable_options as $pattern ) {
            if ( 0 === strpos( $option_name, $pattern ) ) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 배치 옵션 가져오기 (Transient 활용)
     * 
     * @param array $option_names 옵션 이름 배열
     * @param int $expiration 만료 시간
     * @return array 옵션 값 배열
     */
    public static function get_batch( $option_names, $expiration = 3600 ) {
        if ( ! is_array( $option_names ) || empty( $option_names ) ) {
            return array();
        }
        
        $results = array();
        $missing = array();
        
        // Transient에서 먼저 확인
        foreach ( $option_names as $option_name ) {
            if ( ! self::should_cache( $option_name ) ) {
                // 캐싱하지 않는 옵션은 즉시 조회
                $results[ $option_name ] = get_option( $option_name, false );
                continue;
            }
            
            $transient_key = 'jj_opt_' . md5( $option_name );
            $value = get_transient( $transient_key );
            
            if ( false !== $value ) {
                $results[ $option_name ] = $value;
            } else {
                $missing[] = $option_name;
            }
        }
        
        // Transient에 없는 옵션들을 배치로 로드
        if ( ! empty( $missing ) ) {
            // Options Cache의 배치 로드 활용
            if ( class_exists( 'JJ_Options_Cache' ) ) {
                $loaded = JJ_Options_Cache::instance()->get_batch( $missing );
            } else {
                // 폴백: 개별 로드
                $loaded = array();
                foreach ( $missing as $option_name ) {
                    $loaded[ $option_name ] = get_option( $option_name, false );
                }
            }
            
            // Transient에 저장
            foreach ( $loaded as $option_name => $value ) {
                $results[ $option_name ] = $value;
                if ( self::should_cache( $option_name ) && function_exists( 'set_transient' ) ) {
                    $transient_key = 'jj_opt_' . md5( $option_name );
                    set_transient( $transient_key, $value, $expiration );
                }
            }
        }
        
        return $results;
    }
    
    /**
     * 모든 관련 Transient 무효화
     */
    public static function flush() {
        global $wpdb;
        
        if ( ! $wpdb || ! method_exists( $wpdb, 'query' ) ) {
            return;
        }
        
        // jj_opt_ 접두사를 가진 모든 Transient 삭제
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
             WHERE option_name LIKE '_transient_jj_opt_%' 
             OR option_name LIKE '_transient_timeout_jj_opt_%'"
        );
        
        // 사이트 옵션 (멀티사이트)
        if ( is_multisite() ) {
            $wpdb->query(
                "DELETE FROM {$wpdb->sitemeta} 
                 WHERE meta_key LIKE '_transient_jj_opt_%' 
                 OR meta_key LIKE '_transient_timeout_jj_opt_%'"
            );
        }
    }
}

// 옵션 업데이트 시 Transient 자동 무효화
add_action( 'update_option', function( $option_name ) {
    if ( JJ_Transient_Cache::should_cache( $option_name ) ) {
        $transient_key = 'jj_opt_' . md5( $option_name );
        delete_transient( $transient_key );
    }
}, 10, 1 );
