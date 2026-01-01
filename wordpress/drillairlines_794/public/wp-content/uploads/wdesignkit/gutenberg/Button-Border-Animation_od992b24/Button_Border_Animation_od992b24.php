<?php
    function wb_Button_Border_Animation_od992b24() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            

            
        }

        wp_register_script('wbuilder-cgb-block_cu8bdj25-js', WDKIT_SERVER_PATH .'/gutenberg/Button-Border-Animation_od992b24/Button_Border_Animation_od992b24.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.903763', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_6g3r6g25-css', WDKIT_SERVER_PATH .'/gutenberg/Button-Border-Animation_od992b24/Button_Border_Animation_od992b24.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.495518', false );
    
        register_block_type(
            'wdkit/wb-od992b24', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'text_adwel223' => array(
            'type' => 'string',
            'default' => 'Get Started'
        ),
'iconscontrol_e6jy4m24' => array(
            'type' => 'string',
            'default'=> 'fas fa-chevron-right',
        ), 
'url_i6584224' => array(
            'type' => 'object',
            'default' => array(
                'url' => '#',
                'target' => true,
                'nofollow' => 'no-follow'
            ),

        ),
'choose_hxfi6x24' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-button-border-animation-wrapper{  justify-content: {{choose_hxfi6x24}}; }',
                ),
            ),
        ), 
'dimension_bl6pma24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-button-border-animation{ padding: {{dimension_bl6pma24}}}',
                ),
            ),
        ), 
'typography_ja5d8223' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-button-border-animation',
                ),
            ),
        ), 
'slider_n68kl624' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-button-border-animation-inner{padding:{{slider_n68kl624}};}',
                ),
            ),
        ), 
'color_znix5z23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-button-border-animation .wkit-button-border-text{color:{{color_znix5z23}};}',
                ),
            ),
        ), 
'background_zufe5323' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-button-border-animation',
                ),
            ),
        ), 
'color_gg9hk724' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-button-border-animation-inner{background:{{color_gg9hk724}};}',
                ),
            ),
        ), 
'dimension_lf0egz24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-button-border-animation, {{PLUS_WRAP}} .wkit-button-border-animation-inner{ border-radius: {{dimension_lf0egz24}}}',
                ),
            ),
        ), 
'boxshadow_cmt2cy25' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-button-border-animation',
                    ),
                ),
            ), 
'color_z7jnsw23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-button-border-animation:hover .wkit-button-border-text{color:{{color_z7jnsw23}};}',
                ),
            ),
        ), 
'background_y5u0h423' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-button-border-animation:hover',
                ),
            ),
        ), 
'color_odugzw24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-button-border-animation-inner:hover{background:{{color_odugzw24}};}',
                ),
            ),
        ), 
'boxshadow_me7bcy25' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-button-border-animation:hover',
                    ),
                ),
            ), 

'slider_z9nuar25' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-button-border-animation {gap:{{slider_z9nuar25}};}',
                ),
            ),
        ), 
'slider_oujvhj24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-border-icon .tp-title-icon i{font-size:{{slider_oujvhj24}};}',
                ),
            ),
        ), 
'color_1l3ax925' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-button-border-animation .wkit-border-icon .tp-title-icon i{color:{{color_1l3ax925}};}',
                ),
            ),
        ), 
'color_9xrf6i25' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-button-border-animation:hover .wkit-border-icon .tp-title-icon i{color:{{color_9xrf6i25}};}',
                ),
            ),
        ), 


    
                ],
                'style'         => 'wbuilder-cgb-style_6g3r6g25-css',
                'editor_script' => 'wbuilder-cgb-block_cu8bdj25-js',
                'render_callback' => 'wkit_render_callback_9ejopc25'
            )
        );
    
    }
    add_action( 'init', 'wb_Button_Border_Animation_od992b24' );
    
    function wkit_render_callback_9ejopc25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }