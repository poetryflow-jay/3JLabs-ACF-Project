<?php
    function wb_Horizontal_Parallax_Slider_aockms24() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            wp_enqueue_script( 'wd_ex_script_7p88rf25', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.0.7/js/swiper.min.js', [], '2.0.12.175752', true );

            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_externala4t5cq25-js", WDKIT_SERVER_PATH . '/gutenberg/Horizontal-Parallax-Slider_aockms24/index.js', [],'2.0.12.438123', true);}

            wp_enqueue_style( 'wd_css_ex_1_pyjqs625', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.0.7/css/swiper.min.css', false, '2.0.12.193288', 'all' );

        }

        wp_register_script('wbuilder-cgb-block_a4t5cq25-js', WDKIT_SERVER_PATH .'/gutenberg/Horizontal-Parallax-Slider_aockms24/Horizontal_Parallax_Slider_aockms24.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.841371', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_5l9lg025-css', WDKIT_SERVER_PATH .'/gutenberg/Horizontal-Parallax-Slider_aockms24/Horizontal_Parallax_Slider_aockms24.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.322923', false );
    
        register_block_type(
            'wdkit/wb-aockms24', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'repeater_xusa8q23' => array(
            'type' => 'array',
            'repeaterField' => array(
                (object) array(
                    'media_nd5c1223' => array(
            'type' => 'object',
            'default' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/uploads/sites/67/2024/05/placeholder-614.png',
                'Id' => '',
            ),
        ), 
'text_wwxxt823' => array(
            'type' => 'string',
            'default' => 'The Plus'
        ),
'textarea_2d6fdp23' => array(
            'type' => 'string',
            'default' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
        ),

                )
            ),
            'default' => array(array(
 '_key' => '0', 
'media_nd5c1223' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/uploads/sites/67/2024/05/placeholder-614.png',
                'Id' => '',
            ),'text_wwxxt823' => esc_html__('The Plus', 'wdesignkit'), 
'textarea_2d6fdp23' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 'wdesignkit'), 
),),
        ), 
'heading_lvxens23' => array(
            'type' => 'string',
        ), 
'number_762mii23' => array(
        'type' => 'string',
        'default' => '6',
        
    ),
'number_nqdoo823' => array(
        'type' => 'string',
        'default' => '6',
        
    ),
'number_ytr5xm23' => array(
        'type' => 'string',
        'default' => '6',
        
    ),
'number_7xampd23' => array(
        'type' => 'string',
        'default' => '6',
        
    ),
'number_cjvw9323' => array(
        'type' => 'string',
        'default' => '6',
        
    ),
'number_jrqiju23' => array(
        'type' => 'string',
        'default' => '6',
        
    ),
'select_yvs4bc23' => array(
            'type' => 'string',
            'default' => 'left'
        ),
'typography_lbldfg23' => array(
            'type' => 'object',
            'default'=>(object) array(
                'openTypography' => 0,
                'size' => array('md' => 20, 'unit' => 'px'),
                'height' => array('md' => 22, 'unit' => 'px'),
                'spacing' => array('md' => 0.1, 'unit' => 'px'),
                'fontFamily' => array(
                    'family' => '',
                    'fontWeight' => 400,
                ),
                'fontStyle' => 'Default',
                'textTransform' => 'None',
                'textDecoration' => 'Default',
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-nav-slider .wkit-slide-title',
                ),
            ),
        ), 
'dimension_s73szz23' => array(
            'type' => 'object',
            'default' => (object) array(
                'md' => array(
                    "top" => "",
                    "right" => "",
                    "bottom" => "",
                    "left" => "",
                ),
                "unit" => "px",
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-nav-slider .wkit-slider-content{ padding: {{dimension_s73szz23}}}',
                ),
            ),
        ), 
'choose_5cm6x223' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-nav-slider .wkit-slider-content .wkit-slide-title{ text-align: {{choose_5cm6x223}}; }',
                ),
            ),
        ), 
'choose_6s0es223' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-nav-slider .wkit-slider-content{ justify-content: {{choose_6s0es223}}; }',
                ),
            ),
        ), 
'color_j0mtyo23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-nav-slider .wkit-slide-title{color:{{color_j0mtyo23}};}',
                ),
            ),
        ), 
