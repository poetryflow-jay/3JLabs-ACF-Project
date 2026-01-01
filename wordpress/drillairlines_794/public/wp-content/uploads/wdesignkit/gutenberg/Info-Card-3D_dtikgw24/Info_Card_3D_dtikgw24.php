<?php
    function wb_Info_Card_3D_dtikgw24() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_externalrl51hx25-js", WDKIT_SERVER_PATH . '/gutenberg/Info-Card-3D_dtikgw24/index.js', array('jquery'),'2.0.12.337700', true);}

            
        }

        wp_register_script('wbuilder-cgb-block_rl51hx25-js', WDKIT_SERVER_PATH .'/gutenberg/Info-Card-3D_dtikgw24/Info_Card_3D_dtikgw24.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.232906', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_yr3ft025-css', WDKIT_SERVER_PATH .'/gutenberg/Info-Card-3D_dtikgw24/Info_Card_3D_dtikgw24.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.669037', false );
    
        register_block_type(
            'wdkit/wb-dtikgw24', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'media_rgrifu23' => array(
            'type' => 'object',
            'default' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                'Id' => '',
            ),
        ), 
'media_eryzmq23' => array(
            'type' => 'object',
            'default' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                'Id' => '',
            ),
        ), 
'select_zt4n4023' => array(
            'type' => 'string',
            'default' => 'image'
        ),
'media_i0al3x23' => array(
            'type' => 'object',
            'default' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                'Id' => '',
            ),
        ), 
'text_o3cj5e23' => array(
            'type' => 'string',
            'default' => 'Title 1'
        ),
'text_hz6zzq23' => array(
            'type' => 'string',
            'default' => 'Title 2'
        ),
'text_92s25e23' => array(
            'type' => 'string',
            'default' => 'Title 3'
        ),
'switcher_h874oh23' => array(
            'type' => 'boolean',
                'default' => false,
        ), 
'switcher_1gw9zi23' => array(
            'type' => 'boolean',
                'default' => false,
        ), 
'switcher_1fne9g23' => array(
            'type' => 'boolean',
                'default' => false,
        ), 
'url_kfr8f423' => array(
            'type' => 'object',
            'default' => array(
                'url' => '#',
                'target' => true,
                'nofollow' => 'no-follow'
            ),

        ),
'switcher_2l3uz025' => array(
            'type' => 'boolean',
                'default' => true,
        ), 
'switcher_xl8f9r25' => array(
            'type' => 'boolean',
                'default' => true,
        ), 
'choose_bksbr123' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card-main{ text-align: {{choose_bksbr123}}; }',
                ),
            ),
        ), 
'slider_348lea23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card-main .wkit-hover-3d-card-item{width:{{slider_348lea23}};}',
                ),
            ),
        ), 
'slider_6pyzzk23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card-main .wkit-hover-3d-card .wkit-hover-3d-card-wrap{height:{{slider_6pyzzk23}};}',
                ),
            ),
        ), 
'background_3tpr0j23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card-main .wkit-hover-3d-card-wrap::after',
                ),
            ),
        ), 
'dimension_jhyrrj23' => array(
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
                    'selector' => '{{PLUS_WRAP}}  .wkit-hover-3d-card-main .wkit-hover-3d-card .wkit-hover-3d-card-wrap{ border-radius: {{dimension_jhyrrj23}}}',
                ),
            ),
        ), 
'boxshadow_hqqx1u23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card  .wkit-hover-3d-card-wrap',
                    ),
                ),
            ), 
'cssfilter_76xl1g23' => array(
            'type' => 'object',
            'default' => array(
                'openFilter' => false,
                'blur' => 0,
                'brightness' => 100,
                'contrast' => 100,
                'saturate' => 100,
                'hue' => 0,
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card-wrap',
                ),
            ),
        ), 
'background_3gi7wc23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card-main .wkit-hover-3d-card:hover .wkit-hover-3d-card-wrap::before, {{PLUS_WRAP}} .wkit-hover-3d-card-main .wkit-hover-3d-card:hover .wkit-hover-3d-card-wrap::after',
                ),
            ),
        ), 
'boxshadow_b1tdot23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card-main .wkit-hover-3d-card:hover .wkit-hover-3d-card-wrap',
                    ),
                ),
            ), 
'cssfilter_iv0j2w23' => array(
            'type' => 'object',
            'default' => array(
                'openFilter' => false,
                'blur' => 0,
                'brightness' => 100,
                'contrast' => 100,
                'saturate' => 100,
                'hue' => 0,
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card:hover .wkit-hover-3d-card-wrap',
                ),
            ),
        ), 

'slider_t3kvf623' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card .wkit-hover-3d-card-title-1{margin-bottom:{{slider_t3kvf623}};}',
                ),
            ),
        ), 
'slider_w4hvrl23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card-main .wkit-hover-3d-card .wkit-stroke-yes{-webkit-text-stroke-width:{{slider_w4hvrl23}};}',
                ),
            ),
        ), 
