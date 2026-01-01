<?php
    function wb_Text_3D_Effect_wo5xzf24() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_external03wc4925-js", WDKIT_SERVER_PATH . '/gutenberg/Text-3D-Effect_wo5xzf24/index.js', array('jquery'),'2.0.12.706660', true);}

            
        }

        wp_register_script('wbuilder-cgb-block_03wc4925-js', WDKIT_SERVER_PATH .'/gutenberg/Text-3D-Effect_wo5xzf24/Text_3D_Effect_wo5xzf24.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.570654', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_hii64m25-css', WDKIT_SERVER_PATH .'/gutenberg/Text-3D-Effect_wo5xzf24/Text_3D_Effect_wo5xzf24.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.111417', false );
    
        register_block_type(
            'wdkit/wb-wo5xzf24', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'text_l92fen23' => array(
            'type' => 'string',
            'default' => 'The Plus Addons for Elementor'
        ),
'switcher_g098ts23' => array(
            'type' => 'boolean',
                'default' => false,
        ), 
'number_42n98423' => array(
        'type' => 'string',
        'default' => '3',
        
    ),
'choose_nvemg123' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-text-3d-effect{ text-align: {{choose_nvemg123}}; }',
                ),
            ),
        ), 
'typography_l2r7c323' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-text-3d-effect .w-later-span',
                ),
            ),
        ), 
'color_eflma823' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}}  .wkit-text-3d-effect .w-3d-layer .w-later-span{color:{{color_eflma823}};}',
                ),
            ),
        ), 
'textshadow_wiuxx023' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-text-3d-effect .w-later-span',
                ),
            ),
        ), 
'color_krvpi223' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-3d-stage:hover .w-later-span{color:{{color_krvpi223}};}',
                ),
            ),
        ), 
'textshadow_po7j0u23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-3d-stage:hover .w-later-span',
                ),
            ),
        ), 

'slider_4zwpyc23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .w-3d-layer:nth-child(n+11) span{-webkit-text-stroke-width:{{slider_4zwpyc23}};}',
                ),
            ),
        ), 
'slider_ifwopm25' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .w-3d-layer:nth-child(n+12) span{-webkit-text-stroke-width:{{slider_ifwopm25}};}',
                ),
            ),
        ), 
'color_7bou7i23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .w-3d-layer:nth-child(n+11) span{-webkit-text-stroke-color:{{color_7bou7i23}};}',
                ),
            ),
        ), 
'color_xzwt8b23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .w-3d-layer:nth-child(n+12) span{-webkit-text-stroke-color:{{color_xzwt8b23}};}',
                ),
            ),
        ), 
'color_5zpfhk23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-3d-stage:hover .w-3d-layer:nth-child(n+11) span{-webkit-text-stroke-color:{{color_5zpfhk23}};}',
                ),
            ),
        ), 
'color_w1vo3623' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-3d-stage:hover .w-3d-layer:nth-child(n+12) span{-webkit-text-stroke-color:{{color_w1vo3623}};}',
                ),
            ),
        ), 

'slider_d3gpc223' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'deg',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .w-3d-layer{--wkitTextRotateX:{{slider_d3gpc223}};}',
                ),
            ),
        ), 
'slider_kw0d5e23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'deg',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .w-3d-layer{--wkitTextRotateY:{{slider_kw0d5e23}};}',
                ),
            ),
        ), 
'slider_ytfzfy23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'deg',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-3d-stage:hover .w-3d-layer{--wkitTextRotateX:{{slider_ytfzfy23}};}',
                ),
            ),
        ), 
'slider_ey3pkf23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'deg',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-3d-stage:hover .w-3d-layer{--wkitTextRotateY:{{slider_ey3pkf23}};}',
                ),
            ),
        ), 


    
                ],
                'style'         => 'wbuilder-cgb-style_hii64m25-css',
                'editor_script' => 'wbuilder-cgb-block_03wc4925-js',
                'render_callback' => 'wkit_render_callback_qwtyis25'
            )
        );
    
    }
    add_action( 'init', 'wb_Text_3D_Effect_wo5xzf24' );
    
    function wkit_render_callback_qwtyis25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }