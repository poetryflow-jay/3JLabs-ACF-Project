class MyClass_5e2vh225 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                setTimeout(() => {
            let main_html = document.querySelectorAll(".wkit-wb-Widget_b5bsl824")    
                main_html.forEach(element => {
                    this.main_function_5e2vh225([element])
                });
        }, 800);
            });
        }

        main_function_5e2vh225($scope) {
        let is_editable = wp?.blocks ? true : false;
            let accordSlide = $scope[0].querySelectorAll('.wkit-scroll-accord-slide');

let totalHgt = window.innerHeight;

let getHgt = Number( totalHgt * 7) / 100;

let totalLength = Number(accordSlide.length - 1);

accordSlide.forEach((accordion, index) => {
    let getItemStyle = window.getComputedStyle(accordion);
    let getPadding = parseInt(getItemStyle.paddingTop);
    
    let accordTitle = accordion.querySelector('.accord-title');
    let accordTitleMain = accordion.querySelector('.wkit-scroll-accord-title');
    let halfTitleHeight = Number(accordTitle.offsetHeight);

    let ttlHgt = Number(accordTitle.offsetHeight) / 2; 
  
    let transY = (index - totalLength) * getHgt;
        transY = transY - ttlHgt * (accordSlide.length - index);
      
   accordion.style.transform = 'translateY(' + transY + 'px)';
   accordion.style.marginBottom = -accordTitleMain.offsetHeight+'px';
  
   let img = accordion.querySelector('.wkit-scroll-accord-img');
   let imgCls = accordion.querySelector('.wkit-scroll-accord-img .accord-img');
  
    if (!imgCls.getAttribute('src') || imgCls.getAttribute('src') === '') {
        img.style.display = 'none';
    }
});
 
        }
    }

    new MyClass_5e2vh225();