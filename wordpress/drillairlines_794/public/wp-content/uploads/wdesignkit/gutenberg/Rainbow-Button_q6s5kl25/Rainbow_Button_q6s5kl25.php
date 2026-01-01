<?php
    function wb_Rainbow_Button_q6s5kl25() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            

            
        }

        wp_register_script('wbuilder-cgb-block_eiwth125-js', WDKIT_SERVER_PATH .'/gutenberg/Rainbow-Button_q6s5kl25/Rainbow_Button_q6s5kl25.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.831052', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_lx6emr25-css', WDKIT_SERVER_PATH .'/gutenberg/Rainbow-Button_q6s5kl25/Rainbow_Button_q6s5kl25.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.296233', false );
    
        register_block_type(
            'wdkit/wb-q6s5kl25', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'text_rzhtdf24' => array(
            'type' => 'string',
            'default' => 'Click Here'
        ),
'url_ch78tn24' => array(
            'type' => 'object',
            'default' => array(
                'url' => '#',
                'target' => true,
                'nofollow' => 'no-follow'
            ),

        ),
'iconscontrol_nmhs8g24' => array(
            'type' => 'string',
            'default'=> 'fas fa-chevron-right',
        ), 
'choose_w61zfl24' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-rbtn-container{ justify-content: {{choose_w61zfl24}}; }',
                ),
            ),
        ), 
'dimension_9bzkio24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-rbtn-container .wkit-rbtn{ padding: {{dimension_9bzkio24}}}',
                ),
            ),
        ), 
'typography_p25eoi24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-rbtn-container .wkit-rbtn',
                ),
            ),
        ), 
'color_sd14kl24' => array(
            'type' => 'string',
            'default' => '#ffffff',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-rbtn-container .wkit-rbtn{color:{{color_sd14kl24}};}',
                ),
            ),
        ), 
'background_22dzru24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-rbtn-container .wkit-rbtn',
                ),
            ),
        ), 
'border_z9p0ji24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-rbtn-container .wkit-rbtn',
                ),
            ),

        ), 
'dimension_nt5fto24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-rbtn-container .wkit-rbtn{ border-radius: {{dimension_nt5fto24}}}',
                ),
            ),
        ), 
'boxshadow_7fs8rj24' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-rbtn-container .wkit-rbtn',
                    ),
                ),
            ), 
'color_yy1dlm24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-rbtn-container .wkit-rbtn:hover{color:{{color_yy1dlm24}};}',
                ),
            ),
        ), 
'background_owycnd24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-rbtn-container .wkit-rbtn:hover',
                ),
            ),
        ), 
'border_v0b6cq24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-rbtn-container .wkit-rbtn:hover',
                ),
            ),

        ), 
'boxshadow_lsjokn24' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-rbtn-container .wkit-rbtn:hover',
                    ),
                ),
            ), 

'slider_5jfm5325' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-rbtn-container .wkit-rbtn{gap:{{slider_5jfm5325}};}',
                ),
            ),
        ), 
'slider_1ww9yc24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-rbtn-container .wkit-rainbow-btn-icon .tp-title-icon i{font-size:{{slider_1ww9yc24}};}',
                ),
            ),
        ), 
'color_40dqfl25' => array(
            'type' => 'string',
            'default' => '#ffffff',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-rbtn-container .wkit-rbtn .tp-title-icon i{color:{{color_40dqfl25}};}',
                ),
            ),
        ), 
'color_ma8l3625' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-rbtn-container .wkit-rbtn:hover .tp-title-icon i{color:{{color_ma8l3625}};}',
                ),
            ),
        ), 


    
                ],
                'style'         => 'wbuilder-cgb-style_lx6emr25-css',
                'editor_script' => 'wbuilder-cgb-block_eiwth125-js',
                'render_callback' => 'wkit_render_callback_qucfl325'
            )
        );
    
    }
    add_action( 'init', 'wb_Rainbow_Button_q6s5kl25' );
    
    function wkit_render_callback_qucfl325($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }