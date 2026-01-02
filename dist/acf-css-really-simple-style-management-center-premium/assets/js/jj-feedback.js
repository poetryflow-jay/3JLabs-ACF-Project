/**
 * JJ Feedback System JS
 * Phase 8.4
 */
(function($) {
    'use strict';

    const Feedback = {
        init: function() {
            this.bindEvents();
            this.injectStyles();
        },

        injectStyles: function() {
            if ($('#jj-feedback-styles').length) return;

            const css = `
                #jj-feedback-trigger {
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    z-index: 9999;
                    border-radius: 50%;
                    width: 50px;
                    height: 50px;
                    padding: 0;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                #jj-feedback-trigger .dashicons {
                    font-size: 24px;
                    width: 24px;
                    height: 24px;
                    margin: 0;
                }
                
                #jj-feedback-modal {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    z-index: 100000;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .jj-feedback-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.5);
                }
                .jj-feedback-content {
                    position: relative;
                    background: #fff;
                    width: 400px;
                    max-width: 90%;
                    border-radius: 8px;
                    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
                    overflow: hidden;
                    animation: jj-feedback-pop 0.3s ease-out;
                }
                @keyframes jj-feedback-pop {
                    from { transform: scale(0.9); opacity: 0; }
                    to { transform: scale(1); opacity: 1; }
                }
                .jj-feedback-header {
                    padding: 15px 20px;
                    border-bottom: 1px solid #eee;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    background: #f9f9f9;
                }
                .jj-feedback-header h3 { margin: 0; font-size: 16px; }
                .jj-feedback-close {
                    background: none;
                    border: none;
                    font-size: 20px;
                    cursor: pointer;
                    color: #666;
                }
                .jj-feedback-body {
                    padding: 20px;
                }
                .jj-feedback-intro { margin-top: 0; color: #666; }
                
                .jj-feedback-rating {
                    margin: 15px 0;
                    text-align: center;
                }
                .jj-feedback-rating .dashicons {
                    font-size: 32px;
                    width: 32px;
                    height: 32px;
                    color: #ddd;
                    cursor: pointer;
                    transition: color 0.2s;
                }
                .jj-feedback-rating .dashicons.active,
                .jj-feedback-rating .dashicons:hover,
                .jj-feedback-rating .dashicons:hover ~ .dashicons {
                    /* Hover logic is tricky with CSS only for "previous siblings", usually handled by JS or flex-direction: row-reverse */
                }
                .jj-feedback-rating .dashicons.filled {
                    color: #f0b849;
                }
                
                .jj-feedback-type {
                    margin-bottom: 15px;
                    display: flex;
                    gap: 15px;
                    font-size: 13px;
                }
                
                #jj-feedback-message {
                    width: 100%;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    padding: 10px;
                    font-size: 14px;
                }
                
                .jj-feedback-footer {
                    padding: 15px 20px;
                    background: #f9f9f9;
                    border-top: 1px solid #eee;
                    text-align: right;
                }
            `;
            $('head').append('<style id="jj-feedback-styles">' + css + '</style>');
        },

        bindEvents: function() {
            const self = this;

            // Trigger click
            $(document).on('click', '#jj-feedback-trigger', function() {
                $('#jj-feedback-modal').fadeIn(200);
            });

            // Close click
            $(document).on('click', '.jj-feedback-close, .jj-feedback-overlay', function() {
                $('#jj-feedback-modal').fadeOut(200);
            });

            // Rating star click
            $(document).on('click', '.jj-feedback-rating .dashicons', function() {
                const val = $(this).data('value');
                $('#jj-feedback-rating-value').val(val);
                
                // Update visuals
                $('.jj-feedback-rating .dashicons').each(function() {
                    if ($(this).data('value') <= val) {
                        $(this).removeClass('dashicons-star-empty').addClass('dashicons-star-filled filled');
                    } else {
                        $(this).removeClass('dashicons-star-filled filled').addClass('dashicons-star-empty');
                    }
                });

                // Enable submit if message is not empty (or optional)
                self.checkSubmitButton();
            });
            
            // Hover effect for stars
            $(document).on('mouseenter', '.jj-feedback-rating .dashicons', function() {
                const val = $(this).data('value');
                $('.jj-feedback-rating .dashicons').each(function() {
                    if ($(this).data('value') <= val) {
                        $(this).removeClass('dashicons-star-empty').addClass('dashicons-star-filled');
                    } else {
                        $(this).removeClass('dashicons-star-filled').addClass('dashicons-star-empty');
                    }
                });
            }).on('mouseleave', '.jj-feedback-rating', function() {
                const currentVal = $('#jj-feedback-rating-value').val();
                $('.jj-feedback-rating .dashicons').each(function() {
                    if ($(this).data('value') <= currentVal) {
                        $(this).removeClass('dashicons-star-empty').addClass('dashicons-star-filled filled');
                    } else {
                        $(this).removeClass('filled').addClass('dashicons-star-empty').removeClass('dashicons-star-filled');
                    }
                });
            });

            // Textarea input
            $(document).on('input', '#jj-feedback-message', function() {
                self.checkSubmitButton();
            });

            // Submit
            $(document).on('click', '.jj-feedback-submit', function() {
                self.submitFeedback();
            });
        },

        checkSubmitButton: function() {
            const rating = $('#jj-feedback-rating-value').val();
            // const message = $('#jj-feedback-message').val().trim();
            // 평점만 있어도 전송 가능하도록
            if (rating > 0) {
                $('.jj-feedback-submit').prop('disabled', false);
            } else {
                $('.jj-feedback-submit').prop('disabled', true);
            }
        },

        submitFeedback: function() {
            const $btn = $('.jj-feedback-submit');
            $btn.prop('disabled', true).text('전송 중...');

            const data = {
                action: 'jj_submit_feedback',
                security: jjFeedback.nonce,
                rating: $('#jj-feedback-rating-value').val(),
                message: $('#jj-feedback-message').val(),
                type: $('input[name="jj_feedback_type"]:checked').val(),
                include_system_info: $('#jj-feedback-include-system').is(':checked')
            };

            $.post(jjFeedback.ajax_url, data, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    $('#jj-feedback-modal').fadeOut();
                    // Reset form
                    $('#jj-feedback-rating-value').val(0);
                    $('#jj-feedback-message').val('');
                    $('#jj-feedback-include-system').prop('checked', false);
                    $('.jj-feedback-rating .dashicons').removeClass('dashicons-star-filled filled').addClass('dashicons-star-empty');
                } else {
                    alert(response.data.message || jjFeedback.strings.error);
                }
            }).fail(function() {
                alert(jjFeedback.strings.error);
            }).always(function() {
                $btn.prop('disabled', false).text('보내기');
            });
        }
    };

    $(document).ready(function() {
        Feedback.init();
    });

})(jQuery);
