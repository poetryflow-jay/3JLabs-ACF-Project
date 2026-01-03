<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 3J Labs Style Presets
 * - 제니(CPO)의 디자인 감성을 담은 프리셋 라이브러리
 * - 사용자가 즉시 적용할 수 있는 5가지 테마 제공
 */
class JJ_Style_Presets {
    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 제니의 5대 핵심 테마 데이터
     */
    public function get_presets() {
        return array(
            'modern_vivid' => array(
                'name'        => __( 'Modern Vivid', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '세련되고 생동감 넘치는 현대적 디자인', 'acf-css-really-simple-style-management-center' ),
                'thumbnail'   => 'dashicons-art',
                'colors' => array(
                    'primary'    => '#3b82f6',
                    'secondary'  => '#1e293b',
                    'accent'     => '#f59e0b',
                    'background' => '#ffffff',
                    'foreground' => '#0f172a',
                ),
                'typography' => array(
                    'font_family_primary' => '"Inter", system-ui, sans-serif',
                    'font_size_base'      => '16px',
                ),
                'buttons' => array(
                    'border_radius' => '8px',
                )
            ),
            'classic_luxury' => array(
                'name'        => __( 'Classic Luxury', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '고전적인 품격과 신뢰를 주는 디자인', 'acf-css-really-simple-style-management-center' ),
                'thumbnail'   => 'dashicons-admin-appearance',
                'colors' => array(
                    'primary'    => '#1e3a8a',
                    'secondary'  => '#475569',
                    'accent'     => '#b45309',
                    'background' => '#f8fafc',
                    'foreground' => '#1e293b',
                ),
                'typography' => array(
                    'font_family_primary' => '"Playfair Display", serif',
                    'font_size_base'      => '17px',
                ),
                'buttons' => array(
                    'border_radius' => '0px',
                )
            ),
            'nordic_minimal' => array(
                'name'        => __( 'Nordic Minimal', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '여백의 미와 따뜻한 미니멀리즘', 'acf-css-really-simple-style-management-center' ),
                'thumbnail'   => 'dashicons-lightbulb',
                'colors' => array(
                    'primary'    => '#4b5563',
                    'secondary'  => '#9ca3af',
                    'accent'     => '#d1d5db',
                    'background' => '#f3f4f6',
                    'foreground' => '#111827',
                ),
                'typography' => array(
                    'font_family_primary' => '"Outfit", sans-serif',
                    'font_size_base'      => '15px',
                ),
                'buttons' => array(
                    'border_radius' => '24px',
                )
            ),
            'cyber_dark' => array(
                'name'        => __( 'Cyber Dark', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '강렬한 대비와 미래 지향적 다크 모드', 'acf-css-really-simple-style-management-center' ),
                'thumbnail'   => 'dashicons-visibility',
                'colors' => array(
                    'primary'    => '#06b6d4',
                    'secondary'  => '#0ea5e9',
                    'accent'     => '#a855f7',
                    'background' => '#020617',
                    'foreground' => '#f8fafc',
                ),
                'typography' => array(
                    'font_family_primary' => '"JetBrains Mono", monospace',
                    'font_size_base'      => '14px',
                ),
                'buttons' => array(
                    'border_radius' => '4px',
                )
            ),
            'soft_pastel' => array(
                'name'        => __( 'Soft Pastel', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '부드럽고 친근한 파스텔 톤 디자인', 'acf-css-really-simple-style-management-center' ),
                'thumbnail'   => 'dashicons-heart',
                'colors' => array(
                    'primary'    => '#fda4af',
                    'secondary'  => '#f9a8d4',
                    'accent'     => '#fef08a',
                    'background' => '#fff1f2',
                    'foreground' => '#4c0519',
                ),
                'typography' => array(
                    'font_family_primary' => '"Quicksand", sans-serif',
                    'font_size_base'      => '16px',
                ),
                'buttons' => array(
                    'border_radius' => '100px',
                )
            )
        );
    }
}
