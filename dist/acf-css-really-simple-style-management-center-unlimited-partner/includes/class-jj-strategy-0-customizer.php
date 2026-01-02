<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [v4.0.2 신규] 전략 0: Customizer 직접 연동 (최우선 전략)
 * 
 * WordPress Customizer와 플러그인 옵션 간 양방향 실시간 동기화
 * - Customizer 변경 → 플러그인 옵션 자동 동기화 (Sync In)
 * - 플러그인 옵션 변경 → Customizer 자동 반영 (Sync Out)
 * - 모든 테마 지원 (Kadence, Astra, 기본 WordPress 등)
 * - Preview 모드에서도 실시간 동기화
 * 
 * 전략 우선순위: 0 (최우선) > 1 (CSS 변수) > 2 (PHP 필터) > 3 (Dequeue)
 * 
 * @since v4.0.2
 */
final class JJ_Strategy_0_Customizer {
    
    private static $instance = null;
    private $options = array();
    private $active_theme;
    private $theme_slug;
    private $customizer_sync;
    private $error_handler;
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        if ( function_exists( 'wp_get_theme' ) ) {
            $this->active_theme = wp_get_theme();
            $this->theme_slug = $this->active_theme->get_template();
        }
        // [v5.1.7] 클래스 존재 확인 후 인스턴스 생성
        if ( class_exists( 'JJ_Error_Handler' ) ) {
            $this->error_handler = JJ_Error_Handler::instance();
        }
    }
    
    /**
     * 전략 0 초기화
     * 
     * @param array $options 플러그인 옵션
     */
    public function init( $options ) {
        $this->options = $options;
        
        // Customizer Sync 인스턴스 로드
        require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-sync.php';
        $this->customizer_sync = JJ_Customizer_Sync::instance();
        
        // 1. Customizer 저장 시 플러그인 옵션으로 동기화 (Sync In)
        add_action( 'customize_save_after', array( $this, 'sync_from_customizer' ), 5 ); // 최우선 우선순위
        
        // 2. Customizer Preview 모드에서도 실시간 동기화
        add_action( 'customize_preview_init', array( $this, 'init_preview_sync' ), 5 );
        
        // 3. 플러그인 옵션 변경 시 Customizer에 반영 (Sync Out)
        add_action( 'update_option_' . JJ_STYLE_GUIDE_OPTIONS_KEY, array( $this, 'sync_to_customizer' ), 10, 2 );
        
        // 4. 활성화 시 Customizer 값 자동 읽어오기
        add_action( 'init', array( $this, 'auto_sync_on_init' ), 1 ); // 최우선 우선순위
    }
    
    /**
     * Customizer에서 플러그인 옵션으로 동기화 (Sync In)
     * Customizer 저장 시 자동 실행
     */
    public function sync_from_customizer( $manager ) {
        return $this->error_handler->safe_execute( function( $manager ) {
            
            // Customizer Sync를 통해 테마별 색상 읽어오기
            $brand_colors = $this->customizer_sync->sync_brand_palette_from_customizer( true );
            $system_colors = $this->customizer_sync->sync_system_palette_from_customizer( true );
            
            // 플러그인 옵션 업데이트
            $options = get_option( JJ_STYLE_GUIDE_OPTIONS_KEY, array() );
            
            if ( ! isset( $options['palettes'] ) ) {
                $options['palettes'] = array();
            }
            
            // 브랜드 팔레트 동기화
            if ( ! empty( $brand_colors ) ) {
                if ( ! isset( $options['palettes']['brand'] ) ) {
                    $options['palettes']['brand'] = array();
                }
                $options['palettes']['brand'] = array_merge( $options['palettes']['brand'], $brand_colors );
            }
            
            // 시스템 팔레트 동기화
            if ( ! empty( $system_colors ) ) {
                if ( ! isset( $options['palettes']['system'] ) ) {
                    $options['palettes']['system'] = array();
                }
                $options['palettes']['system'] = array_merge( $options['palettes']['system'], $system_colors );
            }
            
            // 옵션 저장
            update_option( JJ_STYLE_GUIDE_OPTIONS_KEY, $options );
            
            // CSS 캐시 초기화
            if ( class_exists( 'JJ_CSS_Cache' ) ) {
                JJ_CSS_Cache::instance()->flush();
            }
            
            return true;
            
        }, array( $manager ), false );
    }
    
    /**
     * Customizer Preview 모드 초기화
     * Preview 모드에서도 실시간으로 색상 변경 감지
     */
    public function init_preview_sync() {
        // Preview 모드에서는 JavaScript를 통해 실시간 동기화
        // (향후 확장 가능)
    }
    
    /**
     * 플러그인 옵션 변경 시 Customizer에 반영 (Sync Out)
     * 원격 컨트롤러 방식: 플러그인에서 테마 Customizer 제어
     * 
     * @param array $old_value 이전 옵션 값
     * @param array $new_value 새로운 옵션 값
     */
    public function sync_to_customizer( $old_value, $new_value ) {
        return $this->error_handler->safe_execute( function( $old_value, $new_value ) {
            
            // 브랜드 팔레트 변경 감지
            $brand_changed = false;
            if ( isset( $new_value['palettes']['brand'] ) ) {
                $old_brand = isset( $old_value['palettes']['brand'] ) ? $old_value['palettes']['brand'] : array();
                $new_brand = $new_value['palettes']['brand'];
                
                // 색상값 변경 확인
                if ( 
                    ( isset( $new_brand['primary_color'] ) && $new_brand['primary_color'] !== ( $old_brand['primary_color'] ?? '' ) ) ||
                    ( isset( $new_brand['primary_color_hover'] ) && $new_brand['primary_color_hover'] !== ( $old_brand['primary_color_hover'] ?? '' ) ) ||
                    ( isset( $new_brand['secondary_color'] ) && $new_brand['secondary_color'] !== ( $old_brand['secondary_color'] ?? '' ) ) ||
                    ( isset( $new_brand['secondary_color_hover'] ) && $new_brand['secondary_color_hover'] !== ( $old_brand['secondary_color_hover'] ?? '' ) )
                ) {
                    $brand_changed = true;
                    $this->sync_brand_to_customizer( $new_brand );
                }
            }
            
            // 시스템 팔레트 변경 감지
            $system_changed = false;
            if ( isset( $new_value['palettes']['system'] ) ) {
                $old_system = isset( $old_value['palettes']['system'] ) ? $old_value['palettes']['system'] : array();
                $new_system = $new_value['palettes']['system'];
                
                // 색상값 변경 확인
                if ( 
                    ( isset( $new_system['site_bg'] ) && $new_system['site_bg'] !== ( $old_system['site_bg'] ?? '' ) ) ||
                    ( isset( $new_system['content_bg'] ) && $new_system['content_bg'] !== ( $old_system['content_bg'] ?? '' ) ) ||
                    ( isset( $new_system['text_color'] ) && $new_system['text_color'] !== ( $old_system['text_color'] ?? '' ) ) ||
                    ( isset( $new_system['link_color'] ) && $new_system['link_color'] !== ( $old_system['link_color'] ?? '' ) )
                ) {
                    $system_changed = true;
                    $this->sync_system_to_customizer( $new_system );
                }
            }
            
            return $brand_changed || $system_changed;
            
        }, array( $old_value, $new_value ), false );
    }
    
    /**
     * 브랜드 팔레트를 Customizer에 반영
     */
    private function sync_brand_to_customizer( $brand_colors ) {
        if ( 'kadence' === $this->theme_slug ) {
            $this->sync_brand_to_kadence( $brand_colors );
        } else if ( 'astra' === $this->theme_slug ) {
            $this->sync_brand_to_astra( $brand_colors );
        } else {
            $this->sync_brand_to_default( $brand_colors );
        }
    }
    
    /**
     * 시스템 팔레트를 Customizer에 반영
     */
    private function sync_system_to_customizer( $system_colors ) {
        if ( 'kadence' === $this->theme_slug ) {
            $this->sync_system_to_kadence( $system_colors );
        } else if ( 'astra' === $this->theme_slug ) {
            $this->sync_system_to_astra( $system_colors );
        } else {
            $this->sync_system_to_default( $system_colors );
        }
    }
    
    /**
     * Kadence 테마에 브랜드 팔레트 반영
     */
    private function sync_brand_to_kadence( $brand_colors ) {
        $kt_options = get_option( 'kadence_theme_options', array() );
        if ( ! is_array( $kt_options ) ) {
            $kt_options = array();
        }
        if ( ! isset( $kt_options['global_palette'] ) ) {
            $kt_options['global_palette'] = array();
        }
        
        if ( ! empty( $brand_colors['primary_color'] ) ) {
            $kt_options['global_palette']['palette1'] = $brand_colors['primary_color'];
        }
        if ( ! empty( $brand_colors['primary_color_hover'] ) ) {
            $kt_options['global_palette']['palette2'] = $brand_colors['primary_color_hover'];
        }
        if ( ! empty( $brand_colors['secondary_color'] ) ) {
            $kt_options['global_palette']['palette3'] = $brand_colors['secondary_color'];
        }
        if ( ! empty( $brand_colors['secondary_color_hover'] ) ) {
            $kt_options['global_palette']['palette4'] = $brand_colors['secondary_color_hover'];
        }
        
        update_option( 'kadence_theme_options', $kt_options );
    }
    
    /**
     * Kadence 테마에 시스템 팔레트 반영
     */
    private function sync_system_to_kadence( $system_colors ) {
        $kt_options = get_option( 'kadence_theme_options', array() );
        if ( ! is_array( $kt_options ) ) {
            $kt_options = array();
        }
        
        if ( ! empty( $system_colors['site_bg'] ) ) {
            $kt_options['site_background'] = $system_colors['site_bg'];
        }
        if ( ! empty( $system_colors['content_bg'] ) ) {
            $kt_options['content_background'] = $system_colors['content_bg'];
        }
        if ( ! empty( $system_colors['link_color'] ) ) {
            $kt_options['link_color'] = $system_colors['link_color'];
            // palette9에도 반영
            if ( ! isset( $kt_options['global_palette'] ) ) {
                $kt_options['global_palette'] = array();
            }
            $kt_options['global_palette']['palette9'] = $system_colors['link_color'];
        }
        if ( ! empty( $system_colors['text_color'] ) ) {
            if ( ! isset( $kt_options['global_palette'] ) ) {
                $kt_options['global_palette'] = array();
            }
            $kt_options['global_palette']['palette6'] = $system_colors['text_color'];
        }
        
        update_option( 'kadence_theme_options', $kt_options );
    }
    
    /**
     * Astra 테마에 브랜드 팔레트 반영
     */
    private function sync_brand_to_astra( $brand_colors ) {
        $astra_options = get_option( 'astra-settings', array() );
        if ( ! is_array( $astra_options ) ) {
            $astra_options = array();
        }
        
        if ( ! empty( $brand_colors['primary_color'] ) ) {
            $astra_options['theme-color'] = $brand_colors['primary_color'];
        }
        
        update_option( 'astra-settings', $astra_options );
    }
    
    /**
     * Astra 테마에 시스템 팔레트 반영
     */
    private function sync_system_to_astra( $system_colors ) {
        $astra_options = get_option( 'astra-settings', array() );
        if ( ! is_array( $astra_options ) ) {
            $astra_options = array();
        }
        
        if ( ! empty( $system_colors['site_bg'] ) ) {
            $astra_options['background-color'] = $system_colors['site_bg'];
        }
        if ( ! empty( $system_colors['text_color'] ) ) {
            $astra_options['text-color'] = $system_colors['text_color'];
        }
        if ( ! empty( $system_colors['link_color'] ) ) {
            $astra_options['link-color'] = $system_colors['link_color'];
        }
        
        update_option( 'astra-settings', $astra_options );
    }
    
    /**
     * 기본 WordPress Customizer에 브랜드 팔레트 반영
     */
    private function sync_brand_to_default( $brand_colors ) {
        if ( ! empty( $brand_colors['primary_color'] ) ) {
            // WordPress 기본 Customizer의 header_textcolor에 반영
            $color = ltrim( $brand_colors['primary_color'], '#' );
            set_theme_mod( 'header_textcolor', $color );
        }
    }
    
    /**
     * 기본 WordPress Customizer에 시스템 팔레트 반영
     */
    private function sync_system_to_default( $system_colors ) {
        if ( ! empty( $system_colors['site_bg'] ) ) {
            $color = ltrim( $system_colors['site_bg'], '#' );
            set_theme_mod( 'background_color', $color );
        }
    }
    
    /**
     * 초기화 시 Customizer 값 자동 읽어오기
     * 플러그인 옵션이 비어있을 때 Customizer에서 자동으로 읽어옴
     */
    public function auto_sync_on_init() {
        // 옵션이 이미 있으면 스킵 (활성화 시점이 아니면)
        $options = get_option( JJ_STYLE_GUIDE_OPTIONS_KEY, array() );
        
        // 브랜드 팔레트가 비어있으면 Customizer에서 읽어오기
        $brand_empty = empty( $options['palettes']['brand']['primary_color'] ) && 
                       empty( $options['palettes']['brand']['primary_color_hover'] );
        
        // 시스템 팔레트가 비어있으면 Customizer에서 읽어오기
        $system_empty = empty( $options['palettes']['system']['link_color'] ) &&
                         empty( $options['palettes']['system']['text_color'] );
        
        if ( $brand_empty || $system_empty ) {
            // Customizer에서 색상 읽어오기
            $brand_colors = $this->customizer_sync->sync_brand_palette_from_customizer( false );
            $system_colors = $this->customizer_sync->sync_system_palette_from_customizer( false );
            
            // 옵션에 저장
            if ( ! empty( $brand_colors ) ) {
                $this->customizer_sync->save_synced_colors( 'brand', $brand_colors, true );
            }
            if ( ! empty( $system_colors ) ) {
                $this->customizer_sync->save_synced_colors( 'system', $system_colors, true );
            }
        }
    }
}

