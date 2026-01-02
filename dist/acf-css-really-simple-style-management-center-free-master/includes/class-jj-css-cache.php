<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * CSS 캐싱 클래스 (v5.0.3 개선, v5.1.7 WordPress 호환성 강화, v5.3.7 부분 업데이트 추가)
 * 
 * 생성된 CSS를 캐싱하여 성능을 향상시킵니다.
 * 옵션이 변경될 때만 캐시를 갱신합니다.
 * 
 * 개선 사항 (v5.0.3):
 * - 옵션 변경 시 자동 캐시 무효화
 * - 메모리 캐시 추가 (같은 요청 내에서 중복 생성 방지)
 * - 부분 캐시 지원 (변수별로 분리)
 * - 캐시 버전 관리 개선
 * - 캐시 통계 및 모니터링
 * 
 * 개선 사항 (v5.1.7):
 * - WordPress 상수(DAY_IN_SECONDS)를 클래스 속성에서 직접 사용하지 않도록 수정
 * - 생성자에서 WordPress 상수 확인 후 초기화 (폴백 값: 86400)
 * 
 * 개선 사항 (v5.3.7):
 * - 부분 CSS 업데이트 지원 (update_partial_css)
 * - 메모리 캐시 크기 제한 (limit_memory_cache_size)
 * 
 * @package JJ_Style_Guide
 * @since v3.5.0
 * @version 5.3.7
 * 
 * @method static JJ_CSS_Cache instance() 싱글톤 인스턴스 반환
 * @method string|false get(string $type, string $identifier = '') CSS 캐시 가져오기
 * @method bool set(string $type, string $css, string $identifier = '') CSS 캐시 저장
 * @method void flush() 모든 CSS 캐시 삭제
 * @method void flush_type(string $type) 특정 타입의 캐시 삭제
 * @method void flush_section(string $section) [v5.0.3] 부분 캐시: 특정 섹션만 무효화
 * @method bool update_partial_css(string $section, string $css_part) [v5.3.7] 부분 CSS 업데이트
 * @method array get_stats() [v5.0.3] 캐시 통계 가져오기
 * @method void clear_memory_cache() [v5.0.3] 메모리 캐시 초기화
 * @method void limit_memory_cache_size() [v5.3.7] 메모리 캐시 크기 제한 및 정리
 */
final class JJ_CSS_Cache {
    
    private static $instance = null;
    private $cache_key_prefix = 'jj_css_cache_';
    private $cache_expiry = 0; // 생성자에서 초기화 (WordPress 상수 사용)
    
    // [v5.0.3] 메모리 캐시 (같은 요청 내에서 중복 생성 방지)
    private static $memory_cache = array();
    
