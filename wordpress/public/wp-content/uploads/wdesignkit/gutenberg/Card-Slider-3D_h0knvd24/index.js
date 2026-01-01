class MyClass_36x3mb25 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                var $this = this;
        setTimeout(() => {
            let main_html = jQuery('.wkit-wb-Widget_h0knvd24');

            jQuery.each(main_html, function (idx, scope) {
                $this.main_function_36x3mb25(jQuery(scope));
            });
        }, 800);
            });
        }

        main_function_36x3mb25($scope) {
        let is_editable = wp?.blocks ? true : false;
            let getSlide = $scope[0].querySelector(".wkit-cardslide-wrapper");
var unid = Math.random().toString(32).slice(2);
if(getSlide.getAttribute('data-unique')){
    return;
}else{
    getSlide.setAttribute('data-unique', unid)
}
if(getSlide){
    var unid = Math.random().toString(32).slice(2);
    let swClss = getSlide.querySelector('.wkit-slider');
    let swiperPag=getSlide.querySelector(".swiper-pagination");
        swiperPag.classList.add('swiper-pag-'+unid);
    let swiperbtnnext=getSlide.querySelector(".swiper-button-next");
        swiperbtnnext.classList.add('swiper-nxt-'+unid);
    let swiperbtnprev=getSlide.querySelector(".swiper-button-prev");
        swiperbtnprev.classList.add('swiper-pre-'+unid);
    var TrandingSlider = new Swiper(swClss, {
        effect: 'coverflow',
        grabCursor: true,
        centeredSlides: true,
        loop: true,
        draggable:true,
        slidesPerView: 'auto',
        coverflowEffect: {
            rotate: 0,
            stretch: 0,
            depth: 200,
            modifier: 3,
        },
        pagination: {
            el: '.swiper-pag-'+unid,
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-nxt-'+unid,
            prevEl: '.swiper-pre-'+unid,
        }
    }); 
}

 
        }
    }

    new MyClass_36x3mb25();