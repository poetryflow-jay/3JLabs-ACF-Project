<?php
/**
 * Nexter Enqueue Styles And Scripts
 *
 * @package	Nexter
 * @since	1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Nexter_Frontend_Enqueue' ) ) {

	class Nexter_Frontend_Enqueue {
		
		/** 
		 * Constructor
		 */
		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 1 );
		}
		
		/**
		 * Check Local Google Font
		 * @since 1.1.0
		 */
		public function check_nxt_ext_local_google_font( $style = false){
			$check = false;
			$nxt_ext = get_option( 'nexter_extra_ext_options' );
			if( !empty($nxt_ext) && isset($nxt_ext['local-google-font']) && !empty($nxt_ext['local-google-font']['switch']) && !empty($nxt_ext['local-google-font']['values']) ){
				$check = true;
				if($style==true){
					return $nxt_ext['local-google-font']['style'];
				}
			}
			
			return $check;
		}

		/**
		 * Theme Load Css And Js
		 */
		public function enqueue_scripts(){
		
			$minified = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
			//Font Load 
			if(!$this->check_nxt_ext_local_google_font()){
				Nexter_Get_Fonts::enqueue_load_fonts();
			}
			
			//Load Style
			//wp_enqueue_style( 'nexter-style', get_template_directory_uri() . '/style' . $minified . '.css', [], NXT_VERSION );
			
			$get_sidebar = nexter_site_sidebar_layout();
			
			//Core CSS
			//reset.css, header-footer.css, container.css, sidebar.css, theme.css
			if ( nexter_settings_page_get( 'reset_min_css' ) ) {
				wp_enqueue_style( 'nexter-reset', NXT_CSS_URI . 'main/reset' . $minified . '.css', [], NXT_VERSION );
			}
			if ( nexter_settings_page_get( 'header_footer_css' ) ) {
				wp_enqueue_style( 'nexter-header-footer', NXT_CSS_URI . 'main/header-footer.css', [], NXT_VERSION );
			}
			if ( nexter_settings_page_get( 'container_css' ) ) {
				wp_enqueue_style( 'nexter-container', NXT_CSS_URI . 'main/container.css', [], NXT_VERSION );
			}
			if ( !empty( $get_sidebar['layout'] ) && $get_sidebar['layout'] !== 'no-sidebar' ) {
				wp_enqueue_style( 'nexter-sidebar', NXT_CSS_URI . 'main/sidebar.css', [], NXT_VERSION );
			}
			if ( nexter_settings_page_get( 'theme_min_css' ) ) {
				wp_enqueue_style( 'nexter-style', NXT_CSS_URI . 'main/theme' . $minified . '.css', [], NXT_VERSION );
			}else{
				wp_register_style(
					'nexter-style',
					'',     
					[],               
					NXT_VERSION
				);
				wp_enqueue_style( 'nexter-style' );
			}

			//Custom Font Load Font Face Style
			$custom_css = Nexter_Get_Fonts::get_custom_fonts_face();
			$custom_css .= $this->nexter_condition_css();
			$custom_css .= Nexter_Dynamic_Css::render_theme_css();
			if( !empty( $custom_css ) ){
				wp_add_inline_style( 'nexter-style',nexter_minify_css_generate($custom_css) );
			}

			if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
				wp_enqueue_script( 'comment-reply' );
			}
		}
		
		public function nexter_condition_css(){
			$style_css = '';
			
			if( !nexter_settings_page_get( 'header_footer_css' ) ){
				$style_css .= '.site-header .nxt-container, .site-header .nxt-container-block-editor, .site-header .nxt-container-fluid,.site-footer .nxt-container, .site-footer .nxt-container-block-editor, .site-footer .nxt-container-fluid{
					padding-right: 0;
					padding-left: 0;
				}';
			}

			$get_sidebar = nexter_site_sidebar_layout();
			$has_sidebar = !empty($get_sidebar) && ($get_sidebar['layout'] === 'left-sidebar' || $get_sidebar['layout'] === 'right-sidebar');
			if( $has_sidebar && !nexter_settings_page_get( 'container_css' ) && !nexter_settings_page_get( 'reset_min_css' )){
				$style_css .= 'html {
					line-height: 1.15;
					-webkit-text-size-adjust: 100%;
					box-sizing: border-box;
				}*, *:before, *:after {
					box-sizing: inherit;
				}';
			}
			if( $has_sidebar && !nexter_settings_page_get( 'container_css' )){
				$style_css .= '.nxt-with-sidebar {
					padding-left: 15px;
					padding-right: 15px;
				}';
			}
			if ( !nexter_settings_page_get( 'container_css' ) ) {
				$style_css .= '.comment-form .nxt-row, .nxt-single-post-content{
					margin-right: 0;
					margin-left: 0;
				}.comment-form .nxt-col{
					padding-right: 0;
					padding-left: 0;
				}';
			}
			//body frame
			$fixed_body_frame = nexter_get_option( 'fixed-body-frame' );
			if( !empty($fixed_body_frame) && $fixed_body_frame == 'on' ){
				$style_css .= '.nxt-body-frame.frame-top, .nxt-body-frame.frame-bottom {
					position: fixed;
					z-index: 12;
					right: 0;
					left: 0;
					display: block;
					padding: 0;
				}

				.nxt-body-frame.frame-top {
					top: 0;
				}

				.admin-bar .nxt-body-frame.frame-top {
					top: 32px;
				}

				.nxt-body-frame.frame-bottom {
					bottom: 0;
				}

				.nxt-body-frame.frame-left, .nxt-body-frame.frame-right {
					position: fixed;
					z-index: 12;
					top: 0;
					bottom: 0;
					display: block;
					padding: 0;
				}

				.nxt-body-frame.frame-left {
					left: 0;
				}

				.nxt-body-frame.frame-right {
					right: 0;
				}';
			}
			
			if(nexter_get_option('page-hide-fea-image') == 'on') {
				$style_css .= '.nxt-hidden{
					display: none;
				}';
			}

			//post navigation
			if( is_single() && (nexter_get_option('s-display-post-nav') == 'on' || empty(nexter_get_option('s-display-post-nav'))) ){
				$style_css .= '.nxt-row{
						display:flex;
						flex-wrap:wrap;
						margin-right:-15px;
						margin-left:-15px
					}
					.nxt-col,.nxt-col-sm-6{
						position:relative;
						width:100%;
						padding-right:15px;
						padding-left:15px
					}
					.nxt-col{
						flex-basis:0;
						flex-grow:1;
						max-width:100%
					}
				.nxt-post-next-prev .nxt-col {
					padding: 0;
				}
				.nxt-post-next-prev {
					margin: 0 0 30px;
				}
				.nxt-post-next-prev .prev,.nxt-post-next-prev .next {
					padding: 30px 6%;
					min-height: 140px;
					display: block;
					font-size: 14px;
					text-transform: uppercase;
					color: #777;
					font-weight: 400;
					letter-spacing: .5px;
				}
				.nxt-post-next-prev .prev {
					background: #f4f4f4;    
				}
				.nxt-post-next-prev .next {
					background: #fff;
					box-shadow: 0 5px 20px 1px rgba(0,0,0,.1);
				}
				.nxt-post-next-prev .prev span, .nxt-post-next-prev .next span {
					display: block;
				}
				.nxt-post-next-prev .prev span:last-child, .nxt-post-next-prev .next span:last-child {
					font-size: 20px;
					line-height: 30px;
					color: #222;
					font-weight: 600;
					text-transform: initial;
					-ms-word-wrap: break-word;
					word-wrap: break-word;
					word-break: break-word;
					margin-top: 3px;
				}';
			}
			
			//author info
			if( is_singular() && (nexter_get_option('s-display-author-info') == 'on' || empty(nexter_get_option('s-display-author-info'))) ){
				$style_css .= '.nxt-author-meta {
					position: relative;
					display: block;
					padding: 20px;	
					border-radius: 3px;
					border: 1px solid #eee;
					margin-bottom: 25px;
				}
				.nxt-author-details{
					align-items: flex-start !important;
					-ms-flex-align: start !important;
				}
				.post-author-avatar {
					margin-right: 1em;
					text-align: center;
				}
				.post-author-avatar img {
					border-radius: 50%;
					max-width: 80px;
				}
				.author-meta-title {
					position: relative;
					display: block;
					font-size: 20px;
					line-height: 1.2;
					text-transform: capitalize;
					color: #222;
					font-weight: 500;
					margin-bottom: 5px;
				}
				.post-author-desc {
					display: block;
				}';
			}

			//post tags
			if( is_singular() && get_the_tags() && (nexter_get_option('s-display-post-tags') == 'on' || empty(nexter_get_option('s-display-post-tags')))){
				$style_css .= '.nxt-tags-share-post {
					position: relative;
					display: block;
					border-top: 1px solid #eee;    
					padding-top: 12px;
					margin: 30px 0;
				}
				.nxt-post-tags {
					position: relative;
					display: block;
				}
				.nxt-post-tags ul {
					list-style: none;
					padding: 0;
					margin: 0;
				}
				.nxt-post-tags ul li {
					display: inline-block;
					margin-right: 6px;
					margin-bottom: 3px;
					margin-top: 3px;
				}
				.nxt-post-tags ul li a {
					color: #222;
					display: inline-block;
					font-weight: 400;
					text-transform: capitalize;
					font-size: 14px;
					letter-spacing: .2px;
					background: transparent;
					padding: 12px 22px;
					border-radius: 30px;
					border: 1px solid #ccc;
					line-height: 15px;
					transition: all 0.3s ease-in-out;
				}
				.nxt-post-tags ul li a:hover{
					background: #d82d34;
					color: #fff;
					border-color: #d82d34;
					box-shadow: 0 1px 3px 0 rgba(0,0,0,.2);
				}';
			}
			
			//Single Post Title
			if(is_singular() && (nexter_get_option('s-display-post-title') == 'on' || empty(nexter_get_option('s-display-post-title')))){
				$style_css .= '.single-post-title h1 {
					margin: 0;
					font-style: normal;
					font-size: 40px;
					line-height: 50px;
					font-weight: 600;
					word-spacing: 0;
					letter-spacing: 0;
					color: #222;
					text-align: left;
					margin-bottom: 5px;
					margin-top: 0;
					-ms-word-wrap: break-word;
					word-wrap: break-word;
				}';
			}

			if(is_singular() && (nexter_get_option('s-display-post-fea-image') == 'on' || empty(nexter_get_option('s-display-post-fea-image')))){
				$style_css .='.nxt-blog-image img {
					width: 100%;
				}
				.nxt-offset-top {
					margin-top: 10px;
				}';
			}

			//Page 404
			if(is_404()){
				$style_css .='.error404 .nxt-container,.error404 .nxt-container-block-editor {
					padding-right: 0;
					padding-left: 0;
				}
				.nexter-404-page {
					position: relative;
					display: block;
					width: 100%;
					padding: 40px 0;
				}
				.nexter-404-page .page-404-img {
					max-width: 350px;
					position: relative;
					display: block;
					margin: 0 auto;
				}
				.nexter-404-page .page-title {
					position: relative;
					text-align: center;
					font-size: 30px;
					line-height: 1.2;
					color: #333;
					margin: 30px 0;
				}
				.nexter-404-page .btn-back-home {
					display: inline-block;
					position: relative;
					margin: 0 auto;
					padding: 10px 18px;
					border: 2px solid #333;
					border-radius: 30px;
					color: #333;
					font-weight: 600;
					line-height: 1.2;
				}
				.error-404.not-found {
					margin: 0 auto;
				}
				.nxt-container-block-editor .site-main > .error-404 > .nxt-template-load > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.elementor){
					margin: 0 auto;
				}';
			}
			
			return $style_css;
		}
	}
	new Nexter_Frontend_Enqueue();
}