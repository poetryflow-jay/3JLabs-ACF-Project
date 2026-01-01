<?php 
/*
 * Redirect 404 Page Extension
 * @since 4.2.0
 */
defined('ABSPATH') or die();

 class Nexter_Ext_Redirect_404_Page {
    
    public static $redirect_opt = [];

    /**
     * Constructor
     */
    public function __construct() {
        $this->nxt_get_post_order_settings();
		add_filter( 'template_redirect', [$this, 'redirect_404_page'], PHP_INT_MAX );
    }

    private function nxt_get_post_order_settings(){
        
		if(isset(self::$redirect_opt) && !empty(self::$redirect_opt)){
			return self::$redirect_opt;
		}

		$option = get_option( 'nexter_extra_ext_options' );
		
		if(!empty($option) && isset($option['redirect-404-page']) && !empty($option['redirect-404-page']['switch']) && !empty($option['redirect-404-page']['values']) ){
			self::$redirect_opt = $option['redirect-404-page']['values'];
		}
        
	}

	public function redirect_404_page() {
        if ( ! is_404() || is_admin() || ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) ) {

            return;

        } elseif ( is_404() ) {

            // wp_safe_redirect( home_url(), 301 );
            
			if ( !empty( self::$redirect_opt ) ) {
				$redirect_url = site_url( '/' . self::$redirect_opt );
			} else {
				$redirect_url = site_url();
			} 

            header( 'HTTP/1.1 301 Moved Permanently');
            header( 'Location: ' . sanitize_url( $redirect_url ) );
            exit();

        }
    }
}

 new Nexter_Ext_Redirect_404_Page();