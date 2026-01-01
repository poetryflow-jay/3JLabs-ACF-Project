<?php
/**
 * Manage Advanced Members for ACF Templates
 *
 * Currently, AMem does not needs template system
 * Users can manage design with CSS & Block editor
 *
 * @since 1.0
 * 
 */
namespace AMem;

use AMem\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tempalte extends Module {

	// protected static $inc = '';
	protected $name = 'amem/template';

	protected $locations = [];

	protected $theme_dir;

	protected $dir;

	public $set_args;

	public $loop;

	function __construct() {
		$this->dir = amem_get_path('templates/');
		$this->theme_dir = get_stylesheet_directory() . '/advanced-members/';
	}


	/**
	 * Loads a template file
	 */
	public function template_load( $template, $args = array() ) {
		if ( is_array( $args ) ) {
			$this->set_args = $args;
		}
		$this->load_template( $template );
	}

	function template_exists($template) {

		$file = $this->dir . $template . '.php';
		$theme_file = $this->theme_dir . $template . '.php';

		return (file_exists($theme_file) || file_exists($file));
	}

	/**
	 * Load a compatible template
	 */
	function load_template( $template ) {
		$loop = ( $this->loop ) ? $this->loop : array();

		if ( isset( $this->set_args ) && is_array( $this->set_args ) ) {
			$args = $this->set_args;

			unset( $args['file'], $args['theme_file'], $args['tpl'] );

			$args = apply_filters( 'amem/template/args', $args, $template );

			extract( $args, EXTR_SKIP );
		}

		$file = $this->dir . "{$template}.php";
		$theme_file = $this->theme_dir . "{$template}.php";
		if ( file_exists( $theme_file ) ) {
			$file = $theme_file;
		}

		if ( file_exists( $file ) ) {
			$dir = str_replace( '/', DIRECTORY_SEPARATOR,  $this->dir );
			$theme_dir = str_replace( '/', DIRECTORY_SEPARATOR,  $this->theme_dir );
			$real_file = wp_normalize_path( realpath( $file ) );
			
			if ( 0 === strpos( $real_file, wp_normalize_path( $dir ) ) || 0 === strpos( $real_file, wp_normalize_path( $theme_dir ) ) ) {
				include $file;
			}
		}
	}
}

amem()->register_module('template', Tempalte::getInstance());