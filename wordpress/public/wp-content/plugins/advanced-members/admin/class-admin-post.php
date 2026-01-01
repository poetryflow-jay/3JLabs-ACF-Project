<?php
/**
 * AMem Admin Form Class
 *
 * @class AMem\Admin\Form
 *
 * @package    AMem
 * @subpackage Admin
 */
namespace AMem\Admin;

use AMem\Singleton;

if ( ! class_exists( 'AMem\Admin\Post' ) ) :

	/**
	 * ACF Admin Post Class
	 *
	 * All the logic for editing a post type.
	 */
	abstract class Post extends \ACF_Admin_Internal_Post_Type {
		use Singleton;

		/**
		 * The slug for the internal post type.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $post_type = '';

		public $hook_name = '';

		public $field_prefix = '';

		/**
		 * The admin body class used for the post type.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $admin_body_class = '';


		public function __construct() {
			parent::__construct();

			$this->hook_name = str_replace( '-', '_', $this->post_type );
		}

		public function admin_body_class( $classes ) {
			$classes .= ' amem-admin-page acf-admin-page amem-admin-page-' . $this->post_type;

			return $classes;
		}

		/**
		 * Enqueues any scripts necessary for internal post type.
		 *
		 * @since 1.0.0
		 */
		public function admin_enqueue_scripts() {
			parent::admin_enqueue_scripts();

			do_action( 'acf/' . $this->hook_name . '/admin_enqueue_scripts' );
		}

		/**
		 * Sets up all functionality for the post type edit page to work.
		 *
		 * @since 1.0.0
		 */
		public function admin_head() {
			// global.
			global $post, $amem_current_post;

			// set global var.
			if ( !isset($amem_current_post) )
				$amem_current_post = $post;

			// 3rd party hook.
			do_action( 'acf/' . $this->hook_name . '/admin_head' );
		}

		function current_screen() {
			if ( ! acf_is_screen( $this->post_type ) ) {
				return;
			}

			add_action( 'load-post.php', array( $this, 'initialize' ), 20 );
			add_action( 'load-post-new.php', array( $this, 'initialize' ), 20 );

			add_action( 'edit_form_after_title', array( $this, 'edit_form_after_title' ), 10, 0 );

			add_action( 'acf/add_meta_boxes', array( $this, 'remove_acf_post_data') );

			add_filter( 'get_user_option_screen_layout_amem-form', array( $this, 'screen_layout' ), 10, 1 );

			add_action( 'add_meta_boxes', array( $this, 'register_fields' ), 5, 2 );

			parent::current_screen();
		}

		function remove_acf_post_data() {
			if ( $ACF_Form_Post = acf_get_instance( 'ACF_Form_Post' ) ) {
				remove_action( 'edit_form_after_title', array( $ACF_Form_Post, 'edit_form_after_title' ), 10 );				
			}
		}

		/**
		 *  Remove Submit Box
		 *
		 *  @since   1.0.0
		 */
		public function initialize() {
			remove_meta_box( 'submitdiv', 'amem-form', 'side' );
		}

		public function is_active( $post = null ) {
			if ( is_null( $post ) ) {
				global $amem_current_post;
				$post = $amem_current_post;
			}

			if ( !$post || !is_a( $post, 'WP_Post') )
				return false;

			$active = in_array( $post->post_status, array( 'publish', 'auto-draft' ) );

			return $active;
		}

		public function register_fields() {

		}

		public function verify_save_post( $post_id, $post ) {
			// Do not save if this is an auto save routine.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return false;
			}

			// Bail early if not an ACF internal post type.
			if ( $post->post_type !== $this->post_type ) {
				return false;
			}

			// Only save once! WordPress saves a revision as well.
			if ( wp_is_post_revision( $post_id ) ) {
				return false;
			}

			// Verify nonce.
			$nonce_name = str_replace(
				array( 'acf-', '-' ),
				array( '', '_' ),
				$this->post_type
			);

			if ( ! acf_verify_nonce( $nonce_name ) ) {
				return false;
			}

			// Bail early if request came from an unauthorised user.
			if ( ! current_user_can( acf_get_setting( 'capability' ) ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Set status, Save default form key and meta data outside ACF
		 *
		 * @since 1.0.0
		 * @param number $post_id
		 * @param object $post
		 */
		function save_post( $post_id, $post ) {
			if ( $this->post_type !== $post->post_type )
				return;

			if ( ! $this->verify_save_post( $post_id, $post ) ) {
				return $post_id;
			}

			// disable filters to ensure ACF loads raw data from DB.
			acf_disable_filters();

			$active_key = $this->field_prefix . 'active';
			$status = isset( $_POST['acf'][$active_key]) && $_POST['acf'][$active_key] ? 'publish' : 'acf-disabled';// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Validated in verify_save_post
			// phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			// phpcs:enable WordPress.Security.NonceVerification.Missing
			$new_status = '';

			if ( !wp_is_post_autosave($post_id) && 'draft' == $post->post_status ) {
				$new_status = 'publish';
			}

			if ( $post->post_status != $status ) {
				$new_status = $status;
			}

			if ( $new_status ) {
					wp_update_post(array(
				  'ID' => $post_id,
				  'post_status' => $new_status
			  ));
			}

			$this->register_fields();
			acf_save_post( $post_id );

			return true;
		}

		/**
		 * This action will allow ACF to render metaboxes after the title.
		 */
		public function edit_form_after_title() {

			// globals.
			global $post;

			// render post data.
			acf_form_data(
				array(
					'screen'        => $this->hook_name,
					'post_id'       => $post->ID,
					'delete_fields' => 0,
					'validation'    => 0,
				)
			);
		}

		/**
		 * This function will add extra HTML to the acf form data element
		 *
		 * @since   1.0.0
		 *
		 * @param array $args Arguments array to pass through to action.
		 * @return void
		 */
		public function form_data( $args ) {
			do_action( 'acf/' . $this->hook_name . '/form_data', $args );
		}

		/**
		 * This function will append extra l10n strings to the acf JS object
		 *
		 * @since   1.0.0
		 *
		 * @param array $l10n The array of translated strings.
		 * @return array $l10n
		 */
		public function admin_l10n( $l10n ) {
			return apply_filters( 'acf/' . $this->hook_name . '/admin_l10n', $l10n );
		}

		/**
		 * Admin footer third party hook support
		 *
		 * @since 1.0.0
		 */
		public function admin_footer() {
			do_action( 'acf/' . $this->hook_name . '/admin_footer' );
		}

		/**
		 * Screen settings html output
		 *
		 * @since   1.0.0
		 *
		 * @param string $html Current screen settings HTML.
		 * @return string $html
		 */
		public function screen_settings( $html ) {
			return $html;
		}

		/**
		 * Sets the "Edit Post Type" screen to use a one-column layout.
		 *
		 * @param integer $columns Number of columns for layout.
		 * @return integer
		 */
		public function screen_layout( $columns = 0 ) {
			return 1;
		}

	}

endif; // Class exists check.

?>
