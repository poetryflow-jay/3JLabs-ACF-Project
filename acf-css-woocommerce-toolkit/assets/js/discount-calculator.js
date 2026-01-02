/**
 * ACF CSS WooCommerce Toolkit - Discount Calculator
 * 
 * 상품 편집 화면의 할인 계산기 JavaScript
 *
 * @package ACF_CSS_WooCommerce_Toolkit
 * @version 1.0.0
 */

(function($) {
    'use strict';

    const ACF_WC_Calculator = {
        
        /**
         * 초기화
         */
        init: function() {
            this.bindEvents();
        },

        /**
         * 이벤트 바인딩
         */
        bindEvents: function() {
            // 퍼센트 할인 적용
            $(document).on('click', '.acf-wc-apply-percent-discount', this.applyPercentDiscount);
            
            // 금액 할인 적용
            $(document).on('click', '.acf-wc-apply-amount-discount', this.applyAmountDiscount);
            
            // Enter 키 지원
            $(document).on('keypress', '#acf_wc_discount_percent', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    ACF_WC_Calculator.applyPercentDiscount();
                }
            });
            
            $(document).on('keypress', '#acf_wc_discount_amount', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    ACF_WC_Calculator.applyAmountDiscount();
                }
            });
        },

        /**
         * 퍼센트 할인 적용
         */
        applyPercentDiscount: function() {
            const regular = parseFloat($('#_regular_price').val());
            const percent = parseFloat($('#acf_wc_discount_percent').val());
            
            if (!regular || isNaN(regular)) {
                ACF_WC_Calculator.showError('정가를 먼저 입력해주세요.');
                return;
            }
            
            if (!percent || isNaN(percent) || percent <= 0 || percent >= 100) {
                ACF_WC_Calculator.showError('유효한 할인율을 입력해주세요 (1-99%).');
                return;
            }
            
            const discountAmount = regular * (percent / 100);
            const salePrice = Math.round(regular - discountAmount);
            
            // 할인가 필드 업데이트
            $('#_sale_price').val(salePrice).trigger('change');
            
            // 미리보기 업데이트
            ACF_WC_Calculator.showPreview({
                type: 'percent',
                regular: regular,
                sale: salePrice,
                discount: discountAmount,
                percent: percent
            });
        },

        /**
         * 금액 할인 적용
         */
        applyAmountDiscount: function() {
            const regular = parseFloat($('#_regular_price').val());
            const discount = parseFloat($('#acf_wc_discount_amount').val());
            
            if (!regular || isNaN(regular)) {
                ACF_WC_Calculator.showError('정가를 먼저 입력해주세요.');
                return;
            }
            
            if (!discount || isNaN(discount) || discount <= 0 || discount >= regular) {
                ACF_WC_Calculator.showError('유효한 할인 금액을 입력해주세요.');
                return;
            }
            
            const salePrice = Math.round(regular - discount);
            const percent = (discount / regular) * 100;
            
            // 할인가 필드 업데이트
            $('#_sale_price').val(salePrice).trigger('change');
            
            // 미리보기 업데이트
            ACF_WC_Calculator.showPreview({
                type: 'amount',
                regular: regular,
                sale: salePrice,
                discount: discount,
                percent: percent
            });
        },

        /**
         * 미리보기 표시
         */
        showPreview: function(data) {
            const i18n = window.acfCssWcAdmin?.i18n || {};
            const currency = window.acfCssWcAdmin?.currency_symbol || '₩';
            
            let html = '';
            
            if (data.type === 'percent') {
                html = `${i18n.regular_price || '정가'} ${data.regular.toLocaleString()}${currency}에서 ` +
                       `${data.percent}% ${i18n.discount || '할인'} → ` +
                       `<strong style="color: #e74c3c;">${data.sale.toLocaleString()}${currency}</strong> ` +
                       `(${i18n.saved || '절약'}: ${data.discount.toLocaleString()}${currency})`;
            } else {
                html = `${i18n.regular_price || '정가'} ${data.regular.toLocaleString()}${currency}에서 ` +
                       `${data.discount.toLocaleString()}${currency} ${i18n.discount || '할인'} → ` +
                       `<strong style="color: #e74c3c;">${data.sale.toLocaleString()}${currency}</strong> ` +
                       `(${data.percent.toFixed(1)}% ${i18n.discount || '할인'})`;
            }
            
            // 할부 정보 추가
            const months = parseInt($('#_installment_months').val());
            if (months > 1) {
                const monthly = Math.round(data.sale / months);
                html += `<br>${i18n.monthly || '월'} ${monthly.toLocaleString()}${currency} × ${months}${i18n.months || '개월'}`;
            }
            
            $('#acf-wc-discount-preview').show();
            $('#acf-wc-preview-text').html(html);
        },

        /**
         * 에러 표시
         */
        showError: function(message) {
            $('#acf-wc-discount-preview').show();
            $('#acf-wc-preview-text').html(`<span style="color: #e74c3c;">⚠️ ${message}</span>`);
        }
    };

    // DOM 준비 시 초기화
    $(document).ready(function() {
        ACF_WC_Calculator.init();
    });

})(jQuery);
