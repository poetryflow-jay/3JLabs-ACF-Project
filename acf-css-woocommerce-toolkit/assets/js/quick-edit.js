/**
 * ACF CSS WooCommerce Toolkit - Quick Edit
 * 
 * 상품 목록 빠른 편집 JavaScript
 *
 * @package ACF_CSS_WooCommerce_Toolkit
 * @version 1.0.0
 */

(function($) {
    'use strict';

    // WooCommerce 빠른 편집 확장
    const ACF_WC_QuickEdit = {
        
        /**
         * 초기화
         */
        init: function() {
            // WooCommerce 빠른 편집이 열릴 때
            $(document).on('click', '.editinline', this.onQuickEditOpen);
        },

        /**
         * 빠른 편집 열릴 때
         */
        onQuickEditOpen: function() {
            const $row = $(this).closest('tr');
            const postId = $row.attr('id').replace('post-', '');
            
            // 할부 개월 수 데이터 가져오기
            const installmentMonths = $row.find('.acf-wc-installment-months-data').data('months') || 0;
            
            // inlineEditPost가 로드된 후 실행
            setTimeout(function() {
                const $editRow = $('#edit-' + postId);
                
                // 할부 개월 수 선택
                $editRow.find('.acf-wc-installment-months').val(installmentMonths);
            }, 50);
        }
    };

    // DOM 준비 시 초기화
    $(document).ready(function() {
        ACF_WC_QuickEdit.init();
    });

})(jQuery);
