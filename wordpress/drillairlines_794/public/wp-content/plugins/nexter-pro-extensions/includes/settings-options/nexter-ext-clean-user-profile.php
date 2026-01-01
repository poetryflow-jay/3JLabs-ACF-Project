<?php 
/*
 * Clean User Profile Extension
 * @since 4.2.0
 */
defined('ABSPATH') or die();

 class Nexter_Ext_Pro_Clean_User_Profile {
    
    public static $clean_user_profile_opt = [];

    /**
     * Constructor
     */
    public function __construct() {
        $this->nxt_get_post_order_settings();
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 110 );
    }

    private function nxt_get_post_order_settings(){
        
		if(isset(self::$clean_user_profile_opt) && !empty(self::$clean_user_profile_opt)){
			return self::$clean_user_profile_opt;
		}

		$option = get_option( 'nexter_extra_ext_options' );
		
		if(!empty($option) && isset($option['clean-user-profile']) && !empty($option['clean-user-profile']['switch']) && !empty($option['clean-user-profile']['values']) ){
			self::$clean_user_profile_opt = $option['clean-user-profile']['values'];
		}
        
	}


	public static function admin_scripts(){
		$screen = get_current_screen();
		
		if ($screen->id === 'user-edit' || $screen->id === 'profile' ) {
			$clean_user_profile_opt = json_encode(self::$clean_user_profile_opt);

			$inline_script = "
			var nxtUserProfile_Hide = $clean_user_profile_opt;
			document.addEventListener('DOMContentLoaded', () => {
				if (typeof nxtUserProfile_Hide === 'undefined') {
					return;
				}
				const URLs = JSON.parse(JSON.stringify(nxtUserProfile_Hide));
				jQuery.each(URLs, function(key, val) {
					jQuery('.' + val).css('display', 'none');
				});
			});";
		
			// Add inline script to admin area
			wp_add_inline_script('jquery', $inline_script);
		}
	} 

}

 new Nexter_Ext_Pro_Clean_User_Profile();