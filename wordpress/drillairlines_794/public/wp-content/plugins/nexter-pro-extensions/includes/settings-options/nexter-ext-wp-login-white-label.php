<?php 
/*
 * Nexter WP Login White Label
 * @since 1.1.0
 */

defined('ABSPATH') or die();

class Nexter_Ext_Wp_Login_White_Label {

    /**
     * Constructor
     */
    public function __construct() {
		$exte_option = get_option( 'nexter_extra_ext_options' );

		if(!empty($exte_option) && isset($exte_option['wp-login-white-label']) && !empty($exte_option['wp-login-white-label']['switch']) && !empty($exte_option['wp-login-white-label']['values']) ){
			add_filter( 'login_enqueue_scripts', [$this, 'nexter_wp_login_styles'] );
			add_filter( 'login_headerurl',[$this, 'nexter_wp_login_url'] );
		}
	}

	public function nexter_wp_login_url($default){
	
		$exte_option = get_option( 'nexter_extra_ext_options' );

		if(!empty($exte_option) && isset($exte_option['wp-login-white-label']) && !empty($exte_option['wp-login-white-label']['switch']) && !empty($exte_option['wp-login-white-label']['values']) ){
			$wpLoginVal = $exte_option['wp-login-white-label']['values'];
			$company_url = (!empty($wpLoginVal['fLogoLink']) && !empty($wpLoginVal['fLogoLink'])) ? $wpLoginVal['fLogoLink'] : "";
			if (!empty($company_url)) {
				return $company_url;
			}
			return $default;
		}
	}

	public function nexter_wp_login_styles(){

		$exte_option = get_option( 'nexter_extra_ext_options' );
		$wp_login_css = '';
		$wpLoginVal = [];

		$deviceVal = ['md'=>[], 'sm'=>[], 'xs'=>[]];
		if(!empty($exte_option) && isset($exte_option['wp-login-white-label']) && !empty($exte_option['wp-login-white-label']['switch']) && !empty($exte_option['wp-login-white-label']['values']) ){
			$wpLoginVal = $exte_option['wp-login-white-label']['values'];

			$getLogo = (!empty($wpLoginVal['formLogo']) && isset($wpLoginVal['formLogo']) && !empty($wpLoginVal['formLogo']['url'])) ? $wpLoginVal['formLogo']['url'] : '';
			if(!empty($getLogo)){
				$wp_login_css .= '.login #login h1 a {background-image: url('.esc_attr($getLogo).'); background-size: contain; background-position: center;}';
			}
			
			$alignment = (!empty($wpLoginVal['layoutType']) && isset($wpLoginVal['layoutType'])) ? $wpLoginVal['layoutType'] : 'left';

			$centerAlign = 'body.login div#login { padding: 30px; margin: 0; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: #fff; }';
			$leftAlign = 'body.login div#login { position: absolute; left: 0; top: 0; height: 100vh; background-color: #fff; padding: 0 30px; display: flex; flex-direction: column; justify-content: center; }';
			$rightAlign = 'body.login div#login { position: absolute; top: 0; height: 100vh; background-color: #fff; padding: 0 30px; display: flex; flex-direction: column; justify-content: center; left: auto; right: 0; }';

			if($alignment == 'center'){
				$wp_login_css .= $centerAlign;
			}else if($alignment == 'right'){
				$wp_login_css .= $rightAlign;
			}else{
				$wp_login_css .= $leftAlign;
			}
			
			if(!empty($exte_option['wp-login-white-label']['css']) && isset($exte_option['wp-login-white-label']['css'])){
				$getCSS = $exte_option['wp-login-white-label']['css'];
				$wp_login_css .= $getCSS;
			}

			$wp_login_css .= '.login form#loginform, .login form#lostpasswordform { padding: 5px; margin-top: 0; border: none; background: unset; box-shadow: none; }';

			echo '<style type="text/css">'.wp_strip_all_tags($wp_login_css).'</style>';
		}

	}

