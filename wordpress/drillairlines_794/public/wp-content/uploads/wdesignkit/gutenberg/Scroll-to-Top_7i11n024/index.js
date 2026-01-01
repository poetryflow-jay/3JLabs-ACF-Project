class MyClass_vfv3q425 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                setTimeout(() => {
            let main_html = document.querySelectorAll(".wkit-wb-Widget_7i11n024")    
                main_html.forEach(element => {
                    this.main_function_vfv3q425([element])
                });
        }, 800);
            });
        }

        main_function_vfv3q425($scope) {
        let is_editable = wp?.blocks ? true : false;
            if($scope && $scope[0]){
    
    let button = $scope[0].querySelector('.wkit-scrolltotop');
    
    let getBack = document.querySelector('.interface-navigable-region.interface-interface-skeleton__content');
    console.log(getBack);
    
    let applyTo = (button.getAttribute("data-applyto")) ? button.getAttribute("data-applyto") : 'body' ;
    let cid = (button.getAttribute("data-cid")) ? button.getAttribute("data-cid") : '' ;
    
    
    let btnOffset = (button.getAttribute("data-desk")) ? Number(button.getAttribute("data-desk")) : 100 ;
    let btnOffsetTab = (button.getAttribute("data-tab")) ? Number(button.getAttribute("data-tab")) : btnOffset ;
    let btnOffsetMob = (button.getAttribute("data-mob")) ? Number(button.getAttribute("data-mob")) : btnOffsetTab ;
    
    let btnOffsetN = "", winScroll = '', conTopOff = 0, topOff = 0;
    
    let width = screen.width;
    if (width >= 1024) {
        btnOffsetN = btnOffset;
    } else if (width < 1024 && width >= 768) {
        btnOffsetN = btnOffsetTab;
    } else if (width < 768){
        btnOffsetN = btnOffsetMob;
    }
    if(applyTo == 'container'){
        winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        let getSelector = '';
        if(cid){
            getSelector = document.querySelector(cid);
        }else{
            getSelector = $scope[0].closest('.e-con-boxed, .e-con-full');
        }
        if(getSelector){
            conTopOff = getSelector.offsetTop;   
        }
        topOff = conTopOff;
        btnOffsetN = btnOffsetN + conTopOff;
    }
    
    
    if(window.wp && window.wp.blocks && getBack){
        getBack.addEventListener('scroll',(e) => {
            let topOffset = getBack.scrollTop;
            console.log(topOffset)
            if(topOffset >= btnOffsetN){
                button.style.visibility = "visible";
            }else{
                button.style.visibility = "hidden";
            }
        });
    }else{
        window.addEventListener('scroll',(e) => {
            if(window.scrollY >= btnOffsetN){
                button.style.visibility = "visible";
            }else{
                button.style.visibility = "hidden";
            }
        });
    }
    
    button.addEventListener('click',() => {
        if(window.wp && window.wp.blocks && getBack){
            getBack.scrollTo({
                top: topOff,
                behavior: 'smooth'
            }); 
        }else{
            window.scroll({
                top: topOff,
                behavior: 'smooth'
            }); 
        }
    });
} 
        }
    }

    new MyClass_vfv3q425();