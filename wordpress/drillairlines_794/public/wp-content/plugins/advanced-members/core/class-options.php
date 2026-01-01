<?php
/**
 * Manage Advanced Members for ACF User
 *
 * @since  1.0
 *
 */
namespace AMem;
use AMem\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Options extends Module {
	protected $name = 'amem/options';
	/**
	 * @var array
	 */
	public $options = array();
	public $modules = array();

	/**
	 * @var array
	 */
	public $permalinks = array();

	public $emails;

	function __construct() {
		$this->init();

		add_action( 'update_option_amem_modules', [$this, 'module_enable_disable'], 10, 2 );

	}

	function init( $force=false ) {
		$this->init_options();
		$this->get_core_pages();
		$this->emails = $this->get_emails($force);
	}

	function init_options() {
		$this->options = get_option( 'amem_options', array() );

		$def_modules = [
			// '_use_menu' => true,
			// '_use_redirects' => true,
			// '_use_adminbar' => true,
			// 'pro/xxx' => true,
		];
		$this->modules = get_option( 'amem_modules', array() );
		$this->modules = array_merge( $def_modules, $this->modules );

		$this->migrate_options();
	}

	function migrate_options() {
		if ( isset($this->options['roles']['apply_roles_redirection']) ) {
			if ( !isset($this->options['redirect']['apply_roles_redirection']) ) {
				$this->options['redirect']['apply_roles_redirection'] = $this->options['roles']['apply_roles_redirection'];
			}
			unset( $this->options['roles'] );
			update_option( 'amem_options', $this->options );
		}

		if ( !empty($this->options['load_theme']) && (is_bool($this->options['load_theme']) || '1' === $this->options['load_theme']) )
			$this->options['load_theme'] = 'default';
	}

	function sanitizers() {
		$map = [
			'core_register' => 'int',
			'core_login' => 'int',
			'core_password-reset' => 'int',
			'core_logout' => 'int',
			'core_account' => 'int',
			'core_account-password' => 'int',
			'core_account-delete' => 'int',
			'core_social_login' => 'int',
			'ajax_submit' => 'bool',
			'load_theme' => 'text',
			'account' => [
				'show_current_passwd' => 'bool',
				'delete_account_text' => 'textarea',
				'delete_account_label' => 'text',
			],
			'redirect' => [
				'_after_registration' => 'text',
				'_after_login' => 'text',
				'_after_logout' => 'text',
				'_after_account_delete' => 'text',
				'apply_roles_redirection' => 'bool',
				'roles' => 'array_text',// array
			],
			'adminbar' => [
				'roles' => 'bool',// array
			],
			'restriction' => [
				'post_types' => 'array_text',
				'redirect_login' => 'bool',
				'redirect_custom' => 'bool',
				'show_message' => 'bool',
				'show_excerpt_message' => 'bool',
				'message' => 'html',
			],
			'activation_link_expiry_time' => 'int',
			'override_pass_changed_email' => 'bool',
			'recaptcha' => [
				'site_key' => 'text',
				'secret_key' => 'text',
				'key_verified' => 'int',
			],
		];

		return apply_filters( 'amem/admin/options/sanitizers', $map );
	}

	/**
	 * Get option value
	 *
	 * @param $option_key
	 * @return mixed|string|void
	 */
	function get( $option_key, $default=null ) {
		$option_arr = explode("/", $option_key);
		if ( 1 == count($option_arr) && isset( $this->options[ $option_key ] ) ) {
			return apply_filters( "amem/option/get/{$option_key}", $this->options[ $option_key ] );
		}

		if ( 1 < count($option_arr) && isset( $this->options[ $option_arr[0] ] ) ) {
			$get_option = $this->options;
			foreach ($option_arr as $key) {
				if (isset($get_option[$key])) {
	        $get_option = $get_option[$key];
		    } else {
	        $get_option = null;
	        break;
		    }
			}

			return apply_filters( "amem/option/get/{$option_key}", $get_option );
		}

		switch ( $option_key ) {
			case 'site_name':
				return get_bloginfo( 'name' );
				break;
			case 'admin_email':
				return get_bloginfo( 'admin_email' );
				break;
			default:
				return $this->get_default($option_key, $default);
				break;
		}

		return '';
	}

	/**
	 * Get default option value
	 * @todo Provide option default filter hook, after set up option cache
	 *
	 * @param $option_key
	 * @return mixed
	 */
	function get_default( $option_key, $default=null ) {
		$defaults = [
			'redirection/_after_logout' => 'redirect_home',
			'redirection/_after_login' => 'redirect_home',
			'redirection/_after_registration' => 'redirect_home',
			'redirection/_after_account_delete' => 'redirect_home',
			'restriction/post_types' => ['page'],
			'restriction/methods' => ['redirect_login' => 1],
			'load_theme' => 'default',
			'restriction/message' => __( 'We\'re sorry, but you don\'t currently have access to this content.', 'advanced-members' ),
			// 'account/use_password' => true,
		];
		if ( null === $default && isset($defaults[$option_key]) ) {
			$default = $defaults[$option_key];
		}
		// $default = apply_filters( "amem/option/default/{$option_key}", $default );
		return $default;
	}

	/**
	 * Get core page ID
	 *
	 * @param string $key
	 *
	 * @return mixed|void
	 */
	function get_core_page_id( $key ) {
		return apply_filters( 'amem/option/core_page_id', 'core_' . $key );
	}

	/**
	 * Get Core Pages
	 *
	 * @return array
	 */
	function get_core_pages() {
		$core_pages = array_keys( amem()->config->get_core_pages() );
		if ( empty( $core_pages ) ) {
			return $this->permalinks;
		}

		foreach ( $core_pages as $page_key ) {
			// bypass duplicates
			if ( isset($this->permalinks[$page_key]) )
				continue;
			$page_option_key = $this->get_core_page_id( $page_key );
			$this->permalinks[ $page_key ] = $this->get( $page_option_key );
		}

		return $this->permalinks;
	}

	function default_emails() {
		$emails = amem()->config->email_notifications;
		$options = [];
		foreach ( $emails as $k => $email ) {
			$force_active = !empty($email['force_active']);
			if ( $force_active )
				$is_active = true;
			else
				$is_active = (isset($email['default_active']) ? $email['default_active'] : false);
			$options[$email['key']] = [
				'is_active' => $is_active,
				'force_active' => $force_active,
				'subject' => $email['subject'],
				'body' => $email['body'],
			];
		}
		return $options;
	}

	function get_emails($force=false) {
		if ( !$force && !empty($this->emails) )
			return $this->emails;

		$emails = [];
		if ( isset($this->options['email']) ) {
			$emails = (array) $this->options['email'];
		}

		if ( !isset($emails['admin_email']) ) $emails['admin_email'] = get_option('admin_email');
		if ( !isset($emails['mail_from']) ) $emails['mail_from'] = get_bloginfo('name');
		if ( !isset($emails['mail_from_addr']) ) $emails['mail_from_addr'] = get_option('admin_email');

		$defaults = $this->default_emails();
		foreach ( $defaults as $k => $d ) {
			if ( !isset($emails[$k] ) )
				$emails[$k] = $d;
			foreach ( $d as $j => $b ) {
				if ( !isset($emails[$k][$j]) )
					$emails[$k][$j] = $b;
			}
			if ( $d['force_active'] )
				$emails[$k]['is_active'] = 1;
		}

		$this->emails = $emails;

		return $this->emails;
	}

	function find_core( $post_type, $meta_key, $meta_value ) {
		$posts = get_posts(
			array(
				'post_type' => $post_type,
				'fields'		=> 'ids',
				'meta_key' => $meta_key,
				'meta_value' => $meta_value
			)
		);
		if ( isset( $posts[0] ) && ! empty( $posts ) )
			return $posts[0];
		return false;
	}

	/**
	 * Get option value
	 *
	 * @param $option_key
	 * @return mixed|string|void
	 */
	function getmodule( $module_key, $default=null ) {
		$module_arr = explode("/", $module_key);
		if ( 1 == count($module_arr) && isset( $this->modules[ $module_key ] ) ) {
			return apply_filters( "amem/option/getmodule/{$module_key}", $this->modules[ $module_key ] );
		}

		if ( 1 < count($module_arr) && isset( $this->modules[ $module_arr[0] ] ) ) {
			$get_module = $this->modules;
			foreach ($module_arr as $key) {
				if (isset($get_module[$key])) {
	        $get_module = $get_module[$key];
		    } else {
	        $get_module = '';
	        break;
		    }
			}
			return apply_filters( "amem/option/getmodule/{$module_key}", $get_module );
		}
	}

	function set( $option_key, $value=null ) {
		$option_arr = explode("/", $option_key);
		$value = apply_filters( "amem/option/set/{$option_key}", $value );

		if ( 1 == count($option_arr) /*&& isset( $this->options[ $option_key ] )*/ ) {
			$this->options[ $option_key ] = $value;

			update_option( 'amem_options', $this->options );
			return true;
		}

		if ( 1 < count($option_arr) /*&& isset( $this->options[ $option_arr[0] ] )*/ ) {
			$this->_set( $this->options, $option_arr, $value );

			update_option( 'amem_options', $this->options );
			return true;
		}
	}

	protected function _set(&$array, $keys, $value) {
		if ( !is_array( $keys ) )
		  $keys = explode("/", $keys);

	  $current = &$array;
	  foreach($keys as $key) {
	    $current = &$current[$key];
	  }
	  $current = $value;
	}

	function module_enable_disable($old, $new) {
		$old_onoff = $this->_filter_module_onoff_only($old);
		$new_onoff = $this->_filter_module_onoff_only($new);

		$changed = array_diff_assoc( $old_onoff, $new_onoff );
		if ( $changed ) {
			foreach ( $changed as $key => $bool ) {
				$module = substr( $key, 0, 5 );// remove use_
				do_action( "amem/module/{$module}/" . ( $bool ? 'enabled' : 'disabled' ) );
			}
		}
	}

	protected function _filter_module_onoff_only($options) {
		return array_filter( (array)$options, function($v, $k) {
			return strpos($k, '_use_') === 0;
		}, ARRAY_FILTER_USE_BOTH );
	}

}

amem()->register_module('options', Options::getInstance());
