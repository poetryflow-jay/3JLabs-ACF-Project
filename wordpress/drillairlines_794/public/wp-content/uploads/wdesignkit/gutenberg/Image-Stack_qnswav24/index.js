class MyClass_7s4lw125 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                setTimeout(() => {
            let main_html = document.querySelectorAll(".wkit-wb-Widget_qnswav24")    
                main_html.forEach(element => {
                    this.main_function_7s4lw125([element])
                });
        }, 800);
            });
        }

        main_function_7s4lw125($scope) {
        let is_editable = wp?.blocks ? true : false;
            let getimage = $scope[0].querySelector(".wkit-image-stack");
let btn = getimage.querySelector('.wkit-img-stack-inner');
let links = getimage.querySelectorAll(".wkit-stack-item");

const isTablet = window.innerWidth >= 768 && window.innerWidth <= 1024;
const isMobile = window.innerWidth <= 767;

links.forEach(link => {
    link.addEventListener('click', (e) => {
        if (
            (isMobile && !btn.classList.contains('disable-mobile-link')) ||
            (isTablet && !btn.classList.contains('disable-tablet-link'))
        ) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
});
 
        }
    }

    new MyClass_7s4lw125();