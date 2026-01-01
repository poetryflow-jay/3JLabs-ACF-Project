<?php
/**
 * Manage Advanced Members for ACF Form Ristriction
 *
 * @since 	0.9.17
 * 
 */
namespace AMem;

use AMem\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Restriction extends Module {

	protected $inc = '';
	protected $name = 'amem/restriction';

	protected $allow_access = false;

	protected $sanitizers = [
		'who' => 'text',
		'roles' => 'array_text',
		'method' => 'text',
		'redirect_url' => 'url',
		'custom_message' => 'bool',
		'message' => 'html',
	];


	function __construct() {
		if ( amem()->options->getmodule('_use_restriction') ) {
			// Fixed option check
			add_filter( 'amem/option/get/restriction/methods', function($value) {
				if ( !is_array($value) || empty($value) )
					$value = ['redirect_login' => 1];

				return $value;
			} );

			// Fixed option check
			add_filter( 'amem/option/get/restriction/methods/redirect_login', '__return_true' );

			// add_filter( 'amem/restriction/post/ignore', array( $this, 'ignore_core_pages' ), 10, 2 );
			/** 
			 * Posts
			 */
			if ( is_admin() ) {
				add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
			}
			add_action( 'save_post', array( $this, 'save_post' ) );

			// change the content of the restricted post
			add_filter( 'the_content', array( $this, 'maybe_restirct_post_content' ), PHP_INT_MAX );
			// change the excerpt of the restricted post
			add_filter( 'get_the_excerpt', array( $this, 'maybe_restrict_post_excerpt' ), PHP_INT_MAX, 2 );

			add_action( 'template_redirect', array( $this, 'maybe_redirect_restricted' ), PHP_INT_MAX );

			/** 
			 * Terms
			 */
			// register actions
			add_action( 'admin_enqueue_scripts', array( $this, 'register_term_actions' ) );

			// save
			add_action( 'edit_term', array( $this, 'save_term' ), 10, 3 );
		}
	}

	function ignore_core_pages($ignore, $post_id) {
		$core_pages = amem()->options->permalinks;
		if ( in_array( $post_id, $core_pages ) )
			return true;

		return $ignore;
	}

	/**
	 * Register actions for terms
	 *
	 */
	function register_term_actions() {
		// global
		global $pagenow;

		// validate page
		if ( $pagenow !== 'term.php' ) {
			return;
		}

		// vars
		$screen   = get_current_screen();
		$taxonomy = $screen->taxonomy;

		if ( !in_array( $taxonomy, $this->taxonomies() ) )
			return;

		// actions
		add_action( "{$taxonomy}_edit_form", array( $this, 'render_term_meta_box' ), 10, 2 );
	}

	protected function verify_save_rule() {
		if ( !isset($_POST['amem']) || !isset($_POST['amem']['restriction']) || !isset($_POST['_amemrestriction']) ) {
			return false;
		}

		$nonce = sanitize_key($_POST['_amemrestriction']);

		if ( !wp_verify_nonce( $nonce, 'amem-restriction') ) {
			return false;
		}

		return true;
	}

	function save_term( $term_id, $tt_id, $taxonomy ) {
		if ( !$this->verify_save_rule() )
			return;

		$data = amem_sanitize_data( $_POST['amem']['restriction'], $this->sanitizers );

		update_term_meta( $term_id, 'amem_content_restriction', $data );
	}

	function save_post($post_id) {
		if ( !$this->verify_save_rule() )
			return;

		$data = amem_sanitize_data( $_POST['amem']['restriction'], $this->sanitizers );

		update_post_meta( $post_id, 'amem_content_restriction', $data );
	}

	function check_user_role( $user_id, $roles ) {
		if ( is_super_admin($user_id) || user_can($user_id, 'administrator') ) {
			return true;
		}

		// means all roles
		if ( empty($roles) )
			return true;

		amem()->user->switch($user_id);
		$user_roles = amem_user('roles');
		amem()->user->restore();

		if ( !$user_roles )
			return false;

		if ( !is_array($user_roles) )
			$user_roles = explode(',', $user_roles);

		return array_intersect($roles, $user_roles);
	}

	protected function post_types() {
		$post_types = (array) amem()->options->get( 'restriction/post_types' );

		return (array) apply_filters( 'amem/restriction/post_types', $post_types );
	}

	protected function taxonomies() {
		$taxonomies = (array) amem()->options->get( 'restriction/taxonomies' );

		return (array) apply_filters( 'amem/restriction/taxonomies', $taxonomies );
	}

	/**
	 * Chcek if post restricted
	 *
	 * @param int $post_id 	ID of post
	 * @param bool $instant 	Is for single page
	 * @param bool $force 	Bypass cache 	
	 *
	 * @return boolean
	 */
	function is_restricted( $post_id, $instant = false, $force = false ) {
		static $cache = array();
		if ( empty( $post_id ) ) {
			return false;
		}

		if ( isset( $cache[ $post_id ] ) && ! $force ) {
			return $cache[ $post_id ];
		}

		// Bypass Admin
		if ( current_user_can( 'administrator' ) ) {
			if ( ! $force ) {
				$cache[ $post_id ] = false;
			}
			return false;
		}

		$post = get_post( $post_id );
		// Bypass post author
		if ( is_user_logged_in() && !empty( $post->post_author ) && $post->post_author == get_current_user_id() ) {
			if ( ! $force ) {
				$cache[ $post_id ] = false;
			}
			return false;
		}

		$restricted = false;

		$rule = $this->get_post_rule( $post_id );
		if ( !empty($rule) ) {
			$restricted = $this->is_restricted_by_rule( $rule );
		} elseif ( $rule = $this->get_term_rule( $post_id ) ) {
			$restricted = $this->is_restricted_by_rule( $rule );
		}

		$restricted = apply_filters( 'amem/restriction/post/restricted', $restricted, $post_id, $instant );

		if ( ! $force ) {
			$cache[ $post_id ] = $restricted;
		}

		return $restricted;
	}

	/**
	 * Is term restricted?
	 *
	 * @param int $term_id
	 * @param bool $instant
	 * @param bool $force
	 *
	 * @return bool
	 * @todo 
	 */
	function is_restricted_term( $term_id, $instant = false, $force = false ) {
		static $cache = array();

		if ( isset( $cache[ $term_id ] ) && ! $force ) {
			return $cache[ $term_id ];
		}

		if ( current_user_can( 'administrator' ) ) {
			if ( ! $force ) {
				$cache[ $term_id ] = false;
			}
			return false;
		}

		$taxonomies = $this->taxonomies();
		if ( empty( $taxonomies ) ) {
			if ( ! $force ) {
				$cache[ $term_id ] = false;
			}
			return false;
		}

		$term = get_term( $term_id );
		if ( !$term || is_wp_error( $term ) || !in_array( $term->taxonomy, $taxonomies ) ) {
			if ( ! $force ) {
				$cache[ $term_id ] = false;
			}
			return false;
		}

		$restricted = false;

		$rule = get_term_meta( $term_id, 'amem_content_restriction', true );


		if ( !empty( $rule ) ) {
			$restricted = $this->is_restricted_by_rule($rule);
		}

		$restricted = apply_filters( 'amem/restriction/term/restricted', $restricted, $term_id, $instant );

		if ( ! $force ) {
			$cache[ $term_id ] = $restricted;
		}
		return $restricted;
	}

	/**
	 * Get restriction settings for post
	 *
	 * @param WP_Post|int $post Post ID or object
	 *
	 * @return bool|array
	 */
	function get_post_rule( $post ) {
		static $cache = array();
		if ( empty( $post ) ) {
			return false;
		}

		if ( ! is_numeric( $post ) && ! is_a( $post, 'WP_Post' ) ) {
			return false;
		}

		$cache_key = is_numeric( $post ) ? $post : $post->ID;

		if ( isset( $cache[ $cache_key ] ) ) {
			return $cache[ $cache_key ];
		}

		if ( is_numeric( $post ) ) {
			$post = get_post( $post );
		}

		// Bypass Admin
		if ( current_user_can( 'administrator' ) ) {
			$cache[ $cache_key ] = false;
			return false;
		}

		$bypass = false;
		$rule = false;

		if ( $post->post_type === 'page' ) {

			if ( amem_is_core_post( $post, 'all' ) )
				$bypass = true;
		}

		$bypass = apply_filters( 'amem/restriction/post/bypass', $bypass, $post );
		if ( $bypass ) {
			$cache[ $cache_key ] = false;
			return false;
		}

		$post_types = $this->post_types();

		if ( in_array( $post->post_type, $post_types ) ) {
			$rule = (array)get_post_meta( $post->ID, 'amem_content_restriction', true );

			if ( ! empty( $rule['who'] ) ) {
				$rule = apply_filters( 'amem/restriction/post/settings', $rule, $post );

				$cache[ $cache_key ] = $rule;
				return $rule;
			}
			// Only apply enabled post types and it's taxonomy terms
			if ( $rule = $this->get_term_rule($post) ) {
				$cache[ $cache_key ] = $rule;
				return $rule;
			}
		}

		$cache[ $cache_key ] = false;

		return false;
	}

	function get_term_rule( $post ) {
		if ( !is_a( $post, 'WP_Post') )
			$post = get_post($post);
		if ( !$post || !is_a($post, 'WP_Post') )
			return false;

		$restricted_taxonomies = amem()->options->get( 'restriction/taxonomies' );

		if ( empty($restricted_taxonomies) )
			return false;

		//get all taxonomies for current post type
		$taxonomies = get_object_taxonomies( $post );

		//get all post terms
		$terms = array();
		if ( ! empty( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				if ( !in_array( $taxonomy, $restricted_taxonomies, true ) ) {
					continue;
				}

				$terms = array_merge( $terms, wp_get_post_terms( $post->ID, $taxonomy, array( 'fields' => 'ids', 'amem_ignore_exclude' => true, ) ) );
			}
		}

		//get restriction options for first term with privacy settings
		foreach ( $terms as $term_id ) {
			$rule = (array)get_term_meta( $term_id, 'amem_content_restriction', true );
			if ( ! empty( $rule['who'] ) ) {
				return $rule;
			}
		}

		return false;
	}

	function maybe_redirect_restricted() {
		if ( !(is_singular() && !is_tax()) || is_admin() || is_404() )
			return;

		$object_id = get_queried_object_id();
		$is_tax = is_tax();
		$hook =  $is_tax ? 'term' : 'post';

		$ignore = apply_filters( "amem/restriction/{$hook}/ignore", false, $object_id );
		if ( $ignore ) {
			return;
		}

		if ( !$is_tax && !$this->is_restricted($object_id) )
			return;
		elseif ( $is_tax && $this->is_restricted_term($object_id) )
			return;

		if ( !$is_tax ) {
			$rule = $this->get_post_rule( $object_id );
		} else {
			return;
			// $rule = $this->get_term_rule( $object_id );
		}

		if ( !in_array( $rule['method'], ['redirect_login', 'redirect_custom'], true ) )
			return;

		if ( $rule['method'] == 'redirect_custom' && !empty($rule['redirect_url']) ) {
			$redirect = esc_url_raw( $rule['redirect_url'] );
		} else {
			$redirect = amem_get_core_page( 'login' );
		}

		if ( $current = amem_get_current_url() ) {
			$redirect = add_query_arg( 'redirect_to', $current, $redirect );
		}

		wp_safe_redirect( $redirect );
		exit;
	}

	function maybe_restirct_post_content($content) {
		if ( current_user_can( 'administrator' ) ) {
			return $content;
		}

		$id = get_the_ID();
		if ( ! $id || is_admin() ) {
			return $content;
		}

		$ignore = apply_filters( 'amem/restriction/post/ignore', false, $id );
		if ( $ignore ) {
			return $content;
		}

		$origin_content = $content;

		if ( $this->is_restricted( $id ) ) {
			$rule = $this->get_post_rule( $id );

			$origin_content = $content;

			switch( $rule['method'] ) {
				case 'show_message':
				case 'show_excerpt_message':
				$message = stripslashes( amem()->options->get( 'restriction/message' ) );
				if ( $rule['custom_message'] && !empty($rule['message']) ) {
					$message = $rule['message'];
				}

				if ( $rule['method'] == 'show_excerpt_message' ) {
					$excerpt = $this->get_the_excerpt( $id );
					$excerpt .= '&hellip;';
					$excerpt = $excerpt ? '<div class="amem-restricted-excerpt">' . $excerpt . '</div>' : '';
				}

				$content = $excerpt . '<div class="amem-restricted-message">' . $message . '</div>';
				$content = apply_shortcodes( $content );
				break;

				// maybe in the loop or custom codes
				case 'redirect_login':
				case 'redirect_custom':
				default:
				$content = stripslashes( amem()->options->get( 'restriction/message' ) );
				break;
			}

			$content = apply_filters( 'amem/restricted/post/content', $content, $id, $origin_content );
		}

		return $content;
	}

	function get_the_excerpt( $id ) {
		remove_filter( 'the_content', array( $this, 'maybe_restirct_post_content' ), PHP_INT_MAX );
		remove_filter( 'get_the_excerpt', array( $this, 'maybe_restrict_post_excerpt' ), PHP_INT_MAX, 2 );
		add_filter( 'excerpt_more', array( $this, 'remove_excerpt_more'), PHP_INT_MAX );

		$excerpt = get_the_excerpt( $id );

		remove_filter( 'excerpt_more', array( $this, 'remove_excerpt_more'), PHP_INT_MAX );
		add_filter( 'the_content', array( $this, 'maybe_restirct_post_content' ), PHP_INT_MAX );
		add_filter( 'get_the_excerpt', array( $this, 'maybe_restrict_post_excerpt' ), PHP_INT_MAX, 2 );

		return $excerpt;
	}

	function remove_excerpt_more($more) {
		return '';
	}

	function maybe_restrict_post_excerpt( $post_excerpt, $post = null ) {
		if ( empty( $post ) || !is_a( $post, 'WP_Post') ) {
			return $post_excerpt;
		}

		if ( current_user_can( 'administrator' ) || is_admin() ) {
			return $post_excerpt;
		}

		$ignore = apply_filters( 'amem/restriction/post/ignore', false, $post->ID );
		if ( $ignore ) {
			return $post_excerpt;
		}

		if ( $this->is_restricted( $post->ID ) ) {
			$rule = $this->get_post_rule( $post->ID );
			if ( $rule['method'] !== 'show_excerpt_message' )
				$post_excerpt = '';
		}

		return $post_excerpt;
	}

	function is_restricted_by_rule( $rule, $instant = false ) {
		$restricted = true;
		if ( empty( $rule['who'] ) ) {
			$restricted = false;
		} else {
			if ( 'loggedout' == $rule['who'] ) {
				if ( ! is_user_logged_in() ) {
					$restricted = false;
					if ( $instant ) {
						$this->allow_access = true;
					}
				}
			} elseif ( 'loggedin' == $rule['who'] ) {
				if ( is_user_logged_in() ) {
					$restricted = false;
					if ( $instant ) {
						$this->allow_access = true;
					}
				}
			} elseif ( is_user_logged_in() && 'roles' == $rule['who'] ) {
				if ( empty( $rule['roles'] ) ) {
					$restricted = false;
					if ( $instant ) {
						$this->allow_access = true;
					}
				} else {
					$user_can = $this->check_user_role( get_current_user_id(), $rule['roles'] );
					if ( $user_can ) {
						$restricted = false;
						if ( $instant ) {
							$this->allow_access = true;
						}
					}
				}
			}
		}

		return $restricted;
	}

	function validate_rule($rule = []) {
		return wp_parse_args( $rule, [
			'who' => '',
			'roles' => [],
			'method' => 'redirect_login',
			'redirect_url' =>'',
			'custom_message' => 0,
			'message' => '',
		] );
	}

	function add_meta_box($current_post_type) {
		$post_types = $this->post_types();

		if ( !in_array( $current_post_type, $post_types) )
			return;

		add_meta_box( 'amem-content-restriction', esc_html( 'Content Restriction Rule', 'advanced-members' ), array( $this, 'render_post_meta_box' ), $current_post_type, 'normal', 'low' );
	}

	function render_post_meta_box( $post ) {
		wp_nonce_field( 'amem-restriction', '_amemrestriction', false, true );

		$data = (array) get_post_meta( $post->ID, 'amem_content_restriction', true );

		$this->render_meta_box( $data, 'div' );
	}

	function render_term_meta_box( $term, $taxonomy ) {
		wp_nonce_field( 'amem-restriction', '_amemrestriction', false, true );

		$data = (array) get_term_meta( $term->term_id, 'amem_content_restriction', true );

		echo '<h2>' . esc_html( 'Content Restriction Rule', 'advanced-members' ) . '</h2>';
		echo '<table class="form-table">';
		acf_render_field_wrap(
			array(
				'type' => 'message',
				'label' => __('About Term Rule', 'advanced-members' ),
				'message' => __( '&#8251; The term access rule will be applied to posts connected to the term, not to the term itself.', 'advanced-members' ) . '<br>' . __( '&#8251; This means you can apply rules to all posts connected to this term at once, rather than individually.' , 'advanced-members' ),
			), 
			'tr',
			'field'
		);
		$this->render_meta_box( $data, 'tr' );
		echo '</table>';
	}

	protected function render_meta_box($data, $tag='div') {
		$data = $this->validate_rule( $data );

		acf_render_field_wrap(
			array(
				'type' => 'select',
				'name' => 'who',
				'key' => 'who',
				'prefix' => 'amem[restriction]',
				'label' => __('Allow Access', 'advanced-members' ),
				'instructions' => __( 'Select who should have access to this content.', 'advanced-members' ),
				'value' => $data['who'],
				'choices' => [
					'' => __( 'Everyone', 'advanced-members' ),
					'loggedin' => __( 'Logged In Users', 'advanced-members' ),
					'roles' => __( 'Logged in Users with Specific Roles', 'advanced-members' ),
					'loggedout' => __( 'Logged Out Users', 'advanced-members' ),
				],
			), 
			$tag,
			'field'
		);

		$roles = amem_allowed_roles();

		acf_render_field_wrap(
			array(
				'type' => 'checkbox',
				'name' => 'roles',
				'key' => 'roles',
				'prefix' => 'amem[restriction]',
				'label' => __( 'User Roles', 'advanced-members' ),
				'instructions' => __( 'Select the user roles that can see this content.', 'advanced-members' ),
				'choices' => $roles,
				'layout' => 'horizontal',
				'value' => $data['roles'],
				'conditions' => array(
					'field' => 'who',
					'operator' => '==',
					'value' => 'roles',
				),
			), 
			$tag,
			'field'
		);

		$choices = [
			'redirect_login' => __( 'Redirect to the Login Page', 'advanced-members' ),
			'redirect_custom' => __( 'Redirect to custom URL', 'advanced-members' ),
			'show_message' => __( 'Show Restriction Message', 'advanced-members' ),
			'show_excerpt_message' => __( 'Show the Excerpt and Restriction Message', 'advanced-members' ),
		];

		$methods = amem()->options->get('restriction/methods');
		foreach( array_keys($choices) as $method ) {
			if ( empty($methods[$method]) )
				unset( $choices[$method] );
		}

		acf_render_field_wrap(
			array(
				'type' => 'radio',
				'name' => 'method',
				'key' => 'method',
				'prefix' => 'amem[restriction]',
				'label' => __( 'What happens when users without access try to view the post?', 'advanced-members' ),
				'choices' => $choices,
				'default_value' => 'redirect_login',
				'defalut' => 'redirect_login',
				'layout' => 'vertical',
				'value' => $data['method'],
				'conditions' => array(
					'field' => 'who',
					'operator' => '!=',
					'value' => '',
				),
			), 
			$tag,
			'field'
		);

		acf_render_field_wrap(
			array(
				'type' => 'text',
				'name' => 'redirect_url',
				'key' => 'redirect_url',
				'prefix' => 'amem[restriction]',
				'label' => __( 'Custom Redirect URL', 'advanced-members' ),
				'value' => $data['redirect_url'],
				'conditions' => array(
					array(
						'field' => 'method',
						'operator' => '==',
						'value' => 'redirect_custom',
					)
				),
			), 
			$tag,
			'field'
		);

		acf_render_field_wrap(
			array(
				'type' => 'true_false',
				'name' => 'custom_message',
				'key' => 'custom_message',
				'prefix' => 'amem[restriction]',
				'label' => __( 'Use Custom restriction message', 'advanced-members' ),
				'value' => $data['custom_message'],
				'ui' => 1,
				'conditions' => array(
					array(
						array(
							'field' => 'method',
							'operator' => '==',
							'value' => 'show_message',
						),
					),
					array(
						array(
							'field' => 'method',
							'operator' => '==',
							'value' => 'show_excerpt_message',
						),
					),
				),
			), 
			$tag,
			'label'
		);

		acf_render_field_wrap(
			array(
				'type' => 'wysiwyg',
				'name' => 'message',
				'key' => 'message',
				'prefix' => 'amem[restriction]',
				'label' => __( 'Custom Restriction Message', 'advanced-members' ),
				'value' => $data['message'],
				'conditions' => array(
					array(
						'field' => 'custom_message',
						'operator' => '==',
						'value' => '1',
					),
				),
			),
			$tag,
			'field'
		);
	}


}

amem()->register_module('redirects', Restriction::getInstance());