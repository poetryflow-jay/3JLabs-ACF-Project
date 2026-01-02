<?php
/**
 * 메모리 관리 및 최적화 클래스
 * 
 * [v5.3.6] 신규 생성
 * - 메모리 사용량 모니터링
 * - 메모리 제한 감지 및 대응
 * - 캐시 크기 제한
 * - 대용량 데이터 처리 최적화
 * - 불필요한 데이터 언로드
 * 
 * @package JJ_Style_Guide
 * @since v5.3.6
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // 직접 접근 차단
}

/**
 * 메모리 관리 및 최적화 클래스
 * 
 * [v5.3.6] 128M 환경 등 낮은 메모리 제한 환경에서도 안정적으로 작동하도록 최적화
 * 
 * @package JJ_Style_Guide
 * @since v5.3.6
 * 
 * @method static JJ_Memory_Manager instance() 싱글톤 인스턴스 반환
 * @method int get_current_memory_usage() 현재 메모리 사용량 가져오기 (바이트)
 * @method int get_peak_memory_usage() 피크 메모리 사용량 가져오기 (바이트)
 * @method float get_memory_usage_ratio() 메모리 사용량 비율 계산 (0.0 ~ 1.0)
 * @method array get_stats() 메모리 통계 가져오기
 * @method array get_memory_history(int $limit = 0) 메모리 사용량 히스토리 가져오기
 * @method array process_large_array(array $data, callable $callback, int $chunk_size = 100) 대용량 배열 처리 최적화
 * @method int limit_cache_size(array &$cache, int $max_size = 10485760) 캐시 크기 제한
 * @method int unload_unnecessary_data() 불필요한 데이터 언로드
 * @method array get_memory_limit_info() 메모리 제한 정보 가져오기
 * @method string format_bytes(int $bytes) 바이트를 읽기 쉬운 형식으로 변환
 */
class JJ_Memory_Manager {
    
    /**
     * 싱글톤 인스턴스
     * 
     * @var JJ_Memory_Manager|null
     */
    private static $instance = null;
    
    /**
     * 메모리 제한 (바이트 단위)
     * 
     * @var int
     */
    private $memory_limit = 0;
    
    /**
     * 메모리 사용량 임계값 (메모리 제한의 80%)
     * 
     * @var int
     */
    private $memory_threshold = 0;
    
    /**
     * 메모리 사용량 모니터링 활성화 여부
     * 
     * @var bool
     */
    private $monitoring_enabled = false;
    
    /**
     * 메모리 사용량 히스토리
     * 
     * @var array
     */
    private $memory_history = array();
    
    /**
     * 최대 히스토리 항목 수
     * 
     * @var int
     */
    private $max_history_items = 50;
    
    /**
     * 싱글톤 인스턴스 반환
     * 
     * @return JJ_Memory_Manager
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * 생성자
     * 
     * [v5.3.6] 메모리 제한 감지 및 초기화
     */
    private function __construct() {
        // WordPress 함수 존재 확인
        if ( ! function_exists( 'ini_get' ) ) {
            return;
        }
        
        // 메모리 제한 가져오기
        $this->memory_limit = $this->parse_memory_limit( ini_get( 'memory_limit' ) );
        
        // 메모리 임계값 설정 (메모리 제한의 80%)
        $this->memory_threshold = (int) ( $this->memory_limit * 0.8 );
        
        // 모니터링 활성화 (WP_DEBUG 모드 또는 낮은 메모리 제한 환경)
        $this->monitoring_enabled = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || ( $this->memory_limit < 256 * 1024 * 1024 );
        
        // WordPress가 완전히 로드된 후 초기화
        if ( did_action( 'plugins_loaded' ) ) {
            $this->init();
        } else {
            add_action( 'plugins_loaded', array( $this, 'init' ), 20 );
        }
    }
    
    /**
     * 초기화
     * 
     * [v5.3.6] 메모리 모니터링 및 최적화 훅 등록
     */
    public function init() {
        // WordPress 함수 존재 확인
        if ( ! function_exists( 'add_action' ) ) {
            return;
        }
        
        // 메모리 모니터링 활성화된 경우 훅 등록
        if ( $this->monitoring_enabled ) {
            add_action( 'admin_init', array( $this, 'check_memory_usage' ), 999 );
            add_action( 'wp', array( $this, 'check_memory_usage' ), 999 );
        }
        
        // 메모리 부족 시 자동 정리
        add_action( 'shutdown', array( $this, 'cleanup_on_shutdown' ), 999 );
    }
    
    /**
     * 메모리 제한 파싱
     * 
     * [v5.3.6] PHP ini_get('memory_limit') 값을 바이트 단위로 변환
     * 
     * @param string $memory_limit 메모리 제한 문자열 (예: "128M", "256M")
     * @return int 바이트 단위 메모리 제한
     */
    private function parse_memory_limit( $memory_limit ) {
        if ( empty( $memory_limit ) || $memory_limit === '-1' ) {
            // 제한 없음 (일반적으로 매우 큰 값)
            return 1024 * 1024 * 1024; // 1GB (기본값)
        }
        
        // 숫자와 단위 분리
        $memory_limit = trim( $memory_limit );
        $unit = strtolower( substr( $memory_limit, -1 ) );
        $value = (int) $memory_limit;
        
        // 단위별 변환
        switch ( $unit ) {
            case 'g':
                $value *= 1024;
                // fallthrough
            case 'm':
                $value *= 1024;
                // fallthrough
            case 'k':
                $value *= 1024;
                break;
            default:
                // 바이트 단위로 이미 지정된 경우
                break;
        }
        
        return $value;
    }
    
    /**
     * 현재 메모리 사용량 가져오기
     * 
     * [v5.3.6] 현재 메모리 사용량을 바이트 단위로 반환
     * 
     * @return int 바이트 단위 메모리 사용량
     */
    public function get_current_memory_usage() {
        if ( ! function_exists( 'memory_get_usage' ) ) {
            return 0;
        }
        
        return memory_get_usage( true ); // true = 실제 메모리 사용량
    }
    
    /**
     * 피크 메모리 사용량 가져오기
     * 
     * [v5.3.6] 스크립트 실행 중 최대 메모리 사용량을 바이트 단위로 반환
     * 
     * @return int 바이트 단위 피크 메모리 사용량
     */
    public function get_peak_memory_usage() {
        if ( ! function_exists( 'memory_get_peak_usage' ) ) {
            return 0;
        }
        
        return memory_get_peak_usage( true ); // true = 실제 메모리 사용량
    }
    
    /**
     * 메모리 사용량 비율 계산
     * 
     * [v5.3.6] 메모리 제한 대비 현재 사용량 비율을 반환
     * 
     * @return float 메모리 사용량 비율 (0.0 ~ 1.0)
     */
    public function get_memory_usage_ratio() {
        if ( $this->memory_limit <= 0 ) {
            return 0.0;
        }
        
        $current_usage = $this->get_current_memory_usage();
        return $current_usage / $this->memory_limit;
    }
    
    /**
     * 메모리 사용량 체크
     * 
     * [v5.3.6] 메모리 사용량이 임계값을 초과하면 자동 정리 실행
     */
    public function check_memory_usage() {
        // WordPress 함수 존재 확인
        if ( ! function_exists( 'memory_get_usage' ) ) {
            return;
        }
        
        $current_usage = $this->get_current_memory_usage();
        $usage_ratio = $this->get_memory_usage_ratio();
        
        // 메모리 사용량 히스토리 기록
        $this->record_memory_usage( $current_usage, $usage_ratio );
        
        // 임계값 초과 시 자동 정리
        if ( $current_usage > $this->memory_threshold ) {
            $this->auto_cleanup();
        }
        
        // 메모리 사용량이 90%를 초과하면 경고 로그
        if ( $usage_ratio > 0.9 ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( sprintf(
                    'JJ Memory Manager: 메모리 사용량이 높습니다. 현재: %s / 제한: %s (%.1f%%)',
                    $this->format_bytes( $current_usage ),
                    $this->format_bytes( $this->memory_limit ),
                    $usage_ratio * 100
                ) );
            }
        }
    }
    
    /**
     * 메모리 사용량 기록
     * 
     * [v5.3.6] 메모리 사용량 히스토리에 기록
     * 
     * @param int $usage 현재 메모리 사용량
     * @param float $ratio 메모리 사용량 비율
     */
    private function record_memory_usage( $usage, $ratio ) {
        // 히스토리 항목 추가
        $this->memory_history[] = array(
            'timestamp' => function_exists( 'current_time' ) ? current_time( 'mysql' ) : date( 'Y-m-d H:i:s' ),
            'usage' => $usage,
            'ratio' => $ratio,
        );
        
        // 최대 항목 수 초과 시 오래된 항목 제거
        if ( count( $this->memory_history ) > $this->max_history_items ) {
            array_shift( $this->memory_history );
        }
    }
    
    /**
     * 자동 정리 실행
     * 
     * [v5.3.6] 메모리 사용량이 높을 때 자동으로 캐시 및 불필요한 데이터 정리
     */
    private function auto_cleanup() {
        // WordPress 함수 존재 확인
        if ( ! function_exists( 'error_log' ) ) {
            return;
        }
        
        $before_usage = $this->get_current_memory_usage();
        
        try {
            // 1. 옵션 캐시 정리
            if ( class_exists( 'JJ_Options_Cache' ) ) {
                $options_cache = JJ_Options_Cache::instance();
                if ( method_exists( $options_cache, 'flush' ) ) {
                    $options_cache->flush();
                }
            }
            
            // 2. CSS 메모리 캐시 정리 (Transient는 유지)
            if ( class_exists( 'JJ_CSS_Cache' ) ) {
                $css_cache = JJ_CSS_Cache::instance();
                if ( method_exists( $css_cache, 'clear_memory_cache' ) ) {
                    $css_cache->clear_memory_cache();
                }
            }
            
            // 3. PHP 가비지 컬렉션 강제 실행
            if ( function_exists( 'gc_collect_cycles' ) ) {
                gc_collect_cycles();
            }
            
            $after_usage = $this->get_current_memory_usage();
            $freed = $before_usage - $after_usage;
            
            // 정리 결과 로깅 (디버그 모드에서만)
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG && $freed > 0 ) {
                error_log( sprintf(
                    'JJ Memory Manager: 자동 정리 완료. 해제된 메모리: %s',
                    $this->format_bytes( $freed )
                ) );
            }
            
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Memory Manager: 자동 정리 중 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Memory Manager: 자동 정리 중 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }
    
    /**
     * 종료 시 정리
     * 
     * [v5.3.6] 스크립트 종료 시 메모리 정리 및 최종 체크
     */
    public function cleanup_on_shutdown() {
        // WordPress 함수 존재 확인
        if ( ! function_exists( 'memory_get_usage' ) ) {
            return;
        }
        
        $peak_usage = $this->get_peak_memory_usage();
        $peak_ratio = $this->memory_limit > 0 ? ( $peak_usage / $this->memory_limit ) : 0;
        
        // 피크 메모리 사용량이 90%를 초과하면 경고 로그
        if ( $peak_ratio > 0.9 && function_exists( 'error_log' ) ) {
            error_log( sprintf(
                'JJ Memory Manager: 피크 메모리 사용량이 높습니다. 피크: %s / 제한: %s (%.1f%%)',
                $this->format_bytes( $peak_usage ),
                $this->format_bytes( $this->memory_limit ),
                $peak_ratio * 100
            ) );
        }
        
        // 메모리 히스토리 정리 (최대 항목 수 제한)
        if ( count( $this->memory_history ) > $this->max_history_items ) {
            $this->memory_history = array_slice( $this->memory_history, -$this->max_history_items );
        }
    }
    
    /**
     * 바이트를 읽기 쉬운 형식으로 변환
     * 
     * [v5.3.6] 바이트를 KB, MB, GB 단위로 변환
     * 
     * @param int $bytes 바이트 수
     * @return string 포맷된 문자열 (예: "128 MB")
     */
    public function format_bytes( $bytes ) {
        if ( $bytes >= 1024 * 1024 * 1024 ) {
            return number_format( $bytes / ( 1024 * 1024 * 1024 ), 2 ) . ' GB';
        } elseif ( $bytes >= 1024 * 1024 ) {
            return number_format( $bytes / ( 1024 * 1024 ), 2 ) . ' MB';
        } elseif ( $bytes >= 1024 ) {
            return number_format( $bytes / 1024, 2 ) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }
    
    /**
     * 메모리 통계 가져오기
     * 
     * [v5.3.6] 현재 메모리 사용량 및 통계 정보 반환
     * 
     * @return array 메모리 통계
     */
    public function get_stats() {
        $current_usage = $this->get_current_memory_usage();
        $peak_usage = $this->get_peak_memory_usage();
        $usage_ratio = $this->get_memory_usage_ratio();
        
        return array(
            'memory_limit' => $this->memory_limit,
            'memory_limit_formatted' => $this->format_bytes( $this->memory_limit ),
            'current_usage' => $current_usage,
            'current_usage_formatted' => $this->format_bytes( $current_usage ),
            'peak_usage' => $peak_usage,
            'peak_usage_formatted' => $this->format_bytes( $peak_usage ),
            'usage_ratio' => $usage_ratio,
            'usage_percentage' => round( $usage_ratio * 100, 2 ),
            'available' => max( 0, $this->memory_limit - $current_usage ),
            'available_formatted' => $this->format_bytes( max( 0, $this->memory_limit - $current_usage ) ),
            'threshold' => $this->memory_threshold,
            'threshold_formatted' => $this->format_bytes( $this->memory_threshold ),
            'monitoring_enabled' => $this->monitoring_enabled,
            'history_count' => count( $this->memory_history ),
        );
    }
    
    /**
     * 메모리 사용량 히스토리 가져오기
     * 
     * [v5.3.6] 기록된 메모리 사용량 히스토리 반환
     * 
     * @param int $limit 반환할 항목 수 (기본값: 모든 항목)
     * @return array 메모리 사용량 히스토리
     */
    public function get_memory_history( $limit = 0 ) {
        if ( $limit > 0 && $limit < count( $this->memory_history ) ) {
            return array_slice( $this->memory_history, -$limit );
        }
        
        return $this->memory_history;
    }
    
    /**
     * 대용량 배열 처리 최적화
     * 
     * [v5.3.6] 대용량 배열을 청크 단위로 처리하여 메모리 사용량 최소화
     * 
     * @param array $data 처리할 배열
     * @param callable $callback 각 청크에 대해 실행할 콜백 함수
     * @param int $chunk_size 청크 크기 (기본값: 100)
     * @return array 처리 결과
     */
    public function process_large_array( $data, $callback, $chunk_size = 100 ) {
        // 배열이 아닌 경우 그대로 반환
        if ( ! is_array( $data ) ) {
            return $data;
        }
        
        // 콜백 함수가 호출 가능한지 확인
        if ( ! is_callable( $callback ) ) {
            return $data;
        }
        
        $results = array();
        $chunks = array_chunk( $data, $chunk_size, true ); // 키 보존
        
        foreach ( $chunks as $chunk ) {
            // 각 청크 처리
            $chunk_result = call_user_func( $callback, $chunk );
            
            if ( is_array( $chunk_result ) ) {
                $results = array_merge( $results, $chunk_result );
            } else {
                $results[] = $chunk_result;
            }
            
            // 메모리 사용량 체크
            $this->check_memory_usage();
            
            // 메모리 사용량이 높으면 잠시 대기 (가비지 컬렉션 시간 확보)
            if ( $this->get_memory_usage_ratio() > 0.8 ) {
                if ( function_exists( 'gc_collect_cycles' ) ) {
                    gc_collect_cycles();
                }
                // 짧은 대기 (0.01초)
                if ( function_exists( 'usleep' ) ) {
                    usleep( 10000 ); // 10ms
                }
            }
        }
        
        return $results;
    }
    
    /**
     * 캐시 크기 제한
     * 
     * [v5.3.6] 캐시 크기가 제한을 초과하면 오래된 항목부터 제거
     * 
     * @param array $cache 캐시 배열 (참조 전달)
     * @param int $max_size 최대 크기 (바이트 단위, 기본값: 10MB)
     * @return int 제거된 항목 수
     */
    public function limit_cache_size( &$cache, $max_size = 10 * 1024 * 1024 ) {
        if ( ! is_array( $cache ) ) {
            return 0;
        }
        
        // 현재 캐시 크기 계산
        $current_size = 0;
        $item_sizes = array();
        
        foreach ( $cache as $key => $value ) {
            $item_size = strlen( serialize( $key ) ) + strlen( serialize( $value ) );
            $item_sizes[ $key ] = $item_size;
            $current_size += $item_size;
        }
        
        // 크기가 제한 이하이면 제거 불필요
        if ( $current_size <= $max_size ) {
            return 0;
        }
        
        // 크기 기준으로 정렬 (큰 것부터)
        arsort( $item_sizes );
        
        // 제거할 항목 수 계산
        $removed_count = 0;
        $target_size = $current_size - $max_size;
        $removed_size = 0;
        
        foreach ( $item_sizes as $key => $size ) {
            if ( $removed_size >= $target_size ) {
                break;
            }
            
            unset( $cache[ $key ] );
            $removed_size += $size;
            $removed_count++;
        }
        
        return $removed_count;
    }
    
    /**
     * 불필요한 데이터 언로드
     * 
     * [v5.3.6] 메모리 사용량이 높을 때 불필요한 데이터를 언로드
     * 
     * @return int 해제된 메모리 (바이트 단위, 추정값)
     */
    public function unload_unnecessary_data() {
        $before_usage = $this->get_current_memory_usage();
        
        try {
            // 1. 옵션 캐시 정리
            if ( class_exists( 'JJ_Options_Cache' ) ) {
                $options_cache = JJ_Options_Cache::instance();
                if ( method_exists( $options_cache, 'flush' ) ) {
                    $options_cache->flush();
                }
            }
            
            // 2. CSS 메모리 캐시 정리
            if ( class_exists( 'JJ_CSS_Cache' ) ) {
                $css_cache = JJ_CSS_Cache::instance();
                if ( method_exists( $css_cache, 'clear_memory_cache' ) ) {
                    $css_cache->clear_memory_cache();
                }
            }
            
            // 3. PHP 가비지 컬렉션 강제 실행
            if ( function_exists( 'gc_collect_cycles' ) ) {
                gc_collect_cycles();
            }
            
            $after_usage = $this->get_current_memory_usage();
            return max( 0, $before_usage - $after_usage );
            
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Memory Manager: 데이터 언로드 중 오류 - ' . $e->getMessage() );
            }
            return 0;
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Memory Manager: 데이터 언로드 중 치명적 오류 - ' . $e->getMessage() );
            }
            return 0;
        }
    }
    
    /**
     * 메모리 제한 정보 가져오기
     * 
     * [v5.3.6] 현재 PHP 메모리 제한 정보 반환
     * 
     * @return array 메모리 제한 정보
     */
    public function get_memory_limit_info() {
        return array(
            'limit' => $this->memory_limit,
            'limit_formatted' => $this->format_bytes( $this->memory_limit ),
            'threshold' => $this->memory_threshold,
            'threshold_formatted' => $this->format_bytes( $this->memory_threshold ),
            'is_low_memory' => $this->memory_limit < 256 * 1024 * 1024, // 256MB 미만이면 낮은 메모리 환경
        );
    }
}

