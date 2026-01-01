<?php
    function wb_Flip_Card_Menu_mc5tke24() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            

            
        }

        wp_register_script('wbuilder-cgb-block_q218sc25-js', WDKIT_SERVER_PATH .'/gutenberg/Flip-Card-Menu_mc5tke24/Flip_Card_Menu_mc5tke24.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.740996', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_ftlyxs25-css', WDKIT_SERVER_PATH .'/gutenberg/Flip-Card-Menu_mc5tke24/Flip_Card_Menu_mc5tke24.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.180007', false );
    
        register_block_type(
            'wdkit/wb-mc5tke24', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'select_wp35xx23' => array(
            'type' => 'string',
            'default' => 'horizontal'
        ),
'text_i272ou23' => array(
            'type' => 'string',
            'default' => 'Swipe & See'
        ),
'iconscontrol_uopbcl23' => array(
            'type' => 'string',
            'default'=> 'fas fa-cog',
        ), 
'text_ci2kgb23' => array(
            'type' => 'string',
            'default' => 'The Plus Addons for Elementor'
        ),
'textarea_ju37rl23' => array(
            'type' => 'string',
            'default' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
        ),
'text_5tof3823' => array(
            'type' => 'string',
            'default' => 'Read More'
        ),
'iconscontrol_gbrrck23' => array(
            'type' => 'string',
            'default'=> 'fas fa-chevron-right',
        ), 
'url_obff7223' => array(
            'type' => 'object',
            'default' => array(
                'url' => '#',
                'target' => true,
                'nofollow' => 'no-follow'
            ),

        ),
'choose_z7c8ju23' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-flip-card .flip-card-frontside{ flex-direction: {{choose_z7c8ju23}}; }',
                ),
            ),
        ), 
'choose_md0hnd23' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-flip-card .flip-card-frontside{ align-items: {{choose_md0hnd23}}; }',
                ),
            ),
        ), 
'choose_xd331p23' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-flip-card .flip-card-frontside{ justify-content: {{choose_xd331p23}}; }',
                ),
            ),
        ), 
'typography_6zh6rx23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .flip-card-frontside .flip-card-frontside-title',
                ),
            ),
        ), 
'color_551wnq23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .flip-card-frontside .flip-card-frontside-title{color:{{color_551wnq23}};}',
                ),
            ),
        ), 
'slider_gp3j3123' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .flip-card-frontside .tp-title-icon{margin-top:{{slider_gp3j3123}};}',
                ),
            ),
        ), 
'slider_zbk6he23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .flip-card-frontside .tp-title-icon{margin-bottom:{{slider_zbk6he23}};}',
                ),
            ),
        ), 
'slider_5ze7ti23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .flip-card-frontside .tp-title-icon i{font-size:{{slider_5ze7ti23}};}',
                ),
            ),
        ), 
'color_6i4ih223' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .flip-card-frontside .tp-title-icon i{color:{{color_6i4ih223}};}',
                ),
            ),
        ), 
'background_um1wcq23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .flip-card-frontside .tp-title-icon',
                ),
            ),
        ), 
'border_wgzsoc23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .flip-card-frontside .tp-title-icon',
                ),
            ),

        ), 
'dimension_hnez1623' => array(
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
                    'selector' => '{{PLUS_WRAP}} .flip-card-frontside .tp-title-icon{ border-radius: {{dimension_hnez1623}}}',
                ),
            ),
        ), 
'boxshadow_9trrak23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .flip-card-frontside .tp-title-icon',
                    ),
                ),
            ), 
'typography_p0cpjw23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .flip-card-backside .flip-card-backside-content .flip-card-title',
                ),
            ),
        ), 
'color_do5i2s23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .flip-card-backside .flip-card-backside-content .flip-card-title{color:{{color_do5i2s23}};}',
                ),
            ),
        ), 
'typography_sq8pc023' => array(
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
                    'selector' => '{{PLUS_WRAP}} .flip-card-backside .flip-card-backside-content .flip-card-desk',
                ),
            ),
        ), 
'color_ux2d6123' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .flip-card-backside .flip-card-backside-content .flip-card-desk{color:{{color_ux2d6123}};}',
                ),
            ),
        ), 
'choose_pms8oi23' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-flip-card-main.type-horizontal > .flip-card-backside{ align-items: {{choose_pms8oi23}}; }',
                ),
            ),
        ), 
'slider_5iskqd25' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .flip-card-backside .tp-title-icon{margin-right:{{slider_5iskqd25}};}',
                ),
            ),
        ), 
'slider_ug3ebm23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .flip-card-backside .tp-title-icon i{font-size:{{slider_ug3ebm23}};}',
                ),
            ),
        ), 
'color_ttr60d23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .flip-card-backside .tp-title-icon i{color:{{color_ttr60d23}};}',
                ),
            ),
        ), 
'background_ed0z0x23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .flip-card-backside .tp-title-icon',
                ),
            ),
        ), 
'border_caib4i23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .flip-card-backside .tp-title-icon',
                ),
            ),

        ), 
'dimension_w1w3fv23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .flip-card-backside .tp-title-icon{ border-radius: {{dimension_w1w3fv23}}}',
                ),
            ),
        ), 
'boxshadow_0gcift23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .flip-card-backside .tp-title-icon',
                    ),
                ),
            ), 
'dimension_ady14f23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .flip-card-backside-content .flip-card-link{ padding: {{dimension_ady14f23}}}',
                ),
            ),
        ), 
'typography_3vfb1423' => array(
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
                    'selector' => '{{PLUS_WRAP}} .flip-card-backside .flip-card-backside-content .flip-card-link',
                ),
            ),
        ), 
'color_yjpoy323' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .flip-card-link{color:{{color_yjpoy323}};}',
                ),
            ),
        ), 
'background_kyfa4223' => array(
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
                    'selector' => '{{PLUS_WRAP}} .flip-card-backside-content .flip-card-link',
                ),
            ),
        ), 
'border_kyvzfb23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .flip-card-link',
                ),
            ),

        ), 
'dimension_2we1dt23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .flip-card-backside-content .flip-card-link{ border-radius: {{dimension_2we1dt23}}}',
                ),
            ),
        ), 
'boxshadow_po2phf23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .flip-card-link',
                    ),
                ),
            ), 
'background_x3t07b23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .flip-card-backside-content .flip-card-link:hover',
                ),
            ),
        ), 
'border_h5dhn123' => array(
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
                    'selector' => '{{PLUS_WRAP}} .flip-card-link:hover',
                ),
            ),

        ), 
'boxshadow_w75r6r23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .flip-card-link:hover',
                    ),
                ),
            ), 

'slider_h2w3p124' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .flip-card-backside .flip-card-backside-content{top:{{slider_h2w3p124}};}',
                ),
            ),
        ), 
'slider_l8y85s24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .flip-card-backside .flip-card-backside-content{row-gap:{{slider_l8y85s24}};}',
                ),
            ),
        ), 
'slider_rq269q23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .flip-card-backside .tp-title-icon{margin-right:{{slider_rq269q23}};}',
                ),
            ),
        ), 
'slider_i7fvqo24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-flip-card-inner .wkit-flip-card-main{width:{{slider_i7fvqo24}};}',
                ),
            ),
        ), 
'slider_7y0dec24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-flip-card-inner .wkit-flip-card-main{height:{{slider_7y0dec24}};}',
                ),
            ),
        ), 
'border_g7e9ws23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-card-1',
                ),
            ),

        ), 
'dimension_bmuok723' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-card-1{ border-radius: {{dimension_bmuok723}}}',
                ),
            ),
        ), 
'boxshadow_2gtthl23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-card-1',
                    ),
                ),
            ), 
'dimension_gswmr923' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-flip-card-main  .flip-card-frontside{ padding: {{dimension_gswmr923}}}',
                ),
            ),
        ), 
'background_0ew14c23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-flip-card-main  .flip-card-frontside',
                ),
            ),
        ), 
'dimension_3079qb23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-flip-card-main > .flip-card-backside{ padding: {{dimension_3079qb23}}}',
                ),
            ),
        ), 
'background_2kcf5723' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-flip-card-main  .flip-card-backside',
                ),
            ),
        ), 

    
                ],
                'style'         => 'wbuilder-cgb-style_ftlyxs25-css',
                'editor_script' => 'wbuilder-cgb-block_q218sc25-js',
                'render_callback' => 'wkit_render_callback_ww01ih25'
            )
        );
    
    }
    add_action( 'init', 'wb_Flip_Card_Menu_mc5tke24' );
    
    function wkit_render_callback_ww01ih25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }