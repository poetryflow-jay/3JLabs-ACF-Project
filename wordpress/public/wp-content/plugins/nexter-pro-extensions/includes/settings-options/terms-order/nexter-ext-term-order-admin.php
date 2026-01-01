<?php
/*
 * Terms Order Admin run
 * @since 4.2.0
 */
defined('ABSPATH') or die();

class Nexter_Ext_Terms_Order_Admin {
	
	public static $taxonomy_opt = [];

	public function __construct($taxonomy_options) {
		self::$taxonomy_opt = $taxonomy_options;
		add_action( 'admin_menu', array( $this, 'add_terms_order_menu' ) );
		add_filter( 'get_terms_orderby', array( $this, 'get_terms_orderby' ), 1, 2);
		add_action( 'wp_ajax_nxt-ext-taxonomy-save-order', array( $this, 'save_terms_order' ) );
	}

	public function enqueue_styles() {
		wp_enqueue_style( 'nxt-term-order', NXT_PRO_EXT_URI . 'assets/css/nxt-taxonomy-order.css', array(), NXT_PRO_EXT_VER, 'all' );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 
			'jquery' 
		);  
        wp_enqueue_script( 
        	'jquery-ui-sortable', 
        	'', 
        	array( 'jquery' ) 
        );
		wp_enqueue_script( 
			'nxt-term-order', 
			NXT_PRO_EXT_URI . 'assets/js/nxt-taxonomy-order.js', 
			array( 'jquery-ui-sortable' ), 
			NXT_PRO_EXT_VER, 
			false 
		);
	}
		
	public function add_terms_order_menu() {

		if(!empty(self::$taxonomy_opt) && isset(self::$taxonomy_opt['taxonomy']) && !empty(self::$taxonomy_opt['taxonomy'])){
			$taxonomies = [];
			foreach ( self::$taxonomy_opt['taxonomy'] as $key => $taxonomy_name ) {
				$taxonomy_info = get_taxonomy( $taxonomy_name );

				if ( empty( $taxonomy_info->show_in_menu ) ||  $taxonomy_info->show_in_menu !== TRUE ) {
					unset( $taxonomies[$key] );
				}else{
					$taxonomies[] = $taxonomy_name ;
				}
			}
			
			if ( count( $taxonomies ) > 0 ) {

				add_submenu_page(
					'nexter_welcome',
					__( 'Terms Order', 'nexter-pro-extensions' ),
					__( 'Terms Order', 'nexter-pro-extensions' ),
					'manage_options',
					'nxt-terms-order',
					array( $this, 'terms_ordering_render' ) 
				);
			}
		}
	}

	public function terms_ordering_render() {
        global $wpdb, $wp_locale;
        
		$taxonomy = '';
		$post_type = 'post';

		if(!empty(self::$taxonomy_opt) && isset(self::$taxonomy_opt['taxonomy']) && !empty(self::$taxonomy_opt['taxonomy']) && isset(self::$taxonomy_opt['taxonomy'][0])){
			$taxonomy = self::$taxonomy_opt['taxonomy'][0];
			$taxonomy_info = get_taxonomy( $taxonomy );
			if ( is_object( $taxonomy_info ) ) {
				$post_type = $taxonomy_info->object_type[0] ? $taxonomy_info->object_type[0] : $post_type;
			}
		}

        // Get post type data
        $post_type 	= isset( $_GET['post_type'] ) ? sanitize_key( $_GET['post_type'] ) : $post_type;
		
        // Get taxonomy data
        $taxonomy = isset( $_GET['taxonomy'] ) ? sanitize_key( $_GET['taxonomy'] ) : $taxonomy;
        
        if ( ! taxonomy_exists( $taxonomy ) ) {
            $taxonomy = '';
		}
		
		// Pass on class instance when assembling the terms ordering page

		$instance = $this;

		include( 'nexter-ext-term-ordering-render.php' ); 
            
    }

	public function tto_terms_list( $taxonomy ) {
        $args = array(
            'orderby' 		=> 'term_order',
            'depth' 		=> 0,
            'child_of' 		=> 0,
            'hide_empty'	=> 0,
        );

		$taxonomy_terms = get_terms( $taxonomy, $args );

        $output = '';

        if ( ! is_wp_error( $taxonomy_terms ) && is_array( $taxonomy_terms ) && count( $taxonomy_terms ) > 0 ) {
            $output = $this->tax_terms_order_list_hierarchy( $taxonomy_terms, $args['depth'], $args );    
        }
		if(is_wp_error( $taxonomy_terms )){
			$output = '<p>' . esc_html__( 'Invalid Taxonomy.', 'nexter-pro-extensions' ) . '</p>';
		}

        echo wp_kses_post( $output );                
    }
        
    public function tax_terms_order_list_hierarchy( $taxonomy_terms, $depth, $r ) {
        $walker = new Nexter_Ext_Terms_Order_Walker; 
        $args = array( $taxonomy_terms, $depth, $r );
        return call_user_func_array( array( &$walker, 'walk' ), $args );
    }
		
	public function get_terms_orderby( $orderby, $args ) {
            if ( apply_filters( 'nxt_get_terms_orderby_ignore', FALSE, $orderby, $args ) ) {
                return $orderby;
			}
                
            if ( isset( $args['orderby'] ) && $args['orderby'] == "term_order" && $orderby != "term_order" ) {
                return "t.term_order";
            }
                
            return $orderby;
    }
		
	public function save_terms_order() {
            global $wpdb;
            
            if ( ! isset( $_POST['nonce'] ) ||
				! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'nxt-ext-taxonomy-save-order' ) ) {
				wp_die(); // or die(), but wp_die() is preferred
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( [ 'message' => 'You do not have permission to perform this action.' ] );
			}
            
            $data = stripslashes( sanitize_text_field( $_POST['order'] ) );
            $unserialised_data = json_decode( $data, TRUE );
                    
            if ( is_array( $unserialised_data ) ) {
				foreach ( $unserialised_data as $key => $values ) {
						
					$items = explode("&", $values);
					unset($item);
					foreach ( $items as $item_key => $item_ ) {
						$items[$item_key] = trim( str_replace( "item[]=", "", $item_ ) );
					}
						
					if ( is_array( $items ) && count( $items ) > 0 ) {
						foreach ( $items as $item_key => $term_id ) {
							$wpdb->update( $wpdb->terms, array( 'term_order' => ($item_key + 1) ), array( 'term_id' => $term_id ) );
						}
						clean_term_cache( $items );
					} 
				}
			}
                
            do_action('nxt_term_update_order');
             
            die();
    }

}