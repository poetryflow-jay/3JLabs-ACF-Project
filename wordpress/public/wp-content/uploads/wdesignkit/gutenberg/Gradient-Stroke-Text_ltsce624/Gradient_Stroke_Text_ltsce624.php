<?php
    function wb_Gradient_Stroke_Text_ltsce624() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            

            
        }

        wp_register_script('wbuilder-cgb-block_80eba725-js', WDKIT_SERVER_PATH .'/gutenberg/Gradient-Stroke-Text_ltsce624/Gradient_Stroke_Text_ltsce624.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.777969', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_1fyyxk25-css', WDKIT_SERVER_PATH .'/gutenberg/Gradient-Stroke-Text_ltsce624/Gradient_Stroke_Text_ltsce624.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.714762', false );
    
        register_block_type(
            'wdkit/wb-ltsce624', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'text_q6ba9v23' => array(
            'type' => 'string',
            'default' => 'Creative Innovation'
        ),
'select_kg2gpc23' => array(
            'type' => 'string',
            'default' => 'normal'
        ),
'select_4o5d2h23' => array(
            'type' => 'string',
            'default' => 'fill'
        ),
'choose_3tehqu23' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .grad-stroke-text-inner{ text-align: {{choose_3tehqu23}}; }',
                ),
            ),
        ), 
'slider_jkxnvz23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .grad-stroke-text{-webkit-text-stroke-width:{{slider_jkxnvz23}};}',
                ),
            ),
        ), 
'typography_9gk5vp23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .grad-stroke-text',
                ),
            ),
        ), 
'background_lfzu8j23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .grad-stroke-text',
                ),
            ),
        ), 
'color_tfmvtp23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .grad-as-stroke .grad-stroke-text{color:{{color_tfmvtp23}};}',
                ),
            ),
        ), 
'color_24l5lb23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .grad-as-fill .grad-stroke-text{-webkit-text-stroke-color:{{color_24l5lb23}};}',
                ),
            ),
        ), 
'textshadow_q8gik824' => array(
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
                    'selector' => '{{PLUS_WRAP}} .grad-stroke-text',
                ),
            ),
        ), 
'background_wogx9y23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .grad-stroke-text:hover',
                ),
            ),
        ), 
'color_0m1wt023' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .grad-as-stroke .grad-stroke-text:hover{color:{{color_0m1wt023}};}',
                ),
            ),
        ), 
'color_lgxnxq23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .grad-as-fill .grad-stroke-text:hover{-webkit-text-stroke-color:{{color_lgxnxq23}};}',
                ),
            ),
        ), 
'textshadow_74zolz24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .grad-stroke-text:hover',
                ),
            ),
        ), 

'color_90ky9123' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .text-gradient-animated .grad-stroke-text{--anicolor1:{{color_90ky9123}};}',
                ),
            ),
        ), 
'color_cohr5p23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .text-gradient-animated .grad-stroke-text{--anicolor2:{{color_cohr5p23}};}',
                ),
            ),
        ), 
'color_2rr6me23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .text-gradient-animated .grad-stroke-text{--anicolor3:{{color_2rr6me23}};}',
                ),
            ),
        ), 
'color_774bux23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .text-gradient-animated .grad-stroke-text{--anicolor4:{{color_774bux23}};}',
                ),
            ),
        ), 
'color_nbwwtw23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .text-gradient-animated .grad-stroke-text{--anicolor5:{{color_nbwwtw23}};}',
                ),
            ),
        ), 
'color_x7tag823' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .text-gradient-animated .grad-stroke-text:hover{--anicolor1:{{color_x7tag823}};}',
                ),
            ),
        ), 
'color_xygt9b23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .text-gradient-animated .grad-stroke-text:hover{--anicolor2:{{color_xygt9b23}};}',
                ),
            ),
        ), 
'color_7aaz9623' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .text-gradient-animated .grad-stroke-text:hover{--anicolor3:{{color_7aaz9623}};}',
                ),
            ),
        ), 
'color_m791k923' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .text-gradient-animated .grad-stroke-text:hover{--anicolor4:{{color_m791k923}};}',
                ),
            ),
        ), 
'color_btontx23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .text-gradient-animated .grad-stroke-text:hover{--anicolor5:{{color_btontx23}};}',
                ),
            ),
        ), 


    
                ],
                'style'         => 'wbuilder-cgb-style_1fyyxk25-css',
                'editor_script' => 'wbuilder-cgb-block_80eba725-js',
                'render_callback' => 'wkit_render_callback_4xbutk25'
            )
        );
    
    }
    add_action( 'init', 'wb_Gradient_Stroke_Text_ltsce624' );
    
    function wkit_render_callback_4xbutk25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }