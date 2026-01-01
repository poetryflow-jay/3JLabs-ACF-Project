(function ($) {
	var amem_admin = {
		init: function () {
			$(document).on( "click", ".amem-admin-page .copyable", this.onClickCopy );
			$(document).on( "click", ".amem-option-page .acf-tab-group a", this.onClickOptionTab );
			$(document).on( "click", ".amem-option-page a.add-role-rule", this.onClickAddRule.bind(this));
			$(document).on( "click", ".amem-option-page a.remove-location-rule", this.onClickRemoveRule.bind(this));
			$(document).on( "click", ".amem-option-page .email-notification-settings .handle ul, .amem-option-page .role-redirection-settings .handle ul", this.ToggleTr.bind(this) );
			$(document).on( "change", ".amem-option-page .email-notification-settings input:checkbox", this.changeEmailStatus.bind(this) );
			$(document).on( "change", ".amem-option-page .acf-field-apply-roles-redirection .acf-true-false input:checkbox", this.changeRoleRedirection.bind(this) );

			$(document).ready(function() {
				$('.amem-readonly input').prop('readonly', true);
			});
			$(document).on('click', '.amem-readonly input', function(){return false;});
		},
		changeRoleRedirection: function (e) {
			let is_active = $(e.target).is(':checked');
			var role_table = $(".amem-option-page .acf-settings-wrap .role-redirection-table");
			if( is_active ){
				role_table.removeClass("acf-hidden");
			}else{
				role_table.addClass("acf-hidden");
			}
			console.log( is_active );
		},
		changeEmailStatus: function (e) {
			var status = $(e.target).closest(".settings").prev(".handle").find("span.amem-email-status");
			let is_active = $(e.target).is(':checked');
			if( is_active ) {
				status.removeClass("amem-email-is-disable");
				status.addClass("amem-email-is-active");
			}else{
				status.removeClass("amem-email-is-active");
				status.addClass("amem-email-is-disable");
			}
		},
		ToggleTr: function (e) {
			var fields = $(e.target).closest(".handle").next(".settings");
			fields.slideToggle();
			$(e.target).closest(".email-notification-settings").toggleClass("open");
			$(e.target).closest(".role-redirection-settings").toggleClass("open");
			// let mailkey = $(e.target).parent().data('emailkey');
			// var target = $(e.target).parent().next(".email-notification-input");
			// target.fadeToggle(1000);
		},
		onClickCopy: function (e) {
    	e.stopPropagation();
      if (!navigator.clipboard || $(e.target).is('input')) return;

      // Find the value to copy depending on input or text elements.
      let copyValue;
      if ($(e.target).hasClass('acf-input-wrap')) {
        copyValue = $(e.target).find('input').first().val();
      } else {
        copyValue = $(e.target).text();
      }
      navigator.clipboard.writeText(copyValue).then(() => {
        $(e.target).closest('.copyable').addClass('copied');
        setTimeout(function () {
          $(e.target).closest('.copyable').removeClass('copied');
        }, 2000);
      });
    },

		onClickOptionTab: function (e) {
			e.preventDefault();
			var currentTab = $(e.target).closest('li');
			var isActive = currentTab.hasClass('active');
			var tabkey = currentTab.data('tabkey');
			if( !isActive ){
				currentTab.siblings('li.active').removeClass('active');
				currentTab.addClass('active');
				$(".amem-option-page .amem-settings:not(.acf-hidden)").addClass('acf-hidden');
				$(".amem-option-page .amem-settings.setting-"+tabkey).removeClass('acf-hidden');

			}
		},
		onClickAddRule: function (e) {
			e.preventDefault();
			// $el = $(e.target).closest('tr');
			$el = $(e.target).prev('table');
			$rule_type = $(e.target).data('add-rule');
			this.addRule($el ,$rule_type);
			// this.addRule($el);
		},
		onClickRemoveRule: function (e) {
			$el = $(e.target).closest('tr');
      this.removeRule($el);
    },
		addRule: function ($el, $rule_type) {
			if( $el.find('tr:last').length > 0 ){
				this.duplicate($el.find('tr:last'));
			}else{
				$.ajax({
          url: amem_options.ajaxurl,
          data: {
            action: 'amem/add_default_rule',
						ruletab: $rule_type,
          },
          type: 'post',
          dataType: 'json',
          success: this.test,
        });
			}

      // this.updateGroupsClass();
    },
		test: function(response) {
			if( response.success && response.data.content ){
				$el.find("tbody").append(response.data.content);
			}
			console.log($el);
			console.log(response);
		},
		duplicate: function (args) {
			if (args instanceof jQuery) {
	      args = {
	        target: args
	      };
	    }

	    // defaults
	    args = acf.parseArgs(args, {
	      target: false,
	      search: '',
	      replace: '',
	      rename: true,
	      before: function ($el) {},
	      after: function ($el, $el2) {},
	      append: function ($el, $el2) {
	        $el.after($el2);
	      }
	    });

	    // compatibility
	    args.target = args.target || args.$el;

	    // vars
	    var $el = args.target;
			// search
	    args.search = args.search || $el.attr('data-id');
	    args.replace = args.replace || acf.uniqid();

	    // before
	    // - allow acf to modify DOM
	    // - fixes bug where select field option is not selected
	    args.before($el);
	    acf.doAction('before_duplicate', $el);

	    // clone
	    var $el2 = $el.clone();

	    // rename
	    if (args.rename) {
	      acf.rename({
	        target: $el2,
	        search: args.search,
	        replace: args.replace,
	        replacer: typeof args.rename === 'function' ? args.rename : null
	      });
	    }

	    // remove classes
	    $el2.removeClass('acf-clone');
	    $el2.find('.ui-sortable').removeClass('ui-sortable');

	    // remove any initialised select2s prevent the duplicated object stealing the previous select2.
	    $el2.find('[data-select2-id]').removeAttr('data-select2-id');
	    $el2.find('.select2').remove();

	    // subfield select2 renames happen after init and contain a duplicated ID. force change those IDs to prevent this.
	    $el2.find('.acf-is-subfields select[data-ui="1"]').each(function () {
	      $(this).prop('id', $(this).prop('id').replace('acf_fields', acf.uniqid('duplicated_') + '_acf_fields'));
	    });

	    // remove tab wrapper to ensure proper init
	    $el2.find('.acf-field-settings > .acf-tab-wrap').remove();
	    // after
	    // - allow acf to modify DOM
	    args.after($el, $el2);
	    // acf.doAction('after_duplicate', $el, $el2);

	    // append
	    args.append($el, $el2);
	    /**
	     * Fires after an element has been duplicated and appended to the DOM.
	     *
	     * @since 1.0
	     *
	     * @param	jQuery $el The original element.
	     * @param	jQuery $el2 The duplicated element.
	     */
	    // acf.doAction('duplicate', $el, $el2);
	    // append
	    // acf.doAction('append', $el2);
	    // return
	    return $el2;
		},
		removeRule: function($tr) {
			$tr.remove();
		},
	}
	amem_admin.init();

	$(document).ready(function(){
		if( $(".amem-option-page .acf-tab-wrap").length > 0 ){
			acf.doAction("show");
		}
	});
})(jQuery);

jQuery(document).ready(function($){
	if ( typeof acf == 'undefined' )
		reuturn;

	amemFG = {
		Hfields: ['user_name', 'user_password', 'user_email', 'user_tos'],
		is_amem: function() {
			return $('.field-group-locations select.refresh-location-rule option[value="amem_form"]').filter(':selected').length;
		},
		apply: function($el, disable) {
			$el.find('.acf-field-setting-type select.field-type option').map(function(){
				var t = $(this);
				if ( $.inArray(t.val(), amemFG.Hfields) !== -1 ) {
					t.prop('disabled', disable);
				}
			});
			$el.trigger('change');
		},
		applyAll: function(disable) {
			$('#acf-field-group-fields .acf-field-object').each(function(){
				amemFG.apply( $(this), disable );
			});
		},
		init: function() {
			acf.addAction( 'add_field_object', function(o) {
				if ( amemFG.is_amem() )
					return;
				amemFG.apply(o.$el, true);
			});
			$(document).on('change', '.field-group-locations select.refresh-location-rule', function() {
				amemFG.applyAll( !amemFG.is_amem() );
			});
			if ( !amemFG.is_amem() ) {
				amemFG.applyAll(true);
			}
		}
	};

	if ( $(document.body).hasClass('post-type-acf-field-group') ) {
		amemFG.init();

		acf.add_action('ready', function(){
			if ( window.location.hash.length > 1 && window.location.hash.indexOf('#amem-tab-') > -1 ) {	
				// remove #amem-tab-
				let hash = decodeURIComponent( window.location.hash.substring(10) );
				setTimeout( function() {
					$('.acf-tab-group li a[data-settings-type="'+hash+'"]').first().trigger('click');
				}, 30 );
			}
		});
	}

	if ( $(document.body).hasClass('nav-menus-php') ) {
		$('.amem-nav-mode').each( function() {
			if ( $(this).find('select').val() == 'logged_in' ) {
				$(this).closest('.amem-nav-edit').find('.amem-nav-roles').removeClass('acf-hidden');
			} else {
				$(this).closest('.amem-nav-edit').find('.amem-nav-roles').addClass('acf-hidden');
			}
		});
		$( document.body ).on('change', '.amem-nav-mode select', function() {
			if ( $(this).val() == 'logged_in' ) {
				$(this).closest('.amem-nav-edit').find('.amem-nav-roles').removeClass('acf-hidden');
			} else {
				$(this).closest('.amem-nav-edit').find('.amem-nav-roles').addClass('acf-hidden');
			}
		});

		if ( typeof wp.customize != 'undefined' ) {
			$( document.body ).on('change', '.amem-nav-mode select, .amem-nav-roles input', function() {
				// wp.customize.state( 'saved' ).set( false );
				title = $(this).closest('.menu-item-settings').find('input[name="menu-item-title"]');
				title.val( title.val() + ' ' ).trigger('change');
			});
		}
	}

});
