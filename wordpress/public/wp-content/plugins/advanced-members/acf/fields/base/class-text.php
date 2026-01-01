<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if ( ! class_exists( 'amem_field_text' ) ) :

	class amem_field_text extends acf_field_text {
		use amem_field;

		function __construct() {

			// initialize
			parent::__construct();
		}

	}

endif; // class_exists check


