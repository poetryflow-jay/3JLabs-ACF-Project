<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Customizer 색상 동기화 클래스
 * 
 * WordPress Customizer에서 색상 값을 가져와서 플러그인 옵션으로 동기화합니다.
 * - 플러그인 활성화 시 자동 동기화
 * - 수동 새로고침 버튼을 통한 동기화
 * 
 * @since v3.8.0
 */
class JJ_Customizer_Sync {
    
    private static $instance = null;
    private $active_theme;
    private $theme_slug;
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->active_theme = wp_get_theme();
        $this->theme_slug = $this->active_theme->get_template();
    }
    
    /**
     * Customizer에서 브랜드 팔레트 색상 가져오기
     * 
     * @param bool $force_update 기존 값이 있어도 강제 업데이트 여부
     * @return array 동기화된 브랜드 팔레트 배열
     */
    public function sync_brand_palette_from_customizer( $force_update = false ) {
        $synced = array();
        
        // WordPress 기본 Customizer 색상
        $background_color = get_theme_mod( 'background_color', '' );
        $header_textcolor = get_theme_mod( 'header_textcolor', '' );
        
        // 테마별 Customizer 색상 가져오기
        if ( 'kadence' === $this->theme_slug ) {
            $synced = $this->sync_kadence_brand_palette( $force_update );
        } else if ( 'astra' === $this->theme_slug ) {
            $synced = $this->sync_astra_brand_palette( $force_update );
        } else {
            // 기본 WordPress Customizer 색상 사용
            $synced = $this->sync_default_brand_palette( $force_update );
        }
        
        return $synced;
    }
    
    /**
     * Customizer에서 시스템 팔레트 색상 가져오기
     * 
     * @param bool $force_update 기존 값이 있어도 강제 업데이트 여부
     * @return array 동기화된 시스템 팔레트 배열
     */
    public function sync_system_palette_from_customizer( $force_update = false ) {
        $synced = array();
        
        // WordPress 기본 Customizer 색상
        $background_color = get_theme_mod( 'background_color', '' );
        $header_textcolor = get_theme_mod( 'header_textcolor', '' );
        
        // 테마별 Customizer 색상 가져오기
        if ( 'kadence' === $this->theme_slug ) {
            $synced = $this->sync_kadence_system_palette( $force_update );
        } else if ( 'astra' === $this->theme_slug ) {
            $synced = $this->sync_astra_system_palette( $force_update );
        } else {
            // 기본 WordPress Customizer 색상 사용
            $synced = $this->sync_default_system_palette( $force_update );
        }
        
        return $synced;
    }
    
    /**
     * Kadence 테마 브랜드 팔레트 동기화
     */
    private function sync_kadence_brand_palette( $force_update = false ) {
        $kt_options = get_option( 'kadence_theme_options', array() );
        $synced = array();
        
        if ( ! empty( $kt_options['global_palette']['palette1'] ) ) {
            $synced['primary_color'] = $kt_options['global_palette']['palette1'];
        }
        if ( ! empty( $kt_options['global_palette']['palette2'] ) ) {
            $synced['primary_color_hover'] = $kt_options['global_palette']['palette2'];
        }
        if ( ! empty( $kt_options['global_palette']['palette3'] ) ) {
            $synced['secondary_color'] = $kt_options['global_palette']['palette3'];
        }
        if ( ! empty( $kt_options['global_palette']['palette4'] ) ) {
            $synced['secondary_color_hover'] = $kt_options['global_palette']['palette4'];
        }
        
        // Customizer에서 직접 가져오기 (Kadence Customizer API)
        $customizer_colors = $this->get_kadence_customizer_colors();
        if ( ! empty( $customizer_colors['palette_1'] ) && ( $force_update || empty( $synced['primary_color'] ) ) ) {
            $synced['primary_color'] = $customizer_colors['palette_1'];
        }
        if ( ! empty( $customizer_colors['palette_2'] ) && ( $force_update || empty( $synced['primary_color_hover'] ) ) ) {
            $synced['primary_color_hover'] = $customizer_colors['palette_2'];
        }
        
        return $synced;
    }
    
    /**
     * Kadence 테마 시스템 팔레트 동기화
     */
    private function sync_kadence_system_palette( $force_update = false ) {
        $kt_options = get_option( 'kadence_theme_options', array() );
        $synced = array();
        
        // 사이트 배경색
        if ( ! empty( $kt_options['site_background'] ) ) {
            $synced['site_bg'] = $kt_options['site_background'];
        } else if ( ! empty( $kt_options['global_palette']['palette5'] ) ) {
            $synced['site_bg'] = $kt_options['global_palette']['palette5'];
        } else {
            $background_color = get_theme_mod( 'background_color', '' );
            if ( $background_color ) {
                $synced['site_bg'] = '#' . $background_color;
            }
        }
        
        // 콘텐츠 배경색
        if ( ! empty( $kt_options['content_background'] ) ) {
            $synced['content_bg'] = $kt_options['content_background'];
        } else if ( ! empty( $synced['site_bg'] ) ) {
            $synced['content_bg'] = $synced['site_bg']; // Fallback
        }
        
        // 텍스트 색상
        if ( ! empty( $kt_options['global_palette']['palette6'] ) ) {
            $synced['text_color'] = $kt_options['global_palette']['palette6'];
        }
        
        // 링크 색상
        if ( ! empty( $kt_options['link_color'] ) ) {
            $synced['link_color'] = $kt_options['link_color'];
        } else if ( ! empty( $kt_options['global_palette']['palette9'] ) ) {
            $synced['link_color'] = $kt_options['global_palette']['palette9'];
        }
        
        // Customizer에서 직접 가져오기
        $customizer_colors = $this->get_kadence_customizer_colors();
        if ( ! empty( $customizer_colors['site_background'] ) && ( $force_update || empty( $synced['site_bg'] ) ) ) {
            $synced['site_bg'] = $customizer_colors['site_background'];
        }
        if ( ! empty( $customizer_colors['content_background'] ) && ( $force_update || empty( $synced['content_bg'] ) ) ) {
            $synced['content_bg'] = $customizer_colors['content_background'];
        }
        if ( ! empty( $customizer_colors['text_color'] ) && ( $force_update || empty( $synced['text_color'] ) ) ) {
            $synced['text_color'] = $customizer_colors['text_color'];
        }
        if ( ! empty( $customizer_colors['link_color'] ) && ( $force_update || empty( $synced['link_color'] ) ) ) {
            $synced['link_color'] = $customizer_colors['link_color'];
        }
        
        return $synced;
    }
    
    /**
     * Kadence Customizer 색상 직접 가져오기
     */
    private function get_kadence_customizer_colors() {
        $colors = array();
        
        // Kadence는 Customizer에서 직접 색상을 저장하는 방식
        // get_theme_mod를 통해 접근 가능한 경우
        $kt_customizer = get_theme_mod( 'kadence_theme_options', array() );
        
        if ( is_array( $kt_customizer ) && ! empty( $kt_customizer ) ) {
            if ( ! empty( $kt_customizer['global_palette'] ) ) {
                $palette = $kt_customizer['global_palette'];
                $colors['palette_1'] = $palette['palette1'] ?? '';
                $colors['palette_2'] = $palette['palette2'] ?? '';
                $colors['site_background'] = $palette['palette5'] ?? '';
                $colors['text_color'] = $palette['palette6'] ?? '';
                $colors['link_color'] = $palette['palette9'] ?? '';
            }
        }
        
        // get_option으로도 시도
        $kt_options = get_option( 'kadence_theme_options', array() );
        if ( ! empty( $kt_options['global_palette'] ) ) {
            $palette = $kt_options['global_palette'];
            if ( empty( $colors['palette_1'] ) && ! empty( $palette['palette1'] ) ) {
                $colors['palette_1'] = $palette['palette1'];
            }
            if ( empty( $colors['palette_2'] ) && ! empty( $palette['palette2'] ) ) {
                $colors['palette_2'] = $palette['palette2'];
            }
            if ( empty( $colors['site_background'] ) && ! empty( $palette['palette5'] ) ) {
                $colors['site_background'] = $palette['palette5'];
            }
            if ( empty( $colors['text_color'] ) && ! empty( $palette['palette6'] ) ) {
                $colors['text_color'] = $palette['palette6'];
            }
            if ( empty( $colors['link_color'] ) && ! empty( $palette['palette9'] ) ) {
                $colors['link_color'] = $palette['palette9'];
            }
        }
        
        return $colors;
    }
    
    /**
     * Astra 테마 브랜드 팔레트 동기화
     */
    private function sync_astra_brand_palette( $force_update = false ) {
        $synced = array();
        $astra_options = get_option( 'astra-settings', array() );
        
        if ( ! empty( $astra_options['theme-color'] ) ) {
            $synced['primary_color'] = $astra_options['theme-color'];
        }
        
        return $synced;
    }
    
    /**
     * Astra 테마 시스템 팔레트 동기화
     */
    private function sync_astra_system_palette( $force_update = false ) {
        $synced = array();
        $astra_options = get_option( 'astra-settings', array() );
        
        if ( ! empty( $astra_options['background-color'] ) ) {
            $synced['site_bg'] = $astra_options['background-color'];
        }
        if ( ! empty( $astra_options['text-color'] ) ) {
            $synced['text_color'] = $astra_options['text-color'];
        }
        if ( ! empty( $astra_options['link-color'] ) ) {
            $synced['link_color'] = $astra_options['link-color'];
        }
        
        return $synced;
    }
    
    /**
     * 기본 WordPress Customizer 브랜드 팔레트 동기화
     */
    private function sync_default_brand_palette( $force_update = false ) {
        $synced = array();
        
        // WordPress 기본 Customizer 색상 사용
        $header_textcolor = get_theme_mod( 'header_textcolor', '' );
        if ( $header_textcolor && $header_textcolor !== 'blank' ) {
            $synced['primary_color'] = '#' . $header_textcolor;
        }
        
        return $synced;
    }
    
    /**
     * 기본 WordPress Customizer 시스템 팔레트 동기화
     */
    private function sync_default_system_palette( $force_update = false ) {
        $synced = array();
        
        // WordPress 기본 Customizer 색상 사용
        $background_color = get_theme_mod( 'background_color', '' );
        if ( $background_color ) {
            $synced['site_bg'] = '#' . $background_color;
            $synced['content_bg'] = '#' . $background_color;
        }
        
        return $synced;
    }
    
    /**
     * 플러그인 옵션에 동기화된 색상 저장
     * 
     * @param string $palette_type 'brand' 또는 'system'
     * @param array $colors 동기화된 색상 배열
     * @param bool $merge 기존 값과 병합할지 여부
     */
    public function save_synced_colors( $palette_type, $colors, $merge = true ) {
        if ( ! in_array( $palette_type, array( 'brand', 'system' ) ) ) {
            return false;
        }
        
        $options = get_option( JJ_STYLE_GUIDE_OPTIONS_KEY, array() );
        
        if ( ! isset( $options['palettes'] ) ) {
            $options['palettes'] = array();
        }
        if ( ! isset( $options['palettes'][ $palette_type ] ) ) {
            $options['palettes'][ $palette_type ] = array();
        }
        
        if ( $merge ) {
            $options['palettes'][ $palette_type ] = array_merge( $options['palettes'][ $palette_type ], $colors );
        } else {
            $options['palettes'][ $palette_type ] = $colors;
        }
        
        return update_option( JJ_STYLE_GUIDE_OPTIONS_KEY, $options );
    }
    
    /**
     * AJAX 핸들러: 현재 색상 불러오기
     */
    public static function ajax_load_current_colors() {
        // [Safety] nonce 파라미터 키/액션명 불일치로 인한 실패 방지
        // - style-guide-editor.js: nonce 키를 사용
        // - 일부 AJAX: security 키를 사용
        if ( isset( $_POST['security'] ) ) {
            check_ajax_referer( 'jj_style_guide_nonce', 'security' );
        } else {
            check_ajax_referer( 'jj_style_guide_nonce', 'nonce' );
        }
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
        
        $palette_type = isset( $_POST['palette_type'] ) ? sanitize_text_field( $_POST['palette_type'] ) : 'brand';
        $force_update = isset( $_POST['force_update'] ) ? (bool) $_POST['force_update'] : false;
        
        $sync = self::instance();
        
        if ( 'brand' === $palette_type ) {
            $colors = $sync->sync_brand_palette_from_customizer( $force_update );
        } else if ( 'system' === $palette_type ) {
            $colors = $sync->sync_system_palette_from_customizer( $force_update );
        } else {
            wp_send_json_error( array( 'message' => __( '잘못된 팔레트 타입입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
        
        // 옵션에 저장
        if ( ! empty( $colors ) ) {
            $sync->save_synced_colors( $palette_type, $colors, true );
        }
        
        wp_send_json_success( array(
            'palette_type' => $palette_type,
            'colors' => $colors,
            'message' => __( '색상이 성공적으로 불러와졌습니다.', 'acf-css-really-simple-style-management-center' ),
        ) );
    }
}