	public static function nxtWLCSSGenerate($wpLoginVal = []){
		$deviceVal = ['md'=>[], 'sm'=>[], 'xs'=>[]];
		/** Body */
		if(!empty($wpLoginVal['bodyBG'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtBackgroundCss($wpLoginVal['bodyBG'], 'body.login', $deviceVal);
		}

		/** Form */
		if(!empty($wpLoginVal['formBoxPadding'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtDimensionCss($wpLoginVal['formBoxPadding'], 'body.login div#login','padding', $deviceVal);
		}
		if(!empty($wpLoginVal['formBoxBG'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtBackgroundCss($wpLoginVal['formBoxBG'], 'body.login div#login', $deviceVal);
		}
		if(!empty($wpLoginVal['formBorder'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtBorderCss($wpLoginVal['formBorder'], 'body.login div#login', $deviceVal);
		}
		if(!empty($wpLoginVal['formBRadius'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtDimensionCss($wpLoginVal['formBRadius'], 'body.login div#login','border-radius', $deviceVal);
		}
		if(!empty($wpLoginVal['formBshadow'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtShadowCss($wpLoginVal['formBshadow'], 'body.login div#login', $deviceVal);
		}

		/** Form Logo */
		if(!empty($wpLoginVal['fLogoWidth'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtRangeCss($wpLoginVal['fLogoWidth'], '.login #login h1 a','width','', $deviceVal);
		}

		/** Form Label */
		if(!empty($wpLoginVal['formLTypo'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtTypoCss($wpLoginVal['formLTypo'], '#loginform label', $deviceVal);
		}
		if(!empty($wpLoginVal['formLColor'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtColorCss($wpLoginVal['formLColor'], '#loginform label','color', $deviceVal);
		}

		/** Form Field */
		if(!empty($wpLoginVal['formTTypo'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtTypoCss($wpLoginVal['formTTypo'], '#loginform input:not(.button)', $deviceVal);
		}
		if(!empty($wpLoginVal['formTColor'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtColorCss($wpLoginVal['formTColor'], '#loginform input:not(.button)','color', $deviceVal);
		}
		if(!empty($wpLoginVal['formTHColor'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtColorCss($wpLoginVal['formTHColor'], '#loginform input:not(.button):focus','color', $deviceVal);
		}
		if(!empty($wpLoginVal['formTBG'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtBackgroundCss($wpLoginVal['formTBG'], '#loginform input:not(.button)', $deviceVal);
		}
		if(!empty($wpLoginVal['formTFBG'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtBackgroundCss($wpLoginVal['formTFBG'], '#loginform input:not(.button):focus', $deviceVal);
		}
		if(!empty($wpLoginVal['formTBorder'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtBorderCss($wpLoginVal['formTBorder'], '#loginform input:not(.button)', $deviceVal);
		}
		if(!empty($wpLoginVal['formTFBorder'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtBorderCss($wpLoginVal['formTFBorder'], '#loginform input:not(.button):focus', $deviceVal);
		}
		if(!empty($wpLoginVal['formTBRadius'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtDimensionCss($wpLoginVal['formTBRadius'], '#loginform input:not(.button)','border-radius', $deviceVal);
		}
		if(!empty($wpLoginVal['formTFBRadius'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtDimensionCss($wpLoginVal['formTFBRadius'], '#loginform input:not(.button):focus','border-radius', $deviceVal);
		}

		/** Form Button */
		if(!empty($wpLoginVal['formBtnTypo'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtTypoCss($wpLoginVal['formBtnTypo'], '#loginform #wp-submit', $deviceVal);
		}
		if(!empty($wpLoginVal['formBtnColor'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtColorCss($wpLoginVal['formBtnColor'], '#loginform #wp-submit','color', $deviceVal);
		}
		if(!empty($wpLoginVal['formBtnHColor'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtColorCss($wpLoginVal['formBtnHColor'], '#loginform #wp-submit:hover','color', $deviceVal);
		}
		if(!empty($wpLoginVal['formBtnBG'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtBackgroundCss($wpLoginVal['formBtnBG'], '#loginform #wp-submit', $deviceVal);
		}
		if(!empty($wpLoginVal['formBtnHBG'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtBackgroundCss($wpLoginVal['formBtnHBG'], '#loginform #wp-submit:hover', $deviceVal);
		}
		if(!empty($wpLoginVal['formBtnBorder'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtBorderCss($wpLoginVal['formBtnBorder'], '#loginform #wp-submit', $deviceVal);
		}
		if(!empty($wpLoginVal['formBtnHBorder'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtBorderCss($wpLoginVal['formBtnHBorder'], '#loginform #wp-submit:hover', $deviceVal);
		}
		if(!empty($wpLoginVal['formBtnBRadius'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtDimensionCss($wpLoginVal['formBtnBRadius'], '#loginform #wp-submit','border-radius', $deviceVal);
		}
		if(!empty($wpLoginVal['formBtnHBRadius'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtDimensionCss($wpLoginVal['formBtnHBRadius'], '#loginform #wp-submit:hover','border-radius', $deviceVal);
		}
		if(!empty($wpLoginVal['formBtnBshadow'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtShadowCss($wpLoginVal['formBtnBshadow'], '#loginform #wp-submit', $deviceVal);
		}
		if(!empty($wpLoginVal['formBtnHBshadow'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtShadowCss($wpLoginVal['formBtnHBshadow'], '#loginform #wp-submit:hover', $deviceVal);
		}

		/** Form Footer */
		if(!empty($wpLoginVal['formFTypo'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtTypoCss($wpLoginVal['formFTypo'], '.login #login #backtoblog a, .login #login #nav a', $deviceVal);
		}
		if(!empty($wpLoginVal['formFColor'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtColorCss($wpLoginVal['formFColor'], '.login #login #backtoblog a, .login #login #nav a','color', $deviceVal);
		}
		if(!empty($wpLoginVal['formFHColor'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtColorCss($wpLoginVal['formFHColor'], '.login #login #backtoblog a:hover, .login #login #nav a:hover','color', $deviceVal);
		}

		$make_Css = Nxt_custom_Fields_Components::nxtMakeCss($deviceVal);

		return $make_Css;
	}

}
new Nexter_Ext_Wp_Login_White_Label();