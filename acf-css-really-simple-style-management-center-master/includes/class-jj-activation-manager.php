<?php
if ( ! defined( 'ABSPATH' ) ) exit; // 직접 접근 차단

if ( ! class_exists( 'JJ_Activation_Manager_Master' ) ) {
/**
 * 활성화 로직 클래스 (Master 버전 전용)
 * 
 * 플러그인 활성화 시 테마 설정을 임포트하고 스타일 가이드 설정 페이지를 생성합니다.
 * v2.0.0: 테마별 심층 동기화 (Deep Sync In) 로직 추가
 * v3.1: 시스템 팔레트 동기화 추가
 * 
 * @since v2.0.0-alpha1
 */
class JJ_Activation_Manager_Master {
    
    /**
     * 플러그인 활성화 시 실행되는 메인 함수
     * [v3.8.0] Customizer 색상 자동 동기화 추가
     * [v5.1.6] 오류 처리 강화: 각 단계별 try-catch 추가
     */
    public static function activate() {
        try {
            self::import_native_settings();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Simple Style Guide: 네이티브 설정 임포트 중 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Simple Style Guide: 네이티브 설정 임포트 중 치명적 오류 - ' . $e->getMessage() );
            }
        }
        
        try {
            self::create_cockpit_page();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Simple Style Guide: 조종석 페이지 생성 중 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Simple Style Guide: 조종석 페이지 생성 중 치명적 오류 - ' . $e->getMessage() );
            }
        }
        
        // [v3.8.0 신규] Customizer 색상 자동 동기화 (활성화 시마다 실행)
        try {
            self::sync_customizer_colors_on_activation();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Simple Style Guide: Customizer 색상 동기화 중 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Simple Style Guide: Customizer 색상 동기화 중 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }
    
    /**
     * 플러그인 활성화 시 Customizer 색상 자동 동기화
     * 
     * @since v3.8.0
     */
    private static function sync_customizer_colors_on_activation() {
        // 상수 정의 확인
        if ( ! defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            return;
        }
        
        // 클래스가 이미 로드되어 있지 않은 경우에만 로드
        if ( ! class_exists( 'JJ_Customizer_Sync' ) ) {
            // [v5.3.6] 상대 경로 의존성 제거: master 버전은 자체 includes 폴더만 사용
            // master 버전은 자체 includes 폴더에 class-jj-customizer-sync.php 파일이 있으므로
            // 상대 경로 참조 없이 자체 파일만 사용합니다.
            $sync_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-sync.php';
            
            // 파일 존재 확인
            if ( file_exists( $sync_file ) ) {
                require_once $sync_file;
                // 파일을 로드한 후 클래스가 실제로 존재하는지 확인
                if ( ! class_exists( 'JJ_Customizer_Sync' ) ) {
                    // 클래스가 정의되지 않은 경우 에러 로그 기록
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ Simple Style Guide: class-jj-customizer-sync.php 파일을 로드했지만 클래스가 정의되지 않았습니다.' );
                    }
                    return;
                }
            } else {
                // 파일을 찾을 수 없으면 로그하고 종료
                // master 버전은 자체 includes 폴더에 파일이 있어야 하므로, 없으면 경고만 기록
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'JJ Simple Style Guide: class-jj-customizer-sync.php 파일을 찾을 수 없습니다. 경로: ' . $sync_file );
                }
                return;
            }
        }
        
        // 클래스가 존재하는지 최종 확인
        if ( ! class_exists( 'JJ_Customizer_Sync' ) ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Simple Style Guide: JJ_Customizer_Sync 클래스를 사용할 수 없습니다.' );
            }
            return;
        }
        
        // 인스턴스 생성 시도 (오류 처리)
        try {
            if ( ! method_exists( 'JJ_Customizer_Sync', 'instance' ) ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'JJ Simple Style Guide: JJ_Customizer_Sync::instance() 메서드를 찾을 수 없습니다.' );
                }
                return;
            }
            
            $sync = JJ_Customizer_Sync::instance();
            if ( ! $sync ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'JJ Simple Style Guide: JJ_Customizer_Sync 인스턴스를 생성할 수 없습니다.' );
                }
                return;
            }
            
            // 브랜드 팔레트 동기화 (기존 값이 있어도 업데이트)
            if ( method_exists( $sync, 'sync_brand_palette_from_customizer' ) ) {
                $brand_colors = $sync->sync_brand_palette_from_customizer( false );
                if ( ! empty( $brand_colors ) && method_exists( $sync, 'save_synced_colors' ) ) {
                    $sync->save_synced_colors( 'brand', $brand_colors, true );
                }
            }
            
            // 시스템 팔레트 동기화 (기존 값이 있어도 업데이트)
            if ( method_exists( $sync, 'sync_system_palette_from_customizer' ) ) {
                $system_colors = $sync->sync_system_palette_from_customizer( false );
                if ( ! empty( $system_colors ) && method_exists( $sync, 'save_synced_colors' ) ) {
                    $sync->save_synced_colors( 'system', $system_colors, true );
                }
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Simple Style Guide: Customizer 동기화 실행 중 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Simple Style Guide: Customizer 동기화 실행 중 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    /**
     * 현재 스타일 임포트 (Sync In)
     * v2.0.0-alpha1: 테마별 심층 동기화 로직으로 진화
     */
    private static function import_native_settings() {
        // 상수 정의 확인
        if ( ! defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ) {
            return;
        }
        
        // WordPress 함수 확인
        if ( ! function_exists( 'get_option' ) ) {
            return;
        }
        
        if ( false !== get_option( JJ_STYLE_GUIDE_OPTIONS_KEY ) ) {
            return; 
        }

        // 기본값 정의 (Fallback)
        // [v4.0.2 수정] 하드코딩된 색상값 제거 - 테마 설정을 존중하고 자동으로 테마 색상을 읽어옴
        $default_hub_options = array(
            'palettes' => array(
                'brand' => array(
                    'primary_color' => '', // [v4.0.2] 하드코딩 제거 - 테마에서 자동으로 읽어옴
                    'primary_color_hover' => '', // [v4.0.2] 하드코딩 제거 - 테마에서 자동으로 읽어옴
                    'secondary_color' => '',
                    'secondary_color_hover' => '',
                ),
                'alternative' => array(), 
                'another' => array(),
                'system' => array(
                    'site_bg' => '#FFFFFF',
                    'content_bg' => '#FFFFFF',
                    'text_color' => '#333333',
                    'link_color' => '', // [v4.0.2] 하드코딩 제거 - 테마에서 자동으로 읽어옴
                ),
            ),
            'typography' => array(
                'h1' => array('font_family' => '', 'font_weight' => '700', 'font_style' => 'normal', 'line_height' => '1.3', 'letter_spacing' => '-0.5', 'text_transform' => 'none', 'font_size' => array('desktop' => '40', 'tablet' => '36', 'mobile' => '32')),
                'h2' => array('font_family' => '', 'font_weight' => '700', 'font_style' => 'normal', 'line_height' => '1.4', 'letter_spacing' => '-0.3', 'text_transform' => 'none', 'font_size' => array('desktop' => '32', 'tablet' => '28', 'mobile' => '26')),
                'h3' => array('font_family' => '', 'font_weight' => '700', 'font_style' => 'normal', 'line_height' => '1.5', 'letter_spacing' => '0', 'text_transform' => 'none', 'font_size' => array('desktop' => '26', 'tablet' => '24', 'mobile' => '22')),
                'h4' => array('font_family' => '', 'font_weight' => '600', 'font_style' => 'normal', 'line_height' => '1.5', 'letter_spacing' => '0', 'text_transform' => 'none', 'font_size' => array('desktop' => '22', 'tablet' => '20', 'mobile' => '20')),
                'h5' => array('font_family' => '', 'font_weight' => '600', 'font_style' => 'normal', 'line_height' => '1.6', 'letter_spacing' => '0', 'text_transform' => 'none', 'font_size' => array('desktop' => '18', 'tablet' => '18', 'mobile' => '18')),
                'h6' => array('font_family' => '', 'font_weight' => '600', 'font_style' => 'normal', 'line_height' => '1.6', 'letter_spacing' => '0', 'text_transform' => 'none', 'font_size' => array('desktop' => '16', 'tablet' => '16', 'mobile' => '16')),
                'p'  => array('font_family' => '', 'font_weight' => '400', 'font_style' => 'normal', 'line_height' => '1.7', 'letter_spacing' => '0', 'text_transform' => 'none', 'font_size' => array('desktop' => '16', 'tablet' => '16', 'mobile' => '16')),
            ),
            'buttons' => array(
                'primary' => array(
                    'background_color' => '', // [v4.0.2] 하드코딩 제거 - 테마에서 자동으로 읽어옴
                    'text_color' => '#FFFFFF',
                    'background_color_hover' => '', // [v4.0.2] 하드코딩 제거 - 테마에서 자동으로 읽어옴
                    'text_color_hover' => '#FFFFFF',
                    'border_color' => '', // [v4.0.2] 하드코딩 제거 - 테마에서 자동으로 읽어옴
                    'border_color_hover' => '', // [v4.0.2] 하드코딩 제거 - 테마에서 자동으로 읽어옴
                    'border_radius' => '4',
                    'padding' => array('top' => '12', 'right' => '24', 'bottom' => '12', 'left' => '24'),
                    'shadow' => array('color' => 'rgba(0,0,0,0.1)', 'x' => '0', 'y' => '2', 'blur' => '5', 'spread' => '0'),
                ),
            ),
            'contexts' => array()
        );

        // 테마별 심층 동기화
        $hub_options = $default_hub_options;
        
        // 현재 활성화된 테마 가져오기
        $active_theme = '';
        if ( function_exists( 'get_template' ) ) {
            $active_theme = strtolower( get_template() );
        }

        if ( 'kadence' === $active_theme ) {
            $kt_options = get_option( 'kadence_theme_options', array() );

            // 1. 타이포그래피 동기화
            $hub_options['typography'] = self::sync_kadence_typography( $kt_options, $default_hub_options['typography'] );
            
            // 2. 버튼 동기화
            $hub_options['buttons'] = self::sync_kadence_buttons( $kt_options, $default_hub_options['buttons'] );

            // 3. 브랜드 팔레트 동기화
            $hub_options['palettes']['brand']['primary_color'] = $kt_options['global_palette']['palette1'] ?? $hub_options['buttons']['primary']['background_color'];
            $hub_options['palettes']['brand']['primary_color_hover'] = $kt_options['global_palette']['palette2'] ?? $hub_options['buttons']['primary']['background_color_hover'];
            
            // 4. 시스템 팔레트 동기화 (v3.1)
            if ( ! empty( $default_hub_options['palettes']['system'] ) ) {
                $hub_options['palettes']['system'] = self::sync_kadence_system_palette( $kt_options, $default_hub_options['palettes']['system'] );
            }
        
        } else if ( 'astra' === $active_theme ) {
            // 향후 Astra 테마 심층 동기화 로직 추가 예정
        } else {
            // Fallback: 기본 플러그인 설정에서 색상 가져오기
            $kwe = get_option( 'kt_woo_extras', array() );
            if ( ! empty( $kwe['product_quickview_button_color'] ) ) $hub_options['palettes']['brand']['primary_color'] = $kwe['product_quickview_button_color'];
            if ( ! empty( $kwe['product_quickview_button_color_hover'] ) ) $hub_options['palettes']['brand']['primary_color_hover'] = $kwe['product_quickview_button_color_hover'];

            $ldd = get_option( 'ld_dashboard_design_settings', array() );
            if ( ! empty( $ldd['color'] ) && empty( $hub_options['palettes']['brand']['primary_color'] ) ) $hub_options['palettes']['brand']['primary_color'] = $ldd['color'];
            if ( ! empty( $ldd['hover_color'] ) && empty( $hub_options['palettes']['brand']['primary_color_hover'] ) ) $hub_options['palettes']['brand']['primary_color_hover'] = $ldd['hover_color'];
            
            $um = get_option( 'um_options', array() );
            if ( ! empty( $um['primary_color'] ) && empty( $hub_options['palettes']['brand']['primary_color'] ) ) $hub_options['palettes']['brand']['primary_color'] = $um['primary_color'];
        }
        
        update_option( JJ_STYLE_GUIDE_OPTIONS_KEY, $hub_options );
    }

    /**
     * Kadence 타이포그래피 심층 동기화 엔진
     * 
     * @param array $kt_options Kadence 테마 옵션 배열
     * @param array $default_typography 기본 타이포그래피 배열
     * @return array 동기화된 타이포그래피 배열
     */
    private static function sync_kadence_typography( $kt_options, $default_typography ) {
        $synced_typography = $default_typography;
        $tags = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p');

        foreach ( $tags as $tag ) {
            $kt_key = ( $tag === 'p' ) ? 'p_font' : $tag . '_font';
            
            if ( ! empty( $kt_options[$kt_key] ) && is_array( $kt_options[$kt_key] ) ) {
                $kt_font = $kt_options[$kt_key];
                
                // Kadence DB 구조를 허브 DB 구조로 매핑
                if ( ! empty( $kt_font['family'] ) ) $synced_typography[$tag]['font_family'] = $kt_font['family'];
                if ( ! empty( $kt_font['weight'] ) ) $synced_typography[$tag]['font_weight'] = $kt_font['weight'];
                if ( ! empty( $kt_font['style'] ) ) $synced_typography[$tag]['font_style'] = $kt_font['style'];
                if ( ! empty( $kt_font['textTransform'] ) ) $synced_typography[$tag]['text_transform'] = $kt_font['textTransform'];
                
                // 반응형 배열 [Desktop, Tablet, Mobile] 매핑
                if ( isset( $kt_font['size'][0] ) && $kt_font['size'][0] !== '' ) $synced_typography[$tag]['font_size']['desktop'] = $kt_font['size'][0];
                if ( isset( $kt_font['size'][1] ) && $kt_font['size'][1] !== '' ) $synced_typography[$tag]['font_size']['tablet'] = $kt_font['size'][1];
                if ( isset( $kt_font['size'][2] ) && $kt_font['size'][2] !== '' ) $synced_typography[$tag]['font_size']['mobile'] = $kt_font['size'][2];
                
                if ( isset( $kt_font['lineHeight'][0] ) && $kt_font['lineHeight'][0] !== '' ) $synced_typography[$tag]['line_height'] = $kt_font['lineHeight'][0];
                if ( isset( $kt_font['letterSpacing'][0] ) && $kt_font['letterSpacing'][0] !== '' ) $synced_typography[$tag]['letter_spacing'] = $kt_font['letterSpacing'][0];
            }
        }
        return $synced_typography;
    }

    /**
     * Kadence 버튼 심층 동기화 엔진
     * 
     * @param array $kt_options Kadence 테마 옵션 배열
     * @param array $default_buttons 기본 버튼 배열
     * @return array 동기화된 버튼 배열
     */
    private static function sync_kadence_buttons( $kt_options, $default_buttons ) {
        $synced_buttons = $default_buttons;
        
        // Kadence는 button_font 키에 대부분의 버튼 스타일을 저장
        if ( ! empty( $kt_options['button_font'] ) && is_array( $kt_options['button_font'] ) ) {
            $kt_btn = $kt_options['button_font'];

            // 색상 매핑
            if ( ! empty( $kt_btn['background'] ) ) $synced_buttons['primary']['background_color'] = $kt_btn['background'];
            if ( ! empty( $kt_btn['color'] ) ) $synced_buttons['primary']['text_color'] = $kt_btn['color'];
            if ( ! empty( $kt_btn['backgroundHover'] ) ) $synced_buttons['primary']['background_color_hover'] = $kt_btn['backgroundHover'];
            if ( ! empty( $kt_btn['colorHover'] ) ) $synced_buttons['primary']['text_color_hover'] = $kt_btn['colorHover'];
            if ( ! empty( $kt_btn['border'] ) ) $synced_buttons['primary']['border_color'] = $kt_btn['border'];
            if ( ! empty( $kt_btn['borderHover'] ) ) $synced_buttons['primary']['border_color_hover'] = $kt_btn['borderHover'];
            
            // 레이아웃 매핑
            if ( isset( $kt_btn['borderRadius'] ) && $kt_btn['borderRadius'] !== '' ) $synced_buttons['primary']['border_radius'] = $kt_btn['borderRadius'];
            
            // 패딩 배열 [Top, Right, Bottom, Left] 매핑
            if ( isset( $kt_btn['padding'][0] ) && $kt_btn['padding'][0] !== '' ) $synced_buttons['primary']['padding']['top'] = $kt_btn['padding'][0];
            if ( isset( $kt_btn['padding'][1] ) && $kt_btn['padding'][1] !== '' ) $synced_buttons['primary']['padding']['right'] = $kt_btn['padding'][1];
            if ( isset( $kt_btn['padding'][2] ) && $kt_btn['padding'][2] !== '' ) $synced_buttons['primary']['padding']['bottom'] = $kt_btn['padding'][2];
            if ( isset( $kt_btn['padding'][3] ) && $kt_btn['padding'][3] !== '' ) $synced_buttons['primary']['padding']['left'] = $kt_btn['padding'][3];

            // 그림자 매핑
            if ( ! empty( $kt_btn['shadow'] ) && is_array( $kt_btn['shadow'] ) ) {
                $kt_shadow = $kt_btn['shadow'];
                if ( ! empty( $kt_shadow['color'] ) ) $synced_buttons['primary']['shadow']['color'] = $kt_shadow['color'];
                if ( isset( $kt_shadow['hOffset'] ) && $kt_shadow['hOffset'] !== '' ) $synced_buttons['primary']['shadow']['x'] = $kt_shadow['hOffset'];
                if ( isset( $kt_shadow['vOffset'] ) && $kt_shadow['vOffset'] !== '' ) $synced_buttons['primary']['shadow']['y'] = $kt_shadow['vOffset'];
                if ( isset( $kt_shadow['blur'] ) && $kt_shadow['blur'] !== '' ) $synced_buttons['primary']['shadow']['blur'] = $kt_shadow['blur'];
                if ( isset( $kt_shadow['spread'] ) && $kt_shadow['spread'] !== '' ) $synced_buttons['primary']['shadow']['spread'] = $kt_shadow['spread'];
            }
        }
        return $synced_buttons;
    }

    /**
     * Kadence 시스템 팔레트 심층 동기화 엔진
     * 
     * Kadence 테마의 시스템 팔레트 설정을 플러그인의 시스템 팔레트로 동기화합니다.
     * - 사이트 배경색, 콘텐츠 배경색
     * - 텍스트 색상, 링크 색상
     * 
     * @param array $kt_options Kadence 테마 옵션 배열
     * @param array $default_system_palette 기본 시스템 팔레트 배열
     * @return array 동기화된 시스템 팔레트 배열
     * 
     * @note 이미지 참조: image_366542.png, image_36070b.jpg
     * @since v3.1
     */
    private static function sync_kadence_system_palette( $kt_options, $default_system_palette ) {
        $synced_palette = $default_system_palette;
        $kt_palette = $kt_options['global_palette'] ?? array();
        
        // Backgrounds 섹션 값 (가장 정확함)
        if ( ! empty( $kt_options['site_background'] ) ) {
            $synced_palette['site_bg'] = $kt_options['site_background'];
        }
        if ( ! empty( $kt_options['content_background'] ) ) {
            $synced_palette['content_bg'] = $kt_options['content_background'];
        }

        // Global Palette의 Base 값 (Fallback)
        // Kadence는 Base 색상을 palette6(텍스트) 등에 저장함
        if ( ! empty( $kt_palette['palette6'] ) ) {
            $synced_palette['text_color'] = $kt_palette['palette6'];
        }
        if ( ! empty( $kt_palette['palette9'] ) ) {
            $synced_palette['link_color'] = $kt_palette['palette9'];
        }

        // Content Links 섹션 값 (링크색의 최종 확인)
        if ( ! empty( $kt_options['link_color'] ) ) {
            $synced_palette['link_color'] = $kt_options['link_color'];
        }

        return $synced_palette;
    }

    /**
     * 스타일 가이드 설정 페이지 자동 생성
     * 
     * @since v1.9.0
     */
    private static function create_cockpit_page() {
        // 상수 정의 확인
        if ( ! defined( 'JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY' ) || ! defined( 'JJ_STYLE_GUIDE_PAGE_SLUG' ) ) {
            return;
        }
        
        // WordPress 함수 확인
        if ( ! function_exists( 'get_option' ) || ! function_exists( 'wp_insert_post' ) ) {
            return;
        }
        
        $page_slug = 'Style-Guide-Setting'; 
        $page_id_key = JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY;
        $page_id = get_option( $page_id_key );

        $page_content = self::get_block_template_content();

        $page_data = array(
            'post_title'   => __( '스타일 가이드 설정', 'jj-style-guide' ), 
            'post_name'    => $page_slug,
            'post_content' => $page_content,
            'post_status'  => 'private', 
            'post_type'    => 'page',
            'comment_status' => 'closed',
            'ping_status'    => 'closed',
        );

        if ( $page_id && get_post( $page_id ) && 'page' === get_post_type( $page_id ) && 'trash' !== get_post_status( $page_id ) ) {
            $page_data['ID'] = $page_id;
            wp_update_post( $page_data );
            return;
        }

        $existing_page = get_page_by_path( $page_slug, OBJECT, 'page' );
        if ( $existing_page ) {
             $page_data['ID'] = $existing_page->ID;
             wp_update_post( $page_data );
             update_option( $page_id_key, $existing_page->ID );
             return;
        }

        $new_page_id = wp_insert_post( $page_data );

        if ( $new_page_id && ! is_wp_error( $new_page_id ) ) {
            update_option( $page_id_key, $new_page_id );
        }
    }

    /**
     * 시각적 스타일 가이드 프리뷰 HTML 블록 템플릿
     * 
     * @return string 스타일 가이드 설정 페이지 콘텐츠
     * @since v2.0.0
     */
    private static function get_block_template_content() {
        
        $customizer_url = esc_url( admin_url( 'customize.php?autofocus[panel]=jj_style_guide_panel' ) );
        $settings_url = esc_url( admin_url( 'options-general.php?page=' . JJ_STYLE_GUIDE_PAGE_SLUG ) );
        
        // [v5.0.3] 개선된 프리뷰 페이지 템플릿
        $content = '
<!--wp:group {"style":{"spacing":{"padding":{"top":"1em","right":"1em","bottom":"1em","left":"1em"}}},"backgroundColor":"light-gray-foreground","textColor":"dark-gray"}-->
<div class="wp-block-group has-dark-gray-color has-light-gray-foreground-background-color has-text-color has-background" style="padding-top:1em;padding-right:1em;padding-bottom:1em;padding-left:1em">
<!--wp:paragraph {"style":{"typography":{"fontSize":"16px"}},"fontFamily":"system-ui"}-->
<p style="font-size:16px;font-family:system-ui"><strong>' . esc_html__( '[안내]', 'jj-style-guide' ) . '</strong> ' . esc_html__( '이 페이지는 설정 또는 사용자 정의 메뉴에서 변경한 스타일이 실시간으로 반영되는 시각적 프리뷰입니다.', 'jj-style-guide' ) . '<br>' . esc_html__( '실제 스타일 편집은 이 페이지의 내용이 아니라, 아래 버튼을 통해 새 탭으로 열리는 컨트롤 패널에서 진행해 주세요.', 'jj-style-guide' ) . '</p>
<!--/wp:paragraph-->
</div>
<!--/wp:group-->

<!--wp:buttons {"layout":{"type":"flex","justifyContent":"center"}}-->
<div class="wp-block-buttons">
<!--wp:button {"style":{"elements":{"link":{"color":{"text":"var:preset|color|vivid-cyan-blue"}}}},"className":"is-style-outline"}-->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-vivid-cyan-blue-color has-text-color wp-element-button" href="' . esc_url( $customizer_url ) . '" target="_blank" rel="noopener">' . esc_html__( '사용자 정의 패널에서 편집 (새 탭)', 'jj-style-guide' ) . '</a></div>
<!--/wp:button-->

<!--wp:button {"style":{"elements":{"link":{"color":{"text":"var:preset|color|vivid-cyan-blue"}}}},"className":"is-style-outline"}-->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-vivid-cyan-blue-color has-text-color wp-element-button" href="' . esc_url( $settings_url ) . '" target="_blank" rel="noopener">' . esc_html__( '플러그인 설정 페이지에서 편집 (새 탭)', 'jj-style-guide' ) . '</a></div>
<!--/wp:button-->
</div>
<!--/wp:buttons-->

<!--wp:separator {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"className":"is-style-wide"}-->
<hr class="wp-block-separator has-alpha-channel-opacity has-vivid-cyan-blue-color has-text-color has-link-color is-style-wide" style="margin-top:var(--wp--preset--spacing--40);margin-bottom:var(--wp--preset--spacing--40)"/>
<!--/wp:separator-->

<!--wp:heading {"level":2}-->
<h2 class="wp-block-heading">' . esc_html__( 'Typography', 'jj-style-guide' ) . '</h2>
<!--/wp:heading-->

<!--wp:heading {"level":1,"className":"jj-preview-h1"}-->
<h1 class="wp-block-heading jj-preview-h1">' . esc_html__( 'H1 Heading: 다람쥐 헌 쳇바퀴에 타고파.', 'jj-style-guide' ) . '</h1>
<!--/wp:heading-->

<!--wp:heading {"level":2,"className":"jj-preview-h2"}-->
<h2 class="wp-block-heading jj-preview-h2">' . esc_html__( 'H2 Heading: 다람쥐 헌 쳇바퀴에 타고파.', 'jj-style-guide' ) . '</h2>
<!--/wp:heading-->

<!--wp:heading {"level":3,"className":"jj-preview-h3"}-->
<h3 class="wp-block-heading jj-preview-h3">' . esc_html__( 'H3 Heading: 다람쥐 헌 쳇바퀴에 타고파.', 'jj-style-guide' ) . '</h3>
<!--/wp:heading-->

<!--wp:heading {"level":4,"className":"jj-preview-h4"}-->
<h4 class="wp-block-heading jj-preview-h4">' . esc_html__( 'H4 Heading: 다람쥐 헌 쳇바퀴에 타고파.', 'jj-style-guide' ) . '</h4>
<!--/wp:heading-->

<!--wp:heading {"level":5,"className":"jj-preview-h5"}-->
<h5 class="wp-block-heading jj-preview-h5">' . esc_html__( 'H5 Heading: 다람쥐 헌 쳇바퀴에 타고파.', 'jj-style-guide' ) . '</h5>
<!--/wp:heading-->

<!--wp:heading {"level":6,"className":"jj-preview-h6"}-->
<h6 class="wp-block-heading jj-preview-h6">' . esc_html__( 'H6 Heading: 다람쥐 헌 쳇바퀴에 타고파.', 'jj-style-guide' ) . '</h6>
<!--/wp:heading-->

<!--wp:paragraph {"className":"jj-preview-p"}-->
<p class="jj-preview-p">' . esc_html__( 'P (Paragraph): 다람쥐 헌 쳇바퀴에 타고파. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim in eros elementum tristique. Duis cursus, mi quis viverra ornare, eros dolor interdum nulla, ut commodo diam libero vitae erat.', 'jj-style-guide' ) . '</p>
<!--/wp:paragraph-->

<!--wp:spacer {"height":"40px"}-->
<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
<!--/wp:spacer-->

<!--wp:heading {"level":2}-->
<h2 class="wp-block-heading">' . esc_html__( 'Color Palette', 'jj-style-guide' ) . '</h2>
<!--/wp:heading-->

<!--wp:columns-->
<div class="wp-block-columns">
<!--wp:column-->
<div class="wp-block-column">
<!--wp:paragraph {"align":"center","className":"jj-preview-color-box jj-preview-color-primary","style":{"border":{"width":"1px","style":"solid","radius":"4px"},"spacing":{"padding":{"top":"1em","right":"1em","bottom":"1em","left":"1em"}}}}-->
<p class="has-text-align-center jj-preview-color-box jj-preview-color-primary" style="border-style:solid;border-width:1px;border-radius:4px;padding-top:1em;padding-right:1em;padding-bottom:1em;padding-left:1em"><strong>' . esc_html__( 'Primary', 'jj-style-guide' ) . '</strong><br>var(--jj-primary-color)</p>
<!--/wp:paragraph-->
</div>
<!--/wp:column-->

<!--wp:column-->
<div class="wp-block-column">
<!--wp:paragraph {"align":"center","className":"jj-preview-color-box jj-preview-color-primary-hover","style":{"border":{"width":"1px","style":"solid","radius":"4px"},"spacing":{"padding":{"top":"1em","right":"1em","bottom":"1em","left":"1em"}}}}-->
<p class="has-text-align-center jj-preview-color-box jj-preview-color-primary-hover" style="border-style:solid;border-width:1px;border-radius:4px;padding-top:1em;padding-right:1em;padding-bottom:1em;padding-left:1em"><strong>' . esc_html__( 'Primary Hover', 'jj-style-guide' ) . '</strong><br>var(--jj-primary-color-hover)</p>
<!--/wp:paragraph-->
</div>
<!--/wp:column-->

<!--wp:column-->
<div class="wp-block-column">
<!--wp:paragraph {"align":"center","className":"jj-preview-color-box jj-preview-color-secondary","style":{"border":{"width":"1px","style":"solid","radius":"4px"},"spacing":{"padding":{"top":"1em","right":"1em","bottom":"1em","left":"1em"}}}}-->
<p class="has-text-align-center jj-preview-color-box jj-preview-color-secondary" style="border-style:solid;border-width:1px;border-radius:4px;padding-top:1em;padding-right:1em;padding-bottom:1em;padding-left:1em"><strong>' . esc_html__( 'Secondary', 'jj-style-guide' ) . '</strong><br>var(--jj-secondary-color)</p>
<!--/wp:paragraph-->
</div>
<!--/wp:column-->

<!--wp:column-->
<div class="wp-block-column">
<!--wp:paragraph {"align":"center","className":"jj-preview-color-box jj-preview-color-secondary-hover","style":{"border":{"width":"1px","style":"solid","radius":"4px"},"spacing":{"padding":{"top":"1em","right":"1em","bottom":"1em","left":"1em"}}}}-->
<p class="has-text-align-center jj-preview-color-box jj-preview-color-secondary-hover" style="border-style:solid;border-width:1px;border-radius:4px;padding-top:1em;padding-right:1em;padding-bottom:1em;padding-left:1em"><strong>' . esc_html__( 'Secondary Hover', 'jj-style-guide' ) . '</strong><br>var(--jj-secondary-color-hover)</p>
<!--/wp:paragraph-->
</div>
<!--/wp:column-->
</div>
<!--/wp:columns-->

<!--wp:spacer {"height":"40px"}-->
<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
<!--/wp:spacer-->

<!--wp:heading {"level":2}-->
<h2 class="wp-block-heading">' . esc_html__( 'Buttons', 'jj-style-guide' ) . '</h2>
<!--/wp:heading-->

<!--wp:buttons-->
<div class="wp-block-buttons">
<!--wp:button {"className":"jj-preview-button-primary"}-->
<div class="wp-block-button jj-preview-button-primary"><a class="wp-block-button__link wp-element-button">' . esc_html__( 'Primary Button', 'jj-style-guide' ) . '</a></div>
<!--/wp:button-->

<!--wp:button {"className":"is-style-outline jj-preview-button-secondary"}-->
<div class="wp-block-button is-style-outline jj-preview-button-secondary"><a class="wp-block-button__link wp-element-button">' . esc_html__( 'Secondary Button', 'jj-style-guide' ) . '</a></div>
<!--/wp:button-->

<!--wp:button {"className":"is-style-minimal jj-preview-button-tertiary"}-->
<div class="wp-block-button is-style-minimal jj-preview-button-tertiary"><a class="wp-block-button__link wp-element-button">' . esc_html__( 'Tertiary (Text) Button', 'jj-style-guide' ) . '</a></div>
<!--/wp:button-->
</div>
<!--/wp:buttons-->

<!--wp:spacer {"height":"40px"}-->
<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
<!--/wp:spacer-->

<!--wp:heading {"level":2}-->
<h2 class="wp-block-heading">' . esc_html__( 'Forms & Fields', 'jj-style-guide' ) . '</h2>
<!--/wp:heading-->

<!--wp:html-->
<div class="jj-preview-forms" style="max-width: 600px; margin: 0 auto;">
<form style="background: #f9f9f9; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
<div style="margin-bottom: 15px;">
<label for="jj-preview-input-text" style="display: block; margin-bottom: 5px; font-weight: 600; color: var(--jj-form-label-color, #333);">' . esc_html__( 'Text Input', 'jj-style-guide' ) . '</label>
<input type="text" id="jj-preview-input-text" placeholder="' . esc_attr__( 'Enter text here...', 'jj-style-guide' ) . '" style="width: 100%; padding: var(--jj-form-input-padding, 8px); border: 1px solid var(--jj-form-input-border, #ccc); border-radius: var(--jj-form-input-border-radius, 4px); font-size: 14px;" />
</div>
<div style="margin-bottom: 15px;">
<label for="jj-preview-textarea" style="display: block; margin-bottom: 5px; font-weight: 600; color: var(--jj-form-label-color, #333);">' . esc_html__( 'Textarea', 'jj-style-guide' ) . '</label>
<textarea id="jj-preview-textarea" rows="4" placeholder="' . esc_attr__( 'Enter your message...', 'jj-style-guide' ) . '" style="width: 100%; padding: var(--jj-form-input-padding, 8px); border: 1px solid var(--jj-form-input-border, #ccc); border-radius: var(--jj-form-input-border-radius, 4px); font-size: 14px; font-family: inherit;"></textarea>
</div>
<div style="margin-bottom: 15px;">
<label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
<input type="checkbox" style="width: 18px; height: 18px; cursor: pointer;" />
<span style="color: var(--jj-form-label-color, #333);">' . esc_html__( 'I agree to the terms and conditions', 'jj-style-guide' ) . '</span>
</label>
</div>
<button type="submit" class="jj-preview-button-primary" style="background: var(--jj-btn-primary-bg, #0073e6); color: var(--jj-btn-primary-text, #fff); border: 1px solid var(--jj-btn-primary-border, #0073e6); border-radius: var(--jj-btn-primary-border-radius, 4px); padding: var(--jj-btn-primary-padding-top, 12px) var(--jj-btn-primary-padding-right, 24px); cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s ease;">' . esc_html__( 'Submit', 'jj-style-guide' ) . '</button>
</form>
</div>
<!--/wp:html-->
';
        return $content;
    }
}
} // End class_exists check