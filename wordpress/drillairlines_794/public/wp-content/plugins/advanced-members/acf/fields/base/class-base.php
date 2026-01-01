<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if ( ! class_exists( 'amem_field_base' ) ) :

	class amem_field_base extends acf_field {
		use amem_field;

		function __construct() {

			// initialize
			parent::__construct();
		}
	}

endif; // class_exists check


