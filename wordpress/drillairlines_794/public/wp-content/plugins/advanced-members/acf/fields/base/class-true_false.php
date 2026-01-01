<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if ( ! class_exists( 'amem_field_true_false' ) ) :

	class amem_field_true_false extends acf_field_true_false {
		use amem_field;

		function __construct() {

			// initialize
			parent::__construct();
		}

	}

endif; // class_exists check
