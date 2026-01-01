"use strict";
document.addEventListener("DOMContentLoaded", function () {
    let getSideBar = document.querySelector('#nxt_sidebar_settings');
    if (getSideBar) {
        let sideBarType = getSideBar.querySelectorAll('input[name="nxt-post-page-sidebar"]');
        let pagePostSidebar = getSideBar.querySelector('select[name="nxt-post-page-display-sidebar"]');
        let customSidebar = getSideBar.querySelector('select[name="nxt-post-page-custom-sidebar"]');

        if (sideBarType) {
            sideBarType.forEach(radio => {
                radio.addEventListener('change', handleVisibility);
            });

            pagePostSidebar.addEventListener('change', handleVisibility);

            function handleVisibility() {
                let selectedRadio = getSideBar.querySelector('input[name="nxt-post-page-sidebar"]:checked');

                if (selectedRadio && (selectedRadio.value == "left-sidebar" || selectedRadio.value == "right-sidebar")) {
                    pagePostSidebar.parentElement.style.display = "";
                } else {
                    pagePostSidebar.parentElement.style.display = "none";
                    customSidebar.parentElement.style.display = "none";
                }

                // Check select2's value
                if (pagePostSidebar.value === "custom") {
                    customSidebar.parentElement.style.display = "";
                } else {
                    customSidebar.parentElement.style.display = "none";
                }
            }

            // Apply changes on page load
            handleVisibility();
        }
    }
});
