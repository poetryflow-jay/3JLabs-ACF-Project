<?php
/**
 * Form Template - Enhanced with Conditional Logic Support
 *
 * @package ACF_Mail_SMTP
 * @version 2.0.0
 * @author 3J Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$form_settings = isset( $form['settings'] ) ? $form['settings'] : array();
$submit_text = isset( $form_settings['submit_text'] ) ? $form_settings['submit_text'] : __( '제출', 'acf-mail-smtp' );
$ajax_submit = isset( $form_settings['ajax_submit'] ) ? $form_settings['ajax_submit'] : true;
$enable_honeypot = isset( $form_settings['enable_honeypot'] ) ? $form_settings['enable_honeypot'] : true;
$form_class = isset( $atts['class'] ) ? ' ' . esc_attr( $atts['class'] ) : '';

// Prepare fields data for conditional logic
$fields_json = wp_json_encode( isset( $form['fields'] ) ? $form['fields'] : array() );
?>

<div class="acf-mail-smtp-form-wrapper<?php echo esc_attr( $form_class ); ?>" data-form-id="<?php echo esc_attr( $form['id'] ); ?>">
    <?php if ( ! empty( $form['title'] ) ) : ?>
        <h2 class="acf-form-title"><?php echo esc_html( $form['title'] ); ?></h2>
    <?php endif; ?>

    <?php if ( ! empty( $form['description'] ) ) : ?>
        <p class="acf-form-description"><?php echo esc_html( $form['description'] ); ?></p>
    <?php endif; ?>

    <form class="acf-mail-smtp-form" method="post" enctype="multipart/form-data" <?php echo $ajax_submit ? 'data-ajax="1"' : ''; ?>>
        <input type="hidden" name="form_id" value="<?php echo esc_attr( $form['id'] ); ?>" />
        <?php wp_nonce_field( 'acf_mail_smtp_nonce', 'acf_mail_smtp_nonce_field' ); ?>

        <?php if ( $enable_honeypot ) : ?>
            <!-- Honeypot field - hidden from users, bots will fill it -->
            <div style="position: absolute; left: -9999px;" aria-hidden="true">
                <input type="text" name="acf_hp_check" value="" tabindex="-1" autocomplete="off" />
            </div>
        <?php endif; ?>

        <?php if ( ! empty( $form['fields'] ) && is_array( $form['fields'] ) ) : ?>
            <?php foreach ( $form['fields'] as $field ) :
                $field_id = isset( $field['id'] ) ? $field['id'] : '';
                $field_name = isset( $field['name'] ) ? $field['name'] : $field_id;
                $field_type = isset( $field['type'] ) ? $field['type'] : 'text';
                $field_label = isset( $field['label'] ) ? $field['label'] : '';
                $field_required = isset( $field['required'] ) && $field['required'];
                $field_settings = isset( $field['settings'] ) ? $field['settings'] : array();
                $field_options = isset( $field['options'] ) ? $field['options'] : array();
                $conditional_logic = isset( $field['conditionalLogic'] ) ? $field['conditionalLogic'] : null;

                // Build CSS classes
                $css_class = 'acf-form-field acf-field-type-' . esc_attr( $field_type );
                if ( isset( $field_settings['cssClass'] ) && $field_settings['cssClass'] ) {
                    $css_class .= ' ' . esc_attr( $field_settings['cssClass'] );
                }
                if ( $field_required ) {
                    $css_class .= ' acf-field-required';
                }
                if ( $conditional_logic && $conditional_logic['enabled'] ) {
                    $css_class .= ' acf-has-conditional-logic';
                }

                // Common attributes
                $placeholder = isset( $field_settings['placeholder'] ) ? $field_settings['placeholder'] : '';
                $default_value = isset( $field_settings['defaultValue'] ) ? $field_settings['defaultValue'] : '';
                $description = isset( $field_settings['description'] ) ? $field_settings['description'] : '';
            ?>
                <div class="<?php echo esc_attr( $css_class ); ?>"
                     data-field-id="<?php echo esc_attr( $field_id ); ?>"
                     data-field-name="<?php echo esc_attr( $field_name ); ?>"
                     <?php if ( $conditional_logic && $conditional_logic['enabled'] ) : ?>
                        data-conditional-logic='<?php echo esc_attr( wp_json_encode( $conditional_logic ) ); ?>'
                     <?php endif; ?>>

                    <?php if ( ! in_array( $field_type, array( 'divider', 'html', 'heading', 'hidden' ), true ) ) : ?>
                        <label for="<?php echo esc_attr( $field_id ); ?>" class="acf-field-label">
                            <?php echo esc_html( $field_label ); ?>
                            <?php if ( $field_required ) : ?>
                                <span class="acf-required">*</span>
                            <?php endif; ?>
                        </label>
                    <?php endif; ?>

                    <?php
                    // Render field based on type
                    switch ( $field_type ) :
                        case 'text':
                        case 'email':
                        case 'tel':
                        case 'url':
                            $min_length = isset( $field_settings['minLength'] ) ? $field_settings['minLength'] : '';
                            $max_length = isset( $field_settings['maxLength'] ) ? $field_settings['maxLength'] : '';
                            ?>
                            <input type="<?php echo esc_attr( $field_type ); ?>"
                                   id="<?php echo esc_attr( $field_id ); ?>"
                                   name="<?php echo esc_attr( $field_name ); ?>"
                                   value="<?php echo esc_attr( $default_value ); ?>"
                                   placeholder="<?php echo esc_attr( $placeholder ); ?>"
                                   <?php echo $min_length ? 'minlength="' . esc_attr( $min_length ) . '"' : ''; ?>
                                   <?php echo $max_length ? 'maxlength="' . esc_attr( $max_length ) . '"' : ''; ?>
                                   <?php echo $field_required ? 'required' : ''; ?> />
                            <?php
                            break;

                        case 'number':
                            $min = isset( $field_settings['min'] ) ? $field_settings['min'] : '';
                            $max = isset( $field_settings['max'] ) ? $field_settings['max'] : '';
                            $step = isset( $field_settings['step'] ) ? $field_settings['step'] : '1';
                            ?>
                            <input type="number"
                                   id="<?php echo esc_attr( $field_id ); ?>"
                                   name="<?php echo esc_attr( $field_name ); ?>"
                                   value="<?php echo esc_attr( $default_value ); ?>"
                                   placeholder="<?php echo esc_attr( $placeholder ); ?>"
                                   <?php echo $min !== '' ? 'min="' . esc_attr( $min ) . '"' : ''; ?>
                                   <?php echo $max !== '' ? 'max="' . esc_attr( $max ) . '"' : ''; ?>
                                   step="<?php echo esc_attr( $step ); ?>"
                                   <?php echo $field_required ? 'required' : ''; ?> />
                            <?php
                            break;

                        case 'textarea':
                            $rows = isset( $field_settings['rows'] ) ? $field_settings['rows'] : 4;
                            $max_length = isset( $field_settings['maxLength'] ) ? $field_settings['maxLength'] : '';
                            ?>
                            <textarea id="<?php echo esc_attr( $field_id ); ?>"
                                      name="<?php echo esc_attr( $field_name ); ?>"
                                      placeholder="<?php echo esc_attr( $placeholder ); ?>"
                                      rows="<?php echo esc_attr( $rows ); ?>"
                                      <?php echo $max_length ? 'maxlength="' . esc_attr( $max_length ) . '"' : ''; ?>
                                      <?php echo $field_required ? 'required' : ''; ?>><?php echo esc_textarea( $default_value ); ?></textarea>
                            <?php
                            break;

                        case 'select':
                            $multiple = isset( $field_settings['multiple'] ) && $field_settings['multiple'];
                            ?>
                            <select id="<?php echo esc_attr( $field_id ); ?>"
                                    name="<?php echo esc_attr( $field_name ) . ( $multiple ? '[]' : '' ); ?>"
                                    <?php echo $multiple ? 'multiple' : ''; ?>
                                    <?php echo $field_required ? 'required' : ''; ?>>
                                <?php if ( $placeholder && ! $multiple ) : ?>
                                    <option value=""><?php echo esc_html( $placeholder ); ?></option>
                                <?php endif; ?>
                                <?php foreach ( $field_options as $option ) :
                                    $opt_label = isset( $option['label'] ) ? $option['label'] : $option;
                                    $opt_value = isset( $option['value'] ) ? $option['value'] : $option;
                                ?>
                                    <option value="<?php echo esc_attr( $opt_value ); ?>">
                                        <?php echo esc_html( $opt_label ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php
                            break;

                        case 'checkbox':
                            $layout = isset( $field_settings['layout'] ) ? $field_settings['layout'] : 'vertical';
                            ?>
                            <div class="acf-options-group acf-layout-<?php echo esc_attr( $layout ); ?>">
                                <?php foreach ( $field_options as $index => $option ) :
                                    $opt_label = isset( $option['label'] ) ? $option['label'] : $option;
                                    $opt_value = isset( $option['value'] ) ? $option['value'] : $option;
                                ?>
                                    <label class="acf-option">
                                        <input type="checkbox"
                                               name="<?php echo esc_attr( $field_name ); ?>[]"
                                               value="<?php echo esc_attr( $opt_value ); ?>" />
                                        <?php echo esc_html( $opt_label ); ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <?php
                            break;

                        case 'radio':
                            $layout = isset( $field_settings['layout'] ) ? $field_settings['layout'] : 'vertical';
                            ?>
                            <div class="acf-options-group acf-layout-<?php echo esc_attr( $layout ); ?>">
                                <?php foreach ( $field_options as $index => $option ) :
                                    $opt_label = isset( $option['label'] ) ? $option['label'] : $option;
                                    $opt_value = isset( $option['value'] ) ? $option['value'] : $option;
                                ?>
                                    <label class="acf-option">
                                        <input type="radio"
                                               name="<?php echo esc_attr( $field_name ); ?>"
                                               value="<?php echo esc_attr( $opt_value ); ?>"
                                               <?php echo $field_required && $index === 0 ? 'required' : ''; ?> />
                                        <?php echo esc_html( $opt_label ); ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <?php
                            break;

                        case 'date':
                            $min_date = isset( $field_settings['minDate'] ) ? $field_settings['minDate'] : '';
                            $max_date = isset( $field_settings['maxDate'] ) ? $field_settings['maxDate'] : '';
                            ?>
                            <input type="date"
                                   id="<?php echo esc_attr( $field_id ); ?>"
                                   name="<?php echo esc_attr( $field_name ); ?>"
                                   <?php echo $min_date ? 'min="' . esc_attr( $min_date ) . '"' : ''; ?>
                                   <?php echo $max_date ? 'max="' . esc_attr( $max_date ) . '"' : ''; ?>
                                   <?php echo $field_required ? 'required' : ''; ?> />
                            <?php
                            break;

                        case 'time':
                            ?>
                            <input type="time"
                                   id="<?php echo esc_attr( $field_id ); ?>"
                                   name="<?php echo esc_attr( $field_name ); ?>"
                                   <?php echo $field_required ? 'required' : ''; ?> />
                            <?php
                            break;

                        case 'datetime':
                            ?>
                            <input type="datetime-local"
                                   id="<?php echo esc_attr( $field_id ); ?>"
                                   name="<?php echo esc_attr( $field_name ); ?>"
                                   <?php echo $field_required ? 'required' : ''; ?> />
                            <?php
                            break;

                        case 'file':
                            $allowed_types = isset( $field_settings['allowedTypes'] ) ? $field_settings['allowedTypes'] : '';
                            $multiple = isset( $field_settings['multiple'] ) && $field_settings['multiple'];
                            $accept = '';
                            if ( $allowed_types ) {
                                $accept = '.' . str_replace( ',', ',.', str_replace( ' ', '', $allowed_types ) );
                            }
                            ?>
                            <input type="file"
                                   id="<?php echo esc_attr( $field_id ); ?>"
                                   name="<?php echo esc_attr( $field_name ) . ( $multiple ? '[]' : '' ); ?>"
                                   <?php echo $accept ? 'accept="' . esc_attr( $accept ) . '"' : ''; ?>
                                   <?php echo $multiple ? 'multiple' : ''; ?>
                                   <?php echo $field_required ? 'required' : ''; ?> />
                            <?php
                            break;

                        case 'hidden':
                            ?>
                            <input type="hidden"
                                   id="<?php echo esc_attr( $field_id ); ?>"
                                   name="<?php echo esc_attr( $field_name ); ?>"
                                   value="<?php echo esc_attr( $default_value ); ?>" />
                            <?php
                            break;

                        case 'html':
                            $content = isset( $field_settings['content'] ) ? $field_settings['content'] : '';
                            echo wp_kses_post( $content );
                            break;

                        case 'divider':
                            ?>
                            <hr class="acf-divider" />
                            <?php
                            break;

                        case 'heading':
                            $content = isset( $field_settings['content'] ) ? $field_settings['content'] : '';
                            $level = isset( $field_settings['level'] ) ? $field_settings['level'] : 'h3';
                            echo '<' . esc_attr( $level ) . ' class="acf-heading">' . esc_html( $content ) . '</' . esc_attr( $level ) . '>';
                            break;

                        default:
                            ?>
                            <input type="text"
                                   id="<?php echo esc_attr( $field_id ); ?>"
                                   name="<?php echo esc_attr( $field_name ); ?>"
                                   value="<?php echo esc_attr( $default_value ); ?>"
                                   placeholder="<?php echo esc_attr( $placeholder ); ?>"
                                   <?php echo $field_required ? 'required' : ''; ?> />
                            <?php
                            break;
                    endswitch;
                    ?>

                    <?php if ( $description ) : ?>
                        <p class="acf-field-description"><?php echo esc_html( $description ); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="acf-form-field acf-field-submit">
            <button type="submit" class="acf-submit-button">
                <?php echo esc_html( $submit_text ); ?>
            </button>
        </div>

        <div class="acf-form-messages" style="display: none;">
            <div class="acf-message acf-message-success" style="display: none;"></div>
            <div class="acf-message acf-message-error" style="display: none;"></div>
        </div>
    </form>
</div>

<script>
(function() {
    'use strict';

    var formWrapper = document.querySelector('.acf-mail-smtp-form-wrapper[data-form-id="<?php echo esc_js( $form['id'] ); ?>"]');
    if (!formWrapper) return;

    var form = formWrapper.querySelector('.acf-mail-smtp-form');
    var fields = JSON.parse('<?php echo addslashes( $fields_json ); ?>');

    /**
     * Evaluate conditional logic
     */
    function evaluateConditions() {
        var conditionalFields = formWrapper.querySelectorAll('.acf-has-conditional-logic');

        conditionalFields.forEach(function(fieldEl) {
            var logicData = fieldEl.dataset.conditionalLogic;
            if (!logicData) return;

            try {
                var logic = JSON.parse(logicData);
                if (!logic.enabled || !logic.rules || logic.rules.length === 0) return;

                var results = logic.rules.map(function(rule) {
                    return evaluateRule(rule);
                });

                var shouldShow;
                if (logic.logic === 'all') {
                    shouldShow = results.every(function(r) { return r; });
                } else {
                    shouldShow = results.some(function(r) { return r; });
                }

                if (logic.action === 'hide') {
                    shouldShow = !shouldShow;
                }

                fieldEl.style.display = shouldShow ? '' : 'none';

                // Disable required validation for hidden fields
                var inputs = fieldEl.querySelectorAll('input, select, textarea');
                inputs.forEach(function(input) {
                    if (!shouldShow) {
                        input.dataset.wasRequired = input.required;
                        input.required = false;
                    } else if (input.dataset.wasRequired === 'true') {
                        input.required = true;
                    }
                });

            } catch (e) {
                console.error('Conditional logic error:', e);
            }
        });
    }

    /**
     * Evaluate single rule
     */
    function evaluateRule(rule) {
        if (!rule.fieldId) return false;

        var sourceField = formWrapper.querySelector('[data-field-id="' + rule.fieldId + '"]');
        if (!sourceField) return false;

        var value = getFieldValue(sourceField, rule.fieldId);
        var compareValue = rule.value || '';

        switch (rule.operator) {
            case '==':
                return value == compareValue;
            case '!=':
                return value != compareValue;
            case '>':
                return parseFloat(value) > parseFloat(compareValue);
            case '<':
                return parseFloat(value) < parseFloat(compareValue);
            case '>=':
                return parseFloat(value) >= parseFloat(compareValue);
            case '<=':
                return parseFloat(value) <= parseFloat(compareValue);
            case 'contains':
                return String(value).toLowerCase().indexOf(String(compareValue).toLowerCase()) !== -1;
            case 'empty':
                return !value || value === '' || (Array.isArray(value) && value.length === 0);
            case 'not_empty':
                return value && value !== '' && !(Array.isArray(value) && value.length === 0);
            default:
                return false;
        }
    }

    /**
     * Get field value
     */
    function getFieldValue(fieldEl, fieldId) {
        var input = fieldEl.querySelector('input, select, textarea');
        if (!input) return '';

        var type = input.type || input.tagName.toLowerCase();

        if (type === 'checkbox') {
            var checked = fieldEl.querySelectorAll('input[type="checkbox"]:checked');
            return Array.from(checked).map(function(c) { return c.value; });
        }

        if (type === 'radio') {
            var selected = fieldEl.querySelector('input[type="radio"]:checked');
            return selected ? selected.value : '';
        }

        if (input.tagName.toLowerCase() === 'select' && input.multiple) {
            return Array.from(input.selectedOptions).map(function(o) { return o.value; });
        }

        return input.value;
    }

    // Bind events for conditional logic
    form.addEventListener('change', evaluateConditions);
    form.addEventListener('input', evaluateConditions);

    // Initial evaluation
    evaluateConditions();

    <?php if ( $ajax_submit ) : ?>
    // AJAX form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        var submitBtn = form.querySelector('.acf-submit-button');
        var originalText = submitBtn.textContent;
        submitBtn.textContent = '<?php echo esc_js( __( '전송 중...', 'acf-mail-smtp' ) ); ?>';
        submitBtn.disabled = true;

        var formData = new FormData(form);
        formData.append('action', 'acf_mail_smtp_submit_form');
        formData.append('nonce', '<?php echo esc_js( wp_create_nonce( 'acf_mail_smtp_nonce' ) ); ?>');

        // Collect field data
        var data = {};
        fields.forEach(function(field) {
            var fieldEl = formWrapper.querySelector('[data-field-id="' + field.id + '"]');
            if (fieldEl && fieldEl.style.display !== 'none') {
                data[field.id] = getFieldValue(fieldEl, field.id);
            }
        });
        formData.append('data', JSON.stringify(data));

        fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
            method: 'POST',
            body: formData
        })
        .then(function(response) { return response.json(); })
        .then(function(response) {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;

            var messages = formWrapper.querySelector('.acf-form-messages');
            var successMsg = messages.querySelector('.acf-message-success');
            var errorMsg = messages.querySelector('.acf-message-error');

            if (response.success) {
                messages.style.display = 'block';
                successMsg.textContent = response.data.message;
                successMsg.style.display = 'block';
                errorMsg.style.display = 'none';
                form.reset();
                evaluateConditions();

                if (response.data.redirect) {
                    window.location.href = response.data.redirect;
                }
            } else {
                messages.style.display = 'block';
                errorMsg.textContent = response.data.message || '<?php echo esc_js( __( '오류가 발생했습니다.', 'acf-mail-smtp' ) ); ?>';
                errorMsg.style.display = 'block';
                successMsg.style.display = 'none';
            }
        })
        .catch(function(error) {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
            console.error('Form submission error:', error);
        });
    });
    <?php endif; ?>
})();
</script>

