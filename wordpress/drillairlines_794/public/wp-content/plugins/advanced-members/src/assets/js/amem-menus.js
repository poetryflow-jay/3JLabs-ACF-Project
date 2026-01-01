(function ($) {

	$(document).on('ready', function() {
		if ( !$('.amem-nav-edit').length )
			return;

		amem_menus.init();
	});


	var amem_menus = {
		init: function () {
			$(document).on('change', '.amem-nav-edit .amem_nav_item-filter_users', this.onChange );
		},
		onChange: function(e) {
			var $el = $(e.currentTarget),
			val = $el.val();
			$el.closest('.amem-nav-edit').find('.amem-nav-roles')[val == 'logged_in' ? 'removeClass' : 'addClass']('acf-hidden');
		}
	}
})(jQuery);
