<?php
/**
 * Frontend
 * 
 * 프론트엔드 폼 렌더링 및 처리
 * 
 * @package ACF_Mail_SMTP
 * @version 1.0.0
 * @author 3J Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_Mail_SMTP_Frontend {

    /**
     * 플러그인 인스턴스
     */
    private $plugin;

    /**
     * 생성자
     */
    public function __construct( $plugin ) {
        $this->plugin = $plugin;
        $this->init_hooks();
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // Enqueue frontend assets
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    /**
     * 프론트엔드 에셋 로드
     */
    public function enqueue_assets() {
        // Only load on pages with forms
        if ( ! $this->has_form_on_page() ) {
            return;
        }

        // CSS
        wp_enqueue_style(
            'acf-mail-smtp-frontend',
            ACF_MAIL_SMTP_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            ACF_MAIL_SMTP_VERSION
        );

        // JS
        wp_enqueue_script(
            'acf-mail-smtp-frontend',
            ACF_MAIL_SMTP_PLUGIN_URL . 'assets/js/frontend.js',
            array( 'jquery' ),
            ACF_MAIL_SMTP_VERSION,
            true
        );

        // Localize script
        wp_localize_script( 'acf-mail-smtp-frontend', 'acfMailSmtp', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'acf_mail_smtp_nonce' ),
            'strings' => array(
                'submitting' => __( '제출 중...', 'acf-mail-smtp' ),
                'error' => __( '오류가 발생했습니다.', 'acf-mail-smtp' ),
            ),
        ) );
    }

    /**
     * 페이지에 폼이 있는지 확인
     */
    private function has_form_on_page() {
        global $post;

        if ( ! $post ) {
            return false;
        }

        // Check for shortcode
        if ( has_shortcode( $post->post_content, 'acf_form' ) ) {
            return true;
        }

        return false;
    }
}