'color_vtucw123' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-nav-slider .nav-slide-overlay{background-color:{{color_vtucw123}};}',
                ),
            ),
        ), 
'border_y1akov23' => array(
            'type' => 'object',
            'default' =>(object) array(
                'openBorder' => 0,
                'type' => '',
                'color' => '',
                'width' =>(object) array(
                    'md' =>(object)array(
                        'top' => '',
                        'left' => '',
                        'bottom' => '',
                        'right' => '',
                    ),
                    'sm' =>(object)array(),
                    'xs' =>(object)array(),
                    "unit" => "",
                ),
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-nav-slider .swiper-slide',
                ),
            ),

        ), 
'dimension_8ds8jt23' => array(
            'type' => 'object',
            'default' => (object) array(
                'md' => array(
                    "top" => "",
                    "right" => "",
                    "bottom" => "",
                    "left" => "",
                ),
                "unit" => "px",
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-nav-slider .swiper-slide{ border-radius: {{dimension_8ds8jt23}}}',
                ),
            ),
        ), 
'boxshadow_ua51a623' => array(
            'type' => 'object',
            'default' =>(object) array(
                'openShadow' => 0,
                'inset' => 0,
                'horizontal' => 0,
                'vertical' => 4,
                'blur' => 8,
                'spread' => 0,
                'color' => "rgba(0,0,0,0.40)",
            ),
            'style' => array(
                    (object) array(
                        'selector' => '{{PLUS_WRAP}} .wkit-nav-slider .swiper-slide',
                    ),
                ),
            ), 
'color_08gz6m23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-nav-slider .swiper-slide:hover .wkit-slide-title{color:{{color_08gz6m23}};}',
                ),
            ),
        ), 
'color_kdsel523' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-nav-slider .swiper-slide:hover .nav-slide-overlay{background-color:{{color_kdsel523}};}',
                ),
            ),
        ), 
'border_tqk44623' => array(
            'type' => 'object',
            'default' =>(object) array(
                'openBorder' => 0,
                'type' => '',
                'color' => '',
                'width' =>(object) array(
                    'md' =>(object)array(
                        'top' => '',
                        'left' => '',
                        'bottom' => '',
                        'right' => '',
                    ),
                    'sm' =>(object)array(),
                    'xs' =>(object)array(),
                    "unit" => "",
                ),
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-nav-slider .swiper-slide:hover',
                ),
            ),

        ), 
'dimension_e6d2pz23' => array(
            'type' => 'object',
            'default' => (object) array(
                'md' => array(
                    "top" => "",
                    "right" => "",
                    "bottom" => "",
                    "left" => "",
                ),
                "unit" => "px",
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-nav-slider .swiper-slide:hover{ border-radius: {{dimension_e6d2pz23}}}',
                ),
            ),
        ), 
'boxshadow_72dhqb23' => array(
            'type' => 'object',
            'default' =>(object) array(
                'openShadow' => 0,
                'inset' => 0,
                'horizontal' => 0,
                'vertical' => 4,
                'blur' => 8,
                'spread' => 0,
                'color' => "rgba(0,0,0,0.40)",
            ),
            'style' => array(
                    (object) array(
                        'selector' => '{{PLUS_WRAP}} .wkit-nav-slider .swiper-slide:hover',
                    ),
                ),
            ), 
'color_la9z9k23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-nav-slider .swiper-slide.swiper-slide-active .wkit-slide-title{color:{{color_la9z9k23}};}',
                ),
            ),
        ), 
'color_q12b3b23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-nav-slider .swiper-slide.swiper-slide-active .nav-slide-overlay{background-color:{{color_q12b3b23}};}',
                ),
            ),
        ), 
'border_hdlhhk23' => array(
            'type' => 'object',
            'default' =>(object) array(
                'openBorder' => 0,
                'type' => '',
                'color' => '',
                'width' =>(object) array(
                    'md' =>(object)array(
                        'top' => '',
                        'left' => '',
                        'bottom' => '',
                        'right' => '',
                    ),
                    'sm' =>(object)array(),
                    'xs' =>(object)array(),
                    "unit" => "",
                ),
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-nav-slider .swiper-slide.swiper-slide-active',
                ),
            ),

        ), 
