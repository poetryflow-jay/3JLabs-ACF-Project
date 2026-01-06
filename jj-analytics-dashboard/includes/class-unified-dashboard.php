<?php
/**
 * 3J Labs Unified Dashboard - 통합 대시보드
 *
 * 모든 3J Labs 플러그인을 한 화면에서 모니터링하고 관리합니다.
 * Phase 50A의 REST API, Plugin Registry, Event Bus를 활용합니다.
 *
 * @package    JJ_Analytics_Dashboard
 * @subpackage Unified_Dashboard
 * @since      1.2.0
 * @version    1.0.0
 * @author     3J Labs (제이x제니x제이슨 연구소)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 통합 대시보드 클래스
 *
 * @since 1.0.0
 */
class JJ_Unified_Dashboard {

    /**
     * 싱글톤 인스턴스
     *
     * @var JJ_Unified_Dashboard|null
     */
    private static $instance = null;

    /**
     * 메뉴 슬러그
     *
     * @var string
     */
    const MENU_SLUG = '3j-labs-command-center';

    /**
     * 옵션 키
     *
     * @var string
     */
    const OPTION_KEY = '3j_unified_dashboard_settings';

    /**
     * 싱글톤 인스턴스 반환
     *
     * @since 1.0.0
     * @return JJ_Unified_Dashboard
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 생성자
     *
     * @since 1.0.0
     */
    private function __construct() {
        $this->init_hooks();
    }

    /**
     * 훅 초기화
     *
     * @since 1.0.0
     */
    private function init_hooks() {
        // 관리자 메뉴
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 5 );

        // 에셋 로드
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

        // AJAX 핸들러
        add_action( 'wp_ajax_3j_unified_get_overview', array( $this, 'ajax_get_overview' ) );
        add_action( 'wp_ajax_3j_unified_get_plugin_details', array( $this, 'ajax_get_plugin_details' ) );
        add_action( 'wp_ajax_3j_unified_get_health', array( $this, 'ajax_get_health' ) );
        add_action( 'wp_ajax_3j_unified_get_events', array( $this, 'ajax_get_events' ) );
        add_action( 'wp_ajax_3j_unified_quick_action', array( $this, 'ajax_quick_action' ) );
        add_action( 'wp_ajax_3j_unified_save_settings', array( $this, 'ajax_save_settings' ) );

        // 이벤트 구독 (Phase 50A Event Bus 활용)
        add_action( 'plugins_loaded', array( $this, 'subscribe_events' ), 99 );

        // 대시보드 위젯
        add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
    }

    /**
     * 이벤트 구독
     *
     * @since 1.0.0
     */
    public function subscribe_events() {
        if ( ! function_exists( 'jj_subscribe' ) ) {
            return;
        }

        // 플러그인 상태 변경 이벤트 구독
        jj_subscribe( 'plugin_activated', array( $this, 'on_plugin_status_changed' ) );
        jj_subscribe( 'plugin_deactivated', array( $this, 'on_plugin_status_changed' ) );
        jj_subscribe( 'plugin_updated', array( $this, 'on_plugin_updated' ) );
        jj_subscribe( 'settings_changed', array( $this, 'on_settings_changed' ) );
    }

    /**
     * 플러그인 상태 변경 핸들러
     *
     * @since 1.0.0
     * @param array $data 이벤트 데이터
     */
    public function on_plugin_status_changed( $data ) {
        // 캐시 무효화
        delete_transient( '3j_unified_overview_cache' );

        // 활동 로그 기록
        $this->log_activity( 'plugin_status_changed', $data );
    }

    /**
     * 플러그인 업데이트 핸들러
     *
     * @since 1.0.0
     * @param array $data 이벤트 데이터
     */
    public function on_plugin_updated( $data ) {
        delete_transient( '3j_unified_overview_cache' );
        $this->log_activity( 'plugin_updated', $data );
    }

    /**
     * 설정 변경 핸들러
     *
     * @since 1.0.0
     * @param array $data 이벤트 데이터
     */
    public function on_settings_changed( $data ) {
        $this->log_activity( 'settings_changed', $data );
    }

    /**
     * 활동 로그 기록
     *
     * @since 1.0.0
     * @param string $type 활동 유형
     * @param array  $data 활동 데이터
     */
    private function log_activity( $type, $data ) {
        $log = get_option( '3j_unified_activity_log', array() );

        $log[] = array(
            'type'      => $type,
            'data'      => $data,
            'timestamp' => time(),
            'user_id'   => get_current_user_id(),
        );

        // 최대 100개 유지
        if ( count( $log ) > 100 ) {
            $log = array_slice( $log, -100 );
        }

        update_option( '3j_unified_activity_log', $log );
    }

    /**
     * 관리자 메뉴 추가
     *
     * @since 1.0.0
     */
    public function add_admin_menu() {
        add_menu_page(
            '3J Labs Command Center',
            '3J Labs',
            'manage_options',
            self::MENU_SLUG,
            array( $this, 'render_dashboard' ),
            'dashicons-superhero-alt',
            3
        );

        add_submenu_page(
            self::MENU_SLUG,
            'Command Center',
            'Command Center',
            'manage_options',
            self::MENU_SLUG,
            array( $this, 'render_dashboard' )
        );

        add_submenu_page(
            self::MENU_SLUG,
            '플러그인 관리',
            '플러그인 관리',
            'manage_options',
            self::MENU_SLUG . '-plugins',
            array( $this, 'render_plugins_page' )
        );

        add_submenu_page(
            self::MENU_SLUG,
            '시스템 상태',
            '시스템 상태',
            'manage_options',
            self::MENU_SLUG . '-health',
            array( $this, 'render_health_page' )
        );

        add_submenu_page(
            self::MENU_SLUG,
            '이벤트 로그',
            '이벤트 로그',
            'manage_options',
            self::MENU_SLUG . '-events',
            array( $this, 'render_events_page' )
        );

        add_submenu_page(
            self::MENU_SLUG,
            '설정',
            '설정',
            'manage_options',
            self::MENU_SLUG . '-settings',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * 대시보드 위젯 추가
     *
     * @since 1.0.0
     */
    public function add_dashboard_widget() {
        wp_add_dashboard_widget(
            '3j_labs_overview_widget',
            '3J Labs 플러그인 현황',
            array( $this, 'render_dashboard_widget' )
        );
    }

    /**
     * 에셋 로드
     *
     * @since 1.0.0
     * @param string $hook 현재 페이지 훅
     */
    public function enqueue_assets( $hook ) {
        // 3J Labs 페이지에서만 로드
        if ( strpos( $hook, self::MENU_SLUG ) === false && $hook !== 'index.php' ) {
            return;
        }

        // Chart.js
        wp_enqueue_script(
            'chartjs',
            'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js',
            array(),
            '4.4.1',
            true
        );

        // 통합 대시보드 스타일
        wp_enqueue_style(
            '3j-unified-dashboard',
            JJ_ANALYTICS_DASHBOARD_PLUGIN_URL . 'assets/css/unified-dashboard.css',
            array(),
            JJ_ANALYTICS_DASHBOARD_VERSION
        );

        // 통합 대시보드 스크립트
        wp_enqueue_script(
            '3j-unified-dashboard',
            JJ_ANALYTICS_DASHBOARD_PLUGIN_URL . 'assets/js/unified-dashboard.js',
            array( 'jquery', 'chartjs' ),
            JJ_ANALYTICS_DASHBOARD_VERSION,
            true
        );

        wp_localize_script( '3j-unified-dashboard', 'JJ_Unified', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( '3j_unified_nonce' ),
            'i18n'     => array(
                'loading'        => '로딩 중...',
                'error'          => '오류가 발생했습니다.',
                'success'        => '성공적으로 처리되었습니다.',
                'confirm_action' => '이 작업을 진행하시겠습니까?',
                'active'         => '활성화',
                'inactive'       => '비활성화',
                'healthy'        => '정상',
                'warning'        => '경고',
                'critical'       => '위험',
            ),
        ));
    }

    /**
     * 대시보드 렌더링
     *
     * @since 1.0.0
     */
    public function render_dashboard() {
        $overview = $this->get_overview_data();
        ?>
        <div class="wrap jj-unified-dashboard">
            <h1>
                <span class="dashicons dashicons-superhero-alt"></span>
                3J Labs Command Center
            </h1>

            <!-- 퀵 스탯 카드 -->
            <div class="jj-stats-grid">
                <div class="jj-stat-card jj-stat-total">
                    <div class="jj-stat-icon"><span class="dashicons dashicons-admin-plugins"></span></div>
                    <div class="jj-stat-content">
                        <span class="jj-stat-number" id="stat-total"><?php echo esc_html( $overview['total'] ); ?></span>
                        <span class="jj-stat-label">전체 플러그인</span>
                    </div>
                </div>

                <div class="jj-stat-card jj-stat-active">
                    <div class="jj-stat-icon"><span class="dashicons dashicons-yes-alt"></span></div>
                    <div class="jj-stat-content">
                        <span class="jj-stat-number" id="stat-active"><?php echo esc_html( $overview['active'] ); ?></span>
                        <span class="jj-stat-label">활성화됨</span>
                    </div>
                </div>

                <div class="jj-stat-card jj-stat-health">
                    <div class="jj-stat-icon"><span class="dashicons dashicons-heart"></span></div>
                    <div class="jj-stat-content">
                        <span class="jj-stat-number jj-health-<?php echo esc_attr( $overview['health_status'] ); ?>" id="stat-health">
                            <?php echo esc_html( ucfirst( $overview['health_status'] ) ); ?>
                        </span>
                        <span class="jj-stat-label">시스템 상태</span>
                    </div>
                </div>

                <div class="jj-stat-card jj-stat-events">
                    <div class="jj-stat-icon"><span class="dashicons dashicons-chart-line"></span></div>
                    <div class="jj-stat-content">
                        <span class="jj-stat-number" id="stat-events"><?php echo esc_html( $overview['events_today'] ); ?></span>
                        <span class="jj-stat-label">오늘 이벤트</span>
                    </div>
                </div>
            </div>

            <!-- 메인 콘텐츠 그리드 -->
            <div class="jj-dashboard-grid">
                <!-- 플러그인 목록 -->
                <div class="jj-card jj-plugins-card">
                    <div class="jj-card-header">
                        <h2><span class="dashicons dashicons-admin-plugins"></span> 플러그인 현황</h2>
                        <button class="button jj-refresh-btn" data-target="plugins">
                            <span class="dashicons dashicons-update"></span>
                        </button>
                    </div>
                    <div class="jj-card-body">
                        <div id="jj-plugins-list" class="jj-plugins-list">
                            <?php $this->render_plugins_list( $overview['plugins'] ); ?>
                        </div>
                    </div>
                </div>

                <!-- 시스템 상태 -->
                <div class="jj-card jj-health-card">
                    <div class="jj-card-header">
                        <h2><span class="dashicons dashicons-heart"></span> 시스템 상태</h2>
                        <button class="button jj-refresh-btn" data-target="health">
                            <span class="dashicons dashicons-update"></span>
                        </button>
                    </div>
                    <div class="jj-card-body">
                        <div id="jj-health-status">
                            <?php $this->render_health_status( $overview['health'] ); ?>
                        </div>
                    </div>
                </div>

                <!-- 최근 활동 -->
                <div class="jj-card jj-activity-card">
                    <div class="jj-card-header">
                        <h2><span class="dashicons dashicons-clock"></span> 최근 활동</h2>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=' . self::MENU_SLUG . '-events' ) ); ?>" class="button">
                            전체 보기
                        </a>
                    </div>
                    <div class="jj-card-body">
                        <div id="jj-recent-activity">
                            <?php $this->render_recent_activity( $overview['recent_activity'] ); ?>
                        </div>
                    </div>
                </div>

                <!-- 카테고리별 분포 차트 -->
                <div class="jj-card jj-chart-card">
                    <div class="jj-card-header">
                        <h2><span class="dashicons dashicons-chart-pie"></span> 카테고리 분포</h2>
                    </div>
                    <div class="jj-card-body">
                        <canvas id="jj-category-chart"></canvas>
                    </div>
                </div>
            </div>

            <!-- 빠른 작업 -->
            <div class="jj-quick-actions">
                <h3>빠른 작업</h3>
                <div class="jj-action-buttons">
                    <button class="button button-primary jj-quick-action" data-action="check_updates">
                        <span class="dashicons dashicons-update"></span> 업데이트 확인
                    </button>
                    <button class="button jj-quick-action" data-action="clear_cache">
                        <span class="dashicons dashicons-trash"></span> 캐시 초기화
                    </button>
                    <button class="button jj-quick-action" data-action="export_settings">
                        <span class="dashicons dashicons-download"></span> 설정 내보내기
                    </button>
                    <a href="<?php echo esc_url( rest_url( '3j-labs/v1/health' ) ); ?>" class="button" target="_blank">
                        <span class="dashicons dashicons-rest-api"></span> API 상태
                    </a>
                </div>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // 카테고리 차트 초기화
            var categoryData = <?php echo wp_json_encode( $overview['categories'] ); ?>;
            var ctx = document.getElementById('jj-category-chart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(categoryData),
                        datasets: [{
                            data: Object.values(categoryData),
                            backgroundColor: [
                                '#FF6B35',
                                '#3B82F6',
                                '#10B981',
                                '#8B5CF6',
                                '#F59E0B',
                                '#EF4444'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        });
        </script>
        <?php
    }

    /**
     * 플러그인 목록 렌더링
     *
     * @since 1.0.0
     * @param array $plugins 플러그인 목록
     */
    private function render_plugins_list( $plugins ) {
        if ( empty( $plugins ) ) {
            echo '<p class="jj-empty">등록된 플러그인이 없습니다.</p>';
            return;
        }

        foreach ( $plugins as $plugin ) {
            $status_class = $plugin['active'] ? 'active' : 'inactive';
            $status_text  = $plugin['active'] ? '활성화' : '비활성화';
            ?>
            <div class="jj-plugin-item jj-plugin-<?php echo esc_attr( $status_class ); ?>">
                <div class="jj-plugin-info">
                    <span class="jj-plugin-name"><?php echo esc_html( $plugin['name'] ); ?></span>
                    <span class="jj-plugin-version">v<?php echo esc_html( $plugin['version'] ); ?></span>
                </div>
                <div class="jj-plugin-meta">
                    <span class="jj-plugin-category"><?php echo esc_html( $plugin['category'] ); ?></span>
                    <span class="jj-plugin-status jj-status-<?php echo esc_attr( $status_class ); ?>">
                        <?php echo esc_html( $status_text ); ?>
                    </span>
                </div>
            </div>
            <?php
        }
    }

    /**
     * 시스템 상태 렌더링
     *
     * @since 1.0.0
     * @param array $health 상태 데이터
     */
    private function render_health_status( $health ) {
        $items = array(
            array(
                'label'  => 'PHP 버전',
                'value'  => $health['php_version'],
                'status' => version_compare( $health['php_version'], '7.4', '>=' ) ? 'good' : 'warning',
            ),
            array(
                'label'  => 'WordPress 버전',
                'value'  => $health['wp_version'],
                'status' => version_compare( $health['wp_version'], '6.0', '>=' ) ? 'good' : 'warning',
            ),
            array(
                'label'  => '메모리 사용',
                'value'  => $health['memory_usage'],
                'status' => $health['memory_status'],
            ),
            array(
                'label'  => '데이터베이스',
                'value'  => $health['db_version'],
                'status' => 'good',
            ),
        );

        foreach ( $items as $item ) {
            ?>
            <div class="jj-health-item">
                <span class="jj-health-label"><?php echo esc_html( $item['label'] ); ?></span>
                <span class="jj-health-value"><?php echo esc_html( $item['value'] ); ?></span>
                <span class="jj-health-indicator jj-indicator-<?php echo esc_attr( $item['status'] ); ?>"></span>
            </div>
            <?php
        }

        if ( ! empty( $health['issues'] ) ) {
            echo '<div class="jj-health-issues">';
            echo '<h4>발견된 문제</h4>';
            foreach ( $health['issues'] as $issue ) {
                printf(
                    '<div class="jj-issue jj-issue-%s">%s</div>',
                    esc_attr( $issue['type'] ),
                    esc_html( $issue['message'] )
                );
            }
            echo '</div>';
        }
    }

    /**
     * 최근 활동 렌더링
     *
     * @since 1.0.0
     * @param array $activities 활동 목록
     */
    private function render_recent_activity( $activities ) {
        if ( empty( $activities ) ) {
            echo '<p class="jj-empty">최근 활동이 없습니다.</p>';
            return;
        }

        echo '<ul class="jj-activity-list">';
        foreach ( array_slice( $activities, 0, 10 ) as $activity ) {
            $time_ago = human_time_diff( $activity['timestamp'], current_time( 'timestamp' ) );
            $icon     = $this->get_activity_icon( $activity['type'] );
            ?>
            <li class="jj-activity-item">
                <span class="jj-activity-icon dashicons <?php echo esc_attr( $icon ); ?>"></span>
                <span class="jj-activity-text"><?php echo esc_html( $this->get_activity_text( $activity ) ); ?></span>
                <span class="jj-activity-time"><?php echo esc_html( $time_ago ); ?> 전</span>
            </li>
            <?php
        }
        echo '</ul>';
    }

    /**
     * 활동 아이콘 반환
     *
     * @since 1.0.0
     * @param string $type 활동 유형
     * @return string
     */
    private function get_activity_icon( $type ) {
        $icons = array(
            'plugin_activated'       => 'dashicons-yes',
            'plugin_deactivated'     => 'dashicons-no',
            'plugin_updated'         => 'dashicons-update',
            'plugin_status_changed'  => 'dashicons-admin-plugins',
            'settings_changed'       => 'dashicons-admin-generic',
            'error'                  => 'dashicons-warning',
        );

        return isset( $icons[ $type ] ) ? $icons[ $type ] : 'dashicons-marker';
    }

    /**
     * 활동 텍스트 반환
     *
     * @since 1.0.0
     * @param array $activity 활동 데이터
     * @return string
     */
    private function get_activity_text( $activity ) {
        $texts = array(
            'plugin_activated'       => '플러그인 활성화됨',
            'plugin_deactivated'     => '플러그인 비활성화됨',
            'plugin_updated'         => '플러그인 업데이트됨',
            'plugin_status_changed'  => '플러그인 상태 변경',
            'settings_changed'       => '설정 변경됨',
        );

        $base = isset( $texts[ $activity['type'] ] ) ? $texts[ $activity['type'] ] : $activity['type'];

        if ( ! empty( $activity['data']['plugin'] ) ) {
            $base .= ': ' . $activity['data']['plugin'];
        }

        return $base;
    }

    /**
     * 대시보드 위젯 렌더링
     *
     * @since 1.0.0
     */
    public function render_dashboard_widget() {
        $overview = $this->get_overview_data();
        ?>
        <div class="jj-widget-overview">
            <div class="jj-widget-stats">
                <div class="jj-widget-stat">
                    <strong><?php echo esc_html( $overview['active'] ); ?>/<?php echo esc_html( $overview['total'] ); ?></strong>
                    <span>플러그인 활성화</span>
                </div>
                <div class="jj-widget-stat">
                    <strong class="jj-health-<?php echo esc_attr( $overview['health_status'] ); ?>">
                        <?php echo esc_html( ucfirst( $overview['health_status'] ) ); ?>
                    </strong>
                    <span>시스템 상태</span>
                </div>
            </div>
            <p>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=' . self::MENU_SLUG ) ); ?>" class="button button-primary">
                    Command Center 열기
                </a>
            </p>
        </div>
        <?php
    }

    /**
     * 개요 데이터 조회
     *
     * @since 1.0.0
     * @return array
     */
    public function get_overview_data() {
        $cached = get_transient( '3j_unified_overview_cache' );
        if ( $cached !== false ) {
            return $cached;
        }

        global $wpdb;

        // 플러그인 목록 (Phase 50A Registry 활용)
        $plugins = array();
        $categories = array();
        $active_count = 0;

        if ( function_exists( 'jj_registry' ) ) {
            $registry = jj_registry();
            $all_plugins = $registry->get_all();

            foreach ( $all_plugins as $slug => $plugin ) {
                $plugins[] = array(
                    'slug'     => $slug,
                    'name'     => $plugin['name'],
                    'version'  => $plugin['version'] ?: 'N/A',
                    'category' => $plugin['category'],
                    'active'   => $plugin['status'] === 'active',
                );

                if ( $plugin['status'] === 'active' ) {
                    $active_count++;
                }

                $cat = $plugin['category'];
                if ( ! isset( $categories[ $cat ] ) ) {
                    $categories[ $cat ] = 0;
                }
                $categories[ $cat ]++;
            }
        } else {
            // 폴백: REST API 사용
            $plugins = $this->get_plugins_fallback();
            foreach ( $plugins as $plugin ) {
                if ( $plugin['active'] ) {
                    $active_count++;
                }
                $cat = $plugin['category'];
                if ( ! isset( $categories[ $cat ] ) ) {
                    $categories[ $cat ] = 0;
                }
                $categories[ $cat ]++;
            }
        }

        // 시스템 상태
        $health = $this->get_health_data();

        // 최근 활동
        $recent_activity = get_option( '3j_unified_activity_log', array() );
        $recent_activity = array_reverse( $recent_activity );

        // 오늘 이벤트 수
        $today_start = strtotime( 'today' );
        $events_today = 0;
        foreach ( $recent_activity as $activity ) {
            if ( $activity['timestamp'] >= $today_start ) {
                $events_today++;
            }
        }

        $overview = array(
            'total'           => count( $plugins ),
            'active'          => $active_count,
            'plugins'         => $plugins,
            'categories'      => $categories,
            'health'          => $health,
            'health_status'   => $health['status'],
            'recent_activity' => $recent_activity,
            'events_today'    => $events_today,
        );

        set_transient( '3j_unified_overview_cache', $overview, 300 ); // 5분 캐시

        return $overview;
    }

    /**
     * 시스템 상태 데이터 조회
     *
     * @since 1.0.0
     * @return array
     */
    private function get_health_data() {
        global $wpdb;

        $memory_limit = wp_convert_hr_to_bytes( ini_get( 'memory_limit' ) );
        $memory_usage = memory_get_usage();
        $memory_percent = ( $memory_usage / $memory_limit ) * 100;

        $health = array(
            'status'        => 'healthy',
            'php_version'   => phpversion(),
            'wp_version'    => get_bloginfo( 'version' ),
            'db_version'    => $wpdb->db_version(),
            'memory_limit'  => size_format( $memory_limit ),
            'memory_usage'  => size_format( $memory_usage ) . ' (' . round( $memory_percent, 1 ) . '%)',
            'memory_status' => $memory_percent < 80 ? 'good' : ( $memory_percent < 90 ? 'warning' : 'critical' ),
            'issues'        => array(),
        );

        // 문제 감지
        if ( version_compare( phpversion(), '7.4', '<' ) ) {
            $health['issues'][] = array(
                'type'    => 'warning',
                'message' => 'PHP 7.4 이상 버전을 권장합니다.',
            );
        }

        if ( $memory_limit < 256 * 1024 * 1024 ) {
            $health['issues'][] = array(
                'type'    => 'warning',
                'message' => 'PHP 메모리 제한을 256MB 이상으로 설정하세요.',
            );
        }

        if ( $memory_percent >= 90 ) {
            $health['issues'][] = array(
                'type'    => 'error',
                'message' => '메모리 사용량이 높습니다.',
            );
            $health['status'] = 'critical';
        } elseif ( count( $health['issues'] ) > 0 ) {
            $health['status'] = 'warning';
        }

        return $health;
    }

    /**
     * 플러그인 목록 폴백
     *
     * @since 1.0.0
     * @return array
     */
    private function get_plugins_fallback() {
        $plugins = array();

        // 3J Labs 플러그인 목록 (하드코딩 폴백)
        $jj_plugins = array(
            'acf-css-master'              => array( 'name' => 'ACF CSS Manager', 'category' => 'core' ),
            'acf-css-neural-link'         => array( 'name' => 'ACF CSS Neural Link', 'category' => 'core' ),
            'acf-css-ai-extension'        => array( 'name' => 'ACF CSS AI Extension', 'category' => 'extension' ),
            'acf-mail-smtp'               => array( 'name' => 'ACF Mail SMTP', 'category' => 'utility' ),
            'jj-analytics-dashboard'      => array( 'name' => 'JJ Analytics Dashboard', 'category' => 'analytics' ),
            'acf-css-woocommerce-toolkit' => array( 'name' => 'ACF CSS WooCommerce Toolkit', 'category' => 'extension' ),
            'acf-code-snippets-box'       => array( 'name' => 'ACF Code Snippets Box', 'category' => 'core' ),
        );

        foreach ( $jj_plugins as $slug => $info ) {
            $plugins[] = array(
                'slug'     => $slug,
                'name'     => $info['name'],
                'version'  => 'N/A',
                'category' => $info['category'],
                'active'   => true,
            );
        }

        return $plugins;
    }

    /**
     * AJAX: 개요 조회
     *
     * @since 1.0.0
     */
    public function ajax_get_overview() {
        check_ajax_referer( '3j_unified_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        delete_transient( '3j_unified_overview_cache' );
        $overview = $this->get_overview_data();

        wp_send_json_success( $overview );
    }

    /**
     * AJAX: 플러그인 상세 조회
     *
     * @since 1.0.0
     */
    public function ajax_get_plugin_details() {
        check_ajax_referer( '3j_unified_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        $slug = isset( $_POST['slug'] ) ? sanitize_key( $_POST['slug'] ) : '';

        if ( empty( $slug ) ) {
            wp_send_json_error( '플러그인 슬러그가 필요합니다.' );
        }

        if ( function_exists( 'jj_registry' ) ) {
            $plugin = jj_registry()->get( $slug );
            if ( $plugin ) {
                wp_send_json_success( $plugin );
            }
        }

        wp_send_json_error( '플러그인을 찾을 수 없습니다.' );
    }

    /**
     * AJAX: 시스템 상태 조회
     *
     * @since 1.0.0
     */
    public function ajax_get_health() {
        check_ajax_referer( '3j_unified_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        $health = $this->get_health_data();
        wp_send_json_success( $health );
    }

    /**
     * AJAX: 이벤트 조회
     *
     * @since 1.0.0
     */
    public function ajax_get_events() {
        check_ajax_referer( '3j_unified_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        $limit = isset( $_POST['limit'] ) ? absint( $_POST['limit'] ) : 50;
        $events = get_option( '3j_unified_activity_log', array() );
        $events = array_reverse( $events );
        $events = array_slice( $events, 0, $limit );

        wp_send_json_success( $events );
    }

    /**
     * AJAX: 빠른 작업
     *
     * @since 1.0.0
     */
    public function ajax_quick_action() {
        check_ajax_referer( '3j_unified_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        $action = isset( $_POST['quick_action'] ) ? sanitize_key( $_POST['quick_action'] ) : '';

        switch ( $action ) {
            case 'check_updates':
                wp_update_plugins();
                $this->log_activity( 'check_updates', array() );
                wp_send_json_success( '업데이트 확인이 완료되었습니다.' );
                break;

            case 'clear_cache':
                delete_transient( '3j_unified_overview_cache' );
                if ( function_exists( 'wp_cache_flush' ) ) {
                    wp_cache_flush();
                }
                $this->log_activity( 'clear_cache', array() );
                wp_send_json_success( '캐시가 초기화되었습니다.' );
                break;

            case 'export_settings':
                $settings = get_option( self::OPTION_KEY, array() );
                $global   = get_option( '3j_labs_global_settings', array() );
                wp_send_json_success( array(
                    'dashboard' => $settings,
                    'global'    => $global,
                ));
                break;

            default:
                wp_send_json_error( '알 수 없는 작업입니다.' );
        }
    }

    /**
     * AJAX: 설정 저장
     *
     * @since 1.0.0
     */
    public function ajax_save_settings() {
        check_ajax_referer( '3j_unified_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        $settings = isset( $_POST['settings'] ) ? $_POST['settings'] : array();

        // Sanitize
        $sanitized = array();
        foreach ( $settings as $key => $value ) {
            $sanitized[ sanitize_key( $key ) ] = sanitize_text_field( $value );
        }

        update_option( self::OPTION_KEY, $sanitized );
        $this->log_activity( 'settings_changed', $sanitized );

        // 이벤트 발행 (Phase 50A Event Bus)
        if ( function_exists( 'jj_publish' ) ) {
            jj_publish( 'settings_changed', array(
                'plugin'   => 'jj-analytics-dashboard',
                'settings' => $sanitized,
            ));
        }

        wp_send_json_success( '설정이 저장되었습니다.' );
    }

    /**
     * 플러그인 관리 페이지 렌더링
     *
     * @since 1.0.0
     */
    public function render_plugins_page() {
        $overview = $this->get_overview_data();
        ?>
        <div class="wrap jj-unified-dashboard">
            <h1>
                <span class="dashicons dashicons-admin-plugins"></span>
                3J Labs 플러그인 관리
            </h1>

            <div class="jj-plugins-table-wrap">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>플러그인</th>
                            <th>버전</th>
                            <th>카테고리</th>
                            <th>상태</th>
                            <th>작업</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $overview['plugins'] as $plugin ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( $plugin['name'] ); ?></strong></td>
                            <td><?php echo esc_html( $plugin['version'] ); ?></td>
                            <td><span class="jj-category-badge"><?php echo esc_html( $plugin['category'] ); ?></span></td>
                            <td>
                                <span class="jj-status-badge jj-status-<?php echo $plugin['active'] ? 'active' : 'inactive'; ?>">
                                    <?php echo $plugin['active'] ? '활성화' : '비활성화'; ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>" class="button button-small">
                                    관리
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

    /**
     * 시스템 상태 페이지 렌더링
     *
     * @since 1.0.0
     */
    public function render_health_page() {
        $health = $this->get_health_data();
        ?>
        <div class="wrap jj-unified-dashboard">
            <h1>
                <span class="dashicons dashicons-heart"></span>
                시스템 상태
            </h1>

            <div class="jj-health-page">
                <div class="jj-health-overview">
                    <div class="jj-health-status-large jj-health-<?php echo esc_attr( $health['status'] ); ?>">
                        <span class="dashicons dashicons-<?php echo $health['status'] === 'healthy' ? 'yes-alt' : ( $health['status'] === 'warning' ? 'warning' : 'dismiss' ); ?>"></span>
                        <span><?php echo esc_html( ucfirst( $health['status'] ) ); ?></span>
                    </div>
                </div>

                <div class="jj-health-details">
                    <table class="widefat">
                        <tr>
                            <th>PHP 버전</th>
                            <td><?php echo esc_html( $health['php_version'] ); ?></td>
                        </tr>
                        <tr>
                            <th>WordPress 버전</th>
                            <td><?php echo esc_html( $health['wp_version'] ); ?></td>
                        </tr>
                        <tr>
                            <th>데이터베이스 버전</th>
                            <td><?php echo esc_html( $health['db_version'] ); ?></td>
                        </tr>
                        <tr>
                            <th>메모리 제한</th>
                            <td><?php echo esc_html( $health['memory_limit'] ); ?></td>
                        </tr>
                        <tr>
                            <th>메모리 사용</th>
                            <td><?php echo esc_html( $health['memory_usage'] ); ?></td>
                        </tr>
                    </table>
                </div>

                <?php if ( ! empty( $health['issues'] ) ) : ?>
                <div class="jj-health-issues-page">
                    <h2>발견된 문제</h2>
                    <?php foreach ( $health['issues'] as $issue ) : ?>
                    <div class="notice notice-<?php echo $issue['type'] === 'error' ? 'error' : 'warning'; ?>">
                        <p><?php echo esc_html( $issue['message'] ); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * 이벤트 로그 페이지 렌더링
     *
     * @since 1.0.0
     */
    public function render_events_page() {
        $events = get_option( '3j_unified_activity_log', array() );
        $events = array_reverse( $events );
        ?>
        <div class="wrap jj-unified-dashboard">
            <h1>
                <span class="dashicons dashicons-list-view"></span>
                이벤트 로그
            </h1>

            <div class="jj-events-page">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>시간</th>
                            <th>유형</th>
                            <th>내용</th>
                            <th>사용자</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( empty( $events ) ) : ?>
                        <tr>
                            <td colspan="4">이벤트 로그가 없습니다.</td>
                        </tr>
                        <?php else : ?>
                            <?php foreach ( $events as $event ) : ?>
                            <tr>
                                <td><?php echo esc_html( date( 'Y-m-d H:i:s', $event['timestamp'] ) ); ?></td>
                                <td><code><?php echo esc_html( $event['type'] ); ?></code></td>
                                <td><?php echo esc_html( $this->get_activity_text( $event ) ); ?></td>
                                <td><?php echo esc_html( get_user_by( 'id', $event['user_id'] )->display_name ?? 'System' ); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

    /**
     * 설정 페이지 렌더링
     *
     * @since 1.0.0
     */
    public function render_settings_page() {
        $settings = get_option( self::OPTION_KEY, array() );
        ?>
        <div class="wrap jj-unified-dashboard">
            <h1>
                <span class="dashicons dashicons-admin-generic"></span>
                Command Center 설정
            </h1>

            <form id="jj-settings-form" class="jj-settings-form">
                <table class="form-table">
                    <tr>
                        <th>캐시 유효 시간 (초)</th>
                        <td>
                            <input type="number" name="cache_ttl" value="<?php echo esc_attr( $settings['cache_ttl'] ?? 300 ); ?>" min="60" max="3600">
                            <p class="description">대시보드 데이터 캐시 유효 시간</p>
                        </td>
                    </tr>
                    <tr>
                        <th>활동 로그 보존 (개)</th>
                        <td>
                            <input type="number" name="log_retention" value="<?php echo esc_attr( $settings['log_retention'] ?? 100 ); ?>" min="10" max="1000">
                            <p class="description">보존할 활동 로그 항목 수</p>
                        </td>
                    </tr>
                    <tr>
                        <th>대시보드 위젯 표시</th>
                        <td>
                            <label>
                                <input type="checkbox" name="show_widget" value="1" <?php checked( $settings['show_widget'] ?? '1', '1' ); ?>>
                                WordPress 대시보드에 위젯 표시
                            </label>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" class="button button-primary">설정 저장</button>
                </p>
            </form>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('#jj-settings-form').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serializeArray();
                var settings = {};
                formData.forEach(function(item) {
                    settings[item.name] = item.value;
                });

                $.post(JJ_Unified.ajax_url, {
                    action: '3j_unified_save_settings',
                    nonce: JJ_Unified.nonce,
                    settings: settings
                }, function(response) {
                    if (response.success) {
                        alert('설정이 저장되었습니다.');
                    } else {
                        alert('오류: ' + response.data);
                    }
                });
            });
        });
        </script>
        <?php
    }
}
