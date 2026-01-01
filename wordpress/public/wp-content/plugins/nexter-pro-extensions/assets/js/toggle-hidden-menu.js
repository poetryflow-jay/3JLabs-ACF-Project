(function() {
  'use strict';

  document.addEventListener('DOMContentLoaded', function() {

    const showMenuBtn = document.querySelector('#toplevel_page_nxt_ext_show_hidden_menu a');
    const hideMenuBtn = document.querySelector('#toplevel_page_nxt_ext_hide_hidden_menu a');
    const showMenuItem = document.getElementById('toplevel_page_nxt_ext_show_hidden_menu');
    const hideMenuItem = document.getElementById('toplevel_page_nxt_ext_hide_hidden_menu');

    if (hideMenuItem) hideMenuItem.style.display = 'none';

    function toggleHiddenMenus() {
      document.querySelectorAll('.menu-top.nxt_hidden_menu').forEach(el => el.classList.toggle('hidden'));
      document.querySelectorAll('.wp-menu-separator.nxt_hidden_menu').forEach(el => el.classList.toggle('hidden'));

      document.querySelectorAll('#adminmenu .wp-submenu li.nxt_hidden_menu').forEach(el => el.classList.toggle('hidden'));
      document.querySelectorAll('#adminmenu .wp-submenu a.nxt_hidden_menu').forEach(el => el.classList.toggle('hidden'));

      const event = new Event('wp-window-resized');
      document.dispatchEvent(event);
    }

    if (showMenuBtn) {
      showMenuBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (showMenuItem) showMenuItem.style.display = 'none';
        if (hideMenuItem) hideMenuItem.style.display = 'block';
        toggleHiddenMenus();
      });
    }

    if (hideMenuBtn) {
      hideMenuBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (showMenuItem) showMenuItem.style.display = 'block';
        if (hideMenuItem) hideMenuItem.style.display = 'none';
        toggleHiddenMenus();
      });
    }

  });
})();