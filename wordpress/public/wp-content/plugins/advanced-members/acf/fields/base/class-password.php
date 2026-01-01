<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if ( ! class_exists( 'amem_field_password' ) ) :

	class amem_field_password extends acf_field_password {
		use amem_field;

		function __construct() {

			// initialize
			parent::__construct();
		}

	}

endif; // class_exists check


