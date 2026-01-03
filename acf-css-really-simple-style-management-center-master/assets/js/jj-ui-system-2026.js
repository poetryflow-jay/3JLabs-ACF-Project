/**
 * 3J Labs UI System 2026 - JavaScript Utilities
 * Modern WordPress Admin Interface Helpers
 * 
 * @version 1.0.0
 * @author Jason (Lead Engineer) x Jenny (UX Designer)
 * @created 2026-01-03
 */

(function($) {
    'use strict';

    // Namespace
    window.JJ_UI = window.JJ_UI || {};

    /**
     * Tab System
     */
    JJ_UI.Tabs = {
        init: function(container) {
            const $container = $(container);
            const $tabs = $container.find('.jj-tab-link');
            const $contents = $container.find('.jj-tab-content');

            $tabs.on('click', function(e) {
                e.preventDefault();
                const targetId = $(this).attr('href');

                // Update active states
                $tabs.removeClass('active');
                $(this).addClass('active');

                $contents.removeClass('active');
                $(targetId).addClass('active');

                // Save active tab to localStorage
                const storageKey = $container.data('tab-storage') || 'jj_active_tab';
                localStorage.setItem(storageKey, targetId);
            });

            // Restore active tab from localStorage
            const storageKey = $container.data('tab-storage') || 'jj_active_tab';
            const savedTab = localStorage.getItem(storageKey);
            if (savedTab && $(savedTab).length) {
                $tabs.filter('[href="' + savedTab + '"]').trigger('click');
            }
        }
    };

    /**
     * Toast Notification System
     */
    JJ_UI.Toast = {
        container: null,

        init: function() {
            if (!this.container) {
                this.container = $('<div class="jj-toast-container"></div>');
                $('body').append(this.container);

                // Add toast styles if not exists
                if (!$('#jj-toast-styles').length) {
                    $('<style id="jj-toast-styles">' +
                        '.jj-toast-container { position: fixed; top: 32px; right: 32px; z-index: 999999; max-width: 400px; }' +
                        '.jj-toast { background: white; padding: 16px 20px; margin-bottom: 12px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); display: flex; align-items: center; gap: 12px; animation: jj-toast-slide-in 0.3s ease-out; }' +
                        '.jj-toast.success { border-left: 4px solid #10B981; }' +
                        '.jj-toast.error { border-left: 4px solid #EF4444; }' +
                        '.jj-toast.warning { border-left: 4px solid #F59E0B; }' +
                        '.jj-toast.info { border-left: 4px solid #3B82F6; }' +
                        '.jj-toast-icon { font-size: 24px; flex-shrink: 0; }' +
                        '.jj-toast.success .jj-toast-icon { color: #10B981; }' +
                        '.jj-toast.error .jj-toast-icon { color: #EF4444; }' +
                        '.jj-toast.warning .jj-toast-icon { color: #F59E0B; }' +
                        '.jj-toast.info .jj-toast-icon { color: #3B82F6; }' +
                        '.jj-toast-content { flex: 1; }' +
                        '.jj-toast-title { font-weight: 600; margin-bottom: 4px; color: #262626; }' +
                        '.jj-toast-message { font-size: 13px; color: #737373; }' +
                        '@keyframes jj-toast-slide-in { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }' +
                        '@keyframes jj-toast-slide-out { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }' +
                    '</style>').appendTo('head');
                }
            }
        },

        show: function(message, type, duration) {
            this.init();
            type = type || 'info';
            duration = duration || 3000;

            const icons = {
                success: 'dashicons-yes-alt',
                error: 'dashicons-dismiss',
                warning: 'dashicons-warning',
                info: 'dashicons-info'
            };

            const toast = $('<div class="jj-toast ' + type + '">' +
                '<span class="jj-toast-icon dashicons ' + icons[type] + '"></span>' +
                '<div class="jj-toast-content">' +
                    '<div class="jj-toast-message">' + message + '</div>' +
                '</div>' +
            '</div>');

            this.container.append(toast);

            setTimeout(function() {
                toast.css('animation', 'jj-toast-slide-out 0.3s ease-out');
                setTimeout(function() { toast.remove(); }, 300);
            }, duration);
        },

        success: function(message, duration) {
            this.show(message, 'success', duration);
        },

        error: function(message, duration) {
            this.show(message, 'error', duration);
        },

        warning: function(message, duration) {
            this.show(message, 'warning', duration);
        },

        info: function(message, duration) {
            this.show(message, 'info', duration);
        }
    };

    /**
     * Modal Dialog System
     */
    JJ_UI.Modal = {
        show: function(options) {
            const defaults = {
                title: '',
                content: '',
                buttons: [],
                width: '500px',
                onClose: function() {}
            };

            const settings = $.extend({}, defaults, options);

            // Create overlay
            const overlay = $('<div class="jj-modal-overlay"></div>');
            const modal = $('<div class="jj-modal"></div>').css('max-width', settings.width);

            // Modal header
            if (settings.title) {
                const header = $('<div class="jj-modal-header">' +
                    '<h3 class="jj-modal-title">' + settings.title + '</h3>' +
                    '<button class="jj-modal-close" aria-label="Close">&times;</button>' +
                '</div>');
                modal.append(header);
            }

            // Modal content
            const content = $('<div class="jj-modal-content"></div>').html(settings.content);
            modal.append(content);

            // Modal footer
            if (settings.buttons && settings.buttons.length > 0) {
                const footer = $('<div class="jj-modal-footer"></div>');
                settings.buttons.forEach(function(btn) {
                    const button = $('<button></button>')
                        .addClass('jj-btn')
                        .addClass(btn.class || 'jj-btn-secondary')
                        .text(btn.text)
                        .on('click', function() {
                            if (btn.onClick) btn.onClick();
                            JJ_UI.Modal.close();
                        });
                    footer.append(button);
                });
                modal.append(footer);
            }

            overlay.append(modal);
            $('body').append(overlay);

            // Add modal styles if not exists
            if (!$('#jj-modal-styles').length) {
                $('<style id="jj-modal-styles">' +
                    '.jj-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999998; display: flex; align-items: center; justify-content: center; animation: jj-fade-in 0.2s; }' +
                    '.jj-modal { background: white; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); animation: jj-modal-slide-up 0.3s ease-out; }' +
                    '.jj-modal-header { padding: 24px 24px 16px; border-bottom: 1px solid #E5E5E5; display: flex; justify-content: space-between; align-items: center; }' +
                    '.jj-modal-title { margin: 0; font-size: 18px; font-weight: 600; color: #262626; }' +
                    '.jj-modal-close { background: none; border: none; font-size: 28px; color: #737373; cursor: pointer; line-height: 1; padding: 0; width: 32px; height: 32px; }' +
                    '.jj-modal-close:hover { color: #262626; }' +
                    '.jj-modal-content { padding: 24px; max-height: 60vh; overflow-y: auto; }' +
                    '.jj-modal-footer { padding: 16px 24px; border-top: 1px solid #E5E5E5; display: flex; gap: 12px; justify-content: flex-end; }' +
                    '@keyframes jj-modal-slide-up { from { transform: translateY(50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }' +
                '</style>').appendTo('head');
            }

            // Close handlers
            overlay.on('click', function(e) {
                if (e.target === overlay[0]) {
                    JJ_UI.Modal.close();
                    settings.onClose();
                }
            });

            modal.find('.jj-modal-close').on('click', function() {
                JJ_UI.Modal.close();
                settings.onClose();
            });

            // ESC key to close
            $(document).on('keydown.jjmodal', function(e) {
                if (e.key === 'Escape') {
                    JJ_UI.Modal.close();
                    settings.onClose();
                }
            });
        },

        close: function() {
            $('.jj-modal-overlay').fadeOut(200, function() {
                $(this).remove();
            });
            $(document).off('keydown.jjmodal');
        }
    };

    /**
     * Confirmation Dialog
     */
    JJ_UI.Confirm = function(message, onConfirm, onCancel) {
        JJ_UI.Modal.show({
            title: 'Confirm Action',
            content: '<p style="margin:0; font-size:14px; color:#525252;">' + message + '</p>',
            buttons: [
                {
                    text: 'Cancel',
                    class: 'jj-btn-secondary',
                    onClick: onCancel || function() {}
                },
                {
                    text: 'Confirm',
                    class: 'jj-btn-primary',
                    onClick: onConfirm || function() {}
                }
            ]
        });
    };

    /**
     * AJAX Wrapper with Loading State
     */
    JJ_UI.Ajax = function(options) {
        const defaults = {
            action: '',
            data: {},
            button: null,
            success: function() {},
            error: function() {},
            complete: function() {}
        };

        const settings = $.extend({}, defaults, options);
        const $button = $(settings.button);

        // Save original button state
        let originalText = '';
        let originalDisabled = false;

        if ($button.length) {
            originalText = $button.html();
            originalDisabled = $button.prop('disabled');
            $button.prop('disabled', true);
            $button.html('<span class="jj-spinner"></span> Processing...');
        }

        // Prepare data
        const ajaxData = $.extend({}, settings.data, {
            action: settings.action,
            nonce: window.jj_admin_ajax?.nonce || ''
        });

        // Make AJAX request
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: ajaxData,
            success: function(response) {
                if (response.success) {
                    settings.success(response.data);
                } else {
                    const message = response.data?.message || 'An error occurred';
                    JJ_UI.Toast.error(message);
                    settings.error(response.data);
                }
            },
            error: function(xhr, status, error) {
                JJ_UI.Toast.error('Network error: ' + error);
                settings.error({message: error});
            },
            complete: function() {
                // Restore button state
                if ($button.length) {
                    $button.html(originalText);
                    $button.prop('disabled', originalDisabled);
                }
                settings.complete();
            }
        });
    };

    /**
     * Form Validation
     */
    JJ_UI.Validate = {
        form: function($form) {
            let isValid = true;
            const $inputs = $form.find('[required]');

            $inputs.each(function() {
                const $input = $(this);
                const value = $input.val().trim();

                // Remove previous error
                $input.removeClass('jj-input-error');
                $input.siblings('.jj-form-error').remove();

                if (!value) {
                    isValid = false;
                    $input.addClass('jj-input-error');
                    $input.after('<span class="jj-form-error">This field is required</span>');
                }

                // Email validation
                if ($input.attr('type') === 'email' && value) {
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailPattern.test(value)) {
                        isValid = false;
                        $input.addClass('jj-input-error');
                        $input.after('<span class="jj-form-error">Please enter a valid email</span>');
                    }
                }

                // URL validation
                if ($input.attr('type') === 'url' && value) {
                    try {
                        new URL(value);
                    } catch (e) {
                        isValid = false;
                        $input.addClass('jj-input-error');
                        $input.after('<span class="jj-form-error">Please enter a valid URL</span>');
                    }
                }
            });

            // Add error input style
            if (!$('#jj-form-error-styles').length) {
                $('<style id="jj-form-error-styles">' +
                    '.jj-input-error { border-color: #EF4444 !important; }' +
                    '.jj-input-error:focus { box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important; }' +
                '</style>').appendTo('head');
            }

            return isValid;
        }
    };

    /**
     * Copy to Clipboard
     */
    JJ_UI.CopyToClipboard = function(text, button) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function() {
                JJ_UI.Toast.success('Copied to clipboard!');
                if (button) {
                    const $btn = $(button);
                    const originalText = $btn.text();
                    $btn.text('Copied!');
                    setTimeout(function() {
                        $btn.text(originalText);
                    }, 2000);
                }
            }).catch(function() {
                JJ_UI.Toast.error('Failed to copy');
            });
        } else {
            // Fallback for older browsers
            const $temp = $('<textarea>').val(text).appendTo('body').select();
            document.execCommand('copy');
            $temp.remove();
            JJ_UI.Toast.success('Copied to clipboard!');
        }
    };

    /**
     * Debounce Function
     */
    JJ_UI.Debounce = function(func, wait) {
        let timeout;
        return function executedFunction() {
            const context = this;
            const args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                func.apply(context, args);
            }, wait);
        };
    };

    /**
     * Initialize on Document Ready
     */
    $(document).ready(function() {
        // Auto-init tabs
        $('.jj-tabs').each(function() {
            JJ_UI.Tabs.init(this);
        });

        // Auto-init tooltips (if needed in future)
        // JJ_UI.Tooltip.init();
    });

})(jQuery);
