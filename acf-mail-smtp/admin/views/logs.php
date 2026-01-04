<?php
/**
 * Email Logs View
 * 
 * @package ACF_Mail_SMTP
 * @version 1.0.0
 * @author 3J Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;
$emails_table = $wpdb->prefix . 'acf_mail_smtp_emails';

$status = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : '';

$where = '';
$where_values = array();
if ( ! empty( $status ) ) {
    $where = 'WHERE status = %s';
    $where_values[] = $status;
}

$query = "SELECT * FROM $emails_table $where ORDER BY created_at DESC LIMIT 100";
if ( ! empty( $where_values ) ) {
    $emails = $wpdb->get_results( $wpdb->prepare( $query, $where_values ), ARRAY_A );
} else {
    $emails = $wpdb->get_results( $query, ARRAY_A );
}

$total_sent = $wpdb->get_var( "SELECT COUNT(*) FROM $emails_table WHERE status = 'sent'" );
$total_failed = $wpdb->get_var( "SELECT COUNT(*) FROM $emails_table WHERE status = 'failed'" );
$total_pending = $wpdb->get_var( "SELECT COUNT(*) FROM $emails_table WHERE status = 'pending'" );
?>

<div class="wrap acf-mail-smtp-logs-v25">
    <h1 class="wp-bulk-seo-header-v25">
        <?php esc_html_e( '이메일 로그', 'acf-mail-smtp' ); ?>
    </h1>

    <!-- Statistics -->
    <div class="wp-bulk-seo-score-grid-v25" style="margin-top: 30px;">
        <div class="wp-bulk-seo-score-card-v25" style="background: linear-gradient(135deg, #00a32a 0%, #00d084 100%);">
            <div class="score-value" style="font-size: 48px; color: #fff;"><?php echo esc_html( $total_sent ); ?></div>
            <div class="score-label" style="color: #fff;"><?php esc_html_e( '발송 성공', 'acf-mail-smtp' ); ?></div>
        </div>
        <div class="wp-bulk-seo-score-card-v25" style="background: linear-gradient(135deg, #d63638 0%, #f86368 100%);">
            <div class="score-value" style="font-size: 48px; color: #fff;"><?php echo esc_html( $total_failed ); ?></div>
            <div class="score-label" style="color: #fff;"><?php esc_html_e( '발송 실패', 'acf-mail-smtp' ); ?></div>
        </div>
        <div class="wp-bulk-seo-score-card-v25" style="background: linear-gradient(135deg, #2271b1 0%, #72aee6 100%);">
            <div class="score-value" style="font-size: 48px; color: #fff;"><?php echo esc_html( $total_pending ); ?></div>
            <div class="score-label" style="color: #fff;"><?php esc_html_e( '대기 중', 'acf-mail-smtp' ); ?></div>
        </div>
    </div>

    <!-- Filters -->
    <div class="wp-bulk-seo-suggestions-grid-v25" style="margin-top: 30px;">
        <div class="wp-bulk-seo-suggestion-card-v25">
            <form method="get">
                <input type="hidden" name="page" value="acf-mail-smtp-logs" />
                <select name="status">
                    <option value=""><?php esc_html_e( '모든 상태', 'acf-mail-smtp' ); ?></option>
                    <option value="sent" <?php selected( $status, 'sent' ); ?>><?php esc_html_e( '발송 성공', 'acf-mail-smtp' ); ?></option>
                    <option value="failed" <?php selected( $status, 'failed' ); ?>><?php esc_html_e( '발송 실패', 'acf-mail-smtp' ); ?></option>
                    <option value="pending" <?php selected( $status, 'pending' ); ?>><?php esc_html_e( '대기 중', 'acf-mail-smtp' ); ?></option>
                </select>
                <button type="submit" class="wp-bulk-seo-btn-v25 wp-bulk-seo-btn-v25-primary">
                    <?php esc_html_e( '필터', 'acf-mail-smtp' ); ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Email Logs Table -->
    <div class="wp-bulk-seo-suggestions-grid-v25" style="margin-top: 30px;">
        <div class="wp-bulk-seo-suggestion-card-v25">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'ID', 'acf-mail-smtp' ); ?></th>
                        <th><?php esc_html_e( '받는 사람', 'acf-mail-smtp' ); ?></th>
                        <th><?php esc_html_e( '제목', 'acf-mail-smtp' ); ?></th>
                        <th><?php esc_html_e( '상태', 'acf-mail-smtp' ); ?></th>
                        <th><?php esc_html_e( '발송 시간', 'acf-mail-smtp' ); ?></th>
                        <th><?php esc_html_e( '작업', 'acf-mail-smtp' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( ! empty( $emails ) ) : ?>
                        <?php foreach ( $emails as $email ) : ?>
                            <tr>
                                <td><?php echo esc_html( $email['id'] ); ?></td>
                                <td><?php echo esc_html( $email['to_email'] ); ?></td>
                                <td><?php echo esc_html( $email['subject'] ); ?></td>
                                <td>
                                    <span class="status-<?php echo esc_attr( $email['status'] ); ?>">
                                        <?php echo esc_html( ucfirst( $email['status'] ) ); ?>
                                    </span>
                                    <?php if ( ! empty( $email['error_message'] ) ) : ?>
                                        <br><small style="color: #d63638;"><?php echo esc_html( $email['error_message'] ); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo esc_html( $email['sent_at'] ? $email['sent_at'] : $email['created_at'] ); ?></td>
                                <td>
                                    <a href="#" class="view-email" data-email-id="<?php echo esc_attr( $email['id'] ); ?>">
                                        <?php esc_html_e( '보기', 'acf-mail-smtp' ); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6"><?php esc_html_e( '이메일 로그가 없습니다.', 'acf-mail-smtp' ); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('.view-email').on('click', function(e) {
        e.preventDefault();
        var emailId = $(this).data('email-id');
        // Show email content in modal (can be enhanced)
        alert('이메일 상세 보기 기능은 향후 구현 예정입니다.');
    });
});
</script>