'dimension_uugqj923' => array(
            'type' => 'object',
            'default' => (object) array(
                'md' => array(
                    "top" => "",
                    "right" => "",
                    "bottom" => "",
                    "left" => "",
                ),
                "unit" => "px",
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-nav-slider .swiper-slide.swiper-slide-active{ border-radius: {{dimension_uugqj923}}}',
                ),
            ),
        ), 
'boxshadow_2729f823' => array(
            'type' => 'object',
            'default' =>(object) array(
                'openShadow' => 0,
                'inset' => 0,
                'horizontal' => 0,
                'vertical' => 4,
                'blur' => 8,
                'spread' => 0,
                'color' => "rgba(0,0,0,0.40)",
            ),
            'style' => array(
                    (object) array(
                        'selector' => '{{PLUS_WRAP}} .wkit-nav-slider .swiper-slide.swiper-slide-active',
                    ),
                ),
            ), 

'heading_vl8jm123' => array(
            'type' => 'string',
        ), 
'typography_4up2m223' => array(
            'type' => 'object',
            'default'=>(object) array(
                'openTypography' => 0,
                'size' => array('md' => 20, 'unit' => 'px'),
                'height' => array('md' => 22, 'unit' => 'px'),
                'spacing' => array('md' => 0.1, 'unit' => 'px'),
                'fontFamily' => array(
                    'family' => '',
                    'fontWeight' => 400,
                ),
                'fontStyle' => 'Default',
                'textTransform' => 'None',
                'textDecoration' => 'Default',
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-main-slider .wkit-slide-title',
                ),
            ),
        ), 
'slider_3t3vkg23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-main-slider .wkit-slide-title{margin-bottom:{{slider_3t3vkg23}};}',
                ),
            ),
        ), 
'heading_69y7v423' => array(
            'type' => 'string',
        ), 
'typography_duilkw23' => array(
            'type' => 'object',
            'default'=>(object) array(
                'openTypography' => 0,
                'size' => array('md' => 20, 'unit' => 'px'),
                'height' => array('md' => 22, 'unit' => 'px'),
                'spacing' => array('md' => 0.1, 'unit' => 'px'),
                'fontFamily' => array(
                    'family' => '',
                    'fontWeight' => 400,
                ),
                'fontStyle' => 'Default',
                'textTransform' => 'None',
                'textDecoration' => 'Default',
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-main-slider .wkit-slide-desc',
                ),
            ),
        ), 
'dimension_ty1a7123' => array(
            'type' => 'object',
            'default' => (object) array(
                'md' => array(
                    "top" => "",
                    "right" => "",
                    "bottom" => "",
                    "left" => "",
                ),
                "unit" => "px",
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-main-slider .wkit-slider-content{ padding: {{dimension_ty1a7123}}}',
                ),
            ),
        ), 
'choose_z52kxr23' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-slide-title, {{PLUS_WRAP}} .wkit-slide-desc{ text-align: {{choose_z52kxr23}}; }',
                ),
            ),
        ), 
'choose_d8pdmx23' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-main-slider .wkit-slider-content{ justify-content: {{choose_d8pdmx23}}; }',
                ),
            ),
        ), 
'heading_lijhqz23' => array(
            'type' => 'string',
        ), 
'background_825k2m23' => array(
            'type' => 'object',
            'default' =>(object) array(
            'openBg'=> 0,
            'bgType' => "color",
            'videoSource' => 'local',
            'bgDefaultColor' => '',
            'bgGradient' =>(object) array('color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false),
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-main-slider .swiper-slide .main-slide-overlay',
                ),
            ),
        ), 
'color_qghma023' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-main-slider .swiper-slide .wkit-slide-title{color:{{color_qghma023}};}',
                ),
            ),
        ), 
'color_b3gw1x23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-main-slider .swiper-slide .wkit-slide-desc{color:{{color_b3gw1x23}};}',
                ),
            ),
        ), 
