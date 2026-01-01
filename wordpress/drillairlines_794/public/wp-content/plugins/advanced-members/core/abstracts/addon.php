<?php
/**
 * Render Form
 *
 * @since 1.0
 * @package Advanced Members for ACF \ Forms
 * 
 */
namespace AMem;
use AMem\Singleton;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

abstract class AddOn {
	use Singleton;

	protected $inc = '';// module or includes path

	protected $url = '';

	protected $name = '';// sanitized module name

	protected $label = '';// Display name of module

	protected $version = AMEM_VERSION;// Module version. used for upgrade actions and scripts, styles print

	protected $min_amem_version;// Minimum Advanced Members for ACF version required for module

	public $priority = 10;// ready for future use

	public function __construct() {
		if ( $this->name ) {
			add_action( 'amem/module/' . $this->name . '/enabled', [$this, 'action_enabled'] );
			add_action( 'amem/module/' . $this->name . '/disabled', [$this, 'action_disabled'] );
		}
	}

	/** get readonly protected class variables */
	public function __get($key) {
		if ( isset($this->$key) )
			return $this->$key;

		return null;
	}

	public function action_enabled() {/* You can override method if needed */}

	public function action_disabled() {/* You can override method if needed */}

}

