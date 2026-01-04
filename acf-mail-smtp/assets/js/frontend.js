/**
 * ACF Mail SMTP Frontend JavaScript
 * 
 * @package ACF_Mail_SMTP
 * @version 1.0.0
 * @author 3J Labs
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Handle form submission
        $('.acf-mail-smtp-form').on('submit', function(e) {
            e.preventDefault();

            var $form = $(this);
            var $submitBtn = $form.find('.submit-button');
            var formId = $form.data('form-id');
            var formData = {};

            // Collect form data
            $form.find('input, select, textarea').each(function() {
                var $field = $(this);
                var name = $field.attr('name') || $field.data('field-id');
                var type = $field.attr('type');
                var value = '';

                if (type === 'checkbox') {
                    if ($field.is(':checked')) {
                        value = $field.val();
                    }
                } else if (type === 'radio') {
                    if ($field.is(':checked')) {
                        value = $field.val();
                    }
                } else {
                    value = $field.val();
                }

                if (name && value) {
                    if (formData[name]) {
                        if (!Array.isArray(formData[name])) {
                            formData[name] = [formData[name]];
                        }
                        formData[name].push(value);
                    } else {
                        formData[name] = value;
                    }
                }
            });

            // Disable submit button
            $submitBtn.prop('disabled', true);
            var originalText = $submitBtn.text();
            $submitBtn.html(originalText + ' <span class="loading"></span>');

            // Remove previous messages
            $form.find('.message').remove();

            // Submit form
            $.ajax({
                url: acfMailSmtp.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'acf_mail_smtp_submit_form',
                    nonce: acfMailSmtp.nonce,
                    form_id: formId,
                    data: formData
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        var successMsg = $('<div class="message success">' + response.data.message + '</div>');
                        $form.prepend(successMsg);

                        // Reset form
                        $form[0].reset();

                        // Redirect if specified
                        if (response.data.redirect) {
                            setTimeout(function() {
                                window.location.href = response.data.redirect;
                            }, 2000);
                        }
                    } else {
                        // Show error message
                        var errorMsg = $('<div class="message error">' + (response.data.message || acfMailSmtp.strings.error) + '</div>');
                        $form.prepend(errorMsg);
                    }
                },
                error: function() {
                    var errorMsg = $('<div class="message error">' + acfMailSmtp.strings.error + '</div>');
                    $form.prepend(errorMsg);
                },
                complete: function() {
                    // Re-enable submit button
                    $submitBtn.prop('disabled', false);
                    $submitBtn.text(originalText);
                }
            });
        });
    });

})(jQuery);
