class MyClass_54gsid25 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                var $this = this;
        setTimeout(() => {
            let main_html = jQuery('.wkit-wb-Widget_tedwgp24');

            jQuery.each(main_html, function (idx, scope) {
                $this.main_function_54gsid25(jQuery(scope));
            });
        }, 800);
            });
        }

        main_function_54gsid25($scope) {
        let is_editable = wp?.blocks ? true : false;
            let mainTabC = $scope[0].querySelector('.wkit-tabbed-images-carousel');
if (!mainTabC) return;

let tabbed = mainTabC.querySelector(".tabbed-splide");

let splidewv;
if (typeof Splide !== 'undefined' && tabbed) {
    splidewv = new Splide(tabbed, {
        drag: false,
        arrows: false,
        autoplay: false,
        pagination: false,
    }).mount();
}

$scope[0].style.width = '100%';

let setIntVal;

// Switch active tab and Splide slide
function wkittabschange(crt, leftItem) {
    if (!crt.classList.contains('active')) {
        let getItemD = Number(crt.getAttribute('data-item'));
        leftItem.forEach((lia) => {
            lia.classList.remove('active');
            let getDesc = lia.querySelector('.car-content-desc');
            if (getDesc) getDesc.style.display = 'none';
        });

        crt.classList.add('active');
        let gecCDesc = crt.querySelector('.car-content-desc');
        if (gecCDesc) gecCDesc.style.display = 'block';

        // Sync with Splide
        if (splidewv && splidewv.go && !isNaN(getItemD)) {
            splidewv.go(getItemD);
        }
    }
}

// Auto advance logic
function autoIntervalAcc(active, leftItem) {
    if (active) {
        let next = active.nextElementSibling;
        if (!next) {
            next = leftItem[0]; // fallback to first
        }
        if (next) wkittabschange(next, leftItem);
    }
}

function initializeTabCarousel() {
    let leftItem = mainTabC.querySelectorAll('.carousel-left-content');
    let gtDuration = Number(mainTabC.getAttribute('data-duration') || 5);
mainTabC.style.setProperty('--border-duration', `${gtDuration}s`);

    if (leftItem.length) {
        // Initialize: mark first as active
        leftItem.forEach((li, index) => {
            let desc = li.querySelector('.car-content-desc');
            li.setAttribute('data-item', index); // 💡 Ensure this is added
            if (index === 0) {
                li.classList.add('active');
                if (desc) desc.style.display = 'block';
                if (splidewv && splidewv.go) {
                    splidewv.go(index); // force Splide to sync
                }
            } else {
                if (desc) desc.style.display = 'none';
            }

            // Click listener
            li.addEventListener('click', () => {
                wkittabschange(li, leftItem);
                if (mainTabC.classList.contains('tabbed-auto-play-yes')) {
                    clearInterval(setIntVal);
                    setIntVal = setInterval(() => {
                        let active = mainTabC.querySelector('.carousel-left-content.active');
                        autoIntervalAcc(active, leftItem);
                    }, gtDuration * 1000);
                }
            });
        });

        // Start autoplay
        if (mainTabC.classList.contains('tabbed-auto-play-yes')) {
            setIntVal = setInterval(() => {
                let active = mainTabC.querySelector('.carousel-left-content.active');
                autoIntervalAcc(active, leftItem);
            }, gtDuration * 1000);
        }
    }
}

// Ensure everything is ready
setTimeout(initializeTabCarousel, 10); // Or requestAnimationFrame if needed
 
        }
    }

    new MyClass_54gsid25();