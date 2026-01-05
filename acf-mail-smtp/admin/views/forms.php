<?php
/**
 * Forms View - Drag & Drop Form Builder
 *
 * @package ACF_Mail_SMTP
 * @version 2.0.0
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
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-mail-smtp-forms' ) ); ?>" class="acf-btn acf-btn-sm" style="margin-left: 15px; background: #6b7280; color: white;">
                ← <?php esc_html_e( '목록으로', 'acf-mail-smtp' ); ?>
            </a>
        </h1>

        <form id="acf-mail-smtp-form-builder-form">
            <input type="hidden" id="form_id" value="<?php echo esc_attr( $form_id ); ?>" />

            <!-- Form Basic Info -->
            <div class="acf-form-info-bar" style="display: flex; gap: 20px; margin: 20px 0; padding: 20px; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div style="flex: 1;">
                    <label for="form_name" style="display: block; font-weight: 600; margin-bottom: 6px; color: #374151;">
                        <?php esc_html_e( '폼 이름', 'acf-mail-smtp' ); ?> <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" id="form_name" name="form_name"
                           style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;"
                           value="<?php echo $form ? esc_attr( $form['name'] ) : ''; ?>"
                           placeholder="예: 문의하기 폼" required />
                </div>
                <div style="flex: 1;">
                    <label for="form_title" style="display: block; font-weight: 600; margin-bottom: 6px; color: #374151;">
                        <?php esc_html_e( '폼 제목 (프론트엔드 표시)', 'acf-mail-smtp' ); ?>
                    </label>
                    <input type="text" id="form_title" name="form_title"
                           style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;"
                           value="<?php echo $form ? esc_attr( $form['title'] ) : ''; ?>"
                           placeholder="예: 문의하기" />
                </div>
                <div style="flex: 1.5;">
                    <label for="form_description" style="display: block; font-weight: 600; margin-bottom: 6px; color: #374151;">
                        <?php esc_html_e( '설명', 'acf-mail-smtp' ); ?>
                    </label>
                    <input type="text" id="form_description" name="form_description"
                           style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;"
                           value="<?php echo $form ? esc_attr( $form['description'] ) : ''; ?>"
                           placeholder="폼에 대한 간단한 설명" />
                </div>
            </div>

            <!-- Drag & Drop Form Builder -->
            <div id="form-builder-container"></div>

            <!-- Form Settings -->
            <div class="acf-form-settings" style="margin-top: 20px; padding: 24px; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <h3 style="margin: 0 0 20px 0; font-size: 18px; color: #374151;">⚙️ <?php esc_html_e( '폼 설정', 'acf-mail-smtp' ); ?></h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                    <!-- Success Message -->
                    <div>
                        <label for="success_message" style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">
                            <?php esc_html_e( '성공 메시지', 'acf-mail-smtp' ); ?>
                        </label>
                        <textarea id="success_message" name="success_message" rows="2"
                                  style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; resize: vertical;"
                                  placeholder="<?php esc_attr_e( '폼이 성공적으로 제출되었습니다.', 'acf-mail-smtp' ); ?>"><?php echo $form && isset( $form['settings']['success_message'] ) ? esc_textarea( $form['settings']['success_message'] ) : esc_textarea( __( '폼이 성공적으로 제출되었습니다.', 'acf-mail-smtp' ) ); ?></textarea>
                    </div>

                    <!-- Submit Button Text -->
                    <div>
                        <label for="submit_text" style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">
                            <?php esc_html_e( '제출 버튼 텍스트', 'acf-mail-smtp' ); ?>
                        </label>
                        <input type="text" id="submit_text" name="submit_text"
                               style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;"
                               value="<?php echo $form && isset( $form['settings']['submit_text'] ) ? esc_attr( $form['settings']['submit_text'] ) : esc_attr( __( '제출', 'acf-mail-smtp' ) ); ?>" />
                    </div>
                </div>

                <!-- Email Settings -->
                <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
                    <h4 style="margin: 0 0 16px 0; font-size: 16px; color: #374151;">📧 <?php esc_html_e( '이메일 설정', 'acf-mail-smtp' ); ?></h4>

                    <div style="margin-bottom: 16px;">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <input type="checkbox" id="send_email" name="send_email" value="1"
                                   style="width: 18px; height: 18px;"
                                   <?php checked( ! $form || ! isset( $form['settings']['send_email'] ) || $form['settings']['send_email'] ); ?> />
                            <span style="font-weight: 500; color: #374151;"><?php esc_html_e( '폼 제출 시 이메일 발송', 'acf-mail-smtp' ); ?></span>
                        </label>
                    </div>

                    <div id="email-settings" style="<?php echo ( $form && isset( $form['settings']['send_email'] ) && ! $form['settings']['send_email'] ) ? 'display: none;' : ''; ?>">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 20px;">
                            <div>
                                <label for="email_to" style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">
                                    <?php esc_html_e( '받는 사람', 'acf-mail-smtp' ); ?>
                                </label>
                                <input type="email" id="email_to" name="email_to"
                                       style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;"
                                       value="<?php echo $form && isset( $form['settings']['email_to'] ) ? esc_attr( $form['settings']['email_to'] ) : esc_attr( get_option( 'admin_email' ) ); ?>" />
                            </div>
                            <div>
                                <label for="email_subject" style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">
                                    <?php esc_html_e( '이메일 제목', 'acf-mail-smtp' ); ?>
                                </label>
                                <input type="text" id="email_subject" name="email_subject"
                                       style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;"
                                       value="<?php echo $form && isset( $form['settings']['email_subject'] ) ? esc_attr( $form['settings']['email_subject'] ) : esc_attr( __( '새로운 폼 제출', 'acf-mail-smtp' ) ); ?>"
                                       placeholder="<?php esc_attr_e( '{form_title}에서 새로운 제출', 'acf-mail-smtp' ); ?>" />
                                <small style="display: block; margin-top: 4px; color: #9ca3af; font-size: 12px;">
                                    <?php esc_html_e( '변수 사용 가능: {form_title}, {site_name}, {field_name}', 'acf-mail-smtp' ); ?>
                                </small>
                            </div>
                            <div>
                                <label for="reply_to" style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">
                                    <?php esc_html_e( '회신 주소 필드', 'acf-mail-smtp' ); ?>
                                </label>
                                <select id="reply_to" name="reply_to"
                                        style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                                    <option value=""><?php esc_html_e( '없음', 'acf-mail-smtp' ); ?></option>
                                </select>
                            </div>
                        </div>

                        <!-- Email Template Editor -->
                        <div class="acf-email-template-editor" style="margin-top: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <label style="font-weight: 600; color: #374151; font-size: 15px;">
                                    ✉️ <?php esc_html_e( '이메일 템플릿', 'acf-mail-smtp' ); ?>
                                </label>
                                <div class="acf-template-actions" style="display: flex; gap: 8px;">
                                    <button type="button" id="insert-field-var" class="acf-btn acf-btn-sm" style="background: #667eea; color: white; padding: 6px 12px; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;">
                                        📎 <?php esc_html_e( '필드 변수 삽입', 'acf-mail-smtp' ); ?>
                                    </button>
                                    <button type="button" id="preview-email-template" class="acf-btn acf-btn-sm" style="background: #10b981; color: white; padding: 6px 12px; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;">
                                        👁️ <?php esc_html_e( '미리보기', 'acf-mail-smtp' ); ?>
                                    </button>
                                    <button type="button" id="toggle-html-mode" class="acf-btn acf-btn-sm" style="background: #6b7280; color: white; padding: 6px 12px; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;">
                                        &lt;/&gt; <?php esc_html_e( 'HTML', 'acf-mail-smtp' ); ?>
                                    </button>
                                </div>
                            </div>

                            <!-- Field Variables Dropdown -->
                            <div id="field-variables-dropdown" style="display: none; position: absolute; background: white; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); padding: 8px 0; z-index: 1000; max-height: 300px; overflow-y: auto; min-width: 250px;">
                                <div style="padding: 8px 16px; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb; margin-bottom: 4px;">
                                    <?php esc_html_e( '시스템 변수', 'acf-mail-smtp' ); ?>
                                </div>
                                <div class="field-var-item" data-var="{form_title}" style="padding: 8px 16px; cursor: pointer; transition: background 0.2s;">
                                    <code style="background: #f3f4f6; padding: 2px 6px; border-radius: 4px; font-size: 12px;">{form_title}</code>
                                    <span style="margin-left: 8px; color: #6b7280; font-size: 13px;"><?php esc_html_e( '폼 제목', 'acf-mail-smtp' ); ?></span>
                                </div>
                                <div class="field-var-item" data-var="{site_name}" style="padding: 8px 16px; cursor: pointer; transition: background 0.2s;">
                                    <code style="background: #f3f4f6; padding: 2px 6px; border-radius: 4px; font-size: 12px;">{site_name}</code>
                                    <span style="margin-left: 8px; color: #6b7280; font-size: 13px;"><?php esc_html_e( '사이트 이름', 'acf-mail-smtp' ); ?></span>
                                </div>
                                <div class="field-var-item" data-var="{submission_date}" style="padding: 8px 16px; cursor: pointer; transition: background 0.2s;">
                                    <code style="background: #f3f4f6; padding: 2px 6px; border-radius: 4px; font-size: 12px;">{submission_date}</code>
                                    <span style="margin-left: 8px; color: #6b7280; font-size: 13px;"><?php esc_html_e( '제출 날짜', 'acf-mail-smtp' ); ?></span>
                                </div>
                                <div class="field-var-item" data-var="{all_fields}" style="padding: 8px 16px; cursor: pointer; transition: background 0.2s;">
                                    <code style="background: #f3f4f6; padding: 2px 6px; border-radius: 4px; font-size: 12px;">{all_fields}</code>
                                    <span style="margin-left: 8px; color: #6b7280; font-size: 13px;"><?php esc_html_e( '모든 필드 (테이블)', 'acf-mail-smtp' ); ?></span>
                                </div>
                                <div id="form-field-vars" style="border-top: 1px solid #e5e7eb; margin-top: 4px; padding-top: 4px;">
                                    <div style="padding: 8px 16px; font-weight: 600; color: #374151;">
                                        <?php esc_html_e( '폼 필드', 'acf-mail-smtp' ); ?>
                                    </div>
                                    <!-- Dynamic field variables will be inserted here -->
                                </div>
                            </div>

                            <!-- WYSIWYG Editor -->
                            <div id="email-editor-container" style="border: 1px solid #d1d5db; border-radius: 8px; overflow: hidden;">
                                <!-- Toolbar -->
                                <div id="email-editor-toolbar" style="display: flex; flex-wrap: wrap; gap: 4px; padding: 8px 12px; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                    <button type="button" class="editor-btn" data-command="bold" title="Bold" style="padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; background: white; cursor: pointer; font-weight: bold;">B</button>
                                    <button type="button" class="editor-btn" data-command="italic" title="Italic" style="padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; background: white; cursor: pointer; font-style: italic;">I</button>
                                    <button type="button" class="editor-btn" data-command="underline" title="Underline" style="padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; background: white; cursor: pointer; text-decoration: underline;">U</button>
                                    <span style="width: 1px; background: #d1d5db; margin: 0 4px;"></span>
                                    <button type="button" class="editor-btn" data-command="justifyLeft" title="Align Left" style="padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; background: white; cursor: pointer;">⬅</button>
                                    <button type="button" class="editor-btn" data-command="justifyCenter" title="Align Center" style="padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; background: white; cursor: pointer;">⬛</button>
                                    <button type="button" class="editor-btn" data-command="justifyRight" title="Align Right" style="padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; background: white; cursor: pointer;">➡</button>
                                    <span style="width: 1px; background: #d1d5db; margin: 0 4px;"></span>
                                    <button type="button" class="editor-btn" data-command="insertUnorderedList" title="Bullet List" style="padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; background: white; cursor: pointer;">•</button>
                                    <button type="button" class="editor-btn" data-command="insertOrderedList" title="Numbered List" style="padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; background: white; cursor: pointer;">1.</button>
                                    <span style="width: 1px; background: #d1d5db; margin: 0 4px;"></span>
                                    <select class="editor-select" data-command="formatBlock" style="padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; background: white; cursor: pointer;">
                                        <option value="p"><?php esc_html_e( '본문', 'acf-mail-smtp' ); ?></option>
                                        <option value="h1">H1</option>
                                        <option value="h2">H2</option>
                                        <option value="h3">H3</option>
                                        <option value="h4">H4</option>
                                    </select>
                                    <input type="color" class="editor-color" data-command="foreColor" value="#000000" title="Text Color" style="width: 36px; height: 32px; padding: 2px; border: 1px solid #d1d5db; border-radius: 4px; cursor: pointer;">
                                    <button type="button" class="editor-btn" data-command="createLink" title="Insert Link" style="padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; background: white; cursor: pointer;">🔗</button>
                                </div>

                                <!-- Content Editable Area -->
                                <div id="email-editor-content" contenteditable="true" style="min-height: 250px; padding: 20px; font-size: 14px; line-height: 1.6; outline: none;"><?php
                                    $default_template = '<h2 style="color: #374151; margin-bottom: 20px;">{form_title}</h2>
<p style="color: #6b7280; margin-bottom: 20px;">' . esc_html__( '새로운 폼 제출이 있습니다.', 'acf-mail-smtp' ) . '</p>
{all_fields}
<p style="color: #9ca3af; font-size: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">' . esc_html__( '이 이메일은', 'acf-mail-smtp' ) . ' {site_name}' . esc_html__( '에서 자동으로 발송되었습니다.', 'acf-mail-smtp' ) . '</p>';
                                    echo $form && isset( $form['settings']['email_template'] ) ? wp_kses_post( $form['settings']['email_template'] ) : $default_template;
                                ?></div>

                                <!-- HTML Source Editor (hidden by default) -->
                                <textarea id="email-html-source" style="display: none; width: 100%; min-height: 250px; padding: 20px; font-family: monospace; font-size: 13px; border: none; resize: vertical;"></textarea>
                            </div>

                            <input type="hidden" id="email_template" name="email_template" value="" />
                        </div>
                    </div>
                </div>

                <!-- Additional Settings -->
                <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
                    <h4 style="margin: 0 0 16px 0; font-size: 16px; color: #374151;">🔒 <?php esc_html_e( '추가 설정', 'acf-mail-smtp' ); ?></h4>

                    <div style="display: flex; flex-wrap: wrap; gap: 20px;">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <input type="checkbox" id="enable_honeypot" name="enable_honeypot" value="1"
                                   style="width: 18px; height: 18px;"
                                   <?php checked( ! $form || ! isset( $form['settings']['enable_honeypot'] ) || $form['settings']['enable_honeypot'] ); ?> />
                            <span style="font-weight: 500; color: #374151;"><?php esc_html_e( '스팸 방지 (Honeypot)', 'acf-mail-smtp' ); ?></span>
                        </label>

                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <input type="checkbox" id="store_submissions" name="store_submissions" value="1"
                                   style="width: 18px; height: 18px;"
                                   <?php checked( ! $form || ! isset( $form['settings']['store_submissions'] ) || $form['settings']['store_submissions'] ); ?> />
                            <span style="font-weight: 500; color: #374151;"><?php esc_html_e( '제출 데이터 저장', 'acf-mail-smtp' ); ?></span>
                        </label>

                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <input type="checkbox" id="ajax_submit" name="ajax_submit" value="1"
                                   style="width: 18px; height: 18px;"
                                   <?php checked( ! $form || ! isset( $form['settings']['ajax_submit'] ) || $form['settings']['ajax_submit'] ); ?> />
                            <span style="font-weight: 500; color: #374151;"><?php esc_html_e( 'AJAX 제출 (페이지 새로고침 없음)', 'acf-mail-smtp' ); ?></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div style="margin-top: 24px; display: flex; gap: 12px; justify-content: flex-end;">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-mail-smtp-forms' ) ); ?>"
                   class="acf-btn" style="padding: 12px 24px; background: #6b7280; color: white; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    <?php esc_html_e( '취소', 'acf-mail-smtp' ); ?>
                </a>
                <button type="submit" class="acf-btn"
                        style="padding: 12px 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer;">
                    <?php esc_html_e( '💾 저장', 'acf-mail-smtp' ); ?>
                </button>
            </div>
        </form>
    </div>

    <script>
    // Pass existing form fields to JavaScript
    <?php if ( $form && isset( $form['fields'] ) && is_array( $form['fields'] ) ) : ?>
    var existingFormFields = <?php echo wp_json_encode( $form['fields'] ); ?>;
    <?php else : ?>
    var existingFormFields = [];
    <?php endif; ?>

    jQuery(document).ready(function($) {
        // Email checkbox toggle
        $('#send_email').on('change', function() {
            if ($(this).is(':checked')) {
                $('#email-settings').slideDown(200);
            } else {
                $('#email-settings').slideUp(200);
            }
        });

        // Form submit
        $('#acf-mail-smtp-form-builder-form').on('submit', function(e) {
            e.preventDefault();

            var $btn = $(this).find('button[type="submit"]');
            var originalText = $btn.html();
            $btn.html('<?php echo esc_js( __( '저장 중...', 'acf-mail-smtp' ) ); ?>').prop('disabled', true);

            // Get fields from form builder
            var fields = window.formBuilder ? window.formBuilder.getFields() : [];

            var formData = {
                form_id: $('#form_id').val(),
                form_data: {
                    name: $('#form_name').val(),
                    title: $('#form_title').val(),
                    description: $('#form_description').val(),
                    fields: fields,
                    settings: {
                        success_message: $('#success_message').val(),
                        submit_text: $('#submit_text').val(),
                        send_email: $('#send_email').is(':checked'),
                        email_to: $('#email_to').val(),
                        email_subject: $('#email_subject').val(),
                        email_template: $('#email_template').val(),
                        reply_to: $('#reply_to').val(),
                        enable_honeypot: $('#enable_honeypot').is(':checked'),
                        store_submissions: $('#store_submissions').is(':checked'),
                        ajax_submit: $('#ajax_submit').is(':checked')
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
                    form_data: JSON.stringify(formData.form_data)
                },
                success: function(response) {
                    $btn.html(originalText).prop('disabled', false);

                    if (response.success) {
                        window.acfMailSmtpToast(response.data.message, 'success');
                        // Redirect to list after short delay
                        setTimeout(function() {
                            window.location.href = '<?php echo esc_url( admin_url( 'admin.php?page=acf-mail-smtp-forms' ) ); ?>';
                        }, 1000);
                    } else {
                        window.acfMailSmtpToast(response.data.message || acfMailSmtp.strings.error, 'error');
                    }
                },
                error: function() {
                    $btn.html(originalText).prop('disabled', false);
                    window.acfMailSmtpToast(acfMailSmtp.strings.error, 'error');
                }
            });
        });

        // Update reply_to dropdown when fields change
        function updateReplyToOptions() {
            var $select = $('#reply_to');
            var currentValue = $select.val();
            var fields = window.formBuilder ? window.formBuilder.getFields() : [];

            $select.html('<option value=""><?php echo esc_js( __( '없음', 'acf-mail-smtp' ) ); ?></option>');

            fields.forEach(function(field) {
                if (field.type === 'email') {
                    var selected = currentValue === field.name ? 'selected' : '';
                    $select.append('<option value="' + field.name + '" ' + selected + '>' + (field.label || field.name) + '</option>');
                }
            });
        }

        // Update on builder changes
        setInterval(updateReplyToOptions, 2000);

        // ========================================
        // Email Template WYSIWYG Editor
        // ========================================
        var emailEditor = {
            isHtmlMode: false,
            $content: $('#email-editor-content'),
            $source: $('#email-html-source'),
            $toolbar: $('#email-editor-toolbar'),
            $varDropdown: $('#field-variables-dropdown'),

            init: function() {
                this.bindToolbarEvents();
                this.bindVariableEvents();
                this.bindModeToggle();
                this.bindPreview();
                this.syncToHidden();
                this.updateFieldVariables();

                // Focus tracking for execCommand
                this.$content.on('focus', function() {
                    emailEditor.lastSelection = window.getSelection().getRangeAt(0);
                });

                // Update hidden input on content change
                this.$content.on('input blur', function() {
                    emailEditor.syncToHidden();
                });
            },

            bindToolbarEvents: function() {
                var self = this;

                // Button commands (bold, italic, etc.)
                this.$toolbar.find('.editor-btn').on('click', function(e) {
                    e.preventDefault();
                    var command = $(this).data('command');

                    if (command === 'createLink') {
                        var url = prompt('<?php echo esc_js( __( '링크 URL을 입력하세요:', 'acf-mail-smtp' ) ); ?>', 'https://');
                        if (url) {
                            self.$content.focus();
                            document.execCommand('createLink', false, url);
                        }
                    } else {
                        self.$content.focus();
                        document.execCommand(command, false, null);
                    }

                    self.syncToHidden();
                });

                // Select commands (formatBlock)
                this.$toolbar.find('.editor-select').on('change', function(e) {
                    var command = $(this).data('command');
                    var value = $(this).val();

                    self.$content.focus();
                    document.execCommand(command, false, '<' + value + '>');
                    self.syncToHidden();
                });

                // Color picker
                this.$toolbar.find('.editor-color').on('input', function(e) {
                    var command = $(this).data('command');
                    var value = $(this).val();

                    self.$content.focus();
                    document.execCommand(command, false, value);
                    self.syncToHidden();
                });
            },

            bindVariableEvents: function() {
                var self = this;

                // Toggle dropdown
                $('#insert-field-var').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    var $btn = $(this);
                    var $dropdown = self.$varDropdown;

                    if ($dropdown.is(':visible')) {
                        $dropdown.hide();
                    } else {
                        self.updateFieldVariables();
                        var offset = $btn.offset();
                        $dropdown.css({
                            top: offset.top + $btn.outerHeight() + 5,
                            left: offset.left
                        }).show();
                    }
                });

                // Close dropdown on outside click
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('#insert-field-var, #field-variables-dropdown').length) {
                        self.$varDropdown.hide();
                    }
                });

                // Insert variable
                $(document).on('click', '.field-var-item', function(e) {
                    e.preventDefault();
                    var varText = $(this).data('var');

                    if (self.isHtmlMode) {
                        // Insert at cursor in textarea
                        var $textarea = self.$source;
                        var start = $textarea[0].selectionStart;
                        var end = $textarea[0].selectionEnd;
                        var text = $textarea.val();
                        $textarea.val(text.substring(0, start) + varText + text.substring(end));
                        $textarea[0].selectionStart = $textarea[0].selectionEnd = start + varText.length;
                        $textarea.focus();
                    } else {
                        // Insert at cursor in contenteditable
                        self.$content.focus();
                        document.execCommand('insertText', false, varText);
                    }

                    self.$varDropdown.hide();
                    self.syncToHidden();
                });

                // Hover effect
                $(document).on('mouseenter', '.field-var-item', function() {
                    $(this).css('background', '#f3f4f6');
                }).on('mouseleave', '.field-var-item', function() {
                    $(this).css('background', 'transparent');
                });
            },

            bindModeToggle: function() {
                var self = this;

                $('#toggle-html-mode').on('click', function(e) {
                    e.preventDefault();

                    if (self.isHtmlMode) {
                        // Switch to Visual mode
                        self.$content.html(self.$source.val());
                        self.$source.hide();
                        self.$content.show();
                        self.$toolbar.find('.editor-btn, .editor-select, .editor-color').prop('disabled', false).css('opacity', 1);
                        $(this).html('&lt;/&gt; <?php echo esc_js( __( 'HTML', 'acf-mail-smtp' ) ); ?>');
                        self.isHtmlMode = false;
                    } else {
                        // Switch to HTML mode
                        self.$source.val(self.$content.html());
                        self.$content.hide();
                        self.$source.show();
                        self.$toolbar.find('.editor-btn, .editor-select, .editor-color').prop('disabled', true).css('opacity', 0.5);
                        $(this).html('👁️ <?php echo esc_js( __( '비주얼', 'acf-mail-smtp' ) ); ?>');
                        self.isHtmlMode = true;
                    }

                    self.syncToHidden();
                });
            },

            bindPreview: function() {
                var self = this;

                $('#preview-email-template').on('click', function(e) {
                    e.preventDefault();

                    var content = self.isHtmlMode ? self.$source.val() : self.$content.html();

                    // Replace variables with sample data
                    var sampleData = {
                        '{form_title}': $('#form_title').val() || '<?php echo esc_js( __( '문의하기', 'acf-mail-smtp' ) ); ?>',
                        '{site_name}': '<?php echo esc_js( get_bloginfo( 'name' ) ); ?>',
                        '{submission_date}': new Date().toLocaleDateString('ko-KR'),
                        '{all_fields}': self.generateSampleFieldsTable()
                    };

                    // Replace form field variables
                    var fields = window.formBuilder ? window.formBuilder.getFields() : [];
                    fields.forEach(function(field) {
                        sampleData['{' + field.name + '}'] = '<?php echo esc_js( __( '샘플 데이터', 'acf-mail-smtp' ) ); ?>';
                    });

                    for (var key in sampleData) {
                        content = content.split(key).join(sampleData[key]);
                    }

                    // Show preview modal
                    self.showPreviewModal(content);
                });
            },

            generateSampleFieldsTable: function() {
                var fields = window.formBuilder ? window.formBuilder.getFields() : [];
                var html = '<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">';

                if (fields.length === 0) {
                    html += '<tr><td style="padding: 12px; border: 1px solid #e5e7eb;"><?php echo esc_js( __( '필드 없음', 'acf-mail-smtp' ) ); ?></td></tr>';
                } else {
                    fields.forEach(function(field) {
                        if (['hidden', 'divider', 'heading', 'html'].indexOf(field.type) === -1) {
                            html += '<tr>';
                            html += '<td style="padding: 12px; border: 1px solid #e5e7eb; background: #f9fafb; font-weight: 600; width: 30%;">' + (field.label || field.name) + '</td>';
                            html += '<td style="padding: 12px; border: 1px solid #e5e7eb;"><?php echo esc_js( __( '샘플 데이터', 'acf-mail-smtp' ) ); ?></td>';
                            html += '</tr>';
                        }
                    });
                }

                html += '</table>';
                return html;
            },

            showPreviewModal: function(content) {
                // Remove existing modal
                $('#email-preview-modal').remove();

                var modalHtml = '<div id="email-preview-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 100001; display: flex; align-items: center; justify-content: center;">';
                modalHtml += '<div style="background: white; border-radius: 12px; max-width: 700px; width: 90%; max-height: 80vh; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">';
                modalHtml += '<div style="padding: 16px 20px; background: #f9fafb; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">';
                modalHtml += '<h3 style="margin: 0; font-size: 16px; color: #374151;">📧 <?php echo esc_js( __( '이메일 미리보기', 'acf-mail-smtp' ) ); ?></h3>';
                modalHtml += '<button type="button" id="close-preview-modal" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #6b7280; line-height: 1;">&times;</button>';
                modalHtml += '</div>';
                modalHtml += '<div style="padding: 24px; overflow-y: auto; max-height: calc(80vh - 60px);">' + content + '</div>';
                modalHtml += '</div></div>';

                $('body').append(modalHtml);

                $('#close-preview-modal, #email-preview-modal').on('click', function(e) {
                    if (e.target === this) {
                        $('#email-preview-modal').remove();
                    }
                });
            },

            updateFieldVariables: function() {
                var fields = window.formBuilder ? window.formBuilder.getFields() : [];
                var $container = $('#form-field-vars');

                // Clear existing field vars (keep header)
                $container.find('.field-var-item').remove();

                if (fields.length === 0) {
                    $container.append('<div class="field-var-item" style="padding: 8px 16px; color: #9ca3af; font-style: italic;"><?php echo esc_js( __( '폼에 필드를 추가하세요', 'acf-mail-smtp' ) ); ?></div>');
                } else {
                    fields.forEach(function(field) {
                        if (['divider', 'heading', 'html'].indexOf(field.type) === -1) {
                            var $item = $('<div class="field-var-item" data-var="{' + field.name + '}" style="padding: 8px 16px; cursor: pointer; transition: background 0.2s;"></div>');
                            $item.html('<code style="background: #f3f4f6; padding: 2px 6px; border-radius: 4px; font-size: 12px;">{' + field.name + '}</code>' +
                                       '<span style="margin-left: 8px; color: #6b7280; font-size: 13px;">' + (field.label || field.name) + '</span>');
                            $container.append($item);
                        }
                    });
                }
            },

            syncToHidden: function() {
                var content = this.isHtmlMode ? this.$source.val() : this.$content.html();
                $('#email_template').val(content);
            }
        };

        // Initialize email editor
        emailEditor.init();

        // Update field variables when form builder changes
        setInterval(function() {
            emailEditor.updateFieldVariables();
        }, 3000);
    });
    </script>
    <?php
} else {
    // Forms list view
    $forms = $forms_manager->get_forms();
    ?>
    <div class="wrap acf-mail-smtp-forms-v25">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1 class="wp-bulk-seo-header-v25" style="margin: 0;">
                <?php esc_html_e( '📋 폼 관리', 'acf-mail-smtp' ); ?>
            </h1>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-mail-smtp-forms&action=new' ) ); ?>"
               class="acf-btn" style="padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 8px; text-decoration: none; font-size: 15px; font-weight: 600;">
                ✨ <?php esc_html_e( '새 폼 만들기', 'acf-mail-smtp' ); ?>
            </a>
        </div>

        <?php if ( ! empty( $forms ) ) : ?>
            <!-- Forms Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
                <?php foreach ( $forms as $form ) :
                    $field_count = isset( $form['fields'] ) ? count( $form['fields'] ) : 0;
                    $status_class = $form['status'] === 'active' ? 'status-active' : 'status-inactive';
                ?>
                    <div class="acf-form-card" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.2s ease;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                            <div>
                                <h3 style="margin: 0 0 8px 0; font-size: 18px; color: #374151;">
                                    <?php echo esc_html( $form['name'] ); ?>
                                </h3>
                                <?php if ( ! empty( $form['title'] ) ) : ?>
                                    <p style="margin: 0; font-size: 14px; color: #6b7280;">
                                        <?php echo esc_html( $form['title'] ); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <span class="<?php echo esc_attr( $status_class ); ?>">
                                <?php echo esc_html( ucfirst( $form['status'] ) ); ?>
                            </span>
                        </div>

                        <div style="display: flex; gap: 20px; margin-bottom: 16px; font-size: 13px; color: #6b7280;">
                            <span>📝 <?php echo esc_html( sprintf( __( '%d개 필드', 'acf-mail-smtp' ), $field_count ) ); ?></span>
                            <span>📅 <?php echo esc_html( date_i18n( 'Y-m-d', strtotime( $form['created_at'] ) ) ); ?></span>
                        </div>

                        <div style="background: #f9fafb; border-radius: 8px; padding: 12px; margin-bottom: 16px;">
                            <code style="font-size: 13px; color: #667eea;">[acf_form id="<?php echo esc_attr( $form['id'] ); ?>"]</code>
                            <button type="button" class="copy-shortcode" data-shortcode='[acf_form id="<?php echo esc_attr( $form['id'] ); ?>"]'
                                    style="margin-left: 10px; padding: 4px 10px; background: #667eea; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                복사
                            </button>
                        </div>

                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-mail-smtp-forms&action=edit&id=' . $form['id'] ) ); ?>"
                               style="padding: 8px 16px; background: #f3f4f6; color: #374151; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 500;">
                                ✏️ <?php esc_html_e( '편집', 'acf-mail-smtp' ); ?>
                            </a>
                            <a href="#" class="duplicate-form" data-form-id="<?php echo esc_attr( $form['id'] ); ?>"
                               style="padding: 8px 16px; background: #dbeafe; color: #1e40af; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 500;">
                                📋 <?php esc_html_e( '복제', 'acf-mail-smtp' ); ?>
                            </a>
                            <a href="#" class="delete-form" data-form-id="<?php echo esc_attr( $form['id'] ); ?>"
                               style="padding: 8px 16px; background: #fee2e2; color: #dc2626; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 500;">
                                🗑️ <?php esc_html_e( '삭제', 'acf-mail-smtp' ); ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <!-- Empty State -->
            <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div style="font-size: 72px; margin-bottom: 20px; opacity: 0.5;">📝</div>
                <h2 style="margin: 0 0 12px 0; font-size: 24px; color: #374151;">
                    <?php esc_html_e( '아직 폼이 없습니다', 'acf-mail-smtp' ); ?>
                </h2>
                <p style="margin: 0 0 24px 0; font-size: 16px; color: #6b7280;">
                    <?php esc_html_e( '드래그 앤 드롭으로 쉽게 폼을 만들어보세요!', 'acf-mail-smtp' ); ?>
                </p>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-mail-smtp-forms&action=new' ) ); ?>"
                   style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px; text-decoration: none; font-size: 16px; font-weight: 600;">
                    ✨ <?php esc_html_e( '첫 번째 폼 만들기', 'acf-mail-smtp' ); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Copy shortcode
        $('.copy-shortcode').on('click', function() {
            var shortcode = $(this).data('shortcode');
            navigator.clipboard.writeText(shortcode).then(function() {
                window.acfMailSmtpToast('<?php echo esc_js( __( '숏코드가 복사되었습니다!', 'acf-mail-smtp' ) ); ?>', 'success');
            });
        });

        // Delete form
        $('.delete-form').on('click', function(e) {
            e.preventDefault();
            if (!confirm(acfMailSmtp.strings.confirmDelete)) {
                return;
            }

            var $card = $(this).closest('.acf-form-card');
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
                        $card.fadeOut(300, function() {
                            $(this).remove();
                            // Check if no forms left
                            if ($('.acf-form-card').length === 0) {
                                location.reload();
                            }
                        });
                        window.acfMailSmtpToast(response.data.message, 'success');
                    } else {
                        window.acfMailSmtpToast(response.data.message || acfMailSmtp.strings.error, 'error');
                    }
                }
            });
        });

        // Duplicate form
        $('.duplicate-form').on('click', function(e) {
            e.preventDefault();
            var formId = $(this).data('form-id');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'acf_mail_smtp_duplicate_form',
                    nonce: acfMailSmtp.nonce,
                    form_id: formId
                },
                success: function(response) {
                    if (response.success) {
                        window.acfMailSmtpToast(response.data.message, 'success');
                        location.reload();
                    } else {
                        window.acfMailSmtpToast(response.data.message || acfMailSmtp.strings.error, 'error');
                    }
                }
            });
        });

        // Hover effect for cards
        $('.acf-form-card').hover(
            function() { $(this).css('transform', 'translateY(-4px)').css('box-shadow', '0 8px 24px rgba(0,0,0,0.12)'); },
            function() { $(this).css('transform', 'translateY(0)').css('box-shadow', '0 2px 8px rgba(0,0,0,0.08)'); }
        );
    });
    </script>
    <?php
}
?>
