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

if ( class_exists('\ACF_Location') ):
class Location extends \ACF_Location {
	function initialize() {
	    $this->name = 'amem_form';
	    $this->label = __( 'Members Forms', 'advanced-members' );
	    $this->category = 'Advanced Members';
	    $this->object_type = 'post';
	    $this->object_subtype = 'amem-form';
	}

	public static function get_operators( $rule ) {
		return array(
			'==' => __( 'is equal to', 'acf' ),
		);
	}

	function get_values( $rule ) {

		$choices = [];
		$forms = amem_get_forms();

		foreach ( $forms as $form ) {
			$choices[ $form['key'] ] = $form['title'];
		}

		return $choices;

	}

	function match( $rule, $screen, $field_group ) {
		// if( isset($screen['post_id']) ) {
	  //   $post_id = $screen['post_id'];
		// } else {
	  //   return false;
		// }

		$match = false;
		// Match with form object
		if ( 'amem_form' == $rule['param'] && isset( $screen['amem_form'] ) ) {
			if ( isset( $rule['value'] ) && $rule['value'] == $screen['amem_form'] ) {
				$match = true;
			}
			if ( $screen['amem_form'] == '__EXISTS__' && !empty( $rule['value'] ) ) {
				$match = true;
			}
		}

		// Match with entry
		if ( isset( $screen['post_type'] ) && 'amem_entry' == $screen['post_type'] ) {
			$entry_form = get_post_meta( $screen['post_id'], 'entry_form', true );
			if ( $entry_form && $entry_form == $rule['value'] ) {
				$match = true;
			}
		}

		return $match;
	}

}
endif;

class Locations extends Module {

	protected $inc = '';
	protected $name = 'locations';

	function __construct() {

		$this->inc = amem_get_path('acf/locations/');

		acf_register_location_type( 'AMem\Location' );
	}

}

amem()->register_module('locations', Locations::getInstance());
