<?php
    function wb_WhatsApp_Chat_opjpzt24() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_externalym94ms25-js", WDKIT_SERVER_PATH . '/gutenberg/WhatsApp-Chat_opjpzt24/index.js', [],'2.1.0.681585', true);}

            
        }

        wp_register_script('wbuilder-cgb-block_ym94ms25-js', WDKIT_SERVER_PATH .'/gutenberg/WhatsApp-Chat_opjpzt24/WhatsApp_Chat_opjpzt24.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.1.0.539589', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_03ylrm25-css', WDKIT_SERVER_PATH .'/gutenberg/WhatsApp-Chat_opjpzt24/WhatsApp_Chat_opjpzt24.css', is_admin() ? array( 'wp-editor' ) : null, '2.1.0.283374', false );
    
        register_block_type(
            'wdkit/wb-opjpzt24', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'text_vyi5pp23' => array(
            'type' => 'string',
            'default' => 'Need a Help?'
        ),
'select_l6nskb23' => array(
            'type' => 'string',
            'default' => 'none'
        ),
'select_7ixk5r25' => array(
            'type' => 'string',
            'default' => 'after'
        ),
'iconscontrol_0p5x3523' => array(
            'type' => 'string',
            'default'=> 'fab fa-whatsapp',
        ), 
'text_wiqdor23' => array(
            'type' => 'string',
            'default' => 'Posimyth'
        ),
'media_vq0g6123' => array(
            'type' => 'object',
            'default' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                'Id' => '',
            ),
        ), 
'iconscontrol_2n1gbw24' => array(
            'type' => 'string',
            'default'=> 'far fa-check-circle',
        ), 
'text_obeg2k23' => array(
            'type' => 'string',
            'default' => 'replies within a day'
        ),
'iconscontrol_m6x3ve24' => array(
            'type' => 'string',
            'default'=> 'far fa-clock',
        ), 
'textarea_q2tlj223' => array(
            'type' => 'string',
            'default' => 'Hi there 👋
How can I help you?',
        ),
'text_by1gvg23' => array(
            'type' => 'string',
            'default' => 'Start Chat'
        ),
'select_egyy2t23' => array(
            'type' => 'string',
            'default' => 'none'
        ),
'iconscontrol_4r22vh23' => array(
            'type' => 'string',
            'default'=> 'fas fa-external-link-alt',
        ), 
'text_wge8i023' => array(
            'type' => 'string',
            'default' => '911234567890'
        ),
'textarea_riravg23' => array(
            'type' => 'string',
            'default' => 'Its a fun place to connect and share.',
        ),
'switcher_xuo26v23' => array(
            'type' => 'boolean',
                'default' => true,
        ), 
'switcher_wvlbhb23' => array(
            'type' => 'boolean',
                'default' => true,
        ), 
'dimension_in5gfk24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-toggle-btn{ padding: {{dimension_in5gfk24}}}',
                ),
            ),
        ), 
'slider_lydd7p24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-toggle-btn{margin-top:{{slider_lydd7p24}};}',
                ),
            ),
        ), 
'typography_htd7qf23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-tgl-btn-text',
                ),
            ),
        ), 
'color_sy57cu23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-tgl-btn-text{color:{{color_sy57cu23}};}',
                ),
            ),
        ), 
'color_dk27qn23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-toggle-btn.dot-show-yes::before{background-color:{{color_dk27qn23}};}',
                ),
            ),
        ), 
'background_ecwnl223' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-toggle-btn',
                ),
            ),
        ), 
'border_5esc5f23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-toggle-btn',
                ),
            ),

        ), 
'dimension_dqp9qp23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-toggle-btn{ border-radius: {{dimension_dqp9qp23}}}',
                ),
            ),
        ), 
'boxshadow_cxjrq723' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-toggle-btn',
                    ),
                ),
            ), 
'color_5fw9ji23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-toggle-btn:hover .wkit-tgl-btn-text{color:{{color_5fw9ji23}};}',
                ),
            ),
        ), 
'color_ku18rf23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-toggle-btn.dot-show-yes:hover::before{background-color:{{color_ku18rf23}};}',
                ),
            ),
        ), 
