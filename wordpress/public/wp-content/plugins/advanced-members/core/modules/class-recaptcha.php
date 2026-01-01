<?php
/**
 * Add Google reCAPTCHA to amem forms
 *
 * @since 	1.0
 * 
 */
namespace AMem;

use AMem\Module;
use AMem\Log;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class reCAPTCHA extends Module {

	protected $inc = '';
	protected $name = 'amem/recaptcha';

	// vars
	protected $settings = [];

	private $action = 'homepage';

	// log
	public $logger;

	function __construct() {
		if ( !amem()->options->getmodule('_use_recaptcha') ) {
			return;
		}

		$this->settings = [
			'version' => AMEM_VERSION,
			'url' => AMEM_URL,
			'path' => AMEM_PATH,
		];

		$this->initialize_settings();

		// include field
		add_action( 'amem/register_addons', [$this, 'include_field_types'] );

		add_filter( 'amem/fields/hidden_types', [$this, 'hide_field_types'] );

		add_filter( 'amem/form/from_post', [$this, 'form_recaptcha'], 10, 2 );
		add_filter( 'amem/form/from_local', [$this, 'form_recaptcha_local'], 10 );

		// add_action( 'amem/form/local_fields', [$this, 'local_fields'], 10, 2 );
		add_action( 'amem/form/after_fields', [$this, 'local_fields'], 99, 2 );

		add_action( 'amem/form/create_submission/before', [$this, 'register_local_fields'], 10, 2 );

		add_action( 'wp_ajax_amem/recaptcha/key_verify', [$this, 'ajax_verify_secret_key'] );

		add_action( 'amem/admin/enqueue_scripts', [$this, 'admin_enqueue_scripts'] );

		add_action( 'amem/form/enqueue_scripts', [$this, 'input_style'] );

	}

	public function include_field_types() {
		amem_include( amem()->fields->inc . 'class-recaptcha.php' );
	}

	public function hide_field_types($types) {
		if ( !in_array( 'amem_recaptcha', $types) ) {
			$types[] = 'amem_recaptcha';
		}

		return $types;
	}

	protected function initialize_settings() {
		$settings = (array) amem()->options->get('recaptcha' );
		$defaults = [
			'version' => 'v3',
			'hide_badge' => false,
			'score' => '0.5',
			'v2_type' => 'checkbox',
			'theme' => 'light',
			'size' => 'normal',
			'site_key' => '',
			'secret_key' => '',
			// 'apply_global' => true,
			'apply_local_forms' => true,
			'key_verified' => 0,
		];

		$this->settings = array_merge( $defaults, $settings );
	}

	public function get_settings( $hard = false ) {
		if ( $hard )
			$this->initialize_settings();

		return $this->settings;
	}

	public function is_ready() {
		return !empty($this->settings['site_key']) && !empty($this->settings['secret_key']) && $this->settings['key_verified'];
	}

	public function form_recaptcha($form, $form_post) {
		// $recaptcha = amem()->options->get('recaptcha/apply_global', false);

		// if ( get_post_meta( $form_post->ID, 'recaptcha_override', true ) ) {
			$recaptcha = get_post_meta( $form_post->ID, 'recaptcha', true );
		// }

		if ( !$this->is_ready() )
			$recaptcha = false;

		$form['data']['recaptcha'] = $recaptcha;

		return $form;
	}

	public function form_recaptcha_local($form) {
		$settings = $this->get_settings();
		$recaptcha = (bool) $settings['apply_local_forms'];

		if ( !$this->is_ready() )
			$recaptcha = false;

		$form['data']['recaptcha'] = $recaptcha;

		return $form;
	}

	private function local_fields_recaptcha() {
		static $registered = null;

		if ( $registered )
			return;

		$fields = [
			'amem_recaptcha' => array(
				'label' => '',
				'key' => 'amem_recaptcha',
				'name' => 'amem_recaptcha',
				'type' => 'amem_recaptcha',
				'required' => 1,
				'_amem_local' => true,
			),
		];

		acf_add_local_field_group( array(
			'key' => 'group_amem_recaptcha',
			'title' => '',
			'fields' => $fields,
		) );

		$registered = true;
	}

	public function register_local_fields($form, $args) {
		if ( empty($form['data']['recaptcha']) )
			return;

		$this->local_fields_recaptcha();
	}

	public function local_fields( $form, $args ) {
		if ( empty($form['data']['recaptcha']) )
			return;

		$this->local_fields_recaptcha();

		$field_group = acf_get_local_field_group('group_amem_recaptcha');

		if ( $field_group )
			amem()->render->render_field_group( $field_group, $form, $args );
	}

	public function input_enqueue_scripts() {
	  amem_register_script( 'amem-recaptcha', amem_get_url("recaptcha-input.js", 'assets/js'), ['acf-input'], AMEM_VERSION, ['asset_path' => amem_get_path('', 'assets/js')] );

	  $translations = [
	  	'error' => __( 'An error has occurred', 'advanced-members' ),
	  	'expired' => __( 'reCAPTCHA has expired', 'advanced-members' ),
	  ];
	  $data_array = [
	    'nonce' => wp_create_nonce('amem-recaptcha'),
	    'l10n' => $translations,
	  ];
	  wp_localize_script( 'amem-recaptcha', 'amemReCAPTCHA', $data_array );

	  wp_enqueue_script( 'amem-recaptcha' );
	}

	function input_style() {
		$css = '.amem-field-type-amem-recaptcha .acf-required { display: none; }';
		wp_add_inline_style( 'amem-form-style', $css );
	}

	public function admin_enqueue_scripts() {
		amem_register_script( 'amem-recaptcha-admin', amem_get_url("recaptcha-admin.js", 'assets/js'), ['acf-input', 'amem-admin'], AMEM_VERSION, ['asset_path' => amem_get_path('', 'assets/js')] );

		$data_array = [
			'nonce' => wp_create_nonce('amem-recaptcha-admin'),
			'strings' => [
				'no_grecaptcha' => __( 'The grecaptcha object could not be found after loading the reCAPTCHA API.', 'advanced-members' ),
				'api_load_failed' => __( 'Failed to load reCAPTCHA API:', 'advanced-members' ),
				'is_valid_key' => __( 'Your key have been validated.', 'advanced-members' ),
				'keys_verified' => __( 'Site key and Secret key have been verified. You can safely save your keys.', 'advanced-members' ),
				'needs_validation' => __( 'reCAPTCHA will not function properly until validated Site Key and Secret Key are configured.', 'advanced-members' ),
				'v2_sitekey_error' => __( 'Failed to load reCAPTCHA v2. Maybe invalid site key.', 'advanced-members', ),
				'invalid_request' => __( 'Unknown error (null) occurred. Please check your site key and reCAPTCHA settings.', 'advanced-members' ),
				'checkbox_novalidation' => __( 'v2 reCAPTCHA Checkbox type keys cannot be verified. You can check it by yourself on frontend forms.', 'advanced-members' ),
				'empty_key' => __( 'Please enter your key.', 'advanced-members' ),
			]
		];

		wp_localize_script( 'amem-recaptcha-admin', 'amemReCaptchaAdmin', $data_array );

		wp_enqueue_script( 'amem-recaptcha-admin' );
	}

	private function get_verify_url($args) {
    switch ($args['version']) {
      case 'v3_enterprise':
        return 'https://recaptchaenterprise.googleapis.com/v1/projects/' . $args['project_id'] . '/assessments';
      default:
        return 'https://www.google.com/recaptcha/api/siteverify';
    }
	}

	public function ajax_verify_secret_key() {
		check_ajax_referer( 'amem-recaptcha-admin', 'nonce' );

		$secret_key = isset($_POST['secret_key']) ? sanitize_text_field($_POST['secret_key']) : null;
		$token = isset($_POST['token']) ? sanitize_text_field($_POST['token']) : null;
		$version = isset($_POST['version']) ? sanitize_text_field($_POST['version']) : null;

		if ( !$secret_key ) {
			wp_send_json_error( __( 'Secret key is not set', 'advanced-members' ) );
		}

		if ( !$token ) {
			wp_send_json_error( __( 'Token is not set', 'advanced-members' ) );
		}

		if ( !$version ) {
			wp_send_json_error( __( 'Version is not set', 'advanced-members' ) );
		}

		$settings = amem()->options->get('recaptcha');

		$args = [
			'version' => $version,
			'score' => $settings['score'],
			'secret_key' => $secret_key
		];

		$res = $this->validate( $token, $args, true );

		if ( true !== $res ) {
			wp_send_json_error( $res );
		}

		wp_send_json_success();
	}

	function get_validation_error_message( $response, $is_admin = false ) {
		$error = false;

		// something went wrong
		if ( !isset($response->success) ) {
		  amem()->recaptcha->debug( $response );
		  $error = __( 'An error has occurred while verifying reCAPTCHA', 'advanced-members' );
		  // amem_add_error( 'amem_field_error', $error );
		  return !$is_admin ? $error : $response->{'error-codes'};
		}

		// error
		if ( $response->success === false ) {
		  $error = $_error = __( 'reCAPTCHA verification failed, please try again', 'advanced-members' );
		  // amem_add_error( 'amem_field_error', $error );
		  if ( $is_admin && !empty($response->{'error-codes'}) ) {
		  	$_error = [];
		  	// 오류 코드별 메시지
		  	$error_messages = array(
		  	    'missing-input-secret' => __( 'The secret parameter is missing', 'advanced-members' ),
		  	    'invalid-input-secret' => __( 'The secret parameter is invalid or malformed', 'advanced-members' ),
		  	    'missing-input-response' => __( 'The response parameter is missing', 'advanced-members' ),
		  	    'invalid-input-response' => __( 'The response parameter is invalid or malformed', 'advanced-members' ),
		  	    'bad-request' => __( 'The request is invalid or malformed', 'advanced-members' ),
		  	    'timeout-or-duplicate' => __( 'The response is no longer valid: either is too old or has been used previously', 'advanced-members' )
		  	);
		  	foreach ($response->{'error-codes'} as $error_code) {
		  	    $_error[] = $error_messages[$error_code];
		  	}
		  }
		  return !$is_admin ? $error : implode( ', ', $_error );
		}

		return false;
	}

	function validate($token, $args, $is_admin = false) {
		$secret_key = $args['secret_key'];

		$request_args = array(
		  'body' => array(
		    'secret'    => $secret_key,
		    'response'  => $token,
		  ),
		);

		if ( $args['version'] === 'v3' || $args['version'] === 'v3_ent' )
			$request_args['action'] = $this->action;

		// post request
		$response = wp_remote_post( $this->get_verify_url($args), $request_args);
		
		// validate request response
		if ( is_wp_error($response) ) {
		  amem()->recaptcha->debug( $response->get_error_message() );
		  $error = __( 'An error has occurred while requesting reCAPTCHA token.', 'advanced-members' );
		  // amem_add_error( 'amem_field_error', $error );
		  return !$is_admin ? $error : $response->get_error_message();
		}
		
		// get response as json
		$data = json_decode( wp_remote_retrieve_body($response) );
		
		$error_message = $this->get_validation_error_message($data, $is_admin);
		if ( false !== $error_message ) {
			return $error_message;
		}

		// we don't need more validation with secret key validation
		if ( $is_admin )
			return true;

		$this->resposne = $data;

		$validate = $this->validate_response_data( $data, $args );
		$def_error = ' ' . __( 'Please reload the page and try again.', 'advanced-members' );

		if ( !$validate || !is_array($validate) ) 
			return __( 'Invalid response from reCAPTCHA.', 'advanced-members' );

		if ( !( $validate[0] && $validate[1] && $validate[2] && $validate[3] && $validate[3] && $validate[4] ) ) {
			if ( !$validate[0] ) {
				return __( 'Failed to verify reCAPTCHA.', 'advanced-members' ) . $def_error;
			}
			if ( !$validate[1] ) {
				return __( 'Failed to verify hostname with reCAPTCHA.', 'advanced-members' );
			}
			if ( !$validate[2] ) {
				return __( 'Failed to verify action with reCAPTCHA.', 'advanced-members' );
			}
			if ( !$validate[3] ) {
				return __( 'It\'s very likely a bot(reCAPTCHA).', 'advanced-members' ) . $def_error;
			}
			if ( !$validate[4] ) {
				return __( 'Maybe expired reCAPTCHA.', 'advanced-members' ) . $def_error;
			}
		}

		return true;
	}

	private function validate_response_data( $response_data, $args ) {
		if (
			! empty( $response_data->{'error-codes'} )
			|| ( property_exists( $response_data, 'success' ) && $response_data->success !== true )
		) {
			return false;
		}

		$is_v2 = $args['version'] === 'v2';

		$validation_properties = array( 'hostname', 'action', 'success', 'score', 'challenge_ts' );
		if ( $is_v2 ) {
			// v2 response not including action and score
			$validation_properties = array( 'hostname', 'success', 'challenge_ts' );
		}
		$response_properties   = array_filter(
			$validation_properties,
			function( $property ) use ( $response_data ) {
				return property_exists( $response_data, $property );
			}
		);

		if ( count( $validation_properties ) !== count( $response_properties ) ) {
			return false;
		}

		$threshold = !empty($args['score']) ? (float) $args['score'] : 0.5;

		return array(
			$response_data->success,
			$this->check_hostname( $response_data->hostname ),
			$is_v2 || $this->check_action( $response_data->action ),
			$is_v2 ||  $this->check_score( $response_data->score, $threshold ),
			$this->check_timestamp( $response_data->challenge_ts ),
		);
	}

	private function check_hostname( $hostname ) {
		if ( ! has_filter( 'amem/recaptcha/valid_hostnames' ) ) {
			return true;
		}

		$valid_hostnames = apply_filters(
			'amem/recaptcha/valid_hostnames',
			array(
				wp_parse_url( get_home_url(), PHP_URL_HOST ),
			)
		);

		return is_array( $valid_hostnames ) ? in_array( $hostname, $valid_hostnames, true ) : false;
	}

	private function check_action( $action ) {
		return $this->action === $action;
	}

	private function check_score( $score, $threshold ) {
		$valid_score = is_float( $score ) && $score >= 0.0 && $score <= 1.0;
		if ( $valid_score && $threshold ) {
			$valid_score = $score >= $threshold;
		}

		return $valid_score;
	}

	private function check_timestamp( $challenge_ts ) {
		return ( gmdate( time() ) - strtotime( $challenge_ts ) ) <= 24 * HOUR_IN_SECONDS;
	}

	public function logger() {
		if ( !isset($this->logger) ) {
			$this->logger = Log::getInstance( ['name' => 'recaptcha'] );
		}

		return $this->logger;
	}

	public function debug($message) {
		if ( defined('WP_DEBUG') && WP_DEBUG === true ) {
			$this->logger()->add( $message );
		}
	}

	private function log_error($description, $object = false) {
		$message = "AMem reCAPTCHA: $description" . PHP_EOL;
		if ( $object ) {
			$message .= print_r($object, true);
		}

		$this->logger()->add( $message );
	}

}

amem()->register_module('recaptcha', reCAPTCHA::getInstance());