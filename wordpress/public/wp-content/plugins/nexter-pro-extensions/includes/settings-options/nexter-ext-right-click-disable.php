<?php 
/*
 * Disable Admin Settings Extension
 * @since 1.1.0
 */
defined('ABSPATH') or die();

 class Nexter_Ext_Right_Click_Disable {
    
    /**
     * Constructor
     */
    public function __construct() {
		
		$extension_option = get_option( 'nexter_site_security' );
		$switch = false;
		if( !empty($extension_option) ){
			$switch = !empty($extension_option['wp-right-click-disable']['values']) ? (isset($extension_option['wp-right-click-disable']['switch']) ? $extension_option['wp-right-click-disable']['switch'] : true) : false;
		}
		
		if (
			!empty($extension_option) && $switch && !empty($extension_option['wp-right-click-disable']['values']) && isset($extension_option['wp-right-click-disable']['values']['disable_access'])
		) {
			$wpDisableSet = $extension_option['wp-right-click-disable']['values'];
			$accessUser = $wpDisableSet['disable_access'];

			$user = wp_get_current_user();
			$roles = (array) $user->roles;
			$currUser = !empty($roles) ? reset($roles) : '';

			if (empty($roles) || ($currUser && !in_array($currUser, $accessUser, true))) {
				if (!is_admin()) {
					add_action('wp_enqueue_scripts', [$this, 'enqueue_right_click_disable_scripts']);
					add_action('wp_footer', [$this, 'nxt_right_click_dis_notice']);
				}
			}
		}
    }
	
	/*
	 * Right Click Disable Message Alert
	 * @since 1.1.0
	 */
	public function nxt_right_click_dis_notice(){
		echo '<div id="nxt-right-click-disable-alert">
			<div class="nxt-disable-inner">
				<div class="nxt-alert-content">
					<span class="nxt-alert-title">' . esc_html__( 'Warning', 'nexter-pro-extensions' ) . '</span>
					<span class="nxt-alert-message"></span>
				</div>
			</div>
			<div class="nxt-time-progress"></div>
		</div>';
	}

	/*
	 * Enqueue Right Click Disable Scripts and Styles
	 * @since 1.1.0
	 */
	public function enqueue_right_click_disable_scripts(){
		// Register script/style handles for inline-only content (no external files)
		wp_register_script('nxt-right-click-disable', false, array(), NXT_PRO_EXT_VER, true);
		wp_enqueue_script('nxt-right-click-disable');
		wp_add_inline_script('nxt-right-click-disable', $this->get_right_click_disable_js());
		
		wp_register_style('nxt-right-click-disable', false, array(), NXT_PRO_EXT_VER);
		wp_enqueue_style('nxt-right-click-disable');
		wp_add_inline_style('nxt-right-click-disable', $this->get_right_click_disable_css());
	}

	/*
	 * Get Right Click Disable JavaScript
	 * @since 1.1.0
	 */
	public function get_right_click_disable_js(){
		$extension_option = get_option( 'nexter_site_security' );

		$switch = false;
		if( !empty($extension_option) ){
			$switch = !empty($extension_option['wp-right-click-disable']['values']) ? (isset($extension_option['wp-right-click-disable']['switch']) ? $extension_option['wp-right-click-disable']['switch'] : true) : false;
		}
		if( !$switch ){
			return '';
		}
		$wpDisableSet = !empty($extension_option['wp-right-click-disable']['values']) ? $extension_option['wp-right-click-disable']['values'] : [];

		$keys = [
			'disable-r-click', 'disable-drag', 'disable-dev-key', 'disable-ser-key', 
			'disable-ctrlc-key', 'disable-ctrlv-key', 'disable-ctrla-key', 'disable-ctrlu-key',
			'disable-ctrlp-key', 'disable-ctrlh-key', 'disable-ctrll-key', 'disable-ctrlk-key',
			'disable-ctrlo-key', 'disable-ctrle-key', 'disable-altd-key', 'disable-f3-key',
			'disable-f6-key', 'disable-f12-key', 'disable-text-selection'
		];
		$settings = [];
		$msgSettings = [];
		$msgDisAp = 3000;

		foreach ($keys as $key) {
			$settings[$key] = false;
			if ($key !== 'disable-text-selection') {
				$msgSettings[$key] = '';
			}
		}
		
		if (!empty($wpDisableSet)) {
			foreach ($keys as $key) {
				$settings[$key] = !empty($wpDisableSet[$key]) ? $wpDisableSet[$key] : $settings[$key];
				if ($key !== 'disable-text-selection') {
					$msgKey = $key . '-msg';
					$msgSettings[$key] = !empty($wpDisableSet[$msgKey]) ? $wpDisableSet[$msgKey] : $msgSettings[$key];
				}
			}
		
			$msgDisAp = !empty($wpDisableSet['alert-dis-time']) ? $wpDisableSet['alert-dis-time'] : $msgDisAp;
		}

		$rightClickJs = 'var options = {
				dRightClick: ' . wp_json_encode($settings["disable-r-click"]) . ',
				dRightClickMsg: ' . wp_json_encode($msgSettings["disable-r-click"]) . ',
				
				dDrag: ' . wp_json_encode($settings["disable-drag"]) . ',
				dDragMsg: ' . wp_json_encode($msgSettings["disable-drag"]) . ',

				dDevKey: ' . wp_json_encode($settings["disable-dev-key"]) . ',
				dDevKeyMsg: ' . wp_json_encode($msgSettings["disable-dev-key"]) . ',
				
				dSerKey: ' . wp_json_encode($settings["disable-ser-key"]) . ',
				dSerKeyMsg: ' . wp_json_encode($msgSettings["disable-ser-key"]) . ',

				dCtrlcKey: ' . wp_json_encode($settings["disable-ctrlc-key"]) . ',
				dCtrlcKeyMsg: ' . wp_json_encode($msgSettings["disable-ctrlc-key"]) . ',

				dCtrlvKey: ' . wp_json_encode($settings["disable-ctrlv-key"]) . ',
				dCtrlvKeyMsg: ' . wp_json_encode($msgSettings["disable-ctrlv-key"]) . ',

				dCtrlaKey: ' . wp_json_encode($settings["disable-ctrla-key"]) . ',
				dCtrlaKeyMsg: ' . wp_json_encode($msgSettings["disable-ctrla-key"]) . ',

				dCtrluKey: ' . wp_json_encode($settings["disable-ctrlu-key"]) . ',
				dCtrluKeyMsg: ' . wp_json_encode($msgSettings["disable-ctrlu-key"]) . ',

				dCtrlpKey: ' . wp_json_encode($settings["disable-ctrlp-key"]) . ',
				dCtrlpKeyMsg: ' . wp_json_encode($msgSettings["disable-ctrlp-key"]) . ',

				dCtrlhKey: ' . wp_json_encode($settings["disable-ctrlh-key"]) . ',
				dCtrlhKeyMsg: ' . wp_json_encode($msgSettings["disable-ctrlh-key"]) . ',

				dCtrllKey: ' . wp_json_encode($settings["disable-ctrll-key"]) . ',
				dCtrllKeyMsg: ' . wp_json_encode($msgSettings["disable-ctrll-key"]) . ',

				dCtrlkKey: ' . wp_json_encode($settings["disable-ctrlk-key"]) . ',
				dCtrlkKeyMsg: ' . wp_json_encode($msgSettings["disable-ctrlk-key"]) . ',

				dCtrloKey: ' . wp_json_encode($settings["disable-ctrlo-key"]) . ',
				dCtrloKeyMsg: ' . wp_json_encode($msgSettings["disable-ctrlo-key"]) . ',

				dCtrleKey: ' . wp_json_encode($settings["disable-ctrle-key"]) . ',
				dCtrleKeyMsg: ' . wp_json_encode($msgSettings["disable-ctrle-key"]) . ',

				dAltdKey: ' . wp_json_encode($settings["disable-altd-key"]) . ',
				dAltdKeyMsg: ' . wp_json_encode($msgSettings["disable-altd-key"]) . ',

				dF3Key: ' . wp_json_encode($settings["disable-f3-key"]) . ',
				dF3KeyMsg: ' . wp_json_encode($msgSettings["disable-f3-key"]) . ',

				dF6Key: ' . wp_json_encode($settings["disable-f6-key"]) . ',
				dF6KeyMsg: ' . wp_json_encode($msgSettings["disable-f6-key"]) . ',

				dF12Key: ' . wp_json_encode($settings["disable-f12-key"]) . ',
				dF12KeyMsg: ' . wp_json_encode($msgSettings["disable-f12-key"]) . ',

				msgDisAp: ' . $msgDisAp . '
			};
			
			if(options && options.dRightClick){
				document.oncontextmenu = function() { 
					if(options.dRightClickMsg){
						show_notice(options.dRightClickMsg);
					}
					return false;
				}
			}
			if(options && options.dDrag){
				document.ondragstart = function() {
					if(options.dDragMsg){
						show_notice(options.dDragMsg);
					}
					return false; 
				}
			}
			
			document.onkeydown = function (event) {
				event = (event || window.event);
				
				if (options && options.dDevKey && ((event.ctrlKey && event.shiftKey && event.keyCode === 67) || (event.ctrlKey && event.shiftKey && event.keyCode === 73) || (event.ctrlKey && event.shiftKey && event.keyCode === 74))) {
					if(options.dDevKeyMsg){
						show_notice(options.dDevKeyMsg);
					}
					return false;
				}
				
				if (options && options.dSerKey && ((event.ctrlKey && event.shiftKey && event.keyCode === 71) || (event.ctrlKey && event.keyCode === 71) || (event.ctrlKey && event.keyCode === 70))) {
					if(options.dSerKeyMsg){
						show_notice(options.dSerKeyMsg);
					}
					return false;
				}
				
				if (options && options.dCtrlcKey && event.ctrlKey && event.keyCode === 67) {
					if(options.dCtrlcKeyMsg){
						show_notice(options.dCtrlcKeyMsg);
					}
					return false;
				}
				if (options && options.dCtrlvKey && event.ctrlKey && event.keyCode === 86) {
					if(options.dCtrlvKeyMsg){
						show_notice(options.dCtrlvKeyMsg);
					}
					return false;
				}
				
				if (options && options.dCtrlaKey && event.ctrlKey && event.keyCode === 65) {
					if(options.dCtrlaKeyMsg){
						show_notice(options.dCtrlaKeyMsg);
					}
					return false;
				}
				
				if (options && options.dCtrluKey && event.ctrlKey && event.keyCode === 85) {
					if(options.dCtrluKeyMsg){
						show_notice(options.dCtrluKeyMsg);
					}
					return false;
				}
				if (options && options.dCtrlpKey && event.ctrlKey && event.keyCode === 80) {
					if(options.dCtrlpKeyMsg){
						show_notice(options.dCtrlpKeyMsg);
					}
					return false;
				}
				if (options && options.dCtrlhKey && event.ctrlKey && event.keyCode === 72) {
					if(options.dCtrlhKeyMsg){
						show_notice(options.dCtrlhKeyMsg);
					}
					return false;
				}
				if (options && options.dCtrllKey && event.ctrlKey && event.keyCode === 76) {
					if(options.dCtrllKeyMsg){
						show_notice(options.dCtrllKeyMsg);
					}
					return false;
				}
				if (options && options.dSerKey && event.ctrlKey && event.keyCode === 75) {
					if(options.dCtrlkKeyMsg){
						show_notice(options.dCtrlkKeyMsg);
					}
					return false;
				}
				if (options && options.dCtrloKey && event.ctrlKey && event.keyCode === 79) {
					if(options.dCtrloKeyMsg){
						show_notice(options.dCtrloKeyMsg);
					}
					return false;
				}
				if (options && options.dCtrleKey && event.ctrlKey && event.keyCode === 69) {
					if(options.dCtrleKeyMsg){
						show_notice(options.dCtrleKeyMsg);
					}
					return false;
				}
				if (options && options.dAltdKey && event.altKey && event.keyCode === 65) {
					if(options.dAltdKeyMsg){
						show_notice(options.dAltdKeyMsg);
					}
					return false;
				}
				if (options && options.dF3Key && event.keyCode === 114) {
					if(options.dF3KeyMsg){
						show_notice(options.dF3KeyMsg);
					}
					return false;
				}
				if (options && options.dF6Key && event.keyCode === 117) {
					if(options.dF6KeyMsg){
						show_notice(options.dF6KeyMsg);
					}
					return false;
				}
				if (options && options.dF12Key && event.keyCode === 123) {
					if(options.dF12KeyMsg){
						show_notice(options.dF12KeyMsg);
					}
					return false;
				}
				
			}
			
			function show_notice(text) {
				var nxt_alert = document.getElementById("nxt-right-click-disable-alert");
				
				if(nxt_alert && text && text!=""){
					let msgClass = nxt_alert.querySelector(".nxt-alert-message"),
						progressClass = nxt_alert.querySelector(".nxt-time-progress");
					if(msgClass){
						msgClass.innerHTML = text;
					}
					if(progressClass){
						progressClass.classList.add("active");
					}
					
					nxt_alert.className = "active";
					setTimeout(function () {
						nxt_alert.className = nxt_alert.className.replace("active", "")
					}, options.msgDisAp);
					setTimeout(function () {
						progressClass.classList.remove("active");
					}, options.msgDisAp+300);
				}
			}';

		return $rightClickJs;
	}

	/*
	 * Get Right Click Disable CSS
	 * @since 1.1.0
	 */
	public function get_right_click_disable_css(){
		$extension_option = get_option( 'nexter_site_security' );

		$switch = false;
		if( !empty($extension_option) ){
			$switch = !empty($extension_option['wp-right-click-disable']['values']) ? (isset($extension_option['wp-right-click-disable']['switch']) ? $extension_option['wp-right-click-disable']['switch'] : true) : false;
		}
		if( !$switch ){
			return '';
		}

		$wpDisableSet = !empty($extension_option['wp-right-click-disable']['values']) ? $extension_option['wp-right-click-disable']['values'] : [];
		$rightDyCss = !empty($extension_option['wp-right-click-disable']['css']) ? $extension_option['wp-right-click-disable']['css'] : '';
		$msgDisAp = !empty($wpDisableSet['alert-dis-time']) ? $wpDisableSet['alert-dis-time'] : 3000;

		$rightClickCss ='#nxt-right-click-disable-alert {
			position: fixed;
			display: inline-flex;
			visibility: hidden;
			overflow: hidden;
			min-width: 250px;
			transform: translateX(calc(100% + 50px));
			background-color: #fff;
			align-items: center;
			border-radius: 8px;
			padding: 20px 30px;
			z-index: 999;
			box-shadow: 0 6px 20px -5px rgb(0 0 0 / 20%);
			transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.35);
		}
		#nxt-right-click-disable-alert .nxt-disable-inner {
			position: relative;
			display: inline-flex;
			align-items: center;
		}
		#nxt-right-click-disable-alert .nxt-disable-inner::before {
			content: "";
			display: inline-flex;
			align-items: center;
			justify-content: center;
			color: #fff;
			width: 35px;
			height: 35px;
			border-radius: 25px;
			background-image: url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 192 512\' fill=\'%23'.(!empty($wpDisableSet['msg-icon-color']) ? ltrim($wpDisableSet['msg-icon-color'],'#') : '222').'\'%3E%3Cpath d=\'M48 80a48 48 0 1 1 96 0A48 48 0 1 1 48 80zM0 224c0-17.7 14.3-32 32-32H96c17.7 0 32 14.3 32 32V448h32c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H64V256H32c-17.7 0-32-14.3-32-32z\'/%3E%3C/svg%3E");
			background-repeat: no-repeat no-repeat;
			background-position: center center;
			background-size: 8px;
			background-color: #313131;
		}
		#nxt-right-click-disable-alert .nxt-alert-content{
			display: flex;
			flex-direction: column;
			margin: 0 20px;
		}
		.nxt-alert-content .nxt-alert-title {
			font-size: 16px;
			font-weight: 600;
			color: #333;
		}
		.nxt-alert-content .nxt-alert-message {
			font-size: 14px;
			font-weight: 400;
			color: #666666;
		}
		#nxt-right-click-disable-alert.active {
			visibility:visible;
			transform: translateX(0%);
		}
		#nxt-right-click-disable-alert .nxt-time-progress {
			position: absolute;
			bottom: 0;
			left: 0;
			height: 3px;
			width: 100%;
		}
		#nxt-right-click-disable-alert .nxt-time-progress::before{
			content: "";
			position: absolute;
			bottom: 0;
			right: 0;
			height: 100%;
			width: 100%;
			background-color: rgb(255, 139, 0);
		}
		@keyframes nxt_time_progress {
			100% {
				right: 100%;
			}
		}';

		if(!empty($wpDisableSet['disable-text-selection'])){
			$rightClickCss .='body * :not(input):not(textarea){
				user-select:none !important;
				-khtml-user-select:none !important;
				-ms-user-select: none !important;
				-webkit-touch-callout: none !important;
				-webkit-user-select: none !important;
				-moz-user-select:none !important;
			}';
		}

		if(!empty($wpDisableSet)){
			if(isset($wpDisableSet['msg-bg-color']) && !empty($wpDisableSet['msg-bg-color'])){
				$rightClickCss .='div#nxt-right-click-disable-alert { background-color: '.esc_attr($wpDisableSet['msg-bg-color']).'}';
			}
			if(isset($wpDisableSet['msg-title-color']) && !empty($wpDisableSet['msg-title-color'])){
				$rightClickCss .='div#nxt-right-click-disable-alert .nxt-alert-content .nxt-alert-title { color: '.esc_attr($wpDisableSet['msg-title-color']).'}';
			}
			if(isset($wpDisableSet['msg-desc-color']) && !empty($wpDisableSet['msg-desc-color'])){
				$rightClickCss .='div#nxt-right-click-disable-alert .nxt-alert-content .nxt-alert-message { color: '.esc_attr($wpDisableSet['msg-desc-color']).'}';
			}
			if(isset($wpDisableSet['msg-icon-color']) && !empty($wpDisableSet['msg-icon-color'])){
				$rightClickCss .='div#nxt-right-click-disable-alert .nxt-disable-inner::before { color: '.esc_attr($wpDisableSet['msg-icon-color']).'}';
			}
			if(isset($wpDisableSet['msg-border-color']) && !empty($wpDisableSet['msg-border-color'])){
				$rightClickCss .='div#nxt-right-click-disable-alert { border: 1px solid '.esc_attr($wpDisableSet['msg-border-color']).'}';
			}
			if(!empty($msgDisAp)){
				$rightClickCss .='.nxt-time-progress.active::before {
					animation: nxt_time_progress '.esc_attr($msgDisAp).'ms linear forwards;
				}';
			}
			if(isset($wpDisableSet['msg-icon-bg-progress']) && !empty($wpDisableSet['msg-icon-bg-progress'])){
				$rightClickCss .='div#nxt-right-click-disable-alert .nxt-time-progress::before,div#nxt-right-click-disable-alert .nxt-disable-inner::before{
					background-color: '.esc_attr($wpDisableSet['msg-icon-bg-progress']).';
				}';
			}
			

			if(isset($wpDisableSet['alert-pos']) && $wpDisableSet['alert-pos']=='top_right'){
				$rightClickCss .='#nxt-right-click-disable-alert { top: 6%; right: 3%; }';
			}else if(isset($wpDisableSet['alert-pos']) && $wpDisableSet['alert-pos']=='bottom_right'){
				$rightClickCss .='#nxt-right-click-disable-alert { bottom: 3%; right: 3%; }';
			}
		}

		if(!empty($rightDyCss)){
			$rightClickCss .= $rightDyCss;
		}

		return $rightClickCss;
	}

	public static function nxtrClickCSSGenerate($wpDisableSet = []){
		$deviceVal = ['md'=>[], 'sm'=>[], 'xs'=>[]];

		/** Title Typo */
		if(!empty($wpDisableSet['titleTypo'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtTypoCss($wpDisableSet['titleTypo'], '.nxt-alert-content .nxt-alert-title', $deviceVal);
		}
		/** Desc Typo */
		if(!empty($wpDisableSet['descTypo'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtTypoCss($wpDisableSet['descTypo'], '.nxt-alert-content .nxt-alert-message', $deviceVal);
		}
		/** Message Padding */
		if(!empty($wpDisableSet['messPadding'])){
			$deviceVal = Nxt_custom_Fields_Components::nxtDimensionCss($wpDisableSet['messPadding'], '#nxt-right-click-disable-alert','padding', $deviceVal);
		}

		$make_Css = Nxt_custom_Fields_Components::nxtMakeCss($deviceVal);

		return $make_Css;
	}

}

 new Nexter_Ext_Right_Click_Disable();