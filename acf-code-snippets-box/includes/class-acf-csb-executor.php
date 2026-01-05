<?php
/**
 * ACF Code Snippets Box - Executor
 *
 * 스니펫 코드 실행 담당
 * [v5.0.0] 코드 위치 지정 및 확장된 트리거 지원
 *
 * @package ACF_Code_Snippets_Box
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Executor 클래스
 */
class ACF_CSB_Executor {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 실행된 스니펫 ID 추적
     */
    private $executed_snippets = array();

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
     * 초기화
     * [v5.0.0] 동적 훅 등록 지원
     */
    public function init() {
        // [v5.0.0] 코드 위치에 따른 동적 훅 등록
        $this->register_dynamic_hooks();

        // 기본 프론트엔드 CSS (wp_head)
        add_action( 'wp_head', array( $this, 'execute_head_snippets' ), 99 );
        add_action( 'wp_head', array( $this, 'execute_head_early_snippets' ), 1 );
        add_action( 'wp_head', array( $this, 'execute_head_late_snippets' ), 999 );

        // 기본 프론트엔드 JS (wp_footer)
        add_action( 'wp_footer', array( $this, 'execute_footer_snippets' ), 99 );
        add_action( 'wp_footer', array( $this, 'execute_footer_early_snippets' ), 1 );
        add_action( 'wp_footer', array( $this, 'execute_footer_late_snippets' ), 999 );

        // 관리자 CSS/JS
        add_action( 'admin_head', array( $this, 'execute_admin_head_snippets' ), 99 );
        add_action( 'admin_footer', array( $this, 'execute_admin_footer_snippets' ), 99 );

        // 로그인 페이지
        add_action( 'login_head', array( $this, 'execute_login_head_snippets' ), 99 );
        add_action( 'login_footer', array( $this, 'execute_login_footer_snippets' ), 99 );

        // PHP 실행 (다양한 훅)
        add_action( 'init', array( $this, 'execute_php_init_snippets' ), 99 );
        add_action( 'after_setup_theme', array( $this, 'execute_php_after_theme_snippets' ), 99 );
        add_action( 'wp_loaded', array( $this, 'execute_php_wp_loaded_snippets' ), 99 );
        add_action( 'template_redirect', array( $this, 'execute_php_template_redirect_snippets' ), 99 );
        add_action( 'wp', array( $this, 'execute_php_wp_snippets' ), 99 );
        add_action( 'admin_init', array( $this, 'execute_php_admin_init_snippets' ), 99 );
        add_action( 'rest_api_init', array( $this, 'execute_php_rest_api_snippets' ), 99 );
        add_action( 'shutdown', array( $this, 'execute_php_shutdown_snippets' ), 99 );

        // HTML 실행 (body 시작)
        add_action( 'wp_body_open', array( $this, 'execute_html_snippets' ), 10 );

        // [v5.0.0] the_content 필터를 통한 HTML 삽입
        add_filter( 'the_content', array( $this, 'execute_content_html_snippets' ), 99 );

        // [v5.0.0] 쇼트코드 등록
        add_shortcode( 'acf_snippet', array( $this, 'shortcode_handler' ) );
    }

    /**
     * 동적 훅 등록 (커스텀 PHP 훅용)
     * [v5.0.0]
     */
    private function register_dynamic_hooks() {
        $snippets = $this->get_active_snippets( 'php', 'everywhere' );

        foreach ( $snippets as $snippet ) {
            $location = get_post_meta( $snippet->ID, '_acf_csb_code_location', true );
            $custom_hook = get_post_meta( $snippet->ID, '_acf_csb_custom_hook', true );
            $priority = get_post_meta( $snippet->ID, '_acf_csb_priority', true ) ?: 10;

            if ( $location === 'custom' && ! empty( $custom_hook ) ) {
                add_action( $custom_hook, function() use ( $snippet ) {
                    $this->execute_single_php_snippet( $snippet );
                }, absint( $priority ) );
            }
        }
    }

    /**
     * 활성화된 스니펫 가져오기
     */
    private function get_active_snippets( $code_type = '', $location = 'frontend' ) {
        $args = array(
            'post_type'      => ACF_CSB_Post_Type::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'     => '_acf_csb_active',
                    'value'   => '1',
                    'compare' => '=',
                ),
            ),
            'orderby'        => 'meta_value_num',
            'meta_key'       => '_acf_csb_priority',
            'order'          => 'ASC',
        );

        // 코드 타입 필터
        if ( ! empty( $code_type ) ) {
            $args['meta_query'][] = array(
                'key'     => '_acf_csb_code_type',
                'value'   => $code_type,
                'compare' => '=',
            );
        }

        $snippets = get_posts( $args );
        $filtered = array();

        foreach ( $snippets as $snippet ) {
            if ( $this->should_execute( $snippet->ID, $location ) ) {
                $filtered[] = $snippet;
            }
        }

        return $filtered;
    }

    /**
     * 실행 조건 확인
     * [v5.0.0] 확장된 트리거 조건 지원
     */
    private function should_execute( $snippet_id, $current_location ) {
        $triggers = get_post_meta( $snippet_id, '_acf_csb_triggers', true );

        if ( empty( $triggers ) ) {
            return true; // 조건이 없으면 실행
        }

        // 위치 확인
        $location = isset( $triggers['location'] ) ? $triggers['location'] : 'everywhere';
        if ( ! $this->check_location_trigger( $location, $triggers ) ) {
            return false;
        }

        // [v5.0.0] URL 패턴 확인
        if ( ! empty( $triggers['url_pattern'] ) ) {
            if ( ! ACF_CSB_Triggers::evaluate_trigger( 'url_pattern', $triggers['url_pattern'] ) ) {
                return false;
            }
        }

        // [v5.0.0] URL 제외 패턴 확인
        if ( ! empty( $triggers['url_exclude'] ) ) {
            if ( ! ACF_CSB_Triggers::evaluate_trigger( 'url_exclude', $triggers['url_exclude'] ) ) {
                return false;
            }
        }

        // [v5.0.0] 쿼리 스트링 확인
        if ( ! empty( $triggers['query_string'] ) ) {
            if ( ! ACF_CSB_Triggers::evaluate_trigger( 'query_string', $triggers['query_string'] ) ) {
                return false;
            }
        }

        // 로그인 상태 확인
        $logged_in = isset( $triggers['logged_in'] ) ? $triggers['logged_in'] : 'all';
        if ( $logged_in === 'logged_in' && ! is_user_logged_in() ) {
            return false;
        }
        if ( $logged_in === 'logged_out' && is_user_logged_in() ) {
            return false;
        }

        // 사용자 역할 확인
        if ( ! empty( $triggers['user_roles'] ) && is_array( $triggers['user_roles'] ) ) {
            $user = wp_get_current_user();
            $user_roles = $user->roles;
            $intersect = array_intersect( $user_roles, $triggers['user_roles'] );
            if ( empty( $intersect ) && ! empty( $user_roles ) ) {
                return false;
            }
        }

        // [v5.0.0] 사용자 권한 확인
        if ( ! empty( $triggers['user_capability'] ) ) {
            if ( ! ACF_CSB_Triggers::evaluate_trigger( 'user_capability', $triggers['user_capability'] ) ) {
                return false;
            }
        }

        // 디바이스 확인
        $device = isset( $triggers['device'] ) ? $triggers['device'] : 'all';
        if ( $device !== 'all' ) {
            $is_mobile = wp_is_mobile();
            if ( $device === 'desktop' && $is_mobile ) return false;
            if ( $device === 'mobile' && ! $is_mobile ) return false;
            if ( $device === 'tablet' ) {
                // 태블릿 감지 (간단한 방식)
                if ( ! $is_mobile ) return false;
            }
        }

        // [v5.0.0] 브라우저 확인
        if ( ! empty( $triggers['browser'] ) && $triggers['browser'] !== 'all' ) {
            if ( ! ACF_CSB_Triggers::evaluate_trigger( 'browser', $triggers['browser'] ) ) {
                return false;
            }
        }

        // [v5.0.0] 시간 기반 트리거
        if ( ! empty( $triggers['time_based'] ) && $triggers['time_based'] !== 'always' ) {
            if ( ! ACF_CSB_Triggers::evaluate_trigger( 'time_based', $triggers['time_based'], $triggers ) ) {
                return false;
            }
        }

        // [v5.0.0] 쿠키 확인
        if ( ! empty( $triggers['cookie_exists'] ) ) {
            if ( ! ACF_CSB_Triggers::evaluate_trigger( 'cookie_exists', $triggers['cookie_exists'] ) ) {
                return false;
            }
        }

        if ( ! empty( $triggers['cookie_value'] ) ) {
            if ( ! ACF_CSB_Triggers::evaluate_trigger( 'cookie_value', $triggers['cookie_value'] ) ) {
                return false;
            }
        }

        // [v5.0.0] 리퍼러 확인
        if ( ! empty( $triggers['referrer'] ) ) {
            if ( ! ACF_CSB_Triggers::evaluate_trigger( 'referrer', $triggers['referrer'] ) ) {
                return false;
            }
        }

        // [v5.0.0] 언어 확인
        if ( ! empty( $triggers['language'] ) ) {
            if ( ! ACF_CSB_Triggers::evaluate_trigger( 'language', $triggers['language'] ) ) {
                return false;
            }
        }

        // [v5.0.0] WooCommerce 확인
        if ( ! empty( $triggers['woocommerce'] ) ) {
            if ( ! ACF_CSB_Triggers::evaluate_trigger( 'woocommerce', $triggers['woocommerce'] ) ) {
                return false;
            }
        }

        // [v5.0.0] 멀티사이트 확인
        if ( ! empty( $triggers['multisite'] ) && is_multisite() ) {
            if ( get_current_blog_id() != $triggers['multisite'] ) {
                return false;
            }
        }

        return true;
    }

    /**
     * 위치 트리거 확인
     * [v5.0.0]
     */
    private function check_location_trigger( $location, $triggers ) {
        switch ( $location ) {
            case 'everywhere':
                return true;

            case 'frontend':
                return ! is_admin();

            case 'admin':
                return is_admin();

            case 'specific_pages':
                if ( ! is_page() ) return false;
                $pages = isset( $triggers['pages'] ) ? array_map( 'intval', explode( ',', $triggers['pages'] ) ) : array();
                return in_array( get_the_ID(), $pages, true );

            case 'specific_posts':
                if ( ! is_single() ) return false;
                $posts = isset( $triggers['posts'] ) ? array_map( 'intval', explode( ',', $triggers['posts'] ) ) : array();
                return in_array( get_the_ID(), $posts, true );

            case 'post_types':
                $selected_types = isset( $triggers['post_types_selected'] ) ? $triggers['post_types_selected'] : array();
                if ( empty( $selected_types ) ) return true;
                return is_singular( $selected_types );

            case 'home':
                return is_front_page() || is_home();

            case 'archive':
                return is_archive();

            case 'search':
                return is_search();

            case '404':
                return is_404();

            case 'singular':
                return is_singular();

            case 'category':
                return is_category();

            case 'tag':
                return is_tag();

            case 'author':
                return is_author();

            case 'date':
                return is_date();

            default:
                return true;
        }
    }

    /**
     * HEAD에 CSS 스니펫 실행 (기본 우선순위)
     * [v5.0.0] 코드 위치 지정 지원
     */
    public function execute_head_snippets() {
        if ( is_admin() ) return;
        $this->execute_css_by_location( 'wp_head' );
    }

    /**
     * HEAD에 CSS 스니펫 실행 (우선순위 1)
     * [v5.0.0]
     */
    public function execute_head_early_snippets() {
        if ( is_admin() ) return;
        $this->execute_css_by_location( 'wp_head_early' );
    }

    /**
     * HEAD에 CSS 스니펫 실행 (우선순위 999)
     * [v5.0.0]
     */
    public function execute_head_late_snippets() {
        if ( is_admin() ) return;
        $this->execute_css_by_location( 'wp_head_late' );
    }

    /**
     * FOOTER에 JS 스니펫 실행 (기본 우선순위)
     * [v5.0.0] 코드 위치 지정 지원
     */
    public function execute_footer_snippets() {
        if ( is_admin() ) return;
        $this->execute_js_by_location( 'wp_footer' );

        // CSS를 footer에 넣는 스니펫도 처리
        $this->execute_css_by_location( 'wp_footer' );
    }

    /**
     * FOOTER에 JS 스니펫 실행 (우선순위 1)
     * [v5.0.0]
     */
    public function execute_footer_early_snippets() {
        if ( is_admin() ) return;
        $this->execute_js_by_location( 'wp_footer_early' );
    }

    /**
     * FOOTER에 JS 스니펫 실행 (우선순위 999)
     * [v5.0.0]
     */
    public function execute_footer_late_snippets() {
        if ( is_admin() ) return;
        $this->execute_js_by_location( 'wp_footer_late' );
    }

    /**
     * 코드 위치별 CSS 실행
     * [v5.0.0]
     */
    private function execute_css_by_location( $target_location ) {
        $snippets = $this->get_active_snippets( 'css', 'frontend' );
        $output = array();

        foreach ( $snippets as $snippet ) {
            $location = get_post_meta( $snippet->ID, '_acf_csb_code_location', true ) ?: 'wp_head';

            if ( $location === $target_location || ( empty( $location ) && $target_location === 'wp_head' ) ) {
                $code = get_post_meta( $snippet->ID, '_acf_csb_code', true );
                if ( ! empty( $code ) && ! in_array( $snippet->ID, $this->executed_snippets, true ) ) {
                    $output[] = "/* Snippet: " . esc_html( $snippet->post_title ) . " */\n" . $this->sanitize_css( $code );
                    $this->executed_snippets[] = $snippet->ID;
                }
            }
        }

        if ( ! empty( $output ) ) {
            echo "\n<!-- ACF Code Snippets Box - CSS (" . esc_html( $target_location ) . ") -->\n<style id=\"acf-csb-css-" . esc_attr( $target_location ) . "\">\n";
            echo implode( "\n", $output );
            echo "\n</style>\n";
        }
    }

    /**
     * 코드 위치별 JS 실행
     * [v5.0.0]
     */
    private function execute_js_by_location( $target_location ) {
        $snippets = $this->get_active_snippets( 'js', 'frontend' );
        $output = array();
        $wrapper_start = '';
        $wrapper_end = '';

        // JS 래퍼 설정
        switch ( $target_location ) {
            case 'jquery_ready':
                $wrapper_start = "jQuery(document).ready(function($) {\n";
                $wrapper_end = "\n});";
                $target_location = 'wp_footer'; // 실제 훅은 wp_footer
                break;
            case 'dom_loaded':
                $wrapper_start = "document.addEventListener('DOMContentLoaded', function() {\n";
                $wrapper_end = "\n});";
                $target_location = 'wp_footer';
                break;
            case 'window_load':
                $wrapper_start = "window.addEventListener('load', function() {\n";
                $wrapper_end = "\n});";
                $target_location = 'wp_footer';
                break;
        }

        foreach ( $snippets as $snippet ) {
            $location = get_post_meta( $snippet->ID, '_acf_csb_code_location', true ) ?: 'wp_footer';

            if ( $location === $target_location || ( empty( $location ) && $target_location === 'wp_footer' ) ) {
                $code = get_post_meta( $snippet->ID, '_acf_csb_code', true );
                if ( ! empty( $code ) && ! in_array( $snippet->ID, $this->executed_snippets, true ) ) {
                    $output[] = "/* Snippet: " . esc_js( $snippet->post_title ) . " */\n(function(){\n" . $code . "\n})();";
                    $this->executed_snippets[] = $snippet->ID;
                }
            }
        }

        if ( ! empty( $output ) ) {
            echo "\n<!-- ACF Code Snippets Box - JavaScript (" . esc_html( $target_location ) . ") -->\n<script id=\"acf-csb-js-" . esc_attr( $target_location ) . "\">\n";
            echo $wrapper_start;
            echo implode( "\n", $output );
            echo $wrapper_end;
            echo "\n</script>\n";
        }
    }

    /**
     * 관리자 HEAD에 CSS 스니펫 실행
     */
    public function execute_admin_head_snippets() {
        $snippets = $this->get_active_snippets( 'css', 'admin' );

        if ( ! empty( $snippets ) ) {
            echo "\n<!-- ACF Code Snippets Box - Admin CSS -->\n<style id=\"acf-csb-admin-css\">\n";
            foreach ( $snippets as $snippet ) {
                $location = get_post_meta( $snippet->ID, '_acf_csb_code_location', true );
                if ( $location === 'admin_head' || empty( $location ) ) {
                    $code = get_post_meta( $snippet->ID, '_acf_csb_code', true );
                    if ( ! empty( $code ) ) {
                        echo $this->sanitize_css( $code ) . "\n";
                        $this->executed_snippets[] = $snippet->ID;
                    }
                }
            }
            echo "</style>\n";
        }
    }

    /**
     * 관리자 FOOTER에 JS 스니펫 실행
     */
    public function execute_admin_footer_snippets() {
        $snippets = $this->get_active_snippets( 'js', 'admin' );

        if ( ! empty( $snippets ) ) {
            echo "\n<!-- ACF Code Snippets Box - Admin JavaScript -->\n<script id=\"acf-csb-admin-js\">\n";
            foreach ( $snippets as $snippet ) {
                $location = get_post_meta( $snippet->ID, '_acf_csb_code_location', true );
                if ( $location === 'admin_footer' || empty( $location ) ) {
                    $code = get_post_meta( $snippet->ID, '_acf_csb_code', true );
                    if ( ! empty( $code ) ) {
                        echo "(function(){\n" . $code . "\n})();\n";
                        $this->executed_snippets[] = $snippet->ID;
                    }
                }
            }
            echo "</script>\n";
        }
    }

    /**
     * 로그인 페이지 HEAD 스니펫 실행
     * [v5.0.0]
     */
    public function execute_login_head_snippets() {
        $snippets = $this->get_active_snippets( 'css', 'everywhere' );

        foreach ( $snippets as $snippet ) {
            $location = get_post_meta( $snippet->ID, '_acf_csb_code_location', true );
            if ( $location === 'login_head' ) {
                $code = get_post_meta( $snippet->ID, '_acf_csb_code', true );
                if ( ! empty( $code ) ) {
                    echo "\n<style id=\"acf-csb-login-css-" . esc_attr( $snippet->ID ) . "\">\n";
                    echo $this->sanitize_css( $code );
                    echo "\n</style>\n";
                    $this->executed_snippets[] = $snippet->ID;
                }
            }
        }
    }

    /**
     * 로그인 페이지 FOOTER 스니펫 실행
     * [v5.0.0]
     */
    public function execute_login_footer_snippets() {
        $snippets = $this->get_active_snippets( 'js', 'everywhere' );

        foreach ( $snippets as $snippet ) {
            $location = get_post_meta( $snippet->ID, '_acf_csb_code_location', true );
            if ( $location === 'login_footer' ) {
                $code = get_post_meta( $snippet->ID, '_acf_csb_code', true );
                if ( ! empty( $code ) ) {
                    echo "\n<script id=\"acf-csb-login-js-" . esc_attr( $snippet->ID ) . "\">\n";
                    echo "(function(){\n" . $code . "\n})();";
                    echo "\n</script>\n";
                    $this->executed_snippets[] = $snippet->ID;
                }
            }
        }
    }

    /**
     * PHP 스니펫 실행 (init 훅)
     * [v5.0.0] 코드 위치에 따른 실행
     */
    public function execute_php_init_snippets() {
        $this->execute_php_by_location( 'init' );
    }

    /**
     * PHP 스니펫 실행 (after_setup_theme)
     * [v5.0.0]
     */
    public function execute_php_after_theme_snippets() {
        $this->execute_php_by_location( 'after_setup_theme' );
    }

    /**
     * PHP 스니펫 실행 (wp_loaded)
     * [v5.0.0]
     */
    public function execute_php_wp_loaded_snippets() {
        $this->execute_php_by_location( 'wp_loaded' );
    }

    /**
     * PHP 스니펫 실행 (template_redirect)
     * [v5.0.0]
     */
    public function execute_php_template_redirect_snippets() {
        $this->execute_php_by_location( 'template_redirect' );
    }

    /**
     * PHP 스니펫 실행 (wp)
     * [v5.0.0]
     */
    public function execute_php_wp_snippets() {
        $this->execute_php_by_location( 'wp' );
    }

    /**
     * PHP 스니펫 실행 (admin_init)
     * [v5.0.0]
     */
    public function execute_php_admin_init_snippets() {
        $this->execute_php_by_location( 'admin_init' );
    }

    /**
     * PHP 스니펫 실행 (rest_api_init)
     * [v5.0.0]
     */
    public function execute_php_rest_api_snippets() {
        $this->execute_php_by_location( 'rest_api_init' );
    }

    /**
     * PHP 스니펫 실행 (shutdown)
     * [v5.0.0]
     */
    public function execute_php_shutdown_snippets() {
        $this->execute_php_by_location( 'shutdown' );
    }

    /**
     * 코드 위치별 PHP 실행
     * [v5.0.0]
     */
    private function execute_php_by_location( $target_location ) {
        // 보안: PHP 실행이 활성화되어 있는지 확인
        $settings = get_option( 'acf_csb_settings', array() );
        if ( empty( $settings['enable_php_execution'] ) ) {
            return;
        }

        $snippets = $this->get_active_snippets( 'php', 'everywhere' );

        foreach ( $snippets as $snippet ) {
            $location = get_post_meta( $snippet->ID, '_acf_csb_code_location', true ) ?: 'init';

            // 기본값이거나 해당 위치면 실행
            if ( $location === $target_location || ( empty( $location ) && $target_location === 'init' ) ) {
                if ( ! in_array( $snippet->ID, $this->executed_snippets, true ) ) {
                    $this->execute_single_php_snippet( $snippet );
                }
            }
        }
    }

    /**
     * 단일 PHP 스니펫 실행
     * [v5.0.0]
     */
    private function execute_single_php_snippet( $snippet ) {
        $settings = get_option( 'acf_csb_settings', array() );
        $code = get_post_meta( $snippet->ID, '_acf_csb_code', true );

        if ( empty( $code ) ) {
            return;
        }

        // 샌드박스 인스턴스
        if ( class_exists( 'ACF_CSB_Sandbox' ) ) {
            $sandbox = ACF_CSB_Sandbox::instance();

            // 샌드박스 옵션
            $sandbox_options = array(
                'max_execution_time' => isset( $settings['php_max_execution_time'] ) ? intval( $settings['php_max_execution_time'] ) : 5,
                'max_memory'         => isset( $settings['php_max_memory'] ) ? $settings['php_max_memory'] : '32M',
            );

            // 샌드박스에서 실행
            $result = $sandbox->execute( $code, $sandbox_options );

            if ( $result['success'] ) {
                // 성능 모니터링
                if ( class_exists( 'ACF_CSB_Performance_Monitor' ) ) {
                    $monitor = ACF_CSB_Performance_Monitor::instance();
                    $monitor->end_timing( $snippet->ID );
                }
                $this->executed_snippets[] = $snippet->ID;
            } else {
                $this->log_error( $snippet->ID, $result['error'] );
            }
        }
    }

    /**
     * HTML 스니펫 실행 (wp_body_open)
     */
    public function execute_html_snippets() {
        if ( is_admin() ) return;

        $snippets = $this->get_active_snippets( 'html', 'frontend' );

        foreach ( $snippets as $snippet ) {
            $location = get_post_meta( $snippet->ID, '_acf_csb_code_location', true ) ?: 'wp_body_open';

            if ( $location === 'wp_body_open' ) {
                $code = get_post_meta( $snippet->ID, '_acf_csb_code', true );
                if ( ! empty( $code ) ) {
                    echo "\n<!-- ACF Code Snippets Box - HTML: " . esc_html( $snippet->post_title ) . " -->\n";
                    echo $code . "\n";
                    $this->executed_snippets[] = $snippet->ID;
                }
            }
        }
    }

    /**
     * the_content 필터를 통한 HTML 삽입
     * [v5.0.0]
     */
    public function execute_content_html_snippets( $content ) {
        if ( is_admin() || ! is_singular() ) {
            return $content;
        }

        $snippets = $this->get_active_snippets( 'html', 'frontend' );
        $before_content = '';
        $after_content = '';

        foreach ( $snippets as $snippet ) {
            $location = get_post_meta( $snippet->ID, '_acf_csb_code_location', true );
            $code = get_post_meta( $snippet->ID, '_acf_csb_code', true );

            if ( empty( $code ) || in_array( $snippet->ID, $this->executed_snippets, true ) ) {
                continue;
            }

            if ( $location === 'before_content' || $location === 'the_content' ) {
                $before_content .= "\n<!-- ACF Snippet: " . esc_html( $snippet->post_title ) . " -->\n" . $code . "\n";
                $this->executed_snippets[] = $snippet->ID;
            }

            if ( $location === 'after_content' || $location === 'the_content' ) {
                $after_content .= "\n<!-- ACF Snippet: " . esc_html( $snippet->post_title ) . " -->\n" . $code . "\n";
                $this->executed_snippets[] = $snippet->ID;
            }
        }

        return $before_content . $content . $after_content;
    }

    /**
     * 쇼트코드 핸들러
     * [v5.0.0]
     */
    public function shortcode_handler( $atts ) {
        $atts = shortcode_atts( array(
            'id' => 0,
        ), $atts, 'acf_snippet' );

        $snippet_id = absint( $atts['id'] );
        if ( ! $snippet_id ) {
            return '';
        }

        $snippet = get_post( $snippet_id );
        if ( ! $snippet || $snippet->post_type !== 'acf_code_snippet' ) {
            return '';
        }

        // 활성화 확인
        $is_active = get_post_meta( $snippet_id, '_acf_csb_active', true );
        if ( ! $is_active ) {
            return '';
        }

        // 조건 확인
        if ( ! $this->should_execute( $snippet_id, 'frontend' ) ) {
            return '';
        }

        $code = get_post_meta( $snippet_id, '_acf_csb_code', true );
        $code_type = get_post_meta( $snippet_id, '_acf_csb_code_type', true );

        if ( empty( $code ) ) {
            return '';
        }

        $this->executed_snippets[] = $snippet_id;

        switch ( $code_type ) {
            case 'css':
                return '<style>' . $this->sanitize_css( $code ) . '</style>';
            case 'js':
                return '<script>(function(){' . $code . '})();</script>';
            case 'html':
                return $code;
            default:
                return '';
        }
    }

    /**
     * CSS 코드 정리
     */
    private function sanitize_css( $css ) {
        // 기본적인 정리만 수행 (악의적인 코드 제거)
        $css = preg_replace( '/<script[^>]*>.*?<\/script>/is', '', $css );
        $css = preg_replace( '/expression\s*\(/i', '', $css );
        $css = preg_replace( '/javascript\s*:/i', '', $css );
        return $css;
    }

    /**
     * PHP 에러 핸들러
     */
    public function php_error_handler( $errno, $errstr, $errfile, $errline ) {
        $this->log_error( 0, "PHP Error: [$errno] $errstr in $errfile on line $errline" );
        return true;
    }

    /**
     * 에러 로깅
     */
    private function log_error( $snippet_id, $message ) {
        $log_file = WP_CONTENT_DIR . '/acf-csb-error.log';
        $timestamp = current_time( 'mysql' );
        $log_entry = "[$timestamp] Snippet #$snippet_id: $message\n";
        error_log( $log_entry, 3, $log_file );
    }

    /**
     * 실행된 스니펫 ID 반환
     */
    public function get_executed_snippets() {
        return $this->executed_snippets;
    }
}
