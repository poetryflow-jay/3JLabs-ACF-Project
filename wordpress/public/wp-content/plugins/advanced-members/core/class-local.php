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

class Local extends Module {

	protected $inc = '';
	protected $name = 'amem/local';

	protected $forms = [];
	protected $groups = [];
	protected $fields = [];

	function __construct() {
		// Local Form/Group/Fields should be registered before 'create_submission' and 'render'
		// add_action( 'amem/form/create_submission/before/type=account', [$this, 'register_local_fields'], 10, 2 );
	}

	function add_form($form) {
		$form['_amem_local'] = true;
		$form = amem_form_from_local( $form );

		if ( $form ) {
			$this->forms[$form['key']] = $form;
			if ( isset($form['local_groups']) && is_array($form['local_groups']) ) {
				$this->groups[$form['key']] = $form['local_groups'];// group keys
			}
		}

		return $form;
	}

	function get_form($key) {
		if ( isset($this->forms[$key]) )
			return $this->forms[$key];
		return false;
	}

	function get_forms() {
		return $this->forms;
	}

	function get_form_fields($key) {
		if ( !isset($this->forms[$key]) )
			return false;

		$groups = $this->get_form_groups($key, true);

		$fields = [];
		foreach( $groups as $group ) {
			if ( $group_fields = $this->get_group_fields($group) )
				$fields += $group_fields;
		}

		return $fields;
	}

	function add_group($group) {
		$group['_amem_local'] = true;
		if ( !isset($group['fields']) )
			$group['fields'] = [];

		if ( !empty($group['_amem_form']) ) {
			if ( !isset($this->groups[$group['_amem_form']]) )
				$this->groups[$group['_amem_form']] = [];
			$this->groups[$group['_amem_form']][] = $group['key'];
		}

		return acf_add_local_field_group( $group );
	}

	function get_group($key) {
		if ( is_array($key) ) {
			$key = $key['key'];
		}
		return acf_get_local_field_group( $key );
	}

	function get_group_fields($key) {
		if ( $group = $this->get_group($key) ) {
			return acf_get_fields($key);
		}
		return false;
	}

	function get_form_groups($key, $return_keys=true) {
		if ( !isset($this->forms[$key]) )
			return false;

		if ( !isset($this->groups[$key]) || !is_array($this->groups[$key]) )
			return false;

		if ( $return_keys )
			return $this->groups[$key];

		$groups = [];
		foreach ( $this->groups[$key] as $get_group ) {
			$groups[] = $this->get_group($get_group);
		}
		$groups = array_filter($groups);

		return $groups;
	}

}

amem()->register_module('local', Local::getInstance());