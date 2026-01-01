<?php
/**
 * Trait for AMem Field
 * 
 * @since 1.0
 *
 * 
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if ( ! trait_exists( 'amem_field' ) ) :

	trait amem_field {

		protected $mode;

		protected $_meta_key;

		protected function _form_type() {
			if ( isset(amem()->form['_amem_local']) )
				return amem()->form['type'];
			if ( isset(amem()->form['post_id']) ) {
				return amem_form_type(amem()->form['post_id']);
			}
			return null;
		}

		function _can_edit($user_id) {
			return ( $user_id && get_current_user_id() == $user_id ) || current_user_can( 'edit_users' );
		}

		public function mode() {
			if ( isset($this->mode) ) {
				return $this->mode;
			}

			if ( $mode = $this->_form_type() ) {
				$this->mode = $mode;
				return $this->mode;
			}

			$this->mode = false;
			return $this->mode;
		}

		public function update_field( $field ) {
			$field['name'] = $this->meta_key ? $this->meta_key : $this->name;
			return $field;
		}

		public function load_field( $field ) {
			$field['name'] = $field['type'];
			return $field;
		}

		protected function _user_id( $post_id ) {
			$user = explode( 'user_', $post_id );
			if ( count($user) > 1 )
				return (int) $user[1];
			return 0;
		}

		/*public function __render_field_settings( $field ) {
			$this->_render_field_settings($field);
		}

		protected function _render_field_settings($field) {
	    acf_render_field_setting( $field, array(
        'label'            => __('Hide Label', 'advanced-members'),
        'instructions'    => '',
        'name'            => 'hide_label',
        'type'            => 'true_false',
        'ui'            => 1,
	    ), true );
		}*/

	}

endif; // class_exists check


