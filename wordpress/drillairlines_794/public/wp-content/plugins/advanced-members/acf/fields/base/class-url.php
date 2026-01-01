<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if ( ! class_exists( 'amem_field_url' ) ) :

	class amem_field_url extends acf_field_url {
		use amem_field;

		function __construct() {

			// initialize
			parent::__construct();
		}

	}

endif; // class_exists check


