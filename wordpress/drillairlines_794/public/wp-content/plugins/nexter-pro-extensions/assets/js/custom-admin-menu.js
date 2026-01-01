(function( $ ) {
   'use strict';

   $(document).ready( function() {

      $('#nxt-admin-menu-org').sortable({
         items: '> li',
         opacity: 0.6,
         placeholder: 'sortable-placeholder',
         tolerance: 'pointer',
         revert: 250
      });

      let menuOrder = $('#nxt-admin-menu-org').sortable("toArray").toString();

      document.getElementById('custom_menu_order').value = menuOrder;

      $('#nxt-admin-menu-org').on( 'sortupdate', function( event, ui) {

         let menuOrder = $('#nxt-admin-menu-org').sortable("toArray").toString();

         document.getElementById('custom_menu_order').value = menuOrder;

      });

      var submenuSortableId = '',
          submenuOrder = {};

      // Initialize sortable elements for submenu items: https://api.jqueryui.com/sortable/
      $('.submenu-sortable').each(function() {
         submenuSortableId = $(this).attr('id');

         $(this).sortable({
            containment: $(this),
            items: '> li',
            opacity: 0.6,
            placeholder: 'submenu-sortable-placeholder',
            tolerance: 'pointer',
            revert: 250
         });
      });

      // Store current submenus items order for saving to options
      $('.submenu-sortable').each(function() {
         submenuSortableId = $(this).attr('id');

         // Get the default/current menu order
         submenuOrder[submenuSortableId] = $(this).sortable("toArray").toString();

         // Set hidden input value for saving in options
         document.getElementById('change_submenus_order').value = JSON.stringify(submenuOrder);
      });

      // Update submenus items order for saving to options
      $('.submenu-sortable').on('sortupdate', function( event, ui) {
         submenuSortableId = $(this).attr('id');
         submenuOrder[submenuSortableId] = $(this).sortable("toArray").toString();
         document.getElementById('change_submenus_order').value = JSON.stringify(submenuOrder);
      });
      
      // Toggle submenu items
      $('#nxt-admin-menu-org').on('click', '.submenu-toggle', function() {
         $(this).children('.toggle-submenu-open').toggleClass('active-submenu-open');
         $(this).parents('.menu-item').find('.submenu-wrapper').toggle();
      });

      // Prepare constant to store IDs of menu items that will be hidden
      var hiddenMenuByRoleInput = document.getElementById('menu_always_hidden');
      if ( hiddenMenuByRoleInput && hiddenMenuByRoleInput.value ) {
         var menuAlwaysHidden = JSON.parse(hiddenMenuByRoleInput.value); // object
      } else {
         var menuAlwaysHidden = {}; // object
      }

      // Initialize object to store hidden menus and set check mark of 'Hide' checkbox
      $('.hide-parent-menu-checkbox').each(function() {
         nxtMenuAlwaysHidden(menuAlwaysHidden,this);
      });

      $('#nxt-admin-menu-org').on('click', '.hide-parent-menu-checkbox', function () {
         const $checkbox = $(this);
         const menuId = $checkbox.data('menu-item-id');
         const isChecked = $checkbox.is(':checked');

         const $options = $('#options-for-' + menuId);
         const $allRoles = $('#all-roles-selected-opt-' + menuId);
         const $toggle = $checkbox.parent().next('.options-toggle').children('.toggle-submenu-open');
         //const $menuItem = $('#' + menuId);
         const $menuItem = $checkbox.closest('.menu-item');

         if (isChecked) {
            $options.show();
            $allRoles.show();
         } else {
            $('#hide-until-toggled-for-' + menuId).prop('checked', false);
            $('#hide-by-role-for-' + menuId).prop('checked', false);
            $('#all-roles-selected-radio-' + menuId).hide();
            $('#hide-for-roles-' + menuId).hide();
            $('#menu-req-capability-' + menuId).hide();
            $options.hide();
            $allRoles.hide();

            if (menuAlwaysHidden[menuId]) {
               menuAlwaysHidden[menuId]['hide_by_toggle'] = false;
               menuAlwaysHidden[menuId]['always_hide'] = false;
            }
         }

         // Update submenu UI state
         const optionsVisible = $options.is(':visible');
         $toggle.toggleClass('active-submenu-open', optionsVisible);
         $menuItem.toggleClass('active-opt', optionsVisible);

         // Update hidden input
         document.getElementById('menu_always_hidden').value = JSON.stringify(menuAlwaysHidden);
      });

      // Handle "Hide until toggled" checkbox click for parent menu items
      $('#nxt-admin-menu-org').on('click', '.hide-until-toggled-checkbox', function () {
         const $checkbox = $(this);
         const menuId = $checkbox.data('menu-item-id');

         const $hideStatus = $('#hide-status-for-' + menuId);
         const $hideByRole = $('#hide-by-role-for-' + menuId);

         const isChecked = $checkbox.is(':checked');

         if (!menuAlwaysHidden[menuId]) {
            menuAlwaysHidden[menuId] = {};
         }

         menuAlwaysHidden[menuId]['hide_by_toggle'] = isChecked;

         if (isChecked) {
            $hideStatus.prop('checked', true);
         } else if (!$hideByRole.is(':checked')) {
            $hideStatus.prop('checked', false);
            // Optional: delete menuAlwaysHidden[menuId];
         }

         document.getElementById('menu_always_hidden').value = JSON.stringify(menuAlwaysHidden);
      });


      // Prepare constant to store IDs of submenu items that will be hidden
      var hiddenSubmenuByRoleInput = document.getElementById('submenu_always_hidden');
      if ( hiddenSubmenuByRoleInput && hiddenSubmenuByRoleInput.value ) {
         var submenuAlwaysHidden = JSON.parse(hiddenSubmenuByRoleInput.value); // object
      } else {
         var submenuAlwaysHidden = {}; // object
      }

      // Initialize object to store hidden menus and set check mark of 'Hide' checkbox
      $('.hide-submenu-checkbox').each(function() {
         nxtSubmenuAlwaysHidden(submenuAlwaysHidden,this);
      });

      // Toggle visibility of options panel for submenu items
      $('#nxt-admin-menu-org').on('click', '.hide-submenu-checkbox', function () {
         const $checkbox = $(this);
         const menuId = $checkbox.data('menu-item-id');

         const $options = $('#options-for-' + menuId);
         const $allRoles = $('#all-roles-selected-opt-' + menuId);
         const $radio = $('#all-roles-selected-radio-' + menuId);
         const $hideForRoles = $('#hide-for-roles-' + menuId);
         const $capability = $('#menu-req-capability-' + menuId);
         const $toggle = $checkbox.parent().next('.options-toggle').children('.toggle-submenu-open');
         const $menuItem = $checkbox.closest('.menu-item');
         const $hideByToggle = $('#hide-until-toggled-for-' + menuId);
         const $hideByRole = $('#hide-by-role-for-' + menuId);

         if (!$checkbox.length || !menuId) return;

         const isChecked = $checkbox.is(':checked');

         if (!submenuAlwaysHidden[menuId]) submenuAlwaysHidden[menuId] = {};

         if (isChecked) {
            $options.show();
            $allRoles.show();
         } else {
            $options.hide();
            $allRoles.hide();
            $radio.hide();
            $hideForRoles.hide();
            $capability.hide();
            $hideByToggle.prop('checked', false);
            $hideByRole.prop('checked', false);
            submenuAlwaysHidden[menuId]['hide_by_toggle'] = false;
            submenuAlwaysHidden[menuId]['always_hide'] = false;
         }

         const isVisible = $options.is(':visible');
         $toggle.toggleClass('active-submenu-open', isVisible);
         $menuItem.toggleClass('active-opt', isVisible);

         document.getElementById('submenu_always_hidden').value = JSON.stringify(submenuAlwaysHidden);
      });

      // Handle "Hide until toggled" checkbox for submenu items
      $('#nxt-admin-menu-org').on('click', '.hide-until-toggled-submenu-checkbox', function () {
         const $checkbox = $(this);
         const menuId = $checkbox.data('menu-item-id');
         const $hideByRole = $('#hide-by-role-for-' + menuId);
         const $hideStatus = $('#hide-status-for-' + menuId);

         if (!submenuAlwaysHidden[menuId]) submenuAlwaysHidden[menuId] = {};

         const isChecked = $checkbox.is(':checked');
         submenuAlwaysHidden[menuId]['hide_by_toggle'] = isChecked;

         if (isChecked) {
            $hideStatus.prop('checked', true);
         } else if (!$hideByRole.is(':checked')) {
            $hideStatus.prop('checked', false);
            // Optional: delete submenuAlwaysHidden[menuId];
         }

         document.getElementById('submenu_always_hidden').value = JSON.stringify(submenuAlwaysHidden);
      });

      // Toggle submenu options manually using the "Options" toggle
      $('#nxt-admin-menu-org').on('click', '.options-toggle', function () {
         const $toggle = $(this).children('.toggle-submenu-open');
         const $menuItem = $(this).closest('.menu-item');
         const menuId = $(this).data('menu-item-id');

         const $options = $('#options-for-' + menuId);
         const $hideByToggle = $('#hide-until-toggled-for-' + menuId);
         const $hideByRole = $('#hide-by-role-for-' + menuId);
         const $hideStatus = $('#hide-status-for-' + menuId);

         $toggle.toggleClass('active-submenu-open');
         $menuItem.toggleClass('active-opt');
         $options.toggle();

         if (!$hideByToggle.is(':checked') && !$hideByRole.is(':checked')) {
            $hideStatus.prop('checked', false);
         }
      });

            
      // Initialize checked status and show related options for 'Always Hide for user role(s)' checkboxes
      $('.hide-by-role-checkbox').each(function () {
         const $checkbox = $(this);
         const menuId = $checkbox.data('menu-item-id');

         if ($checkbox.is(':checked')) {
            $('#hide-status-for-' + menuId).prop('checked', true);
            $('#all-roles-selected-opt-' + menuId).show();
            $('#all-roles-selected-radio-' + menuId).show();

            if ($('#all-roles-except-for-' + menuId).is(':checked') || $('#selected-roles-for-' + menuId).is(':checked')) {
               $('#hide-for-roles-' + menuId).show();
               $('#menu-req-capability-' + menuId).show();
            }
         }
      });
      
      // Handle changes on 'Always Hide for user role(s)' checkboxes
      $('#nxt-admin-menu-org').on('click', '.hide-by-role-checkbox', function () {
         const $checkbox = $(this);
         const menuId = $checkbox.data('menu-item-id');
         const menuType = $checkbox.data('menu-type'); // 'parent' or 'sub'

         // Utility: get the correct hidden menu object (parent or submenu)
         const hiddenMenus = menuType === 'parent' ? menuAlwaysHidden : submenuAlwaysHidden;

         // Ensure the object exists
         if (typeof hiddenMenus[menuId] === 'undefined') {
            hiddenMenus[menuId] = {};
         }

         // Set 'hide status' and reset 'hide until toggled'
         $('#hide-status-for-' + menuId).prop('checked', true);
         $('#hide-until-toggled-for-' + menuId).prop('checked', false);

         // Reset toggle-related flag
         hiddenMenus[menuId]['hide_by_toggle'] = false;

         if ($checkbox.is(':checked')) {
            hiddenMenus[menuId]['always_hide'] = true;
            $('#all-roles-selected-radio-' + menuId).show();

            // Show additional role options if specific role filters are selected
            if ($('#all-roles-except-for-' + menuId).is(':checked') || $('#selected-roles-for-' + menuId).is(':checked')) {
               $('#hide-for-roles-' + menuId).show();
               $('#menu-req-capability-' + menuId).show();

               if ($('#selected-roles-for-' + menuId).is(':checked')) {
                  hiddenMenus[menuId]['always_hide_for'] = 'selected-roles';
               } else if ($('#all-roles-except-for-' + menuId).is(':checked')) {
                  hiddenMenus[menuId]['always_hide_for'] = 'all-roles-except';
               }
            } else if ($('#all-roles-for-' + menuId).is(':checked')) {
               hiddenMenus[menuId]['always_hide_for'] = 'all-roles';
            }
         } else {
            // Uncheck and clear all role hiding flags and UI elements
            hiddenMenus[menuId]['always_hide'] = false;
            hiddenMenus[menuId]['always_hide_for'] = '';
            hiddenMenus[menuId]['which_roles'] = [];

            $('#all-roles-selected-radio-' + menuId).hide();
            $('#hide-for-roles-' + menuId).hide();
            $('#menu-req-capability-' + menuId).hide();

            if (!$('#hide-until-toggled-for-' + menuId).is(':checked')) {
               $('#hide-status-for-' + menuId).prop('checked', false);
            }
         }

         // Update hidden menus data in the corresponding hidden input
         if (menuType === 'parent') {
            document.getElementById('menu_always_hidden').value = JSON.stringify(menuAlwaysHidden);
         } else {
            document.getElementById('submenu_always_hidden').value = JSON.stringify(submenuAlwaysHidden);
         }
      });

      // Handle role scope selection changes (radio buttons)
      $('#nxt-admin-menu-org').on('change', '.all-selected-roles-radios', function () {
         const menuId = $(this).data('menu-item-id');
         const menuType = $(this).data('menu-type'); // 'parent' or 'sub'
         const value = this.value;

         // Reference correct hidden menu object based on type
         const hiddenMenus = menuType === 'parent' ? menuAlwaysHidden : submenuAlwaysHidden;

         if (value === 'all-roles-except' || value === 'selected-roles') {
            $('#hide-for-roles-' + menuId).show();
            $('#menu-req-capability-' + menuId).show();
            hiddenMenus[menuId]['always_hide_for'] = value;
         } else if (value === 'all-roles') {
            hiddenMenus[menuId]['always_hide_for'] = 'all-roles';
            hiddenMenus[menuId]['which_roles'] = [];
            hiddenMenus[menuId]['hide_by_toggle'] = false;
            
            $('#hide-until-toggled-for-' + menuId).prop('checked', false);
            $('#hide-for-roles-' + menuId).hide();
            $('#menu-req-capability-' + menuId).hide();
         }

         // Update the hidden input with serialized data
         if (menuType === 'parent') {
            document.getElementById('menu_always_hidden').value = JSON.stringify(menuAlwaysHidden);
         } else {
            document.getElementById('submenu_always_hidden').value = JSON.stringify(submenuAlwaysHidden);
         }
      });


      $('#nxt-admin-menu-org').on('click', '.role-checkbox', function() {
         const $menuItem = $(this).closest('[data-menu-item-id]');
         const menuId = $menuItem.data('menu-item-id');
         const menuType = $menuItem.data('menu-type'); // 'parent' or 'sub'
         const roleSlug = $(this).data('role');
         const isChecked = $(this).is(':checked');

         // Reference the correct hidden menu object based on menu type
         const hiddenMenus = (menuType === 'parent') ? menuAlwaysHidden : submenuAlwaysHidden;

         // Initialize 'which_roles' array if not defined
         if (!Array.isArray(hiddenMenus[menuId]?.which_roles)) {
            hiddenMenus[menuId] = hiddenMenus[menuId] || {};
            hiddenMenus[menuId].which_roles = [];
         }

         if (isChecked) {
            if (!hiddenMenus[menuId].which_roles.includes(roleSlug)) {
                  hiddenMenus[menuId].which_roles.push(roleSlug);
            }
         } else {
            const index = hiddenMenus[menuId].which_roles.indexOf(roleSlug);
            if (index > -1) {
                  hiddenMenus[menuId].which_roles.splice(index, 1);
            }
         }

         // Update the hidden input with serialized data
         const targetInputId = (menuType === 'parent') ? 'menu_always_hidden' : 'submenu_always_hidden';
         document.getElementById(targetInputId).value = JSON.stringify(hiddenMenus);
      });


      // Prepare constant to store IDs of new separator menu items
      var newSeparatorsInput = document.getElementById('change_menu_new_separators');
      if ( newSeparatorsInput && newSeparatorsInput.value ) {
         var newSeparators = JSON.parse(newSeparatorsInput.value); // object
      } else {
         var newSeparators = {}; // object
      }

      // Add a new menu separator item
      $('.admin-menu-actions-wrapper').on('click', '#nxt-add-menu-separator', function(e) {
         e.preventDefault();

         let separatorIndex = $('#nxt-admin-menu-org li[id^="separator"]').length + 1;

         // Ensure the separator ID is unique
         while (document.getElementById('separator' + separatorIndex)) {
            separatorIndex++;
         }

         nxtAddNewSeparator(menuAlwaysHidden, newSeparators, separatorIndex);
         $('#nxt-admin-menu-org').sortable('refresh'); // Reinitialize sortable behavior
      });

      // Remove a custom separator menu item
      $('#nxt-admin-menu-org').on('click', '.remove-menu-item', function() {
         const $separatorItem = $(this).closest('.menu-item.parent-menu-item');

         nxtDontSaveNewSeparator(newSeparators, $separatorItem);
         $separatorItem.remove();

         $('#nxt-admin-menu-org').sortable('refresh'); // Update sortable list after removal
      });

      // Initialize array to store IDs of hidden menu items
      let hiddenMenuItems = [];
      const hiddenMenuInput = document.getElementById('change_menu_hidden');

      if (hiddenMenuInput) {
         hiddenMenuItems = hiddenMenuInput.value.split(',').filter(Boolean);
      }

      // Toggle hidden state for parent menu items
      document.querySelectorAll('.parent-menu-hide-checkbox').forEach(checkbox => {
         checkbox.addEventListener('click', e => {
            const menuId = e.target.dataset.menuItemId;

            if (e.target.checked) {
               if (!hiddenMenuItems.includes(menuId)) {
                  hiddenMenuItems.push(menuId);
               }
            } else {
               hiddenMenuItems = hiddenMenuItems.filter(id => id !== menuId);
            }

            if (hiddenMenuInput) {
               hiddenMenuInput.value = hiddenMenuItems.join(',');
            }
         });
      });

      // Save button click: collect custom menu titles
      $('#nxt-amo-save-data').click(function(e) {
         e.preventDefault();

         const customMenuTitles = Array.from(document.querySelectorAll('.menu-item-custom-title')).map(input => {
            const menuId = input.dataset.menuItemId;
            const title = input.value;
            return `${menuId}__${title}`;
         });

         document.getElementById('menu_titles').value = customMenuTitles.join(',');
      });


   }); // End of $(document).ready()

   function nxtMenuAlwaysHidden(menuAlwaysHidden, menuItemObject) {
      const $item = $(menuItemObject);
      const menuId = $item.data('menu-item-id');
      const defaults = {
         menu_title: $item.data('menu-item-title'),
         original_menu_id: $item.data('menu-item-id-ori'),
         hide_by_toggle: false,
         always_hide: false,
         always_hide_for: '',
         which_roles: [],
         menu_url_fragment: $item.data('menu-url-fragment')
      };

      menuAlwaysHidden[menuId] = Object.assign({}, defaults, menuAlwaysHidden[menuId] || {});

      if (
         $(`#hide-until-toggled-for-${menuId}`).is(':checked') ||
         $(`#hide-by-role-for-${menuId}`).is(':checked')
      ) {
         $item.prop('checked', true);
      }

      $('#menu_always_hidden').val(JSON.stringify(menuAlwaysHidden));
   }

   function nxtSubmenuAlwaysHidden(submenuAlwaysHidden, submenuItemObject) {
      const $item = $(submenuItemObject);
      const submenuId = $item.data('menu-item-id');

      submenuAlwaysHidden[submenuId] = {
         menu_title: $item.data('menu-item-title'),
         original_menu_id: $item.data('menu-item-id-ori'),
         hide_by_toggle: false,
         always_hide: false,
         always_hide_for: '',
         which_roles: [],
         menu_url_fragment: $item.data('menu-url-fragment'),
         ...(submenuAlwaysHidden[submenuId] || {})
      };

      const $hideToggle = $(`#hide-until-toggled-for-${submenuId}`);
      const $hideByRole = $(`#hide-by-role-for-${submenuId}`);
      if ($hideToggle.is(':checked') || $hideByRole.is(':checked')) {
         $item.prop('checked', true);
      }

      $('#submenu_always_hidden').val(JSON.stringify(submenuAlwaysHidden));
   }

   function nxtAddNewSeparator(menuAlwaysHidden, newSeparators, separatorCount) {
      const $base = $('#separator1');
      let $newSeparator = $base.clone()
         .prop('id', 'separator' + separatorCount)
         .addClass('custom-menu-item')
         .attr('data-custom-menu-item', 'yes');

      $newSeparator.find('.menu-item-title').html(`~~ Separator-${separatorCount} ~~`);

      const updatedHtml = $newSeparator.html().replace(/separator1/g, 'separator' + separatorCount);
      $newSeparator.html(updatedHtml);

      $('#nxt-admin-menu-org').append($newSeparator);

      const $checkbox = $newSeparator.find('.hide-parent-menu-checkbox');
      nxtMenuAlwaysHidden(menuAlwaysHidden, $checkbox);
      nxtSaveNewSeparator(newSeparators, $checkbox);
   }

   // Add separator to list of new separators
   function nxtSaveNewSeparator(newSeparators, newSeparatorObject) {
      const $item = $(newSeparatorObject);
      const menuId = $item.data('menu-item-id');

      newSeparators[menuId] = {
         menu_id: menuId
      };

      $('#change_menu_new_separators').val(JSON.stringify(newSeparators));
   }

   // Remove separator from list of new separators
   function nxtDontSaveNewSeparator(newSeparators, newSeparatorObject) {
      const menuId = $(newSeparatorObject).attr('id');

      if (menuId && newSeparators.hasOwnProperty(menuId)) {
         delete newSeparators[menuId];
      }

      $('#change_menu_new_separators').val(JSON.stringify(newSeparators));
   }

})( jQuery );