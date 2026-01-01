class MyClass_cyvpqs25 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                setTimeout(() => {
            let main_html = document.querySelectorAll(".wkit-wb-Widget_opjpzt24")    
                main_html.forEach(element => {
                    this.main_function_cyvpqs25([element])
                });
        }, 800);
            });
        }

        main_function_cyvpqs25($scope) {
        let is_editable = wp?.blocks ? true : false;
            let getWA = $scope[0].querySelector('.wkit-whatsapp-chat');
var unid = Math.random().toString(32).slice(2);
if(getWA.getAttribute('data-unique')){
    return;
}else{
    getWA.setAttribute('data-unique', unid)
}

let closeCross = getWA.querySelector('.wkit-wa-popup-close');

let getLoader = getWA.querySelector('.whatsapp-typing-loader');
let getBody = getWA.querySelector('.body-main-content');
setTimeout(()=>{
    getLoader.style.opacity = '1';
}, 2000);

setTimeout(()=>{
    let timeEl = getBody.querySelector('.content-time');
    let nowDT = new Date();
    let hours = nowDT.getHours();
    let minutes = nowDT.getMinutes();
    hours = (hours < 10) ? "0" + hours : hours;
    minutes = (minutes < 10) ? "0" + minutes : minutes;
    let formattedTime = `${hours}:${minutes}`;
    timeEl.textContent = formattedTime;
    
    getLoader.style.opacity = '0';
    getBody.style.opacity = '1';
}, 4000);

let waBtn = getWA.querySelector('.wkit-wa-btn-wrap');

let gtNumber = waBtn.getAttribute('wa-number');
let gtMesaage = waBtn.getAttribute('wa-message');
if(gtNumber){
    
    let cLink = 'https://api.whatsapp.com/send/?phone='+gtNumber;
    if(gtMesaage){
        cLink += '&text='+gtMesaage;
    }
    let gtAtag = waBtn.querySelector('.wkit-whatsapp-btn');
    
    gtAtag.href = cLink;
}


let tglBtn = getWA.querySelector('.wkit-toggle-btn');

let chatBox = getWA.querySelector('.wkit-whatsapp-chat-inner');

tglBtn.addEventListener('click', (e)=>{
    let getCrt = e.currentTarget;
    if(getCrt.classList.contains('active')){
        getCrt.classList.remove('active');
        chatBox.classList.remove('active');
    }else{
        getCrt.classList.add('active');
        chatBox.classList.add('active');
    }
});

closeCross.addEventListener('click', (e)=>{
    tglBtn.classList.remove('active');
    chatBox.classList.remove('active');
}); 
        }
    }

    new MyClass_cyvpqs25();