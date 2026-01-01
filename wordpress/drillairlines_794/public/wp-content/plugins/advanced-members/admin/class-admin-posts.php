<?php
namespace AMem\Admin;

use AMem\Singleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'AMem\Admin\Posts' ) ) :

	/**
	 * The ACF Posts admin controller class
	 */
	abstract class Posts extends \ACF_Admin_Internal_Post_Type_List {
		use Singleton;

		/**
		 * The slug for the internal post type.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $post_type = '';

		/**
		 * The admin body class used for the post type.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $admin_body_class = '';

		/**
		 * The name of the store used for the post type.
		 *
		 * @var string
		 */
		public $store = '';

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Current screen actions for the post types list admin page.
		 *
		 * @since 1.0.0
		 */
		public function current_screen() {
			$screen = get_current_screen();

			if ( !$screen || !isset( $screen->post_type ) || $screen->post_type !== $this->post_type )
				return;
			if ( isset( $screen->base ) && 'edit' === $screen->base ) {
				$this->screen = 'list';
			}
			add_action( 'in_admin_header', array( $this, 'in_admin_header' ) );

			add_action( 'admin_body_class', array( $this, 'admin_body_class' ) );

			add_filter( "manage_{$this->post_type}_posts_columns", array( $this, 'admin_table_columns' ), 10, 1 );
			add_action( "manage_{$this->post_type}_posts_custom_column", array( $this, 'admin_table_columns_html' ), 10, 2 );

			add_filter( "views_edit-{$this->post_type}", array( $this, 'admin_table_views' ), 10, 1 );
			add_filter( 'display_post_states', array( $this, 'display_post_states' ), 10, 2 );
			add_filter( "bulk_actions-edit-{$this->post_type}", array( $this, 'admin_table_bulk_actions' ), 10, 1 );

			// Get the current view.
			$this->view = acf_request_arg( 'post_status', '' );

			// Setup and check for custom actions.
			// $this->check_duplicate();
			$this->check_activate();
			$this->check_deactivate();

			// Modify publish post status text and order.
			global $wp_post_statuses;
			/* translators: %s: Active posts count */
			$wp_post_statuses['publish']->label_count = _n_noop( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', 'advanced-members' );
			$wp_post_statuses['trash']                = acf_extract_var( $wp_post_statuses, 'trash' );


			if ( $this->view !== 'trash' ) {
				add_filter( 'post_row_actions', array( $this, 'page_row_actions' ), 10, 2 );
			}

			// remove_all_filters('months_dropdown_results');
			// add_filter( 'months_dropdown_results', '__return_empty_array', 999 );
		}

		public function admin_body_class( $classes ) {
			$classes .= ' amem-admin-page acf-admin-page acf-internal-post-type';
			if( $this->screen == 'list'){
				$classes .= " {$this->post_type}-list amem-post-list";
			}

			return $classes;
		}

		/**
		 * Fix layout to 1
		 *
		 * @param int $columns layout column count.
		 *
		 * @return int
		 */
		public function screen_layout( $columns = 0 ) {
			return 1;
		}

		/**
		 * Customize row actions
		 *
		 * @since   1.0.0
		 *
		 * @param   array   $actions The array of actions HTML.
		 * @param   WP_Post $post The post.
		 * @return  array $actions
		 */
		 public function page_row_actions( $actions, $post ) {
			// Remove "Quick Edit" action.
			unset( $actions['inline'], $actions['inline hide-if-no-js'] );

			$duplicate_action_url = '';

			// // Append "Duplicate" action.
			// if ( 'amem-form' === $this->post_type ) {
			// 	$duplicate_action_url = $this->get_admin_url( '&acfduplicate=' . $post->ID . '&_wpnonce=' . wp_create_nonce( 'bulk-posts' ) );
			// 	// $actions['acfduplicate'] = '<a href="' . esc_url( $duplicate_action_url ) . '" aria-label="' . esc_attr__( 'Duplicate this item', 'advanced-members' ) . '">' . __( 'Duplicate', 'advanced-members' ) . '</a>';
			// }

			// Append the "Activate" or "Deactivate" actions.
			if ( 'acf-disabled' === $post->post_status ) {
				$activate_deactivate_action = 'acfactivate';
				$activate_action_url        = $this->get_admin_url( '&acfactivate=' . $post->ID . '&_wpnonce=' . wp_create_nonce( 'bulk-posts' ) );
				$actions['acfactivate']     = '<a href="' . esc_url( $activate_action_url ) . '" aria-label="' . esc_attr__( 'Activate this item', 'advanced-members' ) . '">' . __( 'Activate', 'advanced-members' ) . '</a>';
			} else {
				$activate_deactivate_action = 'acfdeactivate';
				$deactivate_action_url      = $this->get_admin_url( '&acfdeactivate=' . $post->ID . '&_wpnonce=' . wp_create_nonce( 'bulk-posts' ) );
				$actions['acfdeactivate']   = '<a href="' . esc_url( $deactivate_action_url ) . '" aria-label="' . esc_attr__( 'Deactivate this item', 'advanced-members' ) . '">' . __( 'Deactivate', 'advanced-members' ) . '</a>';
			}

			// Return actions in custom order.
			$order = array( 'edit', /*'acfduplicate',*/ $activate_deactivate_action, 'trash' );

			return array_merge( array_flip( $order ), $actions );
		}

		/**
		 * Admin 상단에 ACF 네비게이션 을 보여준다
		 *
		 * @since   1.0.0
		 */
		function in_admin_header() {
			global $title, $post_new_file, $post_type_object, $acf_page_title, $post;

			$_acf_page_title = $acf_page_title;

			$acf_page_title = false;
			acf_get_view( 'global/navigation' );

			$acf_page_title = $_acf_page_title;

			$screen = get_current_screen();
			if ( isset( $screen->base ) ) {
				if ( 'post' === $screen->base ) {
					acf_get_view( 'global/form-top' );
					amem_get_view( __DIR__ . '/views/form-top-title.php' );
				} elseif ( 'edit' === $screen->base ) {
					acf_get_view( 'global/header' );
				}
			}

			// do_action( 'acf/in_admin_header' );
			do_action( 'amem/in_admin_header' );
		}

		/**
		 * Renders the number of posts created for the post type in the list table.
		 *
		 * @since 1.0.0
		 *
		 * @param array $post_type The main post type array.
		 * @return void
		 */
		public function render_admin_table_column_num_posts( $post_type ) {
			$no_posts  = '<span class="acf-emdash" aria-hidden="true">—</span>';
			$no_posts .= '<span class="screen-reader-text">' . esc_html__( 'No posts', 'advanced-members' ) . '</span>';

			// WP doesn't count posts for post types that don't exist.
			if ( empty( $post_type['active'] ) || 'trash' === get_post_status( $post_type['ID'] ) ) {
				echo acf_esc_html( $no_posts );
				return;
			}

			$num_posts = wp_count_posts( $post_type['post_type'] );
			if ( is_object( $num_posts ) && property_exists( $num_posts, 'publish' ) ) {
				$num_posts = $num_posts->publish;
			}

			if ( ! $num_posts || ! is_numeric( $num_posts ) ) {
				echo acf_esc_html( $no_posts );
				return;
			}

			printf(
				'<a href="%s">%s</a>',
				esc_url( admin_url( 'edit.php?post_type=' . $post_type['post_type'] ) ),
				esc_html( number_format_i18n( $num_posts ) )
			);
		}

		/**
		 * Gets the translated action notice text for list table actions (activate, deactivate, sync, etc.).
		 *
		 * @since 1.0.0
		 *
		 * @param string  $action The action being performed.
		 * @param integer $count  The number of items the action was performed on.
		 * @return string
		 */
		public function get_action_notice_text( $action, $count = 1 ) {
			$text  = '';
			$count = (int) $count;

			switch ( $action ) {
				case 'acfactivatecomplete':
					$text = sprintf(
						/* translators: %s: number of post types activated */
						_n( '%s Form activated.', '%s forms activated.', $count, 'advanced-members' ),
						$count
					);
					break;
				case 'acfdeactivatecomplete':
					$text = sprintf(
						/* translators: %s: number of post types deactivated */
						_n( '%s Form deactivated.', '%s forms deactivated.', $count, 'advanced-members' ),
						$count
					);
					break;
				case 'acfduplicatecomplete':
					$text = sprintf(
						/* translators: %s: number of post types duplicated */
						_n( '%s Form duplicated.', '%s forms duplicated.', $count, 'advanced-members' ),
						$count
					);
					break;
				case 'acfsynccomplete':
					$text = sprintf(
						/* translators: %s: number of post types synchronized */
						_n( '%s Form synchronized.', '%s forms synchronized.', $count, 'advanced-members' ),
						$count
					);
					break;
			}

			return $text;
		}

	}

endif; // Class exists check.
