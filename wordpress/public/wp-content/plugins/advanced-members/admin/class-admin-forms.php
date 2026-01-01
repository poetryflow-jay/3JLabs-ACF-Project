<?php
namespace AMem\Admin;

use AMem\Singleton;
use AMem\Admin\Posts;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'AMem\Admin\Forms' ) ) :

	/**
	 * The ACF Post Types admin controller class
	 */
	#[AllowDynamicProperties]
	class Forms extends Posts {
		use Singleton;

		/**
		 * The slug for the internal post type.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $post_type = 'amem-form';

		/**
		 * The admin body class used for the post type.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $admin_body_class = 'amem-admin-page amem-form-list';

		/**
		 * The name of the store used for the post type.
		 *
		 * @var string
		 */
		public $store = 'amem-forms';

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
			parent::current_screen();

			$screen = get_current_screen();
			if ( ! $screen )
				return false;

			$amem_screens = amem()->admin->screens();
			$amem_edit_screens = amem()->admin->edit_screens();
			if ( in_array( $screen->id, $amem_screens, true ) ) {
				add_action( 'in_admin_header', array( $this, 'after_in_admin_header' ) );
				return;
			}

			if ( isset( $screen->post_type ) && $screen->post_type === $this->post_type ) {
				// add_action( 'restrict_manage_posts', array( $this, 'add_custom_meta_filter_dropdown'), 10 );
				add_filter( 'pre_get_posts', array( $this, 'type_filter'), 10, 1);
			}
		}

		/**
		 * Add form type filter
		 *
		 * @since   1.0.0
		 */
		function add_custom_meta_filter_dropdown() {
			global $typenow;
			// 특정 포스트 타입에 대해서만 필터링 옵션 추가 (예: 'post', 'page' 등)
			if ($typenow === 'amem-form') {
				?>
				<select name="form_type">
					<option value=""><?php esc_html_e('Show all', 'advanced-members') ?></option>
					<option value="login"<?php echo (isset($_GET['form_type']) && 'login' == sanitize_key($_GET['form_type']) ? ' selected' : '') // phpcs:disable WordPress.Security.NonceVerification -- already verified by ACF ?>><?php esc_html_e('Login Forms', 'advanced-members') ?></option>
					<option value="registration"<?php echo (isset($_GET['form_type']) && 'registration' == sanitize_key($_GET['form_type']) ? ' selected' : '') // phpcs:disable WordPress.Security.NonceVerification -- already verified by ACF ?>><?php esc_html_e('Registration Forms', 'advanced-members') ?></option>
								<option value="account"<?php echo (isset($_GET['form_type']) && 'account' == sanitize_key($_GET['form_type']) ? ' selected' : '') // phpcs:disable WordPress.Security.NonceVerification -- already verified by ACF ?>><?php esc_html_e('Account Forms', 'advanced-members') ?></option>
					<!-- 필터 옵션을 추가하려는 만큼 <option> 태그를 추가합니다. -->
				</select>
				<?php
			}
		}

		/**
		 * Form Type Filter
		 *
		 * @since   1.0.0
		 * @param array $query
		 * @return array $query
		 */
		function type_filter( $query ) {
			if( isset($_REQUEST['form_type']) && !empty($_REQUEST['form_type']) ) { // phpcs:disable WordPress.Security.NonceVerification -- already verified by ACF
				$meta_query = array(
				  'key' => 'select_type',
				  'value' => sanitize_key($_REQUEST['form_type']), // phpcs:disable WordPress.Security.NonceVerification -- already verified by ACF
				  'compare' => '=',
			  );

			  // 메타 쿼리를 설정하여 사용자 목록을 필터링합니다.
			  $query->set('meta_query', array($meta_query));
			}
			return $query;
		}

		/**
		 * Admin 목록 에 column 추가
		 *
		 * @since   1.0.0
		 *
		 * @param   array $columns The columns array.
		 * @return  array
		 */
		public function admin_table_columns( $_columns ) {
			// Set the "no found" label to be our custom HTML for no results.
			if ( empty( acf_request_arg( 's' ) ) ) {
				global $wp_post_types;
				$this->not_found_label                                = $wp_post_types[ $this->post_type ]->labels->not_found;
				$wp_post_types[ $this->post_type ]->labels->not_found = $this->get_not_found_html();
			}

			$columns = array(
				'cb'              	=> $_columns['cb'],
				'title'           	=> $_columns['title'],
				'amem-id' 					=> __( 'ID', 'advanced-members' ),
				'amem-field_group'	=> __( 'Field Group', 'advanced-members' ),
				'amem-type'       	=> __( 'Type', 'advanced-members' ),
				'amem-shortcode'		=> __( 'Shortcode', 'advanced-members' ),
			);
			return $columns;
		}

		/**
		 * Admin 목록 추가된 column 의 값
		 *
		 * @since   1.0.0
		 *
		 * @param string $column_name The name of the column to display.
		 * @param array  $form_id  Form ID.
		 */
		public function admin_table_columns_html( $column_name, $form_id ) {
			switch ( $column_name ) {
				case 'amem-id':
					echo esc_html($form_id);
				break;

				case 'amem-field_group':
					$form = amem_get_form( $form_id );
					$field_groups = amem_get_form_field_groups( $form['key'] );
					if( empty( $field_groups ) ){
						esc_html_e('No connected field groups' , 'advanced-members');
					}else{
						foreach ( $field_groups as $key => $field_group ) {
							echo sprintf('%3$s<a href="%1$s">%2$s</a>', esc_url( get_edit_post_link( $field_group['ID'] ) ), esc_html($field_group['title']), $key > 0 ? ' ,' : ''  );
						}
					}
				break;

				case 'amem-type':
					$amem_types = amem_form_types('core');
					$type = get_post_meta( $form_id, 'select_type', true );
					echo ( isset($amem_types[$type]) ? esc_html($amem_types[$type]) : '' );
					if( $regist_role = get_post_meta( $form_id, 'regist_role', true ) ){
						echo sprintf('[%s]', esc_html(amem_get_role_label($regist_role)) );
					}
				break;

				case 'amem-shortcode':
					$code = sprintf( '[advanced-members form="%s"]', esc_attr($form_id) );
					echo '<code><span class="copyable">' . $code . '</span></code>';
				break;
			}
		}

		function after_in_admin_header() {
			global $title, $acf_page_title;

			$_acf_page_title = $acf_page_title;

			$acf_page_title = false;
			// acf_get_view( 'global/navigation' );

			$acf_page_title = $_acf_page_title;

			$screen = get_current_screen();
			$amem_screens = ['acf_page_amem_dashboard', 'acf_page_amem_settings'];

			if ( in_array( $screen->id, $amem_screens, true ) ) {
				acf_get_view( 'global/form-top' );
			}

			// do_action( 'acf/in_admin_header' );
			do_action( 'amem/in_admin_header' );
		}

		/**
		 * Renders a specific admin table column.
		 *
		 * @since   1.0.0
		 *
		 * @param string $column_name The name of the column to display.
		 * @param array  $post        The main ACF post array.
		 * @return void
		 */
		public function render_admin_table_column( $column_name, $post ) {
			switch ( $column_name ) {
				case 'acf-key':
					echo '<i class="acf-icon acf-icon-key-solid"></i>';
					echo esc_html( $post['key'] );
					break;

				// Description.
				case 'acf-description':
					if ( ( is_string( $post['description'] ) || is_numeric( $post['description'] ) ) && ! empty( $post['description'] ) ) {
						echo '<span class="acf-description">' . acf_esc_html( $post['description'] ) . '</span>';
					} else {
						echo '<span class="acf-emdash" aria-hidden="true">—</span>';
						echo '<span class="screen-reader-text">' . esc_html__( 'No description', 'advanced-members' ) . '</span>';
					}
					break;

				case 'acf-taxonomies':
					$this->render_admin_table_column_taxonomies( $post );
					break;

				case 'acf-field-groups':
					$this->render_admin_table_column_field_groups( $post );
					break;

				case 'acf-count':
					$this->render_admin_table_column_num_posts( $post );
					break;

				// Local JSON.
				case 'acf-json':
					$this->render_admin_table_column_local_status( $post );
					break;
			}
		}

		/**
		 * Renders the field groups attached to the post type in the list table.
		 *
		 * @since 1.0.0
		 *
		 * @param array $post_type The main post type array.
		 * @return void
		 */
		public function render_admin_table_column_field_groups( $post_type ) {
			$field_groups = acf_get_field_groups( array( 'post_type' => $post_type['post_type'] ) );

			if ( empty( $field_groups ) ) {
				echo '<span class="acf-emdash" aria-hidden="true">—</span>';
				echo '<span class="screen-reader-text">' . esc_html__( 'No field groups', 'advanced-members' ) . '</span>';
				return;
			}

			$labels        = wp_list_pluck( $field_groups, 'title' );
			$limit         = 3;
			$shown_labels  = array_slice( $labels, 0, $limit );
			$hidden_labels = array_slice( $labels, $limit );
			$text          = implode( ', ', $shown_labels );

			if ( ! empty( $hidden_labels ) ) {
				$text .= ', <span class="acf-more-items acf-js-tooltip" title="' . implode( ', ', $hidden_labels ) . '">+' . count( $hidden_labels ) . '</span>';
			}

			echo acf_esc_html( $text );
		}

		/**
		 * Renders the taxonomies attached to the post type in the list table.
		 *
		 * @since 1.0.0
		 *
		 * @param array $post_type The main post type array.
		 * @return void
		 */
		public function render_admin_table_column_taxonomies( $post_type ) {
			$taxonomies = array();
			$labels     = array();

			if ( is_array( $post_type['taxonomies'] ) ) {
				$taxonomies = $post_type['taxonomies'];
			}

			$acf_taxonomies = acf_get_internal_post_type_posts( 'acf-taxonomy' );

			foreach ( $acf_taxonomies as $acf_taxonomy ) {
				if ( is_array( $acf_taxonomy['object_type'] ) && in_array( $post_type['post_type'], $acf_taxonomy['object_type'], true ) ) {
					$taxonomies[] = $acf_taxonomy['taxonomy'];
				}
			}

			$taxonomies = array_unique( $taxonomies );

			foreach ( $taxonomies as $tax_slug ) {
				$taxonomy = get_taxonomy( $tax_slug );

				if ( ! is_object( $taxonomy ) || empty( $taxonomy->label ) ) {
					continue;
				}

				$labels[] = $taxonomy->label;
			}

			if ( empty( $labels ) ) {
				echo '<span class="acf-emdash" aria-hidden="true">—</span>';
				echo '<span class="screen-reader-text">' . esc_html__( 'No taxonomies', 'advanced-members' ) . '</span>';
				return;
			}

			$limit         = 3;
			$shown_labels  = array_slice( $labels, 0, $limit );
			$hidden_labels = array_slice( $labels, $limit );
			$text          = implode( ', ', $shown_labels );

			if ( ! empty( $hidden_labels ) ) {
				$text .= ', <span class="acf-more-items acf-js-tooltip" title="' . implode( ', ', $hidden_labels ) . '">+' . count( $hidden_labels ) . '</span>';
			}

			echo acf_esc_html( $text );
		}

	}

	// Instantiate.
	Forms::getInstance();

endif; // Class exists check.
