<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if ( ! class_exists( 'amem_field_select' ) ) :

	class amem_field_seperator extends acf_field_separator {
		use amem_field;

		function __construct() {

			// initialize
			parent::__construct();
		}

	}

endif; // class_exists check
