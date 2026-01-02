<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 9.5] Performance Monitor
 * 
 * 성능 모니터링 시스템
 * - 실시간 성능 메트릭
 * - 성능 알림 시스템
 * - 성능 리포트 자동 생성
 * 
 * @since 9.5.0
 */
class JJ_Performance_Monitor {

    private static $instance = null;
    private $option_key = 'jj_performance_metrics';
    private $alert_key = 'jj_performance_alerts';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_jj_get_performance_metrics', array( $this, 'ajax_get_metrics' ) );
        add_action( 'wp_ajax_jj_generate_performance_report', array( $this, 'ajax_generate_report' ) );
        
        // 성능 측정 (Admin 페이지 로드 시)
        add_action( 'admin_init', array( $this, 'measure_performance' ), 999 );
    }

    /**
     * 에셋 로드
     */
    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'jj-admin-center' ) === false ) {
            return;
        }

        wp_enqueue_script(
            'jj-performance-monitor',
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-performance-monitor.js',
            array( 'jquery', 'jj-common-utils' ),
            defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '9.5.0',
            true
        );

        wp_localize_script(
            'jj-performance-monitor',
            'jjPerformanceMonitor',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'jj_performance_monitor_action' ),
                'thresholds' => $this->get_performance_thresholds(),
            )
        );
    }

    /**
     * 성능 측정
     */
    public function measure_performance() {
        if ( ! is_admin() ) {
            return;
        }

        $screen = get_current_screen();
        if ( ! $screen || strpos( $screen->id, 'jj-admin-center' ) === false ) {
            return;
        }

        $start_time = defined( 'JJ_PERFORMANCE_START_TIME' ) ? JJ_PERFORMANCE_START_TIME : microtime( true );
        $end_time = microtime( true );
        $load_time = ( $end_time - $start_time ) * 1000; // 밀리초

        $metrics = array(
            'timestamp' => current_time( 'mysql' ),
            'page'      => $screen->id,
            'load_time' => round( $load_time, 2 ),
            'memory'    => memory_get_peak_usage( true ),
            'queries'   => $this->count_queries(),
        );

        $this->record_metrics( $metrics );
        $this->check_thresholds( $metrics );
    }

    /**
     * 쿼리 수 카운트
     */
    private function count_queries() {
        global $wpdb;
        
        if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES ) {
            return count( $wpdb->queries ?? array() );
        }

        // Query Monitor 플러그인 사용 가능한 경우
        if ( class_exists( 'QM_Collectors' ) ) {
            $collector = QM_Collectors::get( 'db_queries' );
            if ( $collector ) {
                $data = $collector->get_data();
                return isset( $data['types'] ) ? array_sum( $data['types'] ) : 0;
            }
        }

        return 0;
    }

    /**
     * 메트릭 기록
     */
    private function record_metrics( $metrics ) {
        $all_metrics = get_option( $this->option_key, array() );
        $all_metrics[] = $metrics;

        // 최대 1000개까지만 저장
        if ( count( $all_metrics ) > 1000 ) {
            $all_metrics = array_slice( $all_metrics, -1000 );
        }

        update_option( $this->option_key, $all_metrics );
    }

    /**
     * 성능 임계값 확인
     */
    private function check_thresholds( $metrics ) {
        $thresholds = $this->get_performance_thresholds();
        $alerts = array();

        // 로딩 시간 체크
        if ( $metrics['load_time'] > $thresholds['load_time'] ) {
            $alerts[] = array(
                'type' => 'load_time',
                'message' => sprintf( __( '페이지 로딩 시간이 임계값을 초과했습니다: %dms (임계값: %dms)', 'acf-css-really-simple-style-management-center' ), $metrics['load_time'], $thresholds['load_time'] ),
                'severity' => 'warning',
            );
        }

        // 메모리 사용량 체크
        $memory_mb = $metrics['memory'] / 1024 / 1024;
        if ( $memory_mb > $thresholds['memory'] ) {
            $alerts[] = array(
                'type' => 'memory',
                'message' => sprintf( __( '메모리 사용량이 임계값을 초과했습니다: %.2fMB (임계값: %dMB)', 'acf-css-really-simple-style-management-center' ), $memory_mb, $thresholds['memory'] ),
                'severity' => 'warning',
            );
        }

        // 쿼리 수 체크
        if ( $metrics['queries'] > $thresholds['queries'] ) {
            $alerts[] = array(
                'type' => 'queries',
                'message' => sprintf( __( '데이터베이스 쿼리 수가 임계값을 초과했습니다: %d개 (임계값: %d개)', 'acf-css-really-simple-style-management-center' ), $metrics['queries'], $thresholds['queries'] ),
                'severity' => 'info',
            );
        }

        if ( ! empty( $alerts ) ) {
            $this->record_alerts( $alerts );
        }
    }

    /**
     * 알림 기록
     */
    private function record_alerts( $alerts ) {
        $all_alerts = get_option( $this->alert_key, array() );
        
        foreach ( $alerts as $alert ) {
            $alert['timestamp'] = current_time( 'mysql' );
            $all_alerts[] = $alert;
        }

        // 최대 100개까지만 저장
        if ( count( $all_alerts ) > 100 ) {
            $all_alerts = array_slice( $all_alerts, -100 );
        }

        update_option( $this->alert_key, $all_alerts );
    }

    /**
     * 성능 임계값 가져오기
     */
    private function get_performance_thresholds() {
        return array(
            'load_time' => 2000, // 2초 (밀리초)
            'memory'    => 50,   // 50MB
            'queries'   => 30,   // 30개
        );
    }

    /**
     * 성능 리포트 생성
     */
    public function generate_report( $period = '7days' ) {
        $metrics = $this->get_metrics_by_period( $period );
        
        if ( empty( $metrics ) ) {
            return array(
                'error' => __( '데이터가 없습니다.', 'acf-css-really-simple-style-management-center' ),
            );
        }

        $report = array(
            'period' => $period,
            'summary' => $this->calculate_summary( $metrics ),
            'trends' => $this->analyze_trends( $metrics ),
            'recommendations' => $this->generate_recommendations( $metrics ),
        );

        return $report;
    }

    /**
     * 기간별 메트릭 가져오기
     */
    private function get_metrics_by_period( $period ) {
        $all_metrics = get_option( $this->option_key, array() );
        $cutoff_time = 0;

        switch ( $period ) {
            case '24hours':
                $cutoff_time = strtotime( '-24 hours' );
                break;
            case '7days':
                $cutoff_time = strtotime( '-7 days' );
                break;
            case '30days':
                $cutoff_time = strtotime( '-30 days' );
                break;
            default:
                $cutoff_time = strtotime( '-7 days' );
        }

        $filtered = array_filter( $all_metrics, function( $metric ) use ( $cutoff_time ) {
            return strtotime( $metric['timestamp'] ) >= $cutoff_time;
        } );

        return array_values( $filtered );
    }

    /**
     * 요약 계산
     */
    private function calculate_summary( $metrics ) {
        if ( empty( $metrics ) ) {
            return array();
        }

        $load_times = array_column( $metrics, 'load_time' );
        $memories = array_column( $metrics, 'memory' );
        $queries = array_column( $metrics, 'queries' );

        return array(
            'load_time' => array(
                'avg' => round( array_sum( $load_times ) / count( $load_times ), 2 ),
                'min' => round( min( $load_times ), 2 ),
                'max' => round( max( $load_times ), 2 ),
            ),
            'memory' => array(
                'avg' => round( array_sum( $memories ) / count( $memories ) / 1024 / 1024, 2 ),
                'min' => round( min( $memories ) / 1024 / 1024, 2 ),
                'max' => round( max( $memories ) / 1024 / 1024, 2 ),
            ),
            'queries' => array(
                'avg' => round( array_sum( $queries ) / count( $queries ), 2 ),
                'min' => min( $queries ),
                'max' => max( $queries ),
            ),
        );
    }

    /**
     * 트렌드 분석
     */
    private function analyze_trends( $metrics ) {
        $trends = array(
            'load_time' => 'stable',
            'memory' => 'stable',
            'queries' => 'stable',
        );

        if ( count( $metrics ) < 2 ) {
            return $trends;
        }

        // 최근 절반과 이전 절반 비교
        $mid = floor( count( $metrics ) / 2 );
        $recent = array_slice( $metrics, $mid );
        $previous = array_slice( $metrics, 0, $mid );

        $recent_avg_load = array_sum( array_column( $recent, 'load_time' ) ) / count( $recent );
        $previous_avg_load = array_sum( array_column( $previous, 'load_time' ) ) / count( $previous );

        if ( $recent_avg_load > $previous_avg_load * 1.1 ) {
            $trends['load_time'] = 'increasing';
        } elseif ( $recent_avg_load < $previous_avg_load * 0.9 ) {
            $trends['load_time'] = 'decreasing';
        }

        return $trends;
    }

    /**
     * 권장사항 생성
     */
    private function generate_recommendations( $metrics ) {
        $recommendations = array();
        $summary = $this->calculate_summary( $metrics );
        $thresholds = $this->get_performance_thresholds();

        // 로딩 시간 권장사항
        if ( $summary['load_time']['avg'] > $thresholds['load_time'] ) {
            $recommendations[] = array(
                'type' => 'load_time',
                'priority' => 'high',
                'message' => __( '페이지 로딩 시간이 느립니다. 캐싱을 활성화하거나 불필요한 스크립트를 제거하는 것을 권장합니다.', 'acf-css-really-simple-style-management-center' ),
            );
        }

        // 메모리 권장사항
        if ( $summary['memory']['avg'] > $thresholds['memory'] ) {
            $recommendations[] = array(
                'type' => 'memory',
                'priority' => 'medium',
                'message' => __( '메모리 사용량이 높습니다. 옵션 캐시를 최적화하거나 불필요한 데이터를 정리하는 것을 권장합니다.', 'acf-css-really-simple-style-management-center' ),
            );
        }

        // 쿼리 권장사항
        if ( $summary['queries']['avg'] > $thresholds['queries'] ) {
            $recommendations[] = array(
                'type' => 'queries',
                'priority' => 'medium',
                'message' => __( '데이터베이스 쿼리 수가 많습니다. 옵션 캐시를 활용하거나 배치 로딩을 사용하는 것을 권장합니다.', 'acf-css-really-simple-style-management-center' ),
            );
        }

        return $recommendations;
    }

    /**
     * AJAX: 메트릭 가져오기
     */
    public function ajax_get_metrics() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_performance_monitor_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $period = isset( $_POST['period'] ) ? sanitize_text_field( $_POST['period'] ) : '7days';
        $metrics = $this->get_metrics_by_period( $period );
        $alerts = get_option( $this->alert_key, array() );

        wp_send_json_success( array(
            'metrics' => array_slice( $metrics, -50 ), // 최근 50개만
            'alerts'  => array_slice( $alerts, -20 ),  // 최근 20개만
        ) );
    }

    /**
     * AJAX: 성능 리포트 생성
     */
    public function ajax_generate_report() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_performance_monitor_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $period = isset( $_POST['period'] ) ? sanitize_text_field( $_POST['period'] ) : '7days';
        $report = $this->generate_report( $period );

        wp_send_json_success( $report );
    }
}
