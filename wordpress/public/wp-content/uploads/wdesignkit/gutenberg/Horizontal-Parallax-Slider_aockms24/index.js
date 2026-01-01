class MyClass_9okwck25 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                setTimeout(() => {
            let main_html = document.querySelectorAll(".wkit-wb-Widget_aockms24")    
                main_html.forEach(element => {
                    this.main_function_9okwck25([element])
                });
        }, 800);
            });
        }

        main_function_9okwck25($scope) {
        let is_editable = wp?.blocks ? true : false;
            let crtSlider = $scope[0].querySelector('.wkit-hor-par-slider');

var unid = Math.random().toString(32).slice(2);

let mainSliderC = crtSlider.querySelector('.wkit-main-slider');
let navSliderC = crtSlider.querySelector('.wkit-nav-slider');

let slideNextC = crtSlider.querySelector('.swiper-button-next');
let slidePrevC = crtSlider.querySelector('.swiper-button-prev');

mainSliderC.classList.add('main-slider-'+unid);
navSliderC.classList.add('nav-slider-'+unid);
slideNextC.classList.add('slide-next-'+unid);
slidePrevC.classList.add('slide-prev-'+unid);

let mainSliderSelector = '.main-slider-'+unid,
    navSliderSelector = '.nav-slider-'+unid,
    interleaveOffset = 0.5;
    
let deskSPV = (crtSlider.getAttribute('data-spv-desk')) ? Number(crtSlider.getAttribute('data-spv-desk')) : 6,
    tabSPV = (crtSlider.getAttribute('data-spv-tab')) ? Number(crtSlider.getAttribute('data-spv-tab')) : 6,
    mobSPV = (crtSlider.getAttribute('data-spv-mob')) ? Number(crtSlider.getAttribute('data-spv-mob')) : 6,
    deskSSB = (crtSlider.getAttribute('data-ssb-desk')) ? Number(crtSlider.getAttribute('data-ssb-desk')) : 6,
    tabSSB = (crtSlider.getAttribute('data-ssb-tab')) ? Number(crtSlider.getAttribute('data-ssb-tab')) : 6,
    mobSSB = (crtSlider.getAttribute('data-ssb-mob')) ? Number(crtSlider.getAttribute('data-ssb-mob')) : 6;
    
    let screenWidth = screen.width;
    
    let spaceBet = 6, slidePerV = 6, loopAdiS = 12;
    if (screenWidth >= 1024) {
        spaceBet = deskSSB;
        slidePerV = deskSPV;
        loopAdiS = deskSPV * 2;
    } else if (screenWidth < 1024 && screenWidth >= 768) {
        spaceBet = tabSSB;
        slidePerV = tabSPV;
        loopAdiS = tabSPV * 2;
    } else if (screenWidth < 768) {
        spaceBet = mobSSB;
        slidePerV = mobSPV;
        loopAdiS = mobSPV * 2;
    }

// Main Slider

    setTimeout(()=>{
        let mainSliderOptions = {
          loop: true,
          speed:1000,
          autoplay:{
            delay:3000
          },
          loopAdditionalSlides: 10,
          grabCursor: true,
          watchSlidesProgress: true,
          navigation: {
            nextEl: '.slide-next-'+unid,
            prevEl: '.slide-prev-'+unid,
          },
          on: {
            slideChangeTransitionEnd: function(){
              let swiper = this,
                  captions = swiper.el.querySelectorAll('.wkit-slide-desc');
              for (let i = 0; i < captions.length; ++i) {
                captions[i].classList.remove('show');
              }
              let getCurr = swiper.el.querySelectorAll('[data-swiper-slide-index="'+swiper.realIndex+'"]');
              getCurr.forEach((gc)=>{
                  gc.querySelector('.wkit-slide-desc').classList.add('show');
              })
            },
            progress: function(){
              let swiper = this;
              for (let i = 0; i < swiper.slides.length; i++) {
                let slideProgress = swiper.slides[i].progress,
                    innerOffset = swiper.width * interleaveOffset,
                    innerTranslate = slideProgress * innerOffset;
               
                swiper.slides[i].querySelector(".slide-bg-img").style.transform =
                  "translateX(" + innerTranslate + "px)";
              }
            },
            touchStart: function() {
              let swiper = this;
              for (let i = 0; i < swiper.slides.length; i++) {
                swiper.slides[i].style.transition = "";
              }
            },
            setTransition: function(speed) {
              let swiper = this;
              for (let i = 0; i < swiper.slides.length; i++) {
                swiper.slides[i].style.transition = speed + "ms";
                swiper.slides[i].querySelector(".slide-bg-img").style.transition =
                  speed + "ms";
              }
            }
          }
        };
        let mainSlider = new Swiper(mainSliderSelector, mainSliderOptions);
          
        let navSliderOptions = {
            loop: true,
            loopAdditionalSlides: loopAdiS,
            speed:1000,
            spaceBetween: spaceBet,
            slidesPerView: slidePerV,
            centeredSlides : true,
            touchRatio: 1,
            slideToClickedSlide: true,
            direction: 'vertical',
            on: {
                click: function(){
                    mainSlider.autoplay.stop();
                }
            }
        };
        
        let navSlider = new Swiper(navSliderSelector, navSliderOptions);
        mainSlider.controller.control = navSlider;
        navSlider.controller.control = mainSlider;
    }, 100);
    
// Navigation Slider

 
        }
    }

    new MyClass_9okwck25();