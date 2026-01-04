<?php
/**
 * ACF Code Snippets Box - Code Analyzer
 *
 * 코드 정적 분석, 의존성 분석, 코드 품질 점수 계산
 *
 * @package ACF_Code_Snippets_Box
 * @since 4.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Code Analyzer 클래스
 */
class ACF_CSB_Code_Analyzer {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 싱글톤 인스턴스 반환
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 생성자
     */
    private function __construct() {
        add_action( 'wp_ajax_acf_csb_analyze_code', array( $this, 'ajax_analyze_code' ) );
        add_action( 'wp_ajax_acf_csb_get_code_quality_score', array( $this, 'ajax_get_quality_score' ) );
        add_action( 'wp_ajax_acf_csb_find_dependencies', array( $this, 'ajax_find_dependencies' ) );
    }

    /**
     * 코드 분석 (종합)
     * 
     * @param string $code 코드
     * @param string $code_type 코드 타입 (css, js, php, html)
     * @return array 분석 결과
     */
    public function analyze( $code, $code_type = 'css' ) {
        $analysis = array(
            'code_type'        => $code_type,
            'lines'            => substr_count( $code, "\n" ) + 1,
            'characters'       => strlen( $code ),
            'complexity'       => 0,
            'quality_score'    => 0,
            'issues'           => array(),
            'suggestions'      => array(),
            'dependencies'    => array(),
            'performance'      => array(),
            'security'         => array(),
        );

        switch ( $code_type ) {
            case 'css':
                $analysis = array_merge( $analysis, $this->analyze_css( $code ) );
                break;
            case 'js':
                $analysis = array_merge( $analysis, $this->analyze_javascript( $code ) );
                break;
            case 'php':
                $analysis = array_merge( $analysis, $this->analyze_php( $code ) );
                break;
            case 'html':
                $analysis = array_merge( $analysis, $this->analyze_html( $code ) );
                break;
        }

        // 코드 품질 점수 계산
        $analysis['quality_score'] = $this->calculate_quality_score( $analysis );

        return $analysis;
    }

    /**
     * CSS 분석
     */
    private function analyze_css( $code ) {
        $analysis = array(
            'selectors'        => 0,
            'properties'       => 0,
            'media_queries'    => 0,
            'animations'       => 0,
            'variables'        => 0,
            'important_count' => 0,
            'specificity_issues' => array(),
            'unused_selectors' => array(),
        );

        // 선택자 개수
        preg_match_all( '/[^{}]+(?=\s*\{)/', $code, $selectors );
        $analysis['selectors'] = count( $selectors[0] );

        // 속성 개수
        preg_match_all( '/[a-z-]+(?=\s*:)/i', $code, $properties );
        $analysis['properties'] = count( $properties[0] );

        // 미디어 쿼리
        preg_match_all( '/@media\s+[^{]+/', $code, $media_queries );
        $analysis['media_queries'] = count( $media_queries[0] );

        // 애니메이션
        preg_match_all( '/@(keyframes|animation)/i', $code, $animations );
        $analysis['animations'] = count( $animations[0] );

        // CSS 변수
        preg_match_all( '/--[a-z0-9-]+/i', $code, $variables );
        $analysis['variables'] = count( array_unique( $variables[0] ) );

        // !important 사용 횟수
        preg_match_all( '/!\s*important/i', $code, $important );
        $analysis['important_count'] = count( $important[0] );

        // 문제점 발견
        if ( $analysis['important_count'] > 5 ) {
            $analysis['issues'][] = array(
                'type'    => 'warning',
                'message' => sprintf( __( '!important이 %d번 사용되었습니다. CSS 우선순위 관리에 문제가 있을 수 있습니다.', 'acf-code-snippets-box' ), $analysis['important_count'] ),
            );
        }

        // 중복 선택자 체크
        $selector_lines = preg_split( '/\n/', $code );
        $seen_selectors = array();
        foreach ( $selector_lines as $line_num => $line ) {
            if ( preg_match( '/([^{}]+)(?=\s*\{)/', $line, $match ) ) {
                $selector = trim( $match[1] );
                if ( isset( $seen_selectors[ $selector ] ) ) {
                    $analysis['issues'][] = array(
                        'type'    => 'info',
                        'line'    => $line_num + 1,
                        'message' => sprintf( __( '중복 선택자: %s', 'acf-code-snippets-box' ), $selector ),
                    );
                }
                $seen_selectors[ $selector ] = true;
            }
        }

        // 제안사항
        if ( $analysis['variables'] === 0 && $analysis['selectors'] > 10 ) {
            $analysis['suggestions'][] = __( 'CSS 변수를 사용하여 반복되는 값을 관리하는 것을 고려하세요.', 'acf-code-snippets-box' );
        }

        if ( $analysis['media_queries'] === 0 && $analysis['selectors'] > 5 ) {
            $analysis['suggestions'][] = __( '반응형 디자인을 위해 미디어 쿼리를 추가하는 것을 고려하세요.', 'acf-code-snippets-box' );
        }

        // 성능 분석
        $analysis['performance'] = array(
            'file_size_kb'    => round( strlen( $code ) / 1024, 2 ),
            'selectors_per_file' => $analysis['selectors'],
            'recommendation'  => $this->get_css_performance_recommendation( $analysis ),
        );

        return $analysis;
    }

    /**
     * JavaScript 분석
     */
    private function analyze_javascript( $code ) {
        $analysis = array(
            'functions'        => 0,
            'variables'        => 0,
            'complexity'       => 0,
            'async_operations' => 0,
            'jquery_usage'     => false,
            'dom_manipulations' => 0,
            'event_listeners'  => 0,
        );

        // 함수 개수
        preg_match_all( '/function\s+\w+|const\s+\w+\s*=\s*\(|let\s+\w+\s*=\s*\(|=>\s*{/', $code, $functions );
        $analysis['functions'] = count( $functions[0] );

        // 변수 개수
        preg_match_all( '/(var|let|const)\s+\w+/', $code, $variables );
        $analysis['variables'] = count( $variables[0] );

        // 순환 복잡도 계산 (간단한 버전)
        $analysis['complexity'] = $this->calculate_cyclomatic_complexity( $code );

        // 비동기 작업
        preg_match_all( '/(async|await|Promise|fetch|setTimeout|setInterval)/', $code, $async );
        $analysis['async_operations'] = count( $async[0] );

        // jQuery 사용 여부
        $analysis['jquery_usage'] = strpos( $code, '$(' ) !== false || strpos( $code, 'jQuery(' ) !== false;

        // DOM 조작
        preg_match_all( '/(\.innerHTML|\.textContent|\.appendChild|\.removeChild|\.createElement)/', $code, $dom );
        $analysis['dom_manipulations'] = count( $dom[0] );

        // 이벤트 리스너
        preg_match_all( '/(addEventListener|\.on\(|\.click\(|\.change\(|\.submit\()/', $code, $events );
        $analysis['event_listeners'] = count( $events[0] );

        // 문제점 발견
        if ( $analysis['complexity'] > 20 ) {
            $analysis['issues'][] = array(
                'type'    => 'warning',
                'message' => sprintf( __( '순환 복잡도가 높습니다 (%d). 코드를 더 작은 함수로 분리하는 것을 권장합니다.', 'acf-code-snippets-box' ), $analysis['complexity'] ),
            );
        }

        // console.log 경고
        if ( preg_match_all( '/console\.(log|warn|error|debug)/', $code, $console ) ) {
            $analysis['issues'][] = array(
                'type'    => 'warning',
                'message' => sprintf( __( 'console.%s()가 %d번 사용되었습니다. 프로덕션에서는 제거하는 것을 권장합니다.', 'acf-code-snippets-box' ), 'log', count( $console[0] ) ),
            );
        }

        // 제안사항
        if ( $analysis['jquery_usage'] && ! $analysis['async_operations'] ) {
            $analysis['suggestions'][] = __( 'jQuery를 사용하고 있습니다. 최신 JavaScript (ES6+)로 마이그레이션을 고려하세요.', 'acf-code-snippets-box' );
        }

        // 의존성 분석
        $analysis['dependencies'] = $this->find_javascript_dependencies( $code );

        // 성능 분석
        $analysis['performance'] = array(
            'file_size_kb'    => round( strlen( $code ) / 1024, 2 ),
            'execution_time_estimate' => $this->estimate_execution_time( $analysis ),
            'recommendation'  => $this->get_js_performance_recommendation( $analysis ),
        );

        return $analysis;
    }

    /**
     * PHP 분석
     */
    private function analyze_php( $code ) {
        $analysis = array(
            'functions'        => 0,
            'classes'          => 0,
            'complexity'       => 0,
            'database_queries' => 0,
            'security_issues'  => array(),
        );

        // 함수 개수
        preg_match_all( '/function\s+\w+\s*\(/', $code, $functions );
        $analysis['functions'] = count( $functions[0] );

        // 클래스 개수
        preg_match_all( '/class\s+\w+/', $code, $classes );
        $analysis['classes'] = count( $classes[0] );

        // 순환 복잡도
        $analysis['complexity'] = $this->calculate_cyclomatic_complexity( $code );

        // 데이터베이스 쿼리
        preg_match_all( '/(\$wpdb->|get_post|get_posts|WP_Query|get_user|get_option)/', $code, $queries );
        $analysis['database_queries'] = count( $queries[0] );

        // 보안 문제 검사
        $analysis['security'] = $this->check_php_security( $code );

        // 문제점 발견
        if ( $analysis['database_queries'] > 10 ) {
            $analysis['issues'][] = array(
                'type'    => 'warning',
                'message' => sprintf( __( '데이터베이스 쿼리가 많습니다 (%d개). 성능에 영향을 줄 수 있습니다.', 'acf-code-snippets-box' ), $analysis['database_queries'] ),
            );
        }

        // 제안사항
        if ( $analysis['complexity'] > 15 ) {
            $analysis['suggestions'][] = __( '코드가 복잡합니다. 함수를 더 작은 단위로 분리하는 것을 권장합니다.', 'acf-code-snippets-box' );
        }

        return $analysis;
    }

    /**
     * HTML 분석
     */
    private function analyze_html( $code ) {
        $analysis = array(
            'tags'            => 0,
            'nested_level'   => 0,
            'accessibility_issues' => array(),
            'seo_issues'     => array(),
        );

        // 태그 개수
        preg_match_all( '/<[^\/!][^>]*>/', $code, $tags );
        $analysis['tags'] = count( $tags[0] );

        // 중첩 레벨 계산
        $analysis['nested_level'] = $this->calculate_nesting_level( $code );

        // 접근성 문제
        $analysis['accessibility_issues'] = $this->check_accessibility( $code );

        // SEO 문제
        $analysis['seo_issues'] = $this->check_seo( $code );

        return $analysis;
    }

    /**
     * 순환 복잡도 계산
     */
    private function calculate_cyclomatic_complexity( $code ) {
        $complexity = 1; // 기본 복잡도

        // 조건문
        $complexity += preg_match_all( '/(if|else\s+if|switch|case|while|for|foreach)\s*\(/', $code );
        
        // 논리 연산자
        $complexity += preg_match_all( '/(&&|\|\||and|or)/', $code );

        // 예외 처리
        $complexity += preg_match_all( '/(try|catch|throw)/', $code );

        return $complexity;
    }

    /**
     * 중첩 레벨 계산 (HTML)
     */
    private function calculate_nesting_level( $code ) {
        $max_level = 0;
        $current_level = 0;
        $self_closing = array( 'br', 'hr', 'img', 'input', 'meta', 'link', 'area', 'base', 'col', 'embed', 'param', 'source', 'track', 'wbr' );

        preg_match_all( '/<(\/?)([\w]+)[^>]*>/', $code, $matches, PREG_SET_ORDER );

        foreach ( $matches as $match ) {
            $is_closing = $match[1] === '/';
            $tag = strtolower( $match[2] );

            if ( in_array( $tag, $self_closing, true ) ) {
                continue;
            }

            if ( $is_closing ) {
                $current_level--;
            } else {
                $current_level++;
                $max_level = max( $max_level, $current_level );
            }
        }

        return $max_level;
    }

    /**
     * JavaScript 의존성 찾기
     */
    private function find_javascript_dependencies( $code ) {
        $dependencies = array();

        // jQuery
        if ( preg_match( '/\$\(|jQuery\(/', $code ) ) {
            $dependencies[] = 'jquery';
        }

        // Lodash
        if ( preg_match( '/_\.(map|filter|reduce|each|find)/', $code ) ) {
            $dependencies[] = 'lodash';
        }

        // Axios/Fetch
        if ( preg_match( '/axios\.|fetch\(/', $code ) ) {
            $dependencies[] = 'axios';
        }

        // WordPress API
        if ( preg_match( '/wp\.|wpApiSettings/', $code ) ) {
            $dependencies[] = 'wp-api';
        }

        return $dependencies;
    }

    /**
     * PHP 보안 검사
     */
    private function check_php_security( $code ) {
        $issues = array();

        // SQL 인젝션 위험
        if ( preg_match( '/\$_(GET|POST|REQUEST)\[.*\]\s*\.\s*\$/', $code ) ) {
            $issues[] = array(
                'type'    => 'critical',
                'message' => __( 'SQL 인젝션 위험이 있습니다. prepared statements를 사용하세요.', 'acf-code-snippets-box' ),
            );
        }

        // XSS 위험
        if ( preg_match( '/echo\s+\$_(GET|POST|REQUEST)\[.*\]/', $code ) ) {
            $issues[] = array(
                'type'    => 'warning',
                'message' => __( 'XSS 위험이 있습니다. esc_html() 또는 esc_attr()를 사용하세요.', 'acf-code-snippets-box' ),
            );
        }

        // 위험한 함수
        $dangerous = array( 'eval', 'exec', 'shell_exec', 'system', 'passthru' );
        foreach ( $dangerous as $func ) {
            if ( preg_match( '/\b' . $func . '\s*\(/', $code ) ) {
                $issues[] = array(
                    'type'    => 'critical',
                    'message' => sprintf( __( '위험한 함수 %s()가 사용되었습니다.', 'acf-code-snippets-box' ), $func ),
                );
            }
        }

        return $issues;
    }

    /**
     * 접근성 검사 (HTML)
     */
    private function check_accessibility( $code ) {
        $issues = array();

        // alt 속성 누락
        if ( preg_match_all( '/<img[^>]*>/i', $code, $images ) ) {
            foreach ( $images[0] as $img ) {
                if ( ! preg_match( '/alt\s*=/i', $img ) ) {
                    $issues[] = array(
                        'type'    => 'warning',
                        'message' => __( 'img 태그에 alt 속성이 없습니다.', 'acf-code-snippets-box' ),
                    );
                }
            }
        }

        // 링크에 aria-label 또는 텍스트 없음
        if ( preg_match_all( '/<a[^>]*>(.*?)<\/a>/is', $code, $links ) ) {
            foreach ( $links[0] as $link ) {
                $text = strip_tags( $link );
                if ( empty( trim( $text ) ) && ! preg_match( '/aria-label\s*=/i', $link ) ) {
                    $issues[] = array(
                        'type'    => 'warning',
                        'message' => __( '링크에 접근 가능한 텍스트나 aria-label이 없습니다.', 'acf-code-snippets-box' ),
                    );
                }
            }
        }

        return $issues;
    }

    /**
     * SEO 검사 (HTML)
     */
    private function check_seo( $code ) {
        $issues = array();

        // 제목 태그
        if ( ! preg_match( '/<h[1-6][^>]*>/i', $code ) ) {
            $issues[] = array(
                'type'    => 'info',
                'message' => __( '제목 태그(h1-h6)가 없습니다. SEO에 도움이 될 수 있습니다.', 'acf-code-snippets-box' ),
            );
        }

        // 메타 설명
        if ( ! preg_match( '/<meta[^>]*name\s*=\s*["\']description["\']/i', $code ) ) {
            $issues[] = array(
                'type'    => 'info',
                'message' => __( '메타 description이 없습니다.', 'acf-code-snippets-box' ),
            );
        }

        return $issues;
    }

    /**
     * 코드 품질 점수 계산 (0-100)
     */
    private function calculate_quality_score( $analysis ) {
        $score = 100;

        // 문제점에 따른 감점
        foreach ( $analysis['issues'] as $issue ) {
            switch ( $issue['type'] ) {
                case 'critical':
                    $score -= 10;
                    break;
                case 'warning':
                    $score -= 5;
                    break;
                case 'info':
                    $score -= 2;
                    break;
            }
        }

        // 복잡도에 따른 감점
        if ( isset( $analysis['complexity'] ) && $analysis['complexity'] > 20 ) {
            $score -= min( 20, ( $analysis['complexity'] - 20 ) * 2 );
        }

        // 보안 문제
        if ( ! empty( $analysis['security'] ) ) {
            foreach ( $analysis['security'] as $security_issue ) {
                if ( $security_issue['type'] === 'critical' ) {
                    $score -= 15;
                }
            }
        }

        return max( 0, min( 100, $score ) );
    }

    /**
     * CSS 성능 권장사항
     */
    private function get_css_performance_recommendation( $analysis ) {
        $recommendations = array();

        if ( $analysis['file_size_kb'] > 50 ) {
            $recommendations[] = __( 'CSS 파일 크기가 큽니다. 압축을 고려하세요.', 'acf-code-snippets-box' );
        }

        if ( $analysis['selectors'] > 100 ) {
            $recommendations[] = __( '선택자가 많습니다. CSS를 분리하는 것을 고려하세요.', 'acf-code-snippets-box' );
        }

        return $recommendations;
    }

    /**
     * JavaScript 성능 권장사항
     */
    private function get_js_performance_recommendation( $analysis ) {
        $recommendations = array();

        if ( $analysis['file_size_kb'] > 100 ) {
            $recommendations[] = __( 'JavaScript 파일 크기가 큽니다. 코드 스플리팅을 고려하세요.', 'acf-code-snippets-box' );
        }

        if ( $analysis['dom_manipulations'] > 20 ) {
            $recommendations[] = __( 'DOM 조작이 많습니다. 배치 업데이트를 고려하세요.', 'acf-code-snippets-box' );
        }

        return $recommendations;
    }

    /**
     * 실행 시간 추정 (JavaScript)
     */
    private function estimate_execution_time( $analysis ) {
        $base_time = 1; // 기본 1ms
        $time_per_function = 0.5;
        $time_per_dom_manipulation = 2;

        $estimated = $base_time + 
                    ( $analysis['functions'] * $time_per_function ) +
                    ( $analysis['dom_manipulations'] * $time_per_dom_manipulation );

        return round( $estimated, 2 );
    }

    /**
     * AJAX: 코드 분석
     */
    public function ajax_analyze_code() {
        check_ajax_referer( 'acf_csb_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        $code = isset( $_POST['code'] ) ? wp_unslash( $_POST['code'] ) : '';
        $code_type = isset( $_POST['code_type'] ) ? sanitize_text_field( $_POST['code_type'] ) : 'css';

        $analysis = $this->analyze( $code, $code_type );

        wp_send_json_success( $analysis );
    }

    /**
     * AJAX: 코드 품질 점수
     */
    public function ajax_get_quality_score() {
        check_ajax_referer( 'acf_csb_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        $code = isset( $_POST['code'] ) ? wp_unslash( $_POST['code'] ) : '';
        $code_type = isset( $_POST['code_type'] ) ? sanitize_text_field( $_POST['code_type'] ) : 'css';

        $analysis = $this->analyze( $code, $code_type );

        wp_send_json_success( array(
            'score' => $analysis['quality_score'],
            'grade' => $this->get_quality_grade( $analysis['quality_score'] ),
        ) );
    }

    /**
     * AJAX: 의존성 찾기
     */
    public function ajax_find_dependencies() {
        check_ajax_referer( 'acf_csb_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        $code = isset( $_POST['code'] ) ? wp_unslash( $_POST['code'] ) : '';
        $code_type = isset( $_POST['code_type'] ) ? sanitize_text_field( $_POST['code_type'] ) : 'js';

        if ( $code_type === 'js' ) {
            $dependencies = $this->find_javascript_dependencies( $code );
        } else {
            $dependencies = array();
        }

        wp_send_json_success( array( 'dependencies' => $dependencies ) );
    }

    /**
     * 품질 등급 반환
     */
    private function get_quality_grade( $score ) {
        if ( $score >= 90 ) return 'A+';
        if ( $score >= 80 ) return 'A';
        if ( $score >= 70 ) return 'B';
        if ( $score >= 60 ) return 'C';
        if ( $score >= 50 ) return 'D';
        return 'F';
    }
}
