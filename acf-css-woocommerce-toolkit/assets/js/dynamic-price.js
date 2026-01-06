/**
 * ACF WooCommerce Toolkit - Dynamic Price JavaScript
 *
 * Phase 49-4: 동적 가격 표시 기능
 *
 * @package ACF_CSS_WooCommerce_Toolkit
 * @version 2.5.0
 */

(function($) {
    'use strict';

    const ACFDynamicPrice = {
        config: {
            ajaxUrl: acfDynamicPrice.ajaxUrl || ajaxurl,
            nonce: acfDynamicPrice.nonce || '',
            currency: acfDynamicPrice.currency || '$',
            decimals: acfDynamicPrice.decimals || 2,
            decimalSep: acfDynamicPrice.decimalSep || '.',
            thousandSep: acfDynamicPrice.thousandSep || ',',
            priceFormat: acfDynamicPrice.priceFormat || '%1$s%2$s',
            animatePrices: acfDynamicPrice.animatePrices !== false,
            i18n: acfDynamicPrice.i18n || {}
        },

        countdownTimers: {},

        /**
         * 초기화
         */
        init: function() {
            this.initCountdowns();
            this.bindQuantityChanges();
            this.bindBulkPricingHover();
            this.initPriceObserver();
        },

        /**
         * 카운트다운 타이머 초기화
         */
        initCountdowns: function() {
            const self = this;

            $('.acf-sale-countdown').each(function() {
                const $countdown = $(this);
                const endTime = parseInt($countdown.data('end'), 10);

                if (endTime > 0) {
                    self.startCountdown($countdown, endTime);
                }
            });
        },

        /**
         * 카운트다운 시작
         */
        startCountdown: function($element, endTime) {
            const self = this;
            const timerId = setInterval(function() {
                const now = Math.floor(Date.now() / 1000);
                const diff = endTime - now;

                if (diff <= 0) {
                    clearInterval(timerId);
                    self.onCountdownEnd($element);
                    return;
                }

                const days = Math.floor(diff / 86400);
                const hours = Math.floor((diff % 86400) / 3600);
                const minutes = Math.floor((diff % 3600) / 60);
                const seconds = diff % 60;

                $element.find('.countdown-days').text(self.padZero(days));
                $element.find('.countdown-hours').text(self.padZero(hours));
                $element.find('.countdown-minutes').text(self.padZero(minutes));
                $element.find('.countdown-seconds').text(self.padZero(seconds));

                // 마지막 1시간: 긴급 스타일 추가
                if (diff < 3600) {
                    $element.addClass('urgent');
                }
            }, 1000);

            // 타이머 ID 저장
            const productId = $element.closest('.acf-dynamic-price-info').data('product-id');
            if (productId) {
                this.countdownTimers[productId] = timerId;
            }
        },

        /**
         * 카운트다운 종료 처리
         */
        onCountdownEnd: function($element) {
            $element.find('.countdown-label').text(this.config.i18n.saleEnded || '할인이 종료되었습니다');
            $element.find('.countdown-timer').hide();
            $element.addClass('ended');

            // 페이지 새로고침 (가격 업데이트)
            setTimeout(function() {
                location.reload();
            }, 3000);
        },

        /**
         * 수량 변경 바인딩
         */
        bindQuantityChanges: function() {
            const self = this;

            $(document).on('change', '.quantity input.qty', function() {
                const $input = $(this);
                const quantity = parseInt($input.val(), 10);
                const $form = $input.closest('form.cart, .woocommerce-cart-form');
                const productId = $form.find('input[name="product_id"], button[name="add-to-cart"]').val();

                if (productId && quantity > 0) {
                    self.updateDynamicPrice(productId, quantity);
                }
            });
        },

        /**
         * 동적 가격 업데이트
         */
        updateDynamicPrice: function(productId, quantity) {
            const self = this;
            const $priceWrapper = $('.acf-dynamic-price-info[data-product-id="' + productId + '"]');

            if ($priceWrapper.length === 0) {
                return;
            }

            // 로딩 상태
            $priceWrapper.addClass('loading');

            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'jj_get_dynamic_price',
                    nonce: this.config.nonce,
                    product_id: productId,
                    quantity: quantity
                },
                success: function(response) {
                    if (response.success) {
                        self.updatePriceDisplay(productId, response.data, quantity);
                    }
                },
                complete: function() {
                    $priceWrapper.removeClass('loading');
                }
            });
        },

        /**
         * 가격 표시 업데이트
         */
        updatePriceDisplay: function(productId, data, quantity) {
            const $priceElement = $('p.price, .woocommerce-Price-amount').first();

            if ($priceElement.length === 0) {
                return;
            }

            // 애니메이션
            if (this.config.animatePrices) {
                $priceElement.addClass('price-updating');

                setTimeout(function() {
                    $priceElement.html(data.formatted_total);
                    $priceElement.removeClass('price-updating').addClass('price-changed');

                    setTimeout(function() {
                        $priceElement.removeClass('price-changed');
                    }, 500);
                }, 300);
            } else {
                $priceElement.html(data.formatted_total);
            }

            // 절약 금액 표시
            if (data.savings > 0) {
                this.showSavingsMessage(data.savings);
            }
        },

        /**
         * 절약 금액 메시지 표시
         */
        showSavingsMessage: function(savings) {
            const formattedSavings = this.formatPrice(savings);
            const message = (this.config.i18n.youSave || '절약') + ': ' + formattedSavings;

            // 기존 메시지 제거
            $('.acf-savings-message').remove();

            // 새 메시지 추가
            const $message = $('<div class="acf-savings-message">' + message + '</div>');
            $('p.price').after($message);

            $message.hide().fadeIn(300);

            setTimeout(function() {
                $message.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        },

        /**
         * 대량 구매 가격표 호버 효과
         */
        bindBulkPricingHover: function() {
            $(document).on('mouseenter', '.bulk-pricing-table tbody tr', function() {
                $(this).addClass('highlight');
            }).on('mouseleave', '.bulk-pricing-table tbody tr', function() {
                $(this).removeClass('highlight');
            });

            // 행 클릭 시 수량 설정
            $(document).on('click', '.bulk-pricing-table tbody tr', function() {
                const minQty = $(this).find('td:first').text().match(/\d+/);

                if (minQty) {
                    const $qtyInput = $('input.qty');
                    if ($qtyInput.length) {
                        $qtyInput.val(parseInt(minQty[0], 10)).trigger('change');
                    }
                }
            });
        },

        /**
         * 가격 변경 관찰자 초기화
         */
        initPriceObserver: function() {
            const self = this;

            // Variation 가격 변경 감지
            $(document).on('found_variation', function(event, variation) {
                if (variation && variation.price_html) {
                    // 약간의 딜레이 후 카운트다운 재초기화
                    setTimeout(function() {
                        self.initCountdowns();
                    }, 100);
                }
            });
        },

        // ===== Utility Methods =====

        /**
         * 숫자 패딩
         */
        padZero: function(num) {
            return num < 10 ? '0' + num : num.toString();
        },

        /**
         * 가격 포맷팅
         */
        formatPrice: function(price) {
            const formatted = this.numberFormat(
                price,
                this.config.decimals,
                this.config.decimalSep,
                this.config.thousandSep
            );

            return this.config.priceFormat
                .replace('%1$s', this.config.currency)
                .replace('%2$s', formatted);
        },

        /**
         * 숫자 포맷팅
         */
        numberFormat: function(number, decimals, decimalSep, thousandSep) {
            number = parseFloat(number);
            if (isNaN(number)) {
                return '0';
            }

            const negative = number < 0;
            number = Math.abs(number);

            const parts = number.toFixed(decimals).split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandSep);

            return (negative ? '-' : '') + parts.join(decimalSep);
        }
    };

    // 추가 스타일 주입
    const styles = `
        <style>
            .acf-dynamic-price-info.loading {
                opacity: 0.6;
                pointer-events: none;
            }

            .acf-sale-countdown.urgent {
                animation: urgentPulse 1s ease-in-out infinite;
            }

            @keyframes urgentPulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.02); }
            }

            .acf-sale-countdown.ended {
                background: #f3f4f6;
                border-color: #d1d5db;
            }

            .acf-sale-countdown.ended .countdown-label {
                color: #6b7280;
            }

            .bulk-pricing-table tbody tr {
                cursor: pointer;
                transition: background 0.2s;
            }

            .bulk-pricing-table tbody tr.highlight {
                background: #f0f9ff;
            }

            .acf-savings-message {
                display: inline-block;
                background: #10b981;
                color: #fff;
                font-size: 13px;
                font-weight: 600;
                padding: 6px 12px;
                border-radius: 4px;
                margin-top: 8px;
            }
        </style>
    `;

    $('head').append(styles);

    // DOM Ready
    $(document).ready(function() {
        ACFDynamicPrice.init();
    });

    // 전역 접근용
    window.ACFDynamicPrice = ACFDynamicPrice;

})(jQuery);
