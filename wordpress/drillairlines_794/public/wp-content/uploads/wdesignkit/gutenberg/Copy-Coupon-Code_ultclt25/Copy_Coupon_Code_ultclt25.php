<?php
    function wb_Copy_Coupon_Code_ultclt25() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_externalaqshgf25-js", WDKIT_SERVER_PATH . '/gutenberg/Copy-Coupon-Code_ultclt25/index.js', [],'2.1.0.423287', true);}

            
        }

        wp_register_script('wbuilder-cgb-block_aqshgf25-js', WDKIT_SERVER_PATH .'/gutenberg/Copy-Coupon-Code_ultclt25/Copy_Coupon_Code_ultclt25.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.1.0.110724', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_vomvq125-css', WDKIT_SERVER_PATH .'/gutenberg/Copy-Coupon-Code_ultclt25/Copy_Coupon_Code_ultclt25.css', is_admin() ? array( 'wp-editor' ) : null, '2.1.0.46487', false );
    
        register_block_type(
            'wdkit/wb-ultclt25', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'select_7zsrd024' => array(
            'type' => 'string',
            'default' => 'style-1'
        ),
'text_fxbxos24' => array(
            'type' => 'string',
            'default' => 'ThePlus007'
        ),
'text_0nh8x024' => array(
            'type' => 'string',
            'default' => 'Copy'
        ),
'text_fxwh2r24' => array(
            'type' => 'string',
            'default' => 'Code Copied!'
        ),
'text_0okh1q24' => array(
            'type' => 'string',
            'default' => 'Copied'
        ),
'iconscontrol_btnyb324' => array(
            'type' => 'string',
            'default'=> 'far fa-copy',
        ), 
'choose_lu222g24' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-coupon-row{  justify-content: {{choose_lu222g24}}; }',
                ),
            ),
        ), 
'choose_fvffil24' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-coupon-row #copyvalue{ text-align: {{choose_fvffil24}}; }',
                ),
            ),
        ), 
'dimension_36hkus24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-coupon-row #copyvalue{ padding: {{dimension_36hkus24}}}',
                ),
            ),
        ), 
'slider_tx5yv124' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} #copyvalue{width:{{slider_tx5yv124}};}',
                ),
            ),
        ), 
'slider_2iqmte24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-coupon-row.style-1 .wkit-coupon-code-style-1{gap:{{slider_2iqmte24}};}',
                ),
            ),
        ), 
'typography_kuepvc24' => array(
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
                    'selector' => '{{PLUS_WRAP}} #copyvalue',
                ),
            ),
        ), 
'color_e1tnvn24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-coupon-row #copyvalue{color:{{color_e1tnvn24}};}',
                ),
            ),
        ), 
'background_rc9bw924' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-coupon-row #copyvalue',
                ),
            ),
        ), 
'border_ooq38u25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-coupon-row #copyvalue',
                ),
            ),

        ), 
'dimension_fpccxy24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-coupon-row #copyvalue{ border-radius: {{dimension_fpccxy24}}}',
                ),
            ),
        ), 
'boxshadow_1lno9y25' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-coupon-row #copyvalue',
                    ),
                ),
            ), 
'dimension_2yb4y524' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-coupon-row .copybtn, {{PLUS_WRAP}} .copybtn1 .copiedinner{ padding: {{dimension_2yb4y524}}}',
                ),
            ),
        ), 
'slider_gd6ym724' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .copybtn, {{PLUS_WRAP}} .copybtn1{width:{{slider_gd6ym724}};}',
                ),
            ),
        ), 
'typography_t8e95n24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .copybtn, {{PLUS_WRAP}} .copybtn1 .copiedinner',
                ),
            ),
        ), 
'color_reuphl24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-coupon-row .copybtn, {{PLUS_WRAP}} input.copiedinner{color:{{color_reuphl24}};}',
                ),
            ),
        ), 
'background_yk768e24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-coupon-row .copybtn, {{PLUS_WRAP}} .copybtn1',
                ),
            ),
        ), 
'border_2frpeo24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-coupon-row .copybtn, {{PLUS_WRAP}} .copybtn1',
                ),
            ),

        ), 
'dimension_3feyr324' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-coupon-row .copybtn, {{PLUS_WRAP}} .copybtn1{ border-radius: {{dimension_3feyr324}}}',
                ),
            ),
        ), 
'boxshadow_gej4yj24' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-coupon-row .copybtn, {{PLUS_WRAP}} .copybtn1',
                    ),
                ),
            ), 
'color_gvi1mv24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-coupon-row .copybtn:hover, {{PLUS_WRAP}} input.copiedinner:hover, {{PLUS_WRAP}} .wkit-coupon-code-style-2:hover input.copiedinner{color:{{color_gvi1mv24}};}',
                ),
            ),
        ), 
'background_qami4r24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-coupon-row .copybtn:hover, {{PLUS_WRAP}} input.copiedinner:hover, {{PLUS_WRAP}} .copybtn1:hover',
                ),
            ),
        ), 
'border_13wh7a24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-coupon-row .copybtn:hover, {{PLUS_WRAP}} .wkit-coupon-row .copybtn1:hover',
                ),
            ),

        ), 
'boxshadow_9alk5p24' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-coupon-row .copybtn:hover, {{PLUS_WRAP}} input.copiedinner:hover, {{PLUS_WRAP}} .copybtn1:hover',
                    ),
                ),
            ), 

'slider_k90vna24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-coupon-code-style-1 .copy-btn-icon .tp-title-icon i{font-size:{{slider_k90vna24}};}',
                ),
            ),
        ), 
'color_bhx92i24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-coupon-row .copy-btn-icon .tp-title-icon i{color:{{color_bhx92i24}};}',
                ),
            ),
        ), 
'color_whb2xr24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-coupon-row .copybtn:hover .copy-btn-icon .tp-title-icon i{color:{{color_whb2xr24}};}',
                ),
            ),
        ), 

'typography_va2q3024' => array(
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
                    'selector' => '{{PLUS_WRAP}} .copiedtext',
                ),
            ),
        ), 
'color_8q05jw24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .copiedtext{color:{{color_8q05jw24}};}',
                ),
            ),
        ), 

    
                ],
                'style'         => 'wbuilder-cgb-style_vomvq125-css',
                'editor_script' => 'wbuilder-cgb-block_aqshgf25-js',
                'render_callback' => 'wkit_render_callback_s34uvy25'
            )
        );
    
    }
    add_action( 'init', 'wb_Copy_Coupon_Code_ultclt25' );
    
    function wkit_render_callback_s34uvy25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }