<?php
/**
 * ACF Code Snippets Box - Executor
 * 
 * 스니펫 코드 실행 담당
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
     */
    public function init() {
        // 프론트엔드 CSS/JS
        add_action( 'wp_head', array( $this, 'execute_head_snippets' ), 99 );
        add_action( 'wp_footer', array( $this, 'execute_footer_snippets' ), 99 );

        // 관리자 CSS/JS
        add_action( 'admin_head', array( $this, 'execute_admin_head_snippets' ), 99 );
        add_action( 'admin_footer', array( $this, 'execute_admin_footer_snippets' ), 99 );

        // PHP 실행 (init 액션)
        add_action( 'init', array( $this, 'execute_php_snippets' ), 99 );

        // HTML 실행 (body 시작)
        add_action( 'wp_body_open', array( $this, 'execute_html_snippets' ), 10 );
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
     */
    private function should_execute( $snippet_id, $current_location ) {
        $triggers = get_post_meta( $snippet_id, '_acf_csb_triggers', true );

        if ( empty( $triggers ) ) {
            return true; // 조건이 없으면 실행
        }

        $location = isset( $triggers['location'] ) ? $triggers['location'] : 'everywhere';

        // 위치 확인
        switch ( $location ) {
            case 'everywhere':
                break;
            case 'frontend':
                if ( is_admin() ) return false;
                break;
            case 'admin':
                if ( ! is_admin() ) return false;
                break;
            case 'specific_pages':
                if ( ! is_page() ) return false;
                $pages = isset( $triggers['pages'] ) ? array_map( 'intval', explode( ',', $triggers['pages'] ) ) : array();
                if ( ! in_array( get_the_ID(), $pages, true ) ) return false;
                break;
            case 'specific_posts':
                if ( ! is_single() ) return false;
                $posts = isset( $triggers['posts'] ) ? array_map( 'intval', explode( ',', $triggers['posts'] ) ) : array();
                if ( ! in_array( get_the_ID(), $posts, true ) ) return false;
                break;
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

        // 디바이스 확인
        $device = isset( $triggers['device'] ) ? $triggers['device'] : 'all';
        if ( $device !== 'all' ) {
            $is_mobile = wp_is_mobile();
            if ( $device === 'desktop' && $is_mobile ) return false;
            if ( $device === 'mobile' && ! $is_mobile ) return false;
        }

        return true;
    }

    /**
     * HEAD에 CSS 스니펫 실행
     */
    public function execute_head_snippets() {
        if ( is_admin() ) return;

        $snippets = $this->get_active_snippets( 'css', 'frontend' );
        
        if ( ! empty( $snippets ) ) {
            echo "\n<!-- ACF Code Snippets Box - CSS -->\n<style id=\"acf-csb-custom-css\">\n";
            foreach ( $snippets as $snippet ) {
                $code = get_post_meta( $snippet->ID, '_acf_csb_code', true );
                if ( ! empty( $code ) ) {
                    echo "/* Snippet: " . esc_html( $snippet->post_title ) . " */\n";
                    echo $this->sanitize_css( $code ) . "\n";
                    $this->executed_snippets[] = $snippet->ID;
                }
            }
            echo "</style>\n";
        }
    }

    /**
     * FOOTER에 JS 스니펫 실행
     */
    public function execute_footer_snippets() {
        if ( is_admin() ) return;

        $snippets = $this->get_active_snippets( 'js', 'frontend' );
        
        if ( ! empty( $snippets ) ) {
            echo "\n<!-- ACF Code Snippets Box - JavaScript -->\n<script id=\"acf-csb-custom-js\">\n";
            foreach ( $snippets as $snippet ) {
                $code = get_post_meta( $snippet->ID, '_acf_csb_code', true );
                if ( ! empty( $code ) ) {
                    echo "/* Snippet: " . esc_js( $snippet->post_title ) . " */\n";
                    echo "(function(){\n" . $code . "\n})();\n";
                    $this->executed_snippets[] = $snippet->ID;
                }
            }
            echo "</script>\n";
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
                $code = get_post_meta( $snippet->ID, '_acf_csb_code', true );
                if ( ! empty( $code ) ) {
                    echo $this->sanitize_css( $code ) . "\n";
                    $this->executed_snippets[] = $snippet->ID;
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
                $code = get_post_meta( $snippet->ID, '_acf_csb_code', true );
                if ( ! empty( $code ) ) {
                    echo "(function(){\n" . $code . "\n})();\n";
                    $this->executed_snippets[] = $snippet->ID;
                }
            }
            echo "</script>\n";
        }
    }

    /**
     * PHP 스니펫 실행
     */
    public function execute_php_snippets() {
        // 보안: PHP 실행이 활성화되어 있는지 확인
        $settings = get_option( 'acf_csb_settings', array() );
        if ( empty( $settings['enable_php_execution'] ) ) {
            return;
        }

        $snippets = $this->get_active_snippets( 'php', 'everywhere' );
        
        foreach ( $snippets as $snippet ) {
            $code = get_post_meta( $snippet->ID, '_acf_csb_code', true );
            if ( ! empty( $code ) ) {
                try {
                    // 에러 로깅 활성화
                    if ( ! empty( $settings['enable_error_logging'] ) ) {
                        set_error_handler( array( $this, 'php_error_handler' ) );
                    }
                    
                    eval( $code );
                    
                    if ( ! empty( $settings['enable_error_logging'] ) ) {
                        restore_error_handler();
                    }
                    
                    $this->executed_snippets[] = $snippet->ID;
                } catch ( Exception $e ) {
                    $this->log_error( $snippet->ID, $e->getMessage() );
                } catch ( Error $e ) {
                    $this->log_error( $snippet->ID, $e->getMessage() );
                }
            }
        }
    }

    /**
     * HTML 스니펫 실행
     */
    public function execute_html_snippets() {
        if ( is_admin() ) return;

        $snippets = $this->get_active_snippets( 'html', 'frontend' );
        
        foreach ( $snippets as $snippet ) {
            $code = get_post_meta( $snippet->ID, '_acf_csb_code', true );
            if ( ! empty( $code ) ) {
                echo "\n<!-- ACF Code Snippets Box - HTML: " . esc_html( $snippet->post_title ) . " -->\n";
                echo $code . "\n";
                $this->executed_snippets[] = $snippet->ID;
            }
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
