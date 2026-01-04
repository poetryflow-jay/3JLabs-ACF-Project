<?php
/**
 * Dashboard View
 * 
 * @package ACF_Mail_SMTP
 * @version 1.0.0
 * @author 3J Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$plugin = ACF_Mail_SMTP::get_instance();

// Get statistics
global $wpdb;
$forms_table = $wpdb->prefix . 'acf_mail_smtp_forms';
$submissions_table = $wpdb->prefix . 'acf_mail_smtp_submissions';
$emails_table = $wpdb->prefix . 'acf_mail_smtp_emails';

$total_forms = $wpdb->get_var( "SELECT COUNT(*) FROM $forms_table WHERE status = 'active'" );
$total_submissions = $wpdb->get_var( "SELECT COUNT(*) FROM $submissions_table" );
$total_emails_sent = $wpdb->get_var( "SELECT COUNT(*) FROM $emails_table WHERE status = 'sent'" );
$total_emails_failed = $wpdb->get_var( "SELECT COUNT(*) FROM $emails_table WHERE status = 'failed'" );

// Recent submissions
$recent_submissions = $wpdb->get_results(
    "SELECT s.*, f.name as form_name 
    FROM $submissions_table s 
    LEFT JOIN $forms_table f ON s.form_id = f.id 
    ORDER BY s.created_at DESC 
    LIMIT 10",
    ARRAY_A
);

// Recent emails
$recent_emails = $wpdb->get_results(
    "SELECT * FROM $emails_table 
    ORDER BY created_at DESC 
    LIMIT 10",
    ARRAY_A
);
?>

<div class="wrap acf-mail-smtp-dashboard-v25">
    <h1 class="wp-bulk-seo-header-v25">
        <?php esc_html_e( 'ACF Mail SMTP 대시보드', 'acf-mail-smtp' ); ?>
    </h1>

    <!-- Statistics Cards -->
    <div class="wp-bulk-seo-score-grid-v25" style="margin-top: 30px;">
        <div class="wp-bulk-seo-score-card-v25" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="score-value" style="font-size: 48px; color: #fff;"><?php echo esc_html( $total_forms ); ?></div>
            <div class="score-label" style="color: #fff;"><?php esc_html_e( '활성 폼', 'acf-mail-smtp' ); ?></div>
        </div>
        <div class="wp-bulk-seo-score-card-v25" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="score-value" style="font-size: 48px; color: #fff;"><?php echo esc_html( $total_submissions ); ?></div>
            <div class="score-label" style="color: #fff;"><?php esc_html_e( '총 제출', 'acf-mail-smtp' ); ?></div>
        </div>
        <div class="wp-bulk-seo-score-card-v25" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="score-value" style="font-size: 48px; color: #fff;"><?php echo esc_html( $total_emails_sent ); ?></div>
            <div class="score-label" style="color: #fff;"><?php esc_html_e( '발송된 이메일', 'acf-mail-smtp' ); ?></div>
        </div>
        <div class="wp-bulk-seo-score-card-v25" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
            <div class="score-value" style="font-size: 48px; color: #fff;"><?php echo esc_html( $total_emails_failed ); ?></div>
            <div class="score-label" style="color: #fff;"><?php esc_html_e( '실패한 이메일', 'acf-mail-smtp' ); ?></div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="wp-bulk-seo-suggestions-grid-v25" style="margin-top: 30px;">
        <div class="wp-bulk-seo-suggestion-card-v25">
            <h3><?php esc_html_e( '빠른 작업', 'acf-mail-smtp' ); ?></h3>
            <div style="margin-top: 15px;">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-mail-smtp-forms&action=new' ) ); ?>" class="wp-bulk-seo-btn-v25 wp-bulk-seo-btn-v25-primary">
                    <?php esc_html_e( '새 폼 만들기', 'acf-mail-smtp' ); ?>
                </a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-mail-smtp-smtp' ) ); ?>" class="wp-bulk-seo-btn-v25 wp-bulk-seo-btn-v25-secondary" style="margin-left: 10px;">
                    <?php esc_html_e( 'SMTP 설정', 'acf-mail-smtp' ); ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Submissions -->
    <div class="wp-bulk-seo-suggestions-grid-v25" style="margin-top: 30px;">
        <div class="wp-bulk-seo-suggestion-card-v25">
            <h3><?php esc_html_e( '최근 제출', 'acf-mail-smtp' ); ?></h3>
            <table class="wp-list-table widefat fixed striped" style="margin-top: 15px;">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'ID', 'acf-mail-smtp' ); ?></th>
                        <th><?php esc_html_e( '폼', 'acf-mail-smtp' ); ?></th>
                        <th><?php esc_html_e( '제출 시간', 'acf-mail-smtp' ); ?></th>
                        <th><?php esc_html_e( '상태', 'acf-mail-smtp' ); ?></th>
                        <th><?php esc_html_e( '작업', 'acf-mail-smtp' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( ! empty( $recent_submissions ) ) : ?>
                        <?php foreach ( $recent_submissions as $submission ) : ?>
                            <tr>
                                <td><?php echo esc_html( $submission['id'] ); ?></td>
                                <td><?php echo esc_html( $submission['form_name'] ); ?></td>
                                <td><?php echo esc_html( $submission['created_at'] ); ?></td>
                                <td>
                                    <span class="status-<?php echo esc_attr( $submission['status'] ); ?>">
                                        <?php echo esc_html( ucfirst( $submission['status'] ) ); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-mail-smtp-submissions&id=' . $submission['id'] ) ); ?>">
                                        <?php esc_html_e( '보기', 'acf-mail-smtp' ); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5"><?php esc_html_e( '제출 내역이 없습니다.', 'acf-mail-smtp' ); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Emails -->
    <div class="wp-bulk-seo-suggestions-grid-v25" style="margin-top: 30px;">
        <div class="wp-bulk-seo-suggestion-card-v25">
            <h3><?php esc_html_e( '최근 이메일', 'acf-mail-smtp' ); ?></h3>
            <table class="wp-list-table widefat fixed striped" style="margin-top: 15px;">
                <thead>
                    <tr>
                        <th><?php esc_html_e( '받는 사람', 'acf-mail-smtp' ); ?></th>
                        <th><?php esc_html_e( '제목', 'acf-mail-smtp' ); ?></th>
                        <th><?php esc_html_e( '상태', 'acf-mail-smtp' ); ?></th>
                        <th><?php esc_html_e( '발송 시간', 'acf-mail-smtp' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( ! empty( $recent_emails ) ) : ?>
                        <?php foreach ( $recent_emails as $email ) : ?>
                            <tr>
                                <td><?php echo esc_html( $email['to_email'] ); ?></td>
                                <td><?php echo esc_html( $email['subject'] ); ?></td>
                                <td>
                                    <span class="status-<?php echo esc_attr( $email['status'] ); ?>">
                                        <?php echo esc_html( ucfirst( $email['status'] ) ); ?>
                                    </span>
                                </td>
                                <td><?php echo esc_html( $email['sent_at'] ? $email['sent_at'] : '-' ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4"><?php esc_html_e( '이메일 로그가 없습니다.', 'acf-mail-smtp' ); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.acf-mail-smtp-dashboard-v25 {
    max-width: 1400px;
}
.status-new {
    color: #2271b1;
    font-weight: 600;
}
.status-read {
    color: #00a32a;
    font-weight: 600;
}
.status-sent {
    color: #00a32a;
    font-weight: 600;
}
.status-failed {
    color: #d63638;
    font-weight: 600;
}
</style>