'border_y8b9pb23' => array(
            'type' => 'object',
            'default' =>(object) array(
                'openBorder' => 0,
                'type' => '',
                'color' => '',
                'width' =>(object) array(
                    'md' =>(object)array(
                        'top' => '',
                        'left' => '',
                        'bottom' => '',
                        'right' => '',
                    ),
                    'sm' =>(object)array(),
                    'xs' =>(object)array(),
                    "unit" => "",
                ),
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-main-slider .swiper-slide',
                ),
            ),

        ), 
'dimension_l8p22p23' => array(
            'type' => 'object',
            'default' => (object) array(
                'md' => array(
                    "top" => "",
                    "right" => "",
                    "bottom" => "",
                    "left" => "",
                ),
                "unit" => "px",
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-main-slider .swiper-slide{ border-radius: {{dimension_l8p22p23}}}',
                ),
            ),
        ), 
'boxshadow_w4xd7o23' => array(
            'type' => 'object',
            'default' =>(object) array(
                'openShadow' => 0,
                'inset' => 0,
                'horizontal' => 0,
                'vertical' => 4,
                'blur' => 8,
                'spread' => 0,
                'color' => "rgba(0,0,0,0.40)",
            ),
            'style' => array(
                    (object) array(
                        'selector' => '{{PLUS_WRAP}} .wkit-main-slider .swiper-slide',
                    ),
                ),
            ), 
'color_0beo7w23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-main-slider .swiper-slide:hover .wkit-slide-title{color:{{color_0beo7w23}};}',
                ),
            ),
        ), 
'color_9do1xc23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-main-slider .swiper-slide:hover .wkit-slide-desc{color:{{color_9do1xc23}};}',
                ),
            ),
        ), 
'border_hudn6z23' => array(
            'type' => 'object',
            'default' =>(object) array(
                'openBorder' => 0,
                'type' => '',
                'color' => '',
                'width' =>(object) array(
                    'md' =>(object)array(
                        'top' => '',
                        'left' => '',
                        'bottom' => '',
                        'right' => '',
                    ),
                    'sm' =>(object)array(),
                    'xs' =>(object)array(),
                    "unit" => "",
                ),
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-main-slider .swiper-slide:hover',
                ),
            ),

        ), 
'dimension_1fhooe23' => array(
            'type' => 'object',
            'default' => (object) array(
                'md' => array(
                    "top" => "",
                    "right" => "",
                    "bottom" => "",
                    "left" => "",
                ),
                "unit" => "px",
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-main-slider .swiper-slide:hover{ : {{dimension_1fhooe23}}}',
                ),
            ),
        ), 
'boxshadow_hfpgih23' => array(
            'type' => 'object',
            'default' =>(object) array(
                'openShadow' => 0,
                'inset' => 0,
                'horizontal' => 0,
                'vertical' => 4,
                'blur' => 8,
                'spread' => 0,
                'color' => "rgba(0,0,0,0.40)",
            ),
            'style' => array(
                    (object) array(
                        'selector' => '{{PLUS_WRAP}} .wkit-main-slider .swiper-slide:hover',
                    ),
                ),
            ), 

'slider_dj2v0123' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .swiper-button-next, {{PLUS_WRAP}} .swiper-button-prev{width:{{slider_dj2v0123}};}',
                ),
            ),
        ), 
'slider_t29ndc23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .swiper-button-next, {{PLUS_WRAP}} .swiper-button-prev{height:{{slider_t29ndc23}};}',
                ),
            ),
        ), 
'slider_6cmrwg23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .swiper-button-next:after, {{PLUS_WRAP}} .swiper-button-prev:after{font-size:{{slider_6cmrwg23}};}',
                ),
            ),
        ), 
'color_yas8wd23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .swiper-button-next:after, {{PLUS_WRAP}} .swiper-button-prev:after{color:{{color_yas8wd23}};}',
                ),
            ),
        ), 
'background_nts6e223' => array(
            'type' => 'object',
            'default' =>(object) array(
            'openBg'=> 0,
            'bgType' => "color",
            'videoSource' => 'local',
            'bgDefaultColor' => '',
            'bgGradient' =>(object) array('color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false),
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .swiper-button-next, {{PLUS_WRAP}} .swiper-button-prev',
                ),
            ),
        ), 
