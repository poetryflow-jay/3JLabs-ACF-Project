class MyClass_5vc3ic25 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                var $this = this;
        setTimeout(() => {
            let main_html = jQuery('.wkit-wb-Widget_d0l3gf24');

            jQuery.each(main_html, function (idx, scope) {
                $this.main_function_5vc3ic25(jQuery(scope));
            });
        }, 800);
            });
        }

        main_function_5vc3ic25($scope) {
        let is_editable = wp?.blocks ? true : false;
            
gsap.registerPlugin(ScrollTrigger);

let getEl =$scope[0].querySelector('.wkit-scroll-wrapper');
let getBanner = getEl.querySelector('.wkit-scroll-banner');

let deskScale = (getEl.getAttribute('desktop-scale')) ? Number(getEl.getAttribute('desktop-scale')) : 0.6; 
let tabScale = (getEl.getAttribute('tablet-scale')) ? Number(getEl.getAttribute('tablet-scale')) : 0.6; 
let mobScale = (getEl.getAttribute('mobile-scale')) ? Number(getEl.getAttribute('mobile-scale')) : 0.5; 
let screenWidth = screen.width;
let scaleVal = 0.6;
if (screenWidth >= 1024) {
    scaleVal = deskScale;
} else if (screenWidth < 1024 && screenWidth >= 768) {
    scaleVal = tabScale;
} else if (screenWidth < 768) {
    scaleVal = mobScale;
}

if(!$scope[0].closest('.wp-block')){
    let getCnt = getBanner.querySelectorAll('.wkit-scroll-banner > *');
    
    const tlll = gsap.timeline().to(getBanner, {scale: scaleVal}).fromTo(getCnt,{y: 100, opacity: 0},{y: 0, opacity: 1, stagger: 0.3})
    
    
    ScrollTrigger.create({
    	trigger: getEl,
    	animation: tlll,
    	pin: true,
    	start: 'center center',
    	end: 'bottom top',
    	scrub: 1, 
    })
    let text1 = getBanner.querySelector('.wkit-title');   
}
 
        }
    }

    new MyClass_5vc3ic25();