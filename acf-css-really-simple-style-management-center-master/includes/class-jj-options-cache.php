<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 옵션 캐싱 클래스 (v5.0.3)
 * 
 * get_option 호출을 최적화하여 데이터베이스 쿼리를 줄입니다.
 * 같은 요청 내에서 동일한 옵션을 여러 번 조회하는 것을 방지합니다.
 * 
 * @package JJ_Style_Guide
 * @since 5.0.3
 * @version 5.3.7
 * 
 * @method static JJ_Options_Cache instance() 싱글톤 인스턴스 반환
 * @method mixed get(string $option_name, mixed $default = false) 옵션 가져오기 (캐시 사용)
 * @method bool set(string $option_name, mixed $value, string|bool $autoload = null) 옵션 설정 (캐시 업데이트)
 * @method bool delete(string $option_name) 옵션 삭제 (캐시 무효화)
 * @method void flush() 모든 캐시 무효화
 * @method array get_batch(array $option_names) [v5.3.7] 배치 옵션 가져오기
 * @method array set_batch(array $options, string|bool $autoload = null) [v5.3.7] 배치 옵션 설정
 * @method array get_stats() 캐시 통계 가져오기
 * @method static mixed get_option(string $option_name, mixed $default = false) [v5.0.4] 정적 헬퍼: 옵션 가져오기
 * @method static bool update_option(string $option_name, mixed $value, string|bool $autoload = null) [v5.0.4] 정적 헬퍼: 옵션 설정
 */
final class JJ_Options_Cache {
    
    private static $instance = null;
    
    // [v5.0.3] 메모리 캐시 (같은 요청 내에서 중복 조회 방지)
    private static $cache = array();
    
    // [v5.0.3] 옵션 변경 추적 (캐시 무효화용)
    private static $option_versions = array();
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
            // [v5.1.8] WordPress 함수 존재 확인 후 훅 초기화
            if ( function_exists( 'add_action' ) && function_exists( 'add_filter' ) ) {
                self::$instance->init_hooks();
            }
        }
        return self::$instance;
    }
    
    /**
     * 훅 초기화
     * [v5.1.8] WordPress 함수 안전 호출
     */
    private function init_hooks() {
        // 옵션 업데이트 시 캐시 무효화
        if ( function_exists( 'add_action' ) ) {
            add_action( 'update_option', array( $this, 'invalidate_option_cache' ), 10, 2 );
            add_action( 'delete_option', array( $this, 'invalidate_option_cache' ), 10, 1 );
        }
        
        // 옵션 버전 추적
        if ( function_exists( 'add_filter' ) ) {
            add_filter( 'pre_update_option', array( $this, 'track_option_version' ), 10, 3 );
        }
    }
    
    /**
     * 옵션 가져오기 (캐시 사용)
     * 
     * @param string $option_name 옵션 이름
     * @param mixed $default 기본값
     * @return mixed 옵션 값
     */
    public function get( $option_name, $default = false ) {
        // 캐시 키 생성
        $cache_key = $this->get_cache_key( $option_name );
        
        // 캐시에 있으면 반환
        if ( isset( self::$cache[ $cache_key ] ) ) {
            return self::$cache[ $cache_key ];
        }
        
        // WordPress의 get_option 사용 (자체 캐싱 활용)
        // [v5.1.8] WordPress 함수 안전 호출
        $value = function_exists( 'get_option' ) ? get_option( $option_name, $default ) : $default;
        
        // 캐시에 저장
        self::$cache[ $cache_key ] = $value;
        
        // [v5.3.6] 메모리 최적화: 캐시 크기 제한 체크
        $this->limit_cache_size();
        
        return $value;
    }
    
    /**
     * 옵션 설정 (캐시 업데이트)
     * 
     * @param string $option_name 옵션 이름
     * @param mixed $value 옵션 값
     * @param string|bool $autoload 'yes' 또는 'no', 또는 false
     * @return bool 성공 여부
     */
    public function set( $option_name, $value, $autoload = null ) {
        // WordPress의 update_option 사용
        // [v5.1.8] WordPress 함수 안전 호출
        if ( ! function_exists( 'update_option' ) ) {
            return false;
        }
        $result = update_option( $option_name, $value, $autoload );
        
        if ( $result ) {
            // 캐시 업데이트
            $cache_key = $this->get_cache_key( $option_name );
            self::$cache[ $cache_key ] = $value;
            
            // 버전 업데이트
            self::$option_versions[ $option_name ] = time();
        }
        
        return $result;
    }
    
    /**
     * 옵션 삭제 (캐시 무효화)
     * 
     * @param string $option_name 옵션 이름
     * @return bool 성공 여부
     */
    public function delete( $option_name ) {
        // [v5.1.8] WordPress 함수 안전 호출
        if ( ! function_exists( 'delete_option' ) ) {
            return false;
        }
        $result = delete_option( $option_name );
        
        if ( $result ) {
            $this->invalidate_option_cache( $option_name );
        }
        
        return $result;
    }
    
    /**
     * 옵션 캐시 무효화
     * 
     * @param string $option_name 옵션 이름
     */
    public function invalidate_option_cache( $option_name, $old_value = null ) {
        $cache_key = $this->get_cache_key( $option_name );
        unset( self::$cache[ $cache_key ] );
        
        // 버전 업데이트
        self::$option_versions[ $option_name ] = time();
    }
    
    /**
     * 모든 캐시 무효화
     * [v5.3.6] 메모리 최적화: 캐시 크기 제한 추가
     */
    public function flush() {
        self::$cache = array();
        self::$option_versions = array();
    }
    
    /**
     * [v5.3.6] 캐시 크기 제한 및 정리
     * 메모리 사용량이 높을 때 오래된 캐시 항목 제거
     */
    public function limit_cache_size() {
        // 메모리 매니저가 있는 경우 사용
        if ( class_exists( 'JJ_Memory_Manager' ) ) {
            $memory_manager = JJ_Memory_Manager::instance();
            $memory_info = $memory_manager->get_memory_limit_info();
            
            // 낮은 메모리 환경(256MB 미만)이거나 사용량이 높은 경우 캐시 크기 제한
            if ( $memory_info['is_low_memory'] || $memory_manager->get_memory_usage_ratio() > 0.7 ) {
                // 최대 캐시 크기: 메모리 제한의 5% 또는 5MB 중 작은 값
                $max_cache_size = min( $memory_info['limit'] * 0.05, 5 * 1024 * 1024 );
                $removed_count = $memory_manager->limit_cache_size( self::$cache, $max_cache_size );
                
                if ( $removed_count > 0 && function_exists( 'error_log' ) && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    error_log( sprintf(
                        'JJ Options Cache: 메모리 최적화를 위해 %d개의 캐시 항목을 제거했습니다.',
                        $removed_count
                    ) );
                }
            }
        }
    }
    
    /**
     * 캐시 키 생성
     * 
     * @param string $option_name 옵션 이름
     * @return string 캐시 키
     */
    private function get_cache_key( $option_name ) {
        // 옵션 버전 포함 (옵션이 변경되면 캐시 키도 변경)
        $version = isset( self::$option_versions[ $option_name ] ) 
            ? self::$option_versions[ $option_name ] 
            : 0;
        
        return $option_name . '_v' . $version;
    }
    
    /**
     * 옵션 버전 추적
     */
    public function track_option_version( $value, $option_name, $old_value ) {
        // 옵션이 실제로 변경되었는지 확인
        if ( $value !== $old_value ) {
            self::$option_versions[ $option_name ] = time();
        }
        
        return $value;
    }
    
    /**
     * 캐시 통계 가져오기
     * 
     * @return array 통계 정보
     */
    public function get_stats() {
        return array(
            'cached_options' => count( self::$cache ),
            'option_versions' => count( self::$option_versions ),
            'memory_usage' => $this->get_memory_usage(),
        );
    }
    
    /**
     * 메모리 사용량 계산
     * 
     * @return int 바이트 단위
     */
    private function get_memory_usage() {
        $size = 0;
        foreach ( self::$cache as $key => $value ) {
            $size += strlen( serialize( $key ) ) + strlen( serialize( $value ) );
        }
        return $size;
    }
    
    /**
     * [v5.0.4] 정적 헬퍼: 옵션 가져오기
     * 
     * @param string $option_name 옵션 이름
     * @param mixed $default 기본값
     * @return mixed 옵션 값
     */
    public static function get_option( $option_name, $default = false ) {
        // 클래스가 로드되지 않은 경우 WordPress 기본 함수 사용
        if ( ! class_exists( __CLASS__ ) || ! method_exists( __CLASS__, 'instance' ) ) {
            return get_option( $option_name, $default );
        }
        try {
            return self::instance()->get( $option_name, $default );
        } catch ( Exception $e ) {
            return get_option( $option_name, $default );
        }
    }
    
    /**
     * [v5.3.7] 배치 옵션 가져오기
     * 여러 옵션을 한 번에 가져와서 데이터베이스 쿼리 최적화
     * 
     * @param array $option_names 옵션 이름 배열
     * @return array 옵션 이름을 키로 하는 배열
     */
    public function get_batch( $option_names ) {
        if ( ! is_array( $option_names ) || empty( $option_names ) ) {
            return array();
        }
        
        $results = array();
        $missing_options = array();
        
        // 캐시에서 먼저 확인
        foreach ( $option_names as $option_name ) {
            $cache_key = $this->get_cache_key( $option_name );
            if ( isset( self::$cache[ $cache_key ] ) ) {
                $results[ $option_name ] = self::$cache[ $cache_key ];
            } else {
                $missing_options[] = $option_name;
            }
        }
        
        // 캐시에 없는 옵션들을 한 번에 로드
        if ( ! empty( $missing_options ) && function_exists( 'get_options' ) ) {
            // WordPress 6.0+ get_options 함수 사용 (배치 로드)
            $loaded_options = get_options( $missing_options );
            foreach ( $loaded_options as $option_name => $value ) {
                $cache_key = $this->get_cache_key( $option_name );
                self::$cache[ $cache_key ] = $value;
                $results[ $option_name ] = $value;
            }
        } elseif ( ! empty( $missing_options ) && function_exists( 'get_option' ) ) {
            // 폴백: 개별 로드 (하지만 캐시에 저장하여 다음 요청에서 재사용)
            foreach ( $missing_options as $option_name ) {
                $value = get_option( $option_name, false );
                $cache_key = $this->get_cache_key( $option_name );
                self::$cache[ $cache_key ] = $value;
                $results[ $option_name ] = $value;
            }
        }
        
        // 메모리 최적화: 캐시 크기 제한 체크
        $this->limit_cache_size();
        
        return $results;
    }
    
    /**
     * [v5.3.7] 배치 옵션 설정
     * 여러 옵션을 한 번에 설정하여 데이터베이스 쿼리 최적화
     * 
     * @param array $options 옵션 이름을 키로 하는 배열
     * @param string|bool $autoload 'yes' 또는 'no', 또는 false
     * @return array 성공/실패 결과
     */
    public function set_batch( $options, $autoload = null ) {
        if ( ! is_array( $options ) || empty( $options ) ) {
            return array();
        }
        
        if ( ! function_exists( 'update_option' ) ) {
            return array();
        }
        
        $results = array();
        
        foreach ( $options as $option_name => $value ) {
            $result = update_option( $option_name, $value, $autoload );
            
            if ( $result ) {
                // 캐시 업데이트
                $cache_key = $this->get_cache_key( $option_name );
                self::$cache[ $cache_key ] = $value;
                
                // 버전 업데이트
                self::$option_versions[ $option_name ] = time();
            }
            
            $results[ $option_name ] = $result;
        }
        
        return $results;
    }
    
    /**
     * [v5.0.4] 정적 헬퍼: 옵션 설정
     * 
     * @param string $option_name 옵션 이름
     * @param mixed $value 옵션 값
     * @param string|bool $autoload 'yes' 또는 'no', 또는 false
     * @return bool 성공 여부
     */
    public static function update_option( $option_name, $value, $autoload = null ) {
        // [v5.1.8] WordPress 함수 안전 호출
        if ( ! function_exists( 'update_option' ) ) {
            return false;
        }
        
        // 클래스가 로드되지 않은 경우 WordPress 기본 함수 사용
        if ( ! class_exists( __CLASS__ ) || ! method_exists( __CLASS__, 'instance' ) ) {
            return update_option( $option_name, $value, $autoload );
        }
        try {
            return self::instance()->set( $option_name, $value, $autoload );
        } catch ( Exception $e ) {
            return update_option( $option_name, $value, $autoload );
        } catch ( Error $e ) {
            return update_option( $option_name, $value, $autoload );
        }
    }
    
    /**
     * [v5.0.4] 정적 헬퍼: 옵션 삭제
     * 
     * @param string $option_name 옵션 이름
     * @return bool 성공 여부
     */
    public static function delete_option( $option_name ) {
        // 클래스가 로드되지 않은 경우 WordPress 기본 함수 사용
        if ( ! class_exists( __CLASS__ ) || ! method_exists( __CLASS__, 'instance' ) ) {
            return delete_option( $option_name );
        }
        try {
            return self::instance()->delete( $option_name );
        } catch ( Exception $e ) {
            return delete_option( $option_name );
        }
    }
}

