<?php
/**
 * Advanced Members Form Post Type
 *
 * @since 1.0.0
 * @package Advanced Members for ACF
 */

namespace AMem;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use AMem\Singleton;
use ACF_Internal_Post_Type;

if ( ! class_exists( 'AMem\Forms' ) ) {
	class Forms extends \ACF_Internal_Post_Type {
		use Singleton;

		/**
		 * The ACF internal post type name.
		 *
		 * @var string
		 */
		public $post_type = 'amem-form';

		/**
		 * The prefix for the key used in the main post array.
		 *
		 * @var string
		 */
		public $post_key_prefix = 'amem_form_';

		/**
		 * The cache key for a singular post.
		 *
		 * @var string
		 */
		public $cache_key = 'acf_get_amem_form_post:key:';

		/**
		 * The cache key for a collection of posts.
		 *
		 * @var string
		 */
		public $cache_key_plural = 'acf_get_amem_form_posts';

		/**
		 * The hook name for a singular post.
		 *
		 * @var string
		 */
		public $hook_name = 'amem_form';

		/**
		 * The hook name for a collection of posts.
		 *
		 * @var string
		 */
		public $hook_name_plural = 'amem_forms';

		/**
		 * The name of the store used for the post type.
		 *
		 * @var string
		 */
		public $store = 'amem-forms';

		/**
		 * Constructs the class.
		 */
		public function __construct() {
			$this->register_post_type();

			// Include admin classes in admin.
			if ( is_admin() ) {
				acf_include( 'includes/admin/admin-internal-post-type-list.php' );
				acf_include( 'includes/admin/admin-internal-post-type.php' );
				amem_include( 'admin/class-admin-post.php' );
				amem_include( 'admin/class-admin-form.php' );
				amem_include( 'admin/class-admin-posts.php' );
				amem_include( 'admin/class-admin-forms.php' );
				amem_include( 'admin/class-admin.php' );
				amem_include( 'admin/class-admin-options.php' );
			}

			parent::__construct();
		}

		/**
		 * Registers the acf-post-type custom post type with WordPress.
		 *
		 * @since 1.0.0
		 */
		public function register_post_type() {
			$cap = acf_get_setting( 'capability' );

			$labels = array(
				'name' => _x( 'Advanced Members Forms', 'Post Type General Name', 'advanced-members' ),
				'singular_name' => _x( 'Form', 'Post Type Singular Name', 'advanced-members' ),
				'menu_name' => __( 'Members Forms', 'advanced-members' ),
				'name_admin_bar' => __( 'Form', 'advanced-members' ),
				'archives' => __( 'Form Archives', 'advanced-members' ),
				'parent_item_colon' => __( 'Parent Form:', 'advanced-members' ),
				'all_items' => __( 'Forms', 'advanced-members' ),
				'add_new_item' => __( 'Add New Form', 'advanced-members' ),
				'add_new' => __( 'Add New', 'advanced-members' ),
				'new_item' => __( 'New Form', 'advanced-members' ),
				'edit_item' => __( 'Edit Form', 'advanced-members' ),
				'update_item' => __( 'Update Form', 'advanced-members' ),
				'view_item' => __( 'View Form', 'advanced-members' ),
				'search_items' => __( 'Search Form', 'advanced-members' ),
				'not_found' => __( 'Not found', 'advanced-members' ),
				'not_found_in_trash' => __( 'Not found in Trash', 'advanced-members' ),
				'featured_image' => __( 'Featured Image', 'advanced-members' ),
				'set_featured_image' => __( 'Set featured image', 'advanced-members' ),
				'remove_featured_image' => __( 'Remove featured image', 'advanced-members' ),
				'use_featured_image' => __( 'Use as featured image', 'advanced-members' ),
				'insert_into_item' => __( 'Insert into form', 'advanced-members' ),
				'uploaded_to_this_item' => __( 'Uploaded to this form', 'advanced-members' ),
				'items_list' => __( 'Forms list', 'advanced-members' ),
				'items_list_navigation' => __( 'Forms list navigation', 'advanced-members' ),
				'filter_items_list' => __( 'Filter forms list', 'advanced-members' ),
			);

			register_post_type(
				'amem-form',
				array(
					'label' => __( 'Form', 'advanced-members' ),
					'description' => __( 'Form', 'advanced-members' ),
					'labels'          => $labels,
					'public'          => false,
					'hierarchical'    => false,
					'show_ui'         => true,
					'show_in_menu'    => false,
					'_builtin'        => false,
					'capability_type' => 'page',
					'capabilities'    => array(
						'edit_post'    => $cap,
						'delete_post'  => $cap,
						'edit_posts'   => $cap,
						'delete_posts' => $cap,
					),
					'supports'        => false,
					'rewrite'         => false,
					'query_var'       => false,

					'menu_position' => 80,
					'show_in_admin_bar' => false,
				)
			);
		}

		/**
		 * Gets the default settings array for an ACF post type.
		 *
		 * @return array
		 */
		public function get_settings_array() {
			return array(
				// ACF-specific settings.
				'ID'                       => 0,
				'key'                      => '',
				'title'                    => '',
				'menu_order'               => 0,
				'active'                   => true,
				'post_type'                => '', // First $post_type param passed to register_post_type().
				'advanced_configuration'   => false,
				'import_source'            => '',
				'import_date'              => '',
				// Settings passed to register_post_type().
				'labels'                   => array(
					'name'                     => '',
					'singular_name'            => '',
					'menu_name'                => '',
					'all_items'                => '',
					'add_new'                  => '',
					'add_new_item'             => '',
					'edit_item'                => '',
					'new_item'                 => '',
					'view_item'                => '',
					'view_items'               => '',
					'search_items'             => '',
					'not_found'                => '',
					'not_found_in_trash'       => '',
					'parent_item_colon'        => '',
					'archives'                 => '',
					'attributes'               => '',
					'featured_image'           => '',
					'set_featured_image'       => '',
					'remove_featured_image'    => '',
					'use_featured_image'       => '',
					'insert_into_item'         => '',
					'uploaded_to_this_item'    => '',
					'filter_items_list'        => '',
					'filter_by_date'           => '',
					'items_list_navigation'    => '',
					'items_list'               => '',
					'item_published'           => '',
					'item_published_privately' => '',
					'item_reverted_to_draft'   => '',
					'item_scheduled'           => '',
					'item_updated'             => '',
					'item_link'                => '',
					'item_link_description'    => '',
				),
				'description'              => '',
				'public'                   => true, // WP defaults false, ACF defaults true.
				'hierarchical'             => false,
				'exclude_from_search'      => false,
				'publicly_queryable'       => true,
				'show_ui'                  => true,
				'show_in_menu'             => true,
				'admin_menu_parent'        => '',
				'show_in_admin_bar'        => true,
				'show_in_nav_menus'        => true,
				'show_in_rest'             => true,
				'rest_base'                => '',
				'rest_namespace'           => 'wp/v2',
				'rest_controller_class'    => 'WP_REST_Posts_Controller',
				'menu_position'            => null,
				'menu_icon'                => '',
				'rename_capabilities'      => false,
				'singular_capability_name' => 'post',
				'plural_capability_name'   => 'posts',
				'supports'                 => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
				'taxonomies'               => array(),
				'has_archive'              => false,
				'has_archive_slug'         => '',
				'rewrite'                  => array(
					'permalink_rewrite' => 'post_type_key', // ACF-specific option.
					'slug'              => '',
					'feeds'             => false,
					'pages'             => true,
					'with_front'        => true,
				),
				'query_var'                => 'post_type_key',
				'query_var_name'           => '', // ACF-specific option.
				'can_export'               => true,
				'delete_with_user'         => false,
				'register_meta_box_cb'     => '',
				'enter_title_here'         => '',
			);
		}

		/**
		 * Validates post type values before allowing save from the global $_POST object.
		 * Errors are added to the form using acf_add_internal_post_type_validation_error().
		 *
		 * @since 1.0.0
		 *
		 * @return boolean validity status
		 */
		public function ajax_validate_values() {
			if ( empty( $_POST['acf_post_type']['post_type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified elsewhere.
				return false;
			}

			$post_type_key = acf_sanitize_request_args( wp_unslash( $_POST['acf_post_type']['post_type'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified elsewhere.

			$valid = apply_filters( "acf/{$this->hook_name}/ajax_validate_values", $valid, $_POST['acf_post_type'] ); // phpcs:ignore WordPress.Security -- Raw input send to hook for validation.

			return $valid;
		}

	}

}

amem()->register_module( 'forms', Forms::getInstance() );
