<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [v3.5.0 '재탄생'] '전략 1: CSS 변수' (핵심 엔진)
 * - [신규] 'JJ_CSS_Cache' 적용: 생성된 CSS를 '캐시'하여 '성능' '극대화'
 * - [신규] 'JJ_Selector_Registry' 적용: '하드코딩'된 '전역 선택자'를 '레지스트리'에서 '가져오도록' '변경'
 * - [신규] 'JJ_CSS_Injector' 적용: '복잡'한 'CSS 주입' 로직을 '헬퍼' 함수로 '통합'
 * - [제거] '어댑터 Fallback' CSS 로직 '제거' (v3.5부터 '각' '어댑터'가 '직접' '담당'함)
 */
final class JJ_Strategy_1_CSS_Vars {
    private static $instance = null;
    private $options = array();
    private $error_handler; // [v3.5.0 신규]

    public static function instance() {
        if ( is_null( self::$instance ) ) self::$instance = new self();
        return self::$instance;
    }

    public function init( $options ) {
        $this->options = $options;
        // [v5.1.7] 클래스 존재 확인 후 인스턴스 생성
        if ( class_exists( 'JJ_Error_Handler' ) ) {
            $this->error_handler = JJ_Error_Handler::instance();
        }
        
        // 프런트엔드에서만 전역 CSS를 주입합니다.
        // [v5.3.8] add_action 함수 존재 확인
        if ( function_exists( 'add_action' ) ) {
            add_action( 'wp_enqueue_scripts', array( $this, 'strategy_1_inject_css_variables' ), 999 );
        }
    }

    /**
     * [v3.5.0 '재탄생'] CSS 변수 및 전역 규칙 주입 (캐시 적용)
     */
    public function strategy_1_inject_css_variables() {
        try {
            // [Phase 12] 활성 폰트 URL 로드 (Google Fonts 등)
            $this->enqueue_active_fonts();

            // [v5.3.7] 치명적 오류 방지: 클래스 존재 확인
            if ( ! class_exists( 'JJ_CSS_Cache' ) || ! method_exists( 'JJ_CSS_Cache', 'instance' ) ) {
                return;
            }
            
            // [v3.5.0 '신규'] 'CSS 캐시' 확인
            $cache = JJ_CSS_Cache::instance();
            if ( ! $cache || ! method_exists( $cache, 'get' ) ) {
                return;
            }
            
            $cache_key = 'strategy_1_main_css';
            $cached_css = $cache->get( $cache_key );

            if ( $cached_css ) {
                // [v5.3.7] 치명적 오류 방지: 클래스 존재 확인
                if ( ! class_exists( 'JJ_CSS_Injector' ) || ! method_exists( 'JJ_CSS_Injector', 'inject' ) ) {
                    return;
                }
                
                // '캐시'가 '존재'하면 '즉시' '주입'하고 '종료' (성능 향상)
                JJ_CSS_Injector::inject( $cached_css, array(), 'jj-strategy-1-cache' );
                return;
            }

            // '캐시'가 '없으므로' '새로' '생성'
            list( $css, $global_css, $preview_css ) = $this->generate_css_strings();
            
            // === [ 4. 반응형 미디어 쿼리 (v3.3 '유지') ] ===
            list( $css, $preview_css ) = $this->append_responsive_css( $css, $preview_css );

            // === [ 5. [v3.5.0 '재탄생'] '최종' CSS '결합' 및 '주입' ] ===
            
            // '변수' + '전역 규칙' + '미리보기 규칙'
            $final_css = $css . " " . $global_css . " " . $preview_css;

            // [v5.3.7] CSS 최적화 (압축)
            $final_css = apply_filters( 'jj_generated_css_output', $final_css );
            if ( false ) { // Legacy optimizer code disabled
                $optimizer = JJ_CSS_Optimizer::instance();
                if ( method_exists( $optimizer, 'minify' ) && method_exists( $optimizer, 'is_enabled' ) && $optimizer->is_enabled() ) {
                    $final_css = $optimizer->minify( $final_css );
                }
            }

            // [v5.3.7] CSS 생성 성능 최적화: 캐시 저장 및 주입
            // '캐시'에 '저장'
            if ( method_exists( $cache, 'set' ) ) {
                $cache->set( $cache_key, $final_css );
            }

            // '인젝터'를 '사용'하여 '주입'
            if ( class_exists( 'JJ_CSS_Injector' ) && method_exists( 'JJ_CSS_Injector', 'inject' ) ) {
                JJ_CSS_Injector::inject( $final_css, array( 'kadence-global' ), 'jj-strategy-1-main' );
            }

        } catch ( Exception $e ) {
            $this->error_handler->handle_css_error( 'Strategy_1_CSS_Vars', $e->getMessage() );
        }
    }

    /**
     * [Phase 12] 활성 폰트 URL 로드 (Google Fonts, CDN 등)
     */
    private function enqueue_active_fonts() {
        if ( ! function_exists( 'wp_enqueue_style' ) ) {
            return;
        }

        $key = defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ? JJ_STYLE_GUIDE_OPTIONS_KEY : 'jj_style_guide_options';
        $opt = function_exists( 'get_option' ) ? (array) get_option( $key, array() ) : array();

        $font_urls = isset( $opt['active_font_urls'] ) && is_array( $opt['active_font_urls'] ) ? $opt['active_font_urls'] : array();
        if ( empty( $font_urls ) ) {
            return;
        }

        $index = 0;
        foreach ( $font_urls as $url ) {
            if ( empty( $url ) || ! is_string( $url ) ) {
                continue;
            }
            $url = esc_url( $url );
            if ( empty( $url ) ) {
                continue;
            }
            $handle = 'jj-google-font-' . $index;
            wp_enqueue_style( $handle, $url, array(), null );
            $index++;
        }
    }

    /**
     * [v3.5.0 '신규'] CSS 문자열 생성 로직 분리 (가독성 및 캐싱)
     */
    private function generate_css_strings() {
        
        $css = ":root {";
        $preview_css = ""; 
        $global_css = ""; 
        
        // [v3.5.0 '신규'] '유틸리티' '인스턴스' '로드'
        $registry = JJ_Selector_Registry::instance();
        
        // === [ 1. '허브' CSS 변수 생성 (v3.3 '유지') ] ===
        
        // 1a. '브랜드' 팔레트
        // [v5.3.8] 배열 접근 안전성 강화: PHP 8.4 호환성
        if ( isset( $this->options['palettes'] ) && is_array( $this->options['palettes'] ) && ! empty( $this->options['palettes']['brand'] ) ) {
            $colors = $this->options['palettes']['brand'];
            if ( ! empty( $colors['primary_color'] ) && function_exists( 'esc_attr' ) ) { $css .= "--jj-primary-color: " . esc_attr( $colors['primary_color'] ) . ";"; $preview_css .= ".jj-preview-color-primary { background-color: var(--jj-primary-color); color: #fff; }"; }
            if ( ! empty( $colors['primary_color_hover'] ) && function_exists( 'esc_attr' ) ) { $css .= "--jj-primary-color-hover: " . esc_attr( $colors['primary_color_hover'] ) . ";"; $preview_css .= ".jj-preview-color-primary-hover { background-color: var(--jj-primary-color-hover); color: #fff; }"; }
            if ( ! empty( $colors['secondary_color'] ) && function_exists( 'esc_attr' ) ) { $css .= "--jj-secondary-color: " . esc_attr( $colors['secondary_color'] ) . ";"; $preview_css .= ".jj-preview-color-secondary { background-color: var(--jj-secondary-color); color: #fff; }"; }
            if ( ! empty( $colors['secondary_color_hover'] ) && function_exists( 'esc_attr' ) ) { $css .= "--jj-secondary-color-hover: " . esc_attr( $colors['secondary_color_hover'] ) . ";"; $preview_css .= ".jj-preview-color-secondary-hover { background-color: var(--jj-secondary-color-hover); color: #fff; }"; }
        }

        // 1b. '시스템' 팔레트
        // [v5.3.8] 배열 접근 안전성 강화: PHP 8.4 호환성
        if ( isset( $this->options['palettes'] ) && is_array( $this->options['palettes'] ) && ! empty( $this->options['palettes']['system'] ) ) {
            $sys_colors = $this->options['palettes']['system'];
            if ( ! empty( $sys_colors['site_bg'] ) && function_exists( 'esc_attr' ) ) { $css .= "--jj-sys-site-bg: " . esc_attr( $sys_colors['site_bg'] ) . ";"; }
            if ( ! empty( $sys_colors['content_bg'] ) && function_exists( 'esc_attr' ) ) { $css .= "--jj-sys-content-bg: " . esc_attr( $sys_colors['content_bg'] ) . ";"; }
            if ( ! empty( $sys_colors['text_color'] ) && function_exists( 'esc_attr' ) ) { $css .= "--jj-sys-text: " . esc_attr( $sys_colors['text_color'] ) . ";"; }
            if ( ! empty( $sys_colors['link_color'] ) && function_exists( 'esc_attr' ) ) { $css .= "--jj-sys-link: " . esc_attr( $sys_colors['link_color'] ) . ";"; }
        }
        
        // 1c. '커스텀 폰트' 변수 및 @font-face (@since v3.7.0)
        // [v5.3.8] 배열 접근 안전성 강화: PHP 8.4 호환성
        if ( isset( $this->options['fonts'] ) && ! empty( $this->options['fonts'] ) && is_array( $this->options['fonts'] ) ) {
            $fonts = $this->options['fonts'];

            foreach ( array( 'korean', 'english', 'buttons', 'forms' ) as $role ) {
                if ( ! isset( $fonts[ $role ] ) || ! is_array( $fonts[ $role ] ) || empty( $fonts[ $role ]['family'] ) ) {
                    continue;
                }
                $family = $fonts[ $role ]['family'];
                $attach_id = isset( $fonts[ $role ]['attachment_id'] ) ? (int) $fonts[ $role ]['attachment_id'] : 0;
                $format = isset( $fonts[ $role ]['format'] ) ? $fonts[ $role ]['format'] : '';
                // [v5.3.8] WordPress 함수 안전 호출
                $url = ( $attach_id && function_exists( 'wp_get_attachment_url' ) ) ? wp_get_attachment_url( $attach_id ) : '';

                if ( $url ) {
                    if ( ! $format ) {
                        $ext = strtolower( pathinfo( $url, PATHINFO_EXTENSION ) );
                        if ( in_array( $ext, array( 'woff2', 'woff', 'ttf', 'otf' ), true ) ) {
                            $format_map = array(
                                'woff2' => 'woff2',
                                'woff'  => 'woff',
                                'ttf'   => 'truetype',
                                'otf'   => 'opentype',
                            );
                            $format = isset( $format_map[ $ext ] ) ? $format_map[ $ext ] : '';
                        }
                    }

                    // [v5.3.8] WordPress 함수 안전 호출
                    if ( function_exists( 'esc_attr' ) && function_exists( 'esc_url' ) ) {
                        $global_css .= "@font-face{font-family:'" . esc_attr( $family ) . "';src:url('" . esc_url( $url ) . "')" . ( $format ? " format('" . esc_attr( $format ) . "')" : "" ) . ";font-display:swap;}";
                    }
                }

                // 역할별 CSS 변수 설정
                // [v5.3.8] WordPress 함수 안전 호출
                if ( function_exists( 'esc_attr' ) ) {
                    if ( 'korean' === $role ) {
                        $css .= "--jj-font-ko-family: " . esc_attr( $family ) . ";";
                    } elseif ( 'english' === $role ) {
                        $css .= "--jj-font-en-family: " . esc_attr( $family ) . ";";
                    } elseif ( 'buttons' === $role ) {
                        $css .= "--jj-font-button-family: " . esc_attr( $family ) . ";";
                    } elseif ( 'forms' === $role ) {
                        $css .= "--jj-font-form-family: " . esc_attr( $family ) . ";";
                    }
                }
            }
        }

        // 1d. '타이포그래피' 변수
        // [v5.3.6] 메모리 최적화: 대용량 타이포그래피 데이터 처리
        if ( ! empty( $this->options['typography'] ) ) {
            $typography_count = count( $this->options['typography'] );
            // 대용량 데이터인 경우 메모리 체크
            if ( $typography_count > 50 && class_exists( 'JJ_Memory_Manager' ) ) {
                $memory_manager = JJ_Memory_Manager::instance();
                if ( $memory_manager->get_memory_usage_ratio() > 0.7 ) {
                    // 메모리 사용량이 높으면 가비지 컬렉션 실행
                    if ( function_exists( 'gc_collect_cycles' ) ) {
                        gc_collect_cycles();
                    }
                }
            }
            
            foreach( $this->options['typography'] as $tag => $props ) {
                $preview_selector = ".jj-preview-" . esc_attr( $tag );
                $tag_css = "";
                $tag_var_prefix = "--jj-font-" . esc_attr( $tag ); 
                if ( ! empty( $props['font_family'] ) ) { $css .= "$tag_var_prefix-family: " . esc_attr( $props['font_family'] ) . ";"; $tag_css .= "font-family: var($tag_var_prefix-family);"; }
                if ( ! empty( $props['font_weight'] ) ) { $css .= "$tag_var_prefix-weight: " . esc_attr( $props['font_weight'] ) . ";"; $tag_css .= "font-weight: var($tag_var_prefix-weight);"; }
                if ( ! empty( $props['font_style'] ) ) { $css .= "$tag_var_prefix-style: " . esc_attr( $props['font_style'] ) . ";"; $tag_css .= "font-style: var($tag_var_prefix-style);"; }
                if ( isset( $props['line_height'] ) && $props['line_height'] !== '' ) { $css .= "$tag_var_prefix-line-height: " . esc_attr( $props['line_height'] ) . "em;"; $tag_css .= "line-height: var($tag_var_prefix-line-height);"; }
                if ( isset( $props['letter_spacing'] ) && $props['letter_spacing'] !== '' ) { $css .= "$tag_var_prefix-letter-spacing: " . esc_attr( $props['letter_spacing'] ) . "px;"; $tag_css .= "letter-spacing: var($tag_var_prefix-letter-spacing);"; }
                if ( ! empty( $props['text_transform'] ) ) { $css .= "$tag_var_prefix-text-transform: " . esc_attr( $props['text_transform'] ) . ";"; $tag_css .= "text-transform: var($tag_var_prefix-text-transform);"; }
                if ( ! empty( $props['font_size']['desktop'] ) ) {
                    $size_value = $this->format_font_size_value( $props['font_size']['desktop'] );
                    if ( $size_value ) {
                        $css .= "$tag_var_prefix-size: " . esc_attr( $size_value ) . ";";
                        $tag_css .= "font-size: var($tag_var_prefix-size);";
                    }
                }
                // (태블릿/모바일 변수는 반응형 함수로 이동)
                if ( ! empty( $tag_css ) ) $preview_css .= " $preview_selector { $tag_css }";
            }
        }

        // 1e. '버튼' 변수 (Primary, Secondary, Text)
        if ( ! empty( $this->options['buttons']['primary'] ) ) {
            $btn = $this->options['buttons']['primary']; $btn_var_prefix = "--jj-btn-primary";
            if ( ! empty( $btn['background_color'] ) ) $css .= "$btn_var_prefix-bg: " . esc_attr( $btn['background_color'] ) . ";";
            if ( ! empty( $btn['text_color'] ) ) $css .= "$btn_var_prefix-text: " . esc_attr( $btn['text_color'] ) . ";";
            if ( ! empty( $btn['border_color'] ) ) $css .= "$btn_var_prefix-border: " . esc_attr( $btn['border_color'] ) . ";";
            if ( ! empty( $btn['background_color_hover'] ) ) $css .= "$btn_var_prefix-bg-hover: " . esc_attr( $btn['background_color_hover'] ) . ";";
            if ( ! empty( $btn['text_color_hover'] ) ) $css .= "$btn_var_prefix-text-hover: " . esc_attr( $btn['text_color_hover'] ) . ";";
            if ( ! empty( $btn['border_color_hover'] ) ) $css .= "$btn_var_prefix-border-hover: " . esc_attr( $btn['border_color_hover'] ) . ";";
            if ( isset( $btn['border_radius'] ) && $btn['border_radius'] !== '' ) $css .= "$btn_var_prefix-border-radius: " . esc_attr( $btn['border_radius'] ) . "px;";
            if ( ! empty( $btn['padding']['top'] ) ) $css .= "$btn_var_prefix-padding-top: " . esc_attr( $btn['padding']['top'] ) . "px;";
            if ( ! empty( $btn['padding']['right'] ) ) $css .= "$btn_var_prefix-padding-right: " . esc_attr( $btn['padding']['right'] ) . "px;";
            if ( ! empty( $btn['padding']['bottom'] ) ) $css .= "$btn_var_prefix-padding-bottom: " . esc_attr( $btn['padding']['bottom'] ) . "px;";
            if ( ! empty( $btn['padding']['left'] ) ) $css .= "$btn_var_prefix-padding-left: " . esc_attr( $btn['padding']['left'] ) . "px;";
            if ( isset( $btn['shadow']['x'] ) && $btn['shadow']['x'] !== '' ) { $shadow_x = esc_attr( $btn['shadow']['x'] ) . "px"; $shadow_y = esc_attr( $btn['shadow']['y'] ?? 0 ) . "px"; $shadow_blur = esc_attr( $btn['shadow']['blur'] ?? 0 ) . "px"; $shadow_spread = esc_attr( $btn['shadow']['spread'] ?? 0 ) . "px"; $shadow_color = esc_attr( $btn['shadow']['color'] ?? 'rgba(0,0,0,0.1)' ); $css .= "$btn_var_prefix-shadow: " . "{$shadow_x} {$shadow_y} {$shadow_blur} {$shadow_spread} {$shadow_color};"; }
        }
        if ( ! empty( $this->options['buttons']['secondary'] ) ) {
            $btn = $this->options['buttons']['secondary']; $btn_var_prefix = "--jj-btn-secondary";
            if ( ! empty( $btn['background_color'] ) ) $css .= "$btn_var_prefix-bg: " . esc_attr( $btn['background_color'] ) . ";";
            if ( ! empty( $btn['text_color'] ) ) $css .= "$btn_var_prefix-text: " . esc_attr( $btn['text_color'] ) . ";";
            if ( ! empty( $btn['border_color'] ) ) $css .= "$btn_var_prefix-border: " . esc_attr( $btn['border_color'] ) . ";";
            if ( ! empty( $btn['background_color_hover'] ) ) $css .= "$btn_var_prefix-bg-hover: " . esc_attr( $btn['background_color_hover'] ) . ";";
            if ( ! empty( $btn['text_color_hover'] ) ) $css .= "$btn_var_prefix-text-hover: " . esc_attr( $btn['text_color_hover'] ) . ";";
            if ( ! empty( $btn['border_color_hover'] ) ) $css .= "$btn_var_prefix-border-hover: " . esc_attr( $btn['border_color_hover'] ) . ";";
            if ( isset( $btn['border_radius'] ) && $btn['border_radius'] !== '' ) $css .= "$btn_var_prefix-border-radius: " . esc_attr( $btn['border_radius'] ) . "px;";
            if ( ! empty( $btn['padding']['top'] ) ) $css .= "$btn_var_prefix-padding-top: " . esc_attr( $btn['padding']['top'] ) . "px;";
            if ( ! empty( $btn['padding']['right'] ) ) $css .= "$btn_var_prefix-padding-right: " . esc_attr( $btn['padding']['right'] ) . "px;";
            if ( ! empty( $btn['padding']['bottom'] ) ) $css .= "$btn_var_prefix-padding-bottom: " . esc_attr( $btn['padding']['bottom'] ) . "px;";
            if ( ! empty( $btn['padding']['left'] ) ) $css .= "$btn_var_prefix-padding-left: " . esc_attr( $btn['padding']['left'] ) . "px;";
            if ( isset( $btn['shadow']['x'] ) && $btn['shadow']['x'] !== '' ) { $shadow_x = esc_attr( $btn['shadow']['x'] ) . "px"; $shadow_y = esc_attr( $btn['shadow']['y'] ?? 0 ) . "px"; $shadow_blur = esc_attr( $btn['shadow']['blur'] ?? 0 ) . "px"; $shadow_spread = esc_attr( $btn['shadow']['spread'] ?? 0 ) . "px"; $shadow_color = esc_attr( $btn['shadow']['color'] ?? 'rgba(0,0,0,0.05)' ); $css .= "$btn_var_prefix-shadow: " . "{$shadow_x} {$shadow_y} {$shadow_blur} {$shadow_spread} {$shadow_color};"; }
        }
        if ( ! empty( $this->options['buttons']['text'] ) ) {
            $btn = $this->options['buttons']['text']; $btn_var_prefix = "--jj-btn-text";
            if ( ! empty( $btn['background_color'] ) ) $css .= "$btn_var_prefix-bg: " . esc_attr( $btn['background_color'] ) . ";";
            if ( ! empty( $btn['text_color'] ) ) $css .= "$btn_var_prefix-text: " . esc_attr( $btn['text_color'] ) . ";";
            if ( ! empty( $btn['border_color'] ) ) $css .= "$btn_var_prefix-border: " . esc_attr( $btn['border_color'] ) . ";";
            if ( ! empty( $btn['background_color_hover'] ) ) $css .= "$btn_var_prefix-bg-hover: " . esc_attr( $btn['background_color_hover'] ) . ";";
            if ( ! empty( $btn['text_color_hover'] ) ) $css .= "$btn_var_prefix-text-hover: " . esc_attr( $btn['text_color_hover'] ) . ";";
            if ( ! empty( $btn['border_color_hover'] ) ) $css .= "$btn_var_prefix-border-hover: " . esc_attr( $btn['border_color_hover'] ) . ";";
            if ( isset( $btn['border_radius'] ) && $btn['border_radius'] !== '' ) $css .= "$btn_var_prefix-border-radius: " . esc_attr( $btn['border_radius'] ) . "px;";
            if ( ! empty( $btn['padding']['top'] ) ) $css .= "$btn_var_prefix-padding-top: " . esc_attr( $btn['padding']['top'] ) . "px;";
            if ( ! empty( $btn['padding']['right'] ) ) $css .= "$btn_var_prefix-padding-right: " . esc_attr( $btn['padding']['right'] ) . "px;";
            if ( ! empty( $btn['padding']['bottom'] ) ) $css .= "$btn_var_prefix-padding-bottom: " . esc_attr( $btn['padding']['bottom'] ) . "px;";
            if ( ! empty( $btn['padding']['left'] ) ) $css .= "$btn_var_prefix-padding-left: " . esc_attr( $btn['padding']['left'] ) . "px;";
            if ( isset( $btn['shadow']['x'] ) && $btn['shadow']['x'] !== '' ) { $shadow_x = esc_attr( $btn['shadow']['x'] ) . "px"; $shadow_y = esc_attr( $btn['shadow']['y'] ?? 0 ) . "px"; $shadow_blur = esc_attr( $btn['shadow']['blur'] ?? 0 ) . "px"; $shadow_spread = esc_attr( $btn['shadow']['spread'] ?? 0 ) . "px"; $shadow_color = esc_attr( $btn['shadow']['color'] ?? 'rgba(0,0,0,0)' ); $css .= "$btn_var_prefix-shadow: " . "{$shadow_x} {$shadow_y} {$shadow_blur} {$shadow_spread} {$shadow_color};"; }
        }

        // 1f. '폼 & 필드' 변수
        if ( ! empty( $this->options['forms']['label'] ) ) {
            $label = $this->options['forms']['label']; $label_var_prefix = "--jj-form-label";
            if ( ! empty( $label['font_weight'] ) ) { $css .= "$label_var_prefix-weight: " . esc_attr( $label['font_weight'] ) . ";"; } else { $css .= "$label_var_prefix-weight: var(--jj-font-p-weight, 400);"; }
            if ( ! empty( $label['font_style'] ) ) { $css .= "$label_var_prefix-style: " . esc_attr( $label['font_style'] ) . ";"; } else { $css .= "$label_var_prefix-style: var(--jj-font-p-style, normal);"; }
            if ( ! empty( $label['text_transform'] ) ) { $css .= "$label_var_prefix-text-transform: " . esc_attr( $label['text_transform'] ) . ";"; } else { $css .= "$label_var_prefix-text-transform: var(--jj-font-p-text-transform, none);"; }
            if ( ! empty( $label['font_size'] ) ) { $css .= "$label_var_prefix-size: " . esc_attr( $label['font_size'] ) . "px;"; } else { $css .= "$label_var_prefix-size: var(--jj-font-p-size, 16px);"; }
            if ( ! empty( $label['text_color'] ) ) { $css .= "$label_var_prefix-color: " . esc_attr( $label['text_color'] ) . ";"; } else { $css .= "$label_var_prefix-color: var(--jj-sys-text, inherit);"; }
        }
        if ( ! empty( $this->options['forms']['field'] ) ) {
            $field = $this->options['forms']['field']; $field_var_prefix = "--jj-form-input";
            if ( ! empty( $field['background_color'] ) ) { $css .= "$field_var_prefix-bg: " . esc_attr( $field['background_color'] ) . ";"; }
            if ( ! empty( $field['text_color'] ) ) { $css .= "$field_var_prefix-text: " . esc_attr( $field['text_color'] ) . ";"; }
            if ( ! empty( $field['border_color'] ) ) { $css .= "$field_var_prefix-border: " . esc_attr( $field['border_color'] ) . ";"; }
            if ( ! empty( $field['border_color_focus'] ) ) { $css .= "$field_var_prefix-border-focus: " . esc_attr( $field['border_color_focus'] ) . ";"; }
            if ( isset( $field['border_radius'] ) && $field['border_radius'] !== '' ) { $css .= "$field_var_prefix-border-radius: " . esc_attr( $field['border_radius'] ) . "px;"; }
            if ( isset( $field['border_width'] ) && $field['border_width'] !== '' ) { $css .= "$field_var_prefix-border-width: " . esc_attr( $field['border_width'] ) . "px;"; }
            if ( ! empty( $field['padding']['top'] ) ) { $css .= "$field_var_prefix-padding-top: " . esc_attr( $field['padding']['top'] ) . "px;"; }
            if ( ! empty( $field['padding']['right'] ) ) { $css .= "$field_var_prefix-padding-right: " . esc_attr( $field['padding']['right'] ) . "px;"; }
            if ( ! empty( $field['padding']['bottom'] ) ) { $css .= "$field_var_prefix-padding-bottom: " . esc_attr( $field['padding']['bottom'] ) . "px;"; }
            if ( ! empty( $field['padding']['left'] ) ) { $css .= "$field_var_prefix-padding-left: " . esc_attr( $field['padding']['left'] ) . "px;"; }
        }
        
        // 1f. '컨텍스트' 변수 (v3.5.0 '제거': '어댑터'가 '직접' '처리'하도록 '이관')
        // (v3.4.9의 'contexts' '관련' '변수' '생성' '로직' '모두' '제거')

        $css .= "}"; // :root 종료


        // === [ 2. [v3.5.0 '재탄생'] '전역' 스타일 제어 CSS (레지스트리 '활용') ] ===
        
        $global_css .= "
            /* '시스템 팔레트' 연동 (v3.1) */
            body, .site { 
                background-color: var(--jj-sys-site-bg, inherit);
                color: var(--jj-sys-text, inherit);
            }
            .content-area, .entry-content, .content-background, .site-content {
                background-color: var(--jj-sys-content-bg, inherit);
            }
            a, .link {
                color: var(--jj-sys-link, inherit);
            }
        ";
        
        // 2a. '전역 타이포그래피' 연동 (Elementor / Core Blocks 포함)
        $global_css .= "
            h1,
            .wp-block-heading.is-style-jj-h1,
            .elementor-heading-title.elementor-size-xxl,
            .elementor-heading-title.elementor-size-xl {
                font-family: var(--jj-font-h1-family, inherit);
                font-weight: var(--jj-font-h1-weight, inherit);
                font-style: var(--jj-font-h1-style, normal);
                font-size: var(--jj-font-h1-size);
                line-height: var(--jj-font-h1-line-height);
                letter-spacing: var(--jj-font-h1-letter-spacing);
                text-transform: var(--jj-font-h1-text-transform);
            }
            h2,
            .wp-block-heading.is-style-jj-h2,
            .elementor-heading-title.elementor-size-large {
                font-family: var(--jj-font-h2-family, inherit);
                font-weight: var(--jj-font-h2-weight, inherit);
                font-style: var(--jj-font-h2-style, normal);
                font-size: var(--jj-font-h2-size);
                line-height: var(--jj-font-h2-line-height);
                letter-spacing: var(--jj-font-h2-letter-spacing);
                text-transform: var(--jj-font-h2-text-transform);
            }
            h3,
            .wp-block-heading.is-style-jj-h3,
            .elementor-heading-title.elementor-size-medium {
                font-family: var(--jj-font-h3-family, inherit);
                font-weight: var(--jj-font-h3-weight, inherit);
                font-style: var(--jj-font-h3-style, normal);
                font-size: var(--jj-font-h3-size);
                line-height: var(--jj-font-h3-line-height);
                letter-spacing: var(--jj-font-h3-letter-spacing);
                text-transform: var(--jj-font-h3-text-transform);
            }
            h4,
            .wp-block-heading.is-style-jj-h4,
            .elementor-heading-title.elementor-size-small {
                font-family: var(--jj-font-h4-family, inherit);
                font-weight: var(--jj-font-h4-weight, inherit);
                font-style: var(--jj-font-h4-style, normal);
                font-size: var(--jj-font-h4-size);
                line-height: var(--jj-font-h4-line-height);
                letter-spacing: var(--jj-font-h4-letter-spacing);
                text-transform: var(--jj-font-h4-text-transform);
            }
            h5,
            .wp-block-heading.is-style-jj-h5 {
                font-family: var(--jj-font-h5-family, inherit);
                font-weight: var(--jj-font-h5-weight, inherit);
                font-style: var(--jj-font-h5-style, normal);
                font-size: var(--jj-font-h5-size);
                line-height: var(--jj-font-h5-line-height);
                letter-spacing: var(--jj-font-h5-letter-spacing);
                text-transform: var(--jj-font-h5-text-transform);
            }
            h6,
            .wp-block-heading.is-style-jj-h6 {
                font-family: var(--jj-font-h6-family, inherit);
                font-weight: var(--jj-font-h6-weight, inherit);
                font-style: var(--jj-font-h6-style, normal);
                font-size: var(--jj-font-h6-size);
                line-height: var(--jj-font-h6-line-height);
                letter-spacing: var(--jj-font-h6-letter-spacing);
                text-transform: var(--jj-font-h6-text-transform);
            }
            body,
            p,
            .wp-block-paragraph,
            .elementor-widget-text-editor {
                font-family: var(--jj-font-p-family, inherit);
                font-weight: var(--jj-font-p-weight, inherit);
                font-style: var(--jj-font-p-style, normal);
                font-size: var(--jj-font-p-size);
                line-height: var(--jj-font-p-line-height);
                letter-spacing: var(--jj-font-p-letter-spacing);
                text-transform: var(--jj-font-p-text-transform);
            }
        ";

        // 2b. '전역 폼 라벨' 연동 (레지스트리 '활용')
        $global_css .= $registry->build_css_rule('forms', 'label', 'base', "
            font-family: var(--jj-form-label-family, var(--jj-font-p-family, inherit));
            font-weight: var(--jj-form-label-weight, var(--jj-font-p-weight, 400));
            font-style: var(--jj-form-label-style, var(--jj-font-p-style, normal));
            font-size: var(--jj-form-label-size, var(--jj-font-p-size));
            text-transform: var(--jj-form-label-text-transform, none);
            color: var(--jj-form-label-color, var(--jj-sys-text, inherit));
        ");
        
        // 2c. '전역 폼 입력 필드' 연동 (레지스트리 '활용')
        $global_css .= $registry->build_css_rule('forms', 'input', 'base', "
            background-color: var(--jj-form-input-bg, #fff);
            color: var(--jj-form-input-text, #333);
            border-color: var(--jj-form-input-border, #ccc);
            border-radius: var(--jj-form-input-border-radius, 4px);
            border-width: var(--jj-form-input-border-width, 1px);
            border-style: solid;
            padding-top: var(--jj-form-input-padding-top, 10px);
            padding-right: var(--jj-form-input-padding-right, 12px);
            padding-bottom: var(--jj-form-input-padding-bottom, 10px);
            padding-left: var(--jj-form-input-padding-left, 12px);
            transition: border-color 0.2s ease-in-out;
        ");
        $global_css .= $registry->build_css_rule('forms', 'input', 'focus', "
            border-color: var(--jj-form-input-border-focus, var(--jj-primary-color, #0073e6));
            box-shadow: none;
            outline: none;
        ");

        // 2d. '전역 버튼' 연동 (레지스트리 '활용')
        $global_css .= $registry->build_css_rule('buttons', 'primary', 'base', "
            background-color: var(--jj-btn-primary-bg) !important;
            color: var(--jj-btn-primary-text) !important;
            border-color: var(--jj-btn-primary-border) !important;
            border-radius: var(--jj-btn-primary-border-radius) !important;
            padding-top: var(--jj-btn-primary-padding-top) !important;
            padding-right: var(--jj-btn-primary-padding-right) !important;
            padding-bottom: var(--jj-btn-primary-padding-bottom) !important;
            padding-left: var(--jj-btn-primary-padding-left) !important;
            box-shadow: var(--jj-btn-primary-shadow) !important;
            transition: all 0.2s ease-in-out;
        ");
        $global_css .= $registry->build_css_rule('buttons', 'primary', 'hover', "
            background-color: var(--jj-btn-primary-bg-hover) !important;
            color: var(--jj-btn-primary-text-hover) !important;
            border-color: var(--jj-btn-primary-border-hover) !important;
        ");
        
        $global_css .= $registry->build_css_rule('buttons', 'secondary', 'base', "
            background-color: var(--jj-btn-secondary-bg) !important;
            color: var(--jj-btn-secondary-text) !important;
            border-color: var(--jj-btn-secondary-border) !important;
            border-radius: var(--jj-btn-secondary-border-radius) !important;
            padding-top: var(--jj-btn-secondary-padding-top) !important;
            padding-right: var(--jj-btn-secondary-padding-right) !important;
            padding-bottom: var(--jj-btn-secondary-padding-bottom) !important;
            padding-left: var(--jj-btn-secondary-padding-left) !important;
            box-shadow: var(--jj-btn-secondary-shadow) !important;
        ");
         $global_css .= $registry->build_css_rule('buttons', 'secondary', 'hover', "
            background-color: var(--jj-btn-secondary-bg-hover) !important;
            color: var(--jj-btn-secondary-text-hover) !important;
            border-color: var(--jj-btn-secondary-border-hover) !important;
        ");
        
        // [v3.5.0 '신규'] 'Text' 버튼 '레지스트리' '추가'
        // (레지스트리 'init_default_selectors'에 'buttons[text]' '추가' '필요')
        $registry->add_selectors('buttons', 'text', 'base', array(
             '.wp-block-button.is-style-minimal .wp-block-button__link',
             'a.button.is-style-text',
             '.button.text'
        ));
        $registry->add_selectors('buttons', 'text', 'hover', array(
             '.wp-block-button.is-style-minimal .wp-block-button__link:hover',
             'a.button.is-style-text:hover',
             '.button.text:hover'
        ));
        
        $global_css .= $registry->build_css_rule('buttons', 'text', 'base', "
            background-color: var(--jj-btn-text-bg) !important;
            color: var(--jj-btn-text-text) !important;
            border-color: var(--jj-btn-text-border) !important;
            border-radius: var(--jj-btn-text-border-radius) !important;
            padding-top: var(--jj-btn-text-padding-top) !important;
            padding-right: var(--jj-btn-text-padding-right) !important;
            padding-bottom: var(--jj-btn-text-padding-bottom) !important;
            padding-left: var(--jj-btn-text-padding-left) !important;
            box-shadow: var(--jj-btn-text-shadow) !important;
        ");
        $global_css .= $registry->build_css_rule('buttons', 'text', 'hover', "
            background-color: var(--jj-btn-text-bg-hover) !important;
            color: var(--jj-btn-text-text-hover) !important;
            border-color: var(--jj-btn-text-border-hover) !important;
        ");

        // 2e. 타이포그래피 섹션에서 정의한 커스텀 CSS (@font-face 등) 추가
        if ( ! empty( $this->options['typography']['custom_css'] ) ) {
            $global_css .= "\n" . $this->options['typography']['custom_css'] . "\n";
        }

        // === [ 3. [v3.5.0 '제거'] '어댑터' 'Fallback' CSS ] ===
        // $adapter_css = " ... "; (제거됨)
        
        // 'generate_css_strings' '함수'의 '반환' '값'
        return array( $css, $global_css, $preview_css );
    }
    
    /**
     * [v3.5.0 '신규'] 반응형 CSS 변수 및 미리보기 생성 로직 분리
     */
    private function append_responsive_css( $css, $preview_css ) {
        $breakpoints = $this->get_breakpoints();
        if ( empty( $breakpoints ) ) {
            // 폴백 (기존 동작)
            $breakpoints = array(
                'tablet'  => array( 'max' => 1024 ),
                'phablet' => array( 'max' => 767 ),
                'mobile'  => array( 'max' => 479 ),
            );
        }

        // 미디어쿼리 우선순위:
        // - max-width는 큰 값 → 작은 값 순서로 (작은 화면이 나중에 덮어씀)
        // - min-width는 작은 값 → 큰 값 순서로 (큰 화면이 나중에 덮어씀)
        $ordered = $this->order_breakpoints_for_css( $breakpoints );

        foreach ( $ordered as $bp_key ) {
            if ( 'desktop' === $bp_key ) {
                continue;
            }
            if ( ! isset( $breakpoints[ $bp_key ] ) || ! is_array( $breakpoints[ $bp_key ] ) ) {
                continue;
            }

            $mq = $this->build_media_query( $breakpoints[ $bp_key ] );
            if ( ! $mq ) {
                continue;
            }

            $css_vars = '';
            if ( ! empty( $this->options['typography'] ) ) {
                foreach ( $this->options['typography'] as $tag => $props ) {
                    $tag_var_prefix = "--jj-font-" . esc_attr( $tag );

                    $raw_size = $this->resolve_font_size_for_breakpoint( $props, $bp_key );
                    if ( '' === $raw_size ) {
                        continue;
                    }
                    $size_value = $this->format_font_size_value( $raw_size );
                    if ( ! $size_value ) {
                        continue;
                    }
                    $css_vars .= "$tag_var_prefix-size: " . esc_attr( $size_value ) . ";";
                }
            }

            if ( '' === $css_vars ) {
                continue;
            }

            $css .= " @media " . $mq . " { :root { " . $css_vars . " } }";
            $preview_css .= " @media " . $mq . " { ";
            foreach ( ( $this->options['typography'] ?? array() ) as $tag => $props ) {
                $raw_size = $this->resolve_font_size_for_breakpoint( $props, $bp_key );
                if ( '' === $raw_size ) {
                    continue;
                }
                $preview_css .= ".jj-preview-" . esc_attr( $tag ) . " { font-size: var(--jj-font-" . esc_attr( $tag ) . "-size); }";
            }
            $preview_css .= " }";
        }

        return array( $css, $preview_css );
    }

    /**
     * 타이포그래피 출력 단위(px/rem/em) + 기준 px(base) 설정
     * - 저장값은 기존과 동일하게 "px 숫자"를 유지하고, 출력 시 단위만 변환합니다.
     *
     * @return array{unit:string,base:float}
     */
    private function get_typography_unit_settings() {
        $unit = 'px';
        $base = 16.0;

        $ts = isset( $this->options['typography_settings'] ) && is_array( $this->options['typography_settings'] )
            ? $this->options['typography_settings']
            : array();

        if ( isset( $ts['unit'] ) ) {
            $u = (string) $ts['unit'];
            if ( in_array( $u, array( 'px', 'rem', 'em' ), true ) ) {
                $unit = $u;
            }
        }
        if ( isset( $ts['base_px'] ) && is_numeric( $ts['base_px'] ) ) {
            $b = (float) $ts['base_px'];
            if ( $b > 0 ) {
                $base = $b;
            }
        }

        return array(
            'unit' => $unit,
            'base' => $base,
        );
    }

    /**
     * 폰트 사이즈(px 숫자 문자열)를 설정에 따라 px/rem/em으로 변환하여 반환
     */
    private function format_font_size_value( $px_value ) {
        if ( '' === $px_value || null === $px_value ) {
            return '';
        }
        if ( ! is_numeric( $px_value ) ) {
            return '';
        }

        $settings = $this->get_typography_unit_settings();
        $unit = $settings['unit'];
        $base = (float) $settings['base'];
        $px   = (float) $px_value;

        if ( 'rem' === $unit || 'em' === $unit ) {
            $ratio = $base > 0 ? ( $px / $base ) : 0;
            // 너무 긴 소수 방지
            $ratio = round( $ratio, 4 );
            return (string) $ratio . $unit;
        }

        // default: px
        $px = round( $px, 4 );
        return (string) $px . 'px';
    }

    /**
     * 고급 스타일링(브레이크포인트)에서 사용 중인 브레이크포인트를 가져옵니다.
     */
    private function get_breakpoints() {
        if ( class_exists( 'JJ_Advanced_Styling' ) ) {
            try {
                $as = JJ_Advanced_Styling::instance();
                if ( $as && method_exists( $as, 'get_breakpoints' ) ) {
                    $bps = $as->get_breakpoints();
                    return is_array( $bps ) ? $bps : array();
                }
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        return array();
    }

    /**
     * 브레이크포인트 배열을 CSS 캐스케이드에 유리한 순서로 정렬합니다.
     *
     * @param array $breakpoints
     * @return string[] breakpoint keys
     */
    private function order_breakpoints_for_css( $breakpoints ) {
        $max_only = array();
        $min_only = array();
        $range    = array();

        foreach ( (array) $breakpoints as $k => $bp ) {
            if ( ! is_array( $bp ) ) {
                continue;
            }
            $has_min = isset( $bp['min'] ) && is_numeric( $bp['min'] );
            $has_max = isset( $bp['max'] ) && is_numeric( $bp['max'] );
            if ( $has_min && $has_max ) {
                $range[ $k ] = $bp;
            } elseif ( $has_max ) {
                $max_only[ $k ] = $bp;
            } elseif ( $has_min ) {
                $min_only[ $k ] = $bp;
            }
        }

        // max-only: 큰 max → 작은 max
        uasort( $max_only, function( $a, $b ) {
            return (int) ( $b['max'] ?? 0 ) <=> (int) ( $a['max'] ?? 0 );
        } );
        // range: 큰 max → 작은 max (겹치는 경우 작은 범위가 뒤에 오도록)
        uasort( $range, function( $a, $b ) {
            return (int) ( $b['max'] ?? 0 ) <=> (int) ( $a['max'] ?? 0 );
        } );
        // min-only: 작은 min → 큰 min
        uasort( $min_only, function( $a, $b ) {
            return (int) ( $a['min'] ?? 0 ) <=> (int) ( $b['min'] ?? 0 );
        } );

        return array_merge( array_keys( $range ), array_keys( $max_only ), array_keys( $min_only ) );
    }

    /**
     * breakpoints 항목(min/max)으로부터 @media 조건 문자열을 생성합니다.
     */
    private function build_media_query( $breakpoint ) {
        if ( ! is_array( $breakpoint ) ) {
            return '';
        }
        $has_min = isset( $breakpoint['min'] ) && is_numeric( $breakpoint['min'] );
        $has_max = isset( $breakpoint['max'] ) && is_numeric( $breakpoint['max'] );

        if ( $has_min && $has_max ) {
            return sprintf( '(min-width: %dpx) and (max-width: %dpx)', (int) $breakpoint['min'], (int) $breakpoint['max'] );
        }
        if ( $has_min ) {
            return sprintf( '(min-width: %dpx)', (int) $breakpoint['min'] );
        }
        if ( $has_max ) {
            return sprintf( '(max-width: %dpx)', (int) $breakpoint['max'] );
        }
        return '';
    }

    /**
     * 특정 브레이크포인트 키에 대한 font_size 값을 찾습니다(하위 호환/폴백 포함).
     *
     * @param array $props typography tag props
     * @param string $bp_key
     * @return string
     */
    private function resolve_font_size_for_breakpoint( $props, $bp_key ) {
        if ( ! is_array( $props ) ) {
            return '';
        }
        $sizes = isset( $props['font_size'] ) && is_array( $props['font_size'] ) ? $props['font_size'] : array();

        // 1) 명시값 우선
        if ( isset( $sizes[ $bp_key ] ) && $sizes[ $bp_key ] !== '' ) {
            return (string) $sizes[ $bp_key ];
        }

        // 1b) 초고해상도 폴백 체인
        if ( 'desktop_8k' === $bp_key ) {
            foreach ( array( 'desktop_5k', 'desktop_uhd', 'desktop_qhd', 'desktop' ) as $k ) {
                if ( isset( $sizes[ $k ] ) && $sizes[ $k ] !== '' ) {
                    return (string) $sizes[ $k ];
                }
            }
        }
        if ( 'desktop_5k' === $bp_key ) {
            foreach ( array( 'desktop_uhd', 'desktop_qhd', 'desktop' ) as $k ) {
                if ( isset( $sizes[ $k ] ) && $sizes[ $k ] !== '' ) {
                    return (string) $sizes[ $k ];
                }
            }
        }
        if ( 'desktop_uhd' === $bp_key ) {
            foreach ( array( 'desktop_qhd', 'desktop' ) as $k ) {
                if ( isset( $sizes[ $k ] ) && $sizes[ $k ] !== '' ) {
                    return (string) $sizes[ $k ];
                }
            }
        }
        if ( 'desktop_qhd' === $bp_key ) {
            if ( isset( $sizes['desktop'] ) && $sizes['desktop'] !== '' ) {
                return (string) $sizes['desktop'];
            }
        }

        // 2) 하위 호환 폴백: phablet은 기존 mobile 값을 기본으로 사용(과거 <=767 구간 유지 목적)
        if ( 'phablet' === $bp_key && isset( $sizes['mobile'] ) && $sizes['mobile'] !== '' ) {
            return (string) $sizes['mobile'];
        }

        // 3) phone_small은 mobile(스마트폰) → phablet 순으로 폴백
        if ( 'phone_small' === $bp_key ) {
            if ( isset( $sizes['mobile'] ) && $sizes['mobile'] !== '' ) {
                return (string) $sizes['mobile'];
            }
            if ( isset( $sizes['phablet'] ) && $sizes['phablet'] !== '' ) {
                return (string) $sizes['phablet'];
            }
        }

        // 4) mobile(스마트폰)은 phablet 값이 있으면 폴백 (사용자가 phablet만 입력한 경우)
        if ( 'mobile' === $bp_key && isset( $sizes['phablet'] ) && $sizes['phablet'] !== '' ) {
            return (string) $sizes['phablet'];
        }

        return '';
    }
}