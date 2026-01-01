<?php 
/*
 * Taxonomy (Terms) Order Extension Pro
 * @since 4.3.0
 */
defined('ABSPATH') or die();

class Nexter_Ext_Taxonomy_Order {
    
	public static $taxonomy_opt = [];

	/**
     * Constructor
     */
    public function __construct() {
		$this->nxt_get_post_order_settings();

		if(is_admin()){
			$this->load_dependencies();
			$this->define_admin_hooks();
		}
		add_filter( 'terms_clauses', array( $this, 'maybe_apply_custom_order' ), 10, 3);
    }

	private function nxt_get_post_order_settings(){
        
		if(isset(self::$taxonomy_opt) && !empty(self::$taxonomy_opt)){
			return self::$taxonomy_opt;
		}

		$option = get_option( 'nexter_extra_ext_options' );
		
		if(!empty($option) && isset($option['taxonomy-order']) && !empty($option['taxonomy-order']['switch']) && !empty($option['taxonomy-order']['values']) ){
			self::$taxonomy_opt = (array) $option['taxonomy-order']['values'];
		}
	}

	/**
	 * Load the required dependencies.
	 * Terms_Order_Admin. Defines all hooks for the admin area.
	 */
	private function load_dependencies() {
		require_once NXT_PRO_EXT_DIR . 'includes/settings-options/terms-order/nexter-ext-term-order-admin.php';
		require_once NXT_PRO_EXT_DIR . 'includes/settings-options/terms-order/nexter-ext-term-order-walker.php';
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Nexter_Ext_Terms_Order_Admin(self::$taxonomy_opt);

		// Only load assets on taxonomy terms ordering page

		$request_uri = sanitize_text_field( $_SERVER['REQUEST_URI'] ); // e.g. /wp-admin/index.php?page=page-slug

		if ( false !== strpos( $request_uri, '-terms-order' ) ) {
			add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_scripts' ) );
		}
	}

	public function maybe_apply_custom_order( $clauses, $taxonomies, $args ) {		
		$taxonomies_list = [];
		if(!empty(self::$taxonomy_opt) && isset(self::$taxonomy_opt['taxonomy']) && !empty(self::$taxonomy_opt['taxonomy'])){
			
			foreach ( self::$taxonomy_opt['taxonomy'] as $key => $taxonomy_name ) {
				$taxonomy_info = get_taxonomy( $taxonomy_name );

				if ( empty( $taxonomy_info->show_in_menu ) ||  $taxonomy_info->show_in_menu !== TRUE ) {
					unset( $taxonomies_list[$key] );
				}else{
					$taxonomies_list[] = $taxonomy_name ;
				}
			}
		}

		$taxonomy_matches = array_intersect( $taxonomies, $taxonomies_list );
		
		foreach ( $taxonomies_list as $taxonomy ) {
			$taxonomy_info = get_taxonomy( $taxonomy );
			if ( is_object( $taxonomy_info ) ) {
				$taxonomy_is_for = $taxonomy_info->object_type;
				foreach ( $taxonomy_is_for as $taxonomy_post_type ) {
					if ( apply_filters( 'nxt_get_terms_orderby_ignore', FALSE, $clauses['orderby'], $args ) ) {
						return $clauses;
					}

					// Admin terms order
					if ( is_admin() ) {
						if ( isset( $_GET['orderby'] ) && $_GET['orderby'] != 'term_order' ) {
							return $clauses;
						}
						
						if ( ( ! isset( $args['ignore_term_order'] ) || ( isset( $args['ignore_term_order'] ) && $args['ignore_term_order'] !== TRUE ) ) ) {
							$clauses['orderby'] = 'ORDER BY t.term_order';
						}
							
						return $clauses;
					}
					
					// Frontend terms order
					if ( ( ! isset( $args['ignore_term_order'] ) || ( isset( $args['ignore_term_order'] ) && $args['ignore_term_order'] !== TRUE ) ) ) {
						$clauses['orderby'] = 'ORDER BY t.term_order';        		
					}
				}
			}
		}
        
        return $clauses; 
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->add_term_order_column_in_terms_table();
	}
	
	function add_term_order_column_in_terms_table() {
        global $wpdb;
        $table = $wpdb->terms;
		$column = 'term_order';

		$query = $wpdb->prepare(
			"SHOW COLUMNS FROM {$table} LIKE %s",
			$column
		);

		$result = $wpdb->query( $query );
        if ( 0 == $result ){
			$query = $wpdb->prepare(
				"ALTER TABLE {$table} ADD `term_order` INT(4) NULL DEFAULT %d",
				0
			);
			$result = $wpdb->query( $query );
        }

    }
	
}

$taxonomy_order = new Nexter_Ext_Taxonomy_Order();
$taxonomy_order->run();