'typography_ok2cb823' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card .wkit-hover-3d-card-title-1',
                ),
            ),
        ), 
'color_h9ep6723' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card .wkit-hover-3d-card-title-1{color:{{color_h9ep6723}};}',
                ),
            ),
        ), 
'color_1xe9kh23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card-main .wkit-hover-3d-card .wkit-stroke-yes{-webkit-text-stroke-color:{{color_1xe9kh23}};}',
                ),
            ),
        ), 
'color_iqe69k23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card-main .wkit-hover-3d-card .wkit-stroke-yes{-webkit-text-fill-color:{{color_iqe69k23}};}',
                ),
            ),
        ), 
'textshadow_0mzkjk23' => array(
            'type' => 'object',
            'default' =>(object) array(
                'openShadow' => 0,
                'typeShadow' => 'text-shadow', //"text-shadow" Or "drop-shadow"
                'horizontal' => 2,
                'vertical' => 3,
                'blur' => 2,
                'color' => "rgba(0,0,0,0.5)",
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card .wkit-hover-3d-card-title-1',
                ),
            ),
        ), 
'slider_a58gx923' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card .wkit-hover-3d-card-title-2{margin-bottom:{{slider_a58gx923}};}',
                ),
            ),
        ), 
'slider_mynzrh23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card-main .wkit-hover-3d-card .wkit-stroke-title2-active{-webkit-text-stroke-width:{{slider_mynzrh23}};}',
                ),
            ),
        ), 
'typography_vufd0e23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card .wkit-hover-3d-card-title-2',
                ),
            ),
        ), 
'color_gaetcw23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card .wkit-hover-3d-card-title-2{color:{{color_gaetcw23}};}',
                ),
            ),
        ), 
'color_wporwk23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card-main .wkit-hover-3d-card .wkit-stroke-title2-active{-webkit-text-stroke-color:{{color_wporwk23}};}',
                ),
            ),
        ), 
'color_jfkyzl23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card-main .wkit-hover-3d-card .wkit-stroke-title2-active{-webkit-text-fill-color:{{color_jfkyzl23}};}',
                ),
            ),
        ), 
'textshadow_we0ow423' => array(
            'type' => 'object',
            'default' =>(object) array(
                'openShadow' => 0,
                'typeShadow' => 'text-shadow', //"text-shadow" Or "drop-shadow"
                'horizontal' => 2,
                'vertical' => 3,
                'blur' => 2,
                'color' => "rgba(0,0,0,0.5)",
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card .wkit-hover-3d-card-title-2',
                ),
            ),
        ), 
'slider_c2nbag25' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card .wkit-hover-3d-card-title-3{margin-bottom:{{slider_c2nbag25}};}',
                ),
            ),
        ), 
'slider_wb555l25' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card-main .wkit-hover-3d-card .wkit-stroke-title3-active{-webkit-text-stroke-width:{{slider_wb555l25}};}',
                ),
            ),
        ), 
'typography_06n3ap23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card .wkit-hover-3d-card-title-3',
                ),
            ),
        ), 
'color_1r5x6223' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card .wkit-hover-3d-card-title-3{color:{{color_1r5x6223}};}',
                ),
            ),
        ), 
'color_keowse25' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card-main .wkit-hover-3d-card .wkit-stroke-title3-active{-webkit-text-stroke-color:{{color_keowse25}};}',
                ),
            ),
        ), 
'color_zjtrfb25' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card-main .wkit-hover-3d-card .wkit-stroke-title3-active{-webkit-text-fill-color:{{color_zjtrfb25}};}',
                ),
            ),
        ), 
'textshadow_owj6gw23' => array(
            'type' => 'object',
            'default' =>(object) array(
                'openShadow' => 0,
                'typeShadow' => 'text-shadow', //"text-shadow" Or "drop-shadow"
                'horizontal' => 2,
                'vertical' => 3,
                'blur' => 2,
                'color' => "rgba(0,0,0,0.5)",
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card .wkit-hover-3d-card-title-3',
                ),
            ),
        ), 
'slider_wagk7j23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card-main .wkit-hover-3d-card .wkit-hover-card-character{width:{{slider_wagk7j23}};}',
                ),
            ),
        ), 
'slider_6ra31623' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hover-3d-card-main .wkit-hover-3d-card .wkit-hover-card-character{height:{{slider_6ra31623}};}',
                ),
            ),
        ), 

    
                ],
                'style'         => 'wbuilder-cgb-style_yr3ft025-css',
                'editor_script' => 'wbuilder-cgb-block_rl51hx25-js',
                'render_callback' => 'wkit_render_callback_56j8hb25'
            )
        );
    
    }
    add_action( 'init', 'wb_Info_Card_3D_dtikgw24' );
    
    function wkit_render_callback_56j8hb25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }