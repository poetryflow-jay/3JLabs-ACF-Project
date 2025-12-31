<?php
/**
 * 라이센스 캐싱 클래스
 * 
 * @package JJ_LicenseManagerincludes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_Cache {
    
    /**
     * 캐시 그룹
     */
    private static $cache_group = 'jj_license';
    
    /**
     * 캐시 만료 시간 (초)
     */
    private static $cache_expiration = 3600; // 1시간
    
    /**
     * 라이센스 검증 결과 캐싱
     * 
     * @param string $license_key 라이센스 키
     * @param string $site_id 사이트 ID
     * @param array $result 검증 결과
     * @param int $expiration 만료 시간 (초)
     */
    public static function set_verification_result( $license_key, $site_id, $result, $expiration = null ) {
        if ( $expiration === null ) {
            $expiration = self::$cache_expiration;
        }
        
        $cache_key = self::get_cache_key( 'verification', $license_key, $site_id );
        wp_cache_set( $cache_key, $result, self::$cache_group, $expiration );
        
        // 옵션에도 저장 (객체 캐시가 없는 경우 대비)
        $option_key = 'jj_license_cache_' . md5( $license_key . $site_id );
        update_option( $option_key, array(
            'result' => $result,
            'expires' => time() + $expiration,
        ), false );
    }
    
    /**
     * 라이센스 검증 결과 가져오기
     * 
     * @param string $license_key 라이센스 키
     * @param string $site_id 사이트 ID
     * @return array|false 검증 결과 또는 false
     */
    public static function get_verification_result( $license_key, $site_id ) {
        $cache_key = self::get_cache_key( 'verification', $license_key, $site_id );
        $result = wp_cache_get( $cache_key, self::$cache_group );
        
        if ( $result !== false ) {
            return $result;
        }
        
        // 옵션에서 확인
        $option_key = 'jj_license_cache_' . md5( $license_key . $site_id );
        $cached = get_option( $option_key, false );
        
        if ( $cached && isset( $cached['expires'] ) && $cached['expires'] > time() ) {
            // 객체 캐시에도 저장
            wp_cache_set( $cache_key, $cached['result'], self::$cache_group, $cached['expires'] - time() );
            return $cached['result'];
        }
        
        // 만료된 캐시 삭제
        if ( $cached ) {
            delete_option( $option_key );
        }
        
        return false;
    }
    
    /**
     * 라이센스 정보 캐싱
     * 
     * @param string $license_key 라이센스 키
     * @param array $license_data 라이센스 데이터
     * @param int $expiration 만료 시간 (초)
     */
    public static function set_license_data( $license_key, $license_data, $expiration = null ) {
        if ( $expiration === null ) {
            $expiration = self::$cache_expiration;
        }
        
        $cache_key = self::get_cache_key( 'license', $license_key );
        wp_cache_set( $cache_key, $license_data, self::$cache_group, $expiration );
    }
    
    /**
     * 라이센스 정보 가져오기
     * 
     * @param string $license_key 라이센스 키
     * @return array|false 라이센스 데이터 또는 false
     */
    public static function get_license_data( $license_key ) {
        $cache_key = self::get_cache_key( 'license', $license_key );
        return wp_cache_get( $cache_key, self::$cache_group );
    }
    
    /**
     * 캐시 삭제
     * 
     * @param string $license_key 라이센스 키
     * @param string|null $site_id 사이트 ID (선택사항)
     */
    public static function delete_cache( $license_key, $site_id = null ) {
        // 검증 결과 캐시 삭제
        if ( $site_id ) {
            $cache_key = self::get_cache_key( 'verification', $license_key, $site_id );
            wp_cache_delete( $cache_key, self::$cache_group );
            
            $option_key = 'jj_license_cache_' . md5( $license_key . $site_id );
            delete_option( $option_key );
        }
        
        // 라이센스 데이터 캐시 삭제
        $cache_key = self::get_cache_key( 'license', $license_key );
        wp_cache_delete( $cache_key, self::$cache_group );
    }
    
    /**
     * 모든 캐시 삭제
     */
    public static function flush_cache() {
        global $wpdb;
        
        // 옵션 테이블에서 캐시 삭제
        $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'jj_license_cache_%'" );
        
        // 객체 캐시는 자동으로 만료됨
    }
    
    /**
     * 캐시 키 생성
     * 
     * @param string $type 타입
     * @param string $license_key 라이센스 키
     * @param string|null $site_id 사이트 ID
     * @return string 캐시 키
     */
    private static function get_cache_key( $type, $license_key, $site_id = null ) {
        $key = $type . '_' . md5( $license_key );
        if ( $site_id ) {
            $key .= '_' . md5( $site_id );
        }
        return $key;
    }
}

