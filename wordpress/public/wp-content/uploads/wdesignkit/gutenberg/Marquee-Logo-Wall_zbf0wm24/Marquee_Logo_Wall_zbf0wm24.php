<?php
    function wb_Marquee_Logo_Wall_zbf0wm24() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_externalppyel525-js", WDKIT_SERVER_PATH . '/gutenberg/Marquee-Logo-Wall_zbf0wm24/index.js', [],'2.0.12.249580', true);}

            
        }

        wp_register_script('wbuilder-cgb-block_ppyel525-js', WDKIT_SERVER_PATH .'/gutenberg/Marquee-Logo-Wall_zbf0wm24/Marquee_Logo_Wall_zbf0wm24.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.447479', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_ldv3cw25-css', WDKIT_SERVER_PATH .'/gutenberg/Marquee-Logo-Wall_zbf0wm24/Marquee_Logo_Wall_zbf0wm24.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.203422', false );
    
        register_block_type(
            'wdkit/wb-zbf0wm24', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'select_ow3fri23' => array(
            'type' => 'string',
            'default' => 'marq-hz'
        ),
'repeater_s5r6ra23' => array(
            'type' => 'array',
            'repeaterField' => array(
                (object) array(
                    'select_x3pc8523' => array(
            'type' => 'string',
            'default' => 'image'
        ),
'text_o31ruz24' => array(
            'type' => 'string',
            'default' => 'The Plus'
        ),
'iconscontrol_5z9ltq23' => array(
            'type' => 'string',
            'default'=> 'fas fa-home',
        ), 
'media_wviw9223' => array(
            'type' => 'object',
            'default' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                'Id' => '',
            ),
        ), 
'url_8i85qa23' => array(
            'type' => 'object',
            'default' => array(
                'url' => '#',
                'target' => true,
                'nofollow' => 'no-follow'
            ),

        ),
'heading_gft40r23' => array(
            'type' => 'string',
        ), 
'color_jl1b9o23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.wkit-marquee-icon-effect-txt  span{color:{{color_jl1b9o23}};}',
                ),
            ),
        ), 
'color_fd1smn24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.wkit-marquee-icon-effect-icon .tp-title-icon{color:{{color_fd1smn24}};}',
                ),
            ),
        ), 
'background_uqmkr523' => array(
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
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.wkit-marquee-item',
                ),
            ),
        ), 
'color_p2hzs023' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.wkit-marquee-item{border-color:{{color_p2hzs023}};}',
                ),
            ),
        ), 
'heading_q0hnbe23' => array(
            'type' => 'string',
        ), 
'color_c6gmps23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.wkit-marquee-icon-effect-txt:hover span{color:{{color_c6gmps23}};}',
                ),
            ),
        ), 
'color_1lbyxh24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.wkit-marquee-icon-effect-icon:hover .tp-title-icon{color:{{color_1lbyxh24}};}',
                ),
            ),
        ), 
'background_ow672r23' => array(
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
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.wkit-marquee-item:hover',
                ),
            ),
        ), 
'color_iazgb123' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.wkit-marquee-item:hover{border-color:{{color_iazgb123}};}',
                ),
            ),
        ), 

                )
            ),
            'default' => array(array(
 '_key' => '0', 
'select_x3pc8523' => 'image',
'text_o31ruz24' => esc_html__('The Plus', 'wdesignkit'), 
'iconscontrol_5z9ltq23' => 'fas fa-home', 
'media_wviw9223' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                'Id' => '',
            ),'url_8i85qa23' => array(
                    'url' => '#',
                    'target' => true,
                    'nofollow' => 'no-follow'
                ),'heading_gft40r23' => esc_html__('', 'wdesignkit'), 
'color_jl1b9o23' => '', 
'color_fd1smn24' => '', 
'background_uqmkr523' => '', 
'color_p2hzs023' => '', 
'heading_q0hnbe23' => esc_html__('', 'wdesignkit'), 
'color_c6gmps23' => '', 
'color_1lbyxh24' => '', 
'background_ow672r23' => '', 
'color_iazgb123' => '', 
),),
        ), 
'switcher_diik1b23' => array(
            'type' => 'boolean',
                'default' => false,
        ), 
'number_lgdjdh24' => array(
        'type' => 'string',
        'default' => '20',
        
    ),
'typography_alb1fr24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-marquee-icon-effect-txt',
                ),
            ),
        ), 
'color_8ksqga23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-marquee-icon-effect-txt{color:{{color_8ksqga23}};}',
                ),
            ),
        ), 
'color_nrpsjn23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-marquee-icon-effect-txt:hover{color:{{color_nrpsjn23}};}',
                ),
            ),
        ), 

'slider_coqhac24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-marquee-main-wrap .wkit-marquee-icon-effect-icon .tp-title-icon i{font-size:{{slider_coqhac24}};}',
                ),
            ),
        ), 
'color_rqb89w24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-marquee-main-wrap .wkit-marquee-icon-effect-icon .tp-title-icon i{color:{{color_rqb89w24}};}',
                ),
            ),
        ), 
'color_rfh6cw24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-marquee-main-wrap .wkit-marquee-icon-effect-icon:hover .tp-title-icon i{color:{{color_rfh6cw24}};}',
                ),
            ),
        ), 

'slider_fqfod723' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-marquee-main-wrap .wkit-marquee-icon-effect-image img{width:{{slider_fqfod723}};}',
                ),
            ),
        ), 
'slider_4iex6x25' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-marquee-main-wrap .wkit-marquee-icon-effect-image img{height:{{slider_4iex6x25}};}',
                ),
            ),
        ), 
'slider_hhss1p24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-marquee-main-wrap .wkit-marquee-item{width:{{slider_hhss1p24}};}',
                ),
            ),
        ), 
'slider_l6ijd623' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-marquee-main-wrap .wkit-marquee-item{height:{{slider_l6ijd623}};}',
                ),
            ),
        ), 
'slider_ych76024' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-marquee-main-wrap .wkit-marquee-group, {{PLUS_WRAP}} .wkit-marquee-inn-wrap{gap:{{slider_ych76024}};}',
                ),
            ),
        ), 
'background_hts4b323' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-marquee-main-wrap .wkit-marquee-item',
                ),
            ),
        ), 
'border_gq9ti623' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-marquee-main-wrap .wkit-marquee-item',
                ),
            ),

        ), 
'dimension_1jngxw23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-marquee-main-wrap .wkit-marquee-item{ border-radius: {{dimension_1jngxw23}}}',
                ),
            ),
        ), 
'boxshadow_kfql9o23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-marquee-main-wrap .wkit-marquee-item',
                    ),
                ),
            ), 
'background_b5ehh023' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-marquee-main-wrap .wkit-marquee-item:hover',
                ),
            ),
        ), 
'border_hvcmip23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-marquee-main-wrap .wkit-marquee-item:hover',
                ),
            ),

        ), 
'boxshadow_whvpqu23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-marquee-main-wrap .wkit-marquee-item:hover',
                    ),
                ),
            ), 

'slider_vt06g823' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-marquee-main-wrap{height:{{slider_vt06g823}};}',
                ),
            ),
        ), 

    
                ],
                'style'         => 'wbuilder-cgb-style_ldv3cw25-css',
                'editor_script' => 'wbuilder-cgb-block_ppyel525-js',
                'render_callback' => 'wkit_render_callback_ibpn1z25'
            )
        );
    
    }
    add_action( 'init', 'wb_Marquee_Logo_Wall_zbf0wm24' );
    
    function wkit_render_callback_ibpn1z25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }