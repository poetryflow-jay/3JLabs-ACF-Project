<?php
/**
 * JJ Font Manager - 웹폰트 관리 클래스
 * 
 * Google Fonts, Adobe Fonts, 로컬 폰트 관리를 담당합니다.
 * 폰트 로딩 최적화와 성능 관리 기능을 제공합니다.
 * 
 * @package ACF_CSS_Style_Guide
 * @version 22.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class JJ_Font_Manager
 */
class JJ_Font_Manager {

    /**
     * 싱글톤 인스턴스
     * @var JJ_Font_Manager|null
     */
    private static $instance = null;

    /**
     * 옵션 키
     * @var string
     */
    private $option_key = 'jj_style_guide_fonts';

    /**
     * 등록된 폰트 목록
     * @var array
     */
    private $registered_fonts = array();

    /**
     * 생성자
     */
    private function __construct() {
        $this->init_hooks();
    }

    /**
     * 싱글톤 인스턴스 반환
     * @return JJ_Font_Manager
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // 폰트 로딩
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_fonts' ), 1 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_fonts' ), 1 );
        
        // 폰트 프리로드
        add_action( 'wp_head', array( $this, 'output_font_preload' ), 1 );
        
        // AJAX 핸들러
        add_action( 'wp_ajax_jj_save_font_settings', array( $this, 'ajax_save_font_settings' ) );
        add_action( 'wp_ajax_jj_get_google_fonts', array( $this, 'ajax_get_google_fonts' ) );
        add_action( 'wp_ajax_jj_upload_local_font', array( $this, 'ajax_upload_local_font' ) );
        
        // REST API
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
    }

    /**
     * 폰트 설정 가져오기
     * @return array
     */
    public function get_font_settings() {
        return get_option( $this->option_key, $this->get_default_settings() );
    }

    /**
     * 기본 폰트 설정
     * @return array
     */
    public function get_default_settings() {
        return array(
            'google_fonts' => array(
                array(
                    'family'  => 'Noto Sans KR',
                    'weights' => array( '400', '500', '700' ),
                    'display' => 'swap',
                ),
            ),
            'adobe_fonts' => array(),
            'local_fonts' => array(),
            'font_display' => 'swap',
            'preload_fonts' => array(),
            'disable_google_fonts' => false,
            'self_host_google_fonts' => false,
        );
    }

    /**
     * 폰트 설정 저장
     * @param array $settings
     * @return bool
     */
    public function save_font_settings( $settings ) {
        return update_option( $this->option_key, $settings );
    }

    /**
     * Google Fonts 등록
     * @param string $family 폰트 패밀리
     * @param array $weights 폰트 굵기 배열
     * @param string $display font-display 값
     */
    public function register_google_font( $family, $weights = array( '400' ), $display = 'swap' ) {
        $this->registered_fonts['google'][] = array(
            'family'  => $family,
            'weights' => $weights,
            'display' => $display,
        );
    }

    /**
     * 로컬 폰트 등록
     * @param string $family 폰트 패밀리
     * @param array $files 폰트 파일 배열
     */
    public function register_local_font( $family, $files ) {
        $this->registered_fonts['local'][] = array(
            'family' => $family,
            'files'  => $files,
        );
    }

    /**
     * 프론트엔드 폰트 enqueue
     */
    public function enqueue_fonts() {
        $settings = $this->get_font_settings();
        
        if ( ! empty( $settings['disable_google_fonts'] ) ) {
            return;
        }
        
        // Google Fonts
        if ( ! empty( $settings['google_fonts'] ) ) {
            $this->enqueue_google_fonts( $settings['google_fonts'], $settings['font_display'] );
        }
        
        // 로컬 폰트
        if ( ! empty( $settings['local_fonts'] ) ) {
            $this->enqueue_local_fonts( $settings['local_fonts'] );
        }
    }

    /**
     * 관리자 폰트 enqueue
     */
    public function enqueue_admin_fonts() {
        $settings = $this->get_font_settings();
        
        // 관리자에서도 프리뷰를 위해 폰트 로드
        if ( ! empty( $settings['google_fonts'] ) ) {
            $this->enqueue_google_fonts( $settings['google_fonts'], $settings['font_display'] );
        }
    }

    /**
     * Google Fonts enqueue
     * @param array $fonts
     * @param string $display
     */
    private function enqueue_google_fonts( $fonts, $display = 'swap' ) {
        if ( empty( $fonts ) ) {
            return;
        }
        
        $families = array();
        
        foreach ( $fonts as $font ) {
            if ( empty( $font['family'] ) ) {
                continue;
            }
            
            $family = str_replace( ' ', '+', $font['family'] );
            $weights = ! empty( $font['weights'] ) ? implode( ';', $font['weights'] ) : '400';
            
            // Google Fonts API v2 형식
            $families[] = "family={$family}:wght@{$weights}";
        }
        
        if ( ! empty( $families ) ) {
            $url = 'https://fonts.googleapis.com/css2?' . implode( '&', $families ) . '&display=' . $display;
            wp_enqueue_style( 'jj-google-fonts', $url, array(), null );
        }
    }

    /**
     * 로컬 폰트 enqueue
     * @param array $fonts
     */
    private function enqueue_local_fonts( $fonts ) {
        if ( empty( $fonts ) ) {
            return;
        }
        
        $css = '';
        
        foreach ( $fonts as $font ) {
            if ( empty( $font['family'] ) || empty( $font['files'] ) ) {
                continue;
            }
            
            foreach ( $font['files'] as $file ) {
                $weight = isset( $file['weight'] ) ? $file['weight'] : '400';
                $style = isset( $file['style'] ) ? $file['style'] : 'normal';
                $url = isset( $file['url'] ) ? $file['url'] : '';
                
                if ( empty( $url ) ) {
                    continue;
                }
                
                $css .= "@font-face {\n";
                $css .= "  font-family: '{$font['family']}';\n";
                $css .= "  src: url('{$url}');\n";
                $css .= "  font-weight: {$weight};\n";
                $css .= "  font-style: {$style};\n";
                $css .= "  font-display: swap;\n";
                $css .= "}\n\n";
            }
        }
        
        if ( ! empty( $css ) ) {
            wp_add_inline_style( 'wp-block-library', $css );
        }
    }

    /**
     * 폰트 프리로드 출력
     */
    public function output_font_preload() {
        $settings = $this->get_font_settings();
        
        if ( empty( $settings['preload_fonts'] ) ) {
            return;
        }
        
        foreach ( $settings['preload_fonts'] as $font ) {
            if ( empty( $font['url'] ) ) {
                continue;
            }
            
            $type = isset( $font['type'] ) ? $font['type'] : 'font/woff2';
            echo '<link rel="preload" href="' . esc_url( $font['url'] ) . '" as="font" type="' . esc_attr( $type ) . '" crossorigin="anonymous">' . "\n";
        }
    }

    /**
     * Google Fonts 목록 가져오기 (캐시됨)
     * @return array
     */
    public function get_google_fonts_list() {
        $cache_key = 'jj_google_fonts_list';
        $cached = get_transient( $cache_key );
        
        if ( false !== $cached ) {
            return $cached;
        }
        
        // 인기 폰트 목록 (API 호출 대신 정적 목록 사용)
        $fonts = array(
            array( 'family' => 'Noto Sans KR', 'category' => 'sans-serif' ),
            array( 'family' => 'Roboto', 'category' => 'sans-serif' ),
            array( 'family' => 'Open Sans', 'category' => 'sans-serif' ),
            array( 'family' => 'Lato', 'category' => 'sans-serif' ),
            array( 'family' => 'Montserrat', 'category' => 'sans-serif' ),
            array( 'family' => 'Poppins', 'category' => 'sans-serif' ),
            array( 'family' => 'Inter', 'category' => 'sans-serif' ),
            array( 'family' => 'Nanum Gothic', 'category' => 'sans-serif' ),
            array( 'family' => 'Nanum Myeongjo', 'category' => 'serif' ),
            array( 'family' => 'Spoqa Han Sans Neo', 'category' => 'sans-serif' ),
            array( 'family' => 'Pretendard', 'category' => 'sans-serif' ),
            array( 'family' => 'Playfair Display', 'category' => 'serif' ),
            array( 'family' => 'Merriweather', 'category' => 'serif' ),
            array( 'family' => 'Source Code Pro', 'category' => 'monospace' ),
            array( 'family' => 'Fira Code', 'category' => 'monospace' ),
            array( 'family' => 'JetBrains Mono', 'category' => 'monospace' ),
        );
        
        set_transient( $cache_key, $fonts, WEEK_IN_SECONDS );
        
        return $fonts;
    }

    /**
     * REST API 라우트 등록
     */
    public function register_rest_routes() {
        register_rest_route( 'jj-style-guide/v1', '/fonts', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'rest_get_fonts' ),
                'permission_callback' => '__return_true',
            ),
            array(
                'methods'             => 'POST',
                'callback'            => array( $this, 'rest_save_fonts' ),
                'permission_callback' => array( $this, 'rest_permission_check' ),
            ),
        ) );

        register_rest_route( 'jj-style-guide/v1', '/fonts/google', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'rest_get_google_fonts' ),
            'permission_callback' => '__return_true',
        ) );
    }

    /**
     * REST 권한 체크
     */
    public function rest_permission_check() {
        return current_user_can( 'manage_options' );
    }

    /**
     * REST: 폰트 설정 가져오기
     */
    public function rest_get_fonts() {
        return rest_ensure_response( $this->get_font_settings() );
    }

    /**
     * REST: 폰트 설정 저장
     */
    public function rest_save_fonts( $request ) {
        $params = $request->get_json_params();
        $result = $this->save_font_settings( $params );
        return rest_ensure_response( array( 'success' => $result ) );
    }

    /**
     * REST: Google Fonts 목록
     */
    public function rest_get_google_fonts() {
        return rest_ensure_response( $this->get_google_fonts_list() );
    }

    /**
     * AJAX: 폰트 설정 저장
     */
    public function ajax_save_font_settings() {
        check_ajax_referer( 'jj_style_guide_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
        
        $settings = isset( $_POST['settings'] ) ? json_decode( stripslashes( $_POST['settings'] ), true ) : array();
        $result = $this->save_font_settings( $settings );
        
        if ( $result ) {
            wp_send_json_success( __( '폰트 설정이 저장되었습니다.', 'acf-css-really-simple-style-management-center' ) );
        } else {
            wp_send_json_error( __( '저장에 실패했습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
    }

    /**
     * AJAX: Google Fonts 목록 가져오기
     */
    public function ajax_get_google_fonts() {
        check_ajax_referer( 'jj_style_guide_nonce', 'nonce' );
        wp_send_json_success( $this->get_google_fonts_list() );
    }

    /**
     * AJAX: 로컬 폰트 업로드
     */
    public function ajax_upload_local_font() {
        check_ajax_referer( 'jj_style_guide_nonce', 'nonce' );
        
        if ( ! current_user_can( 'upload_files' ) ) {
            wp_send_json_error( __( '파일 업로드 권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
        
        if ( empty( $_FILES['font_file'] ) ) {
            wp_send_json_error( __( '파일이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
        
        $allowed_types = array( 'woff', 'woff2', 'ttf', 'otf', 'eot' );
        $file = $_FILES['font_file'];
        $ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
        
        if ( ! in_array( $ext, $allowed_types, true ) ) {
            wp_send_json_error( __( '허용되지 않는 파일 형식입니다. (woff, woff2, ttf, otf, eot)', 'acf-css-really-simple-style-management-center' ) );
        }
        
        // WordPress 미디어 라이브러리에 업로드
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        
        $upload = wp_handle_upload( $file, array( 'test_form' => false ) );
        
        if ( isset( $upload['error'] ) ) {
            wp_send_json_error( $upload['error'] );
        }
        
        wp_send_json_success( array(
            'url'      => $upload['url'],
            'file'     => $upload['file'],
            'type'     => $upload['type'],
            'filename' => basename( $upload['file'] ),
        ) );
    }

    /**
     * 폰트 스택 생성
     * @param string $primary 주요 폰트
     * @param string $fallback 대체 폰트 스택
     * @return string
     */
    public function build_font_stack( $primary, $fallback = 'sans-serif' ) {
        $fallback_stacks = array(
            'sans-serif' => 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
            'serif'      => 'Georgia, Cambria, "Times New Roman", Times, serif',
            'monospace'  => 'ui-monospace, SFMono-Regular, "SF Mono", Menlo, Consolas, monospace',
        );
        
        $stack = isset( $fallback_stacks[ $fallback ] ) ? $fallback_stacks[ $fallback ] : $fallback;
        
        return '"' . $primary . '", ' . $stack;
    }
}

// 인스턴스 초기화
add_action( 'init', function() {
    JJ_Font_Manager::instance();
}, 5 );
