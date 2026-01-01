class MyClass_8dl36325 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                setTimeout(() => {
            let main_html = document.querySelectorAll(".wkit-wb-Widget_ko2nhe24")    
                main_html.forEach(element => {
                    this.main_function_8dl36325([element])
                });
        }, 800);
            });
        }

        main_function_8dl36325($scope) {
        let is_editable = wp?.blocks ? true : false;
            let mainCard = $scope[0].querySelector('.wkit-card-hvr-main-interact');
let getContent = mainCard.querySelector('.wkit-card-hvr-inn-content');
let getTitle = mainCard.querySelector('.wkit-card-hvr-title');

let titleHeight = getTitle.offsetHeight;
getContent.style.transform = 'translateY(calc(100% - ' + titleHeight + 'px))';


mainCard.addEventListener('mouseenter', function() {
    getContent.style.transform = 'translateY(0)';
});

mainCard.addEventListener('mouseleave', function() {
    getContent.style.transform = 'translateY(calc(100% - ' + titleHeight + 'px))';
});
 
        }
    }

    new MyClass_8dl36325();