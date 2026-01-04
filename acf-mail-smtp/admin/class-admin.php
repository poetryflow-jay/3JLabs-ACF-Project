<?php
/**
 * Admin Interface Class
 * 
 * @package ACF_Mail_SMTP
 * @version 1.0.0
 * @author 3J Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_Mail_SMTP_Admin {

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
        // Admin notices
        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
    }

    /**
     * 관리자 알림 표시
     */
    public function admin_notices() {
        // Check if SMTP is enabled but not configured
        $enable_smtp = get_option( 'acf_mail_smtp_enable_smtp', false );
        $smtp_host = get_option( 'acf_mail_smtp_smtp_host', '' );

        if ( $enable_smtp && empty( $smtp_host ) ) {
            $screen = get_current_screen();
            if ( $screen && strpos( $screen->id, 'acf-mail-smtp' ) !== false ) {
                ?>
                <div class="notice notice-warning is-dismissible">
                    <p>
                        <strong><?php esc_html_e( 'ACF Mail SMTP', 'acf-mail-smtp' ); ?>:</strong>
                        <?php esc_html_e( 'SMTP가 활성화되었지만 설정이 완료되지 않았습니다.', 'acf-mail-smtp' ); ?>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-mail-smtp-smtp' ) ); ?>">
                            <?php esc_html_e( 'SMTP 설정으로 이동', 'acf-mail-smtp' ); ?>
                        </a>
                    </p>
                </div>
                <?php
            }
        }
    }
}
