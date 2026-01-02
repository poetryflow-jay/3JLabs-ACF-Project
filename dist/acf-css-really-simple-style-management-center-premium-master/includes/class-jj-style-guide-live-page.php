<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * JJ Style Guide Live Page
 *
 * 프론트엔드 "Style Guide" 페이지를 실시간 미리보기 + 간단 편집 허브로 제공합니다.
 * - Shortcode: [jj_style_guide_live]
 * - AJAX 저장(관리자만) + 저장 전 자동 백업(best-effort)
 *
 * 참고 UI 아이디어(구조): Colors / Typography / Buttons 등 섹션 구성
 * - https://quantumlabtemplate.webflow.io/template-pages/style-guide
 *
 * @since 10.6.0 (WIP)
 */
final class JJ_Style_Guide_Live_Page {

    private static $instance = null;
    private $shortcode = 'jj_style_guide_live';
    private $sandbox_option_key = 'jj_style_guide_sandbox_page_id';
    private $migrate_option_key = 'jj_style_guide_live_migrated_version';

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public function init() {
        add_shortcode( $this->shortcode, array( $this, 'render_shortcode' ) );

        // Shortcode가 있는 페이지에서만 에셋 로드
        add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue_assets' ) );

        // AJAX (관리자 전용)
        add_action( 'wp_ajax_jj_style_guide_live_get', array( $this, 'ajax_get' ) );
        add_action( 'wp_ajax_jj_style_guide_live_save', array( $this, 'ajax_save' ) );

        // 기존(예문 기반) 스타일 가이드 페이지를 Live 허브로 자동 전환(업데이트 마이그레이션)
        if ( is_admin() ) {
            add_action( 'admin_init', array( $this, 'maybe_migrate_cockpit_page' ) );
        }
    }

    public function maybe_migrate_cockpit_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        if ( ! defined( 'JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY' ) || ! function_exists( 'get_option' ) ) {
            return;
        }

        $ver = defined( 'JJ_STYLE_GUIDE_VERSION' ) ? (string) JJ_STYLE_GUIDE_VERSION : '10.6.0';
        $done = (string) get_option( $this->migrate_option_key, '' );
        if ( $done === $ver ) {
            return;
        }

        $page_id = (int) get_option( JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY, 0 );
        if ( ! $page_id || ! function_exists( 'get_post' ) ) {
            return;
        }
        $page = get_post( $page_id );
        if ( ! $page || 'page' !== $page->post_type ) {
            return;
        }

        $content = (string) $page->post_content;
        if ( false !== strpos( $content, '[' . $this->shortcode . ']' ) ) {
            update_option( $this->migrate_option_key, $ver );
            return;
        }

        // “기존 예문 프리뷰”로 추정되는 경우에만 자동 치환 (사용자 커스텀 보호)
        $looks_legacy = ( false !== strpos( $content, 'jj-preview-h1' ) )
            || ( false !== strpos( $content, 'jj-preview-color' ) )
            || ( false !== strpos( $content, '다람쥐 헌' ) );

        if ( ! $looks_legacy ) {
            return;
        }

        $new_content = "<!-- wp:shortcode -->\n[" . $this->shortcode . "]\n<!-- /wp:shortcode -->";
        wp_update_post(
            array(
                'ID'           => $page_id,
                'post_title'   => __( 'Style Guide', 'acf-css-really-simple-style-management-center' ),
                'post_content' => $new_content,
                'post_status'  => 'private',
            )
        );

