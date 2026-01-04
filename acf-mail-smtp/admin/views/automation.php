<?php
/**
 * Automation View
 * 
 * @package ACF_Mail_SMTP
 * @version 1.0.0
 * @author 3J Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$plugin = ACF_Mail_SMTP::get_instance();
$automation_manager = $plugin->automation;
$forms_manager = $plugin->forms;

$forms = $forms_manager->get_forms();
$automations = $automation_manager->get_automations();
?>

<div class="wrap acf-mail-smtp-automation-v25">
    <h1 class="wp-bulk-seo-header-v25">
        <?php esc_html_e( '자동화 규칙', 'acf-mail-smtp' ); ?>
        <button type="button" class="wp-bulk-seo-btn-v25 wp-bulk-seo-btn-v25-primary" id="add-automation-btn" style="margin-left: 15px;">
            <?php esc_html_e( '+ 새 규칙 만들기', 'acf-mail-smtp' ); ?>
        </button>
    </h1>

    <div class="wp-bulk-seo-suggestions-grid-v25" style="margin-top: 30px;">
        <div class="wp-bulk-seo-suggestion-card-v25">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'ID', 'acf-mail-smtp' ); ?></th>
                        <th><?php esc_html_e( '이름', 'acf-mail-smtp' ); ?></th>
                        <th><?php esc_html_e( '폼', 'acf-mail-smtp' ); ?></th>
                        <th><?php esc_html_e( '트리거', 'acf-mail-smtp' ); ?></th>
                        <th><?php esc_html_e( '상태', 'acf-mail-smtp' ); ?></th>
                        <th><?php esc_html_e( '작업', 'acf-mail-smtp' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( ! empty( $automations ) ) : ?>
                        <?php foreach ( $automations as $automation ) : ?>
                            <?php $form = $forms_manager->get_form( $automation['form_id'] ); ?>
                            <tr>
                                <td><?php echo esc_html( $automation['id'] ); ?></td>
                                <td><strong><?php echo esc_html( $automation['name'] ); ?></strong></td>
                                <td><?php echo esc_html( $form ? $form['name'] : 'N/A' ); ?></td>
                                <td><?php echo esc_html( ucfirst( str_replace( '_', ' ', $automation['trigger_type'] ) ) ); ?></td>
                                <td>
                                    <span class="status-<?php echo esc_attr( $automation['status'] ); ?>">
                                        <?php echo esc_html( ucfirst( $automation['status'] ) ); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="#" class="edit-automation" data-automation-id="<?php echo esc_attr( $automation['id'] ); ?>">
                                        <?php esc_html_e( '편집', 'acf-mail-smtp' ); ?>
                                    </a>
                                    |
                                    <a href="#" class="delete-automation" data-automation-id="<?php echo esc_attr( $automation['id'] ); ?>">
                                        <?php esc_html_e( '삭제', 'acf-mail-smtp' ); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6"><?php esc_html_e( '자동화 규칙이 없습니다.', 'acf-mail-smtp' ); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Automation Modal (simplified, can be enhanced) -->
<div id="automation-modal" style="display: none;">
    <div class="wp-bulk-seo-suggestion-card-v25" style="max-width: 800px; margin: 50px auto; padding: 30px;">
        <h2 id="modal-title"><?php esc_html_e( '자동화 규칙 만들기', 'acf-mail-smtp' ); ?></h2>
        <form id="automation-form">
            <input type="hidden" id="automation_id" value="0" />
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="automation_name"><?php esc_html_e( '규칙 이름', 'acf-mail-smtp' ); ?></label>
                    </th>
                    <td>
                        <input type="text" id="automation_name" class="regular-text" required />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="automation_form_id"><?php esc_html_e( '폼', 'acf-mail-smtp' ); ?></label>
                    </th>
                    <td>
                        <select id="automation_form_id" required>
                            <option value=""><?php esc_html_e( '선택하세요', 'acf-mail-smtp' ); ?></option>
                            <?php foreach ( $forms as $form ) : ?>
                                <option value="<?php echo esc_attr( $form['id'] ); ?>">
                                    <?php echo esc_html( $form['name'] ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="automation_trigger"><?php esc_html_e( '트리거', 'acf-mail-smtp' ); ?></label>
                    </th>
                    <td>
                        <select id="automation_trigger">
                            <option value="form_submit"><?php esc_html_e( '폼 제출 시', 'acf-mail-smtp' ); ?></option>
                        </select>
                    </td>
                </tr>
            </table>

            <h3><?php esc_html_e( '액션', 'acf-mail-smtp' ); ?></h3>
            <div id="actions-list"></div>
            <button type="button" class="wp-bulk-seo-btn-v25 wp-bulk-seo-btn-v25-secondary" id="add-action-btn">
                <?php esc_html_e( '+ 액션 추가', 'acf-mail-smtp' ); ?>
            </button>

            <p class="submit">
                <button type="submit" class="wp-bulk-seo-btn-v25 wp-bulk-seo-btn-v25-primary">
                    <?php esc_html_e( '저장', 'acf-mail-smtp' ); ?>
                </button>
                <button type="button" class="wp-bulk-seo-btn-v25 wp-bulk-seo-btn-v25-secondary" id="close-modal-btn">
                    <?php esc_html_e( '취소', 'acf-mail-smtp' ); ?>
                </button>
            </p>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Add automation
    $('#add-automation-btn').on('click', function() {
        $('#automation-modal').show();
        $('#automation-form')[0].reset();
        $('#automation_id').val(0);
        $('#modal-title').text('<?php esc_html_e( '자동화 규칙 만들기', 'acf-mail-smtp' ); ?>');
    });

    // Close modal
    $('#close-modal-btn').on('click', function() {
        $('#automation-modal').hide();
    });

    // Add action
    $('#add-action-btn').on('click', function() {
        var actionType = prompt('액션 타입:\n1. send_email - 이메일 발송\n2. redirect - 리다이렉션\n3. webhook - 웹훅');
        
        if (actionType) {
            var actionHtml = '<div class="action-item" style="border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 8px;">';
            actionHtml += '<input type="hidden" class="action-type" value="' + actionType + '" />';
            actionHtml += '<strong>' + actionType + '</strong>';
            actionHtml += '<button type="button" class="button delete-action" style="float: right;">삭제</button>';
            actionHtml += '</div>';
            $('#actions-list').append(actionHtml);
        }
    });

    // Delete action
    $(document).on('click', '.delete-action', function() {
        $(this).closest('.action-item').remove();
    });

    // Save automation
    $('#automation-form').on('submit', function(e) {
        e.preventDefault();

        var automationData = {
            automation_id: $('#automation_id').val(),
            automation_data: {
                name: $('#automation_name').val(),
                form_id: $('#automation_form_id').val(),
                trigger_type: $('#automation_trigger').val(),
                actions: collectActions()
            }
        };

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'acf_mail_smtp_save_automation',
                nonce: acfMailSmtp.nonce,
                automation_id: automationData.automation_id,
                automation_data: automationData.automation_data
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message || acfMailSmtp.strings.error);
                }
            }
        });
    });

    // Delete automation
    $('.delete-automation').on('click', function(e) {
        e.preventDefault();
        if (!confirm(acfMailSmtp.strings.confirmDelete)) {
            return;
        }

        var automationId = $(this).data('automation-id');
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'acf_mail_smtp_delete_automation',
                nonce: acfMailSmtp.nonce,
                automation_id: automationId
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message || acfMailSmtp.strings.error);
                }
            }
        });
    });

    function collectActions() {
        var actions = [];
        $('#actions-list .action-item').each(function() {
            actions.push({
                type: $(this).find('.action-type').val()
            });
        });
        return actions;
    }
});
</script>

<style>
#automation-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 100000;
    overflow-y: auto;
}
</style>
