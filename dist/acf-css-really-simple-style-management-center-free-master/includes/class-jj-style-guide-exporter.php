<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 11] Style Guide Exporter
 * 
 * 스타일 가이드 및 페이지를 다양한 형식으로 내보내기
 * - PDF (브라우저 print API)
 * - 이미지 PNG/JPEG (html2canvas)
 * - HTML ZIP (스타일 포함)
 * - CSS 변수 파일
 * - JSON 디자인 토큰
 * 
 * @since 11.0.0
 */
class JJ_Style_Guide_Exporter {

    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public function init() {
        // AJAX 핸들러
        add_action( 'wp_ajax_jj_export_style_guide_html', array( $this, 'ajax_export_html' ) );
        add_action( 'wp_ajax_jj_export_style_guide_css', array( $this, 'ajax_export_css' ) );
        add_action( 'wp_ajax_jj_export_style_guide_json', array( $this, 'ajax_export_json' ) );
        add_action( 'wp_ajax_jj_export_style_guide_zip', array( $this, 'ajax_export_zip' ) );
    }

    /**
     * 스타일 토큰 수집
     */
    private function get_style_tokens() {
        $key = defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ? JJ_STYLE_GUIDE_OPTIONS_KEY : 'jj_style_guide_options';
        $options = (array) get_option( $key, array() );

        $tokens = array(
            'colors' => array(),
            'typography' => array(),
            'buttons' => array(),
            'forms' => array(),
            'meta' => array(
                'plugin_version' => defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : 'unknown',
                'site_url' => home_url(),
                'site_name' => get_bloginfo( 'name' ),
                'exported_at' => current_time( 'c' ),
            ),
        );

        // Colors
        if ( isset( $options['palettes'] ) ) {
            $brand = $options['palettes']['brand'] ?? array();
            $system = $options['palettes']['system'] ?? array();
            
            $tokens['colors'] = array(
                'primary' => $brand['primary_color'] ?? '#2271b1',
                'primary_hover' => $brand['primary_color_hover'] ?? '#135e96',
                'secondary' => $brand['secondary_color'] ?? '#50575e',
                'secondary_hover' => $brand['secondary_color_hover'] ?? '#3c434a',
                'accent' => $brand['accent_color'] ?? '#d63638',
                'accent_hover' => $brand['accent_color_hover'] ?? '#b32d2e',
                'background' => $system['site_bg'] ?? '#ffffff',
                'content_bg' => $system['content_bg'] ?? '#ffffff',
                'text' => $system['text_color'] ?? '#1d2327',
                'text_secondary' => $system['secondary_text_color'] ?? '#50575e',
                'border' => $system['border_color'] ?? '#c3c4c7',
                'success' => $system['success_color'] ?? '#00a32a',
                'warning' => $system['warning_color'] ?? '#dba617',
                'error' => $system['error_color'] ?? '#d63638',
                'info' => $system['info_color'] ?? '#72aee6',
            );
        }

        // Typography
        if ( isset( $options['typography'] ) ) {
            foreach ( $options['typography'] as $tag => $props ) {
                $tokens['typography'][ $tag ] = array(
                    'font_family' => $props['font_family'] ?? 'inherit',
                    'font_weight' => $props['font_weight'] ?? '400',
                    'font_style' => $props['font_style'] ?? 'normal',
                    'font_size' => $props['font_size'] ?? array(),
                    'line_height' => $props['line_height'] ?? '1.5',
                    'letter_spacing' => $props['letter_spacing'] ?? '0',
                    'text_transform' => $props['text_transform'] ?? 'none',
                );
            }
        }

        // Typography settings
        $tokens['typography_settings'] = array(
            'base_px' => $options['typography_settings']['base_px'] ?? '16',
            'unit' => $options['typography_settings']['unit'] ?? 'px',
        );

        // Buttons
        if ( isset( $options['buttons'] ) ) {
            $tokens['buttons'] = $options['buttons'];
        }

        // Forms
        if ( isset( $options['forms'] ) ) {
            $tokens['forms'] = $options['forms'];
        }

        return $tokens;
    }

