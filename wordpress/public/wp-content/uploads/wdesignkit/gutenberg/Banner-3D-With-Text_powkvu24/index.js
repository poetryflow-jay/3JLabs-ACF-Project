class MyClass_7qkk0525 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                var $this = this;
        setTimeout(() => {
            let main_html = jQuery('.wkit-wb-Widget_powkvu24');

            jQuery.each(main_html, function (idx, scope) {
                $this.main_function_7qkk0525(jQuery(scope));
            });
        }, 800);
            });
        }

        main_function_7qkk0525($scope) {
        let is_editable = wp?.blocks ? true : false;
             VanillaTilt.init($scope[0].querySelectorAll(".banner-3d-inner"));
 
        }
    }

    new MyClass_7qkk0525();