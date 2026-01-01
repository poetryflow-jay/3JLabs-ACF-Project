<?php
namespace AMem;

use AMem\Module;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ADMIN' ) ) :
class ADMIN extends Module {

	public $parent_slug = 'edit.php?post_type=acf-field-group';

	protected $name = 'amem/admin';

	function __construct() {
		$this->inc = __DIR__ . '/';
		$this->name = 'amem/admin';

		add_filter( 'display_post_states', array( $this, 'add_display_post_states' ), 10, 2 );
		add_action( 'admin_notices', array( $this, 'render_notices' ), 1 );

		add_action( 'show_user_profile', array( $this, 'render_custom_user_profile_fields'), 10 );
		add_action( 'edit_user_profile', array( $this, 'render_custom_user_profile_fields'), 10 );

		add_action( 'amem_doaction_install_core_pages', array( $this, 'install_core_pages' ) );
		add_action( 'amem_doaction_not_install_core_pages', array( $this, 'not_install_core_pages' ) );

		// add_action( 'acf/input/admin_footer', array( $this, 'field_group_scripts') );
		add_action( 'admin_init', array( $this, 'install_amem_core_pages'),10 );

		// Field group admin customizations
		add_action( 'current_screen', array( $this, 'field_group_current_screen' ), 11 );
		add_action( 'save_post_acf-field-group', array( $this, 'check_amem_field_group' ), 10 );
		add_action( 'acf/input/admin_head', array( $this, 'field_group_admin_head' ), 11 );

		if ( !get_option( '__amem_migirate_form_meta_keys' ) ) {
			$this->migirate_form_meta_keys();
		}
	}

	function screens() {
		return apply_filters( 'amem/admin/screens', ['acf_page_amem_settings', 'acf_page_amem_dashboard'] );

	}

	function edit_screens() {
		return apply_filters( 'amem/admin/edit_screens', ['amem-form', 'edit-amem-form'] );
	}

	protected function migirate_form_meta_keys() {
		global $wpdb;
		if ( !get_option( '__amem_migirate_form_meta_keys' ) ) {
			// Fix dev version meta keys
			$form_ids = $wpdb->get_col("SELECT ID FROM `$wpdb->posts` WHERE post_type LIKE 'amem-form'" );
			
			if ( $form_ids ) {
				$sql = "UPDATE `$wpdb->postmeta` SET meta_key = REPLACE(meta_key, 'amem_form_', '') WHERE meta_key LIKE 'amem_form_%%' AND post_id IN (" . implode(',', $form_ids) . ")";
				$wpdb->query( $sql );
			}
			update_option( '__amem_migirate_form_meta_keys', 1 );
		}
	}

	/**
	 *  notice 호출
	 *
	 *  @since   1.0.0
	*/
	function render_notices() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$this->notices();
	}

	/**
	 *  기본 폼, 페이지 생성을 위한 notice
	 *
	 *  @since   1.0.0
	*/
	function notices() {
		// Re-init options while doing install core pages
		$notice = '';
		if ( current_user_can( 'manage_options' ) && ! get_option( 'amem_default_installed' ) ) {
			$url = add_query_arg(
				array(
					'amem_do_action' => 'install_core_pages',
					'_wpnonce'      => wp_create_nonce( 'install_core_pages' ),
				)
			);
			$not_url = add_query_arg(
				array(
					'amem_do_action' => 'not_install_core_pages',
					'_wpnonce'      => wp_create_nonce( 'install_core_pages' ),
				)
			);
			ob_start();
			?>
			<div class="info amem-admin-notice notice is-dismissible">
				<p>
					<?php
					// translators: %s: Plugin name.
					esc_html_e( 'Advanced Members needs to create several pages (Registration, Login, Password Reset, Account, Change Password, Delete Account, Logout) to function correctly.', 'advanced-members' );
					?>
				</p>
				<p>
					<a href="<?php echo esc_url( $url ); ?>" class="button button-primary"><?php esc_html_e( 'Create Pages', 'advanced-members' ); ?></a>
					&nbsp;
					<a href="<?php echo esc_url( $not_url ); ?>" class="button button-secondary amem_secondary_dismiss"><?php esc_html_e( 'No thanks', 'advanced-members' ); ?></a>
				</p>
			</div>
			<?php
			$notice = ob_get_clean();
			echo $notice;
		}
		add_action( 'show_user_profile', array( &$this, 'render_custom_user_profile_fields'), 10 );
		add_action( 'edit_user_profile', array( &$this, 'render_custom_user_profile_fields'), 10 );
	}

	/**
	 *  사용자 프로필 Account 폼을 보여줍니다
	 *
	 *  @since   1.0.0
	 *
	 *  @param   WP_User $user 사용자 object
	 */
	function render_custom_user_profile_fields( $user ) {
		// if( amem()->options->get('accform/account_form_showadmin') ) {
		// Removed option and provides hook
		if ( apply_filters( 'amem/account/fields/showadmin', true ) ) {
			$field_groups = $this->get_user_account_field_group($user);
			if( $field_groups ){
				acf_form_data(
					array(
						'screen'     => 'user',
						'post_id'    => 'user_'.$user->ID,
						'validation' => 1,
					)
				);

				$bypass = amem()->fields->predefined_fields();

				echo '<table class="form-table"><tbody>';
				echo '<h2>' . esc_html__('Advanced Members User Account Fields', 'advanced-members') . '</h2>';
				foreach ( $field_groups as $field_group ) {
					// vars
					$fields = acf_get_fields( $field_group );
					foreach ( $fields as $k => $field ) {
						if ( in_array($field['name'], $bypass) )
							unset($fields[$k]);
					}
					if ( $fields )
						acf_render_fields( $fields, 'user_'. $user->ID , 'tr', $field_group['instruction_placement'] );

				}
				echo '</tbody></table>';
			}
		}
	}

	public function add_display_post_states( $post_states, $post ) {
		foreach ( amem()->config->get_core_pages() as $page_key => $page_value ) {
			$page_id = amem()->options->get( amem()->options->get_core_page_id($page_key) );
			if ( $page_id == $post->ID ) {
				$post_states[ 'amem_' . $page_key ] = sprintf( 'Adv. Members %s', $page_value['label'] );
			}
		}
		return $post_states;
	}

	/**
	 *  사용자의 Account 필드 그룹 전달
	 *
	 *  @since   1.0.0
	 *
	 *  @param   WP_User $user 사용자 object
	 *  @return  array $field_groups 해당하는 form 의 필드그룹
	*/
	function get_user_account_field_group( $user ) {
		$user_roles = $user->roles;
		if (!empty($user_roles)) {
	  	$user_role = array_shift($user_roles);
		}
		$role = $user_role;
		$account_option = amem()->options->options['accform'];
		if( is_array($user_role) ){
			$role = $user_role[0];
		}
		$form_id = isset($account_option['default'])? $account_option['default'] : 0 ;
		if( isset( $account_option['rules'] ) ){
			foreach ($account_option['rules'] as $account_role) {
				if( $role == $account_role['role'] ){
					$form_id = $account_role['value'];
				}
			}
		}

		if( !$form_id ){
			return array();
		}
		$field_group_keys = array();
		$form = amem_get_form( $form_id );
		$field_groups = amem_get_form_field_groups( $form['key'] );
		/*
		foreach ($field_groups as $key => $field_group) {
			// $field_group_keys[$key] = $field_group['key'];
		}
		*/
		return $field_groups;
	}

	public function install_amem_core_pages() {
		if ( !empty($_REQUEST['amem_do_action']) && isset($_REQUEST['_wpnonce']) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'install_core_pages' ) ){
			do_action( 'amem_doaction_'. sanitize_text_field($_REQUEST['amem_do_action']) );
		}
	}
	public function not_install_core_pages() {
		amem()->setup()->not_install_core_pages();
	}

	public function install_core_pages() {
		amem()->setup()->install_default_forms();
	}

	function is_amem_field_group($post_id, $force_check = false) {
		if ( !$force_check )
			return get_post_meta($post_id, '_is_amem', true);

    $field_group = acf_get_internal_post_type( $post_id, 'acf-field-group' );
    $is_amem = false;
    if ( $field_group['location'] ) {
			foreach ( $field_group['location'] as $i => $rules ) {
	      foreach( $rules as $j => $rule ) {
	        if ( $rule['param'] == 'amem_form' ) {
	          $is_amem = true;
	          break;
	        }
	      }
			}
		}
		
		return $is_amem;
	}

	function field_group_admin_head() {
		global $field_group;

		$screen = get_current_screen();
		if ( acf_is_screen('acf-field-group') && $screen->action == 'add' && 
		    isset($field_group) && empty($field_group['locations']) && 
		    !empty($_GET['amem_form']) && strpos($_GET['amem_form'], 'form_') === 0 ) {
			$field_group['location'] = array(
				// Group 0.
				array(
					// Rule 0.
					array(
						'param'    => 'amem_form',
						'operator' => '==',
						'value'    => sanitize_key( $_GET['amem_form'] ),
					),
				),
			);
		}
	}

	function field_group_current_screen() {
    if ( ! acf_is_screen( "edit-acf-field-group" ) ) {
      return;
    }
    
    // add_filter( "views_edit-acf-field-group", array( $this, 'field_group_admin_table_views' ), 10, 1 );
    // add_filter( 'display_post_states', array( $this, 'field_group_display_post_states' ), 11, 2 );
    add_filter( 'request', array( $this, 'field_group_query_vars' ), 11 );
	}

	function update_amem_field_group_count() {
		add_action( 'shutdown', array( $this, '_update_amem_field_group_count' ) );
	}

	function _update_amem_field_group_count() {
		global $wpdb;
		static $counted;

		if ( isset( $counted) )
			return;

		$this->migrate_is_amem_field_group();

    $query = $wpdb->prepare( "SELECT COUNT(DISTINCT p.ID) 
			FROM $wpdb->posts p 
      INNER JOIN $wpdb->postmeta pm ON (p.ID = pm.post_id)
      WHERE 1=1
      AND pm.meta_key LIKE %s
      AND pm.meta_value <> ''
      ", '_is_amem' );

    $count = $wpdb->get_var( $query );

    update_option( 'amem_field_group_count', (int) $count );

    return $count;
	}

	function get_amem_field_group_count() {
		$count = get_option( 'amem_field_group_count', null );
		$count = null;
		if ( is_null( $count ) ) {
			$this->migrate_is_amem_field_group();
			$count= $this->_update_amem_field_group_count();
		}

		return (int) $count;
	}

	protected function migrate_is_amem_field_group() {
		global $wpdb;

		$migrated = get_option( '_migrate_is_amem_form_field_group' );

		if ( $migrated )
			return;

		$compare = '%s:5:\"param\";s:9:\"amem_form\"%';

		$sql = $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type LIKE %s AND post_content LIKE %s", 'acf-field-group', $compare );


		$field_groups = $wpdb->get_col( $sql );

		if ( $field_groups ) {
			foreach ( $field_groups as $field_group ) {
				// update_post_meta( $field_group, '_is_amem', true );
				$this->check_amem_field_group( $field_group );
			}
		}

		update_option( '_migrate_is_amem_form_field_group', true );
	}

	function check_amem_field_group($post_id) {

    $field_group = acf_get_internal_post_type( $post_id, 'acf-field-group' );
    delete_post_meta( $post_id, '_is_amem_form' );
    $is_amem = false;
    if ( $field_group['location'] ) {
			foreach ( $field_group['location'] as $i => $rules ) {
	      foreach( $rules as $j => $rule ) {
	        if ( $rule['param'] == 'amem_form' && $rule['operator'] == '==' && $rule['value'] ) {
	        	add_post_meta( $post_id, '_is_amem_form', $rule['value'] );
	        	$is_amem =  true;
	        }
	      }
			}
		}
		if ( $is_amem ) {
			update_post_meta( $post_id, '_is_amem', true );
		} else {
			delete_post_meta( $post_id, '_is_amem' );			
		}

    $this->update_amem_field_group_count();
	}

	function field_group_display_post_states($post_states, $post) {
    $is_amem = $this->is_amem_field_group($post->ID);
    if ( $is_amem ) {
    	$post_states['is_amem'] = ' <span class="dashicons dashicons-admin-users"></span> ' . __( 'Advanced Members', 'advanced-members' );
    }
    
    return $post_states;
	}

	function field_group_admin_table_views($views) {
	    global $wp_list_table, $wp_query;

	    $fg_admoin = acf_get_instance( 'ACF_Admin_Field_Groups' );
	    $count = $this->get_amem_field_group_count();
	    $amem_form = 'true';
	    if ( !empty($_GET['amem_form']) && is_numeric( $_GET['amem_form']) ) {
	        $amem_form = (int) $amem_form;
	    }
	    $views['amem'] = sprintf(
			'<a %s href="%s">%s <span class="count">(%s)</span></a>',
			( !empty($_GET['amem_form']) ? 'class="current"' : '' ),
			esc_url( $fg_admoin->get_admin_url( '&amem_form='.$amem_form ) ),
			esc_html( __( 'Advanced Members', 'advanced-members' ) ),
			$count
		);
		
		return $views;
	}

	function field_group_query_vars($query_vars) {
    if ( ! acf_is_screen( "edit-acf-field-group" ) ) {
      return $query_vars;
    }
    if ( empty($_REQUEST['amem_form']) ) {
      return $query_vars;
    }

    if ( empty($query_vars['meta_query']) ) {
      $query_vars['meta_query'] = [];
    }
    
    $amem_form = $_REQUEST['amem_form'];
    if ( $amem_form == 'true' ) {
      $query_vars['meta_query'][] = [
        'key' => '_is_amem',
        'compare' => 'EXISTS'
      ];
    } else {
      $query_vars['meta_query'][] = [
        'key' => '_is_amem_form',
        'value' => $amem_form
      ];
    }

    return $query_vars;
	}

}
amem()->register_module('admin', ADMIN::getInstance());

endif; // class_exists check
