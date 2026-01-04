<?php
/**
 * Settings View
 * 
 * @package ACF_Mail_SMTP
 * @version 1.0.0
 * @author 3J Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap acf-mail-smtp-settings-v25">
    <h1 class="wp-bulk-seo-header-v25">
        <?php esc_html_e( '설정', 'acf-mail-smtp' ); ?>
    </h1>

    <div class="wp-bulk-seo-suggestions-grid-v25" style="margin-top: 30px;">
        <div class="wp-bulk-seo-suggestion-card-v25">
            <form id="acf-mail-smtp-settings-form">
                <h2><?php esc_html_e( '일반 설정', 'acf-mail-smtp' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="enable_email_logs"><?php esc_html_e( '이메일 로그 활성화', 'acf-mail-smtp' ); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" id="enable_email_logs" name="enable_email_logs" value="1" 
                                       <?php checked( get_option( 'acf_mail_smtp_enable_email_logs', true ) ); ?> />
                                <?php esc_html_e( '이메일 발송 로그 저장', 'acf-mail-smtp' ); ?>
                            </label>
                            <p class="description"><?php esc_html_e( '모든 이메일 발송 내역을 데이터베이스에 저장합니다.', 'acf-mail-smtp' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="enable_automation"><?php esc_html_e( '자동화 활성화', 'acf-mail-smtp' ); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" id="enable_automation" name="enable_automation" value="1" 
                                       <?php checked( get_option( 'acf_mail_smtp_enable_automation', true ) ); ?> />
                                <?php esc_html_e( '자동화 규칙 실행', 'acf-mail-smtp' ); ?>
                            </label>
                            <p class="description"><?php esc_html_e( '폼 제출 후 자동화 규칙을 실행합니다.', 'acf-mail-smtp' ); ?></p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" class="wp-bulk-seo-btn-v25 wp-bulk-seo-btn-v25-primary">
                        <?php esc_html_e( '설정 저장', 'acf-mail-smtp' ); ?>
                    </button>
                </p>
            </form>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#acf-mail-smtp-settings-form').on('submit', function(e) {
        e.preventDefault();

        var settings = {
            enable_email_logs: $('#enable_email_logs').is(':checked'),
            enable_automation: $('#enable_automation').is(':checked'),
        };

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'acf_mail_smtp_save_settings',
                nonce: acfMailSmtp.nonce,
                settings: settings
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message || acfMailSmtp.strings.saved);
                } else {
                    alert(response.data.message || acfMailSmtp.strings.error);
                }
            }
        });
    });
});
</script>
