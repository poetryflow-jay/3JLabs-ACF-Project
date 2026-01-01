class MyClass_t1ix6125 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                setTimeout(() => {
            let main_html = document.querySelectorAll(".wkit-wb-Widget_sj5wmm24")    
                main_html.forEach(element => {
                    this.main_function_t1ix6125([element])
                });
        }, 800);
            });
        }

        main_function_t1ix6125($scope) {
        let is_editable = wp?.blocks ? true : false;
            let ctaimg = $scope[0].querySelectorAll(".wkit-cta-image-box");

ctaimg.forEach((e) => {
    e.parentElement.style.width = '100%';
}); 
        }
    }

    new MyClass_t1ix6125();