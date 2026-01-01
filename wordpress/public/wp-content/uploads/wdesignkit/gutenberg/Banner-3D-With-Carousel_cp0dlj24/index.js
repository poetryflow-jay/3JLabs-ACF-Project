class MyClass_3g2en325 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                var $this = this;
        setTimeout(() => {
            let main_html = jQuery('.wkit-wb-Widget_cp0dlj24');

            jQuery.each(main_html, function (idx, scope) {
                $this.main_function_3g2en325(jQuery(scope));
            });
        }, 800);
            });
        }

        main_function_3g2en325($scope) {
        let is_editable = wp?.blocks ? true : false;
            VanillaTilt.init($scope[0].querySelectorAll(".banner-3d-inner"));
let splidea = $scope[0].querySelector(".wkit-banner-3d-with-carousel");
 var unid = Math.random().toString(32).slice(2);
splidea.setAttribute("id",'wkit'+unid);
$scope[0].style.width = '100%';

new Splide( '#wkit'+unid,  {
  type   : 'loop',
  pagination : false,
  drag: false,
  arrows: true,
  perPage : 1
}).mount(); 
        }
    }

    new MyClass_3g2en325();