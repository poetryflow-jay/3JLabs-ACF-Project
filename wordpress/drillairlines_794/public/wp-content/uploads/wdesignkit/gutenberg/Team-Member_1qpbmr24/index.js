class MyClass_nbc5ct25 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                var $this = this;
        setTimeout(() => {
            let main_html = jQuery('.wkit-wb-Widget_1qpbmr24');

            jQuery.each(main_html, function (idx, scope) {
                $this.main_function_nbc5ct25(jQuery(scope));
            });
        }, 800);
            });
        }

        main_function_nbc5ct25($scope) {
        let is_editable = wp?.blocks ? true : false;
            let teamMember = $scope[0].querySelector('.wkit-team-member');
let memberSecond = teamMember.querySelector('.team-member-second');
let memberSecondInner = memberSecond.querySelector('.team-member-second-info-open-close');
let memberThird = teamMember.querySelector('.team-member-third');


  memberSecondInner.addEventListener("click", function(e){
      
      memberThird.classList.toggle('active');
      
      e.currentTarget.classList.toggle('active');
      
      if (memberThird.style.display == "flex"){
          memberThird.style.display = "none";
        } 
        else {
          memberThird.style.display = "flex";
        }
    });
   
  let memberItem = teamMember.querySelectorAll('.team-member-content-item');
  
  let totalItem = 0;
  memberItem.forEach((el,index)=>{
      if(index==0){
          el.classList.add('active');
      }
      totalItem = index;
  });
  
  let clickNextArrow = teamMember.querySelector('.team-member-third-info-arrow');
  let nextArrow = Number(clickNextArrow.getAttribute('data-counter'))*1000
  
  if(totalItem >= 1){
    clickNextArrow.addEventListener('click', changeItem);
     setTimeout(()=>{
        setInterval(changeItem,nextArrow)
    }, 100);   
  }
 
 function changeItem(){
     let itemActive = teamMember.querySelector('.team-member-content-item.active');
      if(itemActive.nextElementSibling){
            itemActive.nextElementSibling.classList.add('active');
            itemActive.classList.remove('active');
            
        }else{
            let closeInn = itemActive.closest('.team-member-third-interest'),
            firstItem = closeInn.firstElementChild;
            if(firstItem){
                firstItem.classList.add('active');
            }
            itemActive.classList.remove('active');
        }
 } 
        }
    }

    new MyClass_nbc5ct25();