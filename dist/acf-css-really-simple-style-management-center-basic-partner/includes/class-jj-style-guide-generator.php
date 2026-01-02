<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 9.3] Style Guide Generator
 * 
 * 스타일 가이드 자동 생성 시스템
 * - 기존 스타일 분석
 * - 일관성 검사
 * - 자동 스타일 가이드 문서 생성
 * 
 * @since 9.3.0
 */
class JJ_Style_Guide_Generator {

    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_jj_generate_style_guide', array( $this, 'ajax_generate_style_guide' ) );
        add_action( 'wp_ajax_jj_analyze_style_consistency', array( $this, 'ajax_analyze_style_consistency' ) );
    }

    /**
     * 에셋 로드
     */
    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'jj-admin-center' ) === false && 
             strpos( $hook, 'acf-css-really-simple-style-guide' ) === false ) {
            return;
        }

        wp_enqueue_script(
            'jj-style-guide-generator',
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-style-guide-generator.js',
            array( 'jquery', 'jj-common-utils' ),
            defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '9.3.0',
            true
        );

        wp_localize_script(
            'jj-style-guide-generator',
            'jjStyleGuideGenerator',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'jj_style_guide_generator_action' ),
                'strings'  => array(
                    'generating'        => __( '스타일 가이드 생성 중...', 'acf-css-really-simple-style-management-center' ),
                    'generated'        => __( '스타일 가이드가 생성되었습니다.', 'acf-css-really-simple-style-management-center' ),
                    'analyzing'        => __( '스타일 일관성 분석 중...', 'acf-css-really-simple-style-management-center' ),
                    'analysis_complete' => __( '분석이 완료되었습니다.', 'acf-css-really-simple-style-management-center' ),
                ),
            )
        );
    }

    /**
     * 스타일 가이드 생성
     */
    public function generate_style_guide( $options = array() ) {
        $options = wp_parse_args( $options, array(
            'include_palettes'  => true,
            'include_typography' => true,
            'include_buttons'  => true,
            'include_forms'      => true,
            'format'            => 'html', // html, markdown, json
            'template'          => 'default',
        ) );

        $style_data = $this->collect_style_data( $options );
        $guide = $this->build_style_guide( $style_data, $options );

        return array(
            'guide'      => $guide,
            'data'       => $style_data,
            'format'     => $options['format'],
            'generated_at' => current_time( 'mysql' ),
        );
    }

    /**
     * 스타일 데이터 수집
     */
    private function collect_style_data( $options ) {
        $data = array();
        $all_options = get_option( 'jj_style_guide_options', array() );

        // 팔레트
        if ( $options['include_palettes'] && isset( $all_options['palettes'] ) ) {
            $data['palettes'] = $all_options['palettes'];
        }

        // 타이포그래피
        if ( $options['include_typography'] && isset( $all_options['typography'] ) ) {
            $data['typography'] = $all_options['typography'];
        }

        // 버튼
        if ( $options['include_buttons'] && isset( $all_options['buttons'] ) ) {
            $data['buttons'] = $all_options['buttons'];
        }

        // 폼
        if ( $options['include_forms'] && isset( $all_options['forms'] ) ) {
            $data['forms'] = $all_options['forms'];
        }

        return $data;
    }

    /**
     * 스타일 가이드 문서 빌드
     */
    private function build_style_guide( $data, $options ) {
        $format = $options['format'];

        switch ( $format ) {
            case 'markdown':
                return $this->build_markdown_guide( $data );
            case 'json':
                return $this->build_json_guide( $data );
            case 'html':
            default:
                return $this->build_html_guide( $data );
        }
    }

    /**
     * HTML 스타일 가이드 생성
     */
    private function build_html_guide( $data ) {
        // 안전한 토큰 추출 (색상/타입)
        $primary   = $this->get_color_from_data( $data, array( 'palettes', 'brand', 'colors', 'primary_color' ), '#2271b1' );
        $secondary = $this->get_color_from_data( $data, array( 'palettes', 'brand', 'colors', 'secondary_color' ), '#50575e' );
        $accent    = $this->get_color_from_data( $data, array( 'palettes', 'brand', 'colors', 'primary_color_hover' ), $primary );
        $text      = $this->get_color_from_data( $data, array( 'palettes', 'system', 'colors', 'text_color' ), '#1d2327' );
        $bg        = $this->get_color_from_data( $data, array( 'palettes', 'system', 'colors', 'site_bg' ), '#0b0c10' );

        $h1_typo = $this->get_typography_snapshot( $data, 'h1' );
        $p_typo  = $this->get_typography_snapshot( $data, 'p' );

        $html = '<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>스타일 가이드</title>
    <style>
        :root {
            --jj-primary: ' . esc_attr( $primary ) . ';
            --jj-secondary: ' . esc_attr( $secondary ) . ';
            --jj-accent: ' . esc_attr( $accent ) . ';
            --jj-text: ' . esc_attr( $text ) . ';
            --jj-bg: ' . esc_attr( $bg ) . ';
        }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #333; max-width: 1200px; margin: 0 auto; padding: 20px; }
        h1 { border-bottom: 3px solid var(--jj-primary); padding-bottom: 10px; }
        h2 { border-bottom: 2px solid #ddd; padding-bottom: 8px; margin-top: 30px; }
        .meta { color: #555; margin-bottom: 16px; }
        .tip { background: #f5f7fa; border-left: 4px solid var(--jj-primary); padding: 12px 14px; border-radius: 4px; margin: 14px 0; font-size: 14px; color: #333; }
        .color-palette { display: flex; flex-wrap: wrap; gap: 15px; margin: 20px 0; }
        .color-item { width: 150px; text-align: center; }
        .color-swatch { width: 100%; height: 100px; border-radius: 8px; margin-bottom: 8px; border: 1px solid #ddd; box-shadow: 0 6px 18px rgba(0,0,0,0.06); }
        .button-preview { display: inline-block; padding: 10px 20px; margin: 5px; border-radius: 999px; text-decoration: none; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f9fafb; font-weight: 600; }
        .hero-sample { margin: 30px 0; padding: 40px; border-radius: 16px; background: linear-gradient(135deg, rgba(34,113,177,0.12), rgba(34,113,177,0.04)); border: 1px solid rgba(34,113,177,0.12); color: var(--jj-text); box-shadow: 0 12px 28px rgba(0,0,0,0.08); }
        .hero-sample h2 { border: 0; margin: 0 0 12px 0; font-size: ' . esc_attr( $h1_typo['font_size'] ) . '; line-height: ' . esc_attr( $h1_typo['line_height'] ) . '; color: var(--jj-text); }
        .hero-sample p { margin: 0 0 18px 0; font-size: ' . esc_attr( $p_typo['font_size'] ) . '; line-height: ' . esc_attr( $p_typo['line_height'] ) . '; color: rgba(0,0,0,0.8); }
        .hero-buttons { display: flex; gap: 12px; flex-wrap: wrap; }
        .hero-btn-primary { background: var(--jj-primary); color: #fff; border: none; }
        .hero-btn-secondary { background: #fff; color: var(--jj-primary); border: 1px solid var(--jj-primary); }
        .cards { display: grid; grid-template-columns: repeat(auto-fit,minmax(240px,1fr)); gap: 14px; margin-top: 18px; }
        .card { background: #fff; border: 1px solid #eceff3; border-radius: 12px; padding: 16px; box-shadow: 0 8px 16px rgba(0,0,0,0.04); }
        .card h4 { margin: 0 0 8px 0; font-size: 16px; color: var(--jj-text); }
        .card p { margin: 0; font-size: 14px; color: #4a4f57; }
    </style>
</head>
<body>
    <h1>스타일 가이드</h1>
    <div class="meta"><strong>생성일:</strong> ' . date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) . '</div>
    <div class="tip"><strong>사용법:</strong> 아래 팔레트/타이포/버튼 예시는 JJ 토큰을 그대로 반영합니다. 이 페이지에서 값을 조정하면 사이트 전역(버튼, 본문, 헤딩, 카드 등)에 바로 연결되도록 설계하세요.</div>

    <div class="hero-sample">
        <h2>브랜드 메시지를 한눈에 전달하세요</h2>
        <p>헤딩은 명확하게, 본문은 읽기 쉽게. CTA는 Primary 컬러로 눈에 띄게 강조하세요.</p>
        <div class="hero-buttons">
            <a class="button-preview hero-btn-primary" href="#">시작하기</a>
            <a class="button-preview hero-btn-secondary" href="#">자세히 보기</a>
        </div>
        <div class="cards">
            <div class="card">
                <h4>콘트라스트 확보</h4>
                <p>본문 텍스트는 Text 컬러, 배경은 BG 컬러를 사용해 4.5:1 이상 대비를 유지합니다.</p>
            </div>
            <div class="card">
                <h4>CTA 우선순위</h4>
                <p>Primary 버튼은 가장 중요한 행동, Secondary는 보조 행동에 사용하세요.</p>
            </div>
            <div class="card">
                <h4>타이포 위계</h4>
                <p>H1/H2는 페이지 제목, H3/H4는 섹션 제목, 본문은 Paragraph를 사용합니다.</p>
            </div>
        </div>
    </div>';

        // 팔레트 섹션
        if ( ! empty( $data['palettes'] ) ) {
            $html .= '<h2>색상 팔레트</h2>';
            foreach ( $data['palettes'] as $palette_key => $palette ) {
                $palette_name = isset( $palette['name'] ) ? $palette['name'] : $palette_key;
                $html .= '<h3>' . esc_html( $palette_name ) . '</h3>';
                $html .= '<div class="color-palette">';
                
                if ( isset( $palette['colors'] ) && is_array( $palette['colors'] ) ) {
                    foreach ( $palette['colors'] as $color_key => $color ) {
                        $color_value = is_array( $color ) ? ( $color['color'] ?? $color_key ) : $color;
                        $color_name = is_array( $color ) ? ( $color['name'] ?? $color_key ) : $color_key;
                        
                        $html .= '<div class="color-item">
                            <div class="color-swatch" style="background-color: ' . esc_attr( $color_value ) . ';"></div>
                            <div><strong>' . esc_html( $color_name ) . '</strong></div>
                            <div style="font-size: 12px; color: #666;">' . esc_html( $color_value ) . '</div>
                        </div>';
                    }
                }
                
                $html .= '</div>';
            }
        }

        // 타이포그래피 섹션
        if ( ! empty( $data['typography'] ) ) {
            $html .= '<h2>타이포그래피</h2>';
            $html .= '<table>
                <thead>
                    <tr>
                        <th>요소</th>
                        <th>폰트 패밀리</th>
                        <th>크기</th>
                        <th>행간</th>
                        <th>색상</th>
                    </tr>
                </thead>
                <tbody>';
            
            foreach ( $data['typography'] as $key => $typography ) {
                $html .= '<tr>
                    <td><strong>' . esc_html( $key ) . '</strong></td>
                    <td>' . esc_html( $typography['font_family'] ?? '-' ) . '</td>
                    <td>' . esc_html( $typography['font_size'] ?? '-' ) . '</td>
                    <td>' . esc_html( $typography['line_height'] ?? '-' ) . '</td>
                    <td><span style="display: inline-block; width: 20px; height: 20px; background-color: ' . esc_attr( $typography['color'] ?? '#333' ) . '; border: 1px solid #ddd; vertical-align: middle;"></span> ' . esc_html( $typography['color'] ?? '-' ) . '</td>
                </tr>';
            }
            
            $html .= '</tbody></table>';
        }

        // 버튼 섹션
        if ( ! empty( $data['buttons'] ) ) {
            $html .= '<h2>버튼 스타일</h2>';
            foreach ( $data['buttons'] as $button_key => $button ) {
                $button_label = isset( $button['label'] ) ? $button['label'] : $button_key;
                $bg_color = $button['background_color'] ?? '#0073aa';
                $text_color = $button['text_color'] ?? '#fff';
                $border_radius = $button['border_radius'] ?? '4px';
                
                $html .= '<h3>' . esc_html( $button_label ) . '</h3>';
                $html .= '<div>
                    <a href="#" class="button-preview" style="background-color: ' . esc_attr( $bg_color ) . '; color: ' . esc_attr( $text_color ) . '; border-radius: ' . esc_attr( $border_radius ) . ';">' . esc_html( $button_label ) . '</a>
                </div>';
                $html .= '<table style="margin-top: 10px;">
                    <tr><th>배경색</th><td>' . esc_html( $bg_color ) . '</td></tr>
                    <tr><th>텍스트 색상</th><td>' . esc_html( $text_color ) . '</td></tr>
                    <tr><th>테두리 반경</th><td>' . esc_html( $border_radius ) . '</td></tr>
                </table>';
            }
        }

        $html .= '</body></html>';

        return $html;
    }

    /**
     * 안전하게 색상 값 추출
     */
    private function get_color_from_data( $data, $keys, $fallback ) {
        $ref = $data;
        foreach ( $keys as $k ) {
            if ( is_array( $ref ) && isset( $ref[ $k ] ) ) {
                $ref = $ref[ $k ];
            } else {
                return $fallback;
            }
        }
        if ( is_string( $ref ) && $ref !== '' ) {
            return $ref;
        }
        return $fallback;
    }

    /**
     * 타이포그래피 스냅샷
     */
    private function get_typography_snapshot( $data, $key ) {
        $typo = array();
        if ( isset( $data['typography'][ $key ] ) && is_array( $data['typography'][ $key ] ) ) {
            $typo = $data['typography'][ $key ];
        }
        $font_size = isset( $typo['font_size'] ) ? (string) $typo['font_size'] : '32px';
        $line_height = isset( $typo['line_height'] ) ? (string) $typo['line_height'] : '1.3';

        // font_size가 배열(desktop)에 담긴 경우 대응
        if ( is_array( $typo['font_size'] ?? null ) && isset( $typo['font_size']['desktop'] ) ) {
            $font_size = (string) $typo['font_size']['desktop'];
        }

        return array(
            'font_size'   => $font_size,
            'line_height' => $line_height,
        );
    }

    /**
     * Markdown 스타일 가이드 생성
     */
    private function build_markdown_guide( $data ) {
        $md = "# 스타일 가이드\n\n";
        $md .= "**생성일:** " . date_i18n( get_option( 'date_format' ) ) . "\n\n";

        // 팔레트
        if ( ! empty( $data['palettes'] ) ) {
            $md .= "## 색상 팔레트\n\n";
            foreach ( $data['palettes'] as $palette_key => $palette ) {
                $palette_name = isset( $palette['name'] ) ? $palette['name'] : $palette_key;
                $md .= "### " . $palette_name . "\n\n";
                
                if ( isset( $palette['colors'] ) && is_array( $palette['colors'] ) ) {
                    foreach ( $palette['colors'] as $color_key => $color ) {
                        $color_value = is_array( $color ) ? ( $color['color'] ?? $color_key ) : $color;
                        $color_name = is_array( $color ) ? ( $color['name'] ?? $color_key ) : $color_key;
                        $md .= "- **" . $color_name . "**: `" . $color_value . "`\n";
                    }
                }
                $md .= "\n";
            }
        }

        return $md;
    }

    /**
     * JSON 스타일 가이드 생성
     */
    private function build_json_guide( $data ) {
        $json_data = array(
            'version'      => '1.0',
            'generated_at' => current_time( 'mysql' ),
            'data'        => $data,
        );

        return wp_json_encode( $json_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    }

    /**
     * 스타일 일관성 분석
     */
    public function analyze_consistency() {
        $options = get_option( 'jj_style_guide_options', array() );
        $issues = array();
        $suggestions = array();

        // 색상 일관성 검사
        if ( isset( $options['palettes'] ) ) {
            $color_issues = $this->check_color_consistency( $options['palettes'] );
            if ( ! empty( $color_issues ) ) {
                $issues['colors'] = $color_issues;
            }
        }

        // 타이포그래피 일관성 검사
        if ( isset( $options['typography'] ) ) {
            $typography_issues = $this->check_typography_consistency( $options['typography'] );
            if ( ! empty( $typography_issues ) ) {
                $issues['typography'] = $typography_issues;
            }
        }

        // 버튼 스타일 일관성 검사
        if ( isset( $options['buttons'] ) ) {
            $button_issues = $this->check_button_consistency( $options['buttons'] );
            if ( ! empty( $button_issues ) ) {
                $issues['buttons'] = $button_issues;
            }
        }

        return array(
            'issues'      => $issues,
            'suggestions' => $suggestions,
            'score'       => $this->calculate_consistency_score( $issues ),
        );
    }

    /**
     * 색상 일관성 검사
     */
    private function check_color_consistency( $palettes ) {
        $issues = array();
        $all_colors = array();

        foreach ( $palettes as $palette_key => $palette ) {
            if ( isset( $palette['colors'] ) && is_array( $palette['colors'] ) ) {
                foreach ( $palette['colors'] as $color_key => $color ) {
                    $color_value = is_array( $color ) ? ( $color['color'] ?? $color_key ) : $color;
                    $all_colors[] = strtolower( $color_value );
                }
            }
        }

        // 중복 색상 검사
        $color_counts = array_count_values( $all_colors );
        foreach ( $color_counts as $color => $count ) {
            if ( $count > 3 ) {
                $issues[] = array(
                    'type'    => 'duplicate_color',
                    'message' => sprintf( __( '색상 %s가 %d번 사용되었습니다. 통일을 고려하세요.', 'acf-css-really-simple-style-management-center' ), $color, $count ),
                    'severity' => 'warning',
                );
            }
        }

        return $issues;
    }

    /**
     * 타이포그래피 일관성 검사
     */
    private function check_typography_consistency( $typography ) {
        $issues = array();
        $font_families = array();

        foreach ( $typography as $key => $typo ) {
            if ( isset( $typo['font_family'] ) ) {
                $font_families[] = $typo['font_family'];
            }
        }

        $font_counts = array_count_values( $font_families );
        if ( count( $font_counts ) > 3 ) {
            $issues[] = array(
                'type'    => 'too_many_fonts',
                'message' => __( '너무 많은 폰트 패밀리가 사용되고 있습니다. 일관성을 위해 2-3개로 제한하는 것을 권장합니다.', 'acf-css-really-simple-style-management-center' ),
                'severity' => 'warning',
            );
        }

        return $issues;
    }

    /**
     * 버튼 일관성 검사
     */
    private function check_button_consistency( $buttons ) {
        $issues = array();
        $border_radii = array();

        foreach ( $buttons as $button_key => $button ) {
            if ( isset( $button['border_radius'] ) ) {
                $border_radii[] = $button['border_radius'];
            }
        }

        $radius_counts = array_count_values( $border_radii );
        if ( count( $radius_counts ) > 2 ) {
            $issues[] = array(
                'type'    => 'inconsistent_border_radius',
                'message' => __( '버튼 테두리 반경이 일관되지 않습니다. 통일을 권장합니다.', 'acf-css-really-simple-style-management-center' ),
                'severity' => 'info',
            );
        }

        return $issues;
    }

    /**
     * 일관성 점수 계산
     */
    private function calculate_consistency_score( $issues ) {
        $total_issues = 0;
        foreach ( $issues as $category_issues ) {
            $total_issues += count( $category_issues );
        }

        // 100점 만점, 이슈당 5점 감점
        $score = max( 0, 100 - ( $total_issues * 5 ) );
        return $score;
    }

    /**
     * AJAX: 스타일 가이드 생성
     */
    public function ajax_generate_style_guide() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_style_guide_generator_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $options = array(
            'include_palettes'  => isset( $_POST['include_palettes'] ) ? (bool) $_POST['include_palettes'] : true,
            'include_typography' => isset( $_POST['include_typography'] ) ? (bool) $_POST['include_typography'] : true,
            'include_buttons'  => isset( $_POST['include_buttons'] ) ? (bool) $_POST['include_buttons'] : true,
            'include_forms'    => isset( $_POST['include_forms'] ) ? (bool) $_POST['include_forms'] : true,
            'format'           => isset( $_POST['format'] ) ? sanitize_text_field( $_POST['format'] ) : 'html',
        );

        $result = $this->generate_style_guide( $options );

        wp_send_json_success( $result );
    }

    /**
     * AJAX: 스타일 일관성 분석
     */
    public function ajax_analyze_style_consistency() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_style_guide_generator_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $analysis = $this->analyze_consistency();

        wp_send_json_success( $analysis );
    }
}
