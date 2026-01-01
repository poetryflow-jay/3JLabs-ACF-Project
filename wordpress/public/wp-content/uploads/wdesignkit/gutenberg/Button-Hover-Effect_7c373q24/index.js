class MyClass_ijvq9p25 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                setTimeout(() => {
            let main_html = document.querySelectorAll(".wkit-wb-Widget_7c373q24")    
                main_html.forEach(element => {
                    this.main_function_ijvq9p25([element])
                });
        }, 800);
            });
        }

        main_function_ijvq9p25($scope) {
        let is_editable = wp?.blocks ? true : false;
            $scope[0].querySelectorAll('.wkit-button-effect .btn-hover-txt').forEach(button => {
    button.innerHTML = '<div><span>' + button.textContent.trim().split('').join('</span><span>') + '</span></div>';
});

// Define device check functions
const isMobile = window.innerWidth <= 767;
const isTablet = window.innerWidth > 767 && window.innerWidth <= 1024;

$scope[0].querySelectorAll('.wkit-button-effect').forEach(buttonWrap => {
    buttonWrap.addEventListener('click', (e) => {
        const parent = buttonWrap.closest('.wkit-button-h-effect');

        

        const disableForMobile = isMobile && !parent.classList.contains('disable-mobile-link');
        const disableForTablet = isTablet && !parent.classList.contains('disable-tablet-link');

        if (disableForMobile || disableForTablet) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
});
 
        }
    }

    new MyClass_ijvq9p25();