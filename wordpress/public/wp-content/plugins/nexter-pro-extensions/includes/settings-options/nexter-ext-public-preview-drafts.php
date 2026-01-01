<?php 
/*
 * Public Preview for Drafts Extension Pro
 * @since 4.2.0
 */
defined('ABSPATH') or die();

 class Nexter_Ext_Public_Preview_Drafts {
    
	public static $post_type_opt = [];

	/**
     * Constructor
     */
    public function __construct() {
		$this->nxt_get_post_order_settings();
		if ( ! is_admin() ) {
			add_filter( 'query_vars', [ $this, 'add_query_var' ] );
			add_action( 'pre_get_posts', [ $this, 'show_public_preview' ] );
		} else {
			add_filter( 'page_row_actions', [ $this, 'add__action_row_public_preview_link' ], 10, 2 );
			add_filter( 'post_row_actions', [ $this, 'add__action_row_public_preview_link' ], 10, 2 );
			add_action( 'post_submitbox_misc_actions', [ $this, 'add_submitbox_public_preview_button' ] );
			add_action( 'admin_head', [ $this, 'add_gutenberg_public_preview_button' ] );
		}
    }

	private function nxt_get_post_order_settings(){
        
		if(isset(self::$post_type_opt) && !empty(self::$post_type_opt)){
			return self::$post_type_opt;
		}

		$option = get_option( 'nexter_extra_ext_options' );
		
		if(!empty($option) && isset($option['public-preview-drafts']) && !empty($option['public-preview-drafts']['switch']) && !empty($option['public-preview-drafts']['values']) ){
			self::$post_type_opt = (array) $option['public-preview-drafts']['values'];
		}
	}

	/**
	 * Add public preview link to draft posts where enabled.
	 */
	public function add__action_row_public_preview_link( $actions, $post ) {
		$opts = isset(self::$post_type_opt['posts']) ? self::$post_type_opt['posts'] : [];

		if ( is_array( $opts ) && in_array( $post->post_type, $opts ) && $post->post_status === 'draft' ) {
			$link = self::get_preview_link( $post );
			$label = __( 'Public Preview', 'nexter-pro-extensions' );
			$title = __( 'Link to preview this draft publiclly', 'nexter-pro-extensions' );
			$actions['nxt-ext-public-preview'] = "<a href='{$link}' title='{$title}'>{$label}</a>";
		}

		return $actions;
	}

    /**
	 * Add public preview button in post submit/update box (Classic Editor).
	 */
	public function add_submitbox_public_preview_button() {
		global $post;

		$opts = isset(self::$post_type_opt['posts']) ? self::$post_type_opt['posts'] : [];

		if ( in_array( $post->post_type, $opts ) && $post->post_status === 'draft' ) {
			$link  = self::get_preview_link( $post );
			$label = __( 'Public Preview', 'nexter-pro-extensions' );
			$title = __( 'Link to preview this draft publicly', 'nexter-pro-extensions' );

			echo wp_kses_post( "<div class='additional-actions'><span id='public-preview'><a href='{$link}' title='{$title}'>{$label}</a></span></div>" );
		}
	}
    
    /**
     * Add public preview button in the block editor
     */
    public function add_gutenberg_public_preview_button() {
        global $post, $pagenow;
		$current_screen = get_current_screen();

        if ( is_object( $post ) && 'post.php' == $pagenow ) {
            if ( 'draft' == $post->post_status ) {

                if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
                    $public_preview_link = self::get_preview_link( $post );

					wp_enqueue_style(
						'nxt-public-preview',
						NXT_PRO_EXT_URI . 'assets/css/nxt-public-preview.css',
						array(), // dependencies
						NXT_PRO_EXT_VER // version constant
					);

                    wp_register_script(
						'nxt-public-preview',
						NXT_PRO_EXT_URI . 'assets/js/nxt-public-preview.js',
						array( 'wp-edit-post', 'wp-plugins', 'wp-i18n', 'wp-element' ),
						NXT_PRO_EXT_VER,
						true // Load in footer
					);

                    wp_localize_script( 'nxt-public-preview', 'nxt_pp_params', array(
                        'post_text'      => __( 'Public Preview', 'nexter-pro-extensions' ),
                        'post_title'     => __( 'Link to preview this draft publiclly', 'nexter-pro-extensions' ),
                        'public_preview_link' => $public_preview_link
                        )
                    );

                    wp_enqueue_script( 'nxt-public-preview' );
                }                
            }
        }        
    }

    /**
     * Registers the new query var `_ppp`.
     */
    public static function add_query_var( $qv ) {
        $qv[] = 'pp';

        return $qv;
    }
    
    /**
	 * Enables public preview with noindex for search engines.
	 */
    public static function show_public_preview( $q ) {
        if ( 
			$q->is_main_query() &&
			$q->is_preview() &&
			$q->is_singular() &&
			$q->get( 'pp' )
		) {
			if ( ! headers_sent() ) {
				nocache_headers();
				header( 'X-Robots-Tag: noindex' );
			}
	
			add_filter(
				function_exists( 'wp_robots_no_robots' ) ? 'wp_robots' : 'wp_head',
				function_exists( 'wp_robots_no_robots' ) ? 'wp_robots_no_robots' : 'wp_no_robots'
			);
	
			add_filter( 'posts_results', [ __CLASS__, 'set_post_to_publish' ], 10, 2 );
		}
    }

    /**
	 * Sets post status to 'publish' for public preview access.
	 */
	public static function set_post_to_publish( $posts ) {
		// Prevent this filter from affecting other queries.
		remove_filter( 'posts_results', [ __CLASS__, __FUNCTION__ ], 10 );

		if ( empty( $posts ) ) {
			return $posts;
		}

		$id = (int) $posts[0]->ID;

		// Redirect to permalink if already published.
		self::maybe_redirect_to_published_post( $id );

		// Verify the nonce to protect access.
		if ( ! self::verify_nonce( get_query_var( 'pp' ), "public_preview_for_draft_{$id}" ) ) {
			wp_die( esc_html__( 'This link has expired or is invalid!', 'nexter-pro-extensions' ), 403 );
		}

		if ( $posts[0]->post_status === 'draft' ) {
			$posts[0]->post_status = 'publish'; // Make the draft visible.

			// Disable comments and pings.
			add_filter( 'comments_open', '__return_false' );
			add_filter( 'pings_open', '__return_false' );
			add_filter( 'wp_link_pages_link', [ __CLASS__, 'filter_wp_link_pages_link' ], 10, 2 );
		}

		return $posts;
	}
    
    /**
	 * Replaces page link href with a custom preview link.
	 */
	public static function filter_wp_link_pages_link( $link, $page ) {
		$post = get_post();
		if ( ! $post ) {
			return $link;
		}

		$url = add_query_arg( 'page', $page, self::get_preview_link( $post ) );

		// Replace existing href with the custom preview URL.
		return preg_replace( '~href=(["\'])(.+?)\1~', 'href=$1' . esc_url( $url ) . '$1', $link );
	}

    /**
	 * Redirects to the permalink if the post is published or private.
	 */
	private static function maybe_redirect_to_published_post( $id ) {
		$status = get_post_status( $id );

		if ( ! in_array( $status, [ 'publish', 'private' ], true ) ) {
			return false;
		}

		wp_safe_redirect( get_permalink( $id ), 301 );
		exit;
	}

    /**
	 * Generates a public preview URL for the given post.
	 */
	public static function get_preview_link( $post ) {
		$id = $post->ID;
		$type = $post->post_type;

		switch ( $type ) {
			case 'page':
				$args = [ 'page_id' => $id ];
				break;
			case 'post':
				$args = [ 'p' => $id ];
				break;
			default:
				$args = [ 'p' => $id, 'post_type' => $type ];
				break;
		}

		$args += [ 
			'preview' => 'true',
			'pp'      => self::create_nonce( "public_preview_for_draft_{$id}" ),
		];

		return add_query_arg( $args, home_url( '/' ) );

	}

    /**
	 * Returns the time-based tick used for nonce creation.
	 */
    private static function nonce_tick() {
		$days = isset(self::$post_type_opt['days']) ? self::$post_type_opt['days'] : 3;
		$lifetime = $days * DAY_IN_SECONDS;

		return (int) ceil( time() / ( $lifetime / 2 ) );
    }

    /**
	 * Generates a one-time use nonce without an UID.
	 */
	private static function create_nonce( $action = -1 ) {
		$tick = self::nonce_tick();

		return substr( wp_hash( $tick . $action, 'nonce' ), -12, 10 );
	}

    /**
	 * Verifies the nonce within a time limit without using an UID.
	 */
	private static function verify_nonce( $nonce, $action = -1 ) {
		$tick = self::nonce_tick();

		// Check nonce for 0-12 hours range.
		if ( substr( wp_hash( $tick . $action, 'nonce' ), -12, 10 ) === $nonce ) {
			return 1;
		}

		// Check nonce for 12-24 hours range.
		if ( substr( wp_hash( ( $tick - 1 ) . $action, 'nonce' ), -12, 10 ) === $nonce ) {
			return 2;
		}

		// Invalid nonce.
		return false;
	}
}

new Nexter_Ext_Public_Preview_Drafts();