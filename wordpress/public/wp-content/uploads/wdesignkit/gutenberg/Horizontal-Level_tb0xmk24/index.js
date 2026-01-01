class MyClass_xop6fc25 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                var $this = this;
        setTimeout(() => {
            let main_html = jQuery('.wkit-wb-Widget_tb0xmk24');

            jQuery.each(main_html, function (idx, scope) {
                $this.main_function_xop6fc25(jQuery(scope));
            });
        }, 800);
            });
        }

        main_function_xop6fc25($scope) {
        let is_editable = wp?.blocks ? true : false;
            let hzMain = $scope.find('.wkit-multi-step-hz-wrapper');
let hzStep = hzMain[0].querySelectorAll('.wkit-hz-pix-levels-step');

hzStep.forEach(function(el) {
    if (!el.classList.contains('wkit-hz-active')) {
        el.classList.add('wkit-hz-active');
    }else{
        el.classList.remove('wkit-hz-active');
        el.classList.add('wkit-hz-complete');
    }
});

var hzNumber = hzMain[0].querySelectorAll('.wkit-hz-inner-style-2 .wkit-prg-two');

hzNumber.forEach(function(element) {
    if (!element.hasAttribute('data-updated')) {
        var dataIndex = parseInt(element.getAttribute('data-index')) || 0;
        element.setAttribute('data-index', dataIndex + 1);
        element.setAttribute('data-updated', 'true');
    }
});

var hzCheckIcon = hzMain[0].querySelectorAll('.wkit-hz-inner-style-3 .wkit-hz-active-yes .wkit-hz-pix-levels-dot-inner');
hzCheckIcon.forEach(function(ele) {
    ele.classList.add('wkit-hz-check-mark');
});
 
        }
    }

    new MyClass_xop6fc25();