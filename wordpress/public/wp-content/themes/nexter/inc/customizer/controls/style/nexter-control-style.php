<?php
if ( ! class_exists( 'Nexter_Control_Image_Selector' ) ) {

	class Nexter_Control_Image_Selector extends WP_Customize_Control {

		public $type = 'nxt-style'; // Control type

		// Array to hold the image options
		public $choices = array();

		// Constructor to initialize the control
		public function __construct( $manager, $id, $args = array() ) {
			parent::__construct( $manager, $id, $args );
			
			if ( isset( $args['choices'] ) ) {
				$this->choices = $args['choices'];
			}
		}

		// Function to pass data to JavaScript
		public function to_json() {
			parent::to_json();
			$this->json['choices'] = $this->choices;
            $this->json['name'] = isset( $this->input_attrs['name'] ) ? $this->input_attrs['name'] : '';
			$this->json['default'] = $this->setting->default;
			if ( isset( $this->default ) ) {
				$this->json['default'] = $this->default;
			}
			$this->json['value']  = $this->value();
		}

		// Template for the control’s content
		protected function content_template() {
			?>
			<div>
				<# if ( data.label ) { #>
					<span class="customize-control-title">{{{ data.label }}}</span>
				<# } #>

				<# if ( data.description ) { #>
					<span class="description customize-control-description">{{{ data.description }}}</span>
				<# } #>

				<div class="nxt-image-select-control">
					<# _.each( data.choices, function( label, key ) { #>
						<label>
							<input class="radio-input" type="radio" name="{{ data.name }}" value="{{ key }}" {{{ data.link }}} <# if ( data.value === key ) { #> checked="checked" <# } #> />
							<img src="{{ label.image }}" alt="{{ key }}" title="{{ label.title }}" style="width:100px; height:auto;" />
							<!-- <p>{{ label.title }}</p> -->
						</label>
					<# }); #>
				</div>
			</div>
			<?php
		}
	}
}
