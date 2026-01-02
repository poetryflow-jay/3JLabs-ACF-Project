<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [v2.0.0-alpha2 '재탄생'] '전략 2: PHP 필터' ('외교관')
 * - [원칙 2b] 'v2.0.0' '설계'에 따라, 'Kadence' 테마 '필터'('option_kadence_theme_options')를 '제거'
 * - 'CSS 변수'를 '사용'하는 '전략 1'과의 '충돌'을 '방지'
 * - '스포크'('Shop Kit', 'LD Dashboard', 'UM') '필터'는 'v1.9.0'의 'Sync Out' '기능'을 '유지'
 */
final class JJ_Strategy_2_PHP_Filters {
    private static $instance = null;
    private $options = array();
    private $brand_colors = array();
    
    private $original_kwe_options = array();
    private $original_ldd_options = array();
    private $original_um_options = array();
    // private $original_kt_options = array(); // [v2.0.0 '제거']

    public static function instance() {
        if ( is_null( self::$instance ) ) self::$instance = new self();
        return self::$instance;
    }

    public function init( $options, $kwe_options, $ldd_options, $um_options, $kt_options ) {
        $this->options = $options;
        $this->brand_colors = $options['palettes']['brand'] ?? array();
        
        $this->original_kwe_options = $kwe_options;
        $this->original_ldd_options = $ldd_options;
        $this->original_um_options = $um_options;
        // $this->original_kt_options = $kt_options; // [v2.0.0 '제거']

        add_filter( 'option_kt_woo_extras', array( $this, 'filter_kadence_shop_kit' ), 20 );
        add_filter( 'option_ld_dashboard_design_settings', array( $this, 'filter_ld_dashboard' ), 20 );
        add_filter( 'option_um_options', array( $this, 'filter_ultimate_member' ), 20 );
        add_filter( 'learndash_get_label', array( $this, 'filter_learndash_labels' ), 20, 2 );
        
        // [v2.0.0 '제거'] '전략 1'('CSS 변수 장악')이 'Kadence'를 '담당'하므로 '필터' '제거'
        // add_filter( 'option_kadence_theme_options', array( $this, 'filter_kadence_theme_palette' ), 20 );
    }

    /**
     * Kadence Shop Kit 옵션 '장악' (v1.9.0 '계승')
     */
    public function filter_kadence_shop_kit( $options ) {
        if ( ! is_array( $options ) ) $options = $this->original_kwe_options;
        if ( ! is_array( $options ) ) $options = array();

        if ( ! empty( $this->brand_colors['primary_color'] ) ) {
            $options['product_quickview_button_color'] = $this->brand_colors['primary_color'];
        }
        if ( ! empty( $this->brand_colors['primary_color_hover'] ) ) {
            $options['product_quickview_button_color_hover'] = $this->brand_colors['primary_color_hover'];
        }
        return $options;
    }
    
    /**
     * LearnDash Dashboard 옵션 '장악' (v1.9.0 '계승')
     */
    public function filter_ld_dashboard( $options ) {
        if ( ! is_array( $options ) ) $options = $this->original_ldd_options;
        if ( ! is_array( $options ) ) $options = array();
        
        if ( ! empty( $this->brand_colors['primary_color'] ) ) {
            $options['color'] = $this->brand_colors['primary_color'];
        }
        if ( ! empty( $this->brand_colors['primary_color_hover'] ) ) {
            $options['hover_color'] = $this->brand_colors['primary_color_hover'];
        }
        return $options;
    }

    /**
     * Ultimate Member 옵션 '장악' (v1.9.0 '계승')
     */
    public function filter_ultimate_member( $options ) {
        if ( ! is_array( $options ) ) $options = $this->original_um_options;
        if ( ! is_array( $options ) ) $options = array();

        if ( ! empty( $this->brand_colors['primary_color'] ) ) {
            $options['primary_color'] = $this->brand_colors['primary_color'];
        }
        return $options;
    }

    /**
     * [v2.0.0 '제거'] Kadence 테마 전역 팔레트 '장악' (전략 1로 '이관')
     */
    /*
    public function filter_kadence_theme_palette( $options ) {
        if ( ! is_array( $options ) ) $options = $this->original_kt_options;
        if ( ! is_array( $options ) ) $options = array();
        if ( ! isset( $options['global_palette'] ) ) $options['global_palette'] = array();

        if ( ! empty( $this->brand_colors['primary_color'] ) ) {
            $options['global_palette']['palette1'] = $this->brand_colors['primary_color'];
        }
        if ( ! empty( $this->brand_colors['primary_color_hover'] ) ) {
            $options['global_palette']['palette2'] = $this->brand_colors['primary_color_hover'];
        }
        return $options;
    }
    */
    
    /**
     * LearnDash 레이블 '장악' (v1.9.0 '계승')
     */
    public function filter_learndash_labels( $label, $key ) {
        if ( 'course' === $key && ! empty( $this->options['typography']['labels']['course'] ) ) {
            return $this->options['typography']['labels']['course'];
        }
        return $label;
    }
}