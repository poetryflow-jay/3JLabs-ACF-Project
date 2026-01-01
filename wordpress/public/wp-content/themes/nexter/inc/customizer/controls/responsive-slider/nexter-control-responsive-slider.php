<?php
/**
 * Customizer Control: Slider Responsive
 * Type : nxt-responsive-slider
 *
 * @package	Nexter
 * @since	1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Nexter_Control_Responsive_Slider extends WP_Customize_Control {

	/**
	 * Control Type
	 */
	public $type = 'nxt-responsive-slider';

	/**
	 * @suffix
	 */
	public $suffix = '';
	public $units = array( 'px' => 'px' );

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
				'desktop' => $val,
				'tablet'  => '',
				'mobile'  => '',
			);
		}

		$units = array(
			'desktop-unit' => 'px',
			'tablet-unit'  => 'px',
			'mobile-unit'  => 'px',
		);

		foreach ( $units as $key_unit => $unit_value ) {
			if ( ! isset( $val[ $key_unit ] ) ) {
				$val[ $key_unit ] = $unit_value;
			}
		}

		$this->json['value']  = $val;
		$this->json['link']   = $this->get_link();
		$this->json['label']  = esc_html( $this->label );
		$this->json['id']     = $this->id;
		$this->json['suffix'] = $this->suffix;
		$this->json['units']	= $this->units;

		$this->json['inputAttrs'] = '';
		foreach ( $this->input_attrs as $attr => $value ) {
			$this->json['inputAttrs'] .= $attr . '="' . esc_attr( $value ) . '" ';
		}
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

		$desktop = '<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M1.5 1.36364C1.20991 1.36364 0.974747 1.5988 0.974747 1.88889V6.33333C0.974747 6.62342 1.20991 6.85859 1.5 6.85859H5.03986C5.04518 6.85836 5.05053 6.85824 5.0559 6.85824C5.06128 6.85824 5.06663 6.85836 5.07195 6.85859H8.61111C8.9012 6.85859 9.13636 6.62342 9.13636 6.33333V1.88889C9.13636 1.5988 8.9012 1.36364 8.61111 1.36364H1.5ZM5.41954 7.58586H8.61111C9.30286 7.58586 9.86364 7.02508 9.86364 6.33333V1.88889C9.86364 1.19714 9.30286 0.636364 8.61111 0.636364H1.5C0.808249 0.636364 0.247475 1.19714 0.247475 1.88889V6.33333C0.247475 7.02508 0.808249 7.58586 1.5 7.58586H4.69227V8.63636H3.27778C3.07695 8.63636 2.91414 8.79917 2.91414 9C2.91414 9.20083 3.07695 9.36364 3.27778 9.36364H6.83333C7.03416 9.36364 7.19697 9.20083 7.19697 9C7.19697 8.79917 7.03416 8.63636 6.83333 8.63636H5.41954V7.58586Z" fill="#010101"></path></svg>';

		$tablet = '<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M0.98616 1.32927C0.963662 1.37565 0.93913 1.4661 0.93913 1.63707V5.78333H9.05556V1.63707C9.05556 1.4661 9.03102 1.37565 9.00853 1.32927C8.99137 1.29389 8.97149 1.27427 8.93206 1.25587C8.88114 1.2321 8.79755 1.21162 8.65647 1.20044C8.51662 1.18935 8.35073 1.18913 8.14218 1.18913H1.8525C1.64396 1.18913 1.47806 1.18935 1.33822 1.20044C1.19713 1.21162 1.11354 1.2321 1.06262 1.25587C1.02319 1.27427 1.00332 1.29389 0.98616 1.32927ZM9.05556 6.47246H0.93913V7.97707C0.93913 8.10702 0.973503 8.17763 1.00794 8.22103C1.046 8.26898 1.10754 8.3125 1.20175 8.34741C1.40208 8.42165 1.6547 8.425 1.8525 8.425H8.14218C8.33999 8.425 8.59261 8.42165 8.79294 8.34741C8.88714 8.3125 8.94869 8.26898 8.98674 8.22103C9.02118 8.17763 9.05556 8.10702 9.05556 7.97707V6.47246ZM1.84298 0.5H8.15171C8.34867 0.499997 8.54097 0.499993 8.71091 0.51346C8.88234 0.527045 9.06213 0.556089 9.22349 0.631389C9.39633 0.712051 9.53763 0.841018 9.62858 1.02854C9.71418 1.20504 9.74469 1.41178 9.74469 1.63707V7.97707C9.74469 8.24336 9.66899 8.46993 9.52653 8.64943C9.38769 8.82437 9.20551 8.92945 9.0324 8.9936C8.70646 9.11439 8.34055 9.11422 8.15602 9.11413C8.15129 9.11413 8.14667 9.11413 8.14218 9.11413H1.8525C1.84801 9.11413 1.8434 9.11413 1.83867 9.11413C1.65413 9.11422 1.28822 9.11439 0.962281 8.9936C0.789176 8.92945 0.606994 8.82437 0.468154 8.64943C0.325697 8.46993 0.25 8.24336 0.25 7.97707V1.63707C0.25 1.41178 0.280503 1.20504 0.366109 1.02854C0.457057 0.841018 0.598352 0.712051 0.7712 0.631389C0.932557 0.556089 1.11235 0.527045 1.28378 0.51346C1.45372 0.499993 1.64601 0.499997 1.84298 0.5ZM4.65278 7.44873C4.65278 7.25843 4.80704 7.10417 4.99734 7.10417H5.00175C5.19204 7.10417 5.34631 7.25843 5.34631 7.44873C5.34631 7.63903 5.19204 7.7933 5.00175 7.7933H4.99734C4.80704 7.7933 4.65278 7.63903 4.65278 7.44873Z" fill="black"></path></svg>';

		$mobile = '<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M2.7 0.800001C2.36863 0.800001 2.1 1.06863 2.1 1.4V6.5H7.8V1.4C7.8 1.06863 7.53137 0.800001 7.2 0.800001H2.7ZM7.8 7.1H2.1V8.6C2.1 8.93137 2.36863 9.2 2.7 9.2H7.2C7.53137 9.2 7.8 8.93137 7.8 8.6V7.1ZM1.5 1.4C1.5 0.737259 2.03726 0.200001 2.7 0.200001H7.2C7.86274 0.200001 8.4 0.737259 8.4 1.4V8.6C8.4 9.26274 7.86274 9.8 7.2 9.8H2.7C2.03726 9.8 1.5 9.26274 1.5 8.6V1.4ZM4.65 8.15C4.65 7.98432 4.78431 7.85 4.95 7.85H4.9545C5.12019 7.85 5.2545 7.98432 5.2545 8.15C5.2545 8.31569 5.12019 8.45 4.9545 8.45H4.95C4.78431 8.45 4.65 8.31569 4.65 8.15Z" fill="black"></path></svg>';
	
	?>
		<label for="">
			<# if ( data.label ) { #>
				<span class="customize-control-title">{{{ data.label }}}</span>
				<ul class="nxt-resp-slider-devices">
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
					<?php }	?>
				</ul>
			<# } #>
			
			<# if ( data.description ) { #>
				<span class="description customize-control-description">{{{ data.description }}}</span>
			<# } 

			desktop_unit_val = 'px';
			tablet_unit_val  = 'px';
			mobile_unit_val  = 'px';

			if ( data.value['desktop-unit'] ) { 
				desktop_unit_val = data.value['desktop-unit'];
			} 

			if ( data.value['tablet-unit'] ) { 
				tablet_unit_val = data.value['tablet-unit'];
			} 

			if ( data.value['mobile-unit'] ) { 
				mobile_unit_val = data.value['mobile-unit'];
			}

			value_md = '';
			value_sm  = '';
			value_xs  = '';
			default_md = '';
			default_sm  = '';
			default_xs  = '';

			if ( data.value['desktop'] ) { 
				value_md = data.value['desktop'];
			} 

			if ( data.value['tablet'] ) { 
				value_sm = data.value['tablet'];
			} 

			if ( data.value['mobile'] ) { 
				value_xs = data.value['mobile'];
			}

			if ( data.default['desktop'] ) { 
				default_md = data.default['desktop'];
			} 

			if ( data.default['tablet'] ) { 
				default_sm = data.default['tablet'];
			} 

			if ( data.default['mobile'] ) { 
				default_xs = data.default['mobile'];
			} #>
			
			<div class="wrapper">
				<div class="nxt-slider-unit-inner">
					<input type="hidden" class="nxt-slider-unit-hidden nxt-slider-desktop-unit" value="{{desktop_unit_val}}" data-device="desktop">
					<input type="hidden" class="nxt-slider-unit-hidden nxt-slider-tablet-unit" value="{{tablet_unit_val}}" data-device="tablet">
					<input type="hidden" class="nxt-slider-unit-hidden nxt-slider-mobile-unit" value="{{mobile_unit_val}}" data-device="mobile">
				</div>
				<div class="nxt-slider-wrap desktop active">
					<input type="range" value="{{ value_md }}" data-reset="{{ default_md }}" {{{ data.inputAttrs }}} data-id="desktop"/>
					<div class="nxt-slider-field">
						<input type="number" data-id="desktop" class="nxt-responsive-slider-number" value="{{ value_md }}" {{{ data.inputAttrs }}} ><#
						if ( data.suffix ) {
						#><span class="nxt-slider-unit">{{ data.suffix }}</span><#
						} #>
					</div>
					<select class="nxt-slider-units-devices nxt-slider-desktop-responsive-units">
						<#_.each( data.units, function( key_unit ) { 
							unit_active = '', selected = '';
							if ( desktop_unit_val === key_unit ) { 
								unit_active = 'active';
								selected = 'selected';
							}
						#><option class="single-unit {{ unit_active }}" data-unit="{{ key_unit }}" {{selected}}>
							{{{ key_unit }}}
						</option><# 
						});#>
					</select>
				</div>
				<div class="nxt-slider-wrap tablet">
					<input type="range" value="{{ value_sm }}" data-reset="{{ default_sm }}" {{{ data.inputAttrs }}} data-id="tablet"/>
					<div class="nxt-slider-field">
						<input type="number" data-id="tablet" class="nxt-responsive-slider-number" value="{{ value_sm }}" {{{ data.inputAttrs }}} ><#
						if ( data.suffix ) {
						#><span class="nxt-slider-unit">{{ data.suffix }}</span><#
						} #>
					</div>
					<select class="nxt-slider-units-devices nxt-slider-tablet-responsive-units">
						<#_.each( data.units, function( key_unit ) { 
							unit_active = '', selected = '';
							if ( tablet_unit_val === key_unit ) { 
								unit_active = 'active';
								selected = 'selected';
							}
						#><option class="single-unit {{ unit_active }}" data-unit="{{ key_unit }}" {{selected}}>
							{{{ key_unit }}}
						</option><# 
						});#>
					</select>
				</div>
				<div class="nxt-slider-wrap mobile">
					<input type="range" value="{{ value_xs }}" data-reset="{{ default_xs }}" {{{ data.inputAttrs }}} data-id="mobile"/>
					<div class="nxt-slider-field">
						<input type="number" data-id="mobile" class="nxt-responsive-slider-number" value="{{ value_xs }}" {{{ data.inputAttrs }}} ><#
						if ( data.suffix ) {
						#><span class="nxt-slider-unit">{{ data.suffix }}</span><#
						} #>
					</div>
					<select class="nxt-slider-units-devices nxt-slider-mobile-responsive-units">
						<#_.each( data.units, function( key_unit ) { 
							unit_active = '', selected = '';
							if ( mobile_unit_val === key_unit ) { 
								unit_active = 'active';
								selected = 'selected';
							}
						#><option class="single-unit {{ unit_active }}" data-unit="{{ key_unit }}" {{selected}}>
							{{{ key_unit }}}
						</option><# 
						});#>
					</select>
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