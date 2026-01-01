<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if ( ! class_exists( 'amem_field_textarea' ) ) :

	class amem_field_textarea extends acf_field_textarea {
		use amem_field;

		function __construct() {

			// initialize
			parent::__construct();
		}

	}


endif; // class_exists check


