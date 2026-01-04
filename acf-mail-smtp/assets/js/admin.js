/**
 * ACF Mail SMTP Admin JavaScript
 * 
 * @package ACF_Mail_SMTP
 * @version 1.0.0
 * @author 3J Labs
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Common AJAX error handler
        $(document).ajaxError(function(event, xhr, settings, error) {
            if (settings.url && settings.url.includes('acf_mail_smtp')) {
                console.error('ACF Mail SMTP AJAX Error:', error);
            }
        });

        // Toast notifications (simple implementation)
        function showToast(message, type) {
            type = type || 'success';
            var toast = $('<div class="acf-mail-smtp-toast toast-' + type + '">' + message + '</div>');
            $('body').append(toast);
            setTimeout(function() {
                toast.fadeOut(function() {
                    $(this).remove();
                });
            }, 3000);
        }

        // Expose to global scope
        window.acfMailSmtpToast = showToast;
    });

})(jQuery);
