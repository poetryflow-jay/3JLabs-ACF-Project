<?php
    function wb_Button_Hover_Effect_7c373q24() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_externalgx0sq225-js", WDKIT_SERVER_PATH . '/gutenberg/Button-Hover-Effect_7c373q24/index.js', [],'2.0.12.10909', true);}

            
        }

        wp_register_script('wbuilder-cgb-block_gx0sq225-js', WDKIT_SERVER_PATH .'/gutenberg/Button-Hover-Effect_7c373q24/Button_Hover_Effect_7c373q24.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.174720', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_781sol25-css', WDKIT_SERVER_PATH .'/gutenberg/Button-Hover-Effect_7c373q24/Button_Hover_Effect_7c373q24.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.465630', false );
    
        register_block_type(
            'wdkit/wb-7c373q24', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'select_b4b4v923' => array(
            'type' => 'string',
            'default' => 'jumping'
        ),
'text_r41uaa23' => array(
            'type' => 'string',
            'default' => 'Click'
        ),
'iconscontrol_3dtya523' => array(
            'type' => 'string',
            'default'=> 'fas fa-chevron-right',
        ), 
'url_5lwf3f24' => array(
            'type' => 'object',
            'default' => array(
                'url' => '#',
                'target' => true,
                'nofollow' => 'no-follow'
            ),

        ),
'select_3081sf23' => array(
            'type' => 'string',
            'default' => 'after-text'
        ),
'switcher_log9ol25' => array(
            'type' => 'boolean',
                'default' => true,
        ), 
'switcher_bpu64g25' => array(
            'type' => 'boolean',
                'default' => true,
        ), 
'choose_e2qcqg24' => array(
            'type' => 'string',
                'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-button-h-effect{ justify-content: {{choose_e2qcqg24}}; }',
                ),
            ),
        ), 
'dimension_d066om23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-button-h-effect .wkit-button-effect{ padding: {{dimension_d066om23}}}',
                ),
            ),
        ), 
'typography_272b2923' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-button-h-effect .wkit-button-effect',
                ),
            ),
        ), 
'color_dvvn0y23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-button-h-effect .wkit-button-effect{color:{{color_dvvn0y23}};}',
                ),
            ),
        ), 
'background_9hyvsf23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-button-h-effect .wkit-button-effect',
                ),
            ),
        ), 
'border_7ovaie23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-button-h-effect .wkit-button-effect',
                ),
            ),

        ), 
'dimension_5l8cq223' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-button-h-effect .wkit-button-effect{ border-radius: {{dimension_5l8cq223}}}',
                ),
            ),
        ), 
'boxshadow_5r75hr23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-button-h-effect .wkit-button-effect',
                    ),
                ),
            ), 
'color_ut160v23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-button-h-effect .wkit-button-effect:hover{color:{{color_ut160v23}};}',
                ),
            ),
        ), 
'background_k470jq23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-button-h-effect .wkit-button-effect:hover',
                ),
            ),
        ), 
'border_0hahn623' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-button-h-effect .wkit-button-effect:hover',
                ),
            ),

        ), 
'boxshadow_cr8hb323' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-button-h-effect .wkit-button-effect:hover',
                    ),
                ),
            ), 

'slider_0tox5e25' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-button-h-effect .wkit-button-effect{gap:{{slider_0tox5e25}};}',
                ),
            ),
        ), 
'slider_hiu54w25' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-button-h-effect .tp-title-icon i{font-size:{{slider_hiu54w25}};}',
                ),
            ),
        ), 
'color_38lybz23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-button-h-effect .tp-title-icon i{color:{{color_38lybz23}};}',
                ),
            ),
        ), 
'color_e5m6p623' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-button-h-effect .wkit-button-effect:hover .tp-title-icon i{color:{{color_e5m6p623}};}',
                ),
            ),
        ), 


    
                ],
                'style'         => 'wbuilder-cgb-style_781sol25-css',
                'editor_script' => 'wbuilder-cgb-block_gx0sq225-js',
                'render_callback' => 'wkit_render_callback_afoxym25'
            )
        );
    
    }
    add_action( 'init', 'wb_Button_Hover_Effect_7c373q24' );
    
    function wkit_render_callback_afoxym25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }