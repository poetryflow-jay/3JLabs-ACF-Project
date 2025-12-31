<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [v3.8.0 대규모 업그레이드] '사용자 정의' 패널 통합 관리자
 * - [신규] 동적 테마별 섹션 생성: 설치된 테마를 자동 감지하여 각 테마 설정 메뉴 하위에 부가 옵션 섹션 생성
 * - [신규] 팔레트 최소 3개 보장: 브랜드, 시스템, 대안 팔레트 최소 3개 노출 (임시 활성 시 4개)
 * - [신규] 툴팁 시스템: 컬러/폰트/버튼 설정에 적용 위치 정보를 알려주는 툴팁 추가
 * - [신규] 테마 메타데이터 통합: 각 테마/플러그인의 설정값이 어디에 적용되는지 설명 제공
 */
final class JJ_Customizer_Manager {

    private static $instance = null;
    private $options_key = '';
    private $theme_metadata = null;
    private $adapters_config = array();

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        // 상수 정의 확인 후 할당
        if ( defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ) {
            $this->options_key = JJ_STYLE_GUIDE_OPTIONS_KEY;
        }
        
        // 테마 메타데이터 로드 (클래스 존재 확인)
        if ( class_exists( 'JJ_Theme_Metadata' ) ) {
            $this->theme_metadata = JJ_Theme_Metadata::instance();
        }
        
        // 어댑터 설정 로드
        if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            $config_file = JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php';
            if ( file_exists( $config_file ) ) {
                $this->adapters_config = include( $config_file );
            }
        }
        
        add_action( 'customize_register', array( $this, 'register_panels_and_sections' ) );
        add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_customizer_scripts' ) );
    }

    /**
     * 초기화 메서드
     * 생성자에서 이미 모든 초기화가 완료되므로 빈 메서드로 유지
     * 다른 클래스들과의 일관성을 위해 추가
     * 
     * @return void
     */
    public function init() {
        // 생성자에서 이미 모든 훅 등록이 완료됨
        // 추가 초기화가 필요한 경우 여기에 추가
    }

    public function register_panels_and_sections( $wp_customize ) {
        // [v3.8.0 신규] 'JJ 스타일 센터' 패널을 최상단에 배치
        $wp_customize->add_panel( 'jj_style_guide_panel', array(
            'title'       => __( 'JJ 스타일 센터', 'jj-style-guide' ),
            'description' => __( '웹사이트 전체 스타일 중앙 관리, 모든 것을 한 번에 편하게 관리하기', 'jj-style-guide' ),
            'priority'    => 1, // 최상단에 배치
            'capability'  => 'manage_options',
        ) );

        // 기본 섹션 추가
        $this->add_colors_section( $wp_customize );
        $this->add_typography_section( $wp_customize );
        $this->add_buttons_section( $wp_customize );
        
        // 동적 테마별 섹션 생성
        $this->add_dynamic_theme_sections( $wp_customize );
    }

    /**
     * 동적 테마별 섹션 생성
     * 설치된 테마를 감지하여 각 테마의 설정 메뉴 하위에 부가 옵션 섹션 생성
     */
    private function add_dynamic_theme_sections( $wp_customize ) {
        $theme = wp_get_theme();
        $parent = $theme->parent();
        $active_theme = $parent ? $parent->get_template() : $theme->get_template();
        $theme_name = $parent ? $parent->get( 'Name' ) : $theme->get( 'Name' );
        $child_name = $parent ? $theme->get( 'Name' ) : '';
        
        // 지원 테마 목록 확인
        $supported_themes = $this->adapters_config['themes'] ?? array();
        
        if ( ! isset( $supported_themes[ $active_theme ] ) ) {
            return;
        }
        
        // 테마별 섹션 생성
        $section_id = 'jj_theme_' . $active_theme . '_section';
        $section_title = sprintf( __( '%s 부가 옵션', 'jj-style-guide' ), $theme_name );
        if ( ! empty( $child_name ) ) {
            $section_title = sprintf( __( '%s 부가 옵션 (%s 차일드 테마)', 'jj-style-guide' ), $theme_name, $child_name );
        }
        
        $wp_customize->add_section( $section_id, array(
            'title' => $section_title,
            'panel' => 'jj_style_guide_panel',
            'priority' => 100, // 기본 섹션 이후
            'description' => sprintf(
                __( '현재 설치된 %s 테마와 연동되는 추가 스타일 옵션입니다. 이 섹션의 설정은 %s 테마의 기본 설정과 함께 작동합니다.', 'jj-style-guide' ),
                $theme_name,
                $theme_name
            ),
        ) );
        
        // 테마별 컨트롤 추가 (예: Kadence의 경우)
        $this->add_theme_specific_controls( $wp_customize, $section_id, $active_theme, $theme_name );
    }

    /**
     * 'Colors' 섹션 (v3.8.0 대규모 개선)
     * - 팔레트 최소 3개 보장: 브랜드, 시스템, 대안
     * - 임시 팔레트 활성 시 4개로 자동 확장
     * - 툴팁 시스템 통합
     */
    private function add_colors_section( $wp_customize ) {
        // 1. 브랜드 팔레트 섹션
        $brand_section_id = 'jj_colors_brand_section';
        $wp_customize->add_section( $brand_section_id, array( 
            'title' => __( '1. 브랜드 팔레트 (Brand)', 'jj-style-guide' ), 
            'panel' => 'jj_style_guide_panel', 
            'priority' => 10 
        ) );
        
        $brand_colors = array(
            'primary_color' => __( 'Primary Color', 'jj-style-guide' ),
            'primary_color_hover' => __( 'Primary Color (Hover)', 'jj-style-guide' ),
            'secondary_color' => __( 'Secondary Color', 'jj-style-guide' ),
            'secondary_color_hover' => __( 'Secondary Color (Hover)', 'jj-style-guide' ),
        );
        
        $priority = 10;
        foreach ( $brand_colors as $key => $label ) {
            $setting_id = $this->options_key . '[palettes][brand][' . $key . ']';
            $wp_customize->add_setting( $setting_id, array( 
                'default' => '', 
                'type' => 'option', 
                'transport' => 'refresh', 
                'sanitize_callback' => 'sanitize_hex_color' 
            ) );
            
            // 툴팁 정보 가져오기
            $metadata = $this->theme_metadata->get_metadata( 'palettes', 'brand', $key );
            $description = $metadata ? $this->theme_metadata->get_combined_description( 'palettes', 'brand', $key ) : '';
            
            $control_args = array(
                'label' => $label,
                'section' => $brand_section_id,
                'settings' => $setting_id,
                'priority' => $priority,
                'description' => $description,
            );
            
            $control = new \WP_Customize_Color_Control( $wp_customize, $setting_id, $control_args );
            $control->json['tooltip'] = $description; // 툴팁용 데이터 추가
            $wp_customize->add_control( $control );
            
            $priority += 10;
        }
        
        // 2. 시스템 팔레트 섹션 (최소 3개 보장)
        $system_section_id = 'jj_colors_system_section';
        $wp_customize->add_section( $system_section_id, array( 
            'title' => __( '2. 시스템 팔레트 (System)', 'jj-style-guide' ), 
            'panel' => 'jj_style_guide_panel', 
            'priority' => 20 
        ) );
        
        $system_colors = array(
            'background_color' => __( 'Background Color', 'jj-style-guide' ),
            'text_color' => __( 'Text Color', 'jj-style-guide' ),
            'link_color' => __( 'Link Color', 'jj-style-guide' ),
        );
        
        $priority = 10;
        foreach ( $system_colors as $key => $label ) {
            $setting_id = $this->options_key . '[palettes][system][' . $key . ']';
            $wp_customize->add_setting( $setting_id, array( 
                'default' => '', 
                'type' => 'option', 
                'transport' => 'refresh', 
                'sanitize_callback' => 'sanitize_hex_color' 
            ) );
            
            $metadata = $this->theme_metadata->get_metadata( 'palettes', 'system', $key );
            $description = $metadata ? $this->theme_metadata->get_combined_description( 'palettes', 'system', $key ) : '';
            
            $control_args = array(
                'label' => $label,
                'section' => $system_section_id,
                'settings' => $setting_id,
                'priority' => $priority,
                'description' => $description,
            );
            
            $control = new \WP_Customize_Color_Control( $wp_customize, $setting_id, $control_args );
            $control->json['tooltip'] = $description;
            $wp_customize->add_control( $control );
            
            $priority += 10;
        }
        
        // 3. 대안 팔레트 섹션 (최소 3개 보장)
        $alt_section_id = 'jj_colors_alternative_section';
        $wp_customize->add_section( $alt_section_id, array( 
            'title' => __( '3. 대안 팔레트 (Alternative)', 'jj-style-guide' ), 
            'panel' => 'jj_style_guide_panel', 
            'priority' => 30 
        ) );
        
        $alt_colors = array(
            'primary_color' => __( 'Alternative Primary Color', 'jj-style-guide' ),
            'secondary_color' => __( 'Alternative Secondary Color', 'jj-style-guide' ),
        );
        
        $priority = 10;
        foreach ( $alt_colors as $key => $label ) {
            $setting_id = $this->options_key . '[palettes][alternative][' . $key . ']';
            $wp_customize->add_setting( $setting_id, array( 
                'default' => '', 
                'type' => 'option', 
                'transport' => 'refresh', 
                'sanitize_callback' => 'sanitize_hex_color' 
            ) );
            
            $control_args = array(
                'label' => $label,
                'section' => $alt_section_id,
                'settings' => $setting_id,
                'priority' => $priority,
                'description' => __( '대안 팔레트는 브랜드 팔레트와 다른 색상 조합을 시도할 때 사용할 수 있습니다.', 'jj-style-guide' ),
            );
            
            $control = new \WP_Customize_Color_Control( $wp_customize, $setting_id, $control_args );
            $wp_customize->add_control( $control );
            
            $priority += 10;
        }
        
        // 4. 임시 팔레트 섹션 (활성 시에만 표시, 최소 3개 보장 시 4개로 확장)
        $temp_options = get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, array() );
        if ( ! empty( $temp_options['palettes'] ) && ! empty( $temp_options['palettes']['brand'] ) ) {
            $temp_section_id = 'jj_colors_temp_section';
            $wp_customize->add_section( $temp_section_id, array( 
                'title' => __( '4. 임시 팔레트 (Temporary)', 'jj-style-guide' ), 
                'panel' => 'jj_style_guide_panel', 
                'priority' => 40,
                'description' => __( '임시 팔레트는 실험적인 색상 조합을 테스트할 때 사용합니다. 적용 후 "스타일 저장"을 눌러야 최종 반영됩니다.', 'jj-style-guide' ),
            ) );
            
            $temp_colors = array(
                'primary_color' => __( 'Temporary Primary Color', 'jj-style-guide' ),
                'primary_color_hover' => __( 'Temporary Primary Color (Hover)', 'jj-style-guide' ),
            );
            
            $priority = 10;
            foreach ( $temp_colors as $key => $label ) {
                $setting_id = JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY . '[palettes][brand][' . $key . ']';
                $wp_customize->add_setting( $setting_id, array( 
                    'default' => '', 
                    'type' => 'option', 
                    'transport' => 'refresh', 
                    'sanitize_callback' => 'sanitize_hex_color' 
                ) );
                
                $control = new \WP_Customize_Color_Control( $wp_customize, $setting_id, array(
                    'label' => $label,
                    'section' => $temp_section_id,
                    'settings' => $setting_id,
                    'priority' => $priority,
                ) );
                $wp_customize->add_control( $control );
                
                $priority += 10;
            }
        }
    }

    /**
     * 'Typography' 섹션 (v3.8.0 개선: 툴팁 추가)
     */
    private function add_typography_section( $wp_customize ) {
        $section_id = 'jj_typography_section';
        $wp_customize->add_section( $section_id, array( 
            'title' => __( '2. 전역 타이포그래피 (Global)', 'jj-style-guide' ), 
            'panel' => 'jj_style_guide_panel', 
            'priority' => 20 
        ) );
        
        $tags = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p');
        $font_weights = array('100'=>'100', '200'=>'200', '300'=>'300', '400'=>'400', '500'=>'500', '600'=>'600', '700'=>'700', '800'=>'800', '900'=>'900');
        $font_styles = array('normal'=>'Normal', 'italic'=>'Italic');
        $text_transforms = array('' => 'Default', 'none' => 'None', 'uppercase' => 'UPPERCASE', 'capitalize' => 'Capitalize', 'lowercase' => 'lowercase');
        
        $priority = 10;
        foreach ( $tags as $tag ) {
            $setting_prefix = $this->options_key . '[typography][' . $tag . ']';
            $label_prefix = strtoupper($tag) . ' ';

            // Font Family
            $setting_id = $setting_prefix . '[font_family]';
            $wp_customize->add_setting( $setting_id, array( 'type' => 'option', 'sanitize_callback' => 'sanitize_text_field' ) );
            $metadata = $this->theme_metadata->get_metadata( 'typography', $tag, 'font_family' );
            $description = $metadata ? $this->theme_metadata->get_combined_description( 'typography', $tag, 'font_family' ) : '';
            $control_args = array(
                'label' => $label_prefix . __( 'Font Family', 'jj-style-guide' ),
                'section' => $section_id,
                'priority' => $priority++,
                'type' => 'text',
                'description' => $description,
            );
            $control = $wp_customize->add_control( $setting_id, $control_args );
            if ( $control instanceof \WP_Customize_Control ) {
                $control->json['tooltip'] = $description;
            }
            
            // Font Weight
            $setting_id = $setting_prefix . '[font_weight]';
            $wp_customize->add_setting( $setting_id, array( 'type' => 'option', 'default' => '400', 'sanitize_callback' => 'sanitize_key' ) );
            $wp_customize->add_control( $setting_id, array( 'label' => $label_prefix . __( 'Font Weight', 'jj-style-guide' ), 'section' => $section_id, 'priority' => $priority++, 'type' => 'select', 'choices' => $font_weights ) );

            // Font Style
            $setting_id = $setting_prefix . '[font_style]';
            $wp_customize->add_setting( $setting_id, array( 'type' => 'option', 'default' => 'normal', 'sanitize_callback' => 'sanitize_key' ) );
            $wp_customize->add_control( $setting_id, array( 'label' => $label_prefix . __( 'Font Style', 'jj-style-guide' ), 'section' => $section_id, 'priority' => $priority++, 'type' => 'select', 'choices' => $font_styles ) );

            // Line Height
            $setting_id = $setting_prefix . '[line_height]';
            $wp_customize->add_setting( $setting_id, array( 'type' => 'option', 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
            $wp_customize->add_control( $setting_id, array( 'label' => $label_prefix . __( 'Line Height (em)', 'jj-style-guide' ), 'section' => $section_id, 'priority' => $priority++, 'type' => 'number', 'input_attrs' => array( 'step' => '0.1', 'placeholder' => '예: 1.5' ) ) );

            // Letter Spacing
            $setting_id = $setting_prefix . '[letter_spacing]';
            $wp_customize->add_setting( $setting_id, array( 'type' => 'option', 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
            $wp_customize->add_control( $setting_id, array( 'label' => $label_prefix . __( 'Letter Spacing (px)', 'jj-style-guide' ), 'section' => $section_id, 'priority' => $priority++, 'type' => 'number', 'input_attrs' => array( 'step' => '0.1', 'placeholder' => '예: -0.1' ) ) );

            // Text Transform
            $setting_id = $setting_prefix . '[text_transform]';
            $wp_customize->add_setting( $setting_id, array( 'type' => 'option', 'default' => '', 'sanitize_callback' => 'sanitize_key' ) );
            $wp_customize->add_control( $setting_id, array( 'label' => $label_prefix . __( 'Text Transform', 'jj-style-guide' ), 'section' => $section_id, 'priority' => $priority++, 'type' => 'select', 'choices' => $text_transforms ) );

            // Font Size
            $setting_id = $setting_prefix . '[font_size][desktop]';
            $wp_customize->add_setting( $setting_id, array( 'type' => 'option', 'sanitize_callback' => 'absint' ) );
            $wp_customize->add_control( $setting_id, array( 'label' => $label_prefix . __( 'Font Size (Desktop)', 'jj-style-guide' ), 'section' => $section_id, 'priority' => $priority++, 'type' => 'number', 'input_attrs' => array( 'placeholder' => '예: 32' ) ) );
            
            $priority += 5;
        }
    }

    /**
     * 'Buttons' 섹션 (v3.8.0 개선: 툴팁 추가)
     */
    private function add_buttons_section( $wp_customize ) {
        $section_id = 'jj_buttons_section';
        $wp_customize->add_section( $section_id, array( 
            'title' => __( '3. 전역 버튼 (Global Buttons)', 'jj-style-guide' ), 
            'panel' => 'jj_style_guide_panel', 
            'priority' => 30 
        ) );
        
        $button_colors = array(
            'background_color' => __( 'Primary - 배경 색상', 'jj-style-guide' ),
            'background_color_hover' => __( 'Primary - 배경 색상 (Hover)', 'jj-style-guide' ),
            'text_color' => __( 'Primary - 텍스트 색상', 'jj-style-guide' ),
            'text_color_hover' => __( 'Primary - 텍스트 색상 (Hover)', 'jj-style-guide' ),
            'border_color' => __( 'Primary - 테두리 색상', 'jj-style-guide' ),
            'border_color_hover' => __( 'Primary - 테두리 색상 (Hover)', 'jj-style-guide' ),
        );
        
        $priority = 10;
        foreach ( $button_colors as $key => $label ) {
            $setting_id = $this->options_key . '[buttons][primary][' . $key . ']';
            $wp_customize->add_setting( $setting_id, array( 
                'default' => '', 
                'type' => 'option', 
                'transport' => 'refresh', 
                'sanitize_callback' => 'sanitize_hex_color' 
            ) );
            
            // 툴팁 정보 가져오기
            $metadata = $this->theme_metadata->get_metadata( 'buttons', 'primary', $key );
            $description = $metadata ? $this->theme_metadata->get_combined_description( 'buttons', 'primary', $key ) : '';
            
            $control_args = array(
                'label' => $label,
                'section' => $section_id,
                'settings' => $setting_id,
                'priority' => $priority,
                'description' => $description,
            );
            
            $control = new \WP_Customize_Color_Control( $wp_customize, $setting_id, $control_args );
            $control->json['tooltip'] = $description;
            $wp_customize->add_control( $control );
            
            $priority += 10;
        }
        $priority += 10; 
        $setting_id = $this->options_key . '[buttons][primary][border_radius]';
        $wp_customize->add_setting( $setting_id, array( 'type' => 'option', 'default' => '4', 'sanitize_callback' => 'absint' ) );
        $wp_customize->add_control( $setting_id, array( 'label' => __( 'Primary - Border Radius (px)', 'jj-style-guide' ), 'section' => $section_id, 'priority' => $priority++, 'type' => 'number', 'input_attrs' => array( 'min' => '0', 'step' => '1' ) ) );
        $padding_keys = array('top' => 'Top', 'right' => 'Right', 'bottom' => 'Bottom', 'left' => 'Left');
        foreach($padding_keys as $key => $label) {
            $setting_id = $this->options_key . '[buttons][primary][padding][' . $key . ']';
            $wp_customize->add_setting( $setting_id, array( 'type' => 'option', 'default' => ($key == 'top' || $key == 'bottom' ? '12' : '24'), 'sanitize_callback' => 'absint' ) );
            $wp_customize->add_control( $setting_id, array( 'label' => __( 'Primary - Padding ', 'jj-style-guide' ) . $label . ' (px)', 'section' => $section_id, 'priority' => $priority++, 'type' => 'number', 'input_attrs' => array( 'min' => '0', 'step' => '1' ) ) );
        }
        $shadow_keys = array(
            'color'  => array('label' => 'Primary - Shadow Color', 'default' => 'rgba(0,0,0,0.1)', 'type' => 'color'),
            'x'      => array('label' => 'Primary - Shadow X (px)', 'default' => '0', 'type' => 'number'),
            'y'      => array('label' => 'Primary - Shadow Y (px)', 'default' => '10', 'type' => 'number'),
            'blur'   => array('label' => 'Primary - Shadow Blur (px)', 'default' => '15', 'type' => 'number'),
            'spread' => array('label' => 'Primary - Shadow Spread (px)', 'default' => '-5', 'type' => 'number'),
        );
        foreach($shadow_keys as $key => $props) {
            $setting_id = $this->options_key . '[buttons][primary][shadow][' . $key . ']';
            $sanitize_callback = ( $props['type'] == 'color' ) ? 'sanitize_hex_color' : 'sanitize_text_field'; 
            $wp_customize->add_setting( $setting_id, array( 'type' => 'option', 'default' => $props['default'], 'transport' => 'refresh', 'sanitize_callback' => $sanitize_callback ) );
            if ( $props['type'] == 'color' ) {
                 $wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, $setting_id, array( 'label' => $props['label'], 'section' => $section_id, 'settings' => $setting_id, 'priority' => $priority ) ) );
            } else {
                 $wp_customize->add_control( $setting_id, array( 'label' => $props['label'], 'section' => $section_id, 'priority' => $priority, 'type' => 'text' ) );
            }
            $priority++;
        }
    }
    
    /**
     * 테마별 특정 컨트롤 추가
     * 각 테마 어댑터에서 제공하는 설정값을 Customizer에 추가
     */
    private function add_theme_specific_controls( $wp_customize, $section_id, $theme_slug, $theme_name ) {
        // Kadence 테마 특별 처리
        if ( 'kadence' === $theme_slug ) {
            $setting_id = $this->options_key . '[palettes][brand][primary_color]';
            $wp_customize->add_setting( $setting_id, array( 
                'type' => 'option', 
                'transport' => 'refresh', 
                'sanitize_callback' => 'sanitize_hex_color' 
            ) );
            
            $metadata = $this->theme_metadata->get_metadata( 'palettes', 'brand', 'primary_color', 'kadence' );
            $description = $metadata ? $this->theme_metadata->get_combined_description( 'palettes', 'brand', 'primary_color' ) : '';
            $description .= ' ' . sprintf( __( '%s 테마의 전역 팔레트 1과 동기화됩니다.', 'jj-style-guide' ), $theme_name );
            
            $control = new \WP_Customize_Color_Control( $wp_customize, $setting_id . '_kadence_sync', array(
                'label' => sprintf( __( '%s 팔레트 1 (Brand Primary와 동기화)', 'jj-style-guide' ), $theme_name ),
                'section' => $section_id,
                'settings' => $setting_id,
                'priority' => 10,
                'description' => $description,
            ) );
            $control->json['tooltip'] = $description;
            $wp_customize->add_control( $control );

            $setting_id = $this->options_key . '[palettes][brand][primary_color_hover]';
            $wp_customize->add_setting( $setting_id, array( 
                'type' => 'option', 
                'transport' => 'refresh', 
                'sanitize_callback' => 'sanitize_hex_color' 
            ) );
            
            $description = sprintf( __( '%s 테마의 전역 팔레트 2와 동기화됩니다.', 'jj-style-guide' ), $theme_name );
            $control = new \WP_Customize_Color_Control( $wp_customize, $setting_id . '_kadence_sync', array(
                'label' => sprintf( __( '%s 팔레트 2 (Brand Primary Hover와 동기화)', 'jj-style-guide' ), $theme_name ),
                'section' => $section_id,
                'settings' => $setting_id,
                'priority' => 20,
                'description' => $description,
            ) );
            $control->json['tooltip'] = $description;
            $wp_customize->add_control( $control );
        }
        
        // 다른 테마들의 경우도 여기에 추가 가능
        // 예: Astra, GeneratePress 등
    }

    /**
     * Customizer 스크립트 및 스타일 로드
     * 툴팁 기능을 위한 JavaScript/CSS 추가
     */
    public function enqueue_customizer_scripts() {
        wp_enqueue_script(
            'jj-customizer-tooltips',
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-customizer-tooltips.js',
            array( 'jquery', 'customize-controls' ),
            JJ_STYLE_GUIDE_VERSION,
            true
        );
        
        wp_enqueue_style(
            'jj-customizer-tooltips',
            JJ_STYLE_GUIDE_URL . 'assets/css/jj-customizer-tooltips.css',
            array(),
            JJ_STYLE_GUIDE_VERSION
        );
        
        // 테마 메타데이터를 JavaScript로 전달
        $theme = wp_get_theme();
        $parent = $theme->parent();
        $active_theme = $parent ? $parent->get_template() : $theme->get_template();
        
        wp_localize_script( 'jj-customizer-tooltips', 'jjCustomizerData', array(
            'activeTheme' => $active_theme,
            'themeName' => $parent ? $parent->get( 'Name' ) : $theme->get( 'Name' ),
        ) );
    }
}

// 즉시 실행
JJ_Customizer_Manager::instance();