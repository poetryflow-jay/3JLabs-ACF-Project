<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * J&J 실험실
 * - CSS/HTML/JS 스캐너 및 수동 오버라이드 기능을 위한 별도 페이지
 * - Admin Center와 유사한 구조로 분리된 고급 기능 영역
 * 
 * @version 4.2.2
 * - "실험실 센터" → "실험실" 명칭 변경
 * - Labs → 실험실 명칭 변경
 * - 별도 페이지로 분리 (스타일 센터에서 독립)
 * - CSS/HTML/JS 스캐너 기능 강화
 * - 탭 기반 인터페이스 (스캐너, 수동 재정의, 공식 지원 목록)
 */
final class JJ_Labs_Center {

    private static $instance = null;
    private $option_key = 'jj_style_guide_options'; // labs 섹션 포함
    private $labs_tabs_option_key = 'jj_style_guide_labs_tabs_layout'; // [v5.0.0] 실험실 탭 활성화/비활성화
    private $adapters_config = array();
    private $options = array();
    
    // [v5.0.0] 성능 최적화: 실험실 탭 레이아웃 캐싱
    private static $labs_tabs_layout_cache = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            $config_file = JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php';
            if ( file_exists( $config_file ) ) {
                $this->adapters_config = include $config_file;
            }
        }
    }

    /**
     * 초기화: 메뉴 등록 훅 연결
     */
    public function init() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_labs_center_assets' ) );
        add_action( 'admin_init', array( $this, 'save_labs_tabs_layout' ) ); // [v5.0.0] 실험실 탭 활성화/비활성화 저장
        // [v3.7.0] 스캐너 AJAX
        add_action( 'wp_ajax_jj_scan_page_for_css', array( $this, 'ajax_scan_page_for_css' ) );
    }

    /**
     * [v3.7.0] AJAX: 페이지 스캔(최소 유용 버전)
     * - URL의 HTML을 가져와 class/id, style block, 주요 색상/폰트를 추출합니다.
     * - 크로스오리진/로그인/차단 환경에서는 일부 정보만 제공될 수 있습니다.
     */
    public function ajax_scan_page_for_css() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $scan_url = isset( $_POST['scan_url'] ) ? esc_url_raw( wp_unslash( $_POST['scan_url'] ) ) : '';
        if ( empty( $scan_url ) ) {
            wp_send_json_error( array( 'message' => __( 'URL을 입력해주세요.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        if ( function_exists( 'wp_http_validate_url' ) && ! wp_http_validate_url( $scan_url ) ) {
            wp_send_json_error( array( 'message' => __( '유효하지 않은 URL입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $response = wp_remote_get(
            $scan_url,
            array(
                'timeout'     => 15,
                'redirection' => 3,
                'user-agent'  => 'JJ-Labs-Scanner/1.0 (+WordPress)',
            )
        );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( array( 'message' => __( '서버 통신 오류: ', 'acf-css-really-simple-style-management-center' ) . $response->get_error_message() ) );
        }

        $html = wp_remote_retrieve_body( $response );
        if ( empty( $html ) ) {
            wp_send_json_error( array( 'message' => __( '페이지 내용을 가져오지 못했습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // 1) style blocks
        $style_blocks = array();
        if ( preg_match_all( '/<style[^>]*>(.*?)<\\/style>/is', $html, $m ) ) {
            foreach ( (array) ( $m[1] ?? array() ) as $block ) {
                $block = trim( (string) $block );
                if ( '' !== $block ) {
                    $style_blocks[] = $block;
                }
            }
        }
        $style_blocks = array_slice( $style_blocks, 0, 10 );

        // 2) class selectors
        $class_selectors = array();
        if ( preg_match_all( '/class\\s*=\\s*[\"\\\']([^\"\\\']+)[\"\\\']/i', $html, $cm ) ) {
            foreach ( (array) ( $cm[1] ?? array() ) as $class_attr ) {
                $parts = preg_split( '/\\s+/', trim( (string) $class_attr ) );
                foreach ( (array) $parts as $cls ) {
                    $cls = preg_replace( '/[^a-zA-Z0-9_-]/', '', (string) $cls );
                    if ( '' !== $cls ) {
                        $class_selectors[] = '.' . $cls;
                    }
                }
            }
        }
        $class_selectors = array_values( array_unique( $class_selectors ) );
        sort( $class_selectors );

        // 3) id selectors
        $id_selectors = array();
        if ( preg_match_all( '/id\\s*=\\s*[\"\\\']([^\"\\\']+)[\"\\\']/i', $html, $im ) ) {
            foreach ( (array) ( $im[1] ?? array() ) as $id ) {
                $id = preg_replace( '/[^a-zA-Z0-9_-]/', '', (string) $id );
                if ( '' !== $id ) {
                    $id_selectors[] = '#' . $id;
                }
            }
        }
        $id_selectors = array_values( array_unique( $id_selectors ) );
        sort( $id_selectors );

        // 4) colors (hex + rgb)
        $scan_text = $html . "\n\n" . implode( "\n\n", $style_blocks );
        $color_counts = array();

        // hex colors
        if ( preg_match_all( '/#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})\\b/', $scan_text, $hm ) ) {
            foreach ( (array) ( $hm[0] ?? array() ) as $hex ) {
                $hex = strtoupper( $hex );
                if ( strlen( $hex ) === 4 ) {
                    // #RGB -> #RRGGBB
                    $hex = '#' . $hex[1] . $hex[1] . $hex[2] . $hex[2] . $hex[3] . $hex[3];
                }
                $color_counts[ $hex ] = ( $color_counts[ $hex ] ?? 0 ) + 1;
            }
        }

        // rgb/rgba colors
        if ( preg_match_all( '/rgba?\\(\\s*(\\d{1,3})\\s*,\\s*(\\d{1,3})\\s*,\\s*(\\d{1,3})/i', $scan_text, $rm, PREG_SET_ORDER ) ) {
            foreach ( $rm as $match ) {
                $r = max( 0, min( 255, intval( $match[1] ) ) );
                $g = max( 0, min( 255, intval( $match[2] ) ) );
                $b = max( 0, min( 255, intval( $match[3] ) ) );
                $hex = sprintf( '#%02X%02X%02X', $r, $g, $b );
                $color_counts[ $hex ] = ( $color_counts[ $hex ] ?? 0 ) + 1;
            }
        }

        arsort( $color_counts );
        $colors = array_slice( array_keys( $color_counts ), 0, 50 );

        // 5) fonts
        $fonts = array();
        if ( preg_match_all( '/font-family\\s*:\\s*([^;\\}\\n]+)[;\\}]/i', $scan_text, $fm ) ) {
            foreach ( (array) ( $fm[1] ?? array() ) as $font_list ) {
                $parts = explode( ',', (string) $font_list );
                foreach ( $parts as $font ) {
                    $font = trim( (string) $font );
                    $font = trim( $font, "\"'" );
                    if ( '' === $font ) {
                        continue;
                    }
                    $fonts[] = $font;
                }
            }
        }
        $fonts = array_values( array_unique( $fonts ) );
        $fonts = array_slice( $fonts, 0, 20 );

        // 6) conflicts (간단)
        $conflicts = array();
        if ( count( $colors ) >= 25 ) {
            $conflicts[] = array(
                'type' => 'warning',
                'message' => __( '색상이 너무 다양합니다.', 'acf-css-really-simple-style-management-center' ),
                'suggestion' => __( '상위 2~4개 색상만 브랜드/시스템 팔레트로 정리하면 UI 일관성이 좋아집니다.', 'acf-css-really-simple-style-management-center' ),
            );
        }

        $html_snippet = substr( $html, 0, 2000 );

        wp_send_json_success(
            array(
                'scan_url' => $scan_url,
                'class_selectors' => $class_selectors,
                'id_selectors' => $id_selectors,
                'style_blocks' => $style_blocks,
                'html_snippet' => $html_snippet,
                'colors' => $colors,
                'fonts' => $fonts,
                'conflicts' => $conflicts,
                'suggestions' => array(
                    'primary_colors' => array_slice( $colors, 0, 6 ),
                    'font_families' => array_slice( $fonts, 0, 6 ),
                ),
            )
        );
    }

    /**
     * 실험실 전용 CSS/JS enqueue
     */
    public function enqueue_labs_center_assets( $hook ) {
        // 실험실 페이지에서만 로드
        if ( 'settings_page_jj-labs-center' !== $hook ) {
            return;
        }

        wp_enqueue_media(); // 미디어 업로더 (향후 확장용)
        wp_enqueue_style( 'wp-color-picker' );
        
        // [v3.7.0 '신규'] WordPress CodeMirror for CSS 에디터
        $code_editor_settings = wp_get_code_editor_settings( array( 'type' => 'text/css' ) );
        wp_enqueue_code_editor( $code_editor_settings );
        
        // [Safety] 상수 미정의(로드 순서 문제) 대비
        $base_url = defined( 'JJ_STYLE_GUIDE_URL' ) ? JJ_STYLE_GUIDE_URL : plugin_dir_url( dirname( __FILE__ ) ) . '../';
        $version  = defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.0.0';

        $css_url = $base_url . 'assets/css/jj-labs-center.css';
        $js_url  = $base_url . 'assets/js/jj-labs-center.js';

        wp_enqueue_style(
            'jj-labs-center',
            $css_url,
            array( 'wp-color-picker', 'code-editor' ),
            $version
        );

        wp_enqueue_script(
            'jj-labs-center',
            $js_url,
            array( 'jquery', 'wp-color-picker', 'code-editor' ),
            $version,
            true
        );

        // AJAX 파라미터 로컬라이즈
        wp_localize_script(
            'jj-labs-center',
            'jjLabsCenter',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'jj_style_guide_nonce' ),
            )
        );
        
        // 스타일 가이드 에디터 스크립트도 로드 (스타일 저장 기능용)
        wp_enqueue_script(
            'jj-style-guide-editor',
            $base_url . 'assets/js/jj-style-guide-editor.js',
            array( 'jquery', 'wp-color-picker' ),
            $version,
            true
        );
        
        // [v5.0.3] 키보드 단축키 시스템 로드
        wp_enqueue_script( 'jj-keyboard-shortcuts', $base_url . 'assets/js/jj-keyboard-shortcuts.js', array( 'jquery' ), $version, true );
        
        // [v5.0.3] 툴팁 시스템 로드
        wp_enqueue_script( 'jj-tooltips', $base_url . 'assets/js/jj-tooltips.js', array( 'jquery' ), $version, true );
        
        $this->options = (array) get_option( $this->option_key );
        wp_localize_script(
            'jj-style-guide-editor',
            'jj_admin_params',
            array(
                'nonce'    => wp_create_nonce( 'jj_style_guide_nonce' ),
                'temp_palette_nonce' => wp_create_nonce( 'jj_temp_palette_nonce' ),
                'settings' => $this->options,
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'locale'   => get_locale(), // [v5.0.3] 다국어 지원을 위한 로케일 전달
                'i18n'     => array(
                    'saving' => __( '저장 중...', 'acf-css-really-simple-style-management-center' ),
                    'saved'  => __( '스타일 저장됨', 'acf-css-really-simple-style-management-center' ),
                ),
            )
        );
    }

    /**
     * "J&J 실험실" 서브메뉴 추가
     * [v20.2.5] 등록 로직 간소화 및 권한 체크 강화
     */
    public function add_admin_menu_page() {
        // [v22.0.0] JJ_Admin_Center::add_admin_menu_page()에서 서브메뉴로 일괄 관리하므로
        // 여기서는 개별적인 메뉴 등록을 수행하지 않습니다. (중복 방지)
    }

    /**
     * 실험실 화면 렌더링
     */
    public function render_labs_center_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $this->options = (array) get_option( $this->option_key );
        $options = $this->options;
        
        // [v5.1.6] Master/Partner에서는 업그레이드 유도 UI를 절대 표시하지 않음
        $is_master_version = false;
        $is_partner_or_higher = false;
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $edition = JJ_Edition_Controller::instance();
                $is_master_version = $edition->is_at_least( 'master' );
                $is_partner_or_higher = $edition->is_at_least( 'partner' );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        if ( ! $is_partner_or_higher && defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $is_partner_or_higher = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
        }
        $purchase_url = '';
        if ( ! $is_partner_or_higher ) {
            $license_manager = null;
            $purchase_url = 'https://j-j-labs.com'; // 기본값
            if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
                require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
                if ( class_exists( 'JJ_License_Manager' ) ) {
                    $license_manager = JJ_License_Manager::instance();
                    if ( $license_manager ) {
                        $purchase_url = $license_manager->get_purchase_url( 'upgrade' );
                    }
                }
            }
        }
        $labs_options = $options['labs'] ?? array();
        $adapters_config = $this->adapters_config;

        ?>
        <?php if ( ! $is_partner_or_higher && $purchase_url ) : ?>
        <div class="notice notice-info" style="margin: 20px 20px 0 0;">
            <p style="display: flex; align-items: center; gap: 10px;">
                <strong><?php esc_html_e( '더 많은 기능을 사용하려면 업그레이드하세요!', 'acf-css-really-simple-style-management-center' ); ?></strong>
                <a href="<?php echo esc_url( $purchase_url ); ?>" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="button button-primary" 
                   style="font-size: 13px; padding: 0 16px; height: 36px; line-height: 34px; font-weight: 600;">
                    <span class="dashicons dashicons-cart" style="margin-top: 4px;"></span> <?php esc_html_e( '업그레이드하기', 'acf-css-really-simple-style-management-center' ); ?>
                </a>
            </p>
        </div>
        <?php endif; ?>
        <div class="wrap jj-labs-center-wrap">
            <div class="jj-labs-center-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 8px;">
                        <h1 style="margin: 0;"><?php _e( 'ACF CSS 실험실 센터', 'acf-css-really-simple-style-management-center' ); ?> <span class="version">v<?php echo esc_html( defined('JJ_STYLE_GUIDE_VERSION') ? JJ_STYLE_GUIDE_VERSION : '8.0.0' ); ?></span></h1>
                        <a href="<?php echo esc_url( admin_url( 'options-general.php?page=' . JJ_STYLE_GUIDE_PAGE_SLUG ) ); ?>" 
                           class="button button-secondary" 
                           style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                            <?php _e( '스타일 센터', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                        <a href="<?php echo esc_url( admin_url( 'options-general.php?page=jj-admin-center' ) ); ?>" 
                           class="button button-secondary" 
                           style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                            <?php _e( '관리자 센터', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                        <?php
                        // [v5.1.6] 마스터 버전이 아닌 경우 결제 유도 문구 표시
                        if ( ! $is_partner_or_higher && $purchase_url ) {
                            ?>
                            <a href="<?php echo esc_url( $purchase_url ); ?>" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="button button-primary" 
                               style="font-size: 13px; padding: 0 16px; height: 36px; line-height: 34px; font-weight: 600;">
                                <span class="dashicons dashicons-cart" style="margin-top: 4px;"></span> <?php _e( '업그레이드하기', 'acf-css-really-simple-style-management-center' ); ?>
                            </a>
                            <?php
                        }
                        ?>
                    </div>
                    <p class="description" style="margin: 0; line-height: 1.6;">
                        <span class="jj-labs-tab-description" data-tab-type="scanner" data-tooltip="labs-tab-scanner">
                            <?php _e( 'ACF CSS 실험실 센터는 아직 공식적으로 지원되지 않는 테마나 플러그인의 스타일을 분석하고 세밀하게 조정하기 위한 고급 개발자 도구입니다.', 'acf-css-really-simple-style-management-center' ); ?>
                        </span>
                        <br>
                        <span style="color: #666;">
                            <?php _e( '• <strong>CSS/HTML/JS 스캐너</strong>: 분석하고 싶은 페이지의 URL을 입력하거나, 활성 플러그인/테마에서 대상을 선택한 뒤 스캔을 시작하세요. CSS 선택자, HTML 구조, JavaScript 정보를 추출하여 표시합니다.', 'acf-css-really-simple-style-management-center' ); ?>
                            <br>
                            <?php _e( '• <strong>수동 재정의</strong>: 해당 리소스의 스타일 구조를 파악하는 데 도움이 되는 기본 정보를 함께 확인하실 수 있습니다. 직접 CSS를 작성하여 특정 스타일을 재정의할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
                            <br>
                            <?php _e( '• <strong>공식 지원 목록</strong>: ACF CSS가 공식적으로 지원하는 테마와 플러그인 목록을 확인할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
                        </span>
                    </p>
                </div>
                <div class="jj-header-actions">
                    <!-- [v4.2.2] 실험실 설정 내보내기/불러오기 -->
                    <button type="button" class="button button-secondary jj-export-settings" data-center="labs-center" style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px; margin-right: 8px;">
                        <span class="dashicons dashicons-download" style="margin-top: 4px;"></span> <?php _e( '내보내기', 'acf-css-really-simple-style-management-center' ); ?>
                    </button>
                    <button type="button" class="button button-secondary jj-import-settings" data-center="labs-center" style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                        <span class="dashicons dashicons-upload" style="margin-top: 4px;"></span> <?php _e( '불러오기', 'acf-css-really-simple-style-management-center' ); ?>
                    </button>
                </div>
            </div>

            <!-- [v5.0.0] 실험실 탭 활성화/비활성화 설정 -->
            <form method="post" action="" style="margin-bottom: 20px;">
                <?php wp_nonce_field( 'jj_labs_tabs_save_action' ); ?>
                <div style="padding: 15px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px;">
                    <h3 style="margin-top: 0;"><?php _e( '실험실 탭 활성화/비활성화', 'acf-css-really-simple-style-management-center' ); ?></h3>
                    <p class="description" style="margin-bottom: 15px;">
                        <?php _e( '실험실의 각 탭을 활성화 또는 비활성화할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
                        <span class="jj-tooltip" style="margin-left: 5px;">
                            <span class="dashicons dashicons-editor-help"></span>
                            <span class="jj-tooltip-content">
                                <?php _e( '비활성화된 탭은 실험실에서 숨겨지지만 설정 데이터는 보존됩니다.', 'acf-css-really-simple-style-management-center' ); ?>
                            </span>
                        </span>
                    </p>
                    
                    <!-- [v5.0.0] 일괄 작업 버튼 -->
                    <div class="jj-bulk-actions" style="margin-bottom: 15px;">
                        <button type="button" class="button button-small jj-bulk-enable-all-labs">
                            <span class="dashicons dashicons-yes-alt" style="font-size: 16px; margin-top: 3px;"></span>
                            <?php _e( '모두 활성화', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <button type="button" class="button button-small jj-bulk-disable-all-labs">
                            <span class="dashicons dashicons-dismiss" style="font-size: 16px; margin-top: 3px;"></span>
                            <?php _e( '모두 비활성화', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                    
                    <!-- [v5.0.0] 검색/필터 -->
                    <div class="jj-section-filter" style="margin-bottom: 15px;">
                        <input type="search" 
                               id="jj-labs-tabs-search" 
                               placeholder="<?php esc_attr_e( '탭 검색...', 'acf-css-really-simple-style-management-center' ); ?>"
                               style="width: 100%; max-width: 400px; padding: 8px 12px; border: 1px solid #8c8f94; border-radius: 3px; font-size: 13px;" />
                    </div>

                    <!-- [v5.0.0] 경고 메시지 -->
                    <div class="notice notice-warning inline jj-labs-tabs-disable-warning" style="display: none; margin: 15px 0;">
                        <p>
                            <strong><?php _e( '주의:', 'acf-css-really-simple-style-management-center' ); ?></strong>
                            <?php _e( '탭을 비활성화하면 실험실에서 해당 탭이 표시되지 않습니다. 비활성화된 탭의 설정 데이터는 보존되지만 UI에서 접근할 수 없게 됩니다.', 'acf-css-really-simple-style-management-center' ); ?>
                        </p>
                    </div>

                    <?php
                    $labs_tabs_layout = $this->get_labs_tabs_layout();
                    $labs_tabs_default = $this->get_default_labs_tabs_layout();
                    ?>
                    <table class="form-table" style="margin: 0;">
                        <thead>
                        <tr>
                            <th style="width: 30px;">
                                <input type="checkbox" 
                                       id="jj-select-all-labs-tabs" 
                                       class="jj-select-all-checkbox" 
                                       title="<?php esc_attr_e( '전체 선택/해제', 'acf-css-really-simple-style-management-center' ); ?>" />
                            </th>
                            <th><?php _e( '탭', 'acf-css-really-simple-style-management-center' ); ?></th>
                            <th><?php _e( '활성화', 'acf-css-really-simple-style-management-center' ); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ( $labs_tabs_default as $tab_slug => $tab_meta ) :
                            $tab_current = isset( $labs_tabs_layout[ $tab_slug ] ) ? $labs_tabs_layout[ $tab_slug ] : $tab_meta;
                            $tab_enabled = ! empty( $tab_current['enabled'] );
                            ?>
                            <tr class="jj-labs-tab-row <?php echo $tab_enabled ? '' : 'jj-tab-row-disabled'; ?>"
                                data-tab-slug="<?php echo esc_attr( $tab_slug ); ?>"
                                data-tab-label="<?php echo esc_attr( strtolower( $tab_meta['label'] ) ); ?>">
                                <td style="width: 30px; text-align: center; padding: 8px 0;">
                                    <input type="checkbox" 
                                           class="jj-labs-tab-checkbox" 
                                           data-tab="<?php echo esc_attr( $tab_slug ); ?>"
                                           <?php checked( $tab_enabled ); ?> />
                                </td>
                                <th scope="row" style="padding: 8px 0; font-weight: normal;">
                                    <?php echo esc_html( $tab_meta['label'] ); ?>
                                    <?php if ( ! $tab_enabled ) : ?>
                                        <span class="jj-disabled-icon" title="<?php esc_attr_e( '비활성화됨', 'acf-css-really-simple-style-management-center' ); ?>"></span>
                                    <?php else : ?>
                                        <span class="jj-enabled-icon" title="<?php esc_attr_e( '활성화됨', 'acf-css-really-simple-style-management-center' ); ?>"></span>
                                    <?php endif; ?>
                                    <div style="font-size:10px;color:#999;margin-top:2px;">
                                        <code><?php echo esc_html( $tab_slug ); ?></code>
                                    </div>
                                </th>
                                <td style="padding: 8px 0;">
                                    <label>
                                        <input type="checkbox"
                                               name="jj_labs_tabs_layout[<?php echo esc_attr( $tab_slug ); ?>][enabled]"
                                               value="1"
                                               class="jj-labs-tab-enabled-checkbox"
                                               data-tab="<?php echo esc_attr( $tab_slug ); ?>"
                                            <?php checked( $tab_enabled ); ?> />
                                        <?php _e( '활성화', 'acf-css-really-simple-style-management-center' ); ?>
                                    </label>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p style="margin-top: 15px; margin-bottom: 0;">
                        <button type="submit" class="button button-primary">
                            <?php _e( '저장', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </p>
                </div>
            </form>

            <div class="jj-labs-center-tabs-container">
                <ul class="jj-labs-center-tabs">
                    <?php 
                    // [v5.0.0] 탭 활성화/비활성화 체크
                    $tab_enabled_scanner = ! empty( $labs_tabs_layout['scanner']['enabled'] ?? true );
                    $tab_enabled_overrides = ! empty( $labs_tabs_layout['overrides']['enabled'] ?? true );
                    $tab_enabled_supported = ! empty( $labs_tabs_layout['supported']['enabled'] ?? true );
                    
                    // 활성화된 탭이 하나도 없으면 스캐너 탭을 기본으로 활성화
                    $has_enabled_tabs = $tab_enabled_scanner || $tab_enabled_overrides || $tab_enabled_supported;
                    if ( ! $has_enabled_tabs ) {
                        $tab_enabled_scanner = true;
                    }
                    
                    $first_enabled_tab = $tab_enabled_scanner ? 'scanner' : ( $tab_enabled_overrides ? 'overrides' : 'supported' );
                    ?>
                    <?php if ( $tab_enabled_scanner ) : ?>
                    <li class="<?php echo ( $first_enabled_tab === 'scanner' ) ? 'active' : ''; ?>" data-tab="scanner">
                        <a href="#scanner"><?php _e( '스캐너', 'acf-css-really-simple-style-management-center' ); ?></a>
                    </li>
                    <?php endif; ?>
                    <?php if ( $tab_enabled_overrides ) : ?>
                    <li class="<?php echo ( $first_enabled_tab === 'overrides' ) ? 'active' : ''; ?>" data-tab="overrides">
                        <a href="#overrides"><?php _e( '수동 재정의', 'acf-css-really-simple-style-management-center' ); ?></a>
                    </li>
                    <?php endif; ?>
                    <?php if ( $tab_enabled_supported ) : ?>
                    <li class="<?php echo ( $first_enabled_tab === 'supported' ) ? 'active' : ''; ?>" data-tab="supported">
                        <a href="#supported"><?php _e( '공식 지원 목록', 'acf-css-really-simple-style-management-center' ); ?></a>
                    </li>
                    <?php endif; ?>
                </ul>

                <!-- 스캐너 탭 -->
                <?php if ( $tab_enabled_scanner ) : ?>
                <div class="jj-labs-center-tab-content <?php echo ( $first_enabled_tab === 'scanner' ) ? 'active' : ''; ?>" data-tab="scanner">
                    <?php
                    $options = $this->options;
                    include JJ_STYLE_GUIDE_PATH . 'includes/editor-views/view-labs-scanner.php';
                    ?>
                </div>
                <?php endif; ?>

                <!-- 수동 재정의 탭 -->
                <?php if ( $tab_enabled_overrides ) : ?>
                <div class="jj-labs-center-tab-content <?php echo ( $first_enabled_tab === 'overrides' ) ? 'active' : ''; ?>" data-tab="overrides">
                    <?php
                    $options = $this->options;
                    include JJ_STYLE_GUIDE_PATH . 'includes/editor-views/view-labs-overrides.php';
                    ?>
                </div>
                <?php endif; ?>

                <!-- 공식 지원 목록 탭 -->
                <?php if ( $tab_enabled_supported ) : ?>
                <div class="jj-labs-center-tab-content <?php echo ( $first_enabled_tab === 'supported' ) ? 'active' : ''; ?>" data-tab="supported">
                    <?php
                    // 실험실 섹션의 공식 지원 목록 부분만 재사용
                    $themes_config_raw = $adapters_config['themes'] ?? array();
                    $spokes_config_raw = $adapters_config['spokes'] ?? array();
                    include JJ_STYLE_GUIDE_PATH . 'includes/editor-views/view-labs-supported-list.php';
                    ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- [v4.2.2] 실험실 설정 내보내기/불러오기 (하단) -->
            <div class="jj-labs-center-footer" style="margin-top: 30px; padding: 15px 25px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <button type="button" class="button button-secondary jj-export-settings" data-center="labs-center" style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px; margin-right: 8px;">
                        <span class="dashicons dashicons-download" style="margin-top: 4px;"></span> <?php _e( '내보내기', 'acf-css-really-simple-style-management-center' ); ?>
                    </button>
                    <button type="button" class="button button-secondary jj-import-settings" data-center="labs-center" style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                        <span class="dashicons dashicons-upload" style="margin-top: 4px;"></span> <?php _e( '불러오기', 'acf-css-really-simple-style-management-center' ); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * [v5.0.0] 실험실 탭 레이아웃 기본값
     *
     * @return array
     */
    public function get_default_labs_tabs_layout() {
        return array(
            'scanner'   => array(
                'label'   => __( '스캐너', 'acf-css-really-simple-style-management-center' ),
                'enabled' => 1,
            ),
            'overrides' => array(
                'label'   => __( '수동 재정의', 'acf-css-really-simple-style-management-center' ),
                'enabled' => 1,
            ),
            'supported' => array(
                'label'   => __( '공식 지원 목록', 'acf-css-really-simple-style-management-center' ),
                'enabled' => 1,
            ),
        );
    }
    
    /**
     * [v5.0.0] 저장된 실험실 탭 레이아웃 + 기본값 병합
     * [v5.0.0] 성능 최적화: static 캐싱 추가
     *
     * @return array
     */
    public function get_labs_tabs_layout() {
        // [v5.0.0] 캐시된 값이 있으면 반환
        if ( self::$labs_tabs_layout_cache !== null ) {
            return self::$labs_tabs_layout_cache;
        }
        
        $stored   = (array) get_option( $this->labs_tabs_option_key );
        $defaults = $this->get_default_labs_tabs_layout();
        $result   = array();

        foreach ( $defaults as $slug => $meta ) {
            $enabled = isset( $stored[ $slug ]['enabled'] ) ? (bool) $stored[ $slug ]['enabled'] : (bool) ( $meta['enabled'] ?? true );
            $result[ $slug ] = array(
                'label'   => $meta['label'],
                'enabled' => $enabled ? 1 : 0,
            );
        }

        // [v5.0.0] 캐시에 저장
        self::$labs_tabs_layout_cache = $result;
        
        return $result;
    }
    
    /**
     * 실험실 탭 레이아웃 캐시 플러시
     * [v5.0.0] 성능 최적화: 옵션이 업데이트되면 호출하여 캐시를 무효화
     *
     * @return void
     */
    public static function flush_labs_tabs_layout_cache() {
        self::$labs_tabs_layout_cache = null;
    }
    
    /**
     * [v5.0.0] 실험실 탭 활성화/비활성화 저장
     */
    public function save_labs_tabs_layout() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        
        // 실험실 페이지에서만 저장
        if ( ! isset( $_GET['page'] ) || 'jj-labs-center' !== $_GET['page'] ) {
            return;
        }
        
        if ( ! isset( $_POST['jj_labs_tabs_layout'] ) || ! isset( $_POST['_wpnonce'] ) ) {
            return;
        }
        
        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'jj_labs_tabs_save_action' ) ) {
            return;
        }
        
        $raw_tabs   = isset( $_POST['jj_labs_tabs_layout'] ) && is_array( $_POST['jj_labs_tabs_layout'] ) ? wp_unslash( $_POST['jj_labs_tabs_layout'] ) : array();
        $clean_tabs = array();
        $defaults   = $this->get_default_labs_tabs_layout();
        
        foreach ( $defaults as $slug => $meta ) {
            $enabled = isset( $raw_tabs[ $slug ]['enabled'] ) && '1' === $raw_tabs[ $slug ]['enabled'];
            $clean_tabs[ $slug ] = array(
                'enabled' => $enabled ? 1 : 0,
            );
        }
        
        update_option( $this->labs_tabs_option_key, $clean_tabs );
        
        // [v5.0.0] 캐시 플러시
        self::flush_labs_tabs_layout_cache();
    }
}

