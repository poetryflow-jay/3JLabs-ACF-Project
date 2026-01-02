<?php
/**
 * JJ Master Code Snippets - 마스터 버전 통합 코드 스니펫 모듈
 * 
 * ACF Code Snippets Box의 핵심 기능을 마스터 버전에 통합합니다.
 * 
 * @since 13.4.4
 * @package ACF_CSS
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_Master_Code_Snippets {

    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init();
    }

    private function init() {
        // 스니펫 포스트 타입 등록
        add_action( 'init', array( $this, 'register_post_type' ) );
        
        // 관리자 메뉴
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 20 );
        
        // 스니펫 실행 엔진
        add_action( 'wp', array( $this, 'execute_snippets' ) );
        add_action( 'admin_init', array( $this, 'execute_admin_snippets' ) );
    }

    /**
     * 스니펫 포스트 타입 등록
     */
    public function register_post_type() {
        $labels = array(
            'name' => __( '코드 스니펫', 'acf-css-really-simple-style-management-center' ),
            'singular_name' => __( '코드 스니펫', 'acf-css-really-simple-style-management-center' ),
            'add_new' => __( '새 스니펫 추가', 'acf-css-really-simple-style-management-center' ),
            'add_new_item' => __( '새 코드 스니펫 추가', 'acf-css-really-simple-style-management-center' ),
            'edit_item' => __( '코드 스니펫 편집', 'acf-css-really-simple-style-management-center' ),
            'all_items' => __( '모든 스니펫', 'acf-css-really-simple-style-management-center' ),
        );

        $args = array(
            'labels' => $labels,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'capability_type' => 'post',
            'supports' => array( 'title', 'editor' ),
            'menu_icon' => 'dashicons-editor-code',
        );

        register_post_type( 'jj_code_snippet', $args );
    }

    /**
     * 관리자 메뉴 추가
     */
    public function add_admin_menu() {
        add_submenu_page(
            'jj-admin-center',
            __( '코드 스니펫', 'acf-css-really-simple-style-management-center' ),
            __( '📝 코드 스니펫', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            'edit.php?post_type=jj_code_snippet'
        );
    }

    /**
     * 프론트엔드 스니펫 실행
     */
    public function execute_snippets() {
        $snippets = $this->get_active_snippets( 'frontend' );
        
        foreach ( $snippets as $snippet ) {
            $this->execute_snippet( $snippet );
        }
    }

    /**
     * 관리자 스니펫 실행
     */
    public function execute_admin_snippets() {
        $snippets = $this->get_active_snippets( 'admin' );
        
        foreach ( $snippets as $snippet ) {
            $this->execute_snippet( $snippet );
        }
    }

    /**
     * 활성 스니펫 가져오기
     */
    private function get_active_snippets( $location = 'frontend' ) {
        $args = array(
            'post_type' => 'jj_code_snippet',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_jj_snippet_location',
                    'value' => $location,
                    'compare' => '=',
                ),
                array(
                    'key' => '_jj_snippet_active',
                    'value' => '1',
                    'compare' => '=',
                ),
            ),
        );

        return get_posts( $args );
    }

    /**
     * 개별 스니펫 실행
     */
    private function execute_snippet( $snippet ) {
        $code = get_post_meta( $snippet->ID, '_jj_snippet_code', true );
        $type = get_post_meta( $snippet->ID, '_jj_snippet_type', true );
        $priority = get_post_meta( $snippet->ID, '_jj_snippet_priority', true );

        if ( empty( $code ) ) {
            return;
        }

        switch ( $type ) {
            case 'php':
                // PHP 코드는 보안상 eval 대신 별도 파일로 저장 후 include
                $this->execute_php_snippet( $snippet->ID, $code );
                break;
            case 'css':
                add_action( 'wp_head', function() use ( $code ) {
                    echo '<style id="jj-snippet-css">' . wp_strip_all_tags( $code ) . '</style>';
                }, intval( $priority ) ?: 10 );
                break;
            case 'js':
                add_action( 'wp_footer', function() use ( $code ) {
                    echo '<script id="jj-snippet-js">' . $code . '</script>';
                }, intval( $priority ) ?: 10 );
                break;
            case 'html':
                add_action( 'wp_body_open', function() use ( $code ) {
                    echo $code;
                }, intval( $priority ) ?: 10 );
                break;
        }
    }

    /**
     * PHP 스니펫 안전 실행
     */
    private function execute_php_snippet( $snippet_id, $code ) {
        // PHP 스니펫은 캐시 파일로 저장 후 include
        $cache_dir = wp_upload_dir()['basedir'] . '/jj-snippets/';
        
        if ( ! file_exists( $cache_dir ) ) {
            wp_mkdir_p( $cache_dir );
            file_put_contents( $cache_dir . '.htaccess', 'deny from all' );
        }

        $cache_file = $cache_dir . 'snippet-' . $snippet_id . '.php';
        $cache_code = '<?php ' . PHP_EOL . '// JJ Code Snippet ID: ' . $snippet_id . PHP_EOL . $code;

        // 코드가 변경되었거나 캐시 파일이 없으면 갱신
        $existing_hash = file_exists( $cache_file ) ? md5_file( $cache_file ) : '';
        $new_hash = md5( $cache_code );

        if ( $existing_hash !== $new_hash ) {
            file_put_contents( $cache_file, $cache_code );
        }

        // 안전하게 실행
        try {
            include $cache_file;
        } catch ( Exception $e ) {
            error_log( 'JJ Snippet Error (ID: ' . $snippet_id . '): ' . $e->getMessage() );
        } catch ( Error $e ) {
            error_log( 'JJ Snippet Fatal Error (ID: ' . $snippet_id . '): ' . $e->getMessage() );
        }
    }
}
