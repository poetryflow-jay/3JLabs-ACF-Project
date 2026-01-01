"use strict";
window.onload = function(){
	var nexterNotice = document.querySelector(".nexter-ext-notice .notice-dismiss");
	if( nexterNotice ){
		nexterNotice.addEventListener('click', function(){
			var request = new XMLHttpRequest();

			request.open('POST', nexter_admin_config.ajaxurl, true);
			request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;');
			request.onload = function () {
			};
			request.send('action=nexter_ext_dismiss_notice&nexter_nonce=' + nexter_admin_config.ajax_nonce);
		});
	}

    /* let nxtExtInstall = document.querySelector('.nexter-install-ext');
    if(nxtExtInstall){
        nxtExtInstall.addEventListener('click', (e)=>{
            e.currentTarget.innerHTML = "<span class='nexter-loading-circle'></span>";

            var request = new XMLHttpRequest();

			request.open('POST', nexter_admin_config.ajaxurl, true);
			request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;');
			request.onload = function () {
                if (request.status >= 200 && request.status < 400) {
                    try {
                        var response = JSON.parse(request.responseText);
                        if (response.Success && response.redirectUrl) {
                            // Redirect to the Nexter Extension page
                            window.location.href = response.redirectUrl;
                        } else {
                            console.error('Failed to process request:', response);
                        }
                    } catch (error) {
                        console.error('Error parsing response:', error);
                    }
                } else {
                    console.error('Request failed with status:', request.status);
                }
            };
			request.send('action=nexter_ext_install&nexter_nonce=' + nexter_admin_config.ajax_nonce);
        })
    } */
}