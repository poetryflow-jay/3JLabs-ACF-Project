<?php
/**
 * Customizer Control: Text Transform Radio Tabs
 * Type : nxt-text-transform
 *
 * @package Nexter
 */

if ( ! class_exists( 'Nexter_Control_Text_Transform' ) && class_exists( 'WP_Customize_Control' ) ) :

	class Nexter_Control_Text_Transform extends WP_Customize_Control {

		/**
		 * Control Type
		 */
		public $type = 'nxt-text-transform';

		/**
		 * Refresh the parameters passed to the JavaScript via JSON.
		 */
		public function to_json() {
			parent::to_json();

			$this->json['default'] = $this->setting->default;
			$this->json['value']   = $this->value();
			$this->json['link']    = $this->get_link();
			$this->json['choices'] = $this->choices;
			$this->json['id']      = $this->id;
			$this->json['label']   = esc_html( $this->label );
		}

		/**
		 * An Underscore (JS) template for this control's content (but not its container).
		 */
		protected function content_template() {

			$labelArray = ['Default'=> 'Ag', 'Inherit'=> 'Ag', 'Capitalize' => 'Ag', 'Uppercase' => 'AG','Lowercase'=>'ag'];

			?>
			<div class="nxt-text-transform-control">
				<# if ( data.label ) { #>
					<span class="customize-control-title">{{{ data.label }}}</span>
				<# } #>

				<div class="nxt-text-transform-options">
					
					<# 
					var labelArray = <?php echo json_encode($labelArray); ?>;
					_.each( data.choices, function( choiceLabel, choiceValue ) { #>

						<input type="radio" name="_customize-text-transform-{{ data.id }}" value="{{ choiceValue }}" id="text-transform-{{choiceValue}}-{{ data.id }}" {{{ data.link }}} <# if ( data.value === choiceValue ) { #> checked="checked" <# } #>>
						<label class="nxt-text-transform-option option-index-{{choiceValue}}" for="text-transform-{{choiceValue}}-{{ data.id }}" data-label="{{choiceLabel}}">{{{ labelArray[choiceLabel] }}}</label>
					<# }); #>
				</div>
			</div>
			<?php
		}

		/**
		 * Render the control's content.
		 */
		protected function render_content() {}
	}

endif;