'background_9ob81j23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-toggle-btn:hover',
                ),
            ),
        ), 
'border_47tsxy23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-toggle-btn:hover',
                ),
            ),

        ), 
'boxshadow_t7be2123' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-toggle-btn:hover',
                    ),
                ),
            ), 
'color_yr45co23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-toggle-btn.active .wkit-tgl-btn-text{color:{{color_yr45co23}};}',
                ),
            ),
        ), 
'color_d4iumi23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-toggle-btn.dot-show-yes.active::before{background-color:{{color_d4iumi23}};}',
                ),
            ),
        ), 
'background_7crk3423' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-toggle-btn.active',
                ),
            ),
        ), 
'border_3vpgwj23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-toggle-btn.active',
                ),
            ),

        ), 
'dimension_ny2hx423' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-toggle-btn.active{ border-radius: {{dimension_ny2hx423}}}',
                ),
            ),
        ), 
'boxshadow_qawmmw23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-toggle-btn.active',
                    ),
                ),
            ), 

'slider_veb44t23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-toggle-btn{gap:{{slider_veb44t23}};}',
                ),
            ),
        ), 
'slider_mw4nur23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-whatsapp-toggle .wkit-toggle-btn .tp-title-icon i{font-size:{{slider_mw4nur23}};}',
                ),
            ),
        ), 
'color_28idto23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-toggle-btn .tp-title-icon i{color:{{color_28idto23}};}',
                ),
            ),
        ), 
'color_c3w3xm23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-toggle-btn:hover .tp-title-icon i{color:{{color_c3w3xm23}};}',
                ),
            ),
        ), 
'color_7gudm823' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-toggle-btn.active .tp-title-icon i{color:{{color_7gudm823}};}',
                ),
            ),
        ), 

'background_fya9if23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-whatsapp-header',
                ),
            ),
        ), 
'slider_mxphtu23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .avatar-img-wrap{width:{{slider_mxphtu23}};}',
                ),
            ),
        ), 
'slider_2h080623' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .avatar-img-wrap{height:{{slider_2h080623}};}',
                ),
            ),
        ), 
'color_u45lua23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .header-avatar::before{background-color:{{color_u45lua23}};}',
                ),
            ),
        ), 
'border_k7nhoe23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .avatar-img-wrap',
                ),
            ),

        ), 
'boxshadow_uni09u23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .header-avatar',
                    ),
                ),
            ), 
'slider_qyqx1225' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .header-name{gap:{{slider_qyqx1225}};}',
                ),
            ),
        ), 
'slider_17yuoc25' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .header-content .header-name .tp-title-icon i{font-size:{{slider_17yuoc25}};}',
                ),
            ),
        ), 
'typography_xia4h723' => array(
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
                    'selector' => '{{PLUS_WRAP}} .header-name',
                ),
            ),
        ), 
'color_oraozg23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .header-name{color:{{color_oraozg23}};}',
                ),
            ),
        ), 
'color_i0o17225' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .header-name .tp-title-icon i{color:{{color_i0o17225}};}',
                ),
            ),
        ), 
'slider_ak69m625' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .header-time{gap:{{slider_ak69m625}};}',
                ),
            ),
        ), 
'slider_yzufd925' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .header-content .header-time .tp-title-icon i{font-size:{{slider_yzufd925}};}',
                ),
            ),
        ), 
'typography_mcrrug23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .header-time',
                ),
            ),
        ), 
'color_kawax723' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .header-time{color:{{color_kawax723}};}',
                ),
            ),
        ), 
'color_cbsu8v25' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .header-time .tp-title-icon i{color:{{color_cbsu8v25}};}',
                ),
            ),
        ), 
'heading_n4q47y23' => array(
            'type' => 'string',
        ), 
'typography_8np72823' => array(
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
                    'selector' => '{{PLUS_WRAP}} .author-name',
                ),
            ),
        ), 
'color_0wn6u123' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .author-name{color:{{color_0wn6u123}};}',
                ),
            ),
        ), 
'heading_oc81cg23' => array(
            'type' => 'string',
        ), 
'typography_oim9na23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .content-text',
                ),
            ),
        ), 
