class MyClass_y9rq3925 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                setTimeout(() => {
            let main_html = document.querySelectorAll(".wkit-wb-Widget_kxwujr25")    
                main_html.forEach(element => {
                    this.main_function_y9rq3925([element])
                });
        }, 800);
            });
        }

        main_function_y9rq3925($scope) {
        let is_editable = wp?.blocks ? true : false;
            let maskBtn = $scope[0].querySelector('.wdk-masked-ani-btn'); // fixed class name
let btn = maskBtn.querySelector('.wdk-mas');
let link = maskBtn.querySelector('.wdk-masked-btn');

const isTablet = window.innerWidth >= 768 && window.innerWidth <= 1024;
const isMobile = window.innerWidth <= 767;

link.addEventListener('click', (e) => {
    if (
        (isMobile && !btn.classList.contains('disable-mobile-link')) ||
        (isTablet && !btn.classList.contains('disable-tablet-link'))
    ) {
        e.preventDefault();
        e.stopPropagation();
    }
});
 
        }
    }

    new MyClass_y9rq3925();