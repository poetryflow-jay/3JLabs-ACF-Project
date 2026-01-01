<?php
/**
 * Manage Advanced Members for ACF User
 *
 * @since 1.0
 *
 */
namespace AMem;

use AMem\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class User extends Module {
	// protected static $inc = '';
	protected $name = 'amem/user';

	/**
	 * @var int
	 */
	public $id = 0;

	/**
	 * @var null
	 */
	public $user_profile;

	/**
	 * @var null
	 */
	public $usermeta;

	public $data;

	protected $switched;

	protected $deleted_user_id;

	protected $backup = [];

	function __construct() {
		add_action( 'init', array( $this, 'set_user' ), 1 );

		add_action( 'init', array( $this, 'user_mail_active' ), 1 );

		if ( is_multisite() ) {
			add_action( 'wpmu_delete_user', [$this, 'delete_hook'], 10, 1 );
		} else {
			add_action( 'delete_user', [$this, 'delete_hook'], 10, 1 );
		}

		// Resend Activation Link
		add_filter( 'user_row_actions', [$this, 'user_row_actions'], 10, 2 );
		add_filter( 'handle_bulk_actions-users', [$this, 'handle_user_row_actions'], 10, 3 );
		add_action( 'admin_notices', [$this, 'row_action_notices'] );
	}

	function set_user( $user_id = null, $clean = false ) {
		if ( isset( $this->data ) ) {
			unset( $this->data );
		}

		$this->data = [];
		if ( $user_id ) {
			$this->id = $user_id;
		} elseif ( is_user_logged_in() && $clean == false ) {
			$this->id = get_current_user_id();
		} else {
			$this->id = 0;
		}

		if ( $this->id ) {
			$data = get_userdata( $this->id );
			$this->data = $this->toArray($data->data);
			$this->data['ID'] = $data->ID;
			$this->data['roles'] = (array)$data->roles;
			$this->core_data();
			$this->data = apply_filters( 'amem/user/data', $this->data, $this->id );
		} else {
			$this->data = null;
		}
	}

	function core_data() {
		if ( !isset($this->data['account_status']) )
			$this->data['account_status'] = get_user_meta( $this->id, 'account_status', true );

		switch ( $this->data['account_status'] ) {
			case 'awaiting_email_confirmation':
			$this->data['account_status_name'] = __( 'Awaiting E-mail Confirmation', 'advanced-members' );
			break;
			case 'awaiting_admin_review':
			$this->data['account_status_name'] = __( 'Pending Review', 'advanced-members' );
			break;
			case 'rejected':
			$this->data['account_status_name'] = __( 'Membership Rejected', 'advanced-members' );
			break;
			case 'inactive':
			$this->data['account_status_name'] = __( 'Membership Inactive', 'advanced-members' );
			break;
			case 'approved':
			default:
			$this->data['account_status'] = 'approved';
			$this->data['account_status_name'] = __( 'Approved', 'advanced-members' );
			break;
		}

		if ( !isset($this->data['wp_roles']) ) {
			if ( isset($this->data['roles']) && is_array($this->data['roles']) )
				$this->data['wp_roles'] = implode( ',', $this->data['roles'] );
		}
	}

	function get($key) {
		if ( $this->data ) {
			if ( isset($this->data[$key]) )
				return $this->data[$key];
			
			return get_user_meta( $this->id, $key, true );
		}
		return null;
		// if ( $this->user_profile && isset($this->user_profile[$key]) )
		// 	return $this->user_profile[$key];
		// return null;
	}

	/**
	 * Reset user data
	 *
	 * @param bool $clean
	 */
	function reset( $clean = false ) {
		$this->set_user( 0, $clean );
	}

	/**
	 * Converts object to array
	 *
	 * @param $obj
	 *
	 * @return array
	 */
	function toArray( $obj ) {
		if ( is_object( $obj ) ) {
			$obj = (array) $obj;
		}
		if ( is_array( $obj ) ) {
			$new = array();
			foreach ( $obj as $key => $val ) {
				$new[ $key ] = $this->toArray( $val );
			}
		} else {
			$new = $obj;
		}

		return $new;
	}

	function switch($user_id) {
		if ( $this->id == $user_id )
			return;

		// keep original
		if ( !$this->switched )
			$this->switched = $this->id;

		// backup current
		if ( !isset($this->backup[$this->id]) ) {
			foreach( ['id', 'data'/*, 'user_profile', 'usermeta'*/] as $k ) {
				$this->backup[$this->id][$k] = $this->$k;
			}
		}

		// set current with cache
		if ( isset($this->backup[$user_id]) ) {
			foreach( ['id', 'data'/*, 'user_profile', 'usermeta'*/] as $k ) {
				$this->$k = $this->backup[$user_id][$k];
			}
		} else {
			$this->set_user($user_id);
		}
	}

	function restore() {
		if ( !$this->switched )
			return;

		$user_id = $this->switched;
		if ( !isset($this->backup[$user_id]) ) {
			$this->set_user($user_id);
		} else {
			foreach( ['id', 'data'/*, 'user_profile', 'usermeta'*/] as $k ) {
				$this->$k = $this->backup[$user_id][$k];
			}
		}
		$this->switched = null;
	}

	function can($caps) {
		/** @todo convert AMem caps to WP cap */
		return user_can( $caps, $this->id);
	}

	/**
	 * @param $user_id
	 */
	function remove_cache( $user_id ) {
		delete_option( "amem_user_cachedata_{$user_id}" );
	}

	/**
	 * Email : Pending email
	 */
	function email_pending( $user_id = null ) {
		if ( $user_id )
			$this->switch($user_id);

		$this->registration_secretkey();
		$this->set_status( 'awaiting_email_confirmation' );

		//clear all sessions for email confirmation pending users
		$user = \WP_Session_Tokens::get_instance( $this->id );
		$user->destroy_all();

		amem()->mail->send( amem_user( 'user_email' ) , 'checkmail_email' );

		if ( $user_id )
			$this->restore();
	}

	/**
	 * Email : Password reset
	 */
	function email_password_reset( $user_id = null ) {
		if ( $user_id )
			$this->switch($user_id);

		$userdata = get_userdata( $this->id );

		$key = $this->maybe_generate_password_reset_key( $userdata );

		add_filter( 'amem/template/merge_tags', array( amem()->passwordreset, 'add_placeholder' ), 10, 1 );
		add_filter( 'amem/template/merge_tags/replace', array( amem()->passwordreset, 'add_replace_placeholder' ), 10, 1 );

		amem()->mail->send( $userdata->user_email, 'resetpw_email' );

		if ( $user_id )
			$this->restore();
	}


	/**
	 * Email : Password changed
	 *
	 * @param null|int $user_id
	 */
	function email_password_changed( $user_id = null ) {
		if ( $user_id ) {
			$this->switch($user_id);
		}

		amem()->mail->send( amem_user( 'user_email' ) , 'changedpw_email' );

		if ( $user_id )
			$this->restore();
	}


	function approve( $user_id = null ) {
		if ( $user_id )
			$this->switch( $user_id );

		// should get raw result form DB
		$status = get_user_meta( $this->id, 'account_status', true );
		if ( 'approved' === $status ) {
			return;
		}

		$status = amem_user('account_status');// get filtered

		if ( $status == 'awaiting_admin_review' ) {
			$userdata = get_userdata( $user_id );

			$this->maybe_generate_password_reset_key( $userdata );

			amem()->mail->send( amem_user( 'user_email' ), 'approved_email' );

		} else {
			amem()->mail->send( amem_user( 'user_email' ), 'welcome_email' );
		}

		// Admin notification
		amem()->mail->send( amem()->options->get('admin_email'), 'notification_new_user', array( 'admin' => true ) );


		$this->set_status( 'approved' );
		delete_user_meta( $this->id, 'registration_secretkey_hash' );
		delete_user_meta( $this->id, 'registration_secretkey_hash_expiry' );

		do_action( 'amem/user/status/approved/after', $this->id );
	}

	function delete( $user_id = null, $send_mail = true ) {
		if ( $user_id )
			$this->switch($user_id);

		$this->send_deleted_mail = $send_mail;

		if ( 'approved' != amem_user( 'account_status' ) || '0' != amem_user( 'user_status' ) || '' != amem_user( 'user_activation_key' ) ) {
			$this->send_deleted_mail = false;
		}

		if ( is_super_admin( $user_id ) ) {
			wp_die( esc_html__( 'Super administrators cannot be deleted.', 'advanced-members' ) );
		}

		if ( is_multisite() ) {

			if ( ! function_exists( 'wpmu_delete_user' ) ) {
				require_once ABSPATH . 'wp-admin/includes/ms.php';
			}

			wpmu_delete_user( $this->id );

		} else {

			if ( ! function_exists( 'wp_delete_user' ) ) {
				require_once ABSPATH . 'wp-admin/includes/user.php';
			}

			/** @todo reassign posts to someone... */
			$reassign = null;
			$reassign = apply_filters( 'amem/user/delete/reassign', $reassign, $user_id, $form, $args );

			wp_delete_user( $this->id, $reassign );

		}

		if ( $user_id )
			$this->restore();
	}

	function delete_hook( $user_id = null ) {
		if ( $user_id )
			$this->switch( $user_id );

		$this->deleted_user_id = $user_id;

		do_action( 'amem/user/delete', $this->id );

		/** @todo Add options and remove comments on delete */
		if ( amem()->options->get( 'delete_comments' ) ) {
			$user = get_user_by( 'id', $this->id );

			$comments = array_merge( get_comments( 'author_email=' . $user->user_email ), get_comments( 'user_id=' . $this->id ) );
			foreach ( $comments as $comment ) {
				wp_delete_comment( $comment->comment_ID, true );
			}
		}

		// send email notifications
		if ( $this->send_deleted_mail ) {
			amem()->mail->send( amem_user( 'user_email' ), 'deletion_email' );

			$admin_email = amem()->options->get( 'admin_email' );
			amem()->mail->send( $admin_email, 'notification_deletion', array( 'admin' => true ) );
		}
	}

	function set_status( $status ) {
		do_action( 'amem/user/status_set/before', $status, $this->id );

		$this->data['account_status'] = $status;

		$this->usermeta_update( 'account_status' );

		do_action( 'amem/user/status_set/after', $status, $this->id );
	}

	/**
	 * Set user secretkey
	 */
	function registration_secretkey() {
		$this->data['registration_secretkey_hash'] = wp_generate_password( 45, false );
		$this->usermeta_update( 'registration_secretkey_hash' );

		$expiry_time = (int) amem()->options->get( 'activation_link_expiry_time' );
		if ( $expiry_time ) {
			$expiry_time = $expiry_time * DAY_IN_SECONDS;
			$this->profile['registration_secretkey_hash_expiry'] = time() + $expiry_time;
			$this->usermeta_update( 'registration_secretkey_hash_expiry' );
		} else {
			unset( $this->profile['registration_secretkey_hash_expiry'] );
			delete_user_meta( $this->id, 'registration_secretkey_hash_expiry' );
		}
	}

	function user_row_actions($actions, $user) {
		if ( get_current_user_id() !== $user->ID && current_user_can( 'edit_user', $user->ID ) ) {
			$this->switch( $user->ID );
			$status = amem_user( 'account_status' );
			switch( $status ) {
				case 'awaiting_email_confirmation':
				$actions['amem_resend_activation'] = "<a class='amem_resend_activation' href='" . wp_nonce_url( "users.php?action=amem_resend_activation&amp;users=$user->ID", 'bulk-users' ) . "'>" . esc_html__( 'Resend Activation E-mail', 'advanced-members' ) . '</a>';
				break;
				default:
				break;
			}
			$this->restore();
		}
		return $actions;
	}

	function handle_user_row_actions($sendback, $action, $user_ids) {
		switch( $action ) {
			case 'amem_resend_activation':
			$sent = false;
			foreach( $user_ids as $user_id ) {
				$this->switch( $user_id );
				$status = amem_user( 'account_status' );
				if ( $status == 'awaiting_email_confirmation' ) {
					if ( get_current_user_id() !== $user_id && current_user_can( 'edit_user', $user_id ) ) {
						$this->email_pending();
						$sent = true;
					}
				}
				$this->restore();
			}
			if ( $sent ) {
				$sendback = admin_url( 'users.php?update=' . $action );
			}
			break;
			default:
			break;
		}
		return $sendback;
	}

	function row_action_notices() {
		if ( empty($_GET['update']) || strpos( sanitize_key($_GET['update']), 'amem_') !== 0 ) // phpcs:disable WordPress.Security.NonceVerification
			return;

		$current_screen = get_current_screen();
		if ( $current_screen->id != 'users' )
			return;

		switch( sanitize_key($_REQUEST['update']) ) { // phpcs:disable WordPress.Security.NonceVerification
			case 'amem_resend_activation':
			echo '<div id="message" class="notice is-dismissible updated"><p>' . esc_html__( 'Activation mail sent', 'advanced-members' ) . '</p></div>';
			break;
			default:
			break;
		}
	}

	/**
	 * @param \WP_User $userdata
	 *
	 * @return string|\WP_Error
	 */
	function maybe_generate_password_reset_key( $userdata ) {
		return get_password_reset_key( $userdata );
	}

	/**
	 * Update one key in user meta
	 *
	 * @param $key
	 */
	function usermeta_update( $key ) {
		// delete the key first just in case
		delete_user_meta( $this->id, $key );
		update_user_meta( $this->id, $key, $this->data[ $key ] );
	}

	function user_mail_active() {
		if ( isset( $_REQUEST['act'] ) && 'user_mail_active' === sanitize_key( $_REQUEST['act'] ) && isset( $_REQUEST['key'] ) && is_string( $_REQUEST['key'] ) && strlen( sanitize_key($_REQUEST['key']) ) == 45 && isset( $_REQUEST['login'] ) && !empty( $_REQUEST['login'] ) ) { // phpcs:disable WordPress.Security.NonceVerification

			$user_login = sanitize_text_field( $_REQUEST['login'] ); // phpcs:disable WordPress.Security.NonceVerification
			$user_id = username_exists($user_login);
			if ( !$user_id ) {
				wp_die( esc_html__( 'This activation link is not valid! Please check the login name', 'advanced-members' ) );
			}

			$secret_hash = get_user_meta( $user_id, 'registration_secretkey_hash', true );
			if ( empty( $secret_hash ) || strtolower( sanitize_text_field( $_REQUEST['key'] ) ) !== strtolower( $secret_hash ) ) { // phpcs:disable WordPress.Security.NonceVerification
				wp_die( esc_html__( 'This activation link has expired or has already been used.', 'advanced-members' ) );
			}

			$secret_hash_expiry = get_user_meta( $user_id, 'registration_secretkey_hash_expiry', true );
			if ( ! empty( $secret_hash_expiry ) && time() > $secret_hash_expiry ) {
				wp_die( esc_html__( 'This activation link has expired.', 'advanced-members' ) );
			}

			$this->approve( $user_id );

			do_action( 'amem/user/mail_active/after', $user_id );

			$redirect = amem_get_core_page( 'login', 'account_active' );
			$redirect = apply_filters( 'amem/user/mail_active/redirect', $redirect, $user_id );

			exit( wp_redirect( $redirect ) );
		}
	}

}

amem()->register_module('user', User::getInstance());