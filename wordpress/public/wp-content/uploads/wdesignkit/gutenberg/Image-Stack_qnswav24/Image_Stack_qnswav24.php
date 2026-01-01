<?php
    function wb_Image_Stack_qnswav24() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_externalumxhuk25-js", WDKIT_SERVER_PATH . '/gutenberg/Image-Stack_qnswav24/index.js', [],'2.0.12.244444', true);}

            
        }

        wp_register_script('wbuilder-cgb-block_umxhuk25-js', WDKIT_SERVER_PATH .'/gutenberg/Image-Stack_qnswav24/Image_Stack_qnswav24.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.902804', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_js18gs25-css', WDKIT_SERVER_PATH .'/gutenberg/Image-Stack_qnswav24/Image_Stack_qnswav24.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.628876', false );
    
        register_block_type(
            'wdkit/wb-qnswav24', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'repeater_9p8fdj23' => array(
            'type' => 'array',
            'repeaterField' => array(
                (object) array(
                    'select_l7xtcr23' => array(
            'type' => 'string',
            'default' => 'icon'
        ),
'iconscontrol_pl2cdy23' => array(
            'type' => 'string',
            'default'=> 'far fa-bell',
        ), 
'media_fu34yt23' => array(
            'type' => 'object',
            'default' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                'Id' => '',
            ),
        ), 
'url_gyefyc23' => array(
            'type' => 'object',
            'default' => array(
                'url' => '#',
                'target' => true,
                'nofollow' => 'no-follow'
            ),

        ),
'text_33si1x23' => array(
            'type' => 'string',
            'default' => 'Stack'
        ),
'select_6y68bz23' => array(
            'type' => 'string',
            'default' => 'top'
        ),
'color_5z824i23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.wkit-stack-item.stack-icon span.tp-title-icon i{color:{{color_5z824i23}};}',
                ),
            ),
        ), 
'background_usybbd23' => array(
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
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.wkit-stack-item.stack-icon span.tp-title-icon',
                ),
            ),
        ), 
'color_bn7iy123' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.wkit-stack-item .tp-title-icon{border-color:{{color_bn7iy123}};}',
                ),
            ),
        ), 

                )
            ),
            'default' => array(array(
 '_key' => '0', 
'select_l7xtcr23' => 'icon',
'iconscontrol_pl2cdy23' => 'far fa-bell', 
'media_fu34yt23' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                'Id' => '',
            ),'url_gyefyc23' => array(
                    'url' => '#',
                    'target' => true,
                    'nofollow' => 'no-follow'
                ),'text_33si1x23' => esc_html__('Stack', 'wdesignkit'), 
'select_6y68bz23' => 'top',
'color_5z824i23' => '', 
'background_usybbd23' => '', 
'color_bn7iy123' => '', 
),),
        ), 
'choose_0rlnnb23' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-image-stack{ text-align: {{choose_0rlnnb23}}; }',
                ),
            ),
        ), 
'slider_wijenl25' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-stack-item .tp-title-icon{width:{{slider_wijenl25}};}',
                ),
            ),
        ), 
'slider_6mmriz25' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-stack-item .tp-title-icon{height:{{slider_6mmriz25}};}',
                ),
            ),
        ), 
'switcher_3uiacy25' => array(
            'type' => 'boolean',
                'default' => true,
        ), 
'switcher_ioybgi25' => array(
            'type' => 'boolean',
                'default' => true,
        ), 
'slider_p4birw24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-stack-item .tp-title-icon{width:{{slider_p4birw24}};}',
                ),
            ),
        ), 
'slider_3s6ybd24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-stack-item .tp-title-icon{height:{{slider_3s6ybd24}};}',
                ),
            ),
        ), 
'slider_6gixpu23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-stack-item:not(:last-child){margin-right:{{slider_6gixpu23}};}',
                ),
            ),
        ), 
'background_axpy1723' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-stack-item .tp-title-icon',
                ),
            ),
        ), 
'border_ehuvwy23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-stack-item .tp-title-icon',
                ),
            ),

        ), 
'dimension_161nab23' => array(
            'type' => 'object',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-stack-item .tp-title-icon{ border-radius: {{dimension_161nab23}}}',
                ),
            ),
        ), 
'boxshadow_q3zdrm23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-stack-item .tp-title-icon',
                    ),
                ),
            ), 
'slider_5tcccd23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-img-stack-inner:hover .wkit-stack-item:not(:last-child){margin-right:{{slider_5tcccd23}};}',
                ),
            ),
        ), 
'background_dr4afo23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-stack-item .tp-title-icon:hover',
                ),
            ),
        ), 
'border_g8z7vx23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-stack-item .tp-title-icon:hover',
                ),
            ),

        ), 
'boxshadow_wfd8us23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-stack-item .tp-title-icon:hover',
                    ),
                ),
            ), 

'slider_ia0q8t23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-stack-item.stack-icon span.tp-title-icon i{font-size:{{slider_ia0q8t23}};}',
                ),
            ),
        ), 
'color_in59z923' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-image-stack .wkit-stack-item .tp-title-icon i{color:{{color_in59z923}};}',
                ),
            ),
        ), 
'color_c3gyg123' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-image-stack .wkit-stack-item .tp-title-icon:hover i{color:{{color_c3gyg123}};}',
                ),
            ),
        ), 

'slider_f4dtud24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-stack-item.stack-image img.tp-title-icon{width:{{slider_f4dtud24}};}',
                ),
            ),
        ), 
'slider_7txiow24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-stack-item.stack-image img.tp-title-icon{height:{{slider_7txiow24}};}',
                ),
            ),
        ), 
'dimension_ype7ov23' => array(
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
                    'selector' => '{{PLUS_WRAP}} [tooltiptext]::after{ padding: {{dimension_ype7ov23}}}',
                ),
            ),
        ), 
'typography_2lfzej23' => array(
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
                    'selector' => '{{PLUS_WRAP}} [tooltiptext]::after',
                ),
            ),
        ), 
'color_btxeeu23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} [tooltiptext]::after{color:{{color_btxeeu23}};}',
                ),
            ),
        ), 
'color_oemd0023' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} [tooltiptext]::after{background-color:{{color_oemd0023}};}',
                ),
            ),
        ), 
'color_7f269w23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} [tooltiptext]::before{border-color:{{color_7f269w23}};}',
                ),
            ),
        ), 
'border_733unm25' => array(
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
                    'selector' => '{{PLUS_WRAP}} [tooltiptext]::after',
                ),
            ),

        ), 
'dimension_2dcnaj23' => array(
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
                    'selector' => '{{PLUS_WRAP}} [tooltiptext]::after{ border-radius: {{dimension_2dcnaj23}}}',
                ),
            ),
        ), 
'boxshadow_gpce3d23' => array(
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
                        'selector' => '{{PLUS_WRAP}} [tooltiptext]::after',
                    ),
                ),
            ), 

    
                ],
                'style'         => 'wbuilder-cgb-style_js18gs25-css',
                'editor_script' => 'wbuilder-cgb-block_umxhuk25-js',
                'render_callback' => 'wkit_render_callback_uslckn25'
            )
        );
    
    }
    add_action( 'init', 'wb_Image_Stack_qnswav24' );
    
    function wkit_render_callback_uslckn25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }