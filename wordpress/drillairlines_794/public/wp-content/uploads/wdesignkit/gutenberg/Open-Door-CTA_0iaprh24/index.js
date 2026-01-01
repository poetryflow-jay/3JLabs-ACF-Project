class MyClass_ji2ye525 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                var $this = this;
        setTimeout(() => {
            let main_html = jQuery('.wkit-wb-Widget_0iaprh24');

            jQuery.each(main_html, function (idx, scope) {
                $this.main_function_ji2ye525(jQuery(scope));
            });
        }, 800);
            });
        }

        main_function_ji2ye525($scope) {
        let is_editable = wp?.blocks ? true : false;
            let getCTA = $scope[0].querySelector('.wkit-open-door-cta');
let ctaAtag = getCTA.querySelector('.open-door-cta');
let linkContainer = getCTA.querySelector('.open-door-cta-inner');
const isTablet = window.innerWidth >= 768 && window.innerWidth <= 1024;
const isMobile = window.innerWidth <= 767;
ctaAtag.addEventListener('click', (e) => {
    if (
        (isMobile && !linkContainer.classList.contains('disable-mobile-link')) ||
        (isTablet && !linkContainer.classList.contains('disable-tablet-link'))
    ) {
        e.preventDefault();
        e.stopPropagation();
    }
});
ctaAtag.addEventListener('mouseenter', (e)=>{
    getCTA.classList.add('active');
})
ctaAtag.addEventListener('mouseleave', (e)=>{
    getCTA.classList.remove('active');
})

 
        }
    }

    new MyClass_ji2ye525();