<?php
    function wb_Swipe_Button_gq644y24() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            

            
        }

        wp_register_script('wbuilder-cgb-block_5adxez25-js', WDKIT_SERVER_PATH .'/gutenberg/Swipe-Button_gq644y24/Swipe_Button_gq644y24.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.420920', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_sglw2k25-css', WDKIT_SERVER_PATH .'/gutenberg/Swipe-Button_gq644y24/Swipe_Button_gq644y24.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.338621', false );
    
        register_block_type(
            'wdkit/wb-gq644y24', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'text_a2tl8t23' => array(
            'type' => 'string',
            'default' => 'Learn More'
        ),
'url_xhyz9y23' => array(
            'type' => 'object',
            'default' => array(
                'url' => '#',
                'target' => true,
                'nofollow' => 'no-follow'
            ),

        ),
'iconscontrol_dj5gjt23' => array(
            'type' => 'string',
            'default'=> 'fas fa-chevron-right',
        ), 
'select_gga2vw23' => array(
            'type' => 'string',
            'default' => 'before'
        ),
'choose_ioive023' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-swipe-btn-main{ justify-content: {{choose_ioive023}}; }',
                ),
            ),
        ), 
'dimension_frabre23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-swipe-btn{ padding: {{dimension_frabre23}}}',
                ),
            ),
        ), 
'typography_r0bhug23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-swipe-btn .wkit-swipe-btn-text',
                ),
            ),
        ), 
'color_bryhtd23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-swipe-btn .wkit-swipe-btn-text{color:{{color_bryhtd23}};}',
                ),
            ),
        ), 
'background_g840zn23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-swipe-btn',
                ),
            ),
        ), 
'border_fr0u3f23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-swipe-btn',
                ),
            ),

        ), 
'dimension_bp67dx23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-swipe-btn{ border-radius: {{dimension_bp67dx23}}}',
                ),
            ),
        ), 
'boxshadow_3wzbi523' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-swipe-btn',
                    ),
                ),
            ), 
'color_fdyk2623' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-swipe-btn:hover .wkit-swipe-btn-text{color:{{color_fdyk2623}};}',
                ),
            ),
        ), 
'background_bccjjx23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-swipe-btn:hover',
                ),
            ),
        ), 
'border_mhzvwb23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-swipe-btn:hover',
                ),
            ),

        ), 
'boxshadow_d3tc3a23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-swipe-btn:hover',
                    ),
                ),
            ), 

'slider_s7a16l23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-swipe-btn{gap:{{slider_s7a16l23}};}',
                ),
            ),
        ), 
'slider_xltx9t23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-swipe-btn .wkit-swipe-icon .tp-title-icon i{font-size:{{slider_xltx9t23}};}',
                ),
            ),
        ), 
'slider_dnehhx25' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-swipe-btn .wkit-swipe-icon .tp-title-icon i{-webkit-text-stroke-width:{{slider_dnehhx25}};}',
                ),
            ),
        ), 
'color_cxxh2623' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-swipe-btn .wkit-swipe-icon .tp-title-icon i{color:{{color_cxxh2623}};}',
                ),
            ),
        ), 
'color_pos1qt25' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-swipe-btn .wkit-swipe-icon .tp-title-icon i{-webkit-text-stroke-color:{{color_pos1qt25}};}',
                ),
            ),
        ), 
'color_kuiyv223' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-swipe-btn:hover .wkit-swipe-icon .tp-title-icon i{color:{{color_kuiyv223}};}',
                ),
            ),
        ), 
'color_iil56a25' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-swipe-btn:hover .wkit-swipe-icon .tp-title-icon i{-webkit-text-stroke-color:{{color_iil56a25}};}',
                ),
            ),
        ), 


    
                ],
                'style'         => 'wbuilder-cgb-style_sglw2k25-css',
                'editor_script' => 'wbuilder-cgb-block_5adxez25-js',
                'render_callback' => 'wkit_render_callback_px04gs25'
            )
        );
    
    }
    add_action( 'init', 'wb_Swipe_Button_gq644y24' );
    
    function wkit_render_callback_px04gs25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }