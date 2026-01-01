<?php
/**
 * Manage Advanced Members for ACF Location Rules
 *
 * @since 	1.0
 *
 */
namespace AMem;

use AMem\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Config extends Module {

	protected $inc = '';
	protected $name = 'amem/config';

	/**
	 * @var array
	 */
	var $options = array();

	/**
	 * @var mixed|void
	 */
	public $email_notifications;

	/**
	 * @var mixed|void
	 */
	public $core_pages;

	/**
	 * @var mixed|void
	 */
	public $core_forms;

	/**
	 * @var array
	 */
	public $amem_default_form_meta = array();

	/**
	 * @var array
	 */
	public $amem_default_field_groups = array();

	/**
	 * @var array
	 */
	public $amem_default_fields = array();

	/**
	 * @var mixed|void
	 */
	public $default_settings;




	function __construct() {
		$this->init_variables();

		$this->email_notifications = apply_filters( 'amem/email/notifications', array(
			'welcome_email' => array(
				'key'           => 'welcome_email',
				'title'         => __( 'Account Welcome Email', 'advanced-members' ),
				'subject'       => 'Welcome to {site_name}!',
				'body'          => 'Hi {display_name},<br /><br />' .
													 'Thank you for signing up with {site_name}! Your account is now active.<br /><br />' .
													 'To login please visit the following url:<br /><br />' .
													 '{login_url} <br /><br />' .
													 'Your account e-mail: {email} <br />' .
													 'Your account username: {username} <br /><br />' .
													 'If you have any problems, please contact us at {admin_email}<br /><br />' .
													 'Thanks,<br />' .
													 '{site_name}',
				'description'   => __('Whether to send the user an email when their account is automatically approved', 'advanced-members'),
				'recipient'   	=> 'user',
				'default_active' => true
			),
			'checkmail_email' => array(
				'key'           => 'checkmail_email',
				'title'         => __( 'Account Activation Email', 'advanced-members' ),
				'subject'       => 'Please activate your account',
				'body'          => 'Hi {display_name},<br /><br />' .
													 'Thank you for signing up with {site_name}! To activate your account, please click the link below to confirm your email address:<br /><br />' .
													 '{user_activation_link} <br /><br />' .
													 'If you have any problems, please contact us at {admin_email}<br /><br />' .
													 'Thanks, <br />' .
													 '{site_name}',
				'description'   => __('Send the user an email when their account needs e-mail activation. (This option is always on)', 'advanced-members'),
				'recipient'   	=> 'user',
				'default_active' => true,
				'force_active' 	=> true,
			),
			'deletion_email' => array(
				'key'           => 'deletion_email',
				'title'         => __( 'Delete Account Email', 'advanced-members' ),
				'subject'       => 'Your account has been deleted',
				'body'          => 'Hi {display_name},<br /><br />' .
													 'This is an automated email to let you know your {site_name} account has been deleted. All of your personal information has been permanently deleted and you will no longer be able to login to {site_name}.<br /><br />' .
													 'If your account has been deleted by accident please contact us at {admin_email} <br />' .
													 'Thanks,<br />' .
													 '{site_name}',
				'description'   => __('Whether to send the user an email when their account is deleted', 'advanced-members'),
				'recipient'   => 'user',
				'default_active' => true
			),
			'resetpw_email' => array(
				'key'           => 'resetpw_email',
				'title'         => __( 'Password Reset Email', 'advanced-members' ),
				'subject'       => 'Reset your password',
				'body'          => 'Hi {display_name},<br /><br />' .
													 'We received a request to reset the password for your account. If you made this request, click the link below to change your password:<br /><br />' .
													 '{password_reset_link}<br /><br />' .
													 'If you didn\'t make this request, you can ignore this email <br /><br />' .
													 'Thanks,<br />' .
													 '{site_name}',
				'description'   => __('Send an email when users changed their password (This option is always on)', 'advanced-members'),
				'recipient'   => 'user',
				'default_active' => true,
				'force_active' 	=> true,
			),
			'changedpw_email' => array(
				'key'           => 'changedpw_email',
				'title'         => __( 'Password Changed Email', 'advanced-members' ),
				'subject'       => 'Your {site_name} password has been changed',
				'body'          => 'Hi {display_name},<br /><br />' .
													 'You recently changed the password associated with your {site_name} account.<br /><br />' .
													 'If you did not make this change and believe your {site_name} account has been compromised, please contact us at the following email address: {admin_email}<br /><br />' .
													 'Thanks,<br />' .
													 '{site_name}',
				'description'   => __('Whether to send the user an email when they requests to reset password (Recommended, please keep on)', 'advanced-members'),
				'recipient'   => 'user',
				'default_active' => true,
			),
			// Disabled
			// 'changedaccount_email' => array(
			// 	'key'           => 'changedaccount_email',
			// 	'title'         => __( 'Account Updated Email', 'advanced-members' ),
			// 	'subject'       => 'Your account at {site_name} was updated',
			// 	'body'          => 'Hi {display_name},<br /><br />' .
			// 										 'You recently updated your {site_name} account.<br /><br />' .
			// 										 'If you did not make this change and believe your {site_name} account has been compromised, please contact us at the following email address: {admin_email}<br /><br />' .
			// 										 'Thanks,<br />' .
			// 										 '{site_name}',
			// 	'description'   => __('Whether to send the user an email when they updated their account', 'advanced-members'),
			// 	'recipient'     => 'user',
			// 	'default_active'=> true
			// ),
			'notification_new_user' => array(
				'key'           => 'notification_new_user',
				'title'         => __( 'New User Notification', 'advanced-members' ),
				'subject'       => '[{site_name}] New user account',
				'body'          => '{display_name} has just created an account on {site_name}. To view their profile click here:<br /><br />' .
													 '{user_profile_link}<br /><br />' .
													 'Registered e-mail: {email}<br />' .
													 'Registered username: {username}',
				'description'   => __('Whether to receive notification when a new user account is created', 'advanced-members'),
				'recipient'   => 'admin',
				'default_active' => true,
			),
			'notification_deletion' => array(
				'key'           => 'notification_deletion',
				'title'         => __( 'Account Deletion Notification', 'advanced-members' ),
				'subject'       => '[{site_name}] Account deleted',
				'body'          => '{display_name} has just deleted their {site_name} account.',
				'description'   => __('Whether to receive notification when an account is deleted', 'advanced-members'),
				'recipient'   => 'admin'
			),
		));

		$this->core_forms = array(
			'register',
			'login',
			'account',
		);

		$this->amem_default_form_meta = array(
			'register' 	=> array(
				'form_key' => 'form_' . uniqid(),
				'select_type' => 'registration',
				'regist_role' => 'subscriber',
				'regist_status' => 'approve',
				'after_registration' => 'redirect_home',
				'description' => '',
				'amem_core_form' => 'register',
				'num_of_submissions' => 0,
				'form_num_of_views' => 0
			),
			'login' 		=> array(
				'form_key' => 'form_' . uniqid(),
				'select_type' => 'login',
				'after_login' => 'redirect_home',
				'description' => '',
				'amem_core_form' => 'login',
				'num_of_submissions' => 0,
				'form_num_of_views' => 0
			),
			'account' 	=> array(
				'form_key' => 'form_' . uniqid(),
				'select_type' => 'account',
				'description' => '',
				'amem_core_form' => 'account',
				'num_of_submissions' => 0,
				'form_num_of_views' => 0
			),
		);

		$this->amem_default_field_group = array(
			'location' => array(array(array(
				'param' 		=> 'amem_form',
				'operator'	=> '==',
				'value'			=> '',
			))),
			'position'							=> 'normal',
			'style'									=> 'default',
			'label_placement'				=> 'top',
			'instruction_placement'	=> 'label',
			'hide_on_screen'				=> '',
			'description'						=> '',
			'show_in_rest'					=> 0
		);
		$this->amem_default_fields = array(
			'register' => array(
				array(
					'title' 	=> __( 'Username', 'advanced-members' ),
					'type'		=> 'username',
					'content' => 'a:12:{s:10:"aria-label";s:0:"";s:4:"type";s:8:"username";s:12:"instructions";s:0:"";s:8:"required";i:1;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:10:"allow_edit";i:0;s:9:"maxlength";s:0:"";s:11:"placeholder";s:2:"ID";s:7:"prepend";s:0:"";s:6:"append";s:0:"";}',
				),
				array(
					'title' 	=> __( 'Password', 'advanced-members' ),
					'type'		=> 'user_password',
					'content' => 'a:13:{s:10:"aria-label";s:0:"";s:4:"type";s:13:"user_password";s:12:"instructions";s:0:"";s:8:"required";i:1;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:17:"password_strength";i:3;s:10:"force_edit";i:0;s:13:"edit_password";s:20:"Edit Password Button";s:20:"cancel_edit_password";s:6:"Cancel";s:11:"placeholder";s:8:"Password";s:7:"prepend";s:0:"";s:6:"append";s:0:"";}',
				),
				array(
					'title' 	=> __( 'Email', 'advanced-members' ),
					'type'		=> 'user_email',
					'content' => 'a:11:{s:10:"aria-label";s:0:"";s:4:"type";s:10:"user_email";s:12:"instructions";s:0:"";s:8:"required";i:1;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:15:"set_as_username";i:0;s:11:"placeholder";s:19:"Your E-mail Address";s:7:"prepend";s:0:"";s:6:"append";s:0:"";}',
				),
			),
			'login'	=> array(
				array(
					'title' 	=> __( 'Username or Email', 'advanced-members' ),
					'type'		=> 'username',
					'content' => 'a:12:{s:10:"aria-label";s:0:"";s:4:"type";s:8:"username";s:12:"instructions";s:0:"";s:8:"required";i:1;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:10:"allow_edit";i:0;s:9:"maxlength";s:0:"";s:11:"placeholder";s:2:"ID";s:7:"prepend";s:0:"";s:6:"append";s:0:"";}',
				),
				array(
					'title' 	=> __( 'Password', 'advanced-members' ),
					'type'		=> 'user_password',
					'content' => 'a:13:{s:10:"aria-label";s:0:"";s:4:"type";s:13:"user_password";s:12:"instructions";s:0:"";s:8:"required";i:1;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:17:"password_strength";i:3;s:10:"force_edit";i:0;s:13:"edit_password";s:20:"Edit Password Button";s:20:"cancel_edit_password";s:6:"Cancel";s:11:"placeholder";s:8:"Password";s:7:"prepend";s:0:"";s:6:"append";s:0:"";}',
				),
			),
			'account'	=> array(
				array(
					'title' 	=> __( 'Username', 'advanced-members' ),
					'type'		=> 'username',
					'content' => 'a:12:{s:10:"aria-label";s:0:"";s:4:"type";s:8:"username";s:12:"instructions";s:0:"";s:8:"required";i:1;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:10:"allow_edit";i:0;s:9:"maxlength";s:0:"";s:11:"placeholder";s:2:"ID";s:7:"prepend";s:0:"";s:6:"append";s:0:"";}',
				),
				array(
					'title' 	=> __( 'Email', 'advanced-members' ),
					'type'		=> 'user_email',
					'content' => 'a:11:{s:10:"aria-label";s:0:"";s:4:"type";s:10:"user_email";s:12:"instructions";s:0:"";s:8:"required";i:1;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:15:"set_as_username";i:0;s:11:"placeholder";s:19:"Your E-mail Address";s:7:"prepend";s:0:"";s:6:"append";s:0:"";}',
				),
			),
		);


		$this->core_pages = array(
			'register' => array( 'label' => __( 'Registration', 'advanced-members' )),
			'login' => array( 'label' => __( 'Login', 'advanced-members' )),
			'password-reset' => array('label' => __( 'Password Reset', 'advanced-members' )),
			'logout' => array('label' => __( 'Logout', 'advanced-members' )),
			'account' => array('label' => __( 'Account', 'advanced-members' )),
			'account-password' => array('label' => __( 'Change Password', 'advanced-members' )),
			'account-delete' => array('label' => __( 'Delete Account', 'advanced-members' )),
		);

		//default settings
		$this->default_settings = array(
		);

	}

	function get_core_pages($force=false) {
		if ( $force || !did_action( 'amem/core/pages' ) )// prevent duplicates
			$this->core_pages = apply_filters( 'amem/core/pages', $this->core_pages );
		return $this->core_pages;
	}

	/**
	 * Set variables
	 */
	function init_variables() {
		$this->options = get_option( 'amem_options', array() );
	}

	function get_after_url( $data ) {
		switch ($data) {
			case 'after_logout':
				$value = '';
				if( 'redirect_home' == $this->options['logout']['_after_logout'] ){
					$value = home_url();
				}elseif ( isset($this->options['logout']['_logout_redirect_url']) && !empty($this->options['logout']['_logout_redirect_url']) ) {
					$value = $this->options['logout']['_logout_redirect_url'];
				}
				return $value;
				break;
		}
	}

}

amem()->register_module('config', Config::getInstance());
