class MyClass_efpbny25 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                var $this = this;
        setTimeout(() => {
            let main_html = jQuery('.wkit-wb-Widget_7kq3uw24');

            jQuery.each(main_html, function (idx, scope) {
                $this.main_function_efpbny25(jQuery(scope));
            });
        }, 800);
            });
        }

        main_function_efpbny25($scope) {
        let is_editable = wp?.blocks ? true : false;
            let vlMain = $scope.find('.wkit-multi-step-vl-wrapper');
let vlStep = vlMain[0].querySelectorAll('.wkit-vl-pix-levels-step');

vlStep.forEach(function(el) {
    if (!el.classList.contains('wkit-vl-active')) {
        el.classList.add('wkit-vl-active');
    }else{
        el.classList.remove('wkit-vl-active');
        el.classList.add('wkit-vl-complete');
    }
});

let vlNumber = vlMain[0].querySelectorAll('.wkit-vl-inner-style-2 .wkit-prg-two');
vlNumber.forEach(function(element, index) {
    let inner = element.querySelector('.wkit-vl-pix-levels-dot-inner');

    // Remove existing number-only nodes
    element.childNodes.forEach(node => {
        if (node.nodeType === 3) element.removeChild(node); // remove text nodes
    });

    // Insert number before the inner span
    if (inner) {
        element.insertBefore(document.createTextNode(index + 1), inner);
    } else {
        element.textContent = index + 1;
    }
});

var vlCheckIcon = vlMain[0].querySelectorAll('.wkit-vl-inner-style-3 .wkit-vl-active-yes .wkit-vl-pix-levels-dot-inner');
vlCheckIcon.forEach(function(ele) {
    ele.classList.add('wkit-vl-check-mark');
});
 
        }
    }

    new MyClass_efpbny25();