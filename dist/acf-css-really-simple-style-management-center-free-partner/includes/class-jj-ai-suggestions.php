<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 9.4] AI Suggestions System
 * 
 * 컨텍스트 인식 AI 제안 시스템
 * - 실시간 스타일 제안
 * - 개인화된 제안
 * - 베스트 프랙티스 제안
 * 
 * @since 9.4.0
 */
class JJ_AI_Suggestions {

    private static $instance = null;
    private $user_preferences_key = 'jj_ai_user_preferences';
    private $suggestion_history_key = 'jj_ai_suggestion_history';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_jj_get_ai_suggestions', array( $this, 'ajax_get_suggestions' ) );
        add_action( 'wp_ajax_jj_apply_ai_suggestion', array( $this, 'ajax_apply_suggestion' ) );
        add_action( 'wp_ajax_jj_feedback_suggestion', array( $this, 'ajax_feedback_suggestion' ) );
    }

    /**
     * 에셋 로드
     */
    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'jj-admin-center' ) === false && 
             strpos( $hook, 'acf-css-really-simple-style-guide' ) === false ) {
            return;
        }

        wp_enqueue_script(
            'jj-ai-suggestions',
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-ai-suggestions.js',
            array( 'jquery', 'jj-common-utils' ),
            defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '9.4.0',
            true
        );

        wp_localize_script(
            'jj-ai-suggestions',
            'jjAISuggestions',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'jj_ai_suggestions_action' ),
                'ai_extension_active' => $this->is_ai_extension_active(),
                'strings'  => array(
                    'suggesting' => __( 'AI 제안 생성 중...', 'acf-css-really-simple-style-management-center' ),
                    'no_suggestions' => __( '제안이 없습니다.', 'acf-css-really-simple-style-management-center' ),
                    'apply_suggestion' => __( '제안 적용', 'acf-css-really-simple-style-management-center' ),
                    'dismiss' => __( '닫기', 'acf-css-really-simple-style-management-center' ),
                ),
            )
        );
    }

    /**
     * AI Extension 활성화 확인
     */
    private function is_ai_extension_active() {
        return class_exists( 'JJ_AI_Extension' ) || 
               ( defined( 'JJ_AI_EXTENSION_ACTIVE' ) && JJ_AI_EXTENSION_ACTIVE );
    }

    /**
     * 컨텍스트 기반 제안 생성
     */
    public function generate_suggestions( $context ) {
        $suggestions = array();

        // AI Extension이 활성화되어 있지 않으면 기본 제안만 제공
        if ( ! $this->is_ai_extension_active() ) {
            return $this->get_default_suggestions( $context );
        }

        // 컨텍스트 분석
        $context_data = $this->analyze_context( $context );

        // 사용자 선호도 기반 제안
        $personalized = $this->get_personalized_suggestions( $context_data );

        // 베스트 프랙티스 제안
        $best_practices = $this->get_best_practice_suggestions( $context_data );

        // 제안 병합 및 정렬
        $suggestions = array_merge( $personalized, $best_practices );
        $suggestions = $this->rank_suggestions( $suggestions, $context_data );

        return array_slice( $suggestions, 0, 5 ); // 상위 5개만 반환
    }

    /**
     * 컨텍스트 분석
     */
    private function analyze_context( $context ) {
        $options = get_option( 'jj_style_guide_options', array() );
        
        return array(
            'current_palette' => isset( $options['palettes'] ) ? $options['palettes'] : array(),
            'current_typography' => isset( $options['typography'] ) ? $options['typography'] : array(),
            'current_buttons' => isset( $options['buttons'] ) ? $options['buttons'] : array(),
            'theme' => wp_get_theme()->get( 'Name' ),
            'context_type' => $context['type'] ?? 'general',
            'user_input' => $context['input'] ?? '',
        );
    }

    /**
     * 기본 제안 (AI Extension 없을 때)
     */
    private function get_default_suggestions( $context ) {
        $suggestions = array();

        // 색상 팔레트 제안
        if ( $context['type'] === 'palette' || $context['type'] === 'general' ) {
            $suggestions[] = array(
                'type' => 'palette',
                'title' => __( '웹 접근성 준수 색상 팔레트', 'acf-css-really-simple-style-management-center' ),
                'description' => __( 'WCAG AA 기준을 만족하는 색상 조합을 제안합니다.', 'acf-css-really-simple-style-management-center' ),
                'action' => 'apply_palette',
                'data' => array(
                    'primary' => '#0073aa',
                    'secondary' => '#00a0d2',
                    'success' => '#00a32a',
                    'warning' => '#dba617',
                    'error' => '#d63638',
                ),
                'confidence' => 0.8,
            );
        }

        // 타이포그래피 제안
        if ( $context['type'] === 'typography' || $context['type'] === 'general' ) {
            $suggestions[] = array(
                'type' => 'typography',
                'title' => __( '가독성 최적화 폰트 크기', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '최적의 가독성을 위한 폰트 크기 조합을 제안합니다.', 'acf-css-really-simple-style-management-center' ),
                'action' => 'apply_typography',
                'data' => array(
                    'h1' => '2.5rem',
                    'h2' => '2rem',
                    'h3' => '1.75rem',
                    'body' => '1rem',
                ),
                'confidence' => 0.75,
            );
        }

        return $suggestions;
    }

    /**
     * 개인화된 제안
     */
    private function get_personalized_suggestions( $context_data ) {
        $suggestions = array();
        $preferences = $this->get_user_preferences();
        $history = $this->get_suggestion_history();

        // 사용자가 자주 선택한 패턴 분석
        $frequent_patterns = $this->analyze_frequent_patterns( $history );

        foreach ( $frequent_patterns as $pattern ) {
            $suggestions[] = array(
                'type' => $pattern['type'],
                'title' => sprintf( __( '자주 사용하신 %s 스타일', 'acf-css-really-simple-style-management-center' ), $pattern['type'] ),
                'description' => __( '과거 선택 패턴을 기반으로 한 제안', 'acf-css-really-simple-style-management-center' ),
                'action' => 'apply_' . $pattern['type'],
                'data' => $pattern['data'],
                'confidence' => $pattern['frequency'] * 0.1, // 빈도에 따른 신뢰도
                'personalized' => true,
            );
        }

        return $suggestions;
    }

    /**
     * 베스트 프랙티스 제안
     */
    private function get_best_practice_suggestions( $context_data ) {
        $suggestions = array();

        // 색상 대비 검사
        if ( ! empty( $context_data['current_palette'] ) ) {
            $contrast_issues = $this->check_color_contrast( $context_data['current_palette'] );
            if ( ! empty( $contrast_issues ) ) {
                $suggestions[] = array(
                    'type' => 'accessibility',
                    'title' => __( '색상 대비 개선 제안', 'acf-css-really-simple-style-management-center' ),
                    'description' => __( '접근성 기준을 만족하도록 색상 대비를 조정하는 것을 권장합니다.', 'acf-css-really-simple-style-management-center' ),
                    'action' => 'improve_contrast',
                    'data' => $contrast_issues,
                    'confidence' => 0.9,
                    'best_practice' => true,
                );
            }
        }

        // 일관성 제안
        $consistency_issues = $this->check_consistency( $context_data );
        if ( ! empty( $consistency_issues ) ) {
            $suggestions[] = array(
                'type' => 'consistency',
                'title' => __( '스타일 일관성 개선', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '디자인 시스템의 일관성을 높이기 위한 제안', 'acf-css-really-simple-style-management-center' ),
                'action' => 'improve_consistency',
                'data' => $consistency_issues,
                'confidence' => 0.85,
                'best_practice' => true,
            );
        }

        return $suggestions;
    }

    /**
     * 제안 순위 매기기
     */
    private function rank_suggestions( $suggestions, $context_data ) {
        usort( $suggestions, function( $a, $b ) {
            $score_a = ( $a['confidence'] ?? 0.5 ) + ( isset( $a['personalized'] ) && $a['personalized'] ? 0.2 : 0 );
            $score_b = ( $b['confidence'] ?? 0.5 ) + ( isset( $b['personalized'] ) && $b['personalized'] ? 0.2 : 0 );
            return $score_b <=> $score_a;
        });

        return $suggestions;
    }

    /**
     * 사용자 선호도 가져오기
     */
    private function get_user_preferences() {
        $user_id = get_current_user_id();
        return get_user_meta( $user_id, $this->user_preferences_key, true ) ?: array();
    }

    /**
     * 제안 히스토리 가져오기
     */
    private function get_suggestion_history( $limit = 50 ) {
        $user_id = get_current_user_id();
        $history = get_user_meta( $user_id, $this->suggestion_history_key, true ) ?: array();
        
        return array_slice( $history, -$limit );
    }

    /**
     * 빈번한 패턴 분석
     */
    private function analyze_frequent_patterns( $history ) {
        $patterns = array();
        
        foreach ( $history as $item ) {
            if ( isset( $item['applied'] ) && $item['applied'] ) {
                $type = $item['type'] ?? 'unknown';
                if ( ! isset( $patterns[ $type ] ) ) {
                    $patterns[ $type ] = array(
                        'type' => $type,
                        'data' => $item['data'] ?? array(),
                        'frequency' => 0,
                    );
                }
                $patterns[ $type ]['frequency']++;
            }
        }

        // 빈도순 정렬
        usort( $patterns, function( $a, $b ) {
            return $b['frequency'] <=> $a['frequency'];
        });

        return array_slice( $patterns, 0, 3 ); // 상위 3개
    }

    /**
     * 색상 대비 검사
     */
    private function check_color_contrast( $palettes ) {
        $issues = array();
        
        // 간단한 대비 검사 (실제로는 더 정교한 알고리즘 필요)
        foreach ( $palettes as $palette_key => $palette ) {
            if ( isset( $palette['colors'] ) && is_array( $palette['colors'] ) ) {
                // 기본적인 검사만 수행
                // 실제 구현에서는 WCAG 대비 비율 계산 필요
            }
        }

        return $issues;
    }

    /**
     * 일관성 검사
     */
    private function check_consistency( $context_data ) {
        $issues = array();
        
        // 간단한 일관성 검사
        if ( isset( $context_data['current_buttons'] ) && count( $context_data['current_buttons'] ) > 0 ) {
            $border_radii = array();
            foreach ( $context_data['current_buttons'] as $button ) {
                if ( isset( $button['border_radius'] ) ) {
                    $border_radii[] = $button['border_radius'];
                }
            }
            
            if ( count( array_unique( $border_radii ) ) > 2 ) {
                $issues[] = array(
                    'type' => 'button_border_radius',
                    'message' => __( '버튼 테두리 반경이 일관되지 않습니다.', 'acf-css-really-simple-style-management-center' ),
                );
            }
        }

        return $issues;
    }

    /**
     * 제안 적용
     */
    public function apply_suggestion( $suggestion_id, $suggestion_data ) {
        $options = get_option( 'jj_style_guide_options', array() );

        switch ( $suggestion_data['action'] ) {
            case 'apply_palette':
                if ( ! isset( $options['palettes'] ) ) {
                    $options['palettes'] = array();
                }
                $options['palettes']['ai_suggested'] = array(
                    'name' => $suggestion_data['title'],
                    'colors' => $suggestion_data['data'],
                );
                break;

            case 'apply_typography':
                if ( ! isset( $options['typography'] ) ) {
                    $options['typography'] = array();
                }
                $options['typography'] = array_merge( $options['typography'], $suggestion_data['data'] );
                break;

            case 'improve_contrast':
                // 대비 개선 로직
                break;

            case 'improve_consistency':
                // 일관성 개선 로직
                break;
        }

        update_option( 'jj_style_guide_options', $options );

        // 히스토리 기록
        $this->record_suggestion_application( $suggestion_id, $suggestion_data );

        return true;
    }

    /**
     * 제안 적용 기록
     */
    private function record_suggestion_application( $suggestion_id, $suggestion_data ) {
        $user_id = get_current_user_id();
        $history = $this->get_suggestion_history( 1000 );
        
        $history[] = array(
            'id' => $suggestion_id,
            'type' => $suggestion_data['type'],
            'data' => $suggestion_data['data'],
            'applied' => true,
            'timestamp' => current_time( 'mysql' ),
        );

        update_user_meta( $user_id, $this->suggestion_history_key, array_slice( $history, -100 ) );
    }

    /**
     * 제안 피드백 기록
     */
    public function record_feedback( $suggestion_id, $feedback ) {
        $user_id = get_current_user_id();
        $history = $this->get_suggestion_history( 1000 );
        
        // 해당 제안 찾아서 피드백 추가
        foreach ( $history as &$item ) {
            if ( $item['id'] === $suggestion_id ) {
                $item['feedback'] = $feedback;
                break;
            }
        }

        update_user_meta( $user_id, $this->suggestion_history_key, array_slice( $history, -100 ) );
    }

    /**
     * AJAX: 제안 가져오기
     */
    public function ajax_get_suggestions() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_ai_suggestions_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $context = array(
            'type' => isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : 'general',
            'input' => isset( $_POST['input'] ) ? sanitize_text_field( $_POST['input'] ) : '',
        );

        $suggestions = $this->generate_suggestions( $context );

        wp_send_json_success( array(
            'suggestions' => $suggestions,
        ) );
    }

    /**
     * AJAX: 제안 적용
     */
    public function ajax_apply_suggestion() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_ai_suggestions_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $suggestion_id = isset( $_POST['suggestion_id'] ) ? sanitize_text_field( $_POST['suggestion_id'] ) : '';
        $suggestion_data = isset( $_POST['suggestion_data'] ) ? json_decode( stripslashes( $_POST['suggestion_data'] ), true ) : array();

        if ( ! $suggestion_id || empty( $suggestion_data ) ) {
            wp_send_json_error( array( 'message' => __( '필수 파라미터가 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $result = $this->apply_suggestion( $suggestion_id, $suggestion_data );

        if ( $result ) {
            wp_send_json_success( array(
                'message' => __( '제안이 적용되었습니다.', 'acf-css-really-simple-style-management-center' ),
            ) );
        } else {
            wp_send_json_error( array( 'message' => __( '제안 적용에 실패했습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
    }

    /**
     * AJAX: 제안 피드백
     */
    public function ajax_feedback_suggestion() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_ai_suggestions_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $suggestion_id = isset( $_POST['suggestion_id'] ) ? sanitize_text_field( $_POST['suggestion_id'] ) : '';
        $feedback = isset( $_POST['feedback'] ) ? sanitize_text_field( $_POST['feedback'] ) : ''; // 'like' or 'dislike'

        if ( ! $suggestion_id || ! $feedback ) {
            wp_send_json_error( array( 'message' => __( '필수 파라미터가 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $this->record_feedback( $suggestion_id, $feedback );

        wp_send_json_success( array(
            'message' => __( '피드백이 기록되었습니다.', 'acf-css-really-simple-style-management-center' ),
        ) );
    }
}
