class MyClass_mw9py425 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                setTimeout(() => {
            let main_html = document.querySelectorAll(".wkit-wb-Widget_ultclt25")    
                main_html.forEach(element => {
                    this.main_function_mw9py425([element])
                });
        }, 800);
            });
        }

        main_function_mw9py425($scope) {
        let is_editable = wp?.blocks ? true : false;
            let copymain = $scope[0].querySelector(".wkit-coupon-row");
let copybtn = copymain.querySelector(".copybtn");
let copybtntext=copymain.querySelector(".copy-btn-text");
let copybtn1 = copymain.querySelector(".copybtn1");
let copytextEf = copymain.querySelector(".copiedtext");
copybtn.addEventListener("click", (e) => {
    let copyInput = copymain.querySelector("#copyvalue"),
        copyText = (copyInput.getAttribute('data-copytext')) ? copyInput.getAttribute('data-copytext') : 'Copy',
        afterText = (copyInput.getAttribute('data-aftertext')) ? copyInput.getAttribute('data-aftertext') : 'Copied';
    copyInput.select();
    document.execCommand("copy");
    copybtntext.textContent = afterText;
      
     setTimeout(()=>{
        copybtntext.textContent = copyText;
    }, 3000);
});

copybtn1.addEventListener("click", (e) => {
    // let copymain1 = $scope[0].querySelector(".wkit-coupon-row");
    let copytext1 = copymain.querySelector(".copiedinner");
    console.log(copytext1);
    copytext1.select();
    document.execCommand("copy");
    copytextEf.classList.add("copied");
    setTimeout(() => {
        copytextEf.classList.remove("copied");
    }, 300);
}); 
        }
    }

    new MyClass_mw9py425();