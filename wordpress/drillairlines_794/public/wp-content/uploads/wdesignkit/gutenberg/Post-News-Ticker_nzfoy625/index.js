class MyClass_7o8zfx25 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                setTimeout(() => {
            let main_html = document.querySelectorAll(".wkit-wb-Widget_nzfoy625")    
                main_html.forEach(element => {
                    this.main_function_7o8zfx25([element])
                });
        }, 800);
            });
        }

        main_function_7o8zfx25($scope) {
        let is_editable = wp?.blocks ? true : false;
            let postnews = $scope[0].querySelector('.wkit-pnt-main-container');
var unid = Math.random().toString(32).slice(2);
if(postnews.getAttribute('data-unique')){
    return;
}else{
    postnews.setAttribute('data-unique', unid)
}
let currentIndex = 0;
let isAnimating = false; // Flag to track animation state
let autoSlideInterval;

function getNewsContents() {
    return postnews.querySelectorAll('.w-pntslider-content'); // Fetch the content only within this widget
}

function updateContent(direction) {
    if (isAnimating) return; // Prevent overlapping animations
    isAnimating = true; // Set animation flag

    const newsContents = getNewsContents();
    if (newsContents.length === 0) {
        isAnimating = false; // Reset animation flag if no content
        return;
    }

    const currentItem = newsContents[currentIndex];
    currentItem.classList.remove('active');
    currentItem.classList.add('outgoing');

    if (direction === 'next') {
        currentIndex = (currentIndex + 1) % newsContents.length;
    } else if (direction === 'prev') {
        currentIndex = (currentIndex - 1 + newsContents.length) % newsContents.length;
    }

    const newItem = newsContents[currentIndex];
    const selectValue = postnews.getAttribute('data-select');

    // Apply animations
    if (selectValue === 'style-1' || selectValue === 'style-2' || selectValue === 'style-4' || selectValue === 'style-5') {
        currentItem.style.transform = direction === 'next' ? 'translateX(-100%)' : 'translateX(100%)';
        newItem.style.transform = direction === 'next' ? 'translateX(100%)' : 'translateX(-100%)';
    } else if (selectValue === 'style-3') {
        currentItem.style.transform = direction === 'next' ? 'translateY(-100%)' : 'translateY(100%)';
        newItem.style.transform = direction === 'next' ? 'translateY(100%)' : 'translateY(-100%)';
    }

    setTimeout(function () {
        if (selectValue === 'style-3') {
            newItem.classList.add('active');
            newItem.style.transform = 'translateY(0%)';
        } else {
            newItem.classList.add('active');
            newItem.style.transform = 'translateX(0%)';
        }
        currentItem.classList.remove('outgoing');
        currentItem.style.transform = '';
        isAnimating = false; // Reset animation flag
    }, 300); // Ensure this matches your CSS transition duration
}

function autoSlide() {
    autoSlideInterval = setInterval(function () {
        updateContent('next');
    }, 3000);
}

// Set the first item as active on load
const newsContents = getNewsContents();
if (newsContents.length > 0) {
    newsContents[0].classList.add('active'); // Ensure the first item is active
}

if (postnews.classList.contains('w-pntslider-style-1') || 
    postnews.classList.contains('w-pntslider-style-3') || 
    postnews.classList.contains('w-pntslider-style-4')) {
    
    postnews.querySelector('.w-pnt-nextBtn').addEventListener('click', function () {
        updateContent('next');
    });
    postnews.querySelector('.w-pnt-prevBtn').addEventListener('click', function () {
        updateContent('prev');
    });
}

if (postnews.classList.contains('w-pntslider-style-2') || 
    postnews.classList.contains('w-pntslider-style-5')) {
    autoSlide();
} 
        }
    }

    new MyClass_7o8zfx25();