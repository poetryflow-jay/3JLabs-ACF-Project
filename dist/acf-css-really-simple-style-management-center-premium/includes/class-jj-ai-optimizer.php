<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 9.4] AI Style Optimizer
 * 
 * 자동 스타일 최적화 시스템
 * - 성능 최적화 제안
 * - 접근성 자동 개선
 * 
 * @since 9.4.0
 */
class JJ_AI_Optimizer {

    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_jj_optimize_performance', array( $this, 'ajax_optimize_performance' ) );
        add_action( 'wp_ajax_jj_improve_accessibility', array( $this, 'ajax_improve_accessibility' ) );
    }

    /**
     * 에셋 로드
     */
    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'jj-admin-center' ) === false ) {
            return;
        }

        wp_enqueue_script(
            'jj-ai-optimizer',
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-ai-optimizer.js',
            array( 'jquery', 'jj-common-utils' ),
            defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '9.4.0',
            true
        );

        wp_localize_script(
            'jj-ai-optimizer',
            'jjAIOptimizer',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'jj_ai_optimizer_action' ),
                'strings'  => array(
                    'optimizing' => __( '최적화 중...', 'acf-css-really-simple-style-management-center' ),
                    'optimized' => __( '최적화가 완료되었습니다.', 'acf-css-really-simple-style-management-center' ),
                ),
            )
        );
    }

    /**
     * 성능 최적화 분석
     */
    public function analyze_performance() {
        $options = get_option( 'jj_style_guide_options', array() );
        $suggestions = array();

        // 1. 불필요한 CSS 검사
        $unused_css = $this->detect_unused_css( $options );
        if ( ! empty( $unused_css ) ) {
            $suggestions[] = array(
                'type' => 'remove_unused_css',
                'title' => __( '사용되지 않는 CSS 제거', 'acf-css-really-simple-style-management-center' ),
                'description' => sprintf( __( '%d개의 사용되지 않는 스타일 규칙을 제거할 수 있습니다.', 'acf-css-really-simple-style-management-center' ), count( $unused_css ) ),
                'data' => $unused_css,
                'impact' => 'medium',
            );
        }

        // 2. 중복 스타일 검사
        $duplicate_styles = $this->detect_duplicate_styles( $options );
        if ( ! empty( $duplicate_styles ) ) {
            $suggestions[] = array(
                'type' => 'merge_duplicates',
                'title' => __( '중복 스타일 통합', 'acf-css-really-simple-style-management-center' ),
                'description' => sprintf( __( '%d개의 중복 스타일을 통합할 수 있습니다.', 'acf-css-really-simple-style-management-center' ), count( $duplicate_styles ) ),
                'data' => $duplicate_styles,
                'impact' => 'low',
            );
        }

        // 3. 캐싱 전략 제안
        $caching_suggestions = $this->suggest_caching_strategy();
        if ( ! empty( $caching_suggestions ) ) {
            $suggestions = array_merge( $suggestions, $caching_suggestions );
        }

        return array(
            'suggestions' => $suggestions,
            'score' => $this->calculate_performance_score( $suggestions ),
        );
    }

    /**
     * 접근성 개선 분석
     */
    public function analyze_accessibility() {
        $options = get_option( 'jj_style_guide_options', array() );
        $improvements = array();

        // 1. 색상 대비 검사
        $contrast_issues = $this->check_color_contrast( $options );
        if ( ! empty( $contrast_issues ) ) {
            $improvements[] = array(
                'type' => 'improve_contrast',
                'title' => __( '색상 대비 개선', 'acf-css-really-simple-style-management-center' ),
                'description' => __( 'WCAG AA 기준을 만족하도록 색상 대비를 조정합니다.', 'acf-css-really-simple-style-management-center' ),
                'data' => $contrast_issues,
                'auto_fixable' => true,
            );
        }

        // 2. 폰트 크기 검사
        $font_size_issues = $this->check_font_sizes( $options );
        if ( ! empty( $font_size_issues ) ) {
            $improvements[] = array(
                'type' => 'improve_font_sizes',
                'title' => __( '폰트 크기 개선', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '가독성을 위해 최소 폰트 크기를 조정합니다.', 'acf-css-really-simple-style-management-center' ),
                'data' => $font_size_issues,
                'auto_fixable' => true,
            );
        }

        // 3. 포커스 표시 검사
        $focus_issues = $this->check_focus_indicators( $options );
        if ( ! empty( $focus_issues ) ) {
            $improvements[] = array(
                'type' => 'add_focus_indicators',
                'title' => __( '포커스 표시 추가', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '키보드 네비게이션을 위한 포커스 표시를 추가합니다.', 'acf-css-really-simple-style-management-center' ),
                'data' => $focus_issues,
                'auto_fixable' => true,
            );
        }

        return array(
            'improvements' => $improvements,
            'score' => $this->calculate_accessibility_score( $improvements ),
        );
    }

    /**
     * 사용되지 않는 CSS 감지
     */
    private function detect_unused_css( $options ) {
        $unused = array();
        
        // 간단한 검사 (실제로는 더 정교한 분석 필요)
        // 예: 정의되었지만 실제로 사용되지 않는 스타일
        
        return $unused;
    }

    /**
     * 중복 스타일 감지
     */
    private function detect_duplicate_styles( $options ) {
        $duplicates = array();
        
        // 버튼 스타일 중복 검사
        if ( isset( $options['buttons'] ) && is_array( $options['buttons'] ) ) {
            $button_styles = array();
            foreach ( $options['buttons'] as $key => $button ) {
                $style_hash = md5( serialize( array(
                    'background_color' => $button['background_color'] ?? '',
                    'text_color' => $button['text_color'] ?? '',
                    'border_radius' => $button['border_radius'] ?? '',
                ) ) );
                
                if ( isset( $button_styles[ $style_hash ] ) ) {
                    $duplicates[] = array(
                        'type' => 'button',
                        'keys' => array( $button_styles[ $style_hash ], $key ),
                    );
                } else {
                    $button_styles[ $style_hash ] = $key;
                }
            }
        }

        return $duplicates;
    }

    /**
     * 캐싱 전략 제안
     */
    private function suggest_caching_strategy() {
        $suggestions = array();
        
        // CSS 캐시 활성화 확인
        if ( ! class_exists( 'JJ_CSS_Cache' ) || ! JJ_CSS_Cache::instance()->is_enabled() ) {
            $suggestions[] = array(
                'type' => 'enable_css_cache',
                'title' => __( 'CSS 캐시 활성화', 'acf-css-really-simple-style-management-center' ),
                'description' => __( 'CSS 캐시를 활성화하여 성능을 향상시킬 수 있습니다.', 'acf-css-really-simple-style-management-center' ),
                'data' => array(),
                'impact' => 'high',
            );
        }

        return $suggestions;
    }

    /**
     * 색상 대비 검사
     */
    private function check_color_contrast( $options ) {
        $issues = array();
        
        // 간단한 대비 검사
        if ( isset( $options['palettes'] ) && is_array( $options['palettes'] ) ) {
            foreach ( $options['palettes'] as $palette_key => $palette ) {
                if ( isset( $palette['colors'] ) && is_array( $palette['colors'] ) ) {
                    // 기본 색상과 텍스트 색상 대비 검사
                    // 실제 구현에서는 WCAG 대비 비율 계산 필요
                }
            }
        }

        return $issues;
    }

    /**
     * 폰트 크기 검사
     */
    private function check_font_sizes( $options ) {
        $issues = array();
        
        if ( isset( $options['typography'] ) && is_array( $options['typography'] ) ) {
            foreach ( $options['typography'] as $key => $typo ) {
                $font_size = $typo['font_size'] ?? '';
                // 최소 14px 권장
                if ( $font_size && preg_match( '/(\d+)px/', $font_size, $matches ) ) {
                    if ( intval( $matches[1] ) < 14 ) {
                        $issues[] = array(
                            'key' => $key,
                            'current' => $font_size,
                            'recommended' => '14px',
                        );
                    }
                }
            }
        }

        return $issues;
    }

    /**
     * 포커스 표시 검사
     */
    private function check_focus_indicators( $options ) {
        $issues = array();
        
        // 버튼에 포커스 스타일이 있는지 확인
        if ( isset( $options['buttons'] ) && is_array( $options['buttons'] ) ) {
            foreach ( $options['buttons'] as $key => $button ) {
                if ( ! isset( $button['focus_style'] ) || empty( $button['focus_style'] ) ) {
                    $issues[] = array(
                        'key' => $key,
                        'type' => 'button',
                    );
                }
            }
        }

        return $issues;
    }

    /**
     * 성능 점수 계산
     */
    private function calculate_performance_score( $suggestions ) {
        $base_score = 100;
        $deduction = count( $suggestions ) * 10;
        return max( 0, $base_score - $deduction );
    }

    /**
     * 접근성 점수 계산
     */
    private function calculate_accessibility_score( $improvements ) {
        $base_score = 100;
        $deduction = count( $improvements ) * 15;
        return max( 0, $base_score - $deduction );
    }

    /**
     * 자동 최적화 적용
     */
    public function apply_auto_optimization( $type, $data ) {
        $options = get_option( 'jj_style_guide_options', array() );

        switch ( $type ) {
            case 'improve_contrast':
                // 색상 대비 자동 조정
                break;

            case 'improve_font_sizes':
                // 폰트 크기 자동 조정
                if ( isset( $data ) && is_array( $data ) ) {
                    foreach ( $data as $issue ) {
                        if ( isset( $options['typography'][ $issue['key'] ] ) ) {
                            $options['typography'][ $issue['key'] ]['font_size'] = $issue['recommended'];
                        }
                    }
                }
                break;

            case 'add_focus_indicators':
                // 포커스 표시 자동 추가
                if ( isset( $data ) && is_array( $data ) ) {
                    foreach ( $data as $issue ) {
                        if ( isset( $options['buttons'][ $issue['key'] ] ) ) {
                            $options['buttons'][ $issue['key'] ]['focus_style'] = 'outline: 2px solid #0073aa; outline-offset: 2px;';
                        }
                    }
                }
                break;
        }

        update_option( 'jj_style_guide_options', $options );
        return true;
    }

    /**
     * AJAX: 성능 최적화
     */
    public function ajax_optimize_performance() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_ai_optimizer_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $analysis = $this->analyze_performance();

        wp_send_json_success( $analysis );
    }

    /**
     * AJAX: 접근성 개선
     */
    public function ajax_improve_accessibility() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_ai_optimizer_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $auto_apply = isset( $_POST['auto_apply'] ) && $_POST['auto_apply'] === 'true';
        $analysis = $this->analyze_accessibility();

        if ( $auto_apply && ! empty( $analysis['improvements'] ) ) {
            foreach ( $analysis['improvements'] as $improvement ) {
                if ( isset( $improvement['auto_fixable'] ) && $improvement['auto_fixable'] ) {
                    $this->apply_auto_optimization( $improvement['type'], $improvement['data'] );
                }
            }
        }

        wp_send_json_success( $analysis );
    }
}
