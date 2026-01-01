'use strict';

jQuery(document).ready(function ($) {

   // Utility: show/hide element
   const show = el => el && $(el).show();
   const hide = el => el && $(el).hide();

   // Utility: fade in/out simulation
   const fadeInOut = (el, duration = 2500) => {
      if (!el) return;
      $(el).show();
      setTimeout(() => { $(el).hide(); }, duration);
   };

   // Utility: send AJAX POST request
   const postData = async (url, data) => {
      const body = $.param(data);
      const response = await fetch(url, {
         method: 'POST',
         headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
         body
      });
      return response.text();
   };

   // Cache DOM elements
   const $saveBtn = $('#nxt-amo-save-data');
   const $savingMsg = $('.nxt-amo-saving-data');
   const $savedMsg = $('.nxt-amo-saved-data');
   const $resetBtn = $('#nxt-amo-reset-menu');
   const $resetSpinner = $('.reset-menu-loader');

   // Save changes event
   $saveBtn.on('click', async function (e) {
      e.preventDefault();
      show($savingMsg);

      let menu_data = {
         action: 'nxt_save_admin_menu',
         nonce: nxt_admin_org_data.amoSaveNonce,
         custom_menu_order: $('#custom_menu_order').val() || '',
         menu_titles: $('#menu_titles').val() || '',
         change_menu_hidden: $('#change_menu_hidden').val() || '',
         change_submenus_order: $('#change_submenus_order').val() || '',
         menu_always_hidden: $('#menu_always_hidden').val() || '',
         submenu_always_hidden: $('#submenu_always_hidden').val() || '',
         change_menu_new_separators: $('#change_menu_new_separators').val() || ''
      };

      try {
         await postData(ajaxurl, menu_data);
         hide($savingMsg);
         fadeInOut($savedMsg, 2900);
      } catch (error) {
         console.error('Save Error:', error);
      }
   });

   // Reset menu event
   $resetBtn.on('click', async function (e) {
      e.preventDefault();
      show($resetSpinner);

      try {
         let rawData = await postData(ajaxurl, {
            action: 'nxt_reset_admin_menu',
            nonce: nxt_admin_org_data.amoResetNonce
         });

         rawData = rawData.trim().replace(/\0+$/, '');
         const response = JSON.parse(rawData);

         if (response?.success) {
            location.reload(true);
         }
         hide($resetSpinner);
      } catch (error) {
         console.error('Reset Error:', error);
      }
   });

});