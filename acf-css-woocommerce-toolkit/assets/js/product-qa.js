/**
 * ACF CSS WooCommerce Toolkit - Product Q&A System
 *
 * 상품 Q&A 프론트엔드 JavaScript
 *
 * @package ACF_CSS_WooCommerce_Toolkit
 */

(function($) {
    'use strict';

    /**
     * Product Q&A 클래스
     */
    class ACFWCProductQA {
        constructor() {
            this.$form = $('#acf-wc-qa-form');
            this.$list = $('.acf-wc-qa-list');
            this.config = window.acfWcQA || {};
            
            this.init();
        }

        init() {
            this.bindEvents();
        }

        /**
         * 이벤트 바인딩
         */
        bindEvents() {
            const self = this;

            // 질문 제출
            this.$form.on('submit', function(e) {
                e.preventDefault();
                self.submitQuestion();
            });

            // 도움이 됨 버튼
            this.$list.on('click', '.qa-helpful-btn', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const qaId = $btn.data('qa-id');
                self.markHelpful(qaId, $btn);
            });

            // 더 보기 (AJAX 페이지네이션)
            this.$list.on('click', '.acf-wc-qa-load-more', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                self.loadMore(page);
            });
        }

        /**
         * 질문 제출
         */
        submitQuestion() {
            const self = this;
            const $submitBtn = this.$form.find('button[type="submit"]');
            const originalText = $submitBtn.text();

            // 버튼 비활성화
            $submitBtn.prop('disabled', true).text(this.config.i18n?.submitting || '등록 중...');

            // 기존 메시지 제거
            this.$form.find('.acf-wc-qa-message').remove();

            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: this.$form.serialize() + '&action=acf_wc_submit_question',
                success: function(response) {
                    if (response.success) {
                        // 성공 메시지
                        self.showMessage('success', self.config.i18n?.success || '질문이 등록되었습니다.');
                        
                        // 폼 초기화
                        self.$form[0].reset();
                    } else {
                        // 에러 메시지
                        self.showMessage('error', response.data || self.config.i18n?.error);
                    }
                },
                error: function() {
                    self.showMessage('error', self.config.i18n?.error || '오류가 발생했습니다.');
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).text(originalText);
                }
            });
        }

        /**
         * 도움이 됨 표시
         */
        markHelpful(qaId, $btn) {
            const self = this;

            if ($btn.hasClass('clicked')) {
                return; // 이미 클릭됨
            }

            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'acf_wc_qa_helpful',
                    question_id: qaId,
                    nonce: this.config.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // 카운트 업데이트
                        $btn.find('.helpful-count').text('(' + response.data.count + ')');
                        $btn.addClass('clicked');
                        
                        // 피드백
                        self.showToast(self.config.i18n?.thanks || '감사합니다!');
                    } else {
                        self.showToast(response.data || '이미 평가하셨습니다.');
                    }
                }
            });
        }

        /**
         * 더 보기 (페이지네이션)
         */
        loadMore(page) {
            const self = this;
            const $loadMoreBtn = this.$list.find('.acf-wc-qa-load-more');
            const productId = this.$form.find('input[name="product_id"]').val();

            $loadMoreBtn.prop('disabled', true).text('로딩 중...');

            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'acf_wc_load_more_qa',
                    product_id: productId,
                    page: page,
                    nonce: this.config.nonce
                },
                success: function(response) {
                    if (response.success && response.data.html) {
                        // 목록에 추가
                        $loadMoreBtn.before(response.data.html);
                        
                        // 다음 페이지 업데이트
                        if (response.data.has_more) {
                            $loadMoreBtn.data('page', page + 1).prop('disabled', false).text('더 보기');
                        } else {
                            $loadMoreBtn.remove();
                        }
                    }
                },
                error: function() {
                    $loadMoreBtn.prop('disabled', false).text('더 보기');
                }
            });
        }

        /**
         * 메시지 표시
         */
        showMessage(type, message) {
            const html = `
                <div class="acf-wc-qa-message ${type}">
                    ${this.escapeHtml(message)}
                </div>
            `;

            this.$form.prepend(html);

            // 5초 후 자동 숨김
            setTimeout(() => {
                this.$form.find('.acf-wc-qa-message').fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        }

        /**
         * 토스트 메시지
         */
        showToast(message) {
            const $toast = $(`
                <div class="acf-wc-qa-toast">
                    ${this.escapeHtml(message)}
                </div>
            `);

            $('body').append($toast);

            setTimeout(() => {
                $toast.addClass('show');
            }, 10);

            setTimeout(() => {
                $toast.removeClass('show');
                setTimeout(() => $toast.remove(), 300);
            }, 2000);
        }

        /**
         * HTML 이스케이프
         */
        escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }
    }

    /**
     * DOM Ready
     */
    $(document).ready(function() {
        if ($('#acf-wc-qa-form').length) {
            window.acfWcProductQA = new ACFWCProductQA();
        }
    });

    // 토스트 스타일 추가
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .acf-wc-qa-toast {
                position: fixed;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%) translateY(100px);
                background: #333;
                color: #fff;
                padding: 12px 24px;
                border-radius: 8px;
                font-size: 14px;
                z-index: 99999;
                opacity: 0;
                transition: all 0.3s ease;
            }
            .acf-wc-qa-toast.show {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        `)
        .appendTo('head');

})(jQuery);
