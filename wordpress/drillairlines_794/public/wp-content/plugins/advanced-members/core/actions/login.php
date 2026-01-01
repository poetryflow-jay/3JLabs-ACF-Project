<?php
/**
 * Handle Login Form
 *
 * @since 1.0
 * @package Advanced Members for ACF \ Forms
 * 
 */
namespace AMem\Action;
use AMem\Action;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

class Login extends Action {

	public $priority = 9;

	protected $loggedin = null;

	function __construct() {
		parent::__construct();

		add_action( 'amem/form/validate/type=login', array( $this, 'validate'), 10, 3 );
		add_action( 'amem/form/validate/type=login', array( $this, 'check_fields'), 10, 3 );

		// @todo
		add_action( 'amem/form/validate/type=login', array( $this, 'logincheck'), 9999, 3 );

		add_action( 'amem/form/submit/type=login', array( $this, 'handle_form'), 10, 3 );

		add_action( 'amem/user/login', array( $this, 'process'), 10, 3 );

		add_filter( 'amem/form/from_post/type=login', [$this, 'form_form_post'], 15, 2 );

		add_filter( 'amem/form/button_html/type=login', array( $this, 'login_buttons' ), 10, 4 );

		add_filter( 'amem/error/messages', [$this, 'messages'] );
		// add_filter( 'amem/form/error_message', [$this, 'error_message'] );
	}

	function form_form_post( $form, $post ) {
		$login_data = [
			'rememberme'  => get_post_meta( $post->ID, 'login_rememberme', true ),
			'passwordreset'  => get_post_meta( $post->ID, 'login_password_reset', true ),
			'extra_button'  => get_post_meta( $post->ID, 'login_extra_button', true ),
			'extra_text'  => get_post_meta( $post->ID, 'login_extra_text', true ),
			'extra_url'  => get_post_meta( $post->ID, 'login_extra_url', true ),
		];

		$form['data'] = array_merge( $form['data'], $login_data );

		return $form;
	}

	function messages($messages) {
		$messages['account_active'] = __( 'Your account is now activated!', 'advanced-members' );

		return $messages;
	}

	function error_message($message) {
		// Do not append to existing
		if ( '' == $message && is_user_logged_in() ) {
			$message = amem()->errors->text( 'logged_in' );
		}

		return $message;
	}

	function validate( $form, $args, $fields ) {
		if ( is_user_logged_in() ) {
			amem_add_error( 'field_amem_errors', amem()->errors->text('logged_in') );
		}
	}

	/**
	 * Check login fields
	 * 
	 */
	function check_fields( $form, $args, $fields ) {
		$data = $form['record'];
		$amem = $form['amem'];

		$no_passwd = ! isset($data['user_password']);
		if ( $no_passwd ) {
			$amem['user_password'] = 'field_amem_errors';
		}
		$user_password = $no_passwd ? '' : $data['user_password'];

		if ( $user_password == '' ) {
			amem_add_error( $amem['user_password'], amem()->errors->text('empty_password') );
			return;
		}

		if ( isset( $data['username'] ) && $data['username'] == '' ) {
			amem_add_error( $amem['username'], amem()->errors->text('empty_username_email') );
		}

		if ( isset( $data['user_email'] ) && $data['user_email'] == '' ) {
			amem_add_error( $amem['user_email'], amem()->errors->text('empty_email') );
		}

		$user_name = '';
		foreach( ['username', 'user_email'] as $ukey ) {
			if ( isset($data[$ukey]) ) {
				if ( $data[$ukey] == '' ) {
					amem_add_error( $amem[$ukey], amem()->errors->text('empty_username_email') );
				} else {
					$user_name = $data[$ukey];
				}
				break;
			}
		}

		if ( $user_name == '' ) {
			amem_add_error( 'field_amem_errors', amem()->errors->text('empty_username_email') );
			return;
		}

		$user = false;
		// user_login with email may exits, so check user_login first
		if ( $user_id = username_exists( $user_name ) ) {
			$user = get_user_by('id', $user_id);
		} elseif ( $user_id = email_exists( $user_name ) ) {
			$user = get_user_by('id', $user_id);
		}

		if ( !$user || is_wp_error($user) ) {
			amem_add_error( $amem[$ukey], sprintf( amem()->errors->text('username_not_exists'), $data[$field] ) );
			return;
		}

		$auth_id = 0;
		if ( wp_check_password( $user_password, $user->data->user_pass, $user->ID ) ) {
			amem()->submission['form']['auth_id'] = $user->ID;
		} else {
			amem_add_error( $amem['user_password'], amem()->errors->text('password_incorrect') );
		}

		// Integration with 3rd-party login handlers e.g. 3rd-party reCAPTCHA etc.
		$third_party_codes = apply_filters( 'amem/authenticate/error_codes', array() );

		// see WP function wp_authenticate()
		$ignore_codes = array( 'empty_username', 'empty_password', 'empty_username_email', 'username_not_exists', 'password_incorrect' );

		$user = apply_filters( 'authenticate', null, $user_name, $user_password );
		if ( is_wp_error( $user ) && ! in_array( $user->get_error_code(), $ignore_codes ) ) {
			if ( ! empty( $third_party_codes ) && in_array( $user->get_error_code(), $third_party_codes ) ) {
				amem_add_error( $amem['user_password'], $user->get_error_message() );
			} else {
				amem_add_error( $amem['user_password'], amem()->errors->text('password_incorrect') );
			}
		}

		$user = apply_filters( 'wp_authenticate_user', $user, $user_password );
		if ( is_wp_error( $user ) && ! in_array( $user->get_error_code(), $ignore_codes ) ) {
			if ( ! empty( $third_party_codes ) && in_array( $user->get_error_code(), $third_party_codes ) ) {
				amem_add_error( $amem['user_password'], $user->get_error_message() );
			} else {
				amem_add_error( $amem['user_password'], amem()->errors->text('password_incorrect') );
			}
		}

		// if there is an error notify wp
		if ( !$this->is_valid() ) {
			do_action( 'wp_login_failed', $user_name, amem()->errors->to_wp_error() );
		}
	}

