<?php
/**
 * Customizer Control: Background
 * Type : nxt-responsive-spacing
 *
 * @package	Nexter
 * @since	1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'Nexter_Control_Responsive_Spacing' ) && class_exists( 'WP_Customize_Control' ) ) :

	class Nexter_Control_Responsive_Spacing extends WP_Customize_Control {

		/**
		 * Control Type
		 */
		public $type = 'nxt-responsive-spacing';

		/**
		 * Linked/Unlinked Choices
		 */
		public $linked = '';

		/**
		 * Unit Type
		 */
		public $unit = array( 'px' => 'px' );
		
		/**
		 * Refresh the parameters passed to the JavaScript via JSON.
		 *
		 * @see WP_Customize_Control::to_json()
		 */
		public function to_json() {
			parent::to_json();

			$this->json['default'] = $this->setting->default;
			if ( isset( $this->default ) ) {
				$this->json['default'] = $this->default;
			}

			$val = maybe_unserialize( $this->value() );

			if ( ! is_array( $val ) || is_numeric( $val ) ) {

				$val = array(
					'md'      => array(
						'top'    => $val,
						'right'  => '',
						'bottom' => $val,
						'left'   => '',
					),
					'sm'       => array(
						'top'    => $val,
						'right'  => '',
						'bottom' => $val,
						'left'   => '',
					),
					'xs'       => array(
						'top'    => $val,
						'right'  => '',
						'bottom' => $val,
						'left'   => '',
					),
					'md-unit' => 'px',
					'sm-unit'  => 'px',
					'xs-unit'  => 'px',
				);
			}

			/* Control Units */
			$units = array(
				'md-unit' => 'px',
				'sm-unit'  => 'px',
				'xs-unit'  => 'px',
			);

			foreach ( $units as $key_unit => $unit_value ) {
				if ( ! isset( $val[ $key_unit ] ) ) {
					$val[ $key_unit ] = $unit_value;
				}
			}

			$this->json['value']	= $val;
			$this->json['choices']	= $this->choices;
			$this->json['link']	= $this->get_link();
			$this->json['id']	= $this->id;
			$this->json['label']	= esc_html( $this->label );
			$this->json['linked']	= $this->linked;
			$this->json['unit']	= $this->unit;
			$this->json['inputAttrs']	= '';
			foreach ( $this->input_attrs as $attr => $value ) {
				$this->json['inputAttrs'] .= $attr . '="' . esc_attr( $value ) . '" ';
			}
			$this->json['inputAttrs']	= maybe_serialize( $this->input_attrs() );

		}
		
		/**
		 * An Underscore (JS) template for this control's content (but not its container).
		 *
		 * Class variables for this control class are available in the `data` JS object;
		 * export custom variables by overriding {@see WP_Customize_Control::to_json()}.
		 *
		 * @see WP_Customize_Control::print_template()
		 *
		 * @access protected
		 */
		protected function content_template() {
			$linked_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none"><path fill="#010101" fill-rule="evenodd" d="M8.12155 1.87842c-.26265 0-.52272.05173-.76537.15224-.24265.10051-.46313.24783-.64885.43354L5.64667 3.52486c-.19526.19527-.51184.19527-.7071 0-.19527-.19526-.19527-.51184 0-.7071L6.00023 1.7571c.27857-.27858.60929-.49956.97327-.65032.36397-.150765.75408-.228362 1.14805-.228362.39396 0 .78407.077597 1.14805.228362.36397.15076.69469.37174.9733.65032.2785.27857.4995.60929.6503.97327.1508.36397.2283.75408.2283 1.14805 0 .39396-.0775.78407-.2283 1.14805-.1508.36397-.3718.69469-.6503.97327L9.18221 7.0604c-.19526.19526-.51185.19526-.70711 0s-.19526-.51185 0-.70711l1.06066-1.06066c.18572-.18572.33304-.40619.43355-.64885.10049-.24265.15219-.50272.15219-.76536 0-.26265-.0517-.52272-.15219-.76537-.10051-.24265-.24783-.46313-.43355-.64885-.18572-.18571-.4062-.33303-.64885-.43354-.24265-.10051-.50272-.15224-.76536-.15224Zm-.35352 2.35363c.19526.19526.19526.51184 0 .7071L4.9396 7.76758c-.19526.19526-.51184.19526-.70711 0-.19526-.19526-.19526-.51184 0-.70711l2.82843-2.82842c.19526-.19527.51185-.19527.70711 0Zm-4.24268.70703c.19526.19526.19526.51184 0 .7071L2.46469 6.70684c-.37507.37508-.58578.88379-.58578 1.41422 0 .53043.21071 1.03914.58578 1.41421.37508.37508.88378.58583 1.41422.58583.26264 0 .52271-.0518.76536-.15228.24265-.10051.46313-.24783.64885-.43355l1.06066-1.06066c.19526-.19526.51184-.19526.70711 0 .19526.19526.19526.51185 0 .70711L6.00023 10.2424c-.27858.2786-.6093.4995-.97327.6503-.36398.1508-.75409.2284-1.14805.2284-.79565 0-1.55871-.3161-2.12132-.8787-.56261-.56263-.878684-1.32569-.878684-2.12134s.316074-1.55871.878684-2.12132l1.06066-1.06066c.19526-.19526.51184-.19526.7071 0Z" clip-rule="evenodd"></path></svg>';

			$desktop = '<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M1.5 1.36364C1.20991 1.36364 0.974747 1.5988 0.974747 1.88889V6.33333C0.974747 6.62342 1.20991 6.85859 1.5 6.85859H5.03986C5.04518 6.85836 5.05053 6.85824 5.0559 6.85824C5.06128 6.85824 5.06663 6.85836 5.07195 6.85859H8.61111C8.9012 6.85859 9.13636 6.62342 9.13636 6.33333V1.88889C9.13636 1.5988 8.9012 1.36364 8.61111 1.36364H1.5ZM5.41954 7.58586H8.61111C9.30286 7.58586 9.86364 7.02508 9.86364 6.33333V1.88889C9.86364 1.19714 9.30286 0.636364 8.61111 0.636364H1.5C0.808249 0.636364 0.247475 1.19714 0.247475 1.88889V6.33333C0.247475 7.02508 0.808249 7.58586 1.5 7.58586H4.69227V8.63636H3.27778C3.07695 8.63636 2.91414 8.79917 2.91414 9C2.91414 9.20083 3.07695 9.36364 3.27778 9.36364H6.83333C7.03416 9.36364 7.19697 9.20083 7.19697 9C7.19697 8.79917 7.03416 8.63636 6.83333 8.63636H5.41954V7.58586Z" fill="#010101"></path></svg>';

			$tablet = '<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M0.98616 1.32927C0.963662 1.37565 0.93913 1.4661 0.93913 1.63707V5.78333H9.05556V1.63707C9.05556 1.4661 9.03102 1.37565 9.00853 1.32927C8.99137 1.29389 8.97149 1.27427 8.93206 1.25587C8.88114 1.2321 8.79755 1.21162 8.65647 1.20044C8.51662 1.18935 8.35073 1.18913 8.14218 1.18913H1.8525C1.64396 1.18913 1.47806 1.18935 1.33822 1.20044C1.19713 1.21162 1.11354 1.2321 1.06262 1.25587C1.02319 1.27427 1.00332 1.29389 0.98616 1.32927ZM9.05556 6.47246H0.93913V7.97707C0.93913 8.10702 0.973503 8.17763 1.00794 8.22103C1.046 8.26898 1.10754 8.3125 1.20175 8.34741C1.40208 8.42165 1.6547 8.425 1.8525 8.425H8.14218C8.33999 8.425 8.59261 8.42165 8.79294 8.34741C8.88714 8.3125 8.94869 8.26898 8.98674 8.22103C9.02118 8.17763 9.05556 8.10702 9.05556 7.97707V6.47246ZM1.84298 0.5H8.15171C8.34867 0.499997 8.54097 0.499993 8.71091 0.51346C8.88234 0.527045 9.06213 0.556089 9.22349 0.631389C9.39633 0.712051 9.53763 0.841018 9.62858 1.02854C9.71418 1.20504 9.74469 1.41178 9.74469 1.63707V7.97707C9.74469 8.24336 9.66899 8.46993 9.52653 8.64943C9.38769 8.82437 9.20551 8.92945 9.0324 8.9936C8.70646 9.11439 8.34055 9.11422 8.15602 9.11413C8.15129 9.11413 8.14667 9.11413 8.14218 9.11413H1.8525C1.84801 9.11413 1.8434 9.11413 1.83867 9.11413C1.65413 9.11422 1.28822 9.11439 0.962281 8.9936C0.789176 8.92945 0.606994 8.82437 0.468154 8.64943C0.325697 8.46993 0.25 8.24336 0.25 7.97707V1.63707C0.25 1.41178 0.280503 1.20504 0.366109 1.02854C0.457057 0.841018 0.598352 0.712051 0.7712 0.631389C0.932557 0.556089 1.11235 0.527045 1.28378 0.51346C1.45372 0.499993 1.64601 0.499997 1.84298 0.5ZM4.65278 7.44873C4.65278 7.25843 4.80704 7.10417 4.99734 7.10417H5.00175C5.19204 7.10417 5.34631 7.25843 5.34631 7.44873C5.34631 7.63903 5.19204 7.7933 5.00175 7.7933H4.99734C4.80704 7.7933 4.65278 7.63903 4.65278 7.44873Z" fill="black"></path></svg>';

			$mobile = '<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M2.7 0.800001C2.36863 0.800001 2.1 1.06863 2.1 1.4V6.5H7.8V1.4C7.8 1.06863 7.53137 0.800001 7.2 0.800001H2.7ZM7.8 7.1H2.1V8.6C2.1 8.93137 2.36863 9.2 2.7 9.2H7.2C7.53137 9.2 7.8 8.93137 7.8 8.6V7.1ZM1.5 1.4C1.5 0.737259 2.03726 0.200001 2.7 0.200001H7.2C7.86274 0.200001 8.4 0.737259 8.4 1.4V8.6C8.4 9.26274 7.86274 9.8 7.2 9.8H2.7C2.03726 9.8 1.5 9.26274 1.5 8.6V1.4ZM4.65 8.15C4.65 7.98432 4.78431 7.85 4.95 7.85H4.9545C5.12019 7.85 5.2545 7.98432 5.2545 8.15C5.2545 8.31569 5.12019 8.45 4.9545 8.45H4.95C4.78431 8.45 4.65 8.31569 4.65 8.15Z" fill="black"></path></svg>';

			$top_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none"><path fill="#010101" d="M1.2 1.2h9.6L9.6 2.4H2.4L1.2 1.2Z"></path><path fill="#E7E7F6" d="m1.2 2.4 1.2 1.2v4.8L1.2 9.6V2.4Zm8.4 1.2 1.2-1.2v7.2L9.6 8.4V3.6Zm-7.2 6h7.2l1.2 1.2H1.2l1.2-1.2Z"></path></svg>';

			$right_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none"><path fill="#E7E7F6" d="M1.2 1.2h9.6L9.6 2.4H2.4L1.2 1.2Zm0 1.2 1.2 1.2v4.8L1.2 9.6V2.4Z"></path><path fill="#010101" d="m9.6 3.6 1.2-1.2v7.2L9.6 8.4V3.6Z"></path><path fill="#E7E7F6" d="M2.4 9.6h7.2l1.2 1.2H1.2l1.2-1.2Z"></path></svg>';

			$bottom_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none"><path fill="#E7E7F6" d="M1.2 1.2h9.6L9.6 2.4H2.4L1.2 1.2Zm0 1.2 1.2 1.2v4.8L1.2 9.6V2.4Zm8.4 1.2 1.2-1.2v7.2L9.6 8.4V3.6Z"></path><path fill="#010101" d="M2.4 9.6h7.2l1.2 1.2H1.2l1.2-1.2Z"></path></svg>';

			$left_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none"><path fill="#E7E7F6" d="M1.2 1.2h9.6L9.6 2.4H2.4L1.2 1.2Z"></path><path fill="#010101" d="m1.2 2.4 1.2 1.2v4.8L1.2 9.6V2.4Z"></path><path fill="#E7E7F6" d="m9.6 3.6 1.2-1.2v7.2L9.6 8.4V3.6Zm-7.2 6h7.2l1.2 1.2H1.2l1.2-1.2Z"></path></svg>';

			$dimension_value = ['top' => $top_svg, 'right' => $right_svg, 'bottom' => $bottom_svg, 'left' => $left_svg];
		?>
			<label class="nxt-resp-spacing" for="" >

				<# if ( data.label ) { #>
					<span class="customize-control-title">{{{ data.label }}}</span>
				<# } #>
				<# if ( data.description ) { #>
					<span class="description customize-control-description">{{{ data.description }}}</span>
				<# } 

				md_unit_val = 'px';
				sm_unit_val  = 'px';
				xs_unit_val  = 'px';

				if ( data.value['md-unit'] ) { 
					md_unit_val = data.value['md-unit'];
				} 

				if ( data.value['sm-unit'] ) { 
					sm_unit_val = data.value['sm-unit'];
				} 

				if ( data.value['xs-unit'] ) { 
					xs_unit_val = data.value['xs-unit'];
				}
				
				
				value_md = '';
				value_sm  = '';
				value_xs  = '';

				if ( data.value['md'] ) { 
					value_md = data.value['md'];
				} 

				if ( data.value['sm'] ) { 
					value_sm = data.value['sm'];
				} 

				if ( data.value['xs'] ) { 
					value_xs = data.value['xs'];
				}
				#>
				<div class="nxt-spacing-units-devices-wrap">
					<div class="nxt-spacing-unit-inner">
						<input type="hidden" class="nxt-spacing-unit-hidden nxt-spacing-desktop-unit" value="{{md_unit_val}}" data-device="md">
						<input type="hidden" class="nxt-spacing-unit-hidden nxt-spacing-tablet-unit" value="{{sm_unit_val}}" data-device="sm">
						<input type="hidden" class="nxt-spacing-unit-hidden nxt-spacing-mobile-unit" value="{{xs_unit_val}}" data-device="xs">
					</div>
					<ul class="nxt-resp-spacing-btns">
						<?php $devices = ['desktop' => $desktop, 'tablet' => $tablet, 'mobile' => $mobile]; 
					foreach($devices as $key => $val){ 
						$active = '';
						if($key == 'desktop' ){
							$active = ' active';
						}
					?>
						<li class="<?php echo esc_attr($key); echo esc_attr($active); ?>">
							<button type="button" class="preview-<?php echo esc_attr($key); echo esc_attr($active);?>" data-device="<?php echo esc_attr($key); ?>">
								<?php echo $val; ?>
							</button>
						</li>
					<?php } ?>
					</ul>
				</div>

				<div class="nxt-resp-spacing-wrap">
					<div class="nxt-spacing-inner-wrap">
						<ul class="nxt-spacing-devices desktop active">
							<# if ( data.linked ) { #>
							<li class="nxt-spacing-input-link-unlink" data-element-connect="{{data.id}}">
								<?php echo $linked_svg ?>
							</li><#
							}
							var dimensionValue = <?php echo json_encode($dimension_value); ?>;
							_.each( data.choices, function( label, val ) {
							#><li {{{ data.inputAttrs }}} class="nxt-spacing-input-item">
								<input type="number" class="nxt-spacing-input nxt-spacing-desktop" value="{{ value_md[ val ] }}" data-id= "{{ val }}">
								<span class="nxt-spacing-label">{{{dimensionValue[val]}}}</span>
							</li><#
							}); #>
							<select class="nxt-spacing-units-devices nxt-spacing-desktop-responsive-units">
								<#_.each( data.unit, function( key_unit ) { 
									unit_active = '', selected = '';
									if ( md_unit_val === key_unit ) { 
										unit_active = 'active';
										selected = 'selected';
									}
								#><option class="single-unit {{ unit_active }}" data-unit="{{ key_unit }}" {{selected}}>
									{{{ key_unit }}}
								</option><# 
								});#>
							</select>
						</ul>

						<ul class="nxt-spacing-devices tablet">
							<# if ( data.linked ) { #>
							<li class="nxt-spacing-input-link-unlink" data-element-connect="{{data.id}}">
								<?php echo $linked_svg ?>
							</li><#
							}
							var dimensionValue = <?php echo json_encode($dimension_value); ?>;
							_.each( data.choices, function( label, val ) { 
							#><li {{{ data.inputAttrs }}} class="nxt-spacing-input-item">
								<input type="number" class="nxt-spacing-input nxt-spacing-tablet" value="{{ value_sm[ val ] }}" data-id="{{ val }}">
								<span class="nxt-spacing-label">{{{dimensionValue[val]}}}</span>
							</li><# 
							}); #>
							<select class="nxt-spacing-units-devices nxt-spacing-tablet-responsive-units">
								<#_.each( data.unit, function( key_unit ) { 
									unit_active = '', selected = '';
									if ( sm_unit_val === key_unit ) { 
										unit_active = 'active';
										selected = 'selected';
									}
								#><option class="single-unit {{ unit_active }}" data-unit="{{ key_unit }}" {{selected}}>
									{{{ key_unit }}}
								</option><# 
								});#>
							</select>
						</ul>

						<ul class="nxt-spacing-devices mobile"><# 
							if ( data.linked ) { #>
							<li class="nxt-spacing-input-link-unlink" data-element-connect="{{data.id}}">
								<?php echo $linked_svg ?>
							</li><#
							}
							var dimensionValue = <?php echo json_encode($dimension_value); ?>;
							_.each( data.choices, function( label, val ) { 
							#><li {{{ data.inputAttrs }}} class="nxt-spacing-input-item">
								<input type="number" class="nxt-spacing-input nxt-spacing-mobile" value="{{ value_xs[ val ] }}" data-id="{{ val }}">
								<span class="nxt-spacing-label">{{{dimensionValue[val]}}}</span>
							</li><# 
							}); #>
							<select class="nxt-spacing-units-devices nxt-spacing-mobile-responsive-units">
								<#_.each( data.unit, function( key_unit ) { 
									unit_active = '', selected = '';
									if ( xs_unit_val === key_unit ) { 
										unit_active = 'active';
										selected = 'selected';
									}
								#><option class="single-unit {{ unit_active }}" data-unit="{{ key_unit }}" {{selected}}>
									{{{ key_unit }}}
								</option><# 
								});#>
							</select>
						</ul>
					</div>
				</div>
			</label>
			<?php
		}

		/**
		 * Render the control's content.
		 *
		 * @see WP_Customize_Control::render_content()
		 */
		protected function render_content() {}
	}
endif;