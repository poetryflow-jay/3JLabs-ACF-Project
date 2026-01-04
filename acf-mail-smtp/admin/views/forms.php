<?php
/**
 * Forms View
 * 
 * @package ACF_Mail_SMTP
 * @version 1.0.0
 * @author 3J Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$plugin = ACF_Mail_SMTP::get_instance();
$forms_manager = $plugin->forms;

$action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : 'list';
$form_id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;

if ( $action === 'edit' || $action === 'new' ) {
    // Form builder view
    $form = $form_id > 0 ? $forms_manager->get_form( $form_id ) : null;
    ?>
    <div class="wrap acf-mail-smtp-forms-v25">
        <h1 class="wp-bulk-seo-header-v25">
            <?php echo $form_id > 0 ? esc_html__( '폼 편집', 'acf-mail-smtp' ) : esc_html__( '새 폼 만들기', 'acf-mail-smtp' ); ?>
        </h1>

        <div class="wp-bulk-seo-suggestions-grid-v25" style="margin-top: 30px;">
            <div class="wp-bulk-seo-suggestion-card-v25">
                <form id="acf-mail-smtp-form-builder">
                    <input type="hidden" id="form_id" value="<?php echo esc_attr( $form_id ); ?>" />
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="form_name"><?php esc_html_e( '폼 이름', 'acf-mail-smtp' ); ?></label>
                            </th>
                            <td>
                                <input type="text" id="form_name" name="form_name" class="regular-text" 
                                       value="<?php echo $form ? esc_attr( $form['name'] ) : ''; ?>" required />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="form_title"><?php esc_html_e( '폼 제목', 'acf-mail-smtp' ); ?></label>
                            </th>
                            <td>
                                <input type="text" id="form_title" name="form_title" class="regular-text" 
                                       value="<?php echo $form ? esc_attr( $form['title'] ) : ''; ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="form_description"><?php esc_html_e( '설명', 'acf-mail-smtp' ); ?></label>
                            </th>
                            <td>
                                <textarea id="form_description" name="form_description" class="large-text" rows="3"><?php echo $form ? esc_textarea( $form['description'] ) : ''; ?></textarea>
                            </td>
                        </tr>
                    </table>

                    <h2><?php esc_html_e( '폼 필드', 'acf-mail-smtp' ); ?></h2>
                    <div id="form-fields-builder">
                        <div id="fields-list"></div>
                        <button type="button" class="wp-bulk-seo-btn-v25 wp-bulk-seo-btn-v25-secondary" id="add-field-btn">
                            <?php esc_html_e( '+ 필드 추가', 'acf-mail-smtp' ); ?>
                        </button>
                    </div>

                    <h2><?php esc_html_e( '설정', 'acf-mail-smtp' ); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="success_message"><?php esc_html_e( '성공 메시지', 'acf-mail-smtp' ); ?></label>
                            </th>
                            <td>
                                <textarea id="success_message" name="success_message" class="large-text" rows="2"><?php echo $form && isset( $form['settings']['success_message'] ) ? esc_textarea( $form['settings']['success_message'] ) : __( '폼이 성공적으로 제출되었습니다.', 'acf-mail-smtp' ); ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="send_email"><?php esc_html_e( '이메일 발송', 'acf-mail-smtp' ); ?></label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" id="send_email" name="send_email" value="1" 
                                           <?php checked( $form && isset( $form['settings']['send_email'] ) ? $form['settings']['send_email'] : true ); ?> />
                                    <?php esc_html_e( '폼 제출 시 이메일 발송', 'acf-mail-smtp' ); ?>
                                </label>
                            </td>
                        </tr>
                        <tr id="email-settings-row" style="<?php echo ( $form && isset( $form['settings']['send_email'] ) && ! $form['settings']['send_email'] ) ? 'display: none;' : ''; ?>">
                            <th scope="row">
                                <label for="email_to"><?php esc_html_e( '받는 사람', 'acf-mail-smtp' ); ?></label>
                            </th>
                            <td>
                                <input type="email" id="email_to" name="email_to" class="regular-text" 
                                       value="<?php echo $form && isset( $form['settings']['email_to'] ) ? esc_attr( $form['settings']['email_to'] ) : get_option( 'admin_email' ); ?>" />
                            </td>
                        </tr>
                        <tr id="email-subject-row" style="<?php echo ( $form && isset( $form['settings']['send_email'] ) && ! $form['settings']['send_email'] ) ? 'display: none;' : ''; ?>">
                            <th scope="row">
                                <label for="email_subject"><?php esc_html_e( '이메일 제목', 'acf-mail-smtp' ); ?></label>
                            </th>
                            <td>
                                <input type="text" id="email_subject" name="email_subject" class="regular-text" 
                                       value="<?php echo $form && isset( $form['settings']['email_subject'] ) ? esc_attr( $form['settings']['email_subject'] ) : __( '새로운 폼 제출', 'acf-mail-smtp' ); ?>" />
                            </td>
                        </tr>
                    </table>

                    <p class="submit">
                        <button type="submit" class="wp-bulk-seo-btn-v25 wp-bulk-seo-btn-v25-primary">
                            <?php esc_html_e( '저장', 'acf-mail-smtp' ); ?>
                        </button>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-mail-smtp-forms' ) ); ?>" class="wp-bulk-seo-btn-v25 wp-bulk-seo-btn-v25-secondary" style="margin-left: 10px;">
                            <?php esc_html_e( '취소', 'acf-mail-smtp' ); ?>
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Field types
        var fieldTypes = {
            'text': { label: '텍스트', icon: '📝' },
            'email': { label: '이메일', icon: '📧' },
            'textarea': { label: '텍스트 영역', icon: '📄' },
            'select': { label: '선택', icon: '📋' },
            'checkbox': { label: '체크박스', icon: '☑️' },
            'radio': { label: '라디오', icon: '🔘' },
            'number': { label: '숫자', icon: '🔢' },
            'date': { label: '날짜', icon: '📅' },
            'file': { label: '파일', icon: '📎' },
        };

        // Load existing fields
        <?php if ( $form && isset( $form['fields'] ) && is_array( $form['fields'] ) ) : ?>
            var existingFields = <?php echo wp_json_encode( $form['fields'] ); ?>;
            existingFields.forEach(function(field) {
                addField(field);
            });
        <?php endif; ?>

        // Add field button
        $('#add-field-btn').on('click', function() {
            showFieldTypeModal();
        });

        // Send email checkbox
        $('#send_email').on('change', function() {
            if ($(this).is(':checked')) {
                $('#email-settings-row, #email-subject-row').show();
            } else {
                $('#email-settings-row, #email-subject-row').hide();
            }
        });

        // Form submit
        $('#acf-mail-smtp-form-builder').on('submit', function(e) {
            e.preventDefault();
            
            var formData = {
                form_id: $('#form_id').val(),
                form_data: {
                    name: $('#form_name').val(),
                    title: $('#form_title').val(),
                    description: $('#form_description').val(),
                    fields: collectFields(),
                    settings: {
                        success_message: $('#success_message').val(),
                        send_email: $('#send_email').is(':checked'),
                        email_to: $('#email_to').val(),
                        email_subject: $('#email_subject').val(),
                    }
                }
            };

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'acf_mail_smtp_save_form',
                    nonce: acfMailSmtp.nonce,
                    form_id: formData.form_id,
                    form_data: formData.form_data
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        window.location.href = '<?php echo esc_url( admin_url( 'admin.php?page=acf-mail-smtp-forms' ) ); ?>';
                    } else {
                        alert(response.data.message || acfMailSmtp.strings.error);
                    }
                }
            });
        });

        function showFieldTypeModal() {
            // Simple field type selection (can be enhanced with modal)
            var fieldType = prompt('필드 타입을 선택하세요:\n' + Object.keys(fieldTypes).map(function(key) {
                return key + ' - ' + fieldTypes[key].label;
            }).join('\n'));

            if (fieldType && fieldTypes[fieldType]) {
                addField({ type: fieldType });
            }
        }

        function addField(fieldData) {
            fieldData = fieldData || { type: 'text' };
            var fieldId = fieldData.id || 'field_' + Date.now();
            var fieldType = fieldData.type || 'text';
            var fieldLabel = fieldData.label || '';
            var fieldRequired = fieldData.required || false;

            var fieldHtml = '<div class="field-item" data-field-id="' + fieldId + '" style="border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 8px;">';
            fieldHtml += '<div style="display: flex; justify-content: space-between; align-items: center;">';
            fieldHtml += '<div>';
            fieldHtml += '<strong>' + (fieldTypes[fieldType] ? fieldTypes[fieldType].icon + ' ' + fieldTypes[fieldType].label : fieldType) + '</strong>';
            fieldHtml += '<input type="text" class="field-label" placeholder="필드 레이블" value="' + fieldLabel + '" style="margin-left: 10px; width: 200px;" />';
            fieldHtml += '<label style="margin-left: 10px;"><input type="checkbox" class="field-required" ' + (fieldRequired ? 'checked' : '') + ' /> 필수</label>';
            fieldHtml += '</div>';
            fieldHtml += '<button type="button" class="button delete-field">삭제</button>';
            fieldHtml += '</div>';
            fieldHtml += '</div>';

            $('#fields-list').append(fieldHtml);

            // Delete field
            $('#fields-list').on('click', '.delete-field', function() {
                $(this).closest('.field-item').remove();
            });
        }

        function collectFields() {
            var fields = [];
            $('#fields-list .field-item').each(function() {
                var $item = $(this);
                fields.push({
                    id: $item.data('field-id'),
                    type: $item.find('strong').text().split(' ')[1] || 'text',
                    label: $item.find('.field-label').val(),
                    required: $item.find('.field-required').is(':checked')
                });
            });
            return fields;
        }
    });
    </script>
    <?php
} else {
    // Forms list view
    $forms = $forms_manager->get_forms();
    ?>
    <div class="wrap acf-mail-smtp-forms-v25">
        <h1 class="wp-bulk-seo-header-v25">
            <?php esc_html_e( '폼 관리', 'acf-mail-smtp' ); ?>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-mail-smtp-forms&action=new' ) ); ?>" class="wp-bulk-seo-btn-v25 wp-bulk-seo-btn-v25-primary" style="margin-left: 15px;">
                <?php esc_html_e( '+ 새 폼 만들기', 'acf-mail-smtp' ); ?>
            </a>
        </h1>

        <div class="wp-bulk-seo-suggestions-grid-v25" style="margin-top: 30px;">
            <div class="wp-bulk-seo-suggestion-card-v25">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'ID', 'acf-mail-smtp' ); ?></th>
                            <th><?php esc_html_e( '이름', 'acf-mail-smtp' ); ?></th>
                            <th><?php esc_html_e( '제목', 'acf-mail-smtp' ); ?></th>
                            <th><?php esc_html_e( '상태', 'acf-mail-smtp' ); ?></th>
                            <th><?php esc_html_e( '생성일', 'acf-mail-smtp' ); ?></th>
                            <th><?php esc_html_e( '작업', 'acf-mail-smtp' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( ! empty( $forms ) ) : ?>
                            <?php foreach ( $forms as $form ) : ?>
                                <tr>
                                    <td><?php echo esc_html( $form['id'] ); ?></td>
                                    <td><strong><?php echo esc_html( $form['name'] ); ?></strong></td>
                                    <td><?php echo esc_html( $form['title'] ); ?></td>
                                    <td>
                                        <span class="status-<?php echo esc_attr( $form['status'] ); ?>">
                                            <?php echo esc_html( ucfirst( $form['status'] ) ); ?>
                                        </span>
                                    </td>
                                    <td><?php echo esc_html( $form['created_at'] ); ?></td>
                                    <td>
                                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-mail-smtp-forms&action=edit&id=' . $form['id'] ) ); ?>">
                                            <?php esc_html_e( '편집', 'acf-mail-smtp' ); ?>
                                        </a>
                                        |
                                        <a href="#" class="delete-form" data-form-id="<?php echo esc_attr( $form['id'] ); ?>">
                                            <?php esc_html_e( '삭제', 'acf-mail-smtp' ); ?>
                                        </a>
                                        |
                                        <code>[acf_form id="<?php echo esc_attr( $form['id'] ); ?>"]</code>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="6"><?php esc_html_e( '폼이 없습니다.', 'acf-mail-smtp' ); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        $('.delete-form').on('click', function(e) {
            e.preventDefault();
            if (!confirm(acfMailSmtp.strings.confirmDelete)) {
                return;
            }

            var formId = $(this).data('form-id');
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'acf_mail_smtp_delete_form',
                    nonce: acfMailSmtp.nonce,
                    form_id: formId
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
    });
    </script>
    <?php
}
?>
