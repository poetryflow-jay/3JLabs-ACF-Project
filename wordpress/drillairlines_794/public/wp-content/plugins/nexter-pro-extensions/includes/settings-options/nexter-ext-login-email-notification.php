<?php 
/*
 * Nexter Login Email Notification
 * @since 1.1.0
 */

defined('ABSPATH') or die();

class Nexter_Ext_Login_Email_Notification {

    /**
     * Constructor
     */
    public function __construct() {
		$extension_option = get_option( 'nexter_site_security' );

		if(!empty($extension_option) && isset($extension_option['email-login-notification']) && !empty($extension_option['email-login-notification']['switch']) && !empty($extension_option['email-login-notification']['values']) ){
			$exclude_ip = (!empty($extension_option['email-login-notification']['values']['exclude_ips'])) ? $extension_option['email-login-notification']['values']['exclude_ips'] : [];
			$ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'undefined';
			$ip_address = filter_var($ip_address, FILTER_VALIDATE_IP) ? $ip_address : null;
			$exclude_ip = array_map('trim', $exclude_ip);

			if(!in_array($ip_address, $exclude_ip)){
				add_action('wp_login', [ $this, 'nexter_ext_email_login_notification'], 10, 2);	
			}
		}
    }

	/*** Notification Send Function */
	function nexter_ext_email_login_notification($login, $user){
		$extension_option = get_option( 'nexter_site_security' );
		$loginAlertVal = $extension_option['email-login-notification']['values'];
		$get_alert = (!empty($loginAlertVal['get_alert'])) ? $loginAlertVal['get_alert'] : [];
		$alert_message = (!empty($loginAlertVal['alert_message'])) ? $loginAlertVal['alert_message'] : '';
		$custom_subject = (!empty($loginAlertVal['subject'])) ? $loginAlertVal['subject'] : '';

		if (!isset($user->roles) || !is_array($user->roles) || empty($user->roles)) return;
		$current_role = (array) $user->roles;
		foreach($current_role as $role) {
			if (in_array($role, $get_alert)) {
				$name = get_bloginfo('name');
				$admin_email = get_bloginfo('admin_email');
				
				if ( !empty( $custom_subject ) ) {
					$subject = str_replace( '[SiteName]', $name, $custom_subject );
				} else {
					$subject = ucwords( $role ) . ' ' . __( 'Login @ ', 'nexter-pro-extensions' ) . $name;
				}
				$subject = ucwords($role) .' '. __('Login @ ', 'nexter-pro-extensions') . $name;
				$message = self::nexter_ext_email_login_notification_message($login, $name, $role);
				$message .= esc_html($alert_message);
				wp_mail($admin_email, $subject, $message, 'From: '. $admin_email);
			}
		}
	}

	/*** Get Login User Details Function */
	function nexter_ext_email_login_notification_message($login, $name, $role) {	
		$date_format = get_option('date_format');
		$time_format = get_option('time_format');
		$format = $date_format .' @ '. $time_format;
		$date = current_datetime()->format($format);
		$message  = ucwords($role) .' '. __('logged in at ', 'nexter-pro-extensions') . $name . __(' on ', 'nexter-pro-extensions') . $date . "\n\n";
		$message .= __('User Name: ', 'nexter-pro-extensions') . $login . "\n";

		if(isset($_SERVER)){
			$request   = isset( $_SERVER['REQUEST_URI'] )        ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) )        : 'undefined';
			$query     = isset( $_SERVER['QUERY_STRING'] )       ? sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) )       : 'undefined';
			$referer   = isset( $_SERVER['HTTP_REFERER'] )       ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) )       : 'undefined';
			$agent     = isset( $_SERVER['HTTP_USER_AGENT'] )    ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) )    : 'undefined';
			$server    = isset( $_SERVER['SERVER_NAME'] )        ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) )        : 'undefined';
			$http_host = isset( $_SERVER['HTTP_HOST'] )          ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) )          : 'undefined';

			$ip_remote = isset( $_SERVER['REMOTE_ADDR'] )        ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) )        : 'undefined';
			$ip_client = isset( $_SERVER['HTTP_CLIENT_IP'] )     ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) )     : 'undefined';
			$ip_forwrd = isset( $_SERVER['HTTP_X_FORWARDED_FOR'])? sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR']) ) : 'undefined';

			
			$host_remote = (filter_var($ip_remote, FILTER_VALIDATE_IP)) ? sanitize_text_field(@gethostbyaddr($ip_remote)) : 'undefined';
			$host_client = (filter_var($ip_client, FILTER_VALIDATE_IP)) ? sanitize_text_field(@gethostbyaddr($ip_client)) : 'undefined';
			$host_forwrd = (filter_var($ip_forwrd, FILTER_VALIDATE_IP)) ? sanitize_text_field(@gethostbyaddr($ip_forwrd)) : 'undefined';
			
			$query = !empty($query) ? $query : '';
			
			$server_info =  array (
				'Request Url'	=> $request, 
				'Query'			=> $query, 
				'Referrer'		=> $referer, 
				'User Agent'	=> $agent, 
				'Server'		=> $server, 
				'Http Host'		=> $http_host, 
				'IP Remote'		=> $ip_remote, 
				'IP Client'		=> $ip_client, 
				'IP Forward'	=> $ip_forwrd,
				'Host Remote'	=> $host_remote, 
				'Host Client'	=> $host_client, 
				'Host Forward'	=> $host_forwrd,
			);

			foreach ($server_info as $key => $val) {
				$message .= $key .': '. $val . "\n";
			}
		}

		
		$message .= "\n" . __('Site URL: ', 'nexter-pro-extensions') . get_bloginfo('url') . "\n";
		return $message;
	}
	

}
new Nexter_Ext_Login_Email_Notification();
