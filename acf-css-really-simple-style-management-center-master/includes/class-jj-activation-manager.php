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
            'post_title'   => __( 'Style Guide', 'acf-css-really-simple-style-management-center' ),
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
        // 이 페이지는 이제 “실시간 미리보기 + 편집” 허브로 사용합니다.
        // (QuantumLab 스타일가이드 구조 참고: Colors/Typography/Buttons)
        // https://quantumlabtemplate.webflow.io/template-pages/style-guide
        $content = '
<!-- wp:shortcode -->
[jj_style_guide_live]
<!-- /wp:shortcode -->';
        return $content;
    }
}
} // End class_exists check