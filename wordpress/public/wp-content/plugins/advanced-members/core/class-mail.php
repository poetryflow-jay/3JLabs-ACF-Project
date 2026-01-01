<?php
/**
 * Manage Advanced Members for ACF Fields
 *
 * @since  1.0
 *
 */
namespace AMem;

use AMem\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Mail extends Module {
	// protected static $inc = '';
	protected $name = 'amem/mail';

	public $headers = '';

	/**
	 * @var string
	 */
	public $subject = '';

	/**
	 * @var string
	 */
	public $body = '';

	function __construct() {
		add_action( 'amem/email/send/before', [$this, 'set_content_type'] );
		add_action( 'amem/email/send/after', [$this, 'restore_content_type'] );

		add_filter( 'password_change_email', [$this, 'replace_password_changed_email'], 9999 );
	}

	function replace_password_changed_email($email_array) {
		if ( amem()->options->get('override_pass_changed_email') ) {
			$template = 'changedpw_email';
			$email = $email_array['to'];
			$this->send( $email, $template );

			// disable core mail by removing receipt
			unset( $email_array['to'], $email_array['subject'], $email_array['message'] );
		}

		return $email_array;
	}

	function set_content_type() {
		add_filter( 'wp_mail_content_type', [$this, 'send_as_html'] );
	}

	function restore_content_type() {
		remove_filter( 'wp_mail_content_type', [$this, 'send_as_html'] );
	}

	function send_as_html($type) {
		return 'text/html';
	}

	/**
	 * Send Email function
	 *
	 * @param string $email
	 * @param null $template
	 * @param array $args
	 */
	public function send( $email, $template, $args = array() ) {
		if ( ! is_email( $email ) ) {
			return;
		}

		$options = amem()->options->get_emails();

		if( !isset($options[$template]) ){
			return;
		}

		if( empty($options[$template]['is_active']) ){
			return;
		}

		$hook_disabled = apply_filters( 'amem_disable_email_notification', false, $email, $template, $args );
		if ( false !== $hook_disabled ) {
			return;
		}

		do_action( 'amem/email/send/before', $email, $template, $args );

		add_filter( 'amem/template/merge_tags', array( $this, 'add_placeholder' ), 10, 1 );
		add_filter( 'amem/template/merge_tags/replace', array( $this, 'add_replace_placeholder' ), 10, 1 );

		$this->headers     = 'From: ' . stripslashes( $options['mail_from'] ) . ' <' . $options['mail_from_addr'] . '>' . "\r\n";
		$this->headers 		.= "Content-Type: text/html\r\n";

		$subject = apply_filters( 'amem/email/subject', $options[$template]['subject'], $template );
		$subject = wp_unslash( amem_convert_tags( $subject, $args ) );
		$this->subject = html_entity_decode( $subject, ENT_QUOTES, 'UTF-8' );

		$body = $options[$template]['body'];
		$body = apply_filters( 'amem/email/body_content', $body, $slug, $args );
		$body = wpautop( $body );
		$body = wp_kses_post( $body );
		$this->body = amem_convert_tags( $body, $args );

		wp_mail( $email, $this->subject, $this->body, $this->headers );

		do_action( 'amem/email/send/after', $email, $template, $args );

	}

	protected function setup_email($options, $template) {

	}

	/**
	 * AMem Placeholders for site url, admin email, submit registration
	 *
	 * @param $placeholders
	 *
	 * @return array
	 */
	public function add_placeholder( $placeholders ) {
		$placeholders[] = '{site_url}';
		$placeholders[] = '{admin_email}';
		$placeholders[] = '{login_url}';
		$placeholders[] = '{password}';
		$placeholders[] = '{user_activation_link}';
		$placeholders[] = '{user_profile_link}';
		return $placeholders;
	}

	/**
	 * AMem Replace Placeholders for site url, admin email, submit registration
	 *
	 * @param $replace_placeholders
	 *
	 * @return array
	 */
	public function add_replace_placeholder( $replace_placeholders ) {
		$replace_placeholders[] = get_bloginfo( 'url' );
		$replace_placeholders[] = amem()->options->get('admin_email');
		$replace_placeholders[] = amem_get_core_page( 'login' );
		$replace_placeholders[] = esc_html__( 'Your set password', 'advanced-members' );
		$replace_placeholders[] = amem_user( 'user_activation_link' );
		$replace_placeholders[] = sprintf( admin_url( '/user-edit.php?user_id=%s' ), amem_user('ID') );
		return $replace_placeholders;
	}
}
amem()->register_module('mail', Mail::getInstance());
