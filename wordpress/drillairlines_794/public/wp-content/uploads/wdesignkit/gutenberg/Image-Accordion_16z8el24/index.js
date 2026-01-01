class MyClass_xzojzt25 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                var $this = this;
        setTimeout(() => {
            let main_html = jQuery('.wkit-wb-Widget_16z8el24');

            jQuery.each(main_html, function (idx, scope) {
                $this.main_function_xzojzt25(jQuery(scope));
            });
        }, 800);
            });
        }

        main_function_xzojzt25($scope) {
        let is_editable = wp?.blocks ? true : false;
            let mainEl = $scope[0].querySelector('.wkit-img-accrod-main-wrap'); 
let allItems = $scope[0].querySelectorAll('.wkit-img-accord-item');

var unid = Math.random().toString(32).slice(2);
if(mainEl.getAttribute('data-unique')){
    return;
}else{
    mainEl.setAttribute('data-unique', unid)
}

let getSplide = $scope[0].querySelector('.splide')

let splidewv = new Splide(getSplide, {
    type: 'loop',
    arrows: false,
    autoWidth: true,
});

splidewv.mount();

allItems.forEach((aia, index)=>{
    if(index==0){
        aia.classList.add('wkit-item-active'); 
    }
});

function handleItemClick(ev) {
    let activeItem = mainEl.querySelectorAll('.wkit-item-active');

    if (ev.currentTarget.classList.contains('wkit-item-active')) {
        ev.currentTarget.classList.remove('wkit-item-active');
    } else {
        ev.currentTarget.classList.add('wkit-item-active');
        if (activeItem) {
            activeItem.forEach((aia) => {
                aia.classList.remove('wkit-item-active');
            });
        }
    }
}

function applyClickEvents() {
    let allSlides = getSplide.querySelectorAll('.splide__slide');
    allSlides.forEach((slide) => {
        let getInner = slide.querySelector('.wkit-img-accord-item');
        getInner.removeEventListener('click', handleItemClick);
        getInner.addEventListener('click', handleItemClick);
    });
}

applyClickEvents();

splidewv.on('mounted moved', () => {
    applyClickEvents();
}); 
        }
    }

    new MyClass_xzojzt25();