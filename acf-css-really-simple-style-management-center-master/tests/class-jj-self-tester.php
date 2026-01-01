<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * JJ Self Tester
 * 
 * 플러그인 자가 진단 및 테스트 실행기
 * 
 * @since v6.2.0
 * @version 8.5.2 - 자동 진단 기능 추가
 */
class JJ_Self_Tester {

    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // 자동 진단 스케줄러 등록
        add_action( 'init', array( $this, 'schedule_auto_diagnostics' ) );
        add_action( 'jj_auto_diagnostics', array( $this, 'run_auto_diagnostics' ) );
        
        // 관리자 알림 표시
        add_action( 'admin_notices', array( $this, 'maybe_show_diagnostic_notices' ) );
    }

    /**
     * [Phase 8.5.2] 자동 진단 스케줄러 등록
     */
    public function schedule_auto_diagnostics() {
        if ( ! wp_next_scheduled( 'jj_auto_diagnostics' ) ) {
            // 매일 자정에 자동 진단 실행
            wp_schedule_event( time(), 'daily', 'jj_auto_diagnostics' );
        }
    }

    /**
     * [Phase 8.5.2] 자동 진단 실행
     */
    public function run_auto_diagnostics() {
        $results = self::run_tests();
        
        // 실패한 항목만 필터링
        $failures = array_filter( $results, function( $result ) {
            return isset( $result['status'] ) && $result['status'] === 'fail';
        });
        
        // 경고 항목 필터링
        $warnings = array_filter( $results, function( $result ) {
            return isset( $result['status'] ) && $result['status'] === 'warn';
        });
        
        // 결과를 옵션에 저장 (최근 10회)
        $diagnostic_history = get_option( 'jj_self_test_history', array() );
        array_unshift( $diagnostic_history, array(
            'timestamp' => current_time( 'mysql' ),
            'total' => count( $results ),
            'pass' => count( $results ) - count( $failures ) - count( $warnings ),
            'fail' => count( $failures ),
            'warn' => count( $warnings ),
            'failures' => array_slice( $failures, 0, 10 ), // 최대 10개만 저장
        ));
        
        if ( count( $diagnostic_history ) > 10 ) {
            $diagnostic_history = array_slice( $diagnostic_history, 0, 10 );
        }
        
        update_option( 'jj_self_test_history', $diagnostic_history );
        update_option( 'jj_self_test_last_run', current_time( 'mysql' ) );
        
        // 실패가 있으면 알림 플래그 설정
        if ( ! empty( $failures ) ) {
            update_option( 'jj_self_test_has_failures', true );
        } else {
            delete_option( 'jj_self_test_has_failures' );
        }
    }

    /**
     * [Phase 8.5.2] 관리자 알림 표시
     */
    public function maybe_show_diagnostic_notices() {
        // Admin Center 페이지에서만 표시
        $screen = get_current_screen();
        if ( ! $screen || strpos( $screen->id, 'jj-admin-center' ) === false ) {
            return;
        }
        
        // 실패가 있는지 확인
        $has_failures = get_option( 'jj_self_test_has_failures', false );
        if ( ! $has_failures ) {
            return;
        }
        
        // 최근 실행 시간 확인
        $last_run = get_option( 'jj_self_test_last_run' );
        if ( ! $last_run ) {
            return;
        }
        
        // 사용자가 이미 알림을 닫았는지 확인
        $dismissed = get_user_meta( get_current_user_id(), 'jj_diagnostic_notice_dismissed', true );
        if ( $dismissed && ( time() - intval( $dismissed ) ) < DAY_IN_SECONDS ) {
            return;
        }
        
        ?>
        <div class="notice notice-error jj-diagnostic-notice is-dismissible" style="border-left-color: #d63638;">
            <p>
                <strong>
                    <span class="dashicons dashicons-warning" style="color: #d63638; vertical-align: middle;"></span>
                    <?php esc_html_e( '시스템 자가 진단에서 문제가 발견되었습니다.', 'jj-style-guide' ); ?>
                </strong>
            </p>
            <p>
                <?php esc_html_e( '최근 자동 진단에서 일부 항목이 실패했습니다. System Status 탭에서 자세한 내용을 확인하세요.', 'jj-style-guide' ); ?>
            </p>
            <p>
                <a href="<?php echo esc_url( admin_url( 'options-general.php?page=jj-admin-center#system-status' ) ); ?>" class="button button-primary">
                    <?php esc_html_e( 'System Status 확인하기', 'jj-style-guide' ); ?>
                </a>
                <button type="button" class="button jj-dismiss-diagnostic-notice" style="margin-left: 5px;">
                    <?php esc_html_e( '24시간 동안 숨기기', 'jj-style-guide' ); ?>
                </button>
            </p>
        </div>
        <script>
        jQuery(document).ready(function($) {
            $('.jj-dismiss-diagnostic-notice').on('click', function() {
                $.post(ajaxurl, {
                    action: 'jj_dismiss_diagnostic_notice',
                    nonce: '<?php echo esc_js( wp_create_nonce( 'jj_diagnostic_notice' ) ); ?>'
                });
                $('.jj-diagnostic-notice').fadeOut();
            });
        });
        </script>
        <?php
    }

    /**
     * [Phase 8.5.2] 진단 이력 가져오기
     */
    public static function get_diagnostic_history() {
        return get_option( 'jj_self_test_history', array() );
    }

    /**
     * [Phase 8.5.2] 최근 진단 결과 요약
     */
    public static function get_last_diagnostic_summary() {
        $history = self::get_diagnostic_history();
        if ( empty( $history ) ) {
            return null;
        }
        return $history[0];
    }

    public static function run_tests() {
        $results = array();

        // 0. Constants / Helpers
        $consts = array(
            'JJ_STYLE_GUIDE_VERSION',
            'JJ_STYLE_GUIDE_PATH',
            'JJ_STYLE_GUIDE_URL',
            'JJ_STYLE_GUIDE_OPTIONS_KEY',
        );
        foreach ( $consts as $c ) {
            $results[] = array(
                'test'    => "Constant: {$c}",
                'status'  => defined( $c ) ? 'pass' : 'fail',
                'message' => defined( $c ) ? 'Defined' : 'Not defined',
            );
        }

        $results[] = array(
            'test'    => 'Helper: jj_style_guide_text()',
            'status'  => function_exists( 'jj_style_guide_text' ) ? 'pass' : 'warn',
            'message' => function_exists( 'jj_style_guide_text' ) ? 'Available' : 'Missing (fallback recommended)',
        );

        // 1. Core Classes Check
        $core_classes = array(
            'JJ_Admin_Center',
            'JJ_Safe_Loader',
            'JJ_Edition_Controller',
            'JJ_CSS_Cache',
            'JJ_Options_Cache',
            'JJ_CSS_Injector',
            'JJ_Smart_Palette', // AI
            'JJ_Cloud_Manager', // Cloud
            'JJ_Visual_Command_Center', // Visual
            'JJ_Partner_Hub', // Partner (Optional)
        );

        foreach ( $core_classes as $class ) {
            if ( class_exists( $class ) ) {
                $results[] = array( 'test' => "Class: $class", 'status' => 'pass', 'message' => 'Loaded successfully' );
            } else {
                // Partner Hub is optional depending on edition
                if ( $class === 'JJ_Partner_Hub' ) {
                    if ( class_exists( 'JJ_Edition_Controller' ) && method_exists( 'JJ_Edition_Controller', 'instance' ) ) {
                        $license_type = JJ_Edition_Controller::instance()->get_license_type();
                        if ( $license_type !== 'PARTNER' && $license_type !== 'MASTER' ) {
                            $results[] = array( 'test' => "Class: $class", 'status' => 'skip', 'message' => 'Skipped (Edition mismatch)' );
                        } else {
                            $results[] = array( 'test' => "Class: $class", 'status' => 'fail', 'message' => 'Class not found' );
                        }
                    } else {
                        $results[] = array( 'test' => "Class: $class", 'status' => 'warn', 'message' => 'Edition controller missing (cannot decide optionality)' );
                    }
                } else {
                    $results[] = array( 'test' => "Class: $class", 'status' => 'fail', 'message' => 'Class not found' );
                }
            }
        }

        // 1.5 Safe Loader Diagnostics (missing/failed requires)
        if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'get_missing_files' ) ) {
            $missing = (array) JJ_Safe_Loader::get_missing_files();
            $req = array();
            $opt = array();
            foreach ( $missing as $m ) {
                if ( ! is_array( $m ) ) continue;
                if ( ! empty( $m['required'] ) ) {
                    $req[] = $m;
                } else {
                    $opt[] = $m;
                }
            }

            $results[] = array(
                'test'    => 'Safe Loader: Required files',
                'status'  => empty( $req ) ? 'pass' : 'fail',
                'message' => empty( $req )
                    ? 'No missing required files'
                    : ( 'Missing required files: ' . count( $req ) ),
            );

            $results[] = array(
                'test'    => 'Safe Loader: Optional files',
                'status'  => empty( $opt ) ? 'pass' : 'warn',
                'message' => empty( $opt )
                    ? 'No missing optional files'
                    : ( 'Missing optional files: ' . count( $opt ) ),
            );

            // required 누락은 "원인 파일"을 바로 볼 수 있도록 개별 항목으로 출력(최대 30개)
            if ( ! empty( $req ) ) {
                $max = 30;
                $n = 0;
                foreach ( $req as $r ) {
                    if ( $n >= $max ) {
                        $results[] = array(
                            'test'    => 'Safe Loader: Required files (more)',
                            'status'  => 'fail',
                            'message' => 'Too many missing required files. (truncated)',
                        );
                        break;
                    }
                    $path = isset( $r['path'] ) ? (string) $r['path'] : '';
                    $short = $path;
                    if ( defined( 'JJ_STYLE_GUIDE_PATH' ) && '' !== $path ) {
                        $short = str_replace( JJ_STYLE_GUIDE_PATH, '', $path );
                        $short = ltrim( $short, "/\\" );
                    }
                    $count = isset( $r['count'] ) ? (int) $r['count'] : 1;
                    $results[] = array(
                        'test'    => 'Missing required file: ' . $short,
                        'status'  => 'fail',
                        'message' => 'Not found (count=' . $count . ')',
                    );
                    $n++;
                }
            }
        } else {
            // 폴백 로더 진단( Safe Loader 자체 누락/손상 대비 )
            if ( isset( $GLOBALS['jj_style_guide_boot_diag'] ) && is_array( $GLOBALS['jj_style_guide_boot_diag'] ) ) {
                $mr = isset( $GLOBALS['jj_style_guide_boot_diag']['missing_required'] ) && is_array( $GLOBALS['jj_style_guide_boot_diag']['missing_required'] )
                    ? array_values( $GLOBALS['jj_style_guide_boot_diag']['missing_required'] )
                    : array();
                $mo = isset( $GLOBALS['jj_style_guide_boot_diag']['missing_optional'] ) && is_array( $GLOBALS['jj_style_guide_boot_diag']['missing_optional'] )
                    ? array_values( $GLOBALS['jj_style_guide_boot_diag']['missing_optional'] )
                    : array();
                $errs = isset( $GLOBALS['jj_style_guide_boot_diag']['load_errors'] ) && is_array( $GLOBALS['jj_style_guide_boot_diag']['load_errors'] )
                    ? $GLOBALS['jj_style_guide_boot_diag']['load_errors']
                    : array();

                $results[] = array(
                    'test'    => 'Fallback Loader: Required files',
                    'status'  => empty( $mr ) ? 'pass' : 'fail',
                    'message' => empty( $mr ) ? 'No missing required files' : ( 'Missing required files: ' . count( $mr ) ),
                );
                $results[] = array(
                    'test'    => 'Fallback Loader: Optional files',
                    'status'  => empty( $mo ) ? 'pass' : 'warn',
                    'message' => empty( $mo ) ? 'No missing optional files' : ( 'Missing optional files: ' . count( $mo ) ),
                );
                $results[] = array(
                    'test'    => 'Fallback Loader: Load errors',
                    'status'  => empty( $errs ) ? 'pass' : 'fail',
                    'message' => empty( $errs ) ? 'No load errors' : ( 'Load errors detected: ' . count( $errs ) ),
                );
            } else {
                $results[] = array(
                    'test'    => 'Safe Loader: Diagnostics',
                    'status'  => 'warn',
                    'message' => 'Safe/Fallback loader diagnostics not available',
                );
            }
        }

        if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'get_load_errors' ) ) {
            $errs = (array) JJ_Safe_Loader::get_load_errors();
            $results[] = array(
                'test'    => 'Safe Loader: Load errors',
                'status'  => empty( $errs ) ? 'pass' : 'fail',
                'message' => empty( $errs ) ? 'No load errors' : ( 'Load errors detected: ' . count( $errs ) ),
            );

            if ( ! empty( $errs ) ) {
                $max = 30;
                $n = 0;
                foreach ( $errs as $e ) {
                    if ( ! is_array( $e ) ) continue;
                    if ( $n >= $max ) {
                        $results[] = array(
                            'test'    => 'Safe Loader: Load errors (more)',
                            'status'  => 'fail',
                            'message' => 'Too many load errors. (truncated)',
                        );
                        break;
                    }
                    $path = isset( $e['path'] ) ? (string) $e['path'] : '';
                    $short = $path;
                    if ( defined( 'JJ_STYLE_GUIDE_PATH' ) && '' !== $path ) {
                        $short = str_replace( JJ_STYLE_GUIDE_PATH, '', $path );
                        $short = ltrim( $short, "/\\" );
                    }
                    $msg = isset( $e['error'] ) ? (string) $e['error'] : 'Unknown error';
                    if ( function_exists( 'mb_substr' ) ) {
                        $msg = mb_substr( $msg, 0, 220 );
                    } else {
                        $msg = substr( $msg, 0, 220 );
                    }
                    $results[] = array(
                        'test'    => 'Load error: ' . $short,
                        'status'  => 'fail',
                        'message' => $msg,
                    );
                    $n++;
                }
            }
        }

        // 1.6 File Manifest (critical templates/views)
        $manifest = array(
            // Admin Center tabs
            'includes/admin/views/tabs/tab-updates.php'       => true,
            'includes/admin/views/tabs/tab-system-status.php' => true,
            // Editor views
            'includes/editor-views/view-section-colors.php'   => true,
            // Presets assets
            'assets/js/jj-style-guide-presets.js'             => true,
            'assets/css/jj-style-guide-presets.css'           => true,
        );
        if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            foreach ( $manifest as $rel => $required ) {
                $path = JJ_STYLE_GUIDE_PATH . ltrim( $rel, '/\\' );
                $exists = file_exists( $path );
                $results[] = array(
                    'test'    => 'File: ' . $rel,
                    'status'  => $exists ? 'pass' : ( $required ? 'fail' : 'warn' ),
                    'message' => $exists ? 'OK' : 'Missing',
                );
            }
        }

        // 2. Directory Permissions
        $dirs = array(
            JJ_STYLE_GUIDE_PATH . 'assets/css/generated' => 'is_writable',
        );

        foreach ( $dirs as $path => $check ) {
            if ( file_exists( $path ) ) {
                if ( is_writable( $path ) ) {
                    $results[] = array( 'test' => "Dir: " . basename( $path ), 'status' => 'pass', 'message' => 'Writable' );
                } else {
                    $results[] = array( 'test' => "Dir: " . basename( $path ), 'status' => 'warn', 'message' => 'Not writable' );
                }
            } else {
                $results[] = array( 'test' => "Dir: " . basename( $path ), 'status' => 'warn', 'message' => 'Not found (Auto-created on save)' );
            }
        }

        // 3. Option Integrity
        $options = get_option( 'jj_style_guide_options' );
        if ( is_array( $options ) ) {
            $results[] = array( 'test' => "Options Integrity", 'status' => 'pass', 'message' => 'Valid Array' );
        } else {
            $results[] = array( 'test' => "Options Integrity", 'status' => 'fail', 'message' => 'Corrupted or Missing' );
        }

        // 4. Update / Cron health
        if ( function_exists( 'wp_next_scheduled' ) ) {
            $next = (int) wp_next_scheduled( 'wp_update_plugins' );
            $results[] = array(
                'test'    => 'Cron: wp_update_plugins',
                'status'  => $next ? 'pass' : 'warn',
                'message' => $next ? ( 'Next: ' . ( function_exists( 'date_i18n' ) ? date_i18n( 'Y-m-d H:i:s', $next ) : (string) $next ) ) : 'Not scheduled (may be disabled)',
            );
        } else {
            $results[] = array( 'test' => 'Cron: wp_update_plugins', 'status' => 'warn', 'message' => 'wp_next_scheduled() not available' );
        }

        // 5. Update transient basic sanity
        if ( function_exists( 'get_site_transient' ) ) {
            $u = get_site_transient( 'update_plugins' );
            $last_checked = ( is_object( $u ) && isset( $u->last_checked ) ) ? (int) $u->last_checked : 0;
            $resp = ( is_object( $u ) && isset( $u->response ) && is_array( $u->response ) ) ? $u->response : array();
            $results[] = array(
                'test'    => 'Update transient: update_plugins',
                'status'  => $u ? 'pass' : 'warn',
                'message' => $u ? ( 'last_checked=' . ( $last_checked ? ( function_exists( 'date_i18n' ) ? date_i18n( 'Y-m-d H:i:s', $last_checked ) : (string) $last_checked ) : '0' ) . ', updates=' . count( $resp ) ) : 'Missing (will be generated on update check)',
            );
        } else {
            $results[] = array( 'test' => 'Update transient: update_plugins', 'status' => 'warn', 'message' => 'get_site_transient() not available' );
        }

        // 6. Auto-update site option sanity
        if ( function_exists( 'get_site_option' ) ) {
            $auto = get_site_option( 'auto_update_plugins', array() );
            $results[] = array(
                'test'    => 'Site option: auto_update_plugins',
                'status'  => is_array( $auto ) ? 'pass' : 'fail',
                'message' => is_array( $auto ) ? ( 'Count: ' . count( $auto ) ) : 'Invalid type (should be array)',
            );
        }

        // 7. Required plugins (WooCommerce) status (if plugin.php available)
        if ( defined( 'ABSPATH' ) ) {
            $plugin_php = ABSPATH . 'wp-admin/includes/plugin.php';
            if ( file_exists( $plugin_php ) ) {
                require_once $plugin_php;
            }
        }
        if ( function_exists( 'is_plugin_active' ) ) {
            $woo_pf = 'woocommerce/woocommerce.php';
            $woo_active = is_plugin_active( $woo_pf );
            $results[] = array(
                'test'    => 'Required: WooCommerce',
                'status'  => $woo_active ? 'pass' : 'warn',
                'message' => $woo_active ? 'Active' : 'Inactive or not installed (Woo License add-on requires it)',
            );
        }

        // [Phase 8.0] 실제 운영 체크리스트 확장

        // 8. AJAX / Nonce 검증
        $results[] = array(
            'test'    => 'AJAX: Nonce verification',
            'status'  => ( defined( 'JJ_STYLE_GUIDE_VERSION' ) && function_exists( 'wp_create_nonce' ) ) ? 'pass' : 'warn',
            'message' => ( defined( 'JJ_STYLE_GUIDE_VERSION' ) && function_exists( 'wp_create_nonce' ) )
                ? 'AJAX nonce functions available'
                : 'Nonce functions may not be available',
        );

        if ( class_exists( 'JJ_Admin_Center' ) && method_exists( 'JJ_Admin_Center', 'instance' ) ) {
            $admin_center = JJ_Admin_Center::instance();
            if ( method_exists( $admin_center, 'get_ajax_url' ) ) {
                $ajax_url = $admin_center->get_ajax_url();
                $results[] = array(
                    'test'    => 'AJAX: URL configured',
                    'status'  => ! empty( $ajax_url ) ? 'pass' : 'warn',
                    'message' => ! empty( $ajax_url ) ? ( 'URL: ' . $ajax_url ) : 'AJAX URL not configured',
                );
            }
        }

        // 9. 업데이트 트랜지언트 상태
        if ( function_exists( 'get_site_transient' ) ) {
            $update_transient = get_site_transient( 'update_plugins' );
            $transient_valid = ( is_object( $update_transient ) || is_array( $update_transient ) );
            $transient_age = 0;
            if ( is_object( $update_transient ) && isset( $update_transient->last_checked ) ) {
                $transient_age = time() - (int) $update_transient->last_checked;
            }
            
            $results[] = array(
                'test'    => 'Update Transient: Status',
                'status'  => $transient_valid ? ( $transient_age < 86400 ? 'pass' : 'warn' ) : 'warn',
                'message' => $transient_valid
                    ? ( 'Last checked: ' . ( $transient_age < 3600 ? $transient_age . 's ago' : ( floor( $transient_age / 3600 ) . 'h ago' ) ) )
                    : 'Transient not found or invalid',
            );
        }

        // 10. Cron 스케줄 상태
        if ( function_exists( 'wp_next_scheduled' ) ) {
            $wp_update_plugins_next = wp_next_scheduled( 'wp_update_plugins' );
            $wp_update_plugins_status = $wp_update_plugins_next ? 'pass' : 'warn';
            $results[] = array(
                'test'    => 'Cron: wp_update_plugins',
                'status'  => $wp_update_plugins_status,
                'message' => $wp_update_plugins_next
                    ? ( 'Next scheduled: ' . ( function_exists( 'date_i18n' ) ? date_i18n( 'Y-m-d H:i:s', $wp_update_plugins_next ) : (string) $wp_update_plugins_next ) )
                    : 'Not scheduled (may be disabled)',
            );
            
            $wp_update_themes_next = wp_next_scheduled( 'wp_update_themes' );
            $results[] = array(
                'test'    => 'Cron: wp_update_themes',
                'status'  => $wp_update_themes_next ? 'pass' : 'warn',
                'message' => $wp_update_themes_next
                    ? ( 'Next scheduled: ' . ( function_exists( 'date_i18n' ) ? date_i18n( 'Y-m-d H:i:s', $wp_update_themes_next ) : (string) $wp_update_themes_next ) )
                    : 'Not scheduled (may be disabled)',
            );
        } else {
            $results[] = array(
                'test'    => 'Cron: Functions',
                'status'  => 'warn',
                'message' => 'wp_next_scheduled() not available',
            );
        }

        // 11. 권한 확인
        $cap_manage_options = current_user_can( 'manage_options' );
        $cap_install_plugins = current_user_can( 'install_plugins' );
        $cap_activate_plugins = current_user_can( 'activate_plugins' );
        
        $results[] = array(
            'test'    => 'Permissions: manage_options',
            'status'  => $cap_manage_options ? 'pass' : 'fail',
            'message' => $cap_manage_options ? 'User can manage options' : 'User cannot manage options (critical)',
        );
        
        $results[] = array(
            'test'    => 'Permissions: install_plugins',
            'status'  => $cap_install_plugins ? 'pass' : 'warn',
            'message' => $cap_install_plugins ? 'User can install plugins' : 'User cannot install plugins (plugin updates may fail)',
        );
        
        $results[] = array(
            'test'    => 'Permissions: activate_plugins',
            'status'  => $cap_activate_plugins ? 'pass' : 'warn',
            'message' => $cap_activate_plugins ? 'User can activate plugins' : 'User cannot activate plugins (add-on activation may fail)',
        );

        // 12. 필수 플러그인 연동 (WooCommerce 상세)
        if ( function_exists( 'is_plugin_active' ) ) {
            $required_plugins = array(
                'woocommerce/woocommerce.php' => array(
                    'name' => 'WooCommerce',
                    'required_for' => 'Woo License add-on',
                ),
            );
            
            foreach ( $required_plugins as $plugin_file => $plugin_info ) {
                $installed = function_exists( 'get_plugins' ) && isset( get_plugins()[ $plugin_file ] );
                $active = is_plugin_active( $plugin_file );
                
                if ( $installed ) {
                    $plugin_data = get_plugins()[ $plugin_file ];
                    $version = isset( $plugin_data['Version'] ) ? $plugin_data['Version'] : 'unknown';
                    
                    $results[] = array(
                        'test'    => 'Plugin Integration: ' . $plugin_info['name'],
                        'status'  => $active ? 'pass' : 'warn',
                        'message' => $active
                            ? ( 'Active (v' . $version . ') - ' . $plugin_info['required_for'] )
                            : ( 'Installed but inactive (v' . $version . ') - ' . $plugin_info['required_for'] ),
                    );
                } else {
                    $results[] = array(
                        'test'    => 'Plugin Integration: ' . $plugin_info['name'],
                        'status'  => 'warn',
                        'message' => 'Not installed - ' . $plugin_info['required_for'],
                    );
                }
            }
        }

        // 13. PHP/WordPress 환경 호환성
        $php_version = PHP_VERSION;
        $php_major = (int) explode( '.', $php_version )[0];
        $php_minor = (int) explode( '.', $php_version )[1];
        $php_compatible = ( $php_major > 7 || ( $php_major === 7 && $php_minor >= 4 ) );
        
        $results[] = array(
            'test'    => 'Environment: PHP Version',
            'status'  => $php_compatible ? 'pass' : 'fail',
            'message' => 'PHP ' . $php_version . ( $php_compatible ? ' (Compatible)' : ' (Requires PHP 7.4+)' ),
        );

        $wp_version = get_bloginfo( 'version' );
        $wp_major = (float) $wp_version;
        $wp_compatible = $wp_major >= 5.0;
        
        $results[] = array(
            'test'    => 'Environment: WordPress Version',
            'status'  => $wp_compatible ? 'pass' : 'warn',
            'message' => 'WordPress ' . $wp_version . ( $wp_compatible ? ' (Compatible)' : ' (Requires WordPress 5.0+)' ),
        );

        // 14. 데이터베이스 연결 상태
        global $wpdb;
        if ( isset( $wpdb ) && method_exists( $wpdb, 'get_var' ) ) {
            $db_check = $wpdb->get_var( 'SELECT 1' );
            $results[] = array(
                'test'    => 'Database: Connection',
                'status'  => ( $db_check === '1' || $db_check === 1 ) ? 'pass' : 'fail',
                'message' => ( $db_check === '1' || $db_check === 1 ) ? 'Database connection OK' : 'Database connection failed',
            );
        } else {
            $results[] = array(
                'test'    => 'Database: Connection',
                'status'  => 'warn',
                'message' => 'Database object not available',
            );
        }

        return $results;
    }
}

