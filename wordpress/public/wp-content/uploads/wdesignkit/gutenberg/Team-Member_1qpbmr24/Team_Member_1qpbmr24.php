<?php
    function wb_Team_Member_1qpbmr24() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_externalctv3zq25-js", WDKIT_SERVER_PATH . '/gutenberg/Team-Member_1qpbmr24/index.js', array('jquery'),'2.0.12.611704', true);}

            
        }

        wp_register_script('wbuilder-cgb-block_ctv3zq25-js', WDKIT_SERVER_PATH .'/gutenberg/Team-Member_1qpbmr24/Team_Member_1qpbmr24.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.210065', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_x063x625-css', WDKIT_SERVER_PATH .'/gutenberg/Team-Member_1qpbmr24/Team_Member_1qpbmr24.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.456531', false );
    
        register_block_type(
            'wdkit/wb-1qpbmr24', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'text_l1jn6223' => array(
            'type' => 'string',
            'default' => 'Posimyth Innovation'
        ),
'switcher_ojei9t24' => array(
            'type' => 'boolean',
                'default' => false,
        ), 
'url_ceyz7y24' => array(
            'type' => 'object',
            'default' => array(
                'url' => '#',
                'target' => true,
                'nofollow' => 'no-follow'
            ),

        ),
'text_zmblu923' => array(
            'type' => 'string',
            'default' => 'Product based Company'
        ),
'media_32xtty23' => array(
            'type' => 'object',
            'default' => array(
                'url' => '',
                'Id' => '',
            ),
        ), 
'repeater_9eeglb23' => array(
            'type' => 'array',
            'repeaterField' => array(
                (object) array(
                    'text_0lehe723' => array(
            'type' => 'string',
            'default' => 'How is Feeling In Company?'
        ),
'text_b4fs5123' => array(
            'type' => 'string',
            'default' => 'Very Nice Environment'
        ),

                )
            ),
            'default' => array(array(
 '_key' => '0', 
'text_0lehe723' => esc_html__('How is Feeling In Company?', 'wdesignkit'), 
'text_b4fs5123' => esc_html__('Very Nice Environment', 'wdesignkit'), 
),),
        ), 
'iconscontrol_pe6nkm23' => array(
            'type' => 'string',
            'default'=> 'fas fa-arrow-right',
        ), 
'number_k7clr924' => array(
        'type' => 'string',
        'default' => '1',
        
    ),
'heading_tdn66z23' => array(
            'type' => 'string',
        ), 
'typography_8ltefq23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .team-member-second-info-title',
                ),
            ),
        ), 
'color_kyc5g323' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .team-member-second-info-title{color:{{color_kyc5g323}};}',
                ),
            ),
        ), 
'heading_n9z4ln23' => array(
            'type' => 'string',
        ), 
'typography_cwat2k23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .team-member-third-info-designation',
                ),
            ),
        ), 
'color_1tl0m823' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .team-member-third-info-designation{color:{{color_1tl0m823}};}',
                ),
            ),
        ), 
'heading_98fbik23' => array(
            'type' => 'string',
        ), 
'typography_txa9ln23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .team-member-third-question',
                ),
            ),
        ), 
'color_xxy5e023' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .team-member-third-question{color:{{color_xxy5e023}};}',
                ),
            ),
        ), 
'heading_fkmicb23' => array(
            'type' => 'string',
        ), 
'typography_wpm3b723' => array(
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
                    'selector' => '{{PLUS_WRAP}} .team-member-third-answer',
                ),
            ),
        ), 
'color_pvhcpj23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .team-member-third-answer{color:{{color_pvhcpj23}};}',
                ),
            ),
        ), 
'border_qsgvnb24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .team-memeber-first .team-member-first-circle',
                ),
            ),

        ), 
'color_rm0flj24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .circle-one{background-color:{{color_rm0flj24}};}',
                ),
            ),
        ), 
'color_ilkqhl24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .circle-two{background-color:{{color_ilkqhl24}};}',
                ),
            ),
        ), 
'color_rfy9t924' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .circle-three{background-color:{{color_rfy9t924}};}',
                ),
            ),
        ), 
'choose_oqjasf24' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-team-member{ justify-content: {{choose_oqjasf24}}; }',
                ),
            ),
        ), 
'slider_2gsaq423' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-team-member-inner{width:{{slider_2gsaq423}};}',
                ),
            ),
        ), 
'heading_90s51623' => array(
            'type' => 'string',
        ), 
'dimension_omxavm23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .team-memeber-first{ padding: {{dimension_omxavm23}}}',
                ),
            ),
        ), 
'heading_khgtni23' => array(
            'type' => 'string',
        ), 
'slider_sytywj24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .team-member-second-info-open-close{font-size:{{slider_sytywj24}};}',
                ),
            ),
        ), 
'color_tyn54v24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .team-member-second-info-open-close{color:{{color_tyn54v24}};}',
                ),
            ),
        ), 
'dimension_nne6w723' => array(
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
                    'selector' => '{{PLUS_WRAP}} .team-member-second-info{ padding: {{dimension_nne6w723}}}',
                ),
            ),
        ), 
'cssfilter_lbjzf423' => array(
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
                    'selector' => '{{PLUS_WRAP}} .team-member-second .team-memeber-second-inner',
                ),
            ),
        ), 
'border_blx8sh23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-team-member-inner',
                ),
            ),

        ), 
'dimension_17qn4523' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-team-member-inner{ border-radius: {{dimension_17qn4523}}}',
                ),
            ),
        ), 
'background_n59vra23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .team-member-second .team-memeber-second-inner',
                ),
            ),
        ), 
'boxshadow_g8h72p23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-team-member-inner',
                    ),
                ),
            ), 
'cssfilter_v0pz0r23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-team-member-inner:hover .team-memeber-second-inner',
                ),
            ),
        ), 
'border_mezyq423' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-team-member-inner:hover',
                ),
            ),

        ), 
'dimension_udi1yb23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-team-member-inner:hover{ border-radius: {{dimension_udi1yb23}}}',
                ),
            ),
        ), 
'background_ljmsu923' => array(
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
                    'selector' => '{{PLUS_WRAP}} .team-member-second:hover .team-memeber-second-inner',
                ),
            ),
        ), 
'boxshadow_kj13ph23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-team-member-inner:hover',
                    ),
                ),
            ), 

'heading_u4aj9m23' => array(
            'type' => 'string',
        ), 
'choose_e4wkhj24' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .team-member-third.active  .team-member-third-interest{ text-align: {{choose_e4wkhj24}}; }',
                ),
            ),
        ), 
'dimension_39xrvj23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .team-member-third{ padding: {{dimension_39xrvj23}}}',
                ),
            ),
        ), 
'heading_ddahaj24' => array(
            'type' => 'string',
        ), 
'slider_1jfk7k24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .team-member-third-info-arrow{font-size:{{slider_1jfk7k24}};}',
                ),
            ),
        ), 
'color_38m0go24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .team-member-third-info-arrow{color:{{color_38m0go24}};}',
                ),
            ),
        ), 

    
                ],
                'style'         => 'wbuilder-cgb-style_x063x625-css',
                'editor_script' => 'wbuilder-cgb-block_ctv3zq25-js',
                'render_callback' => 'wkit_render_callback_2z05qn25'
            )
        );
    
    }
    add_action( 'init', 'wb_Team_Member_1qpbmr24' );
    
    function wkit_render_callback_2z05qn25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }