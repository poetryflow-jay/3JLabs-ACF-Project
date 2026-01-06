<?php
/**
 * 3J Labs REST API - 통합 REST API 시스템
 *
 * 모든 3J Labs 플러그인이 공유하는 통일된 REST API 구조를 제공합니다.
 *
 * 엔드포인트:
 * - GET  /wp-json/3j-labs/v1/plugins          - 설치된 플러그인 목록
 * - GET  /wp-json/3j-labs/v1/plugins/{slug}   - 개별 플러그인 상세
 * - GET  /wp-json/3j-labs/v1/health           - 시스템 상태
 * - GET  /wp-json/3j-labs/v1/settings         - 통합 설정
 * - POST /wp-json/3j-labs/v1/settings         - 설정 업데이트
 * - GET  /wp-json/3j-labs/v1/analytics        - 크로스 플러그인 분석
 *
 * @package    3J_Labs_Shared
 * @subpackage REST_API
 * @since      1.0.0
 * @version    1.0.0
 * @author     3J Labs (제이x제니x제이슨 연구소)
 * @link       https://3j-labs.com
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'JJ_3J_REST_API' ) ) {

    /**
     * 3J Labs 통합 REST API 클래스
     *
     * @since 1.0.0
     */
    class JJ_3J_REST_API {

        /**
         * API 네임스페이스
         *
         * @var string
         */
        const API_NAMESPACE = '3j-labs/v1';

        /**
         * API 버전
         *
         * @var string
         */
        const API_VERSION = '1.0.0';

        /**
         * 싱글톤 인스턴스
         *
         * @var JJ_3J_REST_API|null
         */
        private static $instance = null;

        /**
         * 등록된 플러그인 목록
         *
         * @var array
         */
        private $registered_plugins = array();

        /**
         * 이벤트 리스너 목록
         *
         * @var array
         */
        private $event_listeners = array();

        /**
         * 싱글톤 인스턴스 반환
         *
         * @since 1.0.0
         * @return JJ_3J_REST_API
         */
        public static function instance() {
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
            add_action( 'rest_api_init', array( $this, 'register_routes' ) );
            add_action( 'init', array( $this, 'discover_plugins' ), 20 );
        }

        /**
         * REST API 라우트 등록
         *
         * @since 1.0.0
         */
        public function register_routes() {
            // GET /plugins - 플러그인 목록
            register_rest_route(
                self::API_NAMESPACE,
                '/plugins',
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_plugins' ),
                    'permission_callback' => array( $this, 'check_read_permission' ),
                )
            );

            // GET /plugins/{slug} - 개별 플러그인 상세
            register_rest_route(
                self::API_NAMESPACE,
                '/plugins/(?P<slug>[a-zA-Z0-9_-]+)',
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_plugin' ),
                    'permission_callback' => array( $this, 'check_read_permission' ),
                    'args'                => array(
                        'slug' => array(
                            'required'          => true,
                            'type'              => 'string',
                            'sanitize_callback' => 'sanitize_key',
                        ),
                    ),
                )
            );

            // GET /health - 시스템 상태
            register_rest_route(
                self::API_NAMESPACE,
                '/health',
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_health' ),
                    'permission_callback' => array( $this, 'check_read_permission' ),
                )
            );

            // GET /settings - 통합 설정 조회
            register_rest_route(
                self::API_NAMESPACE,
                '/settings',
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_settings' ),
                    'permission_callback' => array( $this, 'check_admin_permission' ),
                )
            );

            // POST /settings - 통합 설정 업데이트
            register_rest_route(
                self::API_NAMESPACE,
                '/settings',
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'update_settings' ),
                    'permission_callback' => array( $this, 'check_admin_permission' ),
                )
            );

            // GET /analytics - 크로스 플러그인 분석
            register_rest_route(
                self::API_NAMESPACE,
                '/analytics',
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_analytics' ),
                    'permission_callback' => array( $this, 'check_admin_permission' ),
                    'args'                => array(
                        'period' => array(
                            'default'           => '7d',
                            'type'              => 'string',
                            'enum'              => array( '24h', '7d', '30d', '90d' ),
                            'sanitize_callback' => 'sanitize_key',
                        ),
                    ),
                )
            );

            // GET /events - 이벤트 로그
            register_rest_route(
                self::API_NAMESPACE,
                '/events',
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_events' ),
                    'permission_callback' => array( $this, 'check_admin_permission' ),
                    'args'                => array(
                        'limit' => array(
                            'default'           => 50,
                            'type'              => 'integer',
                            'minimum'           => 1,
                            'maximum'           => 200,
                            'sanitize_callback' => 'absint',
                        ),
                        'plugin' => array(
                            'type'              => 'string',
                            'sanitize_callback' => 'sanitize_key',
                        ),
                    ),
                )
            );

            // POST /events - 이벤트 기록
            register_rest_route(
                self::API_NAMESPACE,
                '/events',
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'log_event' ),
                    'permission_callback' => array( $this, 'check_admin_permission' ),
                    'args'                => array(
                        'plugin' => array(
                            'required'          => true,
                            'type'              => 'string',
                            'sanitize_callback' => 'sanitize_key',
                        ),
                        'event' => array(
                            'required'          => true,
                            'type'              => 'string',
                            'sanitize_callback' => 'sanitize_text_field',
                        ),
                        'data' => array(
                            'type'    => 'object',
                            'default' => array(),
                        ),
                    ),
                )
            );
        }

        /**
         * 3J Labs 플러그인 자동 탐지
         *
         * @since 1.0.0
         */
        public function discover_plugins() {
            // 3J Labs 플러그인 목록 (슬러그 => 정보)
            $jj_plugins = array(
                'acf-css-master' => array(
                    'name'        => 'ACF CSS Manager',
                    'file'        => 'acf-css-really-simple-style-management-center-master/acf-css-really-simple-style-guide.php',
                    'constant'    => 'ACF_CSS_MASTER_VERSION',
                    'category'    => 'core',
                    'description' => 'WordPress 스타일 통합 관리 시스템',
                ),
                'acf-css-neural-link' => array(
                    'name'        => 'ACF CSS Neural Link',
                    'file'        => 'acf-css-neural-link/acf-css-neural-link.php',
                    'constant'    => 'ACF_CSS_NEURAL_LINK_VERSION',
                    'category'    => 'core',
                    'description' => '패턴 학습 및 업데이트 관리',
                ),
                'acf-css-ai-extension' => array(
                    'name'        => 'ACF CSS AI Extension',
                    'file'        => 'acf-css-ai-extension/acf-css-ai-extension.php',
                    'constant'    => 'ACF_CSS_AI_VERSION',
                    'category'    => 'extension',
                    'description' => 'AI 기반 스타일 추천 및 생성',
                ),
                'acf-mail-smtp' => array(
                    'name'        => 'ACF Mail SMTP',
                    'file'        => 'acf-mail-smtp/acf-mail-smtp.php',
                    'constant'    => 'ACF_MAIL_SMTP_VERSION',
                    'category'    => 'utility',
                    'description' => '메일 SMTP + Gmail API',
                ),
                'jj-analytics-dashboard' => array(
                    'name'        => 'JJ Analytics Dashboard',
                    'file'        => 'jj-analytics-dashboard/jj-analytics-dashboard.php',
                    'constant'    => 'JJ_ANALYTICS_DASHBOARD_VERSION',
                    'category'    => 'analytics',
                    'description' => '전체 플러그인 통합 분석 대시보드',
                ),
                'acf-css-woocommerce-toolkit' => array(
                    'name'        => 'ACF CSS WooCommerce Toolkit',
                    'file'        => 'acf-css-woocommerce-toolkit/acf-css-woocommerce-toolkit.php',
                    'constant'    => 'ACF_CSS_WC_VERSION',
                    'category'    => 'extension',
                    'description' => 'WooCommerce 스타일 및 기능 확장',
                ),
                'acf-code-snippets-box' => array(
                    'name'        => 'ACF Code Snippets Box',
                    'file'        => 'acf-code-snippets-box/acf-code-snippets-box.php',
                    'constant'    => 'ACF_CSB_VERSION',
                    'category'    => 'core',
                    'description' => 'CSS/JS/PHP 코드 스니펫 관리',
                ),
                'acf-nudge-flow' => array(
                    'name'        => 'ACF MBA Nudge Flow',
                    'file'        => 'acf-nudge-flow/acf-nudge-flow.php',
                    'constant'    => 'ACF_MBA_VERSION',
                    'category'    => 'marketing',
                    'description' => '마케팅 자동화 및 넛지 시스템',
                ),
                'wp-bulk-manager' => array(
                    'name'        => 'WP Bulk Manager',
                    'file'        => 'wp-bulk-manager/wp-bulk-installer.php',
                    'constant'    => 'WP_BULK_INSTALLER_VERSION',
                    'category'    => 'utility',
                    'description' => '플러그인/테마 대량 설치 및 관리',
                ),
                'acf-user-journey-analytics' => array(
                    'name'        => 'ACF User Journey Analytics',
                    'file'        => 'acf-user-journey-analytics/acf-user-journey-analytics.php',
                    'constant'    => 'ACF_UJA_VERSION',
                    'category'    => 'analytics',
                    'description' => '무료 트래픽 분석 대시보드',
                ),
                'jj-marketing-automation-dashboard' => array(
                    'name'        => 'JJ Marketing Automation Dashboard',
                    'file'        => 'jj-marketing-automation-dashboard/jj-marketing-automation-dashboard.php',
                    'constant'    => 'JJ_MAD_VERSION',
                    'category'    => 'marketing',
                    'description' => '종합 마케팅 자동화 대시보드',
                ),
                'admin-menu-editor-pro' => array(
                    'name'        => 'Admin Menu Editor Pro',
                    'file'        => 'admin-menu-editor-pro/admin-menu-editor-pro.php',
                    'constant'    => 'AMEP_VERSION',
                    'category'    => 'utility',
                    'description' => '관리자 메뉴 커스터마이저',
                ),
                'acf-css-woo-license' => array(
                    'name'        => 'ACF CSS Woo License Bridge',
                    'file'        => 'acf-css-woo-license/acf-css-woo-license.php',
                    'constant'    => 'ACF_CSS_WOO_LICENSE_VERSION',
                    'category'    => 'extension',
                    'description' => 'WooCommerce 라이센스 브릿지',
                ),
                'oneclick-seo-pro' => array(
                    'name'        => 'WP 1-Click SEO Pro',
                    'file'        => 'SEO/oneclick-seo-pro/oneclick-seo-pro.php',
                    'constant'    => 'ONECLICK_SEO_PRO_VERSION',
                    'category'    => 'seo',
                    'description' => '원클릭 SEO 최적화',
                ),
            );

            // 각 플러그인 상태 확인
            foreach ( $jj_plugins as $slug => $info ) {
                $plugin_file = WP_PLUGIN_DIR . '/' . $info['file'];
                $is_active   = false;
                $version     = null;

                // 플러그인 파일 존재 확인
                if ( file_exists( $plugin_file ) ) {
                    // 활성화 상태 확인
                    $is_active = is_plugin_active( $info['file'] );

                    // 버전 상수 확인
                    if ( defined( $info['constant'] ) ) {
                        $version = constant( $info['constant'] );
                    }
                }

                $this->registered_plugins[ $slug ] = array(
                    'slug'        => $slug,
                    'name'        => $info['name'],
                    'description' => $info['description'],
                    'category'    => $info['category'],
                    'version'     => $version,
                    'installed'   => file_exists( $plugin_file ),
                    'active'      => $is_active,
                    'file'        => $info['file'],
                );
            }

            /**
             * 추가 플러그인 등록 허용
             *
             * @since 1.0.0
             * @param array $registered_plugins 등록된 플러그인 목록
             */
            $this->registered_plugins = apply_filters( '3j_labs_registered_plugins', $this->registered_plugins );
        }

        /**
         * 플러그인 수동 등록
         *
         * @since 1.0.0
         * @param string $slug    플러그인 슬러그
         * @param array  $data    플러그인 데이터
         * @return bool
         */
        public function register_plugin( $slug, $data ) {
            if ( empty( $slug ) || ! is_array( $data ) ) {
                return false;
            }

            $defaults = array(
                'slug'        => $slug,
                'name'        => $slug,
                'description' => '',
                'category'    => 'other',
                'version'     => '1.0.0',
                'installed'   => true,
                'active'      => true,
                'file'        => '',
            );

            $this->registered_plugins[ $slug ] = wp_parse_args( $data, $defaults );

            return true;
        }

        /**
         * 읽기 권한 확인
         *
         * @since 1.0.0
         * @return bool
         */
        public function check_read_permission() {
            // 기본적으로 로그인한 사용자만 허용
            return is_user_logged_in();
        }

        /**
         * 관리자 권한 확인
         *
         * @since 1.0.0
         * @return bool
         */
        public function check_admin_permission() {
            return current_user_can( 'manage_options' );
        }

        /**
         * 플러그인 목록 조회
         *
         * @since 1.0.0
         * @param WP_REST_Request $request 요청 객체
         * @return WP_REST_Response
         */
        public function get_plugins( $request ) {
            $plugins = array_values( $this->registered_plugins );

            // 카테고리별 그룹핑 옵션
            $group_by = $request->get_param( 'group_by' );
            if ( 'category' === $group_by ) {
                $grouped = array();
                foreach ( $plugins as $plugin ) {
                    $cat = $plugin['category'];
                    if ( ! isset( $grouped[ $cat ] ) ) {
                        $grouped[ $cat ] = array();
                    }
                    $grouped[ $cat ][] = $plugin;
                }
                $plugins = $grouped;
            }

            return new WP_REST_Response(
                array(
                    'success' => true,
                    'data'    => $plugins,
                    'meta'    => array(
                        'total'      => count( $this->registered_plugins ),
                        'active'     => count( array_filter( $this->registered_plugins, function( $p ) { return $p['active']; } ) ),
                        'installed'  => count( array_filter( $this->registered_plugins, function( $p ) { return $p['installed']; } ) ),
                        'api_version' => self::API_VERSION,
                    ),
                ),
                200
            );
        }

        /**
         * 개별 플러그인 조회
         *
         * @since 1.0.0
         * @param WP_REST_Request $request 요청 객체
         * @return WP_REST_Response
         */
        public function get_plugin( $request ) {
            $slug = $request->get_param( 'slug' );

            if ( ! isset( $this->registered_plugins[ $slug ] ) ) {
                return new WP_REST_Response(
                    array(
                        'success' => false,
                        'message' => '플러그인을 찾을 수 없습니다.',
                        'code'    => 'plugin_not_found',
                    ),
                    404
                );
            }

            $plugin = $this->registered_plugins[ $slug ];

            // 추가 상세 정보 수집
            $plugin['settings'] = $this->get_plugin_settings( $slug );
            $plugin['stats']    = $this->get_plugin_stats( $slug );

            return new WP_REST_Response(
                array(
                    'success' => true,
                    'data'    => $plugin,
                ),
                200
            );
        }

        /**
         * 시스템 상태 조회
         *
         * @since 1.0.0
         * @param WP_REST_Request $request 요청 객체
         * @return WP_REST_Response
         */
        public function get_health( $request ) {
            global $wpdb;

            $health = array(
                'status'      => 'healthy',
                'timestamp'   => current_time( 'mysql' ),
                'environment' => array(
                    'wordpress' => get_bloginfo( 'version' ),
                    'php'       => phpversion(),
                    'mysql'     => $wpdb->db_version(),
                    'memory'    => array(
                        'limit'     => ini_get( 'memory_limit' ),
                        'usage'     => size_format( memory_get_usage() ),
                        'peak'      => size_format( memory_get_peak_usage() ),
                    ),
                ),
                'plugins'     => array(
                    'total'       => count( $this->registered_plugins ),
                    'active'      => 0,
                    'inactive'    => 0,
                    'not_installed' => 0,
                ),
                'issues'      => array(),
            );

            // 플러그인 상태 집계
            foreach ( $this->registered_plugins as $plugin ) {
                if ( ! $plugin['installed'] ) {
                    $health['plugins']['not_installed']++;
                } elseif ( $plugin['active'] ) {
                    $health['plugins']['active']++;
                } else {
                    $health['plugins']['inactive']++;
                }
            }

            // 문제 감지
            $memory_limit = wp_convert_hr_to_bytes( ini_get( 'memory_limit' ) );
            if ( $memory_limit < 256 * 1024 * 1024 ) {
                $health['issues'][] = array(
                    'type'    => 'warning',
                    'message' => 'PHP 메모리 제한이 256MB 미만입니다.',
                );
            }

            if ( version_compare( phpversion(), '7.4', '<' ) ) {
                $health['issues'][] = array(
                    'type'    => 'error',
                    'message' => 'PHP 7.4 이상 버전이 권장됩니다.',
                );
                $health['status'] = 'degraded';
            }

            if ( count( $health['issues'] ) > 0 && $health['status'] === 'healthy' ) {
                $health['status'] = 'warning';
            }

            return new WP_REST_Response(
                array(
                    'success' => true,
                    'data'    => $health,
                ),
                200
            );
        }

        /**
         * 통합 설정 조회
         *
         * @since 1.0.0
         * @param WP_REST_Request $request 요청 객체
         * @return WP_REST_Response
         */
        public function get_settings( $request ) {
            $settings = get_option( '3j_labs_global_settings', array() );

            $defaults = array(
                'auto_update'       => false,
                'analytics_enabled' => true,
                'event_logging'     => true,
                'log_retention'     => 30, // 일
                'api_access'        => 'authenticated',
                'notification_email' => get_option( 'admin_email' ),
            );

            $settings = wp_parse_args( $settings, $defaults );

            // 플러그인별 설정 수집
            $plugin_settings = array();
            foreach ( $this->registered_plugins as $slug => $plugin ) {
                if ( $plugin['active'] ) {
                    $plugin_settings[ $slug ] = $this->get_plugin_settings( $slug );
                }
            }

            return new WP_REST_Response(
                array(
                    'success' => true,
                    'data'    => array(
                        'global'  => $settings,
                        'plugins' => $plugin_settings,
                    ),
                ),
                200
            );
        }

        /**
         * 통합 설정 업데이트
         *
         * @since 1.0.0
         * @param WP_REST_Request $request 요청 객체
         * @return WP_REST_Response
         */
        public function update_settings( $request ) {
            $new_settings = $request->get_json_params();

            if ( empty( $new_settings ) ) {
                return new WP_REST_Response(
                    array(
                        'success' => false,
                        'message' => '업데이트할 설정이 없습니다.',
                    ),
                    400
                );
            }

            $current = get_option( '3j_labs_global_settings', array() );
            $allowed = array( 'auto_update', 'analytics_enabled', 'event_logging', 'log_retention', 'api_access', 'notification_email' );

            foreach ( $new_settings as $key => $value ) {
                if ( in_array( $key, $allowed, true ) ) {
                    $current[ $key ] = sanitize_text_field( $value );
                }
            }

            update_option( '3j_labs_global_settings', $current );

            // 이벤트 기록
            $this->record_event( '3j-labs-api', 'settings_updated', $new_settings );

            return new WP_REST_Response(
                array(
                    'success' => true,
                    'message' => '설정이 업데이트되었습니다.',
                    'data'    => $current,
                ),
                200
            );
        }

        /**
         * 크로스 플러그인 분석 조회
         *
         * @since 1.0.0
         * @param WP_REST_Request $request 요청 객체
         * @return WP_REST_Response
         */
        public function get_analytics( $request ) {
            $period = $request->get_param( 'period' );

            // 기간 계산
            $days = 7;
            switch ( $period ) {
                case '24h':
                    $days = 1;
                    break;
                case '30d':
                    $days = 30;
                    break;
                case '90d':
                    $days = 90;
                    break;
            }

            $analytics = array(
                'period'        => $period,
                'start_date'    => date( 'Y-m-d', strtotime( "-{$days} days" ) ),
                'end_date'      => date( 'Y-m-d' ),
                'summary'       => array(
                    'total_events'   => 0,
                    'active_plugins' => count( array_filter( $this->registered_plugins, function( $p ) { return $p['active']; } ) ),
                ),
                'by_plugin'     => array(),
                'by_event_type' => array(),
                'trends'        => array(),
            );

            // 이벤트 로그에서 통계 수집
            $events = get_option( '3j_labs_event_log', array() );
            $cutoff = strtotime( "-{$days} days" );

            foreach ( $events as $event ) {
                if ( $event['timestamp'] < $cutoff ) {
                    continue;
                }

                $analytics['summary']['total_events']++;

                // 플러그인별
                $plugin_slug = $event['plugin'];
                if ( ! isset( $analytics['by_plugin'][ $plugin_slug ] ) ) {
                    $analytics['by_plugin'][ $plugin_slug ] = 0;
                }
                $analytics['by_plugin'][ $plugin_slug ]++;

                // 이벤트 유형별
                $event_type = $event['event'];
                if ( ! isset( $analytics['by_event_type'][ $event_type ] ) ) {
                    $analytics['by_event_type'][ $event_type ] = 0;
                }
                $analytics['by_event_type'][ $event_type ]++;
            }

            // 활성 플러그인 조합 분석
            $active_slugs = array();
            foreach ( $this->registered_plugins as $slug => $plugin ) {
                if ( $plugin['active'] ) {
                    $active_slugs[] = $slug;
                }
            }
            $analytics['summary']['active_combination'] = $active_slugs;

            /**
             * 분석 데이터 필터
             *
             * @since 1.0.0
             * @param array  $analytics 분석 데이터
             * @param string $period    기간
             */
            $analytics = apply_filters( '3j_labs_analytics_data', $analytics, $period );

            return new WP_REST_Response(
                array(
                    'success' => true,
                    'data'    => $analytics,
                ),
                200
            );
        }

        /**
         * 이벤트 로그 조회
         *
         * @since 1.0.0
         * @param WP_REST_Request $request 요청 객체
         * @return WP_REST_Response
         */
        public function get_events( $request ) {
            $limit  = $request->get_param( 'limit' );
            $plugin = $request->get_param( 'plugin' );

            $events = get_option( '3j_labs_event_log', array() );

            // 플러그인 필터링
            if ( $plugin ) {
                $events = array_filter( $events, function( $e ) use ( $plugin ) {
                    return $e['plugin'] === $plugin;
                });
            }

            // 최신순 정렬
            usort( $events, function( $a, $b ) {
                return $b['timestamp'] - $a['timestamp'];
            });

            // 제한
            $events = array_slice( $events, 0, $limit );

            return new WP_REST_Response(
                array(
                    'success' => true,
                    'data'    => $events,
                    'meta'    => array(
                        'total' => count( get_option( '3j_labs_event_log', array() ) ),
                        'limit' => $limit,
                    ),
                ),
                200
            );
        }

        /**
         * 이벤트 기록 (API 엔드포인트)
         *
         * @since 1.0.0
         * @param WP_REST_Request $request 요청 객체
         * @return WP_REST_Response
         */
        public function log_event( $request ) {
            $plugin = $request->get_param( 'plugin' );
            $event  = $request->get_param( 'event' );
            $data   = $request->get_param( 'data' );

            $this->record_event( $plugin, $event, $data );

            return new WP_REST_Response(
                array(
                    'success' => true,
                    'message' => '이벤트가 기록되었습니다.',
                ),
                201
            );
        }

        /**
         * 이벤트 기록 (내부 메서드)
         *
         * @since 1.0.0
         * @param string $plugin 플러그인 슬러그
         * @param string $event  이벤트 이름
         * @param array  $data   추가 데이터
         */
        public function record_event( $plugin, $event, $data = array() ) {
            $settings = get_option( '3j_labs_global_settings', array() );

            // 이벤트 로깅 비활성화 확인
            if ( isset( $settings['event_logging'] ) && ! $settings['event_logging'] ) {
                return;
            }

            $events = get_option( '3j_labs_event_log', array() );

            $events[] = array(
                'id'        => wp_generate_uuid4(),
                'plugin'    => $plugin,
                'event'     => $event,
                'data'      => $data,
                'timestamp' => time(),
                'user_id'   => get_current_user_id(),
            );

            // 로그 보존 기간 적용 (기본 30일)
            $retention = isset( $settings['log_retention'] ) ? intval( $settings['log_retention'] ) : 30;
            $cutoff    = strtotime( "-{$retention} days" );

            $events = array_filter( $events, function( $e ) use ( $cutoff ) {
                return $e['timestamp'] >= $cutoff;
            });

            update_option( '3j_labs_event_log', $events );

            /**
             * 이벤트 기록 후 액션
             *
             * @since 1.0.0
             * @param string $plugin 플러그인 슬러그
             * @param string $event  이벤트 이름
             * @param array  $data   추가 데이터
             */
            do_action( '3j_labs_event_recorded', $plugin, $event, $data );
        }

        /**
         * 플러그인 설정 조회
         *
         * @since 1.0.0
         * @param string $slug 플러그인 슬러그
         * @return array
         */
        private function get_plugin_settings( $slug ) {
            /**
             * 플러그인별 설정 조회 필터
             *
             * @since 1.0.0
             * @param array  $settings 설정 배열
             * @param string $slug     플러그인 슬러그
             */
            return apply_filters( "3j_labs_plugin_settings_{$slug}", array(), $slug );
        }

        /**
         * 플러그인 통계 조회
         *
         * @since 1.0.0
         * @param string $slug 플러그인 슬러그
         * @return array
         */
        private function get_plugin_stats( $slug ) {
            $events = get_option( '3j_labs_event_log', array() );
            $count  = 0;

            foreach ( $events as $event ) {
                if ( $event['plugin'] === $slug ) {
                    $count++;
                }
            }

            /**
             * 플러그인별 통계 조회 필터
             *
             * @since 1.0.0
             * @param array  $stats 통계 배열
             * @param string $slug  플러그인 슬러그
             */
            return apply_filters( "3j_labs_plugin_stats_{$slug}", array(
                'event_count' => $count,
            ), $slug );
        }

        /**
         * 등록된 플러그인 목록 반환
         *
         * @since 1.0.0
         * @return array
         */
        public function get_registered_plugins() {
            return $this->registered_plugins;
        }

        /**
         * API 정보 반환
         *
         * @since 1.0.0
         * @return array
         */
        public static function get_api_info() {
            return array(
                'namespace' => self::API_NAMESPACE,
                'version'   => self::API_VERSION,
                'base_url'  => rest_url( self::API_NAMESPACE ),
            );
        }
    }
}

// 자동 초기화
add_action( 'plugins_loaded', function() {
    JJ_3J_REST_API::instance();
}, 5 );
