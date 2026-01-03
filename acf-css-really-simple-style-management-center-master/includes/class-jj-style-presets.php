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
            ),
            'ecommerce_pro' => array(
                'name'        => __( 'E-commerce Pro', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '쇼핑몰 최적화 - 전환율 중심 디자인', 'acf-css-really-simple-style-management-center' ),
                'thumbnail'   => 'dashicons-cart',
                'industry'    => 'ecommerce',
                'colors' => array(
                    'primary'    => '#10b981',
                    'secondary'  => '#059669',
                    'accent'     => '#f59e0b',
                    'background' => '#ffffff',
                    'foreground' => '#111827',
                ),
                'typography' => array(
                    'font_family_primary' => '"Poppins", sans-serif',
                    'font_size_base'      => '16px',
                ),
                'buttons' => array(
                    'border_radius' => '6px',
                )
            ),
            'blog_magazine' => array(
                'name'        => __( 'Blog & Magazine', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '콘텐츠 중심 - 가독성과 타이포그래피', 'acf-css-really-simple-style-management-center' ),
                'thumbnail'   => 'dashicons-media-document',
                'industry'    => 'blog',
                'colors' => array(
                    'primary'    => '#1e293b',
                    'secondary'  => '#475569',
                    'accent'     => '#dc2626',
                    'background' => '#f8fafc',
                    'foreground' => '#0f172a',
                ),
                'typography' => array(
                    'font_family_primary' => '"Merriweather", serif',
                    'font_size_base'      => '18px',
                ),
                'buttons' => array(
                    'border_radius' => '2px',
                )
            ),
            'portfolio_creative' => array(
                'name'        => __( 'Portfolio Creative', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '크리에이터를 위한 비주얼 중심 포트폴리오', 'acf-css-really-simple-style-management-center' ),
                'thumbnail'   => 'dashicons-portfolio',
                'industry'    => 'portfolio',
                'colors' => array(
                    'primary'    => '#8b5cf6',
                    'secondary'  => '#a78bfa',
                    'accent'     => '#f472b6',
                    'background' => '#fafafa',
                    'foreground' => '#171717',
                ),
                'typography' => array(
                    'font_family_primary' => '"Space Grotesk", sans-serif',
                    'font_size_base'      => '16px',
                ),
                'buttons' => array(
                    'border_radius' => '12px',
                )
            ),
            'agency_bold' => array(
                'name'        => __( 'Agency Bold', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '에이전시를 위한 전문성과 신뢰감', 'acf-css-really-simple-style-management-center' ),
                'thumbnail'   => 'dashicons-businessman',
                'industry'    => 'agency',
                'colors' => array(
                    'primary'    => '#0f172a',
                    'secondary'  => '#334155',
                    'accent'     => '#06b6d4',
                    'background' => '#f1f5f9',
                    'foreground' => '#020617',
                ),
                'typography' => array(
                    'font_family_primary' => '"Work Sans", sans-serif',
                    'font_size_base'      => '16px',
                ),
                'buttons' => array(
                    'border_radius' => '4px',
                )
            ),
            'saas_clean' => array(
                'name'        => __( 'SaaS Clean', 'acf-css-really-simple-style-management-center' ),
                'description' => __( 'SaaS 제품을 위한 깔끔하고 모던한 UI', 'acf-css-really-simple-style-management-center' ),
                'thumbnail'   => 'dashicons-cloud',
                'industry'    => 'saas',
                'colors' => array(
                    'primary'    => '#6366f1',
                    'secondary'  => '#8b5cf6',
                    'accent'     => '#ec4899',
                    'background' => '#ffffff',
                    'foreground' => '#111827',
                ),
                'typography' => array(
                    'font_family_primary' => '"DM Sans", sans-serif',
                    'font_size_base'      => '15px',
                ),
                'buttons' => array(
                    'border_radius' => '8px',
                )
            )
        );
    }
}
