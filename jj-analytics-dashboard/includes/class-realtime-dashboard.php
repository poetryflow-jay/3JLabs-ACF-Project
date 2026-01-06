<?php
/**
 * Realtime Dashboard Widget
 *
 * 실시간 데이터 업데이트 대시보드 위젯
 * WebSocket 또는 AJAX 폴링으로 실시간 데이터 표시
 *
 * @package JJ_Analytics_Dashboard
 * @version 1.1.0
 * @since Phase 49-3
 * @author 3J Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_Realtime_Dashboard {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 캐시 키 접두사
     */
    private $cache_prefix = 'jj_realtime_';

    /**
     * 캐시 만료 시간 (초)
     */
    private $cache_duration = 60;

    /**
     * 싱글톤 인스턴스 반환
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 생성자
     */
    private function __construct() {
        $this->init_hooks();
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // AJAX 핸들러
        add_action( 'wp_ajax_jj_realtime_get_stats', array( $this, 'ajax_get_stats' ) );
        add_action( 'wp_ajax_jj_realtime_get_plugin_status', array( $this, 'ajax_get_plugin_status' ) );
        add_action( 'wp_ajax_jj_realtime_get_system_info', array( $this, 'ajax_get_system_info' ) );
        add_action( 'wp_ajax_jj_realtime_get_activity_log', array( $this, 'ajax_get_activity_log' ) );
        add_action( 'wp_ajax_jj_realtime_get_chart_data', array( $this, 'ajax_get_chart_data' ) );

        // REST API 엔드포인트
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
    }

    /**
     * REST API 라우트 등록
     */
    public function register_rest_routes() {
        register_rest_route( 'jj-analytics/v1', '/stats', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'rest_get_stats' ),
            'permission_callback' => function() {
                return current_user_can( 'manage_options' );
            },
        ) );

        register_rest_route( 'jj-analytics/v1', '/plugins', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'rest_get_plugins' ),
            'permission_callback' => function() {
                return current_user_can( 'manage_options' );
            },
        ) );

        register_rest_route( 'jj-analytics/v1', '/system', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'rest_get_system' ),
            'permission_callback' => function() {
                return current_user_can( 'manage_options' );
            },
        ) );
    }

    /**
     * 실시간 통계 데이터 가져오기
     */
    public function get_realtime_stats() {
        $cache_key = $this->cache_prefix . 'stats';
        $cached = get_transient( $cache_key );

        if ( false !== $cached ) {
            return $cached;
        }

        $stats = array(
            'timestamp'     => current_time( 'timestamp' ),
            'timestamp_iso' => current_time( 'c' ),
            'overview'      => $this->get_overview_stats(),
            'plugins'       => $this->get_plugin_stats(),
            'performance'   => $this->get_performance_stats(),
            'activity'      => $this->get_recent_activity(),
        );

        set_transient( $cache_key, $stats, $this->cache_duration );

        return $stats;
    }

    /**
     * 개요 통계
     */
    private function get_overview_stats() {
        global $wpdb;

        // 3J Labs 플러그인 목록
        $jj_plugins = $this->get_jj_plugins();
        $active_count = 0;

        foreach ( $jj_plugins as $plugin ) {
            if ( is_plugin_active( $plugin['file'] ) ) {
                $active_count++;
            }
        }

        // 이메일 통계 (ACF Mail SMTP)
        $email_stats = $this->get_email_stats();

        // 폼 통계 (ACF Mail SMTP)
        $form_stats = $this->get_form_stats();

        // 스타일 가이드 통계
        $style_stats = $this->get_style_guide_stats();

        return array(
            'total_plugins'  => count( $jj_plugins ),
            'active_plugins' => $active_count,
            'emails_sent'    => $email_stats['total_sent'],
            'emails_today'   => $email_stats['today'],
            'forms_total'    => $form_stats['total'],
            'submissions'    => $form_stats['submissions'],
            'styles_saved'   => $style_stats['saved'],
            'presets_used'   => $style_stats['presets'],
        );
    }

    /**
     * 3J Labs 플러그인 목록 가져오기
     */
    private function get_jj_plugins() {
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $all_plugins = get_plugins();
        $jj_plugins = array();

        // 3J Labs 플러그인 식별
        $jj_identifiers = array(
            'acf-css-really-simple-style-management-center',
            'acf-css-ai-extension',
            'acf-css-neural-link',
            'acf-mail-smtp',
            'acf-nudge-flow',
            'acf-user-journey-analytics',
            'acf-code-snippets-box',
            'acf-css-woocommerce-toolkit',
            'jj-analytics-dashboard',
            'wp-bulk-manager',
        );

        foreach ( $all_plugins as $file => $plugin_data ) {
            foreach ( $jj_identifiers as $id ) {
                if ( strpos( $file, $id ) !== false ) {
                    $jj_plugins[] = array(
                        'file'    => $file,
                        'name'    => $plugin_data['Name'],
                        'version' => $plugin_data['Version'],
                        'active'  => is_plugin_active( $file ),
                    );
                    break;
                }
            }

            // Author가 3J Labs인 경우도 포함
            if ( isset( $plugin_data['Author'] ) && strpos( $plugin_data['Author'], '3J Labs' ) !== false ) {
                $already_added = false;
                foreach ( $jj_plugins as $existing ) {
                    if ( $existing['file'] === $file ) {
                        $already_added = true;
                        break;
                    }
                }
                if ( ! $already_added ) {
                    $jj_plugins[] = array(
                        'file'    => $file,
                        'name'    => $plugin_data['Name'],
                        'version' => $plugin_data['Version'],
                        'active'  => is_plugin_active( $file ),
                    );
                }
            }
        }

        return $jj_plugins;
    }

    /**
     * 플러그인별 통계
     */
    private function get_plugin_stats() {
        $plugins = $this->get_jj_plugins();
        $stats = array();

        foreach ( $plugins as $plugin ) {
            $plugin_id = sanitize_key( dirname( $plugin['file'] ) );

            $stats[ $plugin_id ] = array(
                'name'          => $plugin['name'],
                'version'       => $plugin['version'],
                'active'        => $plugin['active'],
                'health'        => $this->check_plugin_health( $plugin ),
                'last_updated'  => $this->get_plugin_last_update( $plugin ),
            );
        }

        return $stats;
    }

    /**
     * 플러그인 상태 체크
     */
    private function check_plugin_health( $plugin ) {
        // 기본: healthy
        $health = 'healthy';

        if ( ! $plugin['active'] ) {
            $health = 'inactive';
        }

        // 추가 체크: 업데이트 가능 여부, 오류 등
        // 실제 구현에서는 더 상세한 체크 가능

        return $health;
    }

    /**
     * 플러그인 마지막 업데이트 시간
     */
    private function get_plugin_last_update( $plugin ) {
        $plugin_path = WP_PLUGIN_DIR . '/' . dirname( $plugin['file'] );

        if ( is_dir( $plugin_path ) ) {
            $mtime = filemtime( $plugin_path );
            return date( 'Y-m-d H:i:s', $mtime );
        }

        return null;
    }

    /**
     * 이메일 통계
     */
    private function get_email_stats() {
        global $wpdb;

        $table = $wpdb->prefix . 'acf_mail_smtp_emails';

        // 테이블 존재 확인
        $table_exists = $wpdb->get_var( $wpdb->prepare(
            "SHOW TABLES LIKE %s",
            $table
        ) ) === $table;

        if ( ! $table_exists ) {
            return array(
                'total_sent' => 0,
                'today'      => 0,
                'success'    => 0,
                'failed'     => 0,
            );
        }

        $total = $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status = 'sent'" );
        $today = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE status = 'sent' AND DATE(sent_at) = %s",
            current_time( 'Y-m-d' )
        ) );
        $failed = $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status = 'failed'" );

        return array(
            'total_sent' => (int) $total,
            'today'      => (int) $today,
            'success'    => (int) $total,
            'failed'     => (int) $failed,
        );
    }

    /**
     * 폼 통계
     */
    private function get_form_stats() {
        global $wpdb;

        $forms_table = $wpdb->prefix . 'acf_mail_smtp_forms';
        $submissions_table = $wpdb->prefix . 'acf_mail_smtp_submissions';

        // 테이블 존재 확인
        $forms_exists = $wpdb->get_var( $wpdb->prepare(
            "SHOW TABLES LIKE %s",
            $forms_table
        ) ) === $forms_table;

        if ( ! $forms_exists ) {
            return array(
                'total'       => 0,
                'submissions' => 0,
                'today'       => 0,
            );
        }

        $total_forms = $wpdb->get_var( "SELECT COUNT(*) FROM {$forms_table}" );
        $total_submissions = $wpdb->get_var( "SELECT COUNT(*) FROM {$submissions_table}" );
        $today_submissions = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$submissions_table} WHERE DATE(created_at) = %s",
            current_time( 'Y-m-d' )
        ) );

        return array(
            'total'       => (int) $total_forms,
            'submissions' => (int) $total_submissions,
            'today'       => (int) $today_submissions,
        );
    }

    /**
     * 스타일 가이드 통계
     */
    private function get_style_guide_stats() {
        $option_key = 'jj_style_guide_options';
        $options = get_option( $option_key, array() );

        $presets_key = 'jj_style_guide_ai_palette_presets';
        $presets = get_option( $presets_key, array() );

        return array(
            'saved'    => ! empty( $options ) ? 1 : 0,
            'presets'  => is_array( $presets ) ? count( $presets ) : 0,
            'modified' => ! empty( $options ) ? date( 'Y-m-d H:i:s' ) : null,
        );
    }

    /**
     * 성능 통계
     */
    private function get_performance_stats() {
        global $wpdb;

        // 메모리 사용량
        $memory_usage = memory_get_usage( true );
        $memory_limit = ini_get( 'memory_limit' );
        $memory_limit_bytes = $this->convert_to_bytes( $memory_limit );
        $memory_percent = $memory_limit_bytes > 0 ? round( ( $memory_usage / $memory_limit_bytes ) * 100, 2 ) : 0;

        // DB 쿼리 수
        $query_count = $wpdb->num_queries;

        // 페이지 로드 시간 (대략적)
        $load_time = timer_stop( 0, 4 );

        // PHP 버전
        $php_version = phpversion();

        // WP 버전
        $wp_version = get_bloginfo( 'version' );

        return array(
            'memory_usage'   => $this->format_bytes( $memory_usage ),
            'memory_percent' => $memory_percent,
            'memory_limit'   => $memory_limit,
            'query_count'    => $query_count,
            'load_time'      => $load_time . 's',
            'php_version'    => $php_version,
            'wp_version'     => $wp_version,
        );
    }

    /**
     * 최근 활동 로그
     */
    private function get_recent_activity() {
        $activities = array();

        // 최근 이메일 발송
        $recent_emails = $this->get_recent_emails( 5 );
        foreach ( $recent_emails as $email ) {
            $activities[] = array(
                'type'      => 'email',
                'icon'      => 'email-alt',
                'message'   => sprintf( '이메일 발송: %s', $email->to_email ),
                'timestamp' => $email->sent_at,
            );
        }

        // 최근 폼 제출
        $recent_submissions = $this->get_recent_submissions( 5 );
        foreach ( $recent_submissions as $sub ) {
            $activities[] = array(
                'type'      => 'form',
                'icon'      => 'feedback',
                'message'   => sprintf( '폼 제출 (ID: %d)', $sub->form_id ),
                'timestamp' => $sub->created_at,
            );
        }

        // 타임스탬프로 정렬
        usort( $activities, function( $a, $b ) {
            return strtotime( $b['timestamp'] ) - strtotime( $a['timestamp'] );
        } );

        return array_slice( $activities, 0, 10 );
    }

    /**
     * 최근 이메일 가져오기
     */
    private function get_recent_emails( $limit = 5 ) {
        global $wpdb;

        $table = $wpdb->prefix . 'acf_mail_smtp_emails';

        $table_exists = $wpdb->get_var( $wpdb->prepare(
            "SHOW TABLES LIKE %s",
            $table
        ) ) === $table;

        if ( ! $table_exists ) {
            return array();
        }

        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table} ORDER BY created_at DESC LIMIT %d",
            $limit
        ) );

        return $results ? $results : array();
    }

    /**
     * 최근 폼 제출 가져오기
     */
    private function get_recent_submissions( $limit = 5 ) {
        global $wpdb;

        $table = $wpdb->prefix . 'acf_mail_smtp_submissions';

        $table_exists = $wpdb->get_var( $wpdb->prepare(
            "SHOW TABLES LIKE %s",
            $table
        ) ) === $table;

        if ( ! $table_exists ) {
            return array();
        }

        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table} ORDER BY created_at DESC LIMIT %d",
            $limit
        ) );

        return $results ? $results : array();
    }

    /**
     * 차트 데이터 가져오기
     */
    public function get_chart_data( $period = 7, $type = 'emails' ) {
        global $wpdb;

        $data = array(
            'labels' => array(),
            'values' => array(),
        );

        // 날짜 레이블 생성
        for ( $i = $period - 1; $i >= 0; $i-- ) {
            $date = date( 'Y-m-d', strtotime( "-{$i} days" ) );
            $data['labels'][] = date( 'm/d', strtotime( $date ) );
            $data['values'][ $date ] = 0;
        }

        switch ( $type ) {
            case 'emails':
                $table = $wpdb->prefix . 'acf_mail_smtp_emails';
                $table_exists = $wpdb->get_var( $wpdb->prepare(
                    "SHOW TABLES LIKE %s",
                    $table
                ) ) === $table;

                if ( $table_exists ) {
                    $results = $wpdb->get_results( $wpdb->prepare(
                        "SELECT DATE(sent_at) as date, COUNT(*) as count
                         FROM {$table}
                         WHERE sent_at >= DATE_SUB(CURDATE(), INTERVAL %d DAY)
                         AND status = 'sent'
                         GROUP BY DATE(sent_at)",
                        $period
                    ) );

                    foreach ( $results as $row ) {
                        if ( isset( $data['values'][ $row->date ] ) ) {
                            $data['values'][ $row->date ] = (int) $row->count;
                        }
                    }
                }
                break;

            case 'submissions':
                $table = $wpdb->prefix . 'acf_mail_smtp_submissions';
                $table_exists = $wpdb->get_var( $wpdb->prepare(
                    "SHOW TABLES LIKE %s",
                    $table
                ) ) === $table;

                if ( $table_exists ) {
                    $results = $wpdb->get_results( $wpdb->prepare(
                        "SELECT DATE(created_at) as date, COUNT(*) as count
                         FROM {$table}
                         WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL %d DAY)
                         GROUP BY DATE(created_at)",
                        $period
                    ) );

                    foreach ( $results as $row ) {
                        if ( isset( $data['values'][ $row->date ] ) ) {
                            $data['values'][ $row->date ] = (int) $row->count;
                        }
                    }
                }
                break;
        }

        // values를 배열로 변환
        $data['values'] = array_values( $data['values'] );

        return $data;
    }

    // ===== AJAX Handlers =====

    /**
     * AJAX: 실시간 통계
     */
    public function ajax_get_stats() {
        check_ajax_referer( 'jj_analytics_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }

        wp_send_json_success( $this->get_realtime_stats() );
    }

    /**
     * AJAX: 플러그인 상태
     */
    public function ajax_get_plugin_status() {
        check_ajax_referer( 'jj_analytics_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }

        wp_send_json_success( $this->get_plugin_stats() );
    }

    /**
     * AJAX: 시스템 정보
     */
    public function ajax_get_system_info() {
        check_ajax_referer( 'jj_analytics_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }

        wp_send_json_success( $this->get_performance_stats() );
    }

    /**
     * AJAX: 활동 로그
     */
    public function ajax_get_activity_log() {
        check_ajax_referer( 'jj_analytics_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }

        wp_send_json_success( $this->get_recent_activity() );
    }

    /**
     * AJAX: 차트 데이터
     */
    public function ajax_get_chart_data() {
        check_ajax_referer( 'jj_analytics_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }

        $period = isset( $_POST['period'] ) ? absint( $_POST['period'] ) : 7;
        $type = isset( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : 'emails';

        wp_send_json_success( $this->get_chart_data( $period, $type ) );
    }

    // ===== REST API Handlers =====

    /**
     * REST: 통계
     */
    public function rest_get_stats( $request ) {
        return rest_ensure_response( $this->get_realtime_stats() );
    }

    /**
     * REST: 플러그인
     */
    public function rest_get_plugins( $request ) {
        return rest_ensure_response( $this->get_plugin_stats() );
    }

    /**
     * REST: 시스템
     */
    public function rest_get_system( $request ) {
        return rest_ensure_response( $this->get_performance_stats() );
    }

    // ===== Utility Methods =====

    /**
     * 바이트로 변환
     */
    private function convert_to_bytes( $value ) {
        $value = trim( $value );
        $last = strtolower( $value[ strlen( $value ) - 1 ] );
        $value = (int) $value;

        switch ( $last ) {
            case 'g':
                $value *= 1024;
                // fall through
            case 'm':
                $value *= 1024;
                // fall through
            case 'k':
                $value *= 1024;
        }

        return $value;
    }

    /**
     * 바이트 포맷
     */
    private function format_bytes( $bytes, $precision = 2 ) {
        $units = array( 'B', 'KB', 'MB', 'GB', 'TB' );

        $bytes = max( $bytes, 0 );
        $pow = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
        $pow = min( $pow, count( $units ) - 1 );

        $bytes /= pow( 1024, $pow );

        return round( $bytes, $precision ) . ' ' . $units[ $pow ];
    }
}
