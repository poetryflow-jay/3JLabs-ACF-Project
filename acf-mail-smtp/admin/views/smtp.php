<?php
/**
 * SMTP Settings View
 * 
 * @package ACF_Mail_SMTP
 * @version 1.0.0
 * @author 3J Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$plugin = ACF_Mail_SMTP::get_instance();
$smtp_manager = $plugin->smtp;
$settings = $smtp_manager->get_settings();
?>

<div class="wrap acf-mail-smtp-smtp-v25">
    <h1 class="wp-bulk-seo-header-v25">
        <?php esc_html_e( 'SMTP 설정', 'acf-mail-smtp' ); ?>
    </h1>

    <div class="wp-bulk-seo-suggestions-grid-v25" style="margin-top: 30px;">
        <div class="wp-bulk-seo-suggestion-card-v25">
            <form id="acf-mail-smtp-smtp-form">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="enable_smtp"><?php esc_html_e( 'SMTP 활성화', 'acf-mail-smtp' ); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" id="enable_smtp" name="enable_smtp" value="1" 
                                       <?php checked( $settings['enable_smtp'] ); ?> />
                                <?php esc_html_e( 'SMTP를 사용하여 이메일 발송', 'acf-mail-smtp' ); ?>
                            </label>
                            <p class="description"><?php esc_html_e( '체크하면 WordPress 기본 이메일 대신 SMTP를 사용합니다.', 'acf-mail-smtp' ); ?></p>
                        </td>
                    </tr>
                </table>

                <div id="smtp-settings" style="<?php echo $settings['enable_smtp'] ? '' : 'display: none;'; ?>">
                    <h2><?php esc_html_e( 'SMTP 서버 설정', 'acf-mail-smtp' ); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="smtp_host"><?php esc_html_e( 'SMTP 호스트', 'acf-mail-smtp' ); ?></label>
                            </th>
                            <td>
                                <input type="text" id="smtp_host" name="smtp_host" class="regular-text" 
                                       value="<?php echo esc_attr( $settings['smtp_host'] ); ?>" 
                                       placeholder="smtp.gmail.com" />
                                <p class="description"><?php esc_html_e( 'SMTP 서버 주소 (예: smtp.gmail.com)', 'acf-mail-smtp' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="smtp_port"><?php esc_html_e( 'SMTP 포트', 'acf-mail-smtp' ); ?></label>
                            </th>
                            <td>
                                <input type="number" id="smtp_port" name="smtp_port" class="small-text" 
                                       value="<?php echo esc_attr( $settings['smtp_port'] ); ?>" 
                                       min="1" max="65535" />
                                <p class="description"><?php esc_html_e( '일반적으로 587 (TLS) 또는 465 (SSL)', 'acf-mail-smtp' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="smtp_encryption"><?php esc_html_e( '암호화', 'acf-mail-smtp' ); ?></label>
                            </th>
                            <td>
                                <select id="smtp_encryption" name="smtp_encryption">
                                    <option value="none" <?php selected( $settings['smtp_encryption'], 'none' ); ?>><?php esc_html_e( '없음', 'acf-mail-smtp' ); ?></option>
                                    <option value="ssl" <?php selected( $settings['smtp_encryption'], 'ssl' ); ?>>SSL</option>
                                    <option value="tls" <?php selected( $settings['smtp_encryption'], 'tls' ); ?>>TLS</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="smtp_auth"><?php esc_html_e( '인증 사용', 'acf-mail-smtp' ); ?></label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" id="smtp_auth" name="smtp_auth" value="1" 
                                           <?php checked( $settings['smtp_auth'] ); ?> />
                                    <?php esc_html_e( 'SMTP 인증 사용', 'acf-mail-smtp' ); ?>
                                </label>
                            </td>
                        </tr>
                    </table>

                    <div id="auth-settings" style="<?php echo $settings['smtp_auth'] ? '' : 'display: none;'; ?>">
                        <h2><?php esc_html_e( '인증 정보', 'acf-mail-smtp' ); ?></h2>
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="smtp_username"><?php esc_html_e( '사용자 이름', 'acf-mail-smtp' ); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="smtp_username" name="smtp_username" class="regular-text" 
                                           value="<?php echo esc_attr( $settings['smtp_username'] ); ?>" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="smtp_password"><?php esc_html_e( '비밀번호', 'acf-mail-smtp' ); ?></label>
                                </th>
                                <td>
                                    <input type="password" id="smtp_password" name="smtp_password" class="regular-text" 
                                           placeholder="<?php esc_attr_e( '비밀번호를 변경하려면 입력하세요', 'acf-mail-smtp' ); ?>" />
                                    <p class="description"><?php esc_html_e( '비밀번호는 암호화되어 저장됩니다.', 'acf-mail-smtp' ); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <h2><?php esc_html_e( '발신자 정보', 'acf-mail-smtp' ); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="from_email"><?php esc_html_e( '발신 이메일', 'acf-mail-smtp' ); ?></label>
                            </th>
                            <td>
                                <input type="email" id="from_email" name="from_email" class="regular-text" 
                                       value="<?php echo esc_attr( $settings['from_email'] ); ?>" />
                                <p class="description"><?php esc_html_e( '이메일 발송 시 사용할 발신 주소', 'acf-mail-smtp' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="from_name"><?php esc_html_e( '발신자 이름', 'acf-mail-smtp' ); ?></label>
                            </th>
                            <td>
                                <input type="text" id="from_name" name="from_name" class="regular-text" 
                                       value="<?php echo esc_attr( $settings['from_name'] ); ?>" />
                                <p class="description"><?php esc_html_e( '이메일 발송 시 사용할 발신자 이름', 'acf-mail-smtp' ); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>

                <p class="submit">
                    <button type="submit" class="wp-bulk-seo-btn-v25 wp-bulk-seo-btn-v25-primary">
                        <?php esc_html_e( '설정 저장', 'acf-mail-smtp' ); ?>
                    </button>
                    <button type="button" class="wp-bulk-seo-btn-v25 wp-bulk-seo-btn-v25-secondary" id="test-smtp-btn" style="margin-left: 10px;">
                        <?php esc_html_e( '테스트 이메일 발송', 'acf-mail-smtp' ); ?>
                    </button>
                </p>
            </form>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Toggle SMTP settings
    $('#enable_smtp').on('change', function() {
        if ($(this).is(':checked')) {
            $('#smtp-settings').slideDown();
        } else {
            $('#smtp-settings').slideUp();
        }
    });

    // Toggle auth settings
    $('#smtp_auth').on('change', function() {
        if ($(this).is(':checked')) {
            $('#auth-settings').slideDown();
        } else {
            $('#auth-settings').slideUp();
        }
    });

    // Save SMTP settings
    $('#acf-mail-smtp-smtp-form').on('submit', function(e) {
        e.preventDefault();

        var settings = {
            enable_smtp: $('#enable_smtp').is(':checked'),
            smtp_host: $('#smtp_host').val(),
            smtp_port: $('#smtp_port').val(),
            smtp_encryption: $('#smtp_encryption').val(),
            smtp_auth: $('#smtp_auth').is(':checked'),
            smtp_username: $('#smtp_username').val(),
            smtp_password: $('#smtp_password').val(),
            from_email: $('#from_email').val(),
            from_name: $('#from_name').val(),
        };

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'acf_mail_smtp_save_smtp',
                nonce: acfMailSmtp.nonce,
                settings: settings
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                } else {
                    alert(response.data.message || acfMailSmtp.strings.error);
                }
            }
        });
    });

    // Test SMTP
    $('#test-smtp-btn').on('click', function() {
        var toEmail = prompt('<?php esc_html_e( '테스트 이메일을 받을 주소를 입력하세요:', 'acf-mail-smtp' ); ?>', '<?php echo esc_js( get_option( 'admin_email' ) ); ?>');

        if (!toEmail || !toEmail.includes('@')) {
            alert('<?php esc_html_e( '유효한 이메일 주소를 입력하세요.', 'acf-mail-smtp' ); ?>');
            return;
        }

        $(this).prop('disabled', true).text('<?php esc_html_e( '발송 중...', 'acf-mail-smtp' ); ?>');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'acf_mail_smtp_test_smtp',
                nonce: acfMailSmtp.nonce,
                to_email: toEmail
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                } else {
                    alert(response.data.message || acfMailSmtp.strings.error);
                }
            },
            complete: function() {
                $('#test-smtp-btn').prop('disabled', false).text('<?php esc_html_e( '테스트 이메일 발송', 'acf-mail-smtp' ); ?>');
            }
        });
    });
});
</script>
