<?php
/**
 * Manage Advanced Members for ACF Modules
 *
 * @package Advanced Members for ACF
 * @since 0.1.0
 */
namespace AMem;

use AMem\Module;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

class Modules extends Module {

	private $modules = [];

	private $type;

	private $is_a = 'AMem\Module';

	function __construct( $type='module', $is_a = null ) {

		$this->type = $type;

		$this->is_a = $is_a ? $is_a : $this->is_a;
	}

	public function __get($key) {
		return $this->get($key);
	}

	public function type() {
		return $this->type;
	}

	public static function sanitize_thing_name($name) {
		$names = explode('/', $name);
		$names = array_map( 'ucfirst', $names );
		return lcfirst( implode( '', $names ) );
	}

	public function register($name, $class) {

		$name = static::sanitize_thing_name($name);

		if ( isset($this->modules[$name]) )
			return true;

		if ( is_scalar($class) ) {
			if ( class_exists($class) && is_a($class, $this->is_a, true) ) {
				$this->modules[$name] = $class::getInstance();
			} else {
				return false;
			}
		} elseif ( is_a($class, $this->is_a) ) {
			$this->modules[$name] = $class;
		} else {
			return false;
		}

		return true;
	}

	public function get($name) {
		$name = static::sanitize_thing_name($name);

		if ( isset($this->modules[$name]) )
			return $this->modules[$name];
		return null;
	}

	public function modules() {
		/** @todo Returns sorted by priority */
		// this means all class objects has public $priority variable
		return array_keys($this->modules);
	}

}