        update_option( $this->migrate_option_key, $ver );
    }

    public function maybe_enqueue_assets() {
        if ( is_admin() ) {
            return;
        }

        // Shortcode가 있는 페이지에서만 로드 (성능)
        if ( ! function_exists( 'is_singular' ) || ! is_singular() ) {
            return;
        }
        global $post;
        if ( ! $post || empty( $post->post_content ) || ! function_exists( 'has_shortcode' ) || ! has_shortcode( $post->post_content, $this->shortcode ) ) {
            return;
        }

        $ver = defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '10.6.0';
        $css_path = defined( 'JJ_STYLE_GUIDE_PATH' ) ? JJ_STYLE_GUIDE_PATH . 'assets/css/jj-style-guide-live.css' : '';
        $js_path  = defined( 'JJ_STYLE_GUIDE_PATH' ) ? JJ_STYLE_GUIDE_PATH . 'assets/js/jj-style-guide-live.js' : '';
        if ( $css_path && file_exists( $css_path ) ) {
            $ver = filemtime( $css_path );
        }

        wp_enqueue_style(
            'jj-style-guide-live',
            JJ_STYLE_GUIDE_URL . 'assets/css/jj-style-guide-live.css',
            array(),
            $ver
        );

        $js_ver = $ver;
        if ( $js_path && file_exists( $js_path ) ) {
            $js_ver = filemtime( $js_path );
        }

        wp_enqueue_script(
            'jj-style-guide-live',
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-style-guide-live.js',
            array( 'jquery' ),
            $js_ver,
            true
        );

        wp_localize_script(
            'jj-style-guide-live',
            'jjStyleGuideLive',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'jj_style_guide_live_action' ),
                'exporter_nonce' => wp_create_nonce( 'jj_style_guide_exporter_action' ),
                'can_edit' => current_user_can( 'manage_options' ),
                'data'     => $this->get_live_payload(),
                'i18n'     => array(
                    'saving'        => __( '저장 중...', 'acf-css-really-simple-style-management-center' ),
                    'saved'         => __( '저장되었습니다.', 'acf-css-really-simple-style-management-center' ),
                    'reloaded'      => __( '설정값을 다시 불러왔습니다.', 'acf-css-really-simple-style-management-center' ),
                    'save_failed'   => __( '저장에 실패했습니다.', 'acf-css-really-simple-style-management-center' ),
                    'no_permission' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ),
                    'exporting'     => __( '내보내는 중...', 'acf-css-really-simple-style-management-center' ),
                    'export_done'   => __( '내보내기 완료!', 'acf-css-really-simple-style-management-center' ),
                    'export_failed' => __( '내보내기 실패', 'acf-css-really-simple-style-management-center' ),
                ),
            )
        );
    }

    private function get_live_payload() {
        $key = defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ? JJ_STYLE_GUIDE_OPTIONS_KEY : 'jj_style_guide_options';
        $opt = function_exists( 'get_option' ) ? (array) get_option( $key, array() ) : array();

        $brand = isset( $opt['palettes']['brand'] ) && is_array( $opt['palettes']['brand'] ) ? $opt['palettes']['brand'] : array();
        $system = isset( $opt['palettes']['system'] ) && is_array( $opt['palettes']['system'] ) ? $opt['palettes']['system'] : array();
        $typo = isset( $opt['typography'] ) && is_array( $opt['typography'] ) ? $opt['typography'] : array();
        $typo_settings = isset( $opt['typography_settings'] ) && is_array( $opt['typography_settings'] ) ? $opt['typography_settings'] : array();

        $unit = isset( $typo_settings['unit'] ) ? (string) $typo_settings['unit'] : 'px';
        if ( ! in_array( $unit, array( 'px', 'rem', 'em' ), true ) ) {
            $unit = 'px';
        }
        $base_px = isset( $typo_settings['base_px'] ) && is_numeric( $typo_settings['base_px'] ) ? (string) (int) $typo_settings['base_px'] : '16';

        $out = array(
            'colors' => array(
                'primary'   => $this->sanitize_hex( $brand['primary_color'] ?? '#2271b1', '#2271b1' ),
                'secondary' => $this->sanitize_hex( $brand['secondary_color'] ?? '#50575e', '#50575e' ),
                'accent'    => $this->sanitize_hex( $brand['primary_color_hover'] ?? '', '' ),
                'bg'        => $this->sanitize_hex( $system['site_bg'] ?? '#0b0c10', '#0b0c10' ),
                'text'      => $this->sanitize_hex( $system['text_color'] ?? '#ffffff', '#ffffff' ),
            ),
            'typography_settings' => array(
                'unit'    => $unit,
                'base_px' => $base_px,
            ),
            'typography' => array(
                // 플러그인 옵션 포맷에 맞춰 “숫자 문자열”을 유지합니다.
                // (단위는 CSS 생성 엔진(JJ_Strategy_1_CSS_Vars)에서 px/em을 붙입니다.)
                'h1' => $this->typo_slice( $typo['h1'] ?? array(), '40', '1.3', '-0.5' ),
                'h2' => $this->typo_slice( $typo['h2'] ?? array(), '32', '1.4', '-0.3' ),
                'h3' => $this->typo_slice( $typo['h3'] ?? array(), '26', '1.5', '0' ),
                'p'  => $this->typo_slice( $typo['p'] ?? array(), '16', '1.7', '0' ),
            ),
            'devices' => array(
                array( 'key' => 'desktop',     'label' => __( 'Desktop', 'acf-css-really-simple-style-management-center' ) ),
                array( 'key' => 'laptop',      'label' => __( 'Laptop', 'acf-css-really-simple-style-management-center' ) ),
                array( 'key' => 'tablet',      'label' => __( 'Tablet', 'acf-css-really-simple-style-management-center' ) ),
                array( 'key' => 'phablet',     'label' => __( 'Phablet', 'acf-css-really-simple-style-management-center' ) ),
                array( 'key' => 'mobile',      'label' => __( 'Mobile', 'acf-css-really-simple-style-management-center' ) ),
                array( 'key' => 'phone_small', 'label' => __( 'Small Mobile', 'acf-css-really-simple-style-management-center' ) ),
                array( 'key' => 'desktop_qhd', 'label' => __( 'Desktop (QHD+)', 'acf-css-really-simple-style-management-center' ) ),
                array( 'key' => 'desktop_uhd', 'label' => __( 'Desktop (UHD/4K+)', 'acf-css-really-simple-style-management-center' ) ),
                array( 'key' => 'desktop_5k',  'label' => __( 'Desktop (5K+)', 'acf-css-really-simple-style-management-center' ) ),
                array( 'key' => 'desktop_8k',  'label' => __( 'Desktop (8K+)', 'acf-css-really-simple-style-management-center' ) ),
            ),
        );

        if ( ! $out['colors']['accent'] ) {
            $out['colors']['accent'] = $out['colors']['primary'];
        }

        return $out;
    }

    private function get_sandbox_page_id() {
        // Duplicator가 로드되어 있으면 그 로직을 우선 사용
        if ( class_exists( 'JJ_Style_Guide_Page_Duplicator' ) && method_exists( 'JJ_Style_Guide_Page_Duplicator', 'instance' ) ) {
            try {
                $dup = JJ_Style_Guide_Page_Duplicator::instance();
                if ( $dup && method_exists( $dup, 'get_sandbox_page_id' ) ) {
                    return (int) $dup->get_sandbox_page_id();
                }
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }

        $id = (int) ( function_exists( 'get_option' ) ? get_option( $this->sandbox_option_key, 0 ) : 0 );
        if ( $id && function_exists( 'get_post' ) && get_post( $id ) && 'page' === get_post_type( $id ) && 'trash' !== get_post_status( $id ) ) {
            return $id;
        }
        return 0;
    }

    private function typo_slice( $t, $size_fallback = '40', $lh_fallback = '1.3', $ls_fallback = '-0.5' ) {
        $t = is_array( $t ) ? $t : array();
        $font_family = isset( $t['font_family'] ) ? (string) $t['font_family'] : '';
        $font_weight = isset( $t['font_weight'] ) ? (string) $t['font_weight'] : '';

        $size = $size_fallback;
        if ( isset( $t['font_size'] ) ) {
            if ( is_array( $t['font_size'] ) && isset( $t['font_size']['desktop'] ) ) {
                $size = (string) $t['font_size']['desktop'];
            } elseif ( is_string( $t['font_size'] ) || is_numeric( $t['font_size'] ) ) {
                $size = (string) $t['font_size'];
            }
        }

        $lh = isset( $t['line_height'] ) ? (string) $t['line_height'] : $lh_fallback;
        $ls = isset( $t['letter_spacing'] ) ? (string) $t['letter_spacing'] : $ls_fallback;

        // 디바이스별 폰트 사이즈(숫자 문자열) — Live Page 편집/미리보기용
        $font_sizes = array();
        $raw_sizes = isset( $t['font_size'] ) && is_array( $t['font_size'] ) ? $t['font_size'] : array();
        foreach ( array( 'desktop', 'laptop', 'tablet', 'phablet', 'mobile', 'phone_small', 'desktop_qhd', 'desktop_uhd', 'desktop_5k', 'desktop_8k' ) as $dev ) {
            $val = isset( $raw_sizes[ $dev ] ) ? (string) $raw_sizes[ $dev ] : '';
            $font_sizes[ $dev ] = $val;
        }
        if ( '' === ( $font_sizes['desktop'] ?? '' ) ) {
            $font_sizes['desktop'] = (string) $size;
        }

        return array(
            'font_family'   => $this->sanitize_font_family( $font_family ),
            'font_weight'   => preg_replace( '/[^0-9a-zA-Z]/', '', $font_weight ),
            // 숫자 문자열만 허용 (예: 40 / 1.3 / -0.5)
            // (하위 호환) 단일 font_size는 desktop 기준으로 유지
            'font_size'     => $this->sanitize_number_like( $size, $size_fallback ),
            'font_sizes'    => $font_sizes,
            'line_height'   => $this->sanitize_number_like( $lh, $lh_fallback ),
            'letter_spacing'=> $this->sanitize_number_like( $ls, $ls_fallback ),
        );
    }

    private function sanitize_hex( $value, $fallback ) {
        $value = (string) $value;
        if ( function_exists( 'sanitize_hex_color' ) ) {
            $hex = sanitize_hex_color( $value );
            if ( $hex ) {
                return $hex;
            }
        }
        return $fallback;
    }

    private function sanitize_font_family( $value ) {
        $value = trim( (string) $value );
        if ( '' === $value ) {
            return '';
        }
        $value = preg_replace( '/[^0-9a-zA-Z\\s\\,\\-\\_\\\"\\\']/', '', $value );
        return trim( $value );
    }

    private function sanitize_number_like( $value, $fallback ) {
        $value = trim( (string) $value );
        if ( '' === $value ) {
            return $fallback;
        }
        // 허용: -12, 12, 12.3, -0.5
        if ( preg_match( '/^-?\\d+(\\.\\d+)?$/', $value ) ) {
            return $value;
        }
        return $fallback;
    }

    public function render_shortcode() {
        ob_start();
        $sandbox_id = $this->get_sandbox_page_id();
        ?>
        <div id="jj-style-guide-live" class="jj-sg-live">
            <div class="jj-sg-hero">
                <div class="jj-sg-badge">Styleguide</div>
                <h1 class="jj-sg-title">Style Guide</h1>
                <p class="jj-sg-subtitle">
                    <?php esc_html_e( '이 페이지는 JJ 토큰(컬러/타이포/버튼)을 한눈에 확인하고, 즉시 미리보기하며, (관리자라면) 바로 저장까지 할 수 있는 허브입니다.', 'acf-css-really-simple-style-management-center' ); ?>
                </p>
                <p class="jj-sg-hint">
                    <?php esc_html_e( 'QuantumLab 같은 스타일 가이드 구조(Colors/Typography/Buttons)를 참고해 빠르게 읽히는 “사용 가이드” 중심으로 구성했습니다.', 'acf-css-really-simple-style-management-center' ); ?>
                    <a href="https://quantumlabtemplate.webflow.io/template-pages/style-guide" target="_blank" rel="noopener noreferrer"><?php esc_html_e( '참고 링크', 'acf-css-really-simple-style-management-center' ); ?></a>
                </p>
            </div>

            <div class="jj-sg-grid">
                <div class="jj-sg-left">
                    <div class="jj-sg-section">
                        <div class="jj-sg-section-head">
                            <h2><?php esc_html_e( 'Typography Base & Units', 'acf-css-really-simple-style-management-center' ); ?></h2>
                            <p><?php esc_html_e( '폰트 크기는 px 숫자로 입력하고, 출력 단위(px/rem/em)와 기준 px(1rem/1em)을 설정합니다. 이 값은 Customizer/설정/프런트엔드 모두에 동일하게 적용됩니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
                        </div>
                        <div class="jj-sg-typo-settings">
                            <label>
                                <span><?php esc_html_e( 'Base px (1rem/1em)', 'acf-css-really-simple-style-management-center' ); ?></span>
                                <input type="number" id="jj-sg-typo-base-px" min="1" step="1" />
                            </label>
                            <label>
                                <span><?php esc_html_e( 'Output Unit', 'acf-css-really-simple-style-management-center' ); ?></span>
                                <select id="jj-sg-typo-unit">
                                    <option value="px"><?php esc_html_e( 'px', 'acf-css-really-simple-style-management-center' ); ?></option>
                                    <option value="rem"><?php esc_html_e( 'rem (권장)', 'acf-css-really-simple-style-management-center' ); ?></option>
                                    <option value="em"><?php esc_html_e( 'em', 'acf-css-really-simple-style-management-center' ); ?></option>
                                </select>
                            </label>
                            <label>
                                <span><?php esc_html_e( 'Edit device', 'acf-css-really-simple-style-management-center' ); ?></span>
                                <select id="jj-sg-live-device"></select>
                            </label>
                        </div>

                        <div class="jj-sg-presets">
                            <div class="jj-sg-presets-head">
                                <h3><?php esc_html_e( 'Typography Presets (1-click)', 'acf-css-really-simple-style-management-center' ); ?></h3>
                                <p><?php esc_html_e( 'SaaS/콘텐츠/미니멀 등 프리셋을 한 번 클릭해 전체 스케일(최대 8K)을 빠르게 세팅할 수 있습니다. 적용 후 “저장”을 눌러 반영하세요.', 'acf-css-really-simple-style-management-center' ); ?></p>
                            </div>
                            <div class="jj-sg-presets-toolbar">
                                <input type="text" id="jj-sg-typo-preset-search" placeholder="<?php esc_attr_e( '프리셋 검색 (예: SaaS, Editorial, 8K)', 'acf-css-really-simple-style-management-center' ); ?>" />
                                <button type="button" class="jj-sg-btn jj-sg-btn-secondary" id="jj-sg-typo-preset-undo" disabled>
                                    <?php esc_html_e( '되돌리기', 'acf-css-really-simple-style-management-center' ); ?>
                                </button>
                            </div>
                            <div id="jj-sg-typo-presets"></div>
                        </div>
                    </div>
                    <div class="jj-sg-section">
                        <div class="jj-sg-section-head">
                            <h2><?php esc_html_e( 'Colors', 'acf-css-really-simple-style-management-center' ); ?></h2>
                            <p><?php esc_html_e( 'Primary는 핵심 CTA, Secondary는 보조, Text/BG는 가독성(대비)을 위해 설정하세요.', 'acf-css-really-simple-style-management-center' ); ?></p>
                        </div>

                        <div class="jj-sg-color-cards">
                            <div class="jj-sg-color-card" data-color-key="primary">
                                <div class="jj-sg-swatch"></div>
                                <div class="jj-sg-color-meta">
                                    <div class="jj-sg-color-name">Primary</div>
                                    <div class="jj-sg-color-value"></div>
                                </div>
                                <div class="jj-sg-color-editor"></div>
                            </div>
                            <div class="jj-sg-color-card" data-color-key="secondary">
                                <div class="jj-sg-swatch"></div>
                                <div class="jj-sg-color-meta">
                                    <div class="jj-sg-color-name">Secondary</div>
                                    <div class="jj-sg-color-value"></div>
                                </div>
                                <div class="jj-sg-color-editor"></div>
                            </div>
                            <div class="jj-sg-color-card" data-color-key="accent">
                                <div class="jj-sg-swatch"></div>
                                <div class="jj-sg-color-meta">
                                    <div class="jj-sg-color-name">Accent</div>
                                    <div class="jj-sg-color-value"></div>
                                </div>
                                <div class="jj-sg-color-editor"></div>
                            </div>
                            <div class="jj-sg-color-card" data-color-key="text">
                                <div class="jj-sg-swatch"></div>
                                <div class="jj-sg-color-meta">
                                    <div class="jj-sg-color-name">Text</div>
                                    <div class="jj-sg-color-value"></div>
                                </div>
                                <div class="jj-sg-color-editor"></div>
                            </div>
                            <div class="jj-sg-color-card" data-color-key="bg">
                                <div class="jj-sg-swatch"></div>
                                <div class="jj-sg-color-meta">
                                    <div class="jj-sg-color-name">Background</div>
                                    <div class="jj-sg-color-value"></div>
                                </div>
                                <div class="jj-sg-color-editor"></div>
                            </div>
                        </div>
                    </div>

                    <div class="jj-sg-section">
                        <div class="jj-sg-section-head">
                            <h2><?php esc_html_e( 'Typography', 'acf-css-really-simple-style-management-center' ); ?></h2>
                            <p><?php esc_html_e( '헤딩은 “메시지 전달”, 본문은 “읽기 쉬움”이 목적입니다. 폰트 크기/행간/자간을 조정하며 가독성을 확인하세요.', 'acf-css-really-simple-style-management-center' ); ?></p>
                        </div>

                        <div class="jj-sg-typo-table" data-typo-key="h1"></div>
                        <div class="jj-sg-typo-table" data-typo-key="h2"></div>
                        <div class="jj-sg-typo-table" data-typo-key="h3"></div>
                        <div class="jj-sg-typo-table" data-typo-key="p"></div>
                    </div>

                    <div class="jj-sg-actions">
                        <div class="jj-sg-actions-left">
                            <button type="button" class="jj-sg-btn jj-sg-btn-secondary" id="jj-sg-live-preview">
                                <?php esc_html_e( '미리보기(저장 없음)', 'acf-css-really-simple-style-management-center' ); ?>
                            </button>
                            <button type="button" class="jj-sg-btn jj-sg-btn-secondary" id="jj-sg-live-reload">
                                <?php esc_html_e( '설정값 다시 불러오기', 'acf-css-really-simple-style-management-center' ); ?>
                            </button>
                            <button type="button" class="jj-sg-btn jj-sg-btn-primary" id="jj-sg-live-save">
                                <?php esc_html_e( '저장', 'acf-css-really-simple-style-management-center' ); ?>
                            </button>
                        </div>
                        <div class="jj-sg-actions-right">
                            <div class="jj-sg-export-dropdown">
                                <button type="button" class="jj-sg-btn jj-sg-btn-secondary jj-sg-export-toggle" id="jj-sg-export-toggle">
                                    <span class="dashicons dashicons-download" style="font-size:16px;width:16px;height:16px;margin-right:4px;"></span>
                                    <?php esc_html_e( '내보내기', 'acf-css-really-simple-style-management-center' ); ?>
                                    <span class="dashicons dashicons-arrow-down-alt2" style="font-size:14px;width:14px;height:14px;margin-left:2px;"></span>
                                </button>
                                <div class="jj-sg-export-menu" id="jj-sg-export-menu" style="display:none;">
                                    <button type="button" class="jj-sg-export-item" data-format="pdf">
                                        <span class="dashicons dashicons-media-document"></span>
                                        <?php esc_html_e( 'PDF (인쇄)', 'acf-css-really-simple-style-management-center' ); ?>
                                    </button>
                                    <button type="button" class="jj-sg-export-item" data-format="png">
                                        <span class="dashicons dashicons-format-image"></span>
                                        <?php esc_html_e( 'PNG 이미지', 'acf-css-really-simple-style-management-center' ); ?>
                                    </button>
                                    <button type="button" class="jj-sg-export-item" data-format="html">
                                        <span class="dashicons dashicons-editor-code"></span>
                                        <?php esc_html_e( 'HTML 파일', 'acf-css-really-simple-style-management-center' ); ?>
                                    </button>
                                    <button type="button" class="jj-sg-export-item" data-format="css">
                                        <span class="dashicons dashicons-admin-customizer"></span>
                                        <?php esc_html_e( 'CSS 변수', 'acf-css-really-simple-style-management-center' ); ?>
                                    </button>
                                    <button type="button" class="jj-sg-export-item" data-format="json">
                                        <span class="dashicons dashicons-database"></span>
                                        <?php esc_html_e( 'JSON 토큰', 'acf-css-really-simple-style-management-center' ); ?>
                                    </button>
                                    <button type="button" class="jj-sg-export-item" data-format="zip">
                                        <span class="dashicons dashicons-archive"></span>
                                        <?php esc_html_e( 'ZIP (전체)', 'acf-css-really-simple-style-management-center' ); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="jj-sg-status" id="jj-sg-live-status" aria-live="polite"></div>
                    </div>
                </div>

                <div class="jj-sg-right">
                    <div class="jj-sg-preview">
                        <div class="jj-sg-preview-badge"><?php esc_html_e( 'Live Preview', 'acf-css-really-simple-style-management-center' ); ?></div>
                        <h2 class="jj-sg-preview-title"><?php esc_html_e( '브랜드의 핵심 가치를 한 문장으로 말하세요', 'acf-css-really-simple-style-management-center' ); ?></h2>
                        <p class="jj-sg-preview-text">
                            <?php esc_html_e( '이 본문은 가독성 확인용입니다. 문장의 길이를 조금 길게 두어 행간과 자간의 체감, 색 대비, 버튼 강조가 잘 되는지 확인하세요.', 'acf-css-really-simple-style-management-center' ); ?>
                        </p>
                        <div class="jj-sg-preview-buttons">
                            <a href="#" class="jj-sg-cta jj-sg-cta-primary" onclick="return false;"><?php esc_html_e( 'Get Started Now', 'acf-css-really-simple-style-management-center' ); ?></a>
                            <a href="#" class="jj-sg-cta jj-sg-cta-secondary" onclick="return false;"><?php esc_html_e( 'See Pricing', 'acf-css-really-simple-style-management-center' ); ?></a>
                        </div>
                        <div class="jj-sg-preview-cards">
                            <div class="jj-sg-preview-card">
                                <div class="jj-sg-preview-card-title"><?php esc_html_e( 'CTA 우선순위', 'acf-css-really-simple-style-management-center' ); ?></div>
                                <div class="jj-sg-preview-card-text"><?php esc_html_e( 'Primary는 1개만, Secondary는 보조 행동에 사용하세요.', 'acf-css-really-simple-style-management-center' ); ?></div>
                            </div>
                            <div class="jj-sg-preview-card">
                                <div class="jj-sg-preview-card-title"><?php esc_html_e( '콘트라스트', 'acf-css-really-simple-style-management-center' ); ?></div>
                                <div class="jj-sg-preview-card-text"><?php esc_html_e( 'Text/BG 대비가 낮으면 본문이 흐릿해집니다. 4.5:1 이상을 권장합니다.', 'acf-css-really-simple-style-management-center' ); ?></div>
                            </div>
                            <div class="jj-sg-preview-card">
                                <div class="jj-sg-preview-card-title"><?php esc_html_e( '타이포 위계', 'acf-css-really-simple-style-management-center' ); ?></div>
                                <div class="jj-sg-preview-card-text"><?php esc_html_e( 'H1→H2→H3→본문 순으로 크기/행간이 자연스럽게 줄어드는지 확인하세요.', 'acf-css-really-simple-style-management-center' ); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="jj-sg-note">
                        <?php esc_html_e( '팁: 이 페이지는 “무의미한 예문” 대신 실제 사용법 문장으로 구성되어, 설정을 바꾸면 즉시 실사용 화면처럼 보이도록 설계합니다.', 'acf-css-really-simple-style-management-center' ); ?>
                    </div>
                </div>
            </div>

            <div class="jj-sg-section jj-sg-sandbox">
                <div class="jj-sg-section-head">
                    <h2><?php esc_html_e( 'Example Page (Sandbox)', 'acf-css-really-simple-style-management-center' ); ?></h2>
                    <p>
                        <?php esc_html_e( 'Pages 목록에서 “Duplicate to Style Guide by ACF CSS”를 실행하면, 선택한 페이지가 이 영역에 복제되어 실제 화면 기준으로 토큰 적용을 확인할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
                    </p>
                </div>

                <?php if ( $sandbox_id ) : ?>
                    <div class="jj-sg-sandbox-actions">
                        <a class="jj-sg-btn jj-sg-btn-secondary" href="<?php echo esc_url( get_permalink( $sandbox_id ) ); ?>" target="_blank" rel="noopener noreferrer">
                            <?php esc_html_e( '샌드박스 새 탭 열기', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                    </div>
                    <div class="jj-sg-sandbox-frame-wrap">
                        <iframe id="jj-sg-sandbox-frame" title="<?php esc_attr_e( 'Style Guide Sandbox', 'acf-css-really-simple-style-management-center' ); ?>" src="<?php echo esc_url( get_permalink( $sandbox_id ) ); ?>" loading="lazy"></iframe>
                    </div>
                <?php else : ?>
                    <div class="jj-sg-sandbox-empty">
                        <p><?php esc_html_e( '아직 복제된 샌드박스 페이지가 없습니다. Pages 목록에서 원하는 페이지를 선택해 복제해 주세요.', 'acf-css-really-simple-style-management-center' ); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function ajax_get() {
        check_ajax_referer( 'jj_style_guide_live_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 403 );
        }
        wp_send_json_success( array( 'data' => $this->get_live_payload() ) );
    }

    public function ajax_save() {
        check_ajax_referer( 'jj_style_guide_live_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 403 );
        }

        $payload = isset( $_POST['payload'] ) ? (array) wp_unslash( $_POST['payload'] ) : array();

        $key = defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ? JJ_STYLE_GUIDE_OPTIONS_KEY : 'jj_style_guide_options';
        $opt = function_exists( 'get_option' ) ? (array) get_option( $key, array() ) : array();

        // Best-effort 자동 백업
        if ( class_exists( 'JJ_Backup_Manager' ) && method_exists( 'JJ_Backup_Manager', 'instance' ) ) {
            try {
                $bm = JJ_Backup_Manager::instance();
                if ( $bm && method_exists( $bm, 'create_backup' ) ) {
                    $bm->create_backup( 'auto', __( 'Style Guide Live 편집 전 자동 백업', 'acf-css-really-simple-style-management-center' ) );
                }
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }

        // Colors
        $colors = isset( $payload['colors'] ) && is_array( $payload['colors'] ) ? $payload['colors'] : array();
        $brand =& $opt['palettes']['brand'];
        $system =& $opt['palettes']['system'];
        if ( ! is_array( $brand ) ) $brand = array();
        if ( ! is_array( $system ) ) $system = array();

        if ( isset( $colors['primary'] ) )   $brand['primary_color'] = $this->sanitize_hex( $colors['primary'], $brand['primary_color'] ?? '#2271b1' );
        if ( isset( $colors['secondary'] ) ) $brand['secondary_color'] = $this->sanitize_hex( $colors['secondary'], $brand['secondary_color'] ?? '#50575e' );
        if ( isset( $colors['accent'] ) )    $brand['primary_color_hover'] = $this->sanitize_hex( $colors['accent'], $brand['primary_color_hover'] ?? ( $brand['primary_color'] ?? '#2271b1' ) );
        if ( isset( $colors['bg'] ) )        $system['site_bg'] = $this->sanitize_hex( $colors['bg'], $system['site_bg'] ?? '#0b0c10' );
        if ( isset( $colors['text'] ) )      $system['text_color'] = $this->sanitize_hex( $colors['text'], $system['text_color'] ?? '#ffffff' );

        // Typography
        $typo_in = isset( $payload['typography'] ) && is_array( $payload['typography'] ) ? $payload['typography'] : array();
        $active_device = isset( $payload['active_device'] ) ? sanitize_key( (string) $payload['active_device'] ) : 'desktop';
        $allowed_devices = array( 'desktop', 'laptop', 'tablet', 'phablet', 'mobile', 'phone_small', 'desktop_qhd', 'desktop_uhd', 'desktop_5k', 'desktop_8k' );
        if ( ! in_array( $active_device, $allowed_devices, true ) ) {
            $active_device = 'desktop';
        }

        // Typography settings (unit/base) 저장
        $ts_in = isset( $payload['typography_settings'] ) && is_array( $payload['typography_settings'] ) ? $payload['typography_settings'] : array();
        if ( ! isset( $opt['typography_settings'] ) || ! is_array( $opt['typography_settings'] ) ) {
            $opt['typography_settings'] = array();
        }
        if ( isset( $ts_in['unit'] ) ) {
            $u = sanitize_key( (string) $ts_in['unit'] );
            if ( in_array( $u, array( 'px', 'rem', 'em' ), true ) ) {
                $opt['typography_settings']['unit'] = $u;
            }
        }
        if ( isset( $ts_in['base_px'] ) && is_numeric( $ts_in['base_px'] ) ) {
            $b = (int) $ts_in['base_px'];
            if ( $b > 0 ) {
                $opt['typography_settings']['base_px'] = (string) $b;
            }
        }

        if ( ! isset( $opt['typography'] ) || ! is_array( $opt['typography'] ) ) {
            $opt['typography'] = array();
        }
        foreach ( array( 'h1', 'h2', 'h3', 'p' ) as $k ) {
            if ( ! isset( $typo_in[ $k ] ) || ! is_array( $typo_in[ $k ] ) ) {
                continue;
            }
            if ( ! isset( $opt['typography'][ $k ] ) || ! is_array( $opt['typography'][ $k ] ) ) {
                $opt['typography'][ $k ] = array();
            }
            $fallbacks = $this->typo_slice( $opt['typography'][ $k ] ?? array() );

            $opt['typography'][ $k ]['font_family'] = $this->sanitize_font_family( $typo_in[ $k ]['font_family'] ?? '' );
            $opt['typography'][ $k ]['font_weight'] = preg_replace( '/[^0-9a-zA-Z]/', '', (string) ( $typo_in[ $k ]['font_weight'] ?? '' ) );

            // font_size: desktop 값만 안전하게 업데이트(태블릿/모바일은 유지)
            if ( ! isset( $opt['typography'][ $k ]['font_size'] ) || ! is_array( $opt['typography'][ $k ]['font_size'] ) ) {
                $opt['typography'][ $k ]['font_size'] = array();
            }
            // Live payload가 제공하는 font_sizes가 있으면 전체 디바이스를 한 번에 저장(프리셋/일괄 세팅용)
            $font_sizes_in = isset( $typo_in[ $k ]['font_sizes'] ) && is_array( $typo_in[ $k ]['font_sizes'] ) ? $typo_in[ $k ]['font_sizes'] : null;
            if ( is_array( $font_sizes_in ) ) {
                foreach ( $allowed_devices as $dev ) {
                    if ( ! array_key_exists( $dev, $font_sizes_in ) ) {
                        continue;
                    }
                    $raw = trim( (string) $font_sizes_in[ $dev ] );
                    if ( '' === $raw ) {
                        // 비우기 허용(폴백 체인으로 내려가도록)
                        $opt['typography'][ $k ]['font_size'][ $dev ] = '';
                        continue;
                    }
                    $opt['typography'][ $k ]['font_size'][ $dev ] = $this->sanitize_number_like(
                        $raw,
                        ( isset( $opt['typography'][ $k ]['font_size'][ $dev ] ) ? (string) $opt['typography'][ $k ]['font_size'][ $dev ] : $fallbacks['font_size'] )
                    );
                }
            } else {
                // 기본: 현재 선택 디바이스만 저장
                $opt['typography'][ $k ]['font_size'][ $active_device ] = $this->sanitize_number_like(
                    $typo_in[ $k ]['font_size'] ?? '',
                    $fallbacks['font_size']
                );
            }

            $opt['typography'][ $k ]['line_height'] = $this->sanitize_number_like(
                $typo_in[ $k ]['line_height'] ?? '',
                $fallbacks['line_height']
            );
            $opt['typography'][ $k ]['letter_spacing'] = $this->sanitize_number_like(
                $typo_in[ $k ]['letter_spacing'] ?? '',
                $fallbacks['letter_spacing']
            );
        }

        if ( function_exists( 'update_option' ) ) {
            update_option( $key, $opt );
        }

        // 캐시 플러시(best-effort)
        if ( class_exists( 'JJ_CSS_Cache' ) ) {
            try {
                JJ_CSS_Cache::instance()->flush();
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }

        wp_send_json_success( array(
            'message' => __( '저장되었습니다.', 'acf-css-really-simple-style-management-center' ),
            'data'    => $this->get_live_payload(),
        ) );
    }
}

