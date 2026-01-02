<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * JJ Admin Theme
 * 
 * 워드프레스 관리자 화면의 테마를 제어하는 엔진입니다.
 * CSS 변수를 활용하여 다크 모드, 브랜드 컬러 모드 등을 지원합니다.
 * 
 * @since v5.6.0
 */
class JJ_Admin_Theme {

    private static $instance = null;
    private $options = array();

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->options = get_option( 'jj_style_guide_visual_options', array() );
        
        // 관리자 화면에서만 실행
        if ( is_admin() ) {
            add_action( 'admin_head', array( $this, 'inject_admin_theme_css' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_theme_assets' ) );
        }
    }

    public function enqueue_admin_theme_assets() {
        // 필요 시 별도 CSS 파일 로드
    }

    /**
     * 관리자 테마 CSS 주입
     */
    public function inject_admin_theme_css() {
        // [설정값 예시]
        $theme_mode = isset( $this->options['admin_theme_mode'] ) ? $this->options['admin_theme_mode'] : 'default';
        $accent_color = isset( $this->options['admin_accent_color'] ) ? $this->options['admin_accent_color'] : '';

        if ( $theme_mode === 'default' && empty( $accent_color ) ) {
            return;
        }

        echo '<style type="text/css" id="jj-admin-theme-css">';
        echo ':root {';

        if ( $theme_mode === 'dark' ) {
            // 다크 모드 변수 (간단 예시)
            echo '--jj-admin-bg-base: #1e1e1e;';
            echo '--jj-admin-bg-content: #2c2c2c;';
            echo '--jj-admin-text-base: #e0e0e0;';
            echo '--jj-admin-border-color: #444;';
            
            // WP 기본 스타일 덮어쓰기 (강력하게)
            echo '
            }
            body.wp-admin { background-color: var(--jj-admin-bg-base) !important; color: var(--jj-admin-text-base) !important; }
            #wpcontent, #wpfooter { background-color: var(--jj-admin-bg-base) !important; }
            .postbox, .card, .wrap .notice { background-color: var(--jj-admin-bg-content) !important; color: var(--jj-admin-text-base) !important; border-color: var(--jj-admin-border-color) !important; }
            h1, h2, h3, h4, h5, h6, label, th, td { color: var(--jj-admin-text-base) !important; }
            :root {
            ';
        }

        if ( ! empty( $accent_color ) ) {
            // 브랜드 컬러 적용
            echo "--jj-admin-accent: {$accent_color};";
            
            // WP 메뉴 및 버튼 덮어쓰기
            echo '
            }
            #adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head, #adminmenu .wp-menu-arrow, #adminmenu .wp-menu-arrow div, #adminmenu li.current a.menu-top, #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu, .folded #adminmenu li.current.menu-top, .folded #adminmenu li.wp-has-current-submenu { background: var(--jj-admin-accent) !important; }
            .wp-core-ui .button-primary { background: var(--jj-admin-accent) !important; border-color: var(--jj-admin-accent) !important; }
            :root {
            ';
        }

        echo '}';
        echo '</style>';
    }
}

