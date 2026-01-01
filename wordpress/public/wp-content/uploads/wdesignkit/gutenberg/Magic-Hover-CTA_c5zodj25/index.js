class MyClass_xjrrsk25 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                setTimeout(() => {
            let main_html = document.querySelectorAll(".wkit-wb-Widget_c5zodj25")    
                main_html.forEach(element => {
                    this.main_function_xjrrsk25([element])
                });
        }, 800);
            });
        }

        main_function_xjrrsk25($scope) {
        let is_editable = wp?.blocks ? true : false;
            let container = $scope[0].querySelector('.wkit-magic-hover-cta');

const fillEffect = container.querySelector('.wkit-mhcta-fill-effect');
const title = container.querySelector('.w-magic-title');
const desc = container.querySelector('.w-magic-desc');
const btn = container.querySelector('.w-magic-btn');
const iconContainer = container.querySelector('.w-magic-icon');
const icon = iconContainer.querySelector('svg') || iconContainer.querySelector('i');

// Colors
let color = fillEffect.getAttribute('data-color') || '#836fff';
let strokeColor = title.getAttribute('data-stroke') || '#836fff';
let iconStrokeColor = iconContainer.getAttribute('data-icon-stroke') || '#836fff';

let minOpacity = parseFloat(container.getAttribute('data-minopa')) || 0;
let maxOpacity = parseFloat(container.getAttribute('data-maxopa')) || 1;

const maxTitleStrokeWidth = 2;
const maxIconStrokeWidth = 15;
const minRadius = 0;
const maxRadius = 50;

container.addEventListener('mousemove', function (e) {
  const rect = container.getBoundingClientRect();
  const mouseX = e.clientX - rect.left;
  const mouseY = e.clientY - rect.top;
  const containerWidth = container.offsetWidth;
  const containerHeight = container.offsetHeight;
  const centerX = containerWidth / 2;
  const centerY = containerHeight / 2;

  const distance = Math.sqrt(Math.pow(mouseX - centerX, 2) + Math.pow(mouseY - centerY, 2));
  const maxDistance = Math.sqrt(Math.pow(containerWidth / 2, 2) + Math.pow(containerHeight / 2, 2));
  const normalizedDistance = Math.min(1, distance / maxDistance);

  const radius = maxRadius - normalizedDistance * (maxRadius - minRadius);
  const titleStrokeWidth = maxTitleStrokeWidth * (1 - normalizedDistance);
  const iconStrokeWidth = maxIconStrokeWidth * (1 - normalizedDistance);

  fillEffect.style.background = `radial-gradient(circle at ${mouseX}px ${mouseY}px, ${color} ${radius}px, transparent)`;
  const currentOpacity = minOpacity + (maxOpacity - minOpacity) * normalizedDistance;

  title.style.color = `rgba(255, 255, 255, ${currentOpacity})`;
  title.style.webkitTextStroke = `${titleStrokeWidth}px ${strokeColor}`;
  desc.style.opacity = currentOpacity;

  if (icon.tagName.toLowerCase() === 'svg') {
    icon.style.stroke = hexToRgba(iconStrokeColor, 1 - normalizedDistance);
    icon.style.strokeWidth = '1px';
  } else if (icon.tagName.toLowerCase() === 'i') {
    icon.style.webkitTextStroke = `${iconStrokeWidth / 5}px ${iconStrokeColor}`;
  }
});

container.addEventListener('mouseleave', function () {
  fillEffect.style.background = 'transparent';
  title.style.color = '';
  title.style.webkitTextStroke = '0px transparent';
  desc.style.opacity = maxOpacity;

  if (icon.tagName.toLowerCase() === 'svg') {
    icon.style.stroke = iconStrokeColor;
    icon.style.strokeWidth = '1px';
  } else if (icon.tagName.toLowerCase() === 'i') {
    icon.style.webkitTextStroke = '0px transparent';
    icon.style.color = '';
  }
});

function hexToRgba(hex, opacity) {
  const r = parseInt(hex.slice(1, 3), 16);
  const g = parseInt(hex.slice(3, 5), 16);
  const b = parseInt(hex.slice(5, 7), 16);
  return `rgba(${r}, ${g}, ${b}, ${opacity})`;
}
 
        }
    }

    new MyClass_xjrrsk25();