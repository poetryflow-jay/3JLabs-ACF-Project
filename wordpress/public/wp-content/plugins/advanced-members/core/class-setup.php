<?php
namespace AMem;

use AMem\Module;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Class Setup
 *
 * @package AMem
 */
class Setup extends Module {

	/**
	 * Run setup.
	 */
	public function run_setup() {
		// $this->install_default_forms();
	}
	public function install_default_options() {

		foreach ( amem()->get_module('config')->default_settings as $key => $value ) {
			$options[ $key ] = $value;
		}

	}
	public function not_install_core_pages() {
		update_option( 'amem_default_installed', true );
	}

	public function install_default_forms() {
		if ( current_user_can( 'manage_options' ) && ! get_option( 'amem_default_installed' ) ) {
			foreach ( amem()->get_module('config')->core_forms as $id ) {
				$isset_form = amem()->options->find_core( 'amem-form', 'amem_core_form', $id );
				if ( ! $isset_form ) {
					// if( $id != 'register' ){
					// 	continue;
					// }
					$current_uid = get_current_user_id();

					if ( 'register' === $id ) {
						$title = __('Default Registration', 'advanced-members');
					} elseif ( 'login' === $id ) {
						$title = __('Default Login', 'advanced-members');
					} else {
						$title = __('Default Account', 'advanced-members');
					}

					$form = array(
						'post_type'   => 'amem-form',
						'post_title'  => $title,
						'post_status' => 'publish',
						'post_author' => $current_uid,
					);

					$form_id = wp_insert_post( $form );
					$form_key = '';
					foreach ( amem()->config->amem_default_form_meta[ $id ] as $key => $value ) {
						if( $key == 'form_key'){
							$form_key = $value;
						}
						update_post_meta( $form_id, $key, $value );
					}

					if ( $id == 'account' && $form_id ) {
						amem()->options->set('accform/default', $form_id);
					}

					$acf_fied_group_content = amem()->config->amem_default_field_group;
					$acf_fied_group_content['location'][0][0]['value'] = $form_key;
					$field_group_id = $this->install_default_field_groups( $id, $current_uid , $acf_fied_group_content );

					if( $field_group_id ){
						$acf_fied_group_fields = amem()->config->amem_default_fields[$id];
						$this->insert_fields( $acf_fied_group_fields , $field_group_id );
					}
				}
			}
			$this->install_default_pages();
		}
	}

	function install_default_field_groups( $id , $current_uid, $acf_fied_group_content = array() ) {
		if ( 'register' === $id ) {
			$acf_title = __('Default Registration', 'advanced-members');
		} elseif ( 'login' === $id ) {
			$acf_title = __('Default Login', 'advanced-members');
		} else {
			$acf_title = __('Default Account', 'advanced-members');
		}

		$acf_field_group = array(
			'post_type'   	=> 'acf-field-group',
			'post_title'  	=> $acf_title,
			'post_content'	=> serialize($acf_fied_group_content),
			'post_status' 	=> 'publish',
			'post_name'			=> uniqid('group_'),
			'post_author' 	=> $current_uid,
		);
		return wp_insert_post( $acf_field_group );
	}

	function insert_fields( $field_info , $parent_id ) {
		foreach ($field_info as $idx => $field) {
			$acf_field = array(
				'post_type'   	=> 'acf-field',
				'post_title'  	=> $field['title'],
				'post_content'	=> $field['content'],
				'post_name'			=> uniqid('field_'),
				'post_status' 	=> 'publish',
				'post_excerpt'	=> $field['type'],
				'post_author' 	=> get_current_user_id(),
				'post_parent'		=> $parent_id,
				'menu_order'		=> $idx
			);
			wp_insert_post( $acf_field );
		}
	}

	public function install_default_pages() {
		$amem_core_pages = amem()->config->core_pages;
		$core_pages = array();
		$current_uid = get_current_user_id();

		$blockCode = '<!-- wp:amem/form {"type":"%s","form":%d,"preForm":"%s","hash":"%s","title":"%s"} -->' . PHP_EOL;
		$blockCode .= '<div class="wp-block-amem-form">%s</div>' . PHP_EOL . '<!-- /wp:amem/form -->';

		foreach ($amem_core_pages as $slug => $page) {
			$isset_page = amem()->options->find_core( 'page', 'amem_core_page', $slug );
			$form_id = false;
			if ( ! $isset_page ) {
				//insert new core page
				// If page does not exist - create it.
				$content = '';
				switch( $slug ) {
					case 'account':
					$content = '[advanced-members-account]';
					// placeholders: type, form, preForm, hash, title, shortcode
					$content = sprintf( $blockCode, 'preForm', 0, 'account', $content, esc_attr($page['label']), $content );
					break;
					case 'account-password':
					$content = '[advanced-members-account-password]';
					$content = sprintf( $blockCode, 'preForm', 0, 'account-password', $content, esc_attr($page['label']), $content );
					break;
					case 'account-delete':
					$content = '[advanced-members-account-delete]';
					$content = sprintf( $blockCode, 'preForm', 0, 'account-delete', $content, esc_attr($page['label']), $content );
					break;
					case 'password-reset':
					$content = '[advanced-members-pwreset]';
					$content = sprintf( $blockCode, 'preForm', 0, 'password-reset', $content, esc_attr($page['label']), $content );
					break;
					case 'logout':
					break;
					default:
					$form_id = amem()->options->find_core( 'amem-form', 'amem_core_form', $slug );
					if( !empty($form_id) ){
						$hash = esc_attr( get_post_meta( $form_id, 'form_key', true ) );
						$content = sprintf( '[advanced-members form="%s" title="%s"]', $hash, esc_attr($page['label']) );
						$content = sprintf( $blockCode, 'form', $form_id, '', $hash, esc_attr($page['label']), $content );
					}
					break;
				}

				$amem_core_page = array(
					'post_title'     => $page['label'],
					'post_content'   => $content,
					'post_name'      => $slug,
					'post_type'      => 'page',
					'post_status'    => 'publish',
					'post_author'    => $current_uid,
					'comment_status' => 'closed',
				);

				$page_id = wp_insert_post( $amem_core_page );
				update_post_meta( $page_id, 'amem_core_page', $slug );
				update_post_meta( $page_id, '_amem_form_inserted', ($form_id ? $form_id : $slug) );

				$core_pages[$slug] = $page_id;
			} else {
				$core_pages[$slug] = $isset_page;
			}
		}
		$options = get_option( 'amem_options', array() );
		if(empty($options)){
			$options = array();
		}
		// $change_option = false;
		foreach ($core_pages as $slug => $page_id) {
			$options['core_'.$slug] = $page_id;
			// $change_option = true;
		}

		$modules = get_option( 'amem_modules', array() );

		// if( $change_option ){
			update_option( 'amem_options', $options );
			update_option( 'amem_modules', $modules );
			update_option( 'amem_default_installed', true );
		// }
	}



}

amem()->register_module('setup', Setup::getInstance());
