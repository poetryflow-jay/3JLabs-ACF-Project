class MyClass_pn2qqt25 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                setTimeout(() => {
            let main_html = document.querySelectorAll(".wkit-wb-Widget_2zud4k24")    
                main_html.forEach(element => {
                    this.main_function_pn2qqt25([element])
                });
        }, 800);
            });
        }

        main_function_pn2qqt25($scope) {
        let is_editable = wp?.blocks ? true : false;
            let cBGAll = $scope[0].querySelector('.wkit-cta-with-con-bg');
let gtAllImg = cBGAll.querySelectorAll('.bg-image');
gtAllImg.forEach((el,index)=>{
    if(index==0){
        el.classList.add('active');
    }
});

let gtAllItem = cBGAll.querySelectorAll('.con-image-item');
gtAllItem.forEach((el,index)=>{
    if(index==0){
        el.classList.add('active');
    }
    
    el.addEventListener('mouseenter', (e, index)=>{
        let getbg = e.currentTarget.closest('.cta-with-bg-inner');
        
        let actImg = getbg.querySelector('.bg-image.active');
        if(actImg){
            actImg.classList.remove('active');
        }
        let actItem = getbg.querySelector('.con-image-item.active');
        if(actItem){
            actItem.classList.remove('active');
        }
        
        e.currentTarget.classList.add('active');
        
        let gtIndex = e.currentTarget.getAttribute('data-index');
        
        let newImg = getbg.querySelector('img[data-index="'+gtIndex+'"]');
        newImg.classList.add('active');
        
        console.log(newImg);
    });
    
});


 
        }
    }

    new MyClass_pn2qqt25();