<?php
/**
 * 멀티사이트 중앙관리 허브
 *
 * Phase 50B P50-4: 멀티사이트 환경에서 모든 서브사이트의 3J Labs 플러그인을
 * 중앙에서 관리하고 모니터링하는 기능을 제공합니다.
 *
 * @package    ACF_CSS_Neural_Link
 * @subpackage Multisite
 * @since      8.2.0
 * @author     3J Labs (제이x제니x제이슨 연구소)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 멀티사이트 허브 클래스
 *
 * @since 8.2.0
 */
class JJ_Multisite_Hub {

    /**
     * 싱글톤 인스턴스
     *
     * @var JJ_Multisite_Hub
     */
    private static $instance = null;

    /**
     * 메뉴 슬러그
     *
     * @var string
     */
    const MENU_SLUG = '3j-multisite-hub';

    /**
     * 캐시 그룹
     *
     * @var string
     */
    const CACHE_GROUP = '3j_multisite_hub';

    /**
     * 캐시 만료 시간 (5분)
     *
     * @var int
     */
    const CACHE_EXPIRY = 300;

    /**
     * 관리 대상 플러그인 목록
     *
     * @var array
     */
    private $managed_plugins = array(
        'acf-css-really-simple-style-management-center-master' => array(
            'name'     => 'ACF CSS Manager',
            'category' => 'core',
            'file'     => 'acf-css-really-simple-style-guide.php',
        ),
        'acf-css-neural-link' => array(
            'name'     => '3J Neural Link',
            'category' => 'core',
            'file'     => 'acf-css-neural-link.php',
        ),
        'acf-css-ai-extension' => array(
            'name'     => 'ACF CSS AI Extension',
            'category' => 'ai',
            'file'     => 'acf-css-ai-extension.php',
        ),
        'acf-mail-smtp' => array(
            'name'     => 'ACF Mail SMTP',
            'category' => 'email',
            'file'     => 'acf-mail-smtp.php',
        ),
        'acf-nudge-flow' => array(
            'name'     => 'ACF Nudge Flow',
            'category' => 'marketing',
            'file'     => 'acf-nudge-flow.php',
        ),
        'wp-bulk-manager' => array(
            'name'     => 'WP Bulk Manager',
            'category' => 'utility',
            'file'     => 'wp-bulk-installer.php',
        ),
        'admin-menu-editor-pro' => array(
            'name'     => 'Admin Menu Editor Pro',
            'category' => 'utility',
            'file'     => 'admin-menu-editor-pro.php',
        ),
        'acf-css-woo-license' => array(
            'name'     => 'ACF CSS WooCommerce License',
            'category' => 'commerce',
            'file'     => 'acf-css-woo-license.php',
        ),
        'acf-user-journey-analytics' => array(
            'name'     => 'ACF User Journey Analytics',
            'category' => 'analytics',
            'file'     => 'acf-user-journey-analytics.php',
        ),
        'jj-marketing-automation-dashboard' => array(
            'name'     => 'JJ Marketing Automation',
            'category' => 'marketing',
            'file'     => 'jj-marketing-automation-dashboard.php',
        ),
        'jj-analytics-dashboard' => array(
            'name'     => 'JJ Analytics Dashboard',
            'category' => 'analytics',
            'file'     => 'jj-analytics-dashboard.php',
        ),
    );