'color_u1dm9m23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .content-text{color:{{color_u1dm9m23}};}',
                ),
            ),
        ), 
'heading_9cq99223' => array(
            'type' => 'string',
        ), 
'typography_jw21k123' => array(
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
                    'selector' => '{{PLUS_WRAP}} .content-time',
                ),
            ),
        ), 
'color_lmmbec23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .content-time{color:{{color_lmmbec23}};}',
                ),
            ),
        ), 
'background_qjrdgj23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-whatsapp-body',
                ),
            ),
        ), 
'color_9odl9323' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .whatsapp-typing-loader{--wdotColor1:{{color_9odl9323}};}',
                ),
            ),
        ), 
'color_anbrrw23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .whatsapp-typing-loader{--wdotColor2:{{color_anbrrw23}};}',
                ),
            ),
        ), 
'heading_ygznk023' => array(
            'type' => 'string',
        ), 
'dimension_ofe1s523' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-wa-btn-wrap{ padding: {{dimension_ofe1s523}}}',
                ),
            ),
        ), 
'background_lpaz0623' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-wa-btn-wrap',
                ),
            ),
        ), 
'heading_tl42m423' => array(
            'type' => 'string',
        ), 
'dimension_kfsp3j24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-whatsapp-btn{ padding: {{dimension_kfsp3j24}}}',
                ),
            ),
        ), 
'typography_2uyqzo23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-whatsapp-btn span',
                ),
            ),
        ), 
'color_of6pef23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-whatsapp-btn .wa-btn-text{color:{{color_of6pef23}};}',
                ),
            ),
        ), 
'background_j8ky0523' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-whatsapp-btn',
                ),
            ),
        ), 
'border_um2xc923' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-whatsapp-btn',
                ),
            ),

        ), 
'dimension_nsvcp223' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-whatsapp-btn{ border-radius: {{dimension_nsvcp223}}}',
                ),
            ),
        ), 
'boxshadow_lwnprh23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-whatsapp-btn',
                    ),
                ),
            ), 
'color_s55e8823' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-whatsapp-btn:hover .wa-btn-text{color:{{color_s55e8823}};}',
                ),
            ),
        ), 
'background_lsqnsv23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-whatsapp-btn:hover',
                ),
            ),
        ), 
'border_v893xn23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-whatsapp-btn:hover',
                ),
            ),

        ), 
'boxshadow_pjwdj623' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-whatsapp-btn:hover',
                    ),
                ),
            ), 

'slider_32v3hk23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-whatsapp-btn{gap:{{slider_32v3hk23}};}',
                ),
            ),
        ), 
'slider_uh8ghe23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-whatsapp-chat .wkit-wa-btn-wrap .tp-title-icon i{font-size:{{slider_uh8ghe23}};}',
                ),
            ),
        ), 
'color_oag24j23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-whatsapp-btn .tp-title-icon i{color:{{color_oag24j23}};}',
                ),
            ),
        ), 
'color_egk1he23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-whatsapp-btn:hover .tp-title-icon i{color:{{color_egk1he23}};}',
                ),
            ),
        ), 

'choose_w125ax23' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-whatsapp-chat{ text-align: {{choose_w125ax23}}; }',
                ),
            ),
        ), 
'slider_cl7ey923' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-wa-inner-wrap{width:{{slider_cl7ey923}};}',
                ),
            ),
        ), 
'background_hqy35f24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-whatsapp-chat-inner',
                ),
            ),
        ), 
'border_bkdazs24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-whatsapp-chat-inner',
                ),
            ),

        ), 
'dimension_n36rc224' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-whatsapp-chat-inner{ border-radius: {{dimension_n36rc224}}}',
                ),
            ),
        ), 
'boxshadow_b3o1jt24' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-whatsapp-chat-inner',
                    ),
                ),
            ), 

    
                ],
                'style'         => 'wbuilder-cgb-style_03ylrm25-css',
                'editor_script' => 'wbuilder-cgb-block_ym94ms25-js',
                'render_callback' => 'wkit_render_callback_mhlfhn25'
            )
        );
    
    }
    add_action( 'init', 'wb_WhatsApp_Chat_opjpzt24' );
    
    function wkit_render_callback_mhlfhn25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }