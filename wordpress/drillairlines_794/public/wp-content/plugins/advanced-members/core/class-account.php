<?php
/**
 * Class Account
 *
 * @since 1.0
 * @package Advanced Members for ACF \ Core
 * 
 */
namespace AMem;
use AMem\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Account extends Module {

	/** @var array */
	protected $done = array();

	/** @var */
	public $tabs;

	/** @var string */
	public $current_tab;

	/** @var array */
	public $displayed_fields = array();

	/** @var array */
	public $tab_output = array();

	public $form_id = 0;

	public $form_key = 'form_amem_account';

	function __construct() {
		add_filter( 'amem/form/args/type=account', [$this, 'form_args'], 10, 2 );
		add_filter( 'amem/form/acf_data/type=account', [$this, 'acf_form_data'], 10, 3 );

		add_shortcode( 'advanced-members-account', array( $this, 'shortcode' ) );
		add_shortcode( 'advanced-members-account-password', array( $this, 'shortcode_password' ) );
		add_shortcode( 'advanced-members-account-delete', array( $this, 'shortcode_delete' ) );

		add_action( 'amem/form/create_submission/pre', [$this, 'register_form'] );

		$this->detect_current_tab();
	}

	function form_args($args, $form) {
		// Account form should remove CUSTOM username and password fields
		if ( is_string($args['exclude_fields']) ) {
			$args['exclude_fields'] = expllide( ',', $args['exclude_fields'] );
			$args['exclude_fields'] = array_map( 'trim', $args['exclude_fields'] );
		}
		$exclude_fields = [/*'username', 'user_login', */'delete_user', 'user_password', 'user_password_confirm'];
		if ( !empty( $args['exclude_fields']) ) {
			$args['exclude_fields'] = is_string($args['exclude_fields']) ? explode(',', $args['exclude_fields']) : [];
		}
		$args['exclude_fields'] = array_filter( array_merge( (array)$args['exclude_fields'], $exclude_fields ) );
		$args['exclude_fields'] = array_unique( $args['exclude_fields'] );

		return $args;
	}

	function acf_form_data($data, $form, $args) {
		if ( $form['type'] == 'account' && amem()->user->id > 0 ) {
			$data['post_id'] = "user_" . amem()->user->id;			
		}
		return $data;
	}

	function detect_current_tab() {
		if ( !$this->current_tab && !empty($_POST['_amem_account_tab']) ) // phpcs:disable WordPress.Security.NonceVerification
			$this->current_tab = sanitize_text_field( $_POST['_amem_account_tab'] ); // phpcs:disable WordPress.Security.NonceVerification
	}

	function get_current_tab() {
		return $this->current_tab;
	}

	function _get_form_id() {
		$roles = (array) amem_user('wp_roles');
		$role = reset($roles);

		if ( !$role )
			$role = 'subscriber';

		$form_id = amem()->options->get('accform/default');
		foreach ( (array) amem()->options->get('accform/rules') as $rule ) {
			if ( !is_array($rule) ) continue;
			if ( in_array($rule['role'], $roles) && $rule['value'] > '0' ) {
				$form_id = $rule['value'];
				break;
			}
		}

		$form_id = apply_filters( 'amem/account/form_id', (int) $form_id, $roles );

		return $form_id;
	}

	function get_form_id($force=false) {
		if ( !is_user_logged_in() )
			return 0;

		if ( !$force && $this->form_id )
			return $this->form_id;

		if ( $this->current_tab != 'general' ) {
			return 'form_amem_account';
		}

		$form_id = $this->_get_form_id();

		if ( !$form_id ) {
			$form_id = 'form_amem_account';
		}
		$this->form_id = $form_id;

		return $form_id;
	}

	/**
	 * Init tabs for account.
	 * 
	 * @since 1.0
	 */
	function init_tabs( $args ) {
		$this->tabs = $this->get_tabs();

		ksort( $this->tabs );

		$tabs_structured = array();
		foreach ( $this->tabs as $k => $arr ) {
			foreach ( $arr as $id => $info ) {
				if ( ! empty( $args['tab'] ) && $id !== $args['tab'] ) {
					continue;
				}

				// $output = $this->get_tab_fields( $id, $args );
				// @todo setup tab fields
				$output = true;

				if ( ! empty( $output ) ) {
					$tabs_structured[ $id ] = $info;
				}
			}
		}
		$this->tabs = $tabs_structured;
	}

	/**
	 * Get account page tabs
	 *
	 * @since 1.0
	 */
	function get_tabs() {
		$tabs                 = array();
		if ( is_user_logged_in() ) {
			$tabs[100]['general'] = array(
				'icon'         => null,
				'title'        => __( 'Account', 'advanced-members' ),
				'submit_text' => __( 'Update Account', 'advanced-members' ),
			);

			// if ( amem()->options->get('account/use_password') ) {
				$tabs[200]['password'] = array(
					'icon'         => null,
					'title'        => __( 'Change Password', 'advanced-members' ),
					'submit_text' => __( 'Update Password', 'advanced-members' ),
				);
			// }

			// if ( amem()->options->get('account/use_delete') ) {
				// If user cannot delete profile hide delete tab.
				// if ( amem()->user->can( 'delete_profile' ) || amem()->user->can( 'delete_users' ) ) {
					$tabs[99999]['delete'] = array(
						'icon'         => null,
						'title'        => __( 'Delete Account', 'advanced-members' ),
						'submit_text' => __( 'Delete Account', 'advanced-members' ),
						'submit_btn_atts' => [ 'class' => 'amem-btn-delete' ],
					);
				// }
			// }
		} else {
			$tabs[100]['logged_out'] = array(
				'icon'         => null,
				'title'        => __( 'Logged Out', 'advanced-members' ),
				'submit_text' => null,
			);
		}

		return apply_filters( 'amem/account/tabs', $tabs );
	}

	function shortcode_password( $args=[] ) {
		$args = shortcode_atts(
			array(
				'template' => 'account',
				'mode'     => 'account',
				'form_id'  => '',
			),
			$args,
			'amemembers_account_password'
		);

		$args['tab'] = 'password';

		return $this->shortcode( $args );
	}

	function shortcode_delete( $args=[] ) {
		$args = shortcode_atts(
			array(
				'template' => 'account',
				'mode'     => 'account',
				'form_id'  => '',
			),
			$args,
			'amemembers_account_delete'
		);

		$args['tab'] = 'delete';

		return $this->shortcode( $args );
	}

	function shortcode( $args = array() ) {
		$ajax = isset($args['ajax']) ? $args['ajax'] : null;
		$args = shortcode_atts(
			array(
				'template' => 'account',
				'mode'     => 'account',
				'form_id'  => '',
				'tab'      => 'general',
			),
			$args,
			'amemembers_account'
		);

		if ( !is_null($ajax) )
			$args['ajax'] = $ajax;

		$account_hash = md5( wp_json_encode( $args ) );

		$singleton = apply_filters( 'amem/account/singleton', true, $args );
		if ( true === $singleton && in_array( $account_hash, $this->done, true ) ) {
			return '';
		}

		if ( ! is_user_logged_in() ) {
			$args['tab'] = 'logged_out';
		}

		// reset form_id
		$this->form_id = 0;
		$this->form_key = 'form_amem_account';

		ob_start();

		if ( ! empty( $args['tab'] ) ) {

			if ( 'account' === $args['tab'] ) {
				$args['tab'] = 'general';
			}

			$this->current_tab = $args['tab'];
			if ( !is_user_logged_in() || $this->get_form_id() == 'form_amem_account' )
				$this->register_form();

			$this->init_tabs( $args );

			if ( ! empty( $this->tabs[ $args['tab'] ] ) ) { ?>
				<div class="amem amem-account-tab">
					<?php
					$tab_data = $this->tabs[ $args['tab'] ];
					if ( $args['tab'] == 'general' ) {
						$args['submit_text'] = !empty($args['submit_text']) ? $args['submit_text'] : $tab_data['submit_text'];
					} else {
						$args['submit_text'] = $tab_data['submit_text'];
					}
					if ( isset($tab_data['submit_btn_atts']) ) {
						$args['submit_btn_atts'] = $tab_data['submit_btn_atts'];
					}
					$this->render_account_tab( $args['tab'], $tab_data, $args );
					?>
				</div>
				<?php
			}
		} else {
			/** @todo or not... Support One-Page account tabs */
			// $this->init_tabs( $args );

			// $this->current_tab = apply_filters( 'amem/account/default_tab', $this->current_tab, $args );

			// do_action( "amem/account/before" . $args['tab'], $args );

			// amem()->template->template_load( $args['template'], $args );
		}

		$output = ob_get_clean();

		$this->done[] = $account_hash;

		return $output;
	}

	/**
	 * Render Account Tab HTML
	 */
	function render_account_tab( $tab_id, $tab_data, $args ) {

		do_action( "amem/account/before_content/{$tab_id}", $args, $tab_data );

		do_action( "amem/account/content/{$tab_id}", $args, $tab_data );

		do_action( "amem/account/after_content/{$tab_id}", $args, $tab_data );

	}

	function register_form( $form_key = null ) {
		static $registered;

		if ( isset($registered) )
			return;

		if ( $form_key && $form_key != $this->form_key )
			return;

		$form = array(
			'key' => $this->form_key,
			'post_id' => false,
			'title' => '',//__( 'Password Reset', 'advanced-members' ),
			'type' => 'account',
			'data' => array(
				'ajax' => amem()->options->get('ajax_submit'),
				'description' => '',
				'success_message' => '',
				'after_submit' => 'reload_current',
				'redirect_url' => '',
				'tab' => $this->current_tab,
			),
		);

		// Follow general form settings
		if ( $_form_id = $this->_get_form_id() ) {
			if ( get_post_meta( $_form_id, 'ajax_override', true ) ) {
				$form['data']['ajax'] = (bool) get_post_meta( $_form_id, 'ajax', true );
			}
			if ( $this->current_tab == 'delete' || (!empty($_POST['_amem_account_tab']) && 'delete' == sanitize_key($_POST['_amem_account_tab'])) ) { // phpcs:disable WordPress.Security.NonceVerification
				$form['data']['success_message'] = get_post_meta( $_form_id, "account_deleted_message", true );
				if ( !$form['data']['success_message'] )
					$form['data']['success_message'] = __( 'Account deleted', 'advanced-members' );

				/* Moved to Redirects module */
				// if ( !empty($form['data']['redirect_override']) ) {
				// 	$form['data']['after_submit'] = get_post_meta($_form_id, "after_account_delete", true);
				// 	$form['data']['redirect_url'] = get_post_meta($_form_id, "account_delete_redirect_url", true);
				// } else {
				// 	$form['data']['after_submit'] = amem()->options->get('redirect/_after_account_delete');
				// 	$form['data']['redirect_url'] = amem()->options->get('redirect/account_delete_redirect_url');
				// }
				if ( $form['data']['after_submit'] == 'success_message' ) {// DEV compatibility
					// Deleted user already loggedout so we should redirect to somewhere else.
					// $form['data']['after_submit'] = 'just_success_message';
					$form['data']['after_submit'] = 'redirect_login';
				}
			}
		}

		amem()->local->add_form($form);

		$registered = true;
	}

}

amem()->register_module('account', Account::getInstance());