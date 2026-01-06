<?php
/**
 * Email Logs View
 *
 * @package ACF_Mail_SMTP
 * @version 2.2.0
 * @author 3J Labs
 *
 * [v2.2.0] 이메일 상세 모달, 재발송, 삭제, CSV 내보내기 기능 추가
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
$total_all = intval( $total_sent ) + intval( $total_failed ) + intval( $total_pending );
?>

<div class="wrap acf-mail-smtp-logs-v25">
    <h1 class="wp-bulk-seo-header-v25">
        <?php esc_html_e( '이메일 로그', 'acf-mail-smtp' ); ?>
    </h1>

    <!-- Statistics -->
    <div class="wp-bulk-seo-score-grid-v25" style="margin-top: 30px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
        <div class="wp-bulk-seo-score-card-v25" style="background: linear-gradient(135deg, #00a32a 0%, #00d084 100%); padding: 24px; border-radius: 12px; text-align: center;">
            <div class="score-value" style="font-size: 48px; color: #fff; font-weight: 700;"><?php echo esc_html( $total_sent ); ?></div>
            <div class="score-label" style="color: #fff; font-size: 14px; margin-top: 8px;"><?php esc_html_e( '발송 성공', 'acf-mail-smtp' ); ?></div>
        </div>
        <div class="wp-bulk-seo-score-card-v25" style="background: linear-gradient(135deg, #d63638 0%, #f86368 100%); padding: 24px; border-radius: 12px; text-align: center;">
            <div class="score-value" style="font-size: 48px; color: #fff; font-weight: 700;"><?php echo esc_html( $total_failed ); ?></div>
            <div class="score-label" style="color: #fff; font-size: 14px; margin-top: 8px;"><?php esc_html_e( '발송 실패', 'acf-mail-smtp' ); ?></div>
        </div>
        <div class="wp-bulk-seo-score-card-v25" style="background: linear-gradient(135deg, #2271b1 0%, #72aee6 100%); padding: 24px; border-radius: 12px; text-align: center;">
            <div class="score-value" style="font-size: 48px; color: #fff; font-weight: 700;"><?php echo esc_html( $total_pending ); ?></div>
            <div class="score-label" style="color: #fff; font-size: 14px; margin-top: 8px;"><?php esc_html_e( '대기 중', 'acf-mail-smtp' ); ?></div>
        </div>
        <div class="wp-bulk-seo-score-card-v25" style="background: linear-gradient(135deg, #8c8f94 0%, #a7aaad 100%); padding: 24px; border-radius: 12px; text-align: center;">
            <div class="score-value" style="font-size: 48px; color: #fff; font-weight: 700;"><?php echo esc_html( $total_all ); ?></div>
            <div class="score-label" style="color: #fff; font-size: 14px; margin-top: 8px;"><?php esc_html_e( '전체', 'acf-mail-smtp' ); ?></div>
        </div>
    </div>

    <!-- [v2.2.0] 도구 모음 -->
    <div class="wp-bulk-seo-suggestions-grid-v25" style="margin-top: 30px;">
        <div class="wp-bulk-seo-suggestion-card-v25" style="padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <!-- 필터 -->
            <form method="get" style="display: flex; gap: 10px; align-items: center;">
                <input type="hidden" name="page" value="acf-mail-smtp-logs" />
                <select name="status" style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ddd;">
                    <option value=""><?php esc_html_e( '모든 상태', 'acf-mail-smtp' ); ?></option>
                    <option value="sent" <?php selected( $status, 'sent' ); ?>><?php esc_html_e( '발송 성공', 'acf-mail-smtp' ); ?></option>
                    <option value="failed" <?php selected( $status, 'failed' ); ?>><?php esc_html_e( '발송 실패', 'acf-mail-smtp' ); ?></option>
                    <option value="pending" <?php selected( $status, 'pending' ); ?>><?php esc_html_e( '대기 중', 'acf-mail-smtp' ); ?></option>
                </select>
                <button type="submit" class="button button-primary">
                    <?php esc_html_e( '필터', 'acf-mail-smtp' ); ?>
                </button>
            </form>

            <!-- 도구 버튼들 -->
            <div style="display: flex; gap: 10px;">
                <button type="button" id="export-csv-btn" class="button" style="display: flex; align-items: center; gap: 5px;">
                    <span class="dashicons dashicons-download" style="font-size: 16px;"></span>
                    <?php esc_html_e( 'CSV 내보내기', 'acf-mail-smtp' ); ?>
                </button>
                <button type="button" id="delete-old-logs-btn" class="button" style="display: flex; align-items: center; gap: 5px;">
                    <span class="dashicons dashicons-trash" style="font-size: 16px;"></span>
                    <?php esc_html_e( '오래된 로그 삭제', 'acf-mail-smtp' ); ?>
                </button>
            </div>
        </div>
    </div>

    <!-- Email Logs Table -->
    <div class="wp-bulk-seo-suggestions-grid-v25" style="margin-top: 20px;">
        <div class="wp-bulk-seo-suggestion-card-v25" style="padding: 0; background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
            <table class="wp-list-table widefat fixed striped" style="margin: 0;">
                <thead>
                    <tr>
                        <th style="width: 60px;"><?php esc_html_e( 'ID', 'acf-mail-smtp' ); ?></th>
                        <th style="width: 200px;"><?php esc_html_e( '받는 사람', 'acf-mail-smtp' ); ?></th>
                        <th><?php esc_html_e( '제목', 'acf-mail-smtp' ); ?></th>
                        <th style="width: 120px;"><?php esc_html_e( '상태', 'acf-mail-smtp' ); ?></th>
                        <th style="width: 160px;"><?php esc_html_e( '발송 시간', 'acf-mail-smtp' ); ?></th>
                        <th style="width: 180px;"><?php esc_html_e( '작업', 'acf-mail-smtp' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( ! empty( $emails ) ) : ?>
                        <?php foreach ( $emails as $email ) : ?>
                            <tr data-email-id="<?php echo esc_attr( $email['id'] ); ?>">
                                <td><?php echo esc_html( $email['id'] ); ?></td>
                                <td><?php echo esc_html( $email['to_email'] ); ?></td>
                                <td><?php echo esc_html( wp_trim_words( $email['subject'], 10 ) ); ?></td>
                                <td>
                                    <?php
                                    $status_colors = array(
                                        'sent' => '#00a32a',
                                        'failed' => '#d63638',
                                        'pending' => '#2271b1',
                                    );
                                    $status_labels = array(
                                        'sent' => __( '성공', 'acf-mail-smtp' ),
                                        'failed' => __( '실패', 'acf-mail-smtp' ),
                                        'pending' => __( '대기', 'acf-mail-smtp' ),
                                    );
                                    $email_status = $email['status'];
                                    $status_color = isset( $status_colors[ $email_status ] ) ? $status_colors[ $email_status ] : '#8c8f94';
                                    $status_label = isset( $status_labels[ $email_status ] ) ? $status_labels[ $email_status ] : ucfirst( $email_status );
                                    ?>
                                    <span style="display: inline-block; padding: 4px 10px; border-radius: 4px; background: <?php echo esc_attr( $status_color ); ?>; color: #fff; font-size: 12px; font-weight: 500;">
                                        <?php echo esc_html( $status_label ); ?>
                                    </span>
                                    <?php if ( ! empty( $email['error_message'] ) ) : ?>
                                        <br><small style="color: #d63638; font-size: 11px;" title="<?php echo esc_attr( $email['error_message'] ); ?>"><?php echo esc_html( wp_trim_words( $email['error_message'], 5 ) ); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size: 13px; color: #50575e;"><?php echo esc_html( $email['sent_at'] ? $email['sent_at'] : $email['created_at'] ); ?></td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <button type="button" class="button button-small view-email-btn" data-email-id="<?php echo esc_attr( $email['id'] ); ?>" title="<?php esc_attr_e( '보기', 'acf-mail-smtp' ); ?>">
                                            <span class="dashicons dashicons-visibility" style="font-size: 14px; width: 14px; height: 14px; line-height: 1.4;"></span>
                                        </button>
                                        <?php if ( $email['status'] === 'failed' ) : ?>
                                        <button type="button" class="button button-small resend-email-btn" data-email-id="<?php echo esc_attr( $email['id'] ); ?>" title="<?php esc_attr_e( '재발송', 'acf-mail-smtp' ); ?>">
                                            <span class="dashicons dashicons-controls-repeat" style="font-size: 14px; width: 14px; height: 14px; line-height: 1.4;"></span>
                                        </button>
                                        <?php endif; ?>
                                        <button type="button" class="button button-small delete-email-btn" data-email-id="<?php echo esc_attr( $email['id'] ); ?>" title="<?php esc_attr_e( '삭제', 'acf-mail-smtp' ); ?>">
                                            <span class="dashicons dashicons-trash" style="font-size: 14px; width: 14px; height: 14px; line-height: 1.4;"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px;"><?php esc_html_e( '이메일 로그가 없습니다.', 'acf-mail-smtp' ); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- [v2.2.0] 이메일 상세 모달 -->
<div id="email-detail-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 100000; align-items: center; justify-content: center;">
    <div style="background: #fff; border-radius: 12px; max-width: 700px; width: 90%; max-height: 80vh; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #ddd; background: linear-gradient(135deg, #2271b1 0%, #135e96 100%);">
            <h3 style="margin: 0; color: #fff; font-size: 18px;"><?php esc_html_e( '이메일 상세', 'acf-mail-smtp' ); ?></h3>
            <button type="button" id="close-modal-btn" style="background: none; border: none; color: #fff; font-size: 24px; cursor: pointer; padding: 0; line-height: 1;">&times;</button>
        </div>
        <div id="email-detail-content" style="padding: 24px; overflow-y: auto; max-height: calc(80vh - 140px);">
            <!-- 내용이 여기에 로드됩니다 -->
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 10px; padding: 16px 24px; border-top: 1px solid #ddd; background: #f6f7f7;">
            <button type="button" id="modal-resend-btn" class="button button-primary" style="display: none;">
                <span class="dashicons dashicons-controls-repeat" style="font-size: 16px; margin-right: 5px;"></span>
                <?php esc_html_e( '재발송', 'acf-mail-smtp' ); ?>
            </button>
            <button type="button" id="modal-close-btn" class="button"><?php esc_html_e( '닫기', 'acf-mail-smtp' ); ?></button>
        </div>
    </div>
</div>

<!-- [v2.2.0] 오래된 로그 삭제 모달 -->
<div id="delete-old-logs-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 100000; align-items: center; justify-content: center;">
    <div style="background: #fff; border-radius: 12px; max-width: 400px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
        <div style="padding: 20px 24px; border-bottom: 1px solid #ddd;">
            <h3 style="margin: 0; font-size: 18px;"><?php esc_html_e( '오래된 로그 삭제', 'acf-mail-smtp' ); ?></h3>
        </div>
        <div style="padding: 24px;">
            <p style="margin: 0 0 15px;"><?php esc_html_e( '며칠 이상 지난 로그를 삭제할까요?', 'acf-mail-smtp' ); ?></p>
            <select id="delete-days-select" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                <option value="7">7일 이상</option>
                <option value="14">14일 이상</option>
                <option value="30" selected>30일 이상</option>
                <option value="60">60일 이상</option>
                <option value="90">90일 이상</option>
            </select>
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 10px; padding: 16px 24px; border-top: 1px solid #ddd; background: #f6f7f7;">
            <button type="button" id="cancel-delete-old-btn" class="button"><?php esc_html_e( '취소', 'acf-mail-smtp' ); ?></button>
            <button type="button" id="confirm-delete-old-btn" class="button button-primary" style="background: #d63638; border-color: #d63638;">
                <?php esc_html_e( '삭제', 'acf-mail-smtp' ); ?>
            </button>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var ajaxUrl = '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>';
    var nonce = '<?php echo esc_js( wp_create_nonce( 'acf_mail_smtp_nonce' ) ); ?>';
    var currentEmailId = null;

    // [v2.2.0] 이메일 상세 보기
    $(document).on('click', '.view-email-btn', function(e) {
        e.preventDefault();
        var emailId = $(this).data('email-id');
        currentEmailId = emailId;

        $.post(ajaxUrl, {
            action: 'acf_mail_smtp_get_email',
            nonce: nonce,
            email_id: emailId
        }, function(response) {
            if (response.success) {
                var email = response.data.email;
                var statusColors = { sent: '#00a32a', failed: '#d63638', pending: '#2271b1' };
                var statusLabels = { sent: '<?php echo esc_js( __( '성공', 'acf-mail-smtp' ) ); ?>', failed: '<?php echo esc_js( __( '실패', 'acf-mail-smtp' ) ); ?>', pending: '<?php echo esc_js( __( '대기', 'acf-mail-smtp' ) ); ?>' };

                var html = '<div style="display: grid; gap: 16px;">';
                html += '<div style="display: grid; grid-template-columns: 100px 1fr; gap: 8px; align-items: start;">';
                html += '<strong style="color: #50575e;"><?php echo esc_js( __( '상태', 'acf-mail-smtp' ) ); ?>:</strong>';
                html += '<span style="display: inline-block; padding: 4px 10px; border-radius: 4px; background: ' + (statusColors[email.status] || '#8c8f94') + '; color: #fff; font-size: 12px; width: fit-content;">' + (statusLabels[email.status] || email.status) + '</span>';
                html += '</div>';

                html += '<div style="display: grid; grid-template-columns: 100px 1fr; gap: 8px;">';
                html += '<strong style="color: #50575e;"><?php echo esc_js( __( '받는 사람', 'acf-mail-smtp' ) ); ?>:</strong><span>' + escapeHtml(email.to_email) + '</span>';
                html += '</div>';

                html += '<div style="display: grid; grid-template-columns: 100px 1fr; gap: 8px;">';
                html += '<strong style="color: #50575e;"><?php echo esc_js( __( '보낸 사람', 'acf-mail-smtp' ) ); ?>:</strong><span>' + escapeHtml(email.from_email) + '</span>';
                html += '</div>';

                html += '<div style="display: grid; grid-template-columns: 100px 1fr; gap: 8px;">';
                html += '<strong style="color: #50575e;"><?php echo esc_js( __( '제목', 'acf-mail-smtp' ) ); ?>:</strong><span>' + escapeHtml(email.subject) + '</span>';
                html += '</div>';

                html += '<div style="display: grid; grid-template-columns: 100px 1fr; gap: 8px;">';
                html += '<strong style="color: #50575e;"><?php echo esc_js( __( '발송 시간', 'acf-mail-smtp' ) ); ?>:</strong><span>' + (email.sent_at || email.created_at) + '</span>';
                html += '</div>';

                if (email.error_message) {
                    html += '<div style="display: grid; grid-template-columns: 100px 1fr; gap: 8px;">';
                    html += '<strong style="color: #d63638;"><?php echo esc_js( __( '오류', 'acf-mail-smtp' ) ); ?>:</strong><span style="color: #d63638;">' + escapeHtml(email.error_message) + '</span>';
                    html += '</div>';
                }

                html += '<div style="border-top: 1px solid #ddd; padding-top: 16px; margin-top: 8px;">';
                html += '<strong style="color: #50575e; display: block; margin-bottom: 10px;"><?php echo esc_js( __( '내용', 'acf-mail-smtp' ) ); ?>:</strong>';
                html += '<div style="background: #f6f7f7; padding: 16px; border-radius: 8px; max-height: 300px; overflow-y: auto; border: 1px solid #ddd;">' + email.message + '</div>';
                html += '</div>';
                html += '</div>';

                $('#email-detail-content').html(html);

                if (email.status === 'failed') {
                    $('#modal-resend-btn').show();
                } else {
                    $('#modal-resend-btn').hide();
                }

                $('#email-detail-modal').css('display', 'flex');
            } else {
                alert(response.data.message || '<?php echo esc_js( __( '오류가 발생했습니다.', 'acf-mail-smtp' ) ); ?>');
            }
        });
    });

    // 모달 닫기
    $('#close-modal-btn, #modal-close-btn').on('click', function() {
        $('#email-detail-modal').hide();
        currentEmailId = null;
    });
    $('#email-detail-modal').on('click', function(e) {
        if (e.target === this) {
            $(this).hide();
            currentEmailId = null;
        }
    });

    // [v2.2.0] 이메일 재발송
    $(document).on('click', '.resend-email-btn, #modal-resend-btn', function(e) {
        e.preventDefault();
        var emailId = $(this).data('email-id') || currentEmailId;
        if (!emailId) return;

        if (!confirm('<?php echo esc_js( __( '이 이메일을 재발송하시겠습니까?', 'acf-mail-smtp' ) ); ?>')) return;

        var $btn = $(this);
        $btn.prop('disabled', true);

        $.post(ajaxUrl, {
            action: 'acf_mail_smtp_resend_email',
            nonce: nonce,
            email_id: emailId
        }, function(response) {
            $btn.prop('disabled', false);
            alert(response.data.message);
            if (response.success) {
                location.reload();
            }
        });
    });

    // [v2.2.0] 이메일 삭제
    $(document).on('click', '.delete-email-btn', function(e) {
        e.preventDefault();
        var emailId = $(this).data('email-id');

        if (!confirm('<?php echo esc_js( __( '이 로그를 삭제하시겠습니까?', 'acf-mail-smtp' ) ); ?>')) return;

        var $row = $(this).closest('tr');

        $.post(ajaxUrl, {
            action: 'acf_mail_smtp_delete_email',
            nonce: nonce,
            email_id: emailId
        }, function(response) {
            if (response.success) {
                $row.fadeOut(300, function() { $(this).remove(); });
            } else {
                alert(response.data.message || '<?php echo esc_js( __( '삭제에 실패했습니다.', 'acf-mail-smtp' ) ); ?>');
            }
        });
    });

    // [v2.2.0] CSV 내보내기
    $('#export-csv-btn').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).text('<?php echo esc_js( __( '내보내는 중...', 'acf-mail-smtp' ) ); ?>');

        $.post(ajaxUrl, {
            action: 'acf_mail_smtp_export_logs',
            nonce: nonce,
            status: '<?php echo esc_js( $status ); ?>'
        }, function(response) {
            $btn.prop('disabled', false).html('<span class="dashicons dashicons-download" style="font-size: 16px;"></span> <?php echo esc_js( __( 'CSV 내보내기', 'acf-mail-smtp' ) ); ?>');

            if (response.success) {
                var csv = response.data.csv;
                var csvContent = csv.map(function(row) {
                    return row.map(function(cell) {
                        return '"' + String(cell || '').replace(/"/g, '""') + '"';
                    }).join(',');
                }).join('\n');

                var blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' });
                var link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'email_logs_' + new Date().toISOString().slice(0,10) + '.csv';
                link.click();
            } else {
                alert(response.data.message || '<?php echo esc_js( __( '내보내기에 실패했습니다.', 'acf-mail-smtp' ) ); ?>');
            }
        });
    });

    // [v2.2.0] 오래된 로그 삭제 모달
    $('#delete-old-logs-btn').on('click', function() {
        $('#delete-old-logs-modal').css('display', 'flex');
    });
    $('#cancel-delete-old-btn').on('click', function() {
        $('#delete-old-logs-modal').hide();
    });
    $('#delete-old-logs-modal').on('click', function(e) {
        if (e.target === this) $(this).hide();
    });

    $('#confirm-delete-old-btn').on('click', function() {
        var days = $('#delete-days-select').val();
        var $btn = $(this);
        $btn.prop('disabled', true).text('<?php echo esc_js( __( '삭제 중...', 'acf-mail-smtp' ) ); ?>');

        $.post(ajaxUrl, {
            action: 'acf_mail_smtp_delete_old_logs',
            nonce: nonce,
            days: days
        }, function(response) {
            $btn.prop('disabled', false).text('<?php echo esc_js( __( '삭제', 'acf-mail-smtp' ) ); ?>');
            $('#delete-old-logs-modal').hide();
            alert(response.data.message);
            if (response.success && response.data.deleted > 0) {
                location.reload();
            }
        });
    });

    // 유틸리티
    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>

<style>
/* [v2.2.0] 로그 페이지 스타일 */
.acf-mail-smtp-logs-v25 .wp-list-table th,
.acf-mail-smtp-logs-v25 .wp-list-table td {
    vertical-align: middle;
}
.acf-mail-smtp-logs-v25 .button-small {
    padding: 0 8px;
    min-height: 28px;
    line-height: 28px;
}
.acf-mail-smtp-logs-v25 .button-small .dashicons {
    vertical-align: middle;
}
</style>