    /**
     * 싱글톤 인스턴스 반환
     *
     * @return JJ_Multisite_Hub
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
        // 멀티사이트가 아니면 초기화하지 않음
        if ( ! is_multisite() ) {
            return;
        }

        $this->init_hooks();
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // 네트워크 관리자 메뉴
        add_action( 'network_admin_menu', array( $this, 'add_network_menu' ) );

        // AJAX 핸들러
        add_action( 'wp_ajax_3j_multisite_get_sites', array( $this, 'ajax_get_sites' ) );
        add_action( 'wp_ajax_3j_multisite_get_site_details', array( $this, 'ajax_get_site_details' ) );
        add_action( 'wp_ajax_3j_multisite_sync_plugins', array( $this, 'ajax_sync_plugins' ) );
        add_action( 'wp_ajax_3j_multisite_bulk_action', array( $this, 'ajax_bulk_action' ) );
        add_action( 'wp_ajax_3j_multisite_get_network_stats', array( $this, 'ajax_get_network_stats' ) );

        // REST API 엔드포인트
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );

        // 에셋 로드
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    /**
     * 네트워크 관리자 메뉴 추가
     */
    public function add_network_menu() {
        add_menu_page(
            '3J Labs Multisite Hub',
            '3J Multisite Hub',
            'manage_network',
            self::MENU_SLUG,
            array( $this, 'render_hub_page' ),
            'dashicons-networking',
            25
        );

        add_submenu_page(
            self::MENU_SLUG,
            '사이트 관리',
            '사이트 관리',
            'manage_network',
            self::MENU_SLUG,
            array( $this, 'render_hub_page' )
        );

        add_submenu_page(
            self::MENU_SLUG,
            '플러그인 동기화',
            '플러그인 동기화',
            'manage_network',
            self::MENU_SLUG . '-sync',
            array( $this, 'render_sync_page' )
        );

        add_submenu_page(
            self::MENU_SLUG,
            '네트워크 상태',
            '네트워크 상태',
            'manage_network',
            self::MENU_SLUG . '-health',
            array( $this, 'render_health_page' )
        );

        add_submenu_page(
            self::MENU_SLUG,
            '설정',
            '설정',
            'manage_network',
            self::MENU_SLUG . '-settings',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * 에셋 로드
     *
     * @param string $hook 현재 페이지 훅
     */
    public function enqueue_assets( $hook ) {
        // 멀티사이트 허브 페이지에서만 로드
        if ( strpos( $hook, self::MENU_SLUG ) === false ) {
            return;
        }

        // CSS
        wp_enqueue_style(
            'jj-multisite-hub',
            JJ_NEURAL_LINK_URL . 'assets/css/multisite-hub.css',
            array(),
            JJ_NEURAL_LINK_VERSION
        );

        // JS
        wp_enqueue_script(
            'jj-multisite-hub',
            JJ_NEURAL_LINK_URL . 'assets/js/multisite-hub.js',
            array( 'jquery' ),
            JJ_NEURAL_LINK_VERSION,
            true
        );

        wp_localize_script( 'jj-multisite-hub', 'JJ_Multisite', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'rest_url' => rest_url( '3j-multisite/v1/' ),
            'nonce'    => wp_create_nonce( '3j_multisite_nonce' ),
            'i18n'     => array(
                'loading'       => __( '로딩 중...', 'acf-css-neural-link' ),
                'error'         => __( '오류가 발생했습니다.', 'acf-css-neural-link' ),
                'success'       => __( '성공적으로 완료되었습니다.', 'acf-css-neural-link' ),
                'confirm_sync'  => __( '선택한 사이트에 플러그인을 동기화하시겠습니까?', 'acf-css-neural-link' ),
                'active'        => __( '활성', 'acf-css-neural-link' ),
                'inactive'      => __( '비활성', 'acf-css-neural-link' ),
            ),
        ) );
    }

    /**
     * REST API 라우트 등록
     */
    public function register_rest_routes() {
        register_rest_route( '3j-multisite/v1', '/sites', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'rest_get_sites' ),
            'permission_callback' => array( $this, 'check_network_permission' ),
        ) );

        register_rest_route( '3j-multisite/v1', '/sites/(?P<site_id>\d+)', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'rest_get_site_details' ),
            'permission_callback' => array( $this, 'check_network_permission' ),
            'args'                => array(
                'site_id' => array(
                    'required'          => true,
                    'validate_callback' => function( $param ) {
                        return is_numeric( $param );
                    },
                ),
            ),
        ) );

        register_rest_route( '3j-multisite/v1', '/sync', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'rest_sync_plugins' ),
            'permission_callback' => array( $this, 'check_network_permission' ),
        ) );

        register_rest_route( '3j-multisite/v1', '/stats', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'rest_get_network_stats' ),
            'permission_callback' => array( $this, 'check_network_permission' ),
        ) );
    }

    /**
     * 네트워크 권한 확인
     *
     * @return bool
     */
    public function check_network_permission() {
        return current_user_can( 'manage_network' );
    }

    /**
     * 허브 메인 페이지 렌더링
     */
    public function render_hub_page() {
        if ( ! current_user_can( 'manage_network' ) ) {
            wp_die( __( '이 페이지에 접근할 권한이 없습니다.', 'acf-css-neural-link' ) );
        }

        $sites       = $this->get_all_sites();
        $stats       = $this->get_network_stats();
        $managed     = $this->managed_plugins;

        ?>
        <div class="wrap jj-multisite-hub">
            <h1>
                <span class="dashicons dashicons-networking"></span>
                3J Labs Multisite Hub
                <span class="jj-version-badge">v<?php echo esc_html( JJ_NEURAL_LINK_VERSION ); ?></span>
            </h1>

            <!-- 네트워크 통계 -->
            <div class="jj-network-stats">
                <div class="jj-stat-card jj-stat-sites">
                    <div class="jj-stat-icon">
                        <span class="dashicons dashicons-admin-multisite"></span>
                    </div>
                    <div class="jj-stat-content">
                        <span class="jj-stat-number"><?php echo esc_html( $stats['total_sites'] ); ?></span>
                        <span class="jj-stat-label">전체 사이트</span>
                    </div>
                </div>
                <div class="jj-stat-card jj-stat-plugins">
                    <div class="jj-stat-icon">
                        <span class="dashicons dashicons-admin-plugins"></span>
                    </div>
                    <div class="jj-stat-content">
                        <span class="jj-stat-number"><?php echo esc_html( $stats['total_activations'] ); ?></span>
                        <span class="jj-stat-label">총 활성화</span>
                    </div>
                </div>
                <div class="jj-stat-card jj-stat-health">
                    <div class="jj-stat-icon">
                        <span class="dashicons dashicons-yes-alt"></span>
                    </div>
                    <div class="jj-stat-content">
                        <span class="jj-stat-number jj-health-<?php echo esc_attr( $stats['health_status'] ); ?>">
                            <?php echo esc_html( ucfirst( $stats['health_status'] ) ); ?>
                        </span>
                        <span class="jj-stat-label">네트워크 상태</span>
                    </div>
                </div>
                <div class="jj-stat-card jj-stat-sync">
                    <div class="jj-stat-icon">
                        <span class="dashicons dashicons-update"></span>
                    </div>
                    <div class="jj-stat-content">
                        <span class="jj-stat-number"><?php echo esc_html( $stats['sync_rate'] ); ?>%</span>
                        <span class="jj-stat-label">동기화율</span>
                    </div>
                </div>
            </div>

            <!-- 사이트 목록 -->
            <div class="jj-card jj-sites-card">
                <div class="jj-card-header">
                    <h2>
                        <span class="dashicons dashicons-admin-multisite"></span>
                        사이트 목록
                    </h2>
                    <div class="jj-card-actions">
                        <button type="button" class="button jj-refresh-sites">
                            <span class="dashicons dashicons-update"></span>
                            새로고침
                        </button>
                    </div>
                </div>
                <div class="jj-card-body">
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th class="check-column">
                                    <input type="checkbox" id="jj-select-all-sites">
                                </th>
                                <th>사이트</th>
                                <th>도메인</th>
                                <th>활성 플러그인</th>
                                <th>상태</th>
                                <th>마지막 동기화</th>
                                <th>작업</th>
                            </tr>
                        </thead>
                        <tbody id="jj-sites-list">
                            <?php foreach ( $sites as $site ) : ?>
                                <?php
                                $site_plugins = $this->get_site_plugins( $site->blog_id );
                                $active_count = count( array_filter( $site_plugins, function( $p ) {
                                    return $p['active'];
                                } ) );
                                $last_sync    = get_blog_option( $site->blog_id, '3j_last_sync', '' );
                                ?>
                                <tr data-site-id="<?php echo esc_attr( $site->blog_id ); ?>">
                                    <td class="check-column">
                                        <input type="checkbox" class="jj-site-check" value="<?php echo esc_attr( $site->blog_id ); ?>">
                                    </td>
                                    <td>
                                        <strong><?php echo esc_html( $site->blogname ); ?></strong>
                                        <?php if ( $site->blog_id == get_main_site_id() ) : ?>
                                            <span class="jj-badge jj-badge-primary">메인</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo esc_url( $site->siteurl ); ?>" target="_blank">
                                            <?php echo esc_html( $site->domain . $site->path ); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="jj-plugin-count"><?php echo esc_html( $active_count ); ?></span>
                                        / <?php echo esc_html( count( $this->managed_plugins ) ); ?>
                                    </td>
                                    <td>
                                        <?php if ( $site->deleted || $site->archived || $site->spam ) : ?>
                                            <span class="jj-status jj-status-inactive">비활성</span>
                                        <?php else : ?>
                                            <span class="jj-status jj-status-active">활성</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ( $last_sync ) {
                                            echo esc_html( human_time_diff( strtotime( $last_sync ), current_time( 'timestamp' ) ) ) . ' 전';
                                        } else {
                                            echo '<span class="jj-text-muted">없음</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <button type="button" class="button button-small jj-view-site" data-site-id="<?php echo esc_attr( $site->blog_id ); ?>">
                                            <span class="dashicons dashicons-visibility"></span>
                                        </button>
                                        <button type="button" class="button button-small jj-sync-site" data-site-id="<?php echo esc_attr( $site->blog_id ); ?>">
                                            <span class="dashicons dashicons-update"></span>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="jj-card-footer">
                    <div class="jj-bulk-actions">
                        <select id="jj-bulk-action">
                            <option value="">일괄 작업 선택</option>
                            <option value="sync">선택 사이트 동기화</option>
                            <option value="activate_all">모든 플러그인 활성화</option>
                            <option value="deactivate_all">모든 플러그인 비활성화</option>
                            <option value="clear_cache">캐시 초기화</option>
                        </select>
                        <button type="button" class="button" id="jj-apply-bulk">적용</button>
                    </div>
                </div>
            </div>

            <!-- 사이트 상세 모달 -->
            <div id="jj-site-modal" class="jj-modal" style="display: none;">
                <div class="jj-modal-content">
                    <div class="jj-modal-header">
                        <h3 id="jj-modal-title">사이트 상세</h3>
                        <button type="button" class="jj-modal-close">&times;</button>
                    </div>
                    <div class="jj-modal-body" id="jj-modal-body">
                        <!-- AJAX로 로드 -->
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * 동기화 페이지 렌더링
     */
    public function render_sync_page() {
        if ( ! current_user_can( 'manage_network' ) ) {
            wp_die( __( '이 페이지에 접근할 권한이 없습니다.', 'acf-css-neural-link' ) );
        }

        $sites   = $this->get_all_sites();
        $managed = $this->managed_plugins;

        ?>
        <div class="wrap jj-multisite-hub">
            <h1>
                <span class="dashicons dashicons-update"></span>
                플러그인 동기화
            </h1>

            <div class="jj-sync-panel">
                <div class="jj-card">
                    <div class="jj-card-header">
                        <h2>동기화 대상 플러그인</h2>
                    </div>
                    <div class="jj-card-body">
                        <div class="jj-plugin-grid">
                            <?php foreach ( $managed as $slug => $plugin ) : ?>
                                <div class="jj-plugin-checkbox">
                                    <label>
                                        <input type="checkbox" class="jj-sync-plugin" value="<?php echo esc_attr( $slug ); ?>" checked>
                                        <span class="jj-plugin-name"><?php echo esc_html( $plugin['name'] ); ?></span>
                                        <span class="jj-plugin-category jj-category-<?php echo esc_attr( $plugin['category'] ); ?>">
                                            <?php echo esc_html( $plugin['category'] ); ?>
                                        </span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="jj-card">
                    <div class="jj-card-header">
                        <h2>대상 사이트</h2>
                        <div class="jj-card-actions">
                            <button type="button" class="button" id="jj-select-all">모두 선택</button>
                            <button type="button" class="button" id="jj-deselect-all">모두 해제</button>
                        </div>
                    </div>
                    <div class="jj-card-body">
                        <div class="jj-site-grid">
                            <?php foreach ( $sites as $site ) : ?>
                                <div class="jj-site-checkbox">
                                    <label>
                                        <input type="checkbox" class="jj-sync-site" value="<?php echo esc_attr( $site->blog_id ); ?>">
                                        <span class="jj-site-name"><?php echo esc_html( $site->blogname ); ?></span>
                                        <span class="jj-site-domain"><?php echo esc_html( $site->domain ); ?></span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="jj-sync-actions">
                    <button type="button" class="button button-primary button-hero" id="jj-start-sync">
                        <span class="dashicons dashicons-update"></span>
                        동기화 시작
                    </button>
                </div>

                <!-- 진행 상황 -->
                <div id="jj-sync-progress" class="jj-card" style="display: none;">
                    <div class="jj-card-header">
                        <h2>동기화 진행 중...</h2>
                    </div>
                    <div class="jj-card-body">
                        <div class="jj-progress-bar">
                            <div class="jj-progress-fill" style="width: 0%;"></div>
                        </div>
                        <div class="jj-progress-text">
                            <span id="jj-progress-current">0</span> / <span id="jj-progress-total">0</span> 완료
                        </div>
                        <div id="jj-sync-log" class="jj-sync-log"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * 네트워크 상태 페이지 렌더링
     */
    public function render_health_page() {
        if ( ! current_user_can( 'manage_network' ) ) {
            wp_die( __( '이 페이지에 접근할 권한이 없습니다.', 'acf-css-neural-link' ) );
        }

        $health = $this->get_network_health();

        ?>
        <div class="wrap jj-multisite-hub">
            <h1>
                <span class="dashicons dashicons-heart"></span>
                네트워크 상태
            </h1>

            <div class="jj-health-overview">
                <div class="jj-health-status jj-health-<?php echo esc_attr( $health['status'] ); ?>">
                    <span class="dashicons dashicons-<?php echo $health['status'] === 'healthy' ? 'yes-alt' : ( $health['status'] === 'warning' ? 'warning' : 'dismiss' ); ?>"></span>
                    <span class="jj-health-text">
                        <?php
                        switch ( $health['status'] ) {
                            case 'healthy':
                                echo '네트워크 상태: 양호';
                                break;
                            case 'warning':
                                echo '네트워크 상태: 주의 필요';
                                break;
                            default:
                                echo '네트워크 상태: 문제 발생';
                        }
                        ?>
                    </span>
                </div>
            </div>

            <div class="jj-health-grid">
                <?php foreach ( $health['checks'] as $check ) : ?>
                    <div class="jj-card jj-health-card jj-health-<?php echo esc_attr( $check['status'] ); ?>">
                        <div class="jj-card-header">
                            <h3>
                                <span class="dashicons dashicons-<?php echo esc_attr( $check['icon'] ); ?>"></span>
                                <?php echo esc_html( $check['label'] ); ?>
                            </h3>
                            <span class="jj-health-indicator"></span>
                        </div>
                        <div class="jj-card-body">
                            <p><?php echo esc_html( $check['message'] ); ?></p>
                            <?php if ( ! empty( $check['details'] ) ) : ?>
                                <ul class="jj-check-details">
                                    <?php foreach ( $check['details'] as $detail ) : ?>
                                        <li><?php echo esc_html( $detail ); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * 설정 페이지 렌더링
     */
    public function render_settings_page() {
        if ( ! current_user_can( 'manage_network' ) ) {
            wp_die( __( '이 페이지에 접근할 권한이 없습니다.', 'acf-css-neural-link' ) );
        }

        // 설정 저장 처리
        if ( isset( $_POST['3j_multisite_settings_nonce'] ) && wp_verify_nonce( $_POST['3j_multisite_settings_nonce'], '3j_multisite_settings' ) ) {
            $settings = array(
                'auto_sync'        => isset( $_POST['auto_sync'] ) ? 1 : 0,
                'sync_interval'    => absint( $_POST['sync_interval'] ?? 3600 ),
                'notify_admin'     => isset( $_POST['notify_admin'] ) ? 1 : 0,
                'excluded_sites'   => array_map( 'absint', $_POST['excluded_sites'] ?? array() ),
                'default_plugins'  => array_map( 'sanitize_text_field', $_POST['default_plugins'] ?? array() ),
            );
            update_site_option( '3j_multisite_settings', $settings );
            echo '<div class="notice notice-success"><p>설정이 저장되었습니다.</p></div>';
        }

        $settings = get_site_option( '3j_multisite_settings', array(
            'auto_sync'       => 0,
            'sync_interval'   => 3600,
            'notify_admin'    => 1,
            'excluded_sites'  => array(),
            'default_plugins' => array_keys( $this->managed_plugins ),
        ) );

        $sites = $this->get_all_sites();

        ?>
        <div class="wrap jj-multisite-hub">
            <h1>
                <span class="dashicons dashicons-admin-generic"></span>
                멀티사이트 허브 설정
            </h1>

            <form method="post">
                <?php wp_nonce_field( '3j_multisite_settings', '3j_multisite_settings_nonce' ); ?>

                <div class="jj-card">
                    <div class="jj-card-header">
                        <h2>자동 동기화</h2>
                    </div>
                    <div class="jj-card-body">
                        <table class="form-table">
                            <tr>
                                <th>자동 동기화 활성화</th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="auto_sync" value="1" <?php checked( $settings['auto_sync'], 1 ); ?>>
                                        새 사이트 생성 시 자동으로 플러그인 동기화
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th>동기화 간격</th>
                                <td>
                                    <select name="sync_interval">
                                        <option value="1800" <?php selected( $settings['sync_interval'], 1800 ); ?>>30분</option>
                                        <option value="3600" <?php selected( $settings['sync_interval'], 3600 ); ?>>1시간</option>
                                        <option value="7200" <?php selected( $settings['sync_interval'], 7200 ); ?>>2시간</option>
                                        <option value="21600" <?php selected( $settings['sync_interval'], 21600 ); ?>>6시간</option>
                                        <option value="86400" <?php selected( $settings['sync_interval'], 86400 ); ?>>24시간</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>관리자 알림</th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="notify_admin" value="1" <?php checked( $settings['notify_admin'], 1 ); ?>>
                                        동기화 완료 시 이메일 알림 받기
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="jj-card">
                    <div class="jj-card-header">
                        <h2>기본 플러그인</h2>
                    </div>
                    <div class="jj-card-body">
                        <p class="description">새 사이트에 자동으로 활성화될 플러그인을 선택하세요.</p>
                        <div class="jj-plugin-grid">
                            <?php foreach ( $this->managed_plugins as $slug => $plugin ) : ?>
                                <div class="jj-plugin-checkbox">
                                    <label>
                                        <input type="checkbox" name="default_plugins[]" value="<?php echo esc_attr( $slug ); ?>"
                                            <?php checked( in_array( $slug, $settings['default_plugins'], true ) ); ?>>
                                        <?php echo esc_html( $plugin['name'] ); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="jj-card">
                    <div class="jj-card-header">
                        <h2>제외 사이트</h2>
                    </div>
                    <div class="jj-card-body">
                        <p class="description">동기화에서 제외할 사이트를 선택하세요.</p>
                        <div class="jj-site-grid">
                            <?php foreach ( $sites as $site ) : ?>
                                <div class="jj-site-checkbox">
                                    <label>
                                        <input type="checkbox" name="excluded_sites[]" value="<?php echo esc_attr( $site->blog_id ); ?>"
                                            <?php checked( in_array( $site->blog_id, $settings['excluded_sites'], true ) ); ?>>
                                        <?php echo esc_html( $site->blogname ); ?>
                                        <span class="jj-site-domain"><?php echo esc_html( $site->domain ); ?></span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <?php submit_button( '설정 저장' ); ?>
            </form>
        </div>
        <?php
    }

    /**
     * 모든 사이트 가져오기
     *
     * @return array
     */
    public function get_all_sites() {
        $cache_key = 'all_sites';
        $sites     = wp_cache_get( $cache_key, self::CACHE_GROUP );

        if ( false === $sites ) {
            $sites = get_sites( array(
                'number'  => 0,
                'orderby' => 'id',
                'order'   => 'ASC',
            ) );

            // 사이트 정보 보강
            foreach ( $sites as &$site ) {
                switch_to_blog( $site->blog_id );
                $site->blogname = get_bloginfo( 'name' );
                $site->siteurl  = get_bloginfo( 'url' );
                restore_current_blog();
            }

            wp_cache_set( $cache_key, $sites, self::CACHE_GROUP, self::CACHE_EXPIRY );
        }

        return $sites;
    }

    /**
     * 사이트별 플러그인 상태 가져오기
     *
     * @param int $site_id 사이트 ID
     * @return array
     */
    public function get_site_plugins( $site_id ) {
        $cache_key = 'site_plugins_' . $site_id;
        $plugins   = wp_cache_get( $cache_key, self::CACHE_GROUP );

        if ( false === $plugins ) {
            $plugins = array();

            switch_to_blog( $site_id );

            $active_plugins = get_option( 'active_plugins', array() );

            foreach ( $this->managed_plugins as $slug => $plugin_info ) {
                $plugin_file = $slug . '/' . $plugin_info['file'];
                $is_active   = in_array( $plugin_file, $active_plugins, true );

                $plugins[ $slug ] = array(
                    'name'    => $plugin_info['name'],
                    'slug'    => $slug,
                    'active'  => $is_active,
                    'version' => $is_active ? $this->get_plugin_version( $slug ) : '',
                );
            }

            restore_current_blog();

            wp_cache_set( $cache_key, $plugins, self::CACHE_GROUP, self::CACHE_EXPIRY );
        }

        return $plugins;
    }

    /**
     * 플러그인 버전 가져오기
     *
     * @param string $slug 플러그인 슬러그
     * @return string
     */
    private function get_plugin_version( $slug ) {
        if ( ! isset( $this->managed_plugins[ $slug ] ) ) {
            return '';
        }

        $plugin_file = WP_PLUGIN_DIR . '/' . $slug . '/' . $this->managed_plugins[ $slug ]['file'];

        if ( ! file_exists( $plugin_file ) ) {
            return '';
        }

        $plugin_data = get_plugin_data( $plugin_file, false, false );
        return $plugin_data['Version'] ?? '';
    }

    /**
     * 네트워크 통계 가져오기
     *
     * @return array
     */
    public function get_network_stats() {
        $cache_key = 'network_stats';
        $stats     = wp_cache_get( $cache_key, self::CACHE_GROUP );

        if ( false === $stats ) {
            $sites             = $this->get_all_sites();
            $total_sites       = count( $sites );
            $total_activations = 0;
            $synced_sites      = 0;

            foreach ( $sites as $site ) {
                $plugins = $this->get_site_plugins( $site->blog_id );
                $active  = count( array_filter( $plugins, function( $p ) {
                    return $p['active'];
                } ) );
                $total_activations += $active;

                // 50% 이상 플러그인이 활성화된 경우 동기화된 것으로 간주
                if ( $active >= count( $this->managed_plugins ) / 2 ) {
                    $synced_sites++;
                }
            }

            $sync_rate     = $total_sites > 0 ? round( ( $synced_sites / $total_sites ) * 100 ) : 0;
            $health_status = 'healthy';

            if ( $sync_rate < 50 ) {
                $health_status = 'critical';
            } elseif ( $sync_rate < 80 ) {
                $health_status = 'warning';
            }

            $stats = array(
                'total_sites'       => $total_sites,
                'total_activations' => $total_activations,
                'synced_sites'      => $synced_sites,
                'sync_rate'         => $sync_rate,
                'health_status'     => $health_status,
            );

            wp_cache_set( $cache_key, $stats, self::CACHE_GROUP, self::CACHE_EXPIRY );
        }

        return $stats;
    }

    /**
     * 네트워크 상태 체크
     *
     * @return array
     */
    public function get_network_health() {
        $checks = array();
        $sites  = $this->get_all_sites();
        $stats  = $this->get_network_stats();

        // 동기화 상태 체크
        $checks[] = array(
            'label'   => '플러그인 동기화',
            'icon'    => 'update',
            'status'  => $stats['sync_rate'] >= 80 ? 'good' : ( $stats['sync_rate'] >= 50 ? 'warning' : 'critical' ),
            'message' => sprintf( '네트워크의 %d%%가 동기화됨', $stats['sync_rate'] ),
            'details' => array(
                sprintf( '동기화된 사이트: %d / %d', $stats['synced_sites'], $stats['total_sites'] ),
            ),
        );

        // PHP 버전 체크
        $php_version    = phpversion();
        $php_ok         = version_compare( $php_version, '7.4', '>=' );
        $checks[]       = array(
            'label'   => 'PHP 버전',
            'icon'    => 'code-standards',
            'status'  => $php_ok ? 'good' : 'warning',
            'message' => 'PHP ' . $php_version,
            'details' => $php_ok ? array() : array( 'PHP 7.4 이상 권장' ),
        );

        // 데이터베이스 체크
        global $wpdb;
        $db_version = $wpdb->db_version();
        $checks[]   = array(
            'label'   => '데이터베이스',
            'icon'    => 'database',
            'status'  => 'good',
            'message' => 'MySQL ' . $db_version,
            'details' => array(),
        );

        // WordPress 버전 체크
        global $wp_version;
        $wp_ok    = version_compare( $wp_version, '6.0', '>=' );
        $checks[] = array(
            'label'   => 'WordPress 버전',
            'icon'    => 'wordpress',
            'status'  => $wp_ok ? 'good' : 'warning',
            'message' => 'WordPress ' . $wp_version,
            'details' => $wp_ok ? array() : array( 'WordPress 6.0 이상 권장' ),
        );

        // 메모리 체크
        $memory_limit = ini_get( 'memory_limit' );
        $memory_bytes = wp_convert_hr_to_bytes( $memory_limit );
        $memory_ok    = $memory_bytes >= 256 * 1024 * 1024;
        $checks[]     = array(
            'label'   => '메모리 제한',
            'icon'    => 'performance',
            'status'  => $memory_ok ? 'good' : 'warning',
            'message' => $memory_limit,
            'details' => $memory_ok ? array() : array( '256MB 이상 권장' ),
        );

        // 전체 상태 결정
        $has_critical = false;
        $has_warning  = false;

        foreach ( $checks as $check ) {
            if ( $check['status'] === 'critical' ) {
                $has_critical = true;
            } elseif ( $check['status'] === 'warning' ) {
                $has_warning = true;
            }
        }

        $overall_status = 'healthy';
        if ( $has_critical ) {
            $overall_status = 'critical';
        } elseif ( $has_warning ) {
            $overall_status = 'warning';
        }

        return array(
            'status' => $overall_status,
            'checks' => $checks,
        );
    }

    /**
     * 플러그인 동기화 실행
     *
     * @param int   $site_id  사이트 ID
     * @param array $plugins  플러그인 슬러그 배열
     * @param bool  $activate 활성화 여부
     * @return array
     */
    public function sync_plugins_to_site( $site_id, $plugins = array(), $activate = true ) {
        if ( empty( $plugins ) ) {
            $plugins = array_keys( $this->managed_plugins );
        }

        $results = array(
            'success' => true,
            'synced'  => array(),
            'failed'  => array(),
        );

        switch_to_blog( $site_id );

        foreach ( $plugins as $slug ) {
            if ( ! isset( $this->managed_plugins[ $slug ] ) ) {
                continue;
            }

            $plugin_file = $slug . '/' . $this->managed_plugins[ $slug ]['file'];
            $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;

            if ( ! file_exists( $plugin_path ) ) {
                $results['failed'][] = array(
                    'slug'   => $slug,
                    'reason' => '플러그인 파일을 찾을 수 없음',
                );
                continue;
            }

            if ( $activate ) {
                $result = activate_plugin( $plugin_file, '', false, false );

                if ( is_wp_error( $result ) ) {
                    $results['failed'][] = array(
                        'slug'   => $slug,
                        'reason' => $result->get_error_message(),
                    );
                } else {
                    $results['synced'][] = $slug;
                }
            } else {
                deactivate_plugins( $plugin_file, false, false );
                $results['synced'][] = $slug;
            }
        }

        // 마지막 동기화 시간 기록
        update_option( '3j_last_sync', current_time( 'mysql' ) );

        // 캐시 무효화
        wp_cache_delete( 'site_plugins_' . $site_id, self::CACHE_GROUP );

        restore_current_blog();

        // 실패한 플러그인이 있으면 success를 false로
        if ( ! empty( $results['failed'] ) ) {
            $results['success'] = false;
        }

        // 이벤트 발행 (Phase 50A Event Bus와 연동)
        if ( function_exists( 'jj_publish' ) ) {
            jj_publish( 'multisite_sync_completed', array(
                'site_id' => $site_id,
                'results' => $results,
            ) );
        }

        return $results;
    }

    // ====================================
    // AJAX 핸들러
    // ====================================

    /**
     * AJAX: 사이트 목록 가져오기
     */
    public function ajax_get_sites() {
        check_ajax_referer( '3j_multisite_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_network' ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        $sites = $this->get_all_sites();
        $data  = array();

        foreach ( $sites as $site ) {
            $plugins      = $this->get_site_plugins( $site->blog_id );
            $active_count = count( array_filter( $plugins, function( $p ) {
                return $p['active'];
            } ) );

            $data[] = array(
                'id'           => $site->blog_id,
                'name'         => $site->blogname,
                'domain'       => $site->domain . $site->path,
                'url'          => $site->siteurl,
                'active_count' => $active_count,
                'total_count'  => count( $this->managed_plugins ),
                'is_main'      => $site->blog_id == get_main_site_id(),
                'is_active'    => ! ( $site->deleted || $site->archived || $site->spam ),
            );
        }

        wp_send_json_success( $data );
    }

    /**
     * AJAX: 사이트 상세 정보 가져오기
     */
    public function ajax_get_site_details() {
        check_ajax_referer( '3j_multisite_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_network' ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        $site_id = absint( $_POST['site_id'] ?? 0 );

        if ( ! $site_id ) {
            wp_send_json_error( '잘못된 사이트 ID입니다.' );
        }

        $site    = get_site( $site_id );
        $plugins = $this->get_site_plugins( $site_id );

        switch_to_blog( $site_id );
        $site_name = get_bloginfo( 'name' );
        $site_url  = get_bloginfo( 'url' );
        $last_sync = get_option( '3j_last_sync', '' );
        restore_current_blog();

        $data = array(
            'id'        => $site_id,
            'name'      => $site_name,
            'url'       => $site_url,
            'domain'    => $site->domain . $site->path,
            'plugins'   => $plugins,
            'last_sync' => $last_sync,
        );

        wp_send_json_success( $data );
    }

    /**
     * AJAX: 플러그인 동기화
     */
    public function ajax_sync_plugins() {
        check_ajax_referer( '3j_multisite_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_network' ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        $site_ids = array_map( 'absint', $_POST['site_ids'] ?? array() );
        $plugins  = array_map( 'sanitize_text_field', $_POST['plugins'] ?? array() );
        $activate = isset( $_POST['activate'] ) ? (bool) $_POST['activate'] : true;

        if ( empty( $site_ids ) ) {
            wp_send_json_error( '사이트를 선택해주세요.' );
        }

        $results = array();

        foreach ( $site_ids as $site_id ) {
            $results[ $site_id ] = $this->sync_plugins_to_site( $site_id, $plugins, $activate );
        }

        // 네트워크 통계 캐시 무효화
        wp_cache_delete( 'network_stats', self::CACHE_GROUP );

        wp_send_json_success( array(
            'message' => sprintf( '%d개 사이트에 동기화 완료', count( $site_ids ) ),
            'results' => $results,
        ) );
    }

    /**
     * AJAX: 일괄 작업 실행
     */
    public function ajax_bulk_action() {
        check_ajax_referer( '3j_multisite_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_network' ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        $action   = sanitize_text_field( $_POST['bulk_action'] ?? '' );
        $site_ids = array_map( 'absint', $_POST['site_ids'] ?? array() );

        if ( empty( $action ) || empty( $site_ids ) ) {
            wp_send_json_error( '작업과 사이트를 선택해주세요.' );
        }

        $results = array();

        switch ( $action ) {
            case 'sync':
                foreach ( $site_ids as $site_id ) {
                    $results[ $site_id ] = $this->sync_plugins_to_site( $site_id );
                }
                break;

            case 'activate_all':
                foreach ( $site_ids as $site_id ) {
                    $results[ $site_id ] = $this->sync_plugins_to_site( $site_id, array(), true );
                }
                break;

            case 'deactivate_all':
                foreach ( $site_ids as $site_id ) {
                    $results[ $site_id ] = $this->sync_plugins_to_site( $site_id, array(), false );
                }
                break;

            case 'clear_cache':
                foreach ( $site_ids as $site_id ) {
                    wp_cache_delete( 'site_plugins_' . $site_id, self::CACHE_GROUP );
                    $results[ $site_id ] = array( 'success' => true );
                }
                break;

            default:
                wp_send_json_error( '알 수 없는 작업입니다.' );
        }

        // 네트워크 통계 캐시 무효화
        wp_cache_delete( 'network_stats', self::CACHE_GROUP );
        wp_cache_delete( 'all_sites', self::CACHE_GROUP );

        wp_send_json_success( array(
            'message' => sprintf( '%d개 사이트에 작업 완료', count( $site_ids ) ),
            'results' => $results,
        ) );
    }

    /**
     * AJAX: 네트워크 통계 가져오기
     */
    public function ajax_get_network_stats() {
        check_ajax_referer( '3j_multisite_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_network' ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        $stats = $this->get_network_stats();
        wp_send_json_success( $stats );
    }

    // ====================================
    // REST API 콜백
    // ====================================

    /**
     * REST: 사이트 목록
     *
     * @param WP_REST_Request $request 요청 객체
     * @return WP_REST_Response
     */
    public function rest_get_sites( $request ) {
        $sites = $this->get_all_sites();
        $data  = array();

        foreach ( $sites as $site ) {
            $plugins      = $this->get_site_plugins( $site->blog_id );
            $active_count = count( array_filter( $plugins, function( $p ) {
                return $p['active'];
            } ) );

            $data[] = array(
                'id'           => $site->blog_id,
                'name'         => $site->blogname,
                'domain'       => $site->domain . $site->path,
                'url'          => $site->siteurl,
                'active_count' => $active_count,
                'total_count'  => count( $this->managed_plugins ),
                'is_main'      => $site->blog_id == get_main_site_id(),
            );
        }

        return new WP_REST_Response( $data, 200 );
    }

    /**
     * REST: 사이트 상세
     *
     * @param WP_REST_Request $request 요청 객체
     * @return WP_REST_Response
     */
    public function rest_get_site_details( $request ) {
        $site_id = absint( $request->get_param( 'site_id' ) );
        $site    = get_site( $site_id );

        if ( ! $site ) {
            return new WP_REST_Response( array( 'error' => '사이트를 찾을 수 없습니다.' ), 404 );
        }

        $plugins = $this->get_site_plugins( $site_id );

        switch_to_blog( $site_id );
        $site_name = get_bloginfo( 'name' );
        $site_url  = get_bloginfo( 'url' );
        restore_current_blog();

        return new WP_REST_Response( array(
            'id'      => $site_id,
            'name'    => $site_name,
            'url'     => $site_url,
            'plugins' => $plugins,
        ), 200 );
    }

    /**
     * REST: 플러그인 동기화
     *
     * @param WP_REST_Request $request 요청 객체
     * @return WP_REST_Response
     */
    public function rest_sync_plugins( $request ) {
        $site_ids = $request->get_param( 'site_ids' );
        $plugins  = $request->get_param( 'plugins' );
        $activate = $request->get_param( 'activate' ) ?? true;

        if ( empty( $site_ids ) ) {
            return new WP_REST_Response( array( 'error' => '사이트를 선택해주세요.' ), 400 );
        }

        $results = array();

        foreach ( $site_ids as $site_id ) {
            $results[ $site_id ] = $this->sync_plugins_to_site( absint( $site_id ), $plugins, $activate );
        }

        return new WP_REST_Response( array(
            'message' => sprintf( '%d개 사이트에 동기화 완료', count( $site_ids ) ),
            'results' => $results,
        ), 200 );
    }

    /**
     * REST: 네트워크 통계
     *
     * @param WP_REST_Request $request 요청 객체
     * @return WP_REST_Response
     */
    public function rest_get_network_stats( $request ) {
        $stats = $this->get_network_stats();
        return new WP_REST_Response( $stats, 200 );
    }
}

// 멀티사이트 환경에서만 초기화
if ( is_multisite() ) {
    add_action( 'plugins_loaded', function() {
        JJ_Multisite_Hub::get_instance();
    }, 15 );
}
