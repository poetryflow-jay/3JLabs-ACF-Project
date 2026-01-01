<?php
    function wb_Banner_3D_With_Text_powkvu24() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            wp_enqueue_script( 'wd_ex_script_63p7go25', 'https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.1/vanilla-tilt.min.js', [], '2.0.12.332173', true );

            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_externalbu7c8b25-js", WDKIT_SERVER_PATH . '/gutenberg/Banner-3D-With-Text_powkvu24/index.js', array('jquery'),'2.0.12.616180', true);}

            
        }

        wp_register_script('wbuilder-cgb-block_bu7c8b25-js', WDKIT_SERVER_PATH .'/gutenberg/Banner-3D-With-Text_powkvu24/Banner_3D_With_Text_powkvu24.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.995903', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_uwckof25-css', WDKIT_SERVER_PATH .'/gutenberg/Banner-3D-With-Text_powkvu24/Banner_3D_With_Text_powkvu24.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.412154', false );
    
        register_block_type(
            'wdkit/wb-powkvu24', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'text_2lohav23' => array(
            'type' => 'string',
            'default' => 'The Future of AI '
        ),
'media_d8fqyk23' => array(
            'type' => 'object',
            'default' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                'Id' => '',
            ),
        ), 
'text_owngik23' => array(
            'type' => 'string',
            'default' => 'Automation'
        ),
'iconscontrol_7p9n2e23' => array(
            'type' => 'string',
            'default'=> 'fas fa-cogs',
        ), 
'text_rf6gqh23' => array(
            'type' => 'string',
            'default' => 'Creativity'
        ),
'iconscontrol_a76ucn23' => array(
            'type' => 'string',
            'default'=> 'fas fa-pen',
        ), 
'text_ibsykc23' => array(
            'type' => 'string',
            'default' => 'Watch More'
        ),
'iconscontrol_hgr83i23' => array(
            'type' => 'string',
            'default'=> 'fas fa-chevron-right',
        ), 
'url_34bkrv23' => array(
            'type' => 'object',
            'default' => array(
                'url' => '#',
                'target' => true,
                'nofollow' => 'no-follow'
            ),

        ),
'slider_shzvad23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => '%',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-heading{top:{{slider_shzvad23}};}',
                ),
            ),
        ), 
'slider_a2x1hz23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => '%',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-heading{left:{{slider_a2x1hz23}};}',
                ),
            ),
        ), 
'typography_0kwrj523' => array(
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
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-heading-inner-text',
                ),
            ),
        ), 
'color_vrwl3y23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-heading-inner-text{color:{{color_vrwl3y23}};}',
                ),
            ),
        ), 
'textshadow_28i3rq25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-heading-inner-text',
                ),
            ),
        ), 
'dimension_a9s3lw23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-heading-content{ margin: {{dimension_a9s3lw23}}}',
                ),
            ),
        ), 
'slider_x2xu0w23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-heading-content{top:{{slider_x2xu0w23}};}',
                ),
            ),
        ), 
'slider_i2ibqa23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-heading-content{left:{{slider_i2ibqa23}};}',
                ),
            ),
        ), 
'slider_x0klcn24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-heading-inner .banner-3d-inner-heading-content{gap:{{slider_x0klcn24}};}',
                ),
            ),
        ), 
'typography_u90bcr23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-heading-subtitle',
                ),
            ),
        ), 
'color_zi1nod23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-heading-subtitle{color:{{color_zi1nod23}};}',
                ),
            ),
        ), 
'background_0scwvp23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-heading-subtitle',
                ),
            ),
        ), 
'border_owgmr523' => array(
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
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-heading-subtitle',
                ),
            ),

        ), 
'dimension_jdjlld23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-heading-subtitle{ border-radius: {{dimension_jdjlld23}}}',
                ),
            ),
        ), 
'boxshadow_py2c4123' => array(
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
                        'selector' => '{{PLUS_WRAP}} .banner-3d-inner-heading-subtitle',
                    ),
                ),
            ), 
'slider_dxt92y25' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-heading-inner .banner-3d-inner-heading-content{gap:{{slider_dxt92y25}};}',
                ),
            ),
        ), 
'slider_l1oaun23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-heading .tp-title-icon i{font-size:{{slider_l1oaun23}};}',
                ),
            ),
        ), 
'color_2lsa3223' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-heading .tp-title-icon i{color:{{color_2lsa3223}};}',
                ),
            ),
        ), 
'slider_i87qmm23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => '%',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-btn{top:{{slider_i87qmm23}};}',
                ),
            ),
        ), 
'slider_jokpyw23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => '%',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-btn{left:{{slider_jokpyw23}};}',
                ),
            ),
        ), 
'typography_u7mycy23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-btn-text',
                ),
            ),
        ), 
'color_qfshax23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-btn-text{color:{{color_qfshax23}};}',
                ),
            ),
        ), 
'background_ht728f23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-btn .tp-title-icon',
                ),
            ),
        ), 
'border_l3fepp23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-btn .tp-title-icon',
                ),
            ),

        ), 
'boxshadow_6gqgma23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .banner-3d-inner-btn .tp-title-icon',
                    ),
                ),
            ), 
'color_en7opi23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .btn-banner:hover .banner-3d-inner-btn-text{color:{{color_en7opi23}};}',
                ),
            ),
        ), 
'background_ah7cuv23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-btn:hover .btn-banner .tp-title-icon',
                ),
            ),
        ), 
'border_u0i0l323' => array(
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
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-btn:hover .btn-banner .tp-title-icon',
                ),
            ),

        ), 
'boxshadow_09uoe123' => array(
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
                        'selector' => '{{PLUS_WRAP}} .banner-3d-inner-btn:hover .btn-banner .tp-title-icon',
                    ),
                ),
            ), 

'slider_724x6123' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-btn .tp-title-icon{width:{{slider_724x6123}};}',
                ),
            ),
        ), 
'slider_pkwmqc23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-btn .tp-title-icon{height:{{slider_pkwmqc23}};}',
                ),
            ),
        ), 
'slider_taxx7t23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-btn .tp-title-icon i{font-size:{{slider_taxx7t23}};}',
                ),
            ),
        ), 
'color_9f0ijv23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-btn .tp-title-icon i{color:{{color_9f0ijv23}};}',
                ),
            ),
        ), 
'color_4zpfye23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-btn:hover .btn-banner .tp-title-icon i{color:{{color_4zpfye23}};}',
                ),
            ),
        ), 

'slider_6kca7623' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-media{max-width:{{slider_6kca7623}};}',
                ),
            ),
        ), 
'slider_g6v4ta23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-media-inner{height:{{slider_g6v4ta23}};}',
                ),
            ),
        ), 
'background_4q0qow23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-media-inner',
                ),
            ),
        ), 
'border_qepes223' => array(
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
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-media',
                ),
            ),

        ), 
'dimension_qyjrez23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-media{ border-radius: {{dimension_qyjrez23}}}',
                ),
            ),
        ), 

    
                ],
                'style'         => 'wbuilder-cgb-style_uwckof25-css',
                'editor_script' => 'wbuilder-cgb-block_bu7c8b25-js',
                'render_callback' => 'wkit_render_callback_05ba2t25'
            )
        );
    
    }
    add_action( 'init', 'wb_Banner_3D_With_Text_powkvu24' );
    
    function wkit_render_callback_05ba2t25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }