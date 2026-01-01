<?php 
/**
 * Enqueue Scripts Load
 *
 * @package Nexter Extensions Pro
 * @since 1.0.0
 */
if ( ! class_exists( 'Nexter_Pro_Enqueue_Scripts' ) ) {

	class Nexter_Pro_Enqueue_Scripts {

		/**
		 * Member Variable
		 */
		private static $instance;

		/**
		 * Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueqe_scripts' ] );
		}
		
		/**
		 * Frontend Style & Scripts
		 */
		public function enqueqe_scripts() {
			$is_enabled = Nexter_Pro_Maintenance_Mode::check_maintenance_header_footer();
			if ( $is_enabled ) {
				wp_enqueue_style('nxt-pro-frontend', NXT_PRO_EXT_URI . 'assets/css/nexter-maintenance-mode.min.css', array(), NXT_PRO_EXT_VER, false );
			}
		}
		
	}
}

Nexter_Pro_Enqueue_Scripts::get_instance();