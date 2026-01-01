<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if ( ! class_exists( 'amem_field_email' ) ) :

	class amem_field_email extends acf_field_email {
		use amem_field;

		function __construct() {

			// initialize
			parent::__construct();
		}
	}

endif; // class_exists check