	/**
	 * Login checks through the frontend login
	 *
	 * @todo: check user status and...
	 */
	function logincheck( $form, $args, $fields ) {
		// Logout if logged in
		if ( is_user_logged_in() ) {
			wp_logout();
		}

		$user_id = ( isset( amem()->submission['form']['auth_id'] ) ) ? amem()->submission['form']['auth_id'] : '';
		$status = '';

		if ( $user_id )
			$status = get_field( 'account_status', "user_{$user_id}" ); // account status

		switch ( $status ) {
			// If user can't log in to site...
			case 'inactive':
			case 'awaiting_admin_review':
			case 'awaiting_email_confirmation':
			case 'rejected':
			$redirect_url = add_query_arg( 'errc', esc_attr( $status ), amem_get_current_url() );
			if ( amem()->submissions->is_ajax() ) {
				amem_add_error( 'field_amem_errors', amem()->errors->text($status) );
				// amem_add_submission_error( amem()->errors->text($status) );
				// remove_action( 'amem/user/login', array( $this, 'process') );
				amem()->submissions->set_redirect( $redirect_url );
			}	else {
				amem_add_error( 'field_amem_errors', amem()->errors->text($status) );
				// wp_safe_redirect( $redirect_url );
				// exit;
			}
			remove_action( 'amem/user/login', array( $this, 'process') );
			break;
			default:
			break;
		}
	}

	function auto_login($form, $rememberme = 0) {
		$data = $form['record'];

		/** @var $form data verified via self::check_fields() */
		$user_password = !isset($data['user_password']) ? '' : $data['user_password'];
		// double check user is verified by check_fields
		$user_id = isset(amem()->submission['form']['auth_id']) ? amem()->submission['form']['auth_id'] : 0;

		$user_name = '';
		foreach( ['username', 'user_email'] as $ukey ) {
			if ( isset($data[$ukey]) ) {
				if ( $data[$ukey] == '' ) {
				} else {
					$user_name = $data[$ukey];
				}
				break;
			}
		}

		if ( !$user_name || !$user_password || !$user_id )
			return;

		$credentials = [
			'user_login' => $user_name,
			'user_password' => $user_password,
			'remember' => $rememberme
		];
		
		$loggedin = wp_signon( $credentials );
		if ( !$loggedin || is_wp_error($loggedin) || !is_a($loggedin, 'WP_User') ) {
			amem_add_error( $form['amem']['user_password'], __( 'Failed to login.', 'advanced-members' ) );
		}

		$this->loggedin = $loggedin;
	}

	function process( $form, $fields, $args ) {
		// phpcs:disable WordPress.Security.NonceVerification -- already verified here
		$rememberme = ( isset( $_REQUEST['amem_login_rememberme'] ) && 1 === (int) $_REQUEST['amem_login_rememberme'] ) ? 1 : 0;

		$user_id = isset(amem()->submission['form']['auth_id']) ? amem()->submission['form']['auth_id'] : 0;
		$this->auto_login( $form, $rememberme );
	}

	function handle_form( $form, $fields, $args ) {
		if ( $this->is_valid() ) {
			do_action( 'amem/user/login', $form, $fields, $args );
		}

		do_action( 'amem/user/login/after', $form, $fields, $args );
	}

	function login_buttons( $button, $button_attributes, $form, $args ) {
		$data = $form['data'];

		$submit_text = !empty($data['submit_text']) ? $data['submit_text'] : __( 'Login', 'advanced-members' );
		$submit_text = apply_filters( 'amem/form/login/button/text', $submit_text, $form, $args );

		if ( $data['extra_button'] ) {
			$extra_btn_text = !empty($data['extra_text']) ? $data['extra_text'] : __( 'Register', 'advanced-members' );
			$extra_btn_text = apply_filters( 'amem/form/login/extra_button/text', $extra_btn_text, $form, $args );

			$extra_btn_url = !empty($data['extra_url']) ? $data['extra_url'] : amem_get_core_page( 'register' );
			$extra_btn_url = apply_filters( 'amem/form/login/extra_button/url', $extra_btn_url, $form, $args );			
		}

		$button = $button ? $button : '';
		$has_extra_class = $data['extra_button'] ? ' amem-submit-has-extra' : '';


		$submit_button = sprintf( '<button type="submit" %s>%s</button>', acf_esc_atts( $button_attributes ), esc_html($submit_text) );
		$submit_button .= '<span class="acf-spinner amem-spinner"></span>';

		if ( $data['rememberme'] ) {
			acf_add_local_field( [
  			'key' => 'amem_login_rememberme',
  			'label' => __( 'Remember me', 'advanced-members' ),
  			'name' => 'login_rememberme',
  			'type' => 'true_false',
      	'_amem_local' => true,
      	'wrapper' => [
      		'class' => 'amem-field-row',
      	]
			] );
			$rememberme = acf_get_local_field('amem_login_rememberme');
		}

		ob_start();
		?>

		<div class="amem-submit acf-form-submit<?php echo esc_attr($has_extra_class); ?>">

			<?php if ( $data['rememberme'] ) {
			// amem()->render->render_field($rememberme, $form, $args); ?>
			<div class="amem-local-field">
				<label for="amem_login_rememberme">
					<input type="checkbox" id="amem_login_rememberme" name="amem_login_rememberme">
					<?php esc_html_e( 'Remember me', 'advanced-members' ) ?>
				</label>
			</div>
			<div class="amem-clear"></div>
			<?php }

			if ( $data['extra_button'] ) { ?>
			<ul class="acf-hl amem-hl" data-cols="2">
				<li class="amem-left amem-half acf-hl" data-cols="2">
					<?php echo $submit_button; ?>
				</li>
				<li class="amem-right amem-half acf-hl" data-cols="2">
					<a href="<?php echo esc_url( $extra_btn_url ); ?>" class="acf-btn amem-btn acf-secondary-btn amem-btn-alt button-alt">
						<?php echo esc_html( $extra_btn_text ); ?>
					</a>
				</li>
			</ul>

			<?php } else { ?>
			<div class="amem-center">
				<?php echo $submit_button; ?>
			</div>
			<?php } ?>

			<div class="amem-clear"></div>

			<?php if ( $data['passwordreset'] ) { ?>
			<div class="amem-center amem-forgotpwd-wrap">
				<a href="<?php echo esc_url( amem_get_core_page( 'password-reset' ) ); ?>" class="amem-link-alt amem-forgotpwd-link">
					<?php esc_html_e( 'Forgot your password?', 'advanced-members' ); ?>
				</a>
			</div>
			<?php } ?>

		</div>

		<?php
		$output = ob_get_clean();

		return $button . $output;
	}

	function get_name() {
		return 'login';
	}

	function get_label() {
		return __( 'Login', 'advanced-members' );
	}

}

amem()->register_action('login', Login::getInstance());