<?php
/**
 * Handle Password Reset Form
 *
 * @since 1.0
 * @package Advanced Members for ACF \ Forms
 * 
 */
namespace AMem\Action;
use AMem\Action;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

class PasswordReset extends Action {

	public $priority = 9;

	protected $user;

	function __construct() {
		$this->disable_ajax();

		add_action( 'amem/form/validate/type=passwordreset', array( $this, 'validate'), 10, 3 );
		add_action( 'amem/form/validate/type=passwordreset', array( $this, 'check_fields'), 10, 3 );

		add_action( 'amem/form/submit/type=passwordreset', array( $this, 'handle_form'), 10, 3 );

		add_action( 'amem/user/passwordreset', [$this, 'process'], 10, 3 );

		add_filter( 'amem/form/button_html/type=passwordreset', array( $this, 'button' ), 10, 4 );

		add_action( 'amem/form/hidden_fields/type=passwordreset', [$this, 'hidden_fields'] );

		add_filter( 'amem/form/from_local/type=passwordreset', [$this, 'from_local'] );
		// add_filter( 'amem/form/error_message', [$this, 'error_message'] );
	}

	function get_mode() {
		return amem()->passwordreset->get_mode();
	}

	function from_local($form) {
		$form['amem'] = array(
			'username' => 'username',
			'user_password' => 'user_password',
		);

		$form['record'] = [];

		return $form;
	}

	function error_message($message) {
		// Do not append to existing
		if ( '' == $message && is_user_logged_in() ) {
			$message = amem()->errors->text('logged_in');
		}

		return $message;
	}

	function hidden_fields($form) {
		if ( $this->get_mode() == 'change' ) {
			echo '<input type="hidden" name="_amem_password_change" value="1">';
		} else {
			echo '<input type="hidden" name="_amem_password_reset" value="1">';
		}
	}

	function validate( $form, $args, $fields ) {
		if ( is_user_logged_in() ) {
			amem_add_error( 'field_amem_errors', amem()->errors->text('logged_in') );
		}
	}

	function get_user($user_name) {
		$user = false;
		// user_login with email may exits, so check user_login first
		if ( $user_id = username_exists( $user_name ) ) {
			$user = get_user_by('id', $user_id);
		} elseif ( $user_id = email_exists( $user_name ) ) {
			$user = get_user_by('id', $user_id);
		}

		return $user;
	}

	/**
	 * Check password reset fields
	 */
	function check_fields( $form, $args, $fields ) {
		if ( amem()->passwordreset->get_mode() == 'change' )
			return;

		if ( !isset($this->user) ) {
			$user_name = $form['record']['username'];
			$this->user = $this->get_user($user_name);
		}

		if ( !$this->user || is_wp_error($this->user) ) {
			amem_add_error( 'username', amem()->errors->text('username_not_exists') );
			return;
		}

		/** @todo Consider limit reset attempts rule and apply */
		// $user_id = $this->user->ID;
		// $attempts = (int) get_user_meta( $user_id, 'password_rst_attempts', true );
		// $is_admin = user_can( absint( $user_id ), 'manage_options' );
		// $limit = amem()->options->get( 'reset_password_limit_number' );
		// if ( $limit && amem()->options->get( 'enable_reset_password_limit' ) ) {
		// 	if ( ! ( amem()->options->get( 'disable_admin_reset_password_limit' ) && $is_admin ) ) {
		// 		// Doesn't trigger this when a user has admin capabilities and when reset password limit is disabled for admins
		// 		if ( $attempts >= $limit ) {
		// 			amem_add_error( 'username', __( 'You have reached the limit for requesting password change for this user already. Contact support if you cannot open the email', 'advanced-members' ) );
		// 		} else {
		// 			update_user_meta( $user_id, 'password_rst_attempts', $attempts + 1 );
		// 		}
		// 	}
		// }
	}

	function handle_form( $form, $fields, $args ) {
		if ( $this->is_valid() ) {
			do_action( 'amem/user/passwordreset', $form, $fields, $args );
		}

		do_action( 'amem/user/passwordreset/after', $form, $fields, $args );
	}

	function process( $form, $fields, $args ) {
		$mode = $this->get_mode();

		switch( $mode ) {
			case 'ready':
			case 'reset':
			$this->process_reset( $form, $fields, $args );
			break;
			case 'change':
			$this->process_change( $form, $fields, $args );
			break;
			default:
			break;
		}
	}

	function process_reset( $form, $fields, $args ) {
		if ( !isset($this->user) ) {
			$user_name = $form['record']['username'];
			$this->user = $this->get_user($user_name);
		}

		if ( !$this->user || is_wp_error($this->user) ) {
			// $redirect = remove_query_arg( ['hash', 'login', 'act', 'sent', 'updated', 'errc'] );
			wp_safe_redirect( add_query_arg( array( 'updated' => 'pwreset_invalidkey' ), amem_get_core_page( 'password-reset' ) ) );
			exit;
		}

		amem()->user->email_password_reset($this->user->ID);
		$redirect = remove_query_arg( ['hash', 'login', 'act', 'sent', 'updated'], amem_get_current_url() );
		wp_safe_redirect( add_query_arg( 'updated', 'pwreset_checkemail', $redirect) );
		exit;
	}

	function process_change( $form, $fields, $args ) {
		$record = $form['record'];
		$user = amem()->passwordreset->get_requested_user();

		$errors = new \WP_Error();
		if ( !$user || is_wp_error($user) ) {
			amem()->passwordreset->setcookie( null, false );
			if ( $user && 'expired_key' === $user->get_error_code() ) {
				wp_safe_redirect( add_query_arg( array( 'updated' => 'pwreset_expiredkey' ), amem_get_core_page( 'password-reset' ) ) );
			} else {
				wp_safe_redirect( add_query_arg( array( 'updated' => 'pwreset_invalidkey' ), amem_get_core_page( 'password-reset' ) ) );
			}
			exit;

			if ( !$user ) {
				$errors->add( 'unknown_error', __( 'An unknown error occurred. Please try again.', 'advanced-members' ) );
			} else {
				$errors = $user;
				$user = false;
			}
		}

		/** This action is documented in wp-login.php */
		do_action( 'validate_password_reset', $errors, $user );

		if ( ( ! $errors->get_error_code() ) ) {
			reset_password( $user, trim( $record['user_password'] ) );

			// send the Password Changed Email
			amem()->user->email_password_changed( $user->ID );

			// clear temporary data
			$attempts = (int) get_user_meta( $user->ID, 'password_rst_attempts', true );
			if ( $attempts ) {
				update_user_meta( $user->ID, 'password_rst_attempts', 0 );
			}
			amem()->passwordreset->setcookie( null, false );

			do_action( 'amem/user/passwordchange/after', $user->ID );

			if ( ! is_user_logged_in() ) {
				$url = amem_get_core_page( 'login', 'pwreset_password_changed' );
			} else {
				$url = amem_get_core_page( 'password-reset', 'pwreset_password_changed' );
			}
			wp_safe_redirect( $url );
			exit;
		} else {
			/** @todo get specific error messaes and print it */
			wp_safe_redirect( add_query_arg( array( 'updated' => 'pwreset_unkownerror' ), amem_get_core_page( 'password-reset' ) ) );
			exit;
		}
	}

	function button( $button, $button_attributes, $form, $args ) {
		$data = $form['data'];
		$mode = $this->get_mode();

		$submit_text = __( 'Send Reset Email', 'advanced-members' );
		if ( $mode == 'change' ) {
			$submit_text = __( 'Change Password', 'advanced-members' );
		}

		$submit_button = sprintf( '<button type="submit" %s>%s</button>', acf_esc_atts( $button_attributes ), esc_html($submit_text) );
		$submit_button .= '<span class="acf-spinner amem-spinner"></span>';

		ob_start();
		?>

		<div class="amem-submit acf-form-submit">
			<div class="amem-center">
				<?php echo $submit_button; ?>
			</div>
			<div class="amem-clear"></div>
		</div>

		<?php
		$output = ob_get_clean();

		return $button . $output;
	}

	function get_name() {
		return 'passwordreset';
	}

	function get_label() {
		return __( 'Password Reset', 'advanced-members' );
	}

}

amem()->register_action('passwordreset', PasswordReset::getInstance());