<style>
/* ACF Mail SMTP Form Styles */
.acf-mail-smtp-form-wrapper {
    max-width: 600px;
    margin: 0 auto;
}

.acf-form-title {
    margin: 0 0 10px 0;
    font-size: 24px;
}

.acf-form-description {
    margin: 0 0 20px 0;
    color: #666;
}

.acf-form-field {
    margin-bottom: 20px;
}

.acf-field-label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
}

.acf-required {
    color: #ef4444;
    margin-left: 4px;
}

.acf-mail-smtp-form input[type="text"],
.acf-mail-smtp-form input[type="email"],
.acf-mail-smtp-form input[type="tel"],
.acf-mail-smtp-form input[type="url"],
.acf-mail-smtp-form input[type="number"],
.acf-mail-smtp-form input[type="date"],
.acf-mail-smtp-form input[type="time"],
.acf-mail-smtp-form input[type="datetime-local"],
.acf-mail-smtp-form textarea,
.acf-mail-smtp-form select {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 15px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.acf-mail-smtp-form input:focus,
.acf-mail-smtp-form textarea:focus,
.acf-mail-smtp-form select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
}

.acf-options-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.acf-options-group.acf-layout-horizontal {
    flex-direction: row;
    flex-wrap: wrap;
    gap: 20px;
}

.acf-option {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.acf-option input {
    margin: 0;
}

.acf-field-description {
    margin: 6px 0 0 0;
    font-size: 13px;
    color: #6b7280;
}

.acf-divider {
    border: none;
    border-top: 1px solid #e5e7eb;
    margin: 20px 0;
}

.acf-heading {
    margin: 20px 0 10px 0;
}

.acf-submit-button {
    padding: 12px 32px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.acf-submit-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.acf-submit-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.acf-form-messages {
    margin-top: 20px;
}

.acf-message {
    padding: 14px 18px;
    border-radius: 8px;
    font-size: 14px;
}

.acf-message-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #10b981;
}

.acf-message-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #ef4444;
}

/* Conditional logic transition */
.acf-has-conditional-logic {
    transition: opacity 0.2s ease;
}
</style>
