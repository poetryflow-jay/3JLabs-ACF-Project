<?php
    function wb_Masked_Animated_Button_kxwujr25() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_externaluuaakx25-js", WDKIT_SERVER_PATH . '/gutenberg/Masked-Animated-Button_kxwujr25/index.js', [],'2.0.12.564103', true);}

            
        }

        wp_register_script('wbuilder-cgb-block_uuaakx25-js', WDKIT_SERVER_PATH .'/gutenberg/Masked-Animated-Button_kxwujr25/Masked_Animated_Button_kxwujr25.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.107753', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_ma9kk725-css', WDKIT_SERVER_PATH .'/gutenberg/Masked-Animated-Button_kxwujr25/Masked_Animated_Button_kxwujr25.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.661689', false );
    
        register_block_type(
            'wdkit/wb-kxwujr25', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'select_xmaghz24' => array(
            'type' => 'string',
            'default' => 'style-one'
        ),
'text_1djdcp24' => array(
            'type' => 'string',
            'default' => 'Click here'
        ),
'iconscontrol_u7r8i124' => array(
            'type' => 'string',
            'default'=> 'fas fa-chevron-right',
        ), 
'url_126azl24' => array(
            'type' => 'object',
            'default' => array(
                'url' => '#',
                'target' => true,
                'nofollow' => 'no-follow'
            ),

        ),
'choose_iz2kmq24' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wdk-masked-ani-btn{ justify-content: {{choose_iz2kmq24}}; }',
                ),
            ),
        ), 
'slider_70mq0c24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wdk-sm-icon{margin-left:{{slider_70mq0c24}};}',
                ),
            ),
        ), 
'switcher_eyhwvn25' => array(
            'type' => 'boolean',
                'default' => true,
        ), 
'switcher_4i20c625' => array(
            'type' => 'boolean',
                'default' => true,
        ), 
'dimension_xzzusu25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wdk-masbtn-cover .wdk-masked-btn{ padding: {{dimension_xzzusu25}}}',
                ),
            ),
        ), 
'typography_1qc22q24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wdk-masbtn-cover',
                ),
            ),
        ), 
'color_1dlare24' => array(
            'type' => 'string',
            'default' => '#ffffff',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wdk-masbtn-cover .wdk-masked-btn{color:{{color_1dlare24}};}',
                ),
            ),
        ), 
'background_sqm4pc25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wdk-masbtn-cover .wdk-masked-btn',
                ),
            ),
        ), 
'border_g06v8r24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wdk-masbtn-cover',
                ),
            ),

        ), 
'dimension_ow0d7m24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wdk-masbtn-cover{ border-radius: {{dimension_ow0d7m24}}}',
                ),
            ),
        ), 
'boxshadow_p6qi3a24' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wdk-masbtn-cover',
                    ),
                ),
            ), 
'color_j8zyk324' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wdk-masbtn-cover .wdk-mas{color:{{color_j8zyk324}};}',
                ),
            ),
        ), 
'background_yfmfll25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wdk-masbtn-cover .wdk-mas',
                ),
            ),
        ), 
'border_9fnemp24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wdk-masbtn-cover:hover',
                ),
            ),

        ), 
'boxshadow_l37yw224' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wdk-masbtn-cover:hover',
                    ),
                ),
            ), 

'slider_xqua3z25' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wdk-sm-icon{margin-left:{{slider_xqua3z25}};}',
                ),
            ),
        ), 
'slider_a4hsdi25' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wdk-masbtn-cover .wdk-masked-btn .tp-title-icon i, {{PLUS_WRAP}} .wdk-mas .tp-title-icon i{font-size:{{slider_a4hsdi25}};}',
                ),
            ),
        ), 
'color_lnjuje25' => array(
            'type' => 'string',
            'default' => '#ffffff',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wdk-masbtn-cover .wdk-masked-btn .tp-title-icon i{color:{{color_lnjuje25}};}',
                ),
            ),
        ), 
'color_2t50ys25' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wdk-masbtn-cover .wdk-mas .tp-title-icon i{color:{{color_2t50ys25}};}',
                ),
            ),
        ), 


    
                ],
                'style'         => 'wbuilder-cgb-style_ma9kk725-css',
                'editor_script' => 'wbuilder-cgb-block_uuaakx25-js',
                'render_callback' => 'wkit_render_callback_5y0dif25'
            )
        );
    
    }
    add_action( 'init', 'wb_Masked_Animated_Button_kxwujr25' );
    
    function wkit_render_callback_5y0dif25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }