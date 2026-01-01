<?php
/**
 * ACF Code Snippets Box - Admin
 * 
 * 관리자 페이지 및 설정 관리
 *
 * @package ACF_Code_Snippets_Box
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Admin 클래스
 */
class ACF_CSB_Admin {

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
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
    }

    /**
     * 설정 등록
     */
    public function register_settings() {
        register_setting( 'acf_csb_settings_group', 'acf_csb_settings', array(
            'sanitize_callback' => array( $this, 'sanitize_settings' ),
        ) );

        // 일반 설정 섹션
        add_settings_section(
            'acf_csb_general_section',
            __( '일반 설정', 'acf-code-snippets-box' ),
            array( $this, 'render_general_section' ),
            'acf-code-snippets-settings'
        );

        // PHP 실행 설정
        add_settings_field(
            'enable_php_execution',
            __( 'PHP 코드 실행', 'acf-code-snippets-box' ),
            array( $this, 'render_php_execution_field' ),
            'acf-code-snippets-settings',
            'acf_csb_general_section'
        );

        // 에러 로깅
        add_settings_field(
            'enable_error_logging',
            __( '에러 로깅', 'acf-code-snippets-box' ),
            array( $this, 'render_error_logging_field' ),
            'acf-code-snippets-settings',
            'acf_csb_general_section'
        );

        // 에디터 설정 섹션
        add_settings_section(
            'acf_csb_editor_section',
            __( '에디터 설정', 'acf-code-snippets-box' ),
            array( $this, 'render_editor_section' ),
            'acf-code-snippets-settings'
        );

        // 문법 하이라이팅
        add_settings_field(
            'syntax_highlighting',
            __( '문법 하이라이팅', 'acf-code-snippets-box' ),
            array( $this, 'render_syntax_highlighting_field' ),
            'acf-code-snippets-settings',
            'acf_csb_editor_section'
        );

        // 자동 완성
        add_settings_field(
            'auto_complete',
            __( '자동 완성', 'acf-code-snippets-box' ),
            array( $this, 'render_auto_complete_field' ),
            'acf-code-snippets-settings',
            'acf_csb_editor_section'
        );
    }

    /**
     * 설정 정리
     */
    public function sanitize_settings( $input ) {
        $sanitized = array();
        
        $sanitized['enable_php_execution'] = ! empty( $input['enable_php_execution'] );
        $sanitized['enable_error_logging'] = ! empty( $input['enable_error_logging'] );
        $sanitized['syntax_highlighting']  = ! empty( $input['syntax_highlighting'] );
        $sanitized['auto_complete']        = ! empty( $input['auto_complete'] );
        
        return $sanitized;
    }

    /**
     * 일반 섹션 렌더링
     */
    public function render_general_section() {
        echo '<p>' . esc_html__( '코드 스니펫 실행과 관련된 설정입니다.', 'acf-code-snippets-box' ) . '</p>';
    }

    /**
     * 에디터 섹션 렌더링
     */
    public function render_editor_section() {
        echo '<p>' . esc_html__( '코드 에디터 관련 설정입니다.', 'acf-code-snippets-box' ) . '</p>';
    }

    /**
     * PHP 실행 필드 렌더링
     */
    public function render_php_execution_field() {
        $settings = get_option( 'acf_csb_settings', array() );
        $enabled = ! empty( $settings['enable_php_execution'] );
        ?>
        <label>
            <input type="checkbox" name="acf_csb_settings[enable_php_execution]" value="1" <?php checked( $enabled ); ?>>
            <?php esc_html_e( 'PHP 코드 실행 허용', 'acf-code-snippets-box' ); ?>
        </label>
        <p class="description" style="color: #d63638;">
            <strong><?php esc_html_e( '⚠️ 보안 경고:', 'acf-code-snippets-box' ); ?></strong>
            <?php esc_html_e( 'PHP 코드 실행을 활성화하면 보안 위험이 있습니다. 신뢰할 수 있는 코드만 실행하세요.', 'acf-code-snippets-box' ); ?>
        </p>
        <?php
    }

    /**
     * 에러 로깅 필드 렌더링
     */
    public function render_error_logging_field() {
        $settings = get_option( 'acf_csb_settings', array() );
        $enabled = isset( $settings['enable_error_logging'] ) ? $settings['enable_error_logging'] : true;
        ?>
        <label>
            <input type="checkbox" name="acf_csb_settings[enable_error_logging]" value="1" <?php checked( $enabled ); ?>>
            <?php esc_html_e( '에러 로그 기록', 'acf-code-snippets-box' ); ?>
        </label>
        <p class="description">
            <?php 
            printf(
                esc_html__( '에러 로그 파일: %s', 'acf-code-snippets-box' ),
                '<code>' . esc_html( WP_CONTENT_DIR . '/acf-csb-error.log' ) . '</code>'
            );
            ?>
        </p>
        <?php
    }

    /**
     * 문법 하이라이팅 필드 렌더링
     */
    public function render_syntax_highlighting_field() {
        $settings = get_option( 'acf_csb_settings', array() );
        $enabled = isset( $settings['syntax_highlighting'] ) ? $settings['syntax_highlighting'] : true;
        ?>
        <label>
            <input type="checkbox" name="acf_csb_settings[syntax_highlighting]" value="1" <?php checked( $enabled ); ?>>
            <?php esc_html_e( '문법 하이라이팅 활성화', 'acf-code-snippets-box' ); ?>
        </label>
        <?php
    }

    /**
     * 자동 완성 필드 렌더링
     */
    public function render_auto_complete_field() {
        $settings = get_option( 'acf_csb_settings', array() );
        $enabled = isset( $settings['auto_complete'] ) ? $settings['auto_complete'] : true;
        ?>
        <label>
            <input type="checkbox" name="acf_csb_settings[auto_complete]" value="1" <?php checked( $enabled ); ?>>
            <?php esc_html_e( '자동 완성 활성화', 'acf-code-snippets-box' ); ?>
        </label>
        <p class="description">
            <?php esc_html_e( 'CSS 속성, JavaScript 함수, PHP 함수 등의 자동 완성을 제공합니다.', 'acf-code-snippets-box' ); ?>
        </p>
        <?php
    }

    /**
     * 관리자 알림
     */
    public function admin_notices() {
        // PHP 실행 경고
        $settings = get_option( 'acf_csb_settings', array() );
        if ( ! empty( $settings['enable_php_execution'] ) ) {
            $screen = get_current_screen();
            if ( $screen && strpos( $screen->id, 'acf-code-snippets' ) !== false ) {
                ?>
                <div class="notice notice-warning">
                    <p>
                        <strong><?php esc_html_e( '⚠️ PHP 실행이 활성화되어 있습니다.', 'acf-code-snippets-box' ); ?></strong>
                        <?php esc_html_e( '신뢰할 수 있는 코드만 실행하세요.', 'acf-code-snippets-box' ); ?>
                    </p>
                </div>
                <?php
            }
        }

        // ACF CSS 연동 알림
        if ( ACF_Code_Snippets_Box::is_acf_css_active() ) {
            $screen = get_current_screen();
            if ( $screen && $screen->id === 'acf_code_snippet' ) {
                ?>
                <div class="notice notice-info is-dismissible">
                    <p>
                        <strong><?php esc_html_e( '💡 ACF CSS 연동 활성화', 'acf-code-snippets-box' ); ?></strong>
                        <?php esc_html_e( 'CSS 코드에서 ACF CSS 스타일 변수(--jj-*)를 사용할 수 있습니다.', 'acf-code-snippets-box' ); ?>
                    </p>
                </div>
                <?php
            }
        }
    }
}

// 인스턴스 생성
ACF_CSB_Admin::instance();
