<?php
/**
 * ACF CSS Landing - 자식 테마 Functions
 * 
 * Kadence/Flavor 기반 랜딩 페이지 테마
 * 
 * @package ACF_CSS_Landing
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 부모 테마 + 자식 테마 스타일 로드
 */
function acf_css_landing_enqueue_styles() {
    // 부모 테마 스타일
    wp_enqueue_style( 
        'parent-style', 
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme()->parent()->get('Version')
    );
    
    // 자식 테마 스타일
    wp_enqueue_style( 
        'acf-css-landing-style', 
        get_stylesheet_uri(),
        array( 'parent-style' ),
        wp_get_theme()->get('Version')
    );
    
    // Google Fonts
    wp_enqueue_style(
        'acf-css-landing-fonts',
        'https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;700;900&family=Space+Grotesk:wght@400;500;700&display=swap',
        array(),
        null
    );
}
add_action( 'wp_enqueue_scripts', 'acf_css_landing_enqueue_styles' );

/**
 * 랜딩 페이지 템플릿에 body class 추가
 */
function acf_css_landing_body_class( $classes ) {
    if ( is_page_template( 'template-landing.php' ) ) {
        $classes[] = 'acf-landing';
    }
    return $classes;
}
add_filter( 'body_class', 'acf_css_landing_body_class' );

/**
 * Kadence Blocks 또는 Flavor Blocks 호환성
 */
function acf_css_landing_block_patterns() {
    // 커스텀 블록 패턴 등록 (Kadence Blocks 사용 시)
    if ( function_exists( 'register_block_pattern' ) ) {
        register_block_pattern(
            'acf-css-landing/hero',
            array(
                'title'       => __( 'ACF CSS Hero Section', 'acf-css-landing' ),
                'description' => __( 'ACF CSS Manager 랜딩 페이지 Hero 섹션', 'acf-css-landing' ),
                'content'     => '<!-- wp:group {"className":"acf-hero"} --><div class="wp-block-group acf-hero"><!-- 콘텐츠 --></div><!-- /wp:group -->',
                'categories'  => array( 'acf-css-landing' ),
            )
        );
    }
}
add_action( 'init', 'acf_css_landing_block_patterns' );

/**
 * WooCommerce 라이센스 상품 연동 준비
 */
function acf_css_landing_woocommerce_support() {
    // WooCommerce 활성화 시 추가 설정
    if ( class_exists( 'WooCommerce' ) ) {
        // 커스텀 상품 타입 (라이센스) 지원
        add_theme_support( 'woocommerce' );
        
        // 라이센스 상품 메타 필드 추가 (향후 확장)
        add_filter( 'woocommerce_product_data_tabs', 'acf_css_landing_license_product_tab' );
    }
}
add_action( 'after_setup_theme', 'acf_css_landing_woocommerce_support' );

/**
 * WooCommerce 라이센스 상품 탭 (향후 확장용)
 */
function acf_css_landing_license_product_tab( $tabs ) {
    // 라이센스 관련 탭 추가 예정
    return $tabs;
}

/**
 * Neural Link 연동을 위한 훅 (향후 확장용)
 */
function acf_css_landing_neural_link_integration() {
    // WooCommerce 주문 완료 시 Neural Link에 라이센스 발행 요청
    // add_action( 'woocommerce_order_status_completed', 'acf_css_issue_license_on_purchase' );
}
add_action( 'init', 'acf_css_landing_neural_link_integration' );

/**
 * 커스터마이저 옵션 추가
 */
function acf_css_landing_customizer( $wp_customize ) {
    // ACF CSS Landing 섹션
    $wp_customize->add_section( 'acf_css_landing_options', array(
        'title'    => __( 'ACF CSS Landing 설정', 'acf-css-landing' ),
        'priority' => 30,
    ) );
    
    // Hero 배지 텍스트
    $wp_customize->add_setting( 'acf_hero_badge_text', array(
        'default'           => '🎉 v6.2.0 출시 — AI 스타일 생성 기능 추가',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    
    $wp_customize->add_control( 'acf_hero_badge_text', array(
        'label'   => __( 'Hero 배지 텍스트', 'acf-css-landing' ),
        'section' => 'acf_css_landing_options',
        'type'    => 'text',
    ) );
    
    // CTA 버튼 URL
    $wp_customize->add_setting( 'acf_cta_button_url', array(
        'default'           => '/shop/',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    
    $wp_customize->add_control( 'acf_cta_button_url', array(
        'label'   => __( 'CTA 버튼 URL', 'acf-css-landing' ),
        'section' => 'acf_css_landing_options',
        'type'    => 'url',
    ) );
    
    // 베타 폼 URL
    $wp_customize->add_setting( 'acf_beta_form_url', array(
        'default'           => '#',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    
    $wp_customize->add_control( 'acf_beta_form_url', array(
        'label'   => __( '베타 신청 폼 URL', 'acf-css-landing' ),
        'section' => 'acf_css_landing_options',
        'type'    => 'url',
    ) );
}
add_action( 'customize_register', 'acf_css_landing_customizer' );

/**
 * 커스터마이저 설정 가져오기 헬퍼 함수
 */
function acf_css_get_option( $key, $default = '' ) {
    return get_theme_mod( $key, $default );
}

