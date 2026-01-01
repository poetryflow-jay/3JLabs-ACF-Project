class MyClass_jbygxw25 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                setTimeout(() => {
            let main_html = document.querySelectorAll(".wkit-wb-Widget_165b3324")    
                main_html.forEach(element => {
                    this.main_function_jbygxw25([element])
                });
        }, 800);
            });
        }

        main_function_jbygxw25($scope) {
        let is_editable = wp?.blocks ? true : false;
            let getCrt = $scope[0].querySelector('.wkit-grid-item-hover-effect');
let getEffect = getCrt.getAttribute('data-effect');

let getInner = getCrt.querySelector('.grid-inner');

let getImage = getInner.querySelector('.grid-inner-image');

let getPos = $scope[0].querySelectorAll('.inner__box');


let getTitle = $scope[0].querySelector('.pos-top-title');
var charArray2 = getTitle.textContent.split('');
getTitle.textContent = "";

for (var i = 0; i < charArray2.length; i++) {
    var charSpan = document.createElement("span");
    charSpan.textContent = charArray2[i];
    charSpan.className = "char";
    getTitle.appendChild(charSpan);
  }
  
  
  
let chargetTitle = getTitle.querySelectorAll(".char");



let desc = $scope[0].querySelector(".pos-bottom-desc");
var charArray = desc.textContent.split('');
desc.textContent = "";

for (var i = 0; i < charArray.length; i++) {
    var charSpan = document.createElement("span");
    charSpan.textContent = charArray[i];
    charSpan.className = "char";
    desc.appendChild(charSpan);
  }
let charDesc = desc.querySelectorAll(".char");

if (getEffect === 'style-1') {
    getInner.addEventListener('mouseenter', enter);
    getInner.addEventListener('mouseleave', leave);
}else if (getEffect === 'style-2') {
    getInner.addEventListener('mouseenter', function(event) {
        enter2(event);
    });
    getInner.addEventListener('mouseleave', function(event) {
        leave2(event);
    });
}else{
    getInner.addEventListener('mouseenter', enter3);
    getInner.addEventListener('mouseleave', leave3);
}

function enter() {
    if ( this.leaveTimeline) {
        this.leaveTimeline.kill();
    }
    
    this.enterTimeline = gsap
    .timeline({
        defaults: {
            duration: 0.5,
            ease: 'expo'
        }
    })
    .addLabel('start', 0)
    .fromTo(getImage, {
        filter: 'saturate(100%) brightness(100%)',
    }, {
        scale: 0.85,
        filter: 'saturate(200%) brightness(70%)'
    }, 'start')
    .fromTo(getPos, {
        opacity: 0,
        xPercent: (_, target) => {
            if (target.classList.contains('pos-top-left')) {
                return -100;
            }
            else if (target.classList.contains('pos-top-right')) {
                return 100;
            }
            else if (target.classList.contains('pos-bottom-left')) {
                return -100;
            }
            else if (target.classList.contains('pos-bottom-right')) {
                return 100;
            }
            return 0;
        },
        yPercent: (_, target) => {
            if (target.classList.contains('pos-top-left')) {
                return -100;
            }
            else if (target.classList.contains('pos-top-right')) {
                return -100;
            }
            else if (target.classList.contains('pos-bottom-left')) {
                return 100;
            }
            else if (target.classList.contains('pos-bottom-right')) {
                return 100;
            }
            return 0;
        }
    }, {
        opacity: 1,
        xPercent: 0,
        yPercent: 0,
    }, 'start')
      .fromTo(chargetTitle, {
        opacity: 0,
    }, {
        duration: 0.3,
        opacity: 1,
        stagger: 0.1,
    }, 'start+=.2')
    .fromTo(charDesc, {
        opacity: 0
    }, {
        duration: 0.1,
        opacity: 1,
        stagger: {
            from: 'random',
            each: 0.05
        },
    }, 'start+=.2')
}


function leave() {
    if ( this.enterTimeline ) {
        this.enterTimeline.kill();
    }

    this.leaveTimeline = gsap
    .timeline({
        defaults: {
            duration: 0.8,
            ease: 'expo'
        } 
    })
    .addLabel('start', 0)
    .to(getImage, {
        scale: 1,
        filter: 'saturate(100%) brightness(100%)'
    }, 'start')
    .to(getPos, {
        opacity: 0,
        xPercent: (_, target) => {
            if (target.classList.contains('pos-top-left')) {
                return -100;
            }
            else if (target.classList.contains('pos-top-right')) {
                return 100;
            }
            else if (target.classList.contains('pos-bottom-left')) {
                return -100;
            }
            else if (target.classList.contains('pos-bottom-right')) {
                return 100;
            }
            return 0;
        },
        yPercent: (_, target) => {
            if (target.classList.contains('pos-top-left')) {
                return -100;
            }
            else if (target.classList.contains('pos-top-right')) {
                return -100;
            }
            else if (target.classList.contains('pos-bottom-left')) {
                return 100;
            }
            else if (target.classList.contains('pos-bottom-right')) {
                return 100;
            }
            return 0;
        }
    }, 'start');
}

let lastMouseX = null;
let lastMouseY = null;

document.addEventListener('mousemove', (e) => {
    lastMouseX = e.clientX;
    lastMouseY = e.clientY;
});

const getMouseEnterDirection = (element, lastX, lastY) => {
    const { top, right, bottom, left } = element.getBoundingClientRect();
    
    if (lastY <= Math.floor(top)) return "top";
    if (lastY >= Math.floor(bottom)) return "bottom";
    if (lastX <= Math.floor(left)) return "left";
    if (lastX >= Math.floor(right)) return "right";
    
    return "unknown";
}

function enter2(event){
    const direction = getMouseEnterDirection(event.target, lastMouseX, lastMouseY);
    event.target.dataset.direction = direction;

    if ( event.leaveTimeline ) {
        event.leaveTimeline.kill();
    }
    
    event.enterTimeline = gsap
    .timeline({
        defaults: {
            duration: 0.7,
            ease: 'expo'
        }
    })
    .addLabel('start', 0)
    .fromTo(getImage, {
        filter: 'grayscale(0%)',
    }, {
        //scale: 1,
        xPercent: () => {
            if ( direction === 'left' ) {
                return -10;
            }
            else if ( direction === 'right' ) {
                return 10;
            }
            else return 0;
        },
        yPercent: () => {
            if ( direction === 'top' ) {
                return -10;
            }
            else if ( direction === 'bottom' ) {
                return 10;
            }
            else return 0;
        },
        filter: 'grayscale(40%)'
    }, 'start')
    .fromTo(getPos, {
        opacity: 0,
        xPercent: () => {
            if ( direction === 'left' ) {
                return -20;
            }
            else if ( direction === 'right' ) {
                return 20;
            }
            else return 0;
        },
        yPercent: () => {
            if ( direction === 'top' ) {
                return -20;
            }
            else if ( direction === 'bottom' ) {
                return 20;
            }
            else return 0;
        },
        rotation: -10
    }, {
        opacity: 1,
        xPercent: 0,
        yPercent: 0,
        rotation: 0,
        stagger: 0.08
    }, 'start')
    .fromTo(chargetTitle, {
        opacity: 0,
    }, {
        duration: 0.3,
        opacity: 1,
        stagger: 0.1,
    }, 'start+=.2')
    .fromTo(charDesc, {
        opacity: 0
    }, {
        duration: 0.1,
        opacity: 1,
        stagger: {
            from: 'random',
            each: 0.05
        },
    }, 'start+=.2')
}
function leave2(event) {
   const direction = event.target.dataset.direction;
    
    if ( event.enterTimeline ) {
        event.enterTimeline.kill();
    }
    event.leaveTimeline = gsap
    .timeline({
        defaults: {
            duration: 0.8,
            ease: 'expo'
        } 
    })
    .addLabel('start', 0)
    .to(getImage, {
        //scale: 1.3,
        xPercent: 0,
        yPercent: 0,
        filter: 'grayscale(0%)'
    }, 'start')
    .to(getPos, {
        //scale: 0,
        opacity: 0,
        
        xPercent: () => {
            if ( direction === 'left' ) {
                return -20;
            }
            else if ( direction === 'right' ) {
                return 20;
            }
            else return 0;
        },
        yPercent: () => {
            if ( direction === 'top' ) {
                return -20;
            }
            else if ( direction === 'bottom' ) {
                return 20;
            }
            else return 0;
        },
        rotation: -10
    }, 'start');
    
}

function enter3(){
    if ( this.leaveTimeline ) {
        this.leaveTimeline.kill();
    }
    
    this.enterTimeline = gsap
    .timeline({
        defaults: {
            duration: 0.7,
            ease: 'expo'
        }
    })
    .addLabel('start', 0)
    .fromTo(getImage, {
        filter: 'grayscale(0%)',
    }, {
        filter: 'grayscale(90%)'
    }, 'start')
    .to(getImage, {
        ease: 'power4',
        duration: 0.6,
        scaleY: 1
    }, 'start')
    .to(getImage, {
        duration: 1.5,
        scaleX: 1
    }, 'start')
    .fromTo(getPos, {
        opacity: 0,
        scale: 0,
        rotation: -10
    }, {
        opacity: 1,
        scale: 1,
        rotation: 0,
        stagger: 0.08
    }, 'start')
    .fromTo(chargetTitle, {
        opacity: 0,
    }, {
        duration: 0.3,
        opacity: 1,
        stagger: 0.1,
    }, 'start+=.2')
    .fromTo(charDesc, {
        opacity: 0
    }, {
        duration: 0.1,
        opacity: 1,
        stagger: {
            from: 'random',
            each: 0.05
        },
    }, 'start+=.2')
}
function leave3(){
    if ( this.enterTimeline ) {
        this.enterTimeline.kill();
    }
    this.leaveTimeline = gsap
    .timeline({
        defaults: {
            duration: 0.8,
            ease: 'expo'
        } 
    })
    .addLabel('start', 0)
    .to(getImage, {
        scale: 1.3,
        filter: 'grayscale(0%)'
    }, 'start')
    .to(getPos, {
        scale: 0,
        opacity: 0,
        rotation: -10
    }, 'start');
} 
        }
    }

    new MyClass_jbygxw25();