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
use AMem\REST\Controller;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class REST extends Module {

	function __construct() {
		add_action( 'rest_api_init', function () {
			amem_include( 'core/rest/class-rest-controller.php' );
			$controller = new Controller;
			$controller->register_routes();
		}, 10 );
	}

	function preForms($key=false) {
		$forms = [
			'account' => [
				'preForm' => 'account',
				'form' => 0,
				'hash' => '[advanced-members-account]',
				'slug' => 'account',
				'title' => __( 'Account', 'advanced-members' ),
			],
			'account-password' => [
				'preForm' => 'account-password',
				'form' => 0,
				'hash' => '[advanced-members-account-password]',
				'slug' => 'account',
				'title' =>  __( 'Change Password', 'advanced-members' ),
			],
			'account-delete' => [
				'preForm' => 'account-delete',
				'form' => 0,
				'hash' => '[advanced-members-account-delete]',
				'slug' => 'account',
				'title' => __( 'Delete Account', 'advanced-members' ),
			],
			'password-reset' => [
				'preForm' => 'password-reset',
				'form' => 0,
				'hash' => '[advanced-members-pwreset]',
				'slug' => 'account',
				'title' => __( 'Password Reset', 'advanced-members' ),
			],
		];

		$forms = apply_filters( 'amem/api/forms/preForms', $forms );

		if ( $key ) {
			return ( isset($forms[$key]) ) ? $forms[$key] : false;
		}

		return $forms;
	}

	function forms($args = '') {
		$meta_query = [];
		if ( !empty($args['type']) ) {
			$types = !is_array($args['type']) ? explode(',', $args['type']) : $args['type'];
			$types = array_map('trim', $types);
			unset($args['type']);
			$meta_query = array(
				array(
					'key' => 'select_type',
					'value' => $types,
					'compare' => 'IN',
				),
			);
		}

		if ( !empty($args['meta_query']) )
			$meta_query = array_merge( $meta_query, $args['meta_query'] );

		$args = wp_parse_args( $args, [
			'post_type' => 'amem-form',
			'numberposts' => -1,
			'sort_column' => 'post_title',
			'sort_order' => 'ASC',
			'post_status' => 'publish',
			'meta_query' => $meta_query,
		] );

		$posts = get_posts( $args );
		$forms = [];
		foreach ($posts as $form ) {
			$forms[$form->ID] = [
				'form' => $form->ID,
				'preForm' => '0',
				'hash' => get_post_meta( $form->ID, 'form_key', true),
				'slug' => $form->post_name,
				'title' => $form->post_title,
			];
		}

		return $forms;
	}

	function get_forms($args='') {
		$forms = $this->forms($args);

		$preForms = $this->preForms();

		$allforms = array_merge( (array)$preForms, (array)$forms );

		return array_filter($allforms);
	}

	function get_form($id) {
		if ( $form = $this->preForms($id) )
			return $form;

		$forms = $this->forms(['ID' => $id]);

		if ( $forms ) {
			return array_pop($forms);
		}

		return false;
	}

}

amem()->register_module( 'rest', REST::getInstance() );
