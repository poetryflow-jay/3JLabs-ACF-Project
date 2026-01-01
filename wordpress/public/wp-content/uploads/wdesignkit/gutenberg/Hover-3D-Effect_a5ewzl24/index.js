class MyClass_7r2gys25 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                var $this = this;
        setTimeout(() => {
            let main_html = jQuery('.wkit-wb-Widget_a5ewzl24');

            jQuery.each(main_html, function (idx, scope) {
                $this.main_function_7r2gys25(jQuery(scope));
            });
        }, 800);
            });
        }

        main_function_7r2gys25($scope) {
        let is_editable = wp?.blocks ? true : false;
            let selectimg = $scope[0].querySelector(".wkit-img-box.style-1");
let hover3dEffect = $scope[0].querySelector(".wkit-imghover-wrapper");

if (selectimg && typeof VanillaTilt !== "undefined") {
    let max = Number(selectimg.getAttribute('data-max')) || 10,
        speed = Number(selectimg.getAttribute('data-speed')) || 10,
        perspective = Number(selectimg.getAttribute('data-perspective')) || 10,
        scale = Number(selectimg.getAttribute('data-scale')) || 1;

    VanillaTilt.init(selectimg, {
        max: max,
        speed: speed,
        perspective: perspective,
        scale: scale,
        reset: true,
    });
}

if (hover3dEffect) {
    let link = hover3dEffect.querySelector(".hover-link");
    const isTablet = window.innerWidth >= 768 && window.innerWidth <= 1024;
    const isMobile = window.innerWidth <= 767;

    if (link) {
        link.addEventListener('click', (e) => {
            if (
                (isMobile && !hover3dEffect.classList.contains('disable-mobile-link')) ||
                (isTablet && !hover3dEffect.classList.contains('disable-tablet-link'))
            ) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    }
}
 
        }
    }

    new MyClass_7r2gys25();