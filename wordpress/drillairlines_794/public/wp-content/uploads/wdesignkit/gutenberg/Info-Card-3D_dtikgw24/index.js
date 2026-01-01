class MyClass_78exnc25 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                var $this = this;
        setTimeout(() => {
            let main_html = jQuery('.wkit-wb-Widget_dtikgw24');

            jQuery.each(main_html, function (idx, scope) {
                $this.main_function_78exnc25(jQuery(scope));
            });
        }, 800);
            });
        }

        main_function_78exnc25($scope) {
        let is_editable = wp?.blocks ? true : false;
            let infoCard = $scope[0].querySelector('.wkit-hover-3d-card-main');
let cardItem = infoCard.querySelector('.wkit-hover-3d-card-item');
let link = cardItem.querySelector('.info-link');
const isTablet = window.innerWidth >= 768 && window.innerWidth <= 1024;
const isMobile = window.innerWidth <= 767;

link.addEventListener('click', (e) => {
    if (
        (isMobile && !cardItem.classList.contains('disable-mobile-link')) ||
        (isTablet && !cardItem.classList.contains('disable-tablet-link'))
    ) {
        e.preventDefault();
        e.stopPropagation();
    }
}); 
        }
    }

    new MyClass_78exnc25();