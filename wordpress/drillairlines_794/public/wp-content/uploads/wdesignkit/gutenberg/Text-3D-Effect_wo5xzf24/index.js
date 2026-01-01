class MyClass_wikj2o25 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                var $this = this;
        setTimeout(() => {
            let main_html = jQuery('.wkit-wb-Widget_wo5xzf24');

            jQuery.each(main_html, function (idx, scope) {
                $this.main_function_wikj2o25(jQuery(scope));
            });
        }, 800);
            });
        }

        main_function_wikj2o25($scope) {
        let is_editable = wp?.blocks ? true : false;
            let getStage = $scope[0].querySelector('.wkit-3d-stage');
let getDuration = getStage.getAttribute('data-duration');
if(getDuration){
    let getLayers = getStage.querySelectorAll('.w-3d-layer');
    getLayers.forEach((el)=>{
        el.style.animationDuration = getDuration+'s';
    })
} 
        }
    }

    new MyClass_wikj2o25();