    /**
     * CSS 변수 문자열 생성
     */
    public function generate_css_variables() {
        $tokens = $this->get_style_tokens();
        $css = "/**\n * JJ Style Guide - Design Tokens (CSS Variables)\n";
        $css .= " * Generated: " . $tokens['meta']['exported_at'] . "\n";
        $css .= " * Site: " . $tokens['meta']['site_name'] . " (" . $tokens['meta']['site_url'] . ")\n";
        $css .= " * Plugin Version: " . $tokens['meta']['plugin_version'] . "\n";
        $css .= " */\n\n:root {\n";

        // Colors
        $css .= "  /* ===== Colors ===== */\n";
        foreach ( $tokens['colors'] as $key => $value ) {
            $css_key = str_replace( '_', '-', $key );
            $css .= "  --jj-color-{$css_key}: {$value};\n";
        }

        // Typography
        $css .= "\n  /* ===== Typography ===== */\n";
        $base_px = floatval( $tokens['typography_settings']['base_px'] ?? 16 );
        $unit = $tokens['typography_settings']['unit'] ?? 'px';

        foreach ( $tokens['typography'] as $tag => $props ) {
            $css .= "  /* {$tag} */\n";
            if ( ! empty( $props['font_family'] ) ) {
                $css .= "  --jj-typo-{$tag}-font-family: {$props['font_family']};\n";
            }
            if ( ! empty( $props['font_weight'] ) ) {
                $css .= "  --jj-typo-{$tag}-font-weight: {$props['font_weight']};\n";
            }
            if ( ! empty( $props['line_height'] ) ) {
                $css .= "  --jj-typo-{$tag}-line-height: {$props['line_height']}em;\n";
            }
            if ( isset( $props['letter_spacing'] ) && $props['letter_spacing'] !== '' ) {
                $css .= "  --jj-typo-{$tag}-letter-spacing: {$props['letter_spacing']}px;\n";
            }
            // Font sizes (desktop)
            if ( is_array( $props['font_size'] ) && isset( $props['font_size']['desktop'] ) ) {
                $size = floatval( $props['font_size']['desktop'] );
                if ( $unit === 'rem' || $unit === 'em' ) {
                    $converted = round( $size / $base_px, 4 ) . $unit;
                } else {
                    $converted = $size . 'px';
                }
                $css .= "  --jj-typo-{$tag}-font-size: {$converted};\n";
            }
        }

        $css .= "}\n";

        // Responsive breakpoints
        $breakpoints = array(
            'phone_small' => array( 'max' => 374 ),
            'mobile' => array( 'min' => 375, 'max' => 479 ),
            'phablet' => array( 'min' => 480, 'max' => 767 ),
            'tablet' => array( 'min' => 768, 'max' => 1023 ),
            'laptop' => array( 'min' => 1024, 'max' => 1439 ),
            'desktop_qhd' => array( 'min' => 2560, 'max' => 3839 ),
            'desktop_uhd' => array( 'min' => 3840, 'max' => 5119 ),
            'desktop_5k' => array( 'min' => 5120, 'max' => 7679 ),
            'desktop_8k' => array( 'min' => 7680 ),
        );

        foreach ( $breakpoints as $bp_key => $bp ) {
            $has_size = false;
            foreach ( $tokens['typography'] as $tag => $props ) {
                if ( is_array( $props['font_size'] ) && ! empty( $props['font_size'][ $bp_key ] ) ) {
                    $has_size = true;
                    break;
                }
            }
            if ( ! $has_size ) continue;

            if ( isset( $bp['min'] ) && isset( $bp['max'] ) ) {
                $css .= "\n@media (min-width: {$bp['min']}px) and (max-width: {$bp['max']}px) {\n";
            } elseif ( isset( $bp['max'] ) ) {
                $css .= "\n@media (max-width: {$bp['max']}px) {\n";
            } elseif ( isset( $bp['min'] ) ) {
                $css .= "\n@media (min-width: {$bp['min']}px) {\n";
            } else {
                continue;
            }

            $css .= "  :root {\n";
            foreach ( $tokens['typography'] as $tag => $props ) {
                if ( is_array( $props['font_size'] ) && ! empty( $props['font_size'][ $bp_key ] ) ) {
                    $size = floatval( $props['font_size'][ $bp_key ] );
                    if ( $unit === 'rem' || $unit === 'em' ) {
                        $converted = round( $size / $base_px, 4 ) . $unit;
                    } else {
                        $converted = $size . 'px';
                    }
                    $css .= "    --jj-typo-{$tag}-font-size: {$converted};\n";
                }
            }
            $css .= "  }\n}\n";
        }

        return $css;
    }

    /**
     * JSON 디자인 토큰 생성
     */
    public function generate_json_tokens() {
        $tokens = $this->get_style_tokens();
        return wp_json_encode( $tokens, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    }

    /**
     * HTML 스타일가이드 생성
     */
    public function generate_html_guide() {
        $tokens = $this->get_style_tokens();
        $css = $this->generate_css_variables();

        $html = '<!DOCTYPE html>
<html lang="' . esc_attr( get_locale() ) . '">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . esc_html( get_bloginfo( 'name' ) ) . ' - Style Guide</title>
    <style>
' . $css . '

/* Style Guide Styles */
* { box-sizing: border-box; margin: 0; padding: 0; }
body { 
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Noto Sans KR", sans-serif;
    line-height: 1.6;
    color: var(--jj-color-text, #1d2327);
    background: var(--jj-color-background, #f0f0f1);
    padding: 40px 20px;
}
.container { max-width: 1200px; margin: 0 auto; }
.header { text-align: center; margin-bottom: 60px; }
.header h1 { font-size: 2.5rem; margin-bottom: 10px; color: var(--jj-color-primary); }
.header p { color: var(--jj-color-text-secondary); }
.section { background: #fff; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
.section-title { font-size: 1.5rem; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid var(--jj-color-primary); color: var(--jj-color-text); }
.color-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; }
.color-card { border-radius: 8px; overflow: hidden; border: 1px solid var(--jj-color-border); }
.color-swatch { height: 80px; }
.color-info { padding: 12px; background: #fff; }
.color-name { font-weight: 600; font-size: 13px; margin-bottom: 4px; }
.color-value { font-family: monospace; font-size: 12px; color: var(--jj-color-text-secondary); }
.typo-sample { margin-bottom: 25px; padding: 20px; background: #fafafa; border-radius: 8px; }
.typo-label { font-size: 12px; color: var(--jj-color-text-secondary); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
.typo-preview h1 { font-family: var(--jj-typo-h1-font-family, inherit); font-size: var(--jj-typo-h1-font-size, 40px); font-weight: var(--jj-typo-h1-font-weight, 700); line-height: var(--jj-typo-h1-line-height, 1.2em); }
.typo-preview h2 { font-family: var(--jj-typo-h2-font-family, inherit); font-size: var(--jj-typo-h2-font-size, 32px); font-weight: var(--jj-typo-h2-font-weight, 700); line-height: var(--jj-typo-h2-line-height, 1.3em); }
.typo-preview h3 { font-family: var(--jj-typo-h3-font-family, inherit); font-size: var(--jj-typo-h3-font-size, 26px); font-weight: var(--jj-typo-h3-font-weight, 600); line-height: var(--jj-typo-h3-line-height, 1.35em); }
.typo-preview p { font-family: var(--jj-typo-p-font-family, inherit); font-size: var(--jj-typo-p-font-size, 16px); font-weight: var(--jj-typo-p-font-weight, 400); line-height: var(--jj-typo-p-line-height, 1.7em); }
.btn-grid { display: flex; flex-wrap: wrap; gap: 15px; }
.btn { display: inline-flex; align-items: center; justify-content: center; padding: 12px 24px; border-radius: 6px; font-weight: 600; text-decoration: none; transition: all 0.2s; cursor: pointer; border: none; }
.btn-primary { background: var(--jj-color-primary); color: #fff; }
.btn-primary:hover { background: var(--jj-color-primary-hover); }
.btn-secondary { background: var(--jj-color-secondary); color: #fff; }
.btn-secondary:hover { background: var(--jj-color-secondary-hover); }
.btn-outline { background: transparent; border: 2px solid var(--jj-color-primary); color: var(--jj-color-primary); }
.btn-outline:hover { background: var(--jj-color-primary); color: #fff; }
.footer { text-align: center; margin-top: 60px; padding: 20px; color: var(--jj-color-text-secondary); font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Style Guide</h1>
            <p>' . esc_html( get_bloginfo( 'name' ) ) . ' &bull; Generated ' . esc_html( $tokens['meta']['exported_at'] ) . '</p>
        </div>

        <div class="section">
            <h2 class="section-title">Colors</h2>
            <div class="color-grid">';

        foreach ( $tokens['colors'] as $name => $value ) {
            $display_name = ucwords( str_replace( '_', ' ', $name ) );
            $html .= '
                <div class="color-card">
                    <div class="color-swatch" style="background: ' . esc_attr( $value ) . ';"></div>
                    <div class="color-info">
                        <div class="color-name">' . esc_html( $display_name ) . '</div>
                        <div class="color-value">' . esc_html( $value ) . '</div>
                    </div>
                </div>';
        }

        $html .= '
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">Typography</h2>';

        $typo_samples = array(
            'h1' => 'Heading 1 - The quick brown fox jumps',
            'h2' => 'Heading 2 - The quick brown fox jumps',
            'h3' => 'Heading 3 - The quick brown fox jumps',
            'p' => 'Paragraph - The quick brown fox jumps over the lazy dog. 다람쥐 헌 쳇바퀴에 타고파. 키스의 고유한 자켓은 , , 。',
        );

        foreach ( $typo_samples as $tag => $sample ) {
            $props = $tokens['typography'][ $tag ] ?? array();
            $meta = array();
            if ( ! empty( $props['font_family'] ) ) $meta[] = $props['font_family'];
            if ( ! empty( $props['font_weight'] ) ) $meta[] = $props['font_weight'];
            if ( is_array( $props['font_size'] ) && ! empty( $props['font_size']['desktop'] ) ) {
                $meta[] = $props['font_size']['desktop'] . 'px';
            }
            $html .= '
            <div class="typo-sample">
                <div class="typo-label">' . strtoupper( $tag ) . ( $meta ? ' (' . implode( ', ', $meta ) . ')' : '' ) . '</div>
                <div class="typo-preview"><' . $tag . '>' . esc_html( $sample ) . '</' . $tag . '></div>
            </div>';
        }

        $html .= '
        </div>

        <div class="section">
            <h2 class="section-title">Buttons</h2>
            <div class="btn-grid">
                <button class="btn btn-primary">Primary Button</button>
                <button class="btn btn-secondary">Secondary Button</button>
                <button class="btn btn-outline">Outline Button</button>
            </div>
        </div>

        <div class="footer">
            <p>Generated by JJ Style Guide v' . esc_html( $tokens['meta']['plugin_version'] ) . '</p>
            <p><a href="' . esc_url( $tokens['meta']['site_url'] ) . '">' . esc_html( $tokens['meta']['site_url'] ) . '</a></p>
        </div>
    </div>
</body>
</html>';

        return $html;
    }

    /**
     * AJAX: CSS 내보내기
     */
    public function ajax_export_css() {
        check_ajax_referer( 'jj_style_guide_exporter_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 403 );
        }

        $css = $this->generate_css_variables();
        $filename = sanitize_file_name( get_bloginfo( 'name' ) ) . '-design-tokens.css';

        wp_send_json_success( array(
            'content' => $css,
            'filename' => $filename,
            'mime' => 'text/css',
        ) );
    }

    /**
     * AJAX: JSON 내보내기
     */
    public function ajax_export_json() {
        check_ajax_referer( 'jj_style_guide_exporter_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 403 );
        }

        $json = $this->generate_json_tokens();
        $filename = sanitize_file_name( get_bloginfo( 'name' ) ) . '-design-tokens.json';

        wp_send_json_success( array(
            'content' => $json,
            'filename' => $filename,
            'mime' => 'application/json',
        ) );
    }

    /**
     * AJAX: HTML 내보내기
     */
    public function ajax_export_html() {
        check_ajax_referer( 'jj_style_guide_exporter_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 403 );
        }

        $html = $this->generate_html_guide();
        $filename = sanitize_file_name( get_bloginfo( 'name' ) ) . '-style-guide.html';

        wp_send_json_success( array(
            'content' => $html,
            'filename' => $filename,
            'mime' => 'text/html',
        ) );
    }

    /**
     * AJAX: ZIP 내보내기 (HTML + CSS + JSON)
     */
    public function ajax_export_zip() {
        check_ajax_referer( 'jj_style_guide_exporter_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 403 );
        }

        if ( ! class_exists( 'ZipArchive' ) ) {
            // ZipArchive가 없으면 개별 파일로 대체
            wp_send_json_error( array(
                'message' => __( 'ZIP 기능을 사용할 수 없습니다. 개별 파일로 내보내기를 이용해 주세요.', 'acf-css-really-simple-style-management-center' ),
            ) );
        }

        $site_name = sanitize_file_name( get_bloginfo( 'name' ) );
        $upload_dir = wp_upload_dir();
        $temp_dir = $upload_dir['basedir'] . '/jj-style-guide-export/';
        $zip_path = $temp_dir . $site_name . '-style-guide.zip';

        // 임시 디렉토리 생성
        if ( ! file_exists( $temp_dir ) ) {
            wp_mkdir_p( $temp_dir );
        }

        // 기존 ZIP 삭제
        if ( file_exists( $zip_path ) ) {
            wp_delete_file( $zip_path );
        }

        $zip = new ZipArchive();
        if ( $zip->open( $zip_path, ZipArchive::CREATE ) !== true ) {
            wp_send_json_error( array( 'message' => __( 'ZIP 파일을 생성할 수 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // 파일 추가
        $zip->addFromString( 'style-guide.html', $this->generate_html_guide() );
        $zip->addFromString( 'design-tokens.css', $this->generate_css_variables() );
        $zip->addFromString( 'design-tokens.json', $this->generate_json_tokens() );
        $zip->addFromString( 'README.txt', $this->generate_readme() );

        $zip->close();

        // ZIP 파일 URL 반환
        $zip_url = $upload_dir['baseurl'] . '/jj-style-guide-export/' . $site_name . '-style-guide.zip';

        wp_send_json_success( array(
            'url' => $zip_url,
            'filename' => $site_name . '-style-guide.zip',
        ) );
    }

    /**
     * README 생성
     */
    private function generate_readme() {
        $tokens = $this->get_style_tokens();
        $readme = "JJ Style Guide Export\n";
        $readme .= "======================\n\n";
        $readme .= "Site: " . $tokens['meta']['site_name'] . "\n";
        $readme .= "URL: " . $tokens['meta']['site_url'] . "\n";
        $readme .= "Exported: " . $tokens['meta']['exported_at'] . "\n";
        $readme .= "Plugin Version: " . $tokens['meta']['plugin_version'] . "\n\n";
        $readme .= "Files included:\n";
        $readme .= "- style-guide.html: Complete style guide page\n";
        $readme .= "- design-tokens.css: CSS custom properties (variables)\n";
        $readme .= "- design-tokens.json: JSON design tokens for programmatic use\n\n";
        $readme .= "Usage:\n";
        $readme .= "1. Open style-guide.html in any browser to view the style guide.\n";
        $readme .= "2. Import design-tokens.css into your project to use the CSS variables.\n";
        $readme .= "3. Use design-tokens.json for integrations with design tools like Figma.\n";
        return $readme;
    }
}