    // [v5.0.3] 캐시 버전 (옵션 해시 기반)
    private $cache_version_key = 'jj_css_cache_version';
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
            // [v5.0.3] 옵션 변경 시 자동 캐시 무효화 훅 등록
            self::$instance->init_auto_invalidation();
        }
        return self::$instance;
    }
    
    /**
     * 생성자: WordPress 상수 초기화
     */
    private function __construct() {
        // WordPress 상수가 정의되어 있으면 사용, 없으면 기본값 (86400 = 24시간)
        if ( defined( 'DAY_IN_SECONDS' ) ) {
            $this->cache_expiry = DAY_IN_SECONDS;
        } else {
            $this->cache_expiry = 86400; // 24시간 (초 단위)
        }
    }
    
    /**
     * [v5.0.3] 옵션 변경 시 자동 캐시 무효화 초기화
     */
    private function init_auto_invalidation() {
        // 상수 정의 확인
        if ( ! defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ) {
            return;
        }
        
        // 옵션 변경 시 캐시 무효화
        add_action( 'update_option_' . JJ_STYLE_GUIDE_OPTIONS_KEY, array( $this, 'invalidate_on_option_update' ), 10, 2 );
        
        // 플러그인 활성화/비활성화 시 캐시 무효화
        add_action( 'activated_plugin', array( $this, 'maybe_invalidate_on_plugin_change' ), 10, 2 );
        add_action( 'deactivated_plugin', array( $this, 'maybe_invalidate_on_plugin_change' ), 10, 2 );
        
        // 테마 변경 시 캐시 무효화
        add_action( 'switch_theme', array( $this, 'flush' ) );
    }
    
    /**
     * [v5.0.3] 옵션 업데이트 시 캐시 무효화
     */
    public function invalidate_on_option_update( $old_value, $value ) {
        // 옵션이 실제로 변경되었는지 확인
        if ( $old_value === $value ) {
            return;
        }
        
        // 옵션 해시 계산
        $old_hash = $this->get_options_hash( $old_value );
        $new_hash = $this->get_options_hash( $value );
        
        // 해시가 변경되었으면 캐시 무효화
        if ( $old_hash !== $new_hash ) {
            $this->update_cache_version();
            $this->flush();
        }
    }
    
    /**
     * [v5.0.3] 플러그인 변경 시 캐시 무효화 (관련 플러그인인 경우)
     */
    public function maybe_invalidate_on_plugin_change( $plugin, $network_wide ) {
        // 어댑터 플러그인 목록 확인
        $adapter_plugins = array(
            'woocommerce/woocommerce.php',
            'elementor/elementor.php',
            'beaver-builder-lite-version/fl-builder.php',
            // 추가 어댑터 플러그인...
        );
        
        if ( in_array( $plugin, $adapter_plugins, true ) ) {
            $this->flush();
        }
    }
    
    /**
     * [v5.0.3] 옵션 해시 계산
     */
    private function get_options_hash( $options ) {
        // CSS 생성에 영향을 주는 옵션만 해시 계산
        $relevant_options = array();
        
        if ( isset( $options['palettes'] ) ) {
            $relevant_options['palettes'] = $options['palettes'];
        }
        if ( isset( $options['typography'] ) ) {
            $relevant_options['typography'] = $options['typography'];
        }
        if ( isset( $options['buttons'] ) ) {
            $relevant_options['buttons'] = $options['buttons'];
        }
        if ( isset( $options['forms'] ) ) {
            $relevant_options['forms'] = $options['forms'];
        }
        
        return md5( serialize( $relevant_options ) );
    }
    
    /**
     * [v5.0.3] 캐시 버전 업데이트
     */
    private function update_cache_version() {
        update_option( $this->cache_version_key, time() );
    }
    
    /**
     * [v5.0.3] 현재 캐시 버전 가져오기
     */
    private function get_cache_version() {
        return get_option( $this->cache_version_key, 0 );
    }
    
    /**
     * 캐시 키 생성
     * 
     * @param string $type 캐시 타입 (vars, global, adapter 등)
     * @param string $identifier 식별자 (theme slug, plugin slug 등)
     * @return string 캐시 키
     */
    private function get_cache_key( $type, $identifier = '' ) {
        $key = $this->cache_key_prefix . $type;
        if ( ! empty( $identifier ) ) {
            $key .= '_' . sanitize_key( $identifier );
        }
        $key .= '_' . JJ_STYLE_GUIDE_VERSION;
        $key .= '_' . $this->get_cache_version();
        return $key;
    }
    
    /**
     * 캐시된 CSS 가져오기 (메모리 캐시 우선)
     * 
     * @param string $type 캐시 타입
     * @param string $identifier 식별자
     * @return string|false 캐시된 CSS 또는 false
     */
    public function get( $type, $identifier = '' ) {
        // [v5.0.3] 메모리 캐시 확인
        $memory_key = $type . '_' . $identifier;
        if ( isset( self::$memory_cache[ $memory_key ] ) ) {
            return self::$memory_cache[ $memory_key ];
        }
        
        // 디버그 모드에서는 캐시 비활성화 (개발 중에는 항상 최신 CSS 확인)
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG && ! defined( 'JJ_CSS_CACHE_FORCE_ENABLE' ) ) {
            return false;
        }
        
        $cache_key = $this->get_cache_key( $type, $identifier );
        $cached = get_transient( $cache_key );
        
        if ( false !== $cached ) {
            // [v5.0.3] 메모리 캐시에 저장
            self::$memory_cache[ $memory_key ] = $cached;
            return $cached;
        }
        
        return false;
    }
    
    /**
     * CSS 캐시 저장 (메모리 캐시에도 저장)
     * 
     * @param string $type 캐시 타입
     * @param string $css CSS 내용
     * @param string $identifier 식별자
     * @return bool 성공 여부
     */
    public function set( $type, $css, $identifier = '' ) {
        $cache_key = $this->get_cache_key( $type, $identifier );
        
        // [v5.0.3] 메모리 캐시에 저장
        $memory_key = $type . '_' . $identifier;
        self::$memory_cache[ $memory_key ] = $css;
        
        // [v5.3.6] 메모리 최적화: 캐시 크기 제한 체크
        $this->limit_memory_cache_size();
        
        // Transient에 저장
        return set_transient( $cache_key, $css, $this->cache_expiry );
    }
    
    /**
     * 모든 CSS 캐시 삭제 (메모리 캐시 포함)
     */
    public function flush() {
        // [v5.0.3] 메모리 캐시 초기화
        self::$memory_cache = array();
        
        // [v5.0.3] 캐시 버전 업데이트 (모든 캐시 무효화)
        $this->update_cache_version();
        
        global $wpdb;
        
        // Transient 삭제
        $pattern = '_transient_' . $this->cache_key_prefix . '%';
        $wpdb->query( $wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
            $pattern,
            '_transient_timeout_' . $this->cache_key_prefix . '%'
        ) );
    }
    
    /**
     * 특정 타입의 캐시 삭제
     * 
     * @param string $type 캐시 타입
     */
    public function flush_type( $type ) {
        // [v5.0.3] 메모리 캐시에서 해당 타입 삭제
        foreach ( array_keys( self::$memory_cache ) as $key ) {
            if ( strpos( $key, $type . '_' ) === 0 ) {
                unset( self::$memory_cache[ $key ] );
            }
        }
        
        global $wpdb;
        
        $pattern = '_transient_' . $this->cache_key_prefix . $type . '_%';
        $wpdb->query( $wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
            $pattern,
            '_transient_timeout_' . $this->cache_key_prefix . $type . '_%'
        ) );
    }
    
    /**
     * [v5.0.3] 부분 캐시: 특정 섹션만 무효화
     * [v5.3.7] 부분 CSS 업데이트 지원 추가
     * 
     * @param string $section 섹션 이름 (palettes, typography, buttons, forms)
     */
    public function flush_section( $section ) {
        // 섹션별로 관련된 캐시 타입 무효화
        $section_map = array(
            'palettes' => array( 'strategy_1_main_css', 'strategy_1_vars' ),
            'typography' => array( 'strategy_1_main_css', 'strategy_1_global' ),
            'buttons' => array( 'strategy_1_main_css', 'strategy_1_global' ),
            'forms' => array( 'strategy_1_main_css', 'strategy_1_global' ),
        );
        
        if ( isset( $section_map[ $section ] ) ) {
            foreach ( $section_map[ $section ] as $type ) {
                $this->flush_type( $type );
            }
        } else {
            // 알 수 없는 섹션이면 전체 무효화
            $this->flush();
        }
    }
    
    /**
     * [v5.3.7] 부분 CSS 업데이트
     * 전체 CSS를 재생성하지 않고 변경된 부분만 업데이트
     * 
     * @param string $section 섹션 이름
     * @param string $css_part 부분 CSS 내용
     * @return bool 성공 여부
     */
    public function update_partial_css( $section, $css_part ) {
        if ( empty( $section ) || empty( $css_part ) ) {
            return false;
        }
        
        // 전체 CSS 캐시 가져오기
        $cache_key = 'strategy_1_main_css';
        $full_css = $this->get( $cache_key );
        
        if ( empty( $full_css ) ) {
            // 캐시가 없으면 전체 재생성 필요
            return false;
        }
        
        // 섹션별 CSS 패턴 매핑
        $section_patterns = array(
            'palettes' => '/--jj-[^:]+:[^;]+;/',
            'typography' => '/\.jj-preview-[^}]+}/',
            'buttons' => '/\.jj-button[^}]+}/',
            'forms' => '/\.jj-form[^}]+}/',
        );
        
        // 해당 섹션의 CSS를 새 내용으로 교체
        if ( isset( $section_patterns[ $section ] ) ) {
            // 기존 섹션 CSS 제거
            $full_css = preg_replace( $section_patterns[ $section ], '', $full_css );
            // 새 CSS 추가
            $full_css = $css_part . ' ' . $full_css;
            
            // 캐시 업데이트
            return $this->set( $cache_key, $full_css );
        }
        
        return false;
    }
    
    /**
     * [v5.0.3] 캐시 통계 가져오기
     * 
     * @return array 캐시 통계
     */
    public function get_stats() {
        global $wpdb;
        
        $pattern = '_transient_' . $this->cache_key_prefix . '%';
        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT option_name, LENGTH(option_value) as size FROM {$wpdb->options} WHERE option_name LIKE %s",
            $pattern
        ) );
        
        $stats = array(
            'count' => 0,
            'total_size' => 0,
            'memory_cache_count' => count( self::$memory_cache ),
            'items' => array(),
        );
        
        foreach ( $results as $row ) {
            $stats['count']++;
            $stats['total_size'] += (int) $row->size;
            $stats['items'][] = array(
                'key' => $row->option_name,
                'size' => (int) $row->size,
            );
        }
        
        return $stats;
    }
    
    /**
     * [v5.0.3] 메모리 캐시 초기화 (수동)
     * [v5.3.6] 메모리 최적화: 캐시 크기 제한 추가
     */
    public function clear_memory_cache() {
        self::$memory_cache = array();
    }
    
    /**
     * [v5.3.6] 메모리 캐시 크기 제한 및 정리
     * 메모리 사용량이 높을 때 오래된 캐시 항목 제거
     */
    public function limit_memory_cache_size() {
        // 메모리 매니저가 있는 경우 사용
        if ( class_exists( 'JJ_Memory_Manager' ) ) {
            $memory_manager = JJ_Memory_Manager::instance();
            $memory_info = $memory_manager->get_memory_limit_info();
            
            // 낮은 메모리 환경(256MB 미만)이거나 사용량이 높은 경우 캐시 크기 제한
            if ( $memory_info['is_low_memory'] || $memory_manager->get_memory_usage_ratio() > 0.7 ) {
                // 최대 캐시 크기: 메모리 제한의 3% 또는 3MB 중 작은 값
                $max_cache_size = min( $memory_info['limit'] * 0.03, 3 * 1024 * 1024 );
                $removed_count = $memory_manager->limit_cache_size( self::$memory_cache, $max_cache_size );
                
                if ( $removed_count > 0 && function_exists( 'error_log' ) && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    error_log( sprintf(
                        'JJ CSS Cache: 메모리 최적화를 위해 %d개의 메모리 캐시 항목을 제거했습니다.',
                        $removed_count
                    ) );
                }
            }
        }
    }
}