'border_fxvtfw23' => array(
            'type' => 'object',
            'default' =>(object) array(
                'openBorder' => 0,
                'type' => '',
                'color' => '',
                'width' =>(object) array(
                    'md' =>(object)array(
                        'top' => '',
                        'left' => '',
                        'bottom' => '',
                        'right' => '',
                    ),
                    'sm' =>(object)array(),
                    'xs' =>(object)array(),
                    "unit" => "",
                ),
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .swiper-button-next, {{PLUS_WRAP}} .swiper-button-prev',
                ),
            ),

        ), 
'dimension_47uh7p23' => array(
            'type' => 'object',
            'default' => (object) array(
                'md' => array(
                    "top" => "",
                    "right" => "",
                    "bottom" => "",
                    "left" => "",
                ),
                "unit" => "px",
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .swiper-button-next, {{PLUS_WRAP}} .swiper-button-prev{ border-radius: {{dimension_47uh7p23}}}',
                ),
            ),
        ), 
'boxshadow_649gyd23' => array(
            'type' => 'object',
            'default' =>(object) array(
                'openShadow' => 0,
                'inset' => 0,
                'horizontal' => 0,
                'vertical' => 4,
                'blur' => 8,
                'spread' => 0,
                'color' => "rgba(0,0,0,0.40)",
            ),
            'style' => array(
                    (object) array(
                        'selector' => '{{PLUS_WRAP}} .swiper-button-next, {{PLUS_WRAP}} .swiper-button-prev',
                    ),
                ),
            ), 
'color_g2oqdt23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .swiper-button-next:hover:after, {{PLUS_WRAP}} .swiper-button-prev:hover:after{color:{{color_g2oqdt23}};}',
                ),
            ),
        ), 
'background_f2y8be23' => array(
            'type' => 'object',
            'default' =>(object) array(
            'openBg'=> 0,
            'bgType' => "color",
            'videoSource' => 'local',
            'bgDefaultColor' => '',
            'bgGradient' =>(object) array('color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false),
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .swiper-button-next:hover, {{PLUS_WRAP}} .swiper-button-prev:hover',
                ),
            ),
        ), 
'border_9dwo0k23' => array(
            'type' => 'object',
            'default' =>(object) array(
                'openBorder' => 0,
                'type' => '',
                'color' => '',
                'width' =>(object) array(
                    'md' =>(object)array(
                        'top' => '',
                        'left' => '',
                        'bottom' => '',
                        'right' => '',
                    ),
                    'sm' =>(object)array(),
                    'xs' =>(object)array(),
                    "unit" => "",
                ),
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .swiper-button-next:hover, {{PLUS_WRAP}} .swiper-button-prev:hover',
                ),
            ),

        ), 
'dimension_evf5lq23' => array(
            'type' => 'object',
            'default' => (object) array(
                'md' => array(
                    "top" => "",
                    "right" => "",
                    "bottom" => "",
                    "left" => "",
                ),
                "unit" => "px",
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .swiper-button-next:hover, {{PLUS_WRAP}} .swiper-button-prev:hover{ border-radius: {{dimension_evf5lq23}}}',
                ),
            ),
        ), 
'boxshadow_d91slq23' => array(
            'type' => 'object',
            'default' =>(object) array(
                'openShadow' => 0,
                'inset' => 0,
                'horizontal' => 0,
                'vertical' => 4,
                'blur' => 8,
                'spread' => 0,
                'color' => "rgba(0,0,0,0.40)",
            ),
            'style' => array(
                    (object) array(
                        'selector' => '{{PLUS_WRAP}} .swiper-button-next:hover, {{PLUS_WRAP}} .swiper-button-prev:hover',
                    ),
                ),
            ), 

'slider_m2j52i23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-slider-wrap{height:{{slider_m2j52i23}};}',
                ),
            ),
        ), 

    
                ],
                'style'         => 'wbuilder-cgb-style_5l9lg025-css',
                'editor_script' => 'wbuilder-cgb-block_a4t5cq25-js',
                'render_callback' => 'wkit_render_callback_o9eemj25'
            )
        );
    
    }
    add_action( 'init', 'wb_Horizontal_Parallax_Slider_aockms24' );
    
    function wkit_render_callback_o9eemj25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }