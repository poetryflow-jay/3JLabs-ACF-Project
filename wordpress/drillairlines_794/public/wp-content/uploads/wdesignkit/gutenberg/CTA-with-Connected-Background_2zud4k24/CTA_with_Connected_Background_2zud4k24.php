<?php
    function wb_CTA_with_Connected_Background_2zud4k24() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_externalhtyw0o25-js", WDKIT_SERVER_PATH . '/gutenberg/CTA-with-Connected-Background_2zud4k24/index.js', [],'2.0.12.429260', true);}

            
        }

        wp_register_script('wbuilder-cgb-block_htyw0o25-js', WDKIT_SERVER_PATH .'/gutenberg/CTA-with-Connected-Background_2zud4k24/CTA_with_Connected_Background_2zud4k24.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.895183', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_4k8nte25-css', WDKIT_SERVER_PATH .'/gutenberg/CTA-with-Connected-Background_2zud4k24/CTA_with_Connected_Background_2zud4k24.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.797637', false );
    
        register_block_type(
            'wdkit/wb-2zud4k24', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'select_j13meb23' => array(
            'type' => 'string',
            'default' => 'horizontal'
        ),
'repeater_lcaihe23' => array(
            'type' => 'array',
            'repeaterField' => array(
                (object) array(
                    'text_bqwp3l23' => array(
            'type' => 'string',
            'default' => 'Web Development'
        ),
'textarea_o2vj9h23' => array(
            'type' => 'string',
            'default' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed eiusmod tempor labore.',
        ),
'text_memb1g23' => array(
            'type' => 'string',
            'default' => 'Read More'
        ),
'iconscontrol_y1qbkp25' => array(
            'type' => 'string',
            'default'=> 'fas fa-arrow-right',
        ), 
'url_3hvh9k23' => array(
            'type' => 'object',
            'default' => array(
                'url' => '#',
                'target' => true,
                'nofollow' => 'no-follow'
            ),

        ),
'media_uy9yec23' => array(
            'type' => 'object',
            'default' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                'Id' => '',
            ),
        ), 

                )
            ),
            'default' => array(array(
 '_key' => '0', 
'text_bqwp3l23' => esc_html__('Web Development', 'wdesignkit'), 
'textarea_o2vj9h23' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed eiusmod tempor labore.', 'wdesignkit'), 
'text_memb1g23' => esc_html__('Read More', 'wdesignkit'), 
'iconscontrol_y1qbkp25' => 'fas fa-arrow-right', 
'url_3hvh9k23' => array(
                    'url' => '#',
                    'target' => true,
                    'nofollow' => 'no-follow'
                ),'media_uy9yec23' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                'Id' => '',
            ),),),
        ), 
'choose_2ngt9h25' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .con-image-item .con-item-content{ text-align: {{choose_2ngt9h25}}; }',
                ),
            ),
        ), 
'switcher_xuuqzy23' => array(
            'type' => 'boolean',
                'default' => false,
        ), 
'slider_x47myl23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .con-image-item .con-item-content{--titleoffset:{{slider_x47myl23}};}',
                ),
            ),
        ), 
'typography_htjkbt23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .con-item-content .con-i-title',
                ),
            ),
        ), 
'color_wzqdjg23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .con-item-content .con-i-title{color:{{color_wzqdjg23}};}',
                ),
            ),
        ), 
'slider_b78ag323' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .con-item-content .con-i-desc{margin-top:{{slider_b78ag323}};}',
                ),
            ),
        ), 
'slider_sgeyni23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .con-item-content .con-i-desc{margin-bottom:{{slider_sgeyni23}};}',
                ),
            ),
        ), 
'typography_2ukchn23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .con-item-content .con-i-desc',
                ),
            ),
        ), 
'color_eb55o323' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .con-item-content .con-i-desc{color:{{color_eb55o323}};}',
                ),
            ),
        ), 
'dimension_xgiapc23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .con-item-content .con-i-btn{ padding: {{dimension_xgiapc23}}}',
                ),
            ),
        ), 
'typography_nrxkq123' => array(
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
                    'selector' => '{{PLUS_WRAP}} .con-item-content .con-i-btn',
                ),
            ),
        ), 
'color_gu26ox23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .con-item-content .con-i-btn{color:{{color_gu26ox23}};}',
                ),
            ),
        ), 
'background_s96nkm23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .con-item-content .con-i-btn',
                ),
            ),
        ), 
'border_imnep823' => array(
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
                    'selector' => '{{PLUS_WRAP}} .con-item-content .con-i-btn',
                ),
            ),

        ), 
'dimension_s6ajgn23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .con-item-content .con-i-btn{ border-radius: {{dimension_s6ajgn23}}}',
                ),
            ),
        ), 
'boxshadow_h63ojg23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .con-item-content .con-i-btn',
                    ),
                ),
            ), 
'background_yfdhom23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .con-item-content .con-i-btn:hover',
                ),
            ),
        ), 
'border_1cjwo623' => array(
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
                    'selector' => '{{PLUS_WRAP}} .con-item-content .con-i-btn:hover',
                ),
            ),

        ), 
'boxshadow_y1gqz223' => array(
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
                        'selector' => '{{PLUS_WRAP}} .con-item-content .con-i-btn:hover',
                    ),
                ),
            ), 

'slider_8w867j25' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .con-item-content .con-i-btn{gap:{{slider_8w867j25}};}',
                ),
            ),
        ), 
'slider_o25dha25' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .con-item-content .con-i-btn .tp-title-icon i{font-size:{{slider_o25dha25}};}',
                ),
            ),
        ), 
'color_h8wxun25' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .con-item-content .con-i-btn .tp-title-icon i{color:{{color_h8wxun25}};}',
                ),
            ),
        ), 
'color_tv4uk525' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .con-item-content .con-i-btn:hover .tp-title-icon i{color:{{color_tv4uk525}};}',
                ),
            ),
        ), 

'slider_mczafc23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .cta-with-bg-inner .con-image-item{--bgheight:{{slider_mczafc23}};}',
                ),
            ),
        ), 
'color_rc7mqv23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .con-image-item{background-color:{{color_rc7mqv23}};}',
                ),
            ),
        ), 
'heading_v2fk9n25' => array(
            'type' => 'string',
        ), 
'slider_mqqn4p23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .cta-with-bg-inner .con-image-item:not(:last-child){--bdwidth:{{slider_mqqn4p23}};}',
                ),
            ),
        ), 
'color_d1zd0g23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .cta-with-bg-inner .con-image-item:not(:last-child){--bdcolor:{{color_d1zd0g23}};}',
                ),
            ),
        ), 
'dimension_0xtgn424' => array(
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
                    'selector' => '{{PLUS_WRAP}} .cta-with-bg-inner{ border-radius: {{dimension_0xtgn424}}}',
                ),
            ),
        ), 
'cssfilter_anjuwm23' => array(
            'type' => 'object',
            'default' => array(
                'openFilter' => false,
                'blur' => 0,
                'brightness' => 100,
                'contrast' => 100,
                'saturate' => 100,
                'hue' => 0,
            ),
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .con-image-background',
                ),
            ),
        ), 

    
                ],
                'style'         => 'wbuilder-cgb-style_4k8nte25-css',
                'editor_script' => 'wbuilder-cgb-block_htyw0o25-js',
                'render_callback' => 'wkit_render_callback_nbfwfk25'
            )
        );
    
    }
    add_action( 'init', 'wb_CTA_with_Connected_Background_2zud4k24' );
    
    function wkit_render_callback_nbfwfk25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }