class MyClass_eljfsa25 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                setTimeout(() => {
            let main_html = document.querySelectorAll(".wkit-wb-Widget_zbf0wm24")    
                main_html.forEach(element => {
                    this.main_function_eljfsa25([element])
                });
        }, 800);
            });
        }

        main_function_eljfsa25($scope) {
        let is_editable = wp?.blocks ? true : false;
            let mainWrap = $scope[0].querySelector('.wkit-marquee-main-wrap');
let getSpeed = (mainWrap.getAttribute('data-speed')) ? mainWrap.getAttribute('data-speed') : 20;
let grpInner = mainWrap.querySelector('.wkit-marquee-inn-wrap');

grpInner.style.animationDuration = getSpeed + 's'; 
        }
    }

    new MyClass_eljfsa25();