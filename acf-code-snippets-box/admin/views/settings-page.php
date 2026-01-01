<?php
/**
 * ACF Code Snippets Box - 설정 페이지
 *
 * @package ACF_Code_Snippets_Box
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap">
    <h1>
        <span class="dashicons dashicons-admin-generic" style="font-size: 30px; width: 30px; height: 30px; margin-right: 10px;"></span>
        <?php esc_html_e( 'Code Snippets 설정', 'acf-code-snippets-box' ); ?>
    </h1>

    <form method="post" action="options.php">
        <?php
        settings_fields( 'acf_csb_settings_group' );
        do_settings_sections( 'acf-code-snippets-settings' );
        submit_button();
        ?>
    </form>

    <hr>

    <!-- 시스템 정보 -->
    <div class="acf-csb-system-info" style="background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-top: 30px;">
        <h2><?php esc_html_e( '시스템 정보', 'acf-code-snippets-box' ); ?></h2>
        <table class="widefat striped">
            <tbody>
                <tr>
                    <th><?php esc_html_e( '플러그인 버전', 'acf-code-snippets-box' ); ?></th>
                    <td><?php echo esc_html( ACF_CSB_VERSION ); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'WordPress 버전', 'acf-code-snippets-box' ); ?></th>
                    <td><?php echo esc_html( get_bloginfo( 'version' ) ); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'PHP 버전', 'acf-code-snippets-box' ); ?></th>
                    <td><?php echo esc_html( phpversion() ); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'ACF CSS 연동', 'acf-code-snippets-box' ); ?></th>
                    <td>
                        <?php if ( ACF_Code_Snippets_Box::is_acf_css_active() ) : ?>
                            <span style="color: #00a32a;">✓ <?php esc_html_e( '활성화됨', 'acf-code-snippets-box' ); ?></span>
                            (v<?php echo esc_html( JJ_STYLE_GUIDE_VERSION ); ?>)
                        <?php else : ?>
                            <span style="color: #999;">✗ <?php esc_html_e( '비활성화', 'acf-code-snippets-box' ); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( '에러 로그 파일', 'acf-code-snippets-box' ); ?></th>
                    <td>
                        <code><?php echo esc_html( WP_CONTENT_DIR . '/acf-csb-error.log' ); ?></code>
                        <?php if ( file_exists( WP_CONTENT_DIR . '/acf-csb-error.log' ) ) : ?>
                            <span style="color: #00a32a;"><?php esc_html_e( '(존재함)', 'acf-code-snippets-box' ); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
