<?php
    function wb_Text_Halftone_Effects_3s2fdi24() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_externalzy1oiq25-js", WDKIT_SERVER_PATH . '/gutenberg/Text-Halftone-Effects_3s2fdi24/index.js', array('jquery'),'2.1.0.57613', true);}

            
        }

        wp_register_script('wbuilder-cgb-block_zy1oiq25-js', WDKIT_SERVER_PATH .'/gutenberg/Text-Halftone-Effects_3s2fdi24/Text_Halftone_Effects_3s2fdi24.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.1.0.546082', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_8cs3mu25-css', WDKIT_SERVER_PATH .'/gutenberg/Text-Halftone-Effects_3s2fdi24/Text_Halftone_Effects_3s2fdi24.css', is_admin() ? array( 'wp-editor' ) : null, '2.1.0.125187', false );
    
        register_block_type(
            'wdkit/wb-3s2fdi24', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'text_q4ppee23' => array(
            'type' => 'string',
            'default' => 'Posimyth'
        ),
'url_6eq7uw24' => array(
            'type' => 'object',
            'default' => array(
                'url' => '#',
                'target' => true,
                'nofollow' => 'no-follow'
            ),

        ),
'switcher_zfcj0m24' => array(
            'type' => 'boolean',
                'default' => false,
        ), 
'repeater_5k4fcf24' => array(
            'type' => 'array',
            'repeaterField' => array(
                (object) array(
                    'color_9ly20l24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.wkit-shadow-inner.layer-yes{-webkit-text-fill-color:{{color_9ly20l24}};}',
                ),
            ),
        ), 
'switcher_h4vi6a24' => array(
            'type' => 'boolean',
                'default' => false,
        ), 
'background_khoudh24' => array(
            'type' => 'object',
            'default' =>(object) array(
            'openBg'=> 0,
            'bgType' => "gradient",
            'videoSource' => 'local',
            'bgDefaultColor' => '',
            'bgGradient' =>(object) array('color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false),
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.wkit-shadow-inner.layer-yes',
                ),
            ),
        ), 
'color_l962ur24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.wkit-shadow-inner.layer-yes{-webkit-text-stroke-color:{{color_l962ur24}};}',
                ),
            ),
        ), 
'slider_pb0vso24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.wkit-shadow-inner.layer-yes{-webkit-text-stroke-width:{{slider_pb0vso24}};}',
                ),
            ),
        ), 
'slider_pbqwn824' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.wkit-shadow-inner.layer-yes{--layerToffset:{{slider_pbqwn824}};}',
                ),
            ),
        ), 
'slider_hywd2724' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.wkit-shadow-inner.layer-yes{--layerLoffset:{{slider_hywd2724}};}',
                ),
            ),
        ), 

                )
            ),
            'default' => array(array(
 '_key' => '0', 
'color_9ly20l24' => '', 
'switcher_h4vi6a24' => false,
'background_khoudh24' => '', 
'color_l962ur24' => '', 
'slider_pb0vso24' => '',
'slider_pbqwn824' => '',
'slider_hywd2724' => '',
),),
        ), 
'switcher_vq3o2k23' => array(
            'type' => 'boolean',
                'default' => true,
        ), 
'switcher_l9n3jd24' => array(
            'type' => 'boolean',
                'default' => false,
        ), 
'choose_yehcen24' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-text-halftone-grid{ text-align: {{choose_yehcen24}}; }',
                ),
            ),
        ), 
'slider_baqn3q23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-stroke-yes{-webkit-text-stroke-width:{{slider_baqn3q23}};}',
                ),
            ),
        ), 
'typography_5991mw23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-text-halftone-effects .wkit-text-halftone-grid a, {{PLUS_WRAP}} .wkit-text-halftone-effects .texture-yes:after',
                ),
            ),
        ), 
'color_lx008a23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-text-halftone-grid a{color:{{color_lx008a23}};}',
                ),
            ),
        ), 
'color_on85zj23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}}  .wkit-stroke-yes{-webkit-text-stroke-color:{{color_on85zj23}};}',
                ),
            ),
        ), 
'textshadow_t1vf7623' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-shadow ',
                ),
            ),
        ), 
'slider_67d1ly23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .texture-yes:after{--halftonetopOffset:{{slider_67d1ly23}};}',
                ),
            ),
        ), 
'slider_j1hq5823' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .texture-yes:after{--halftoneleftOffset:{{slider_j1hq5823}};}',
                ),
            ),
        ), 
'background_peph5824' => array(
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
                    'selector' => '{{PLUS_WRAP}} .texture-yes:after',
                ),
            ),
        ), 

    
                ],
                'style'         => 'wbuilder-cgb-style_8cs3mu25-css',
                'editor_script' => 'wbuilder-cgb-block_zy1oiq25-js',
                'render_callback' => 'wkit_render_callback_khtz6e25'
            )
        );
    
    }
    add_action( 'init', 'wb_Text_Halftone_Effects_3s2fdi24' );
    
    function wkit_render_callback_khtz6e25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }