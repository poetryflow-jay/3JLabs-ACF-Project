<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * JJ Smart Palette Generator
 * 
 * 색채학 이론(Color Theory)을 기반으로 조화로운 색상 팔레트를 자동 생성하는 엔진입니다.
 * 
 * @since v5.7.0
 */
class JJ_Smart_Palette {

    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // AJAX 핸들러 등록
        add_action( 'wp_ajax_jj_generate_smart_palette', array( $this, 'ajax_generate_smart_palette' ) );
    }

    /**
     * AJAX 요청 처리
     */
    public function ajax_generate_smart_palette() {
        check_ajax_referer( 'jj_style_guide_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }

        $base_color = isset( $_POST['base_color'] ) ? sanitize_hex_color( $_POST['base_color'] ) : '';
        $harmony = isset( $_POST['harmony'] ) ? sanitize_text_field( $_POST['harmony'] ) : 'monochromatic';

        if ( empty( $base_color ) ) {
            wp_send_json_error( array( 'message' => '색상을 선택하세요.' ) );
        }

        $palette = $this->generate_palette( $base_color, $harmony );

        wp_send_json_success( array( 'palette' => $palette ) );
    }

    /**
     * 팔레트 생성 메인 로직
     */
    public function generate_palette( $hex, $harmony ) {
        $hsl = $this->hex2hsl( $hex );
        $colors = array();

        switch ( $harmony ) {
            case 'analogous': // 유사색
                $colors = array(
                    $this->hsl2hex( $this->adjust_hue( $hsl, -30 ) ),
                    $hex,
                    $this->hsl2hex( $this->adjust_hue( $hsl, 30 ) ),
                    $this->hsl2hex( $this->adjust_hue( $hsl, 60 ) ), // Accent
                );
                break;

            case 'complementary': // 보색
                $colors = array(
                    $hex,
                    $this->hsl2hex( $this->adjust_lightness( $hsl, 20 ) ), // Lighter
                    $this->hsl2hex( $this->adjust_hue( $hsl, 180 ) ), // Complement
                    $this->hsl2hex( array( ($hsl[0] + 180) % 360, $hsl[1], max(0, $hsl[2] - 20) ) ), // Darker Complement
                );
                break;

            case 'triadic': // 3색 조화
                $colors = array(
                    $hex,
                    $this->hsl2hex( $this->adjust_hue( $hsl, 120 ) ),
                    $this->hsl2hex( $this->adjust_hue( $hsl, 240 ) ),
                    $this->hsl2hex( $this->adjust_lightness( $hsl, -20 ) ), // Darker Base
                );
                break;

            case 'monochromatic': // 단색 (명도/채도 변화)
            default:
                $colors = array(
                    $hex,
                    $this->hsl2hex( $this->adjust_lightness( $hsl, 20 ) ), // Light
                    $this->hsl2hex( $this->adjust_lightness( $hsl, 40 ) ), // Lighter
                    $this->hsl2hex( $this->adjust_lightness( $hsl, -20 ) ), // Dark
                );
                break;
        }

        // 결과 매핑 (Primary, Secondary, Background, Text)
        // 로직을 더 정교하게 다듬을 수 있음
        return array(
            'primary' => $colors[0],
            'secondary' => $colors[2], // 강조색
            'background' => '#ffffff', // 기본값
            'text' => '#333333', // 기본값
            'palette' => $colors // 전체 팔레트
        );
    }

    // --- Helper Functions ---

    private function hex2hsl( $hex ) {
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;
        $d = $max - $min;

        if ($d == 0) {
            $h = $s = 0;
        } else {
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
            switch ($max) {
                case $r: $h = ($g - $b) / $d + ($g < $b ? 6 : 0); break;
                case $g: $h = ($b - $r) / $d + 2; break;
                case $b: $h = ($r - $g) / $d + 4; break;
            }
            $h /= 6;
        }

        return array($h * 360, $s, $l);
    }

    private function hsl2hex( $hsl ) {
        $h = $hsl[0] / 360;
        $s = $hsl[1];
        $l = $hsl[2];

        if ($s == 0) {
            $r = $g = $b = $l;
        } else {
            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;
            $r = $this->hue2rgb($p, $q, $h + 1/3);
            $g = $this->hue2rgb($p, $q, $h);
            $b = $this->hue2rgb($p, $q, $h - 1/3);
        }

        return sprintf("#%02x%02x%02x", $r * 255, $g * 255, $b * 255);
    }

    private function hue2rgb( $p, $q, $t ) {
        if ($t < 0) $t += 1;
        if ($t > 1) $t -= 1;
        if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
        if ($t < 1/2) return $q;
        if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;
        return $p;
    }

    private function adjust_hue( $hsl, $degree ) {
        $h = ($hsl[0] + $degree) % 360;
        if ( $h < 0 ) $h += 360;
        return array( $h, $hsl[1], $hsl[2] );
    }

    private function adjust_lightness( $hsl, $percent ) {
        $l = max( 0, min( 1, $hsl[2] + ($percent / 100) ) );
        return array( $hsl[0], $hsl[1], $l );
    }
}

