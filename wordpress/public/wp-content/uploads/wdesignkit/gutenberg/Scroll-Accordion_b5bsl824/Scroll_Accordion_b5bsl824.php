<?php
    function wb_Scroll_Accordion_b5bsl824() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_externals7hf6n25-js", WDKIT_SERVER_PATH . '/gutenberg/Scroll-Accordion_b5bsl824/index.js', [],'2.0.12.533443', true);}

            
        }

        wp_register_script('wbuilder-cgb-block_s7hf6n25-js', WDKIT_SERVER_PATH .'/gutenberg/Scroll-Accordion_b5bsl824/Scroll_Accordion_b5bsl824.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.418301', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_dfi0mk25-css', WDKIT_SERVER_PATH .'/gutenberg/Scroll-Accordion_b5bsl824/Scroll_Accordion_b5bsl824.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.408025', false );
    
        register_block_type(
            'wdkit/wb-b5bsl824', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'text_sr5arl24' => array(
            'type' => 'string',
            'default' => 'The Plus'
        ),
'select_uvzud924' => array(
            'type' => 'string',
            'default' => 'style-1'
        ),
'repeater_82xaam24' => array(
            'type' => 'array',
            'repeaterField' => array(
                (object) array(
                    'text_ir03rf24' => array(
            'type' => 'string',
            'default' => 'The Plus'
        ),
'textarea_w4jlfm24' => array(
            'type' => 'string',
            'default' => 'The process of managing a brand begins with analyzing how it is currently perceived in the market, then plan how the brand should be perceived to achieve its objectives, and then ensure that the brand is perceived as intended and achieves its objectives.',
        ),
'media_9vreus24' => array(
            'type' => 'object',
            'default' => array(
                'url' => '',
                'Id' => '',
            ),
        ), 
'text_wztag624' => array(
            'type' => 'string',
            'default' => 'Click Here'
        ),
'url_id69sx24' => array(
            'type' => 'object',
            'default' => array(
                'url' => '#',
                'target' => true,
                'nofollow' => 'no-follow'
            ),

        ),
'heading_e5z66j24' => array(
            'type' => 'string',
        ), 
'heading_441ttj24' => array(
            'type' => 'string',
        ), 
'color_a7q9fd24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .wkit-scroll-accord-content .wkit-scroll-accord-title .accord-title{color:{{color_a7q9fd24}};}',
                ),
            ),
        ), 
'heading_6vrit624' => array(
            'type' => 'string',
        ), 
'color_ikh7vi24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .wkit-scroll-accord-content:hover .wkit-scroll-accord-title .accord-title{color:{{color_ikh7vi24}};}',
                ),
            ),
        ), 
'heading_c3kuxj24' => array(
            'type' => 'string',
        ), 
'heading_w3zdgr24' => array(
            'type' => 'string',
        ), 
'color_l547ii24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .wkit-scroll-accord-content .wkit-scroll-accord-desc .accord-desc{color:{{color_l547ii24}};}',
                ),
            ),
        ), 
'heading_78n9ht24' => array(
            'type' => 'string',
        ), 
'color_3d7hig24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .wkit-scroll-accord-content:hover .wkit-scroll-accord-desc .accord-desc{color:{{color_3d7hig24}};}',
                ),
            ),
        ), 
'heading_k1mdsl24' => array(
            'type' => 'string',
        ), 
'heading_n15t1524' => array(
            'type' => 'string',
        ), 
'color_booq9a24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .wkit-scroll-accord-content .wkit-scroll-accord-desc .wkit-scroll-accord-btn{color:{{color_booq9a24}};}',
                ),
            ),
        ), 
'background_ut3i3724' => array(
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
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .wkit-scroll-accord-content .wkit-scroll-accord-desc .wkit-scroll-accord-btn',
                ),
            ),
        ), 
'heading_bxyvsj24' => array(
            'type' => 'string',
        ), 
'color_hmcucp24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .wkit-scroll-accord-content .wkit-scroll-accord-desc .wkit-scroll-accord-btn:hover{color:{{color_hmcucp24}};}',
                ),
            ),
        ), 
'background_nxkkq324' => array(
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
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .wkit-scroll-accord-content .wkit-scroll-accord-desc .wkit-scroll-accord-btn:hover',
                ),
            ),
        ), 
'heading_npukp724' => array(
            'type' => 'string',
        ), 
'background_nb5afc24' => array(
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
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.wkit-scroll-accord-slide',
                ),
            ),
        ), 
'dimension_fwhbmq24' => array(
            'type' => 'object',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.wkit-scroll-accord-slide{ border-radius: {{dimension_fwhbmq24}}}',
                ),
            ),
        ), 

                )
            ),
            'default' => array(array(
 '_key' => '0', 
'text_ir03rf24' => esc_html__('The Plus', 'wdesignkit'), 
'textarea_w4jlfm24' => esc_html__('The process of managing a brand begins with analyzing how it is currently perceived in the market, then plan how the brand should be perceived to achieve its objectives, and then ensure that the brand is perceived as intended and achieves its objectives.', 'wdesignkit'), 
'media_9vreus24' => array(
                'url' => '',
                'Id' => '',
            ),'text_wztag624' => esc_html__('Click Here', 'wdesignkit'), 
'url_id69sx24' => array(
                    'url' => '#',
                    'target' => true,
                    'nofollow' => 'no-follow'
                ),'heading_e5z66j24' => esc_html__('', 'wdesignkit'), 
'heading_441ttj24' => esc_html__('', 'wdesignkit'), 
'color_a7q9fd24' => '', 
'heading_6vrit624' => esc_html__('', 'wdesignkit'), 
'color_ikh7vi24' => '', 
'heading_c3kuxj24' => esc_html__('', 'wdesignkit'), 
'heading_w3zdgr24' => esc_html__('', 'wdesignkit'), 
'color_l547ii24' => '', 
'heading_78n9ht24' => esc_html__('', 'wdesignkit'), 
'color_3d7hig24' => '', 
'heading_k1mdsl24' => esc_html__('', 'wdesignkit'), 
'heading_n15t1524' => esc_html__('', 'wdesignkit'), 
'color_booq9a24' => '', 
'background_ut3i3724' => '', 
'heading_bxyvsj24' => esc_html__('', 'wdesignkit'), 
'color_hmcucp24' => '', 
'background_nxkkq324' => '', 
'heading_npukp724' => esc_html__('', 'wdesignkit'), 
'background_nb5afc24' => '', 
'dimension_fwhbmq24' => '',
),),
        ), 
'select_g0b8j924' => array(
            'type' => 'string',
            'default' => 'style-1'
        ),
'choose_f3twee24' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-main-title .scroll-main-title{ justify-content: {{choose_f3twee24}}; }',
                ),
            ),
        ), 
'dimension_5tx2gj24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-main-title{ padding: {{dimension_5tx2gj24}}}',
                ),
            ),
        ), 
'dimension_6ri5z524' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-main-title{ margin: {{dimension_6ri5z524}}}',
                ),
            ),
        ), 
'typography_5cclro24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-main-title .scroll-main-title',
                ),
            ),
        ), 
'color_2oxujw24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-main-title .scroll-main-title{color:{{color_2oxujw24}};}',
                ),
            ),
        ), 
'background_c8jnih24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-main-title',
                ),
            ),
        ), 
'color_e9tuk824' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-main-title:hover .scroll-main-title{color:{{color_e9tuk824}};}',
                ),
            ),
        ), 
'background_jzrf0e24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-main-title:hover',
                ),
            ),
        ), 

'choose_jkx7ef24' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-content.scroll-cnt-style-2 .wkit-scroll-accord-title .accord-title{ justify-content: {{choose_jkx7ef24}}; }',
                ),
            ),
        ), 
'dimension_8cz5tn24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-content .wkit-scroll-accord-title{ padding: {{dimension_8cz5tn24}}}',
                ),
            ),
        ), 
'dimension_bfidii24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-content .wkit-scroll-accord-title .accord-title{ margin: {{dimension_bfidii24}}}',
                ),
            ),
        ), 
'typography_uzq28724' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-content .wkit-scroll-accord-title .accord-title',
                ),
            ),
        ), 
'color_n8do6w24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-content .wkit-scroll-accord-title .accord-title{color:{{color_n8do6w24}};}',
                ),
            ),
        ), 
'color_e6thi224' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-content:hover .wkit-scroll-accord-title .accord-title{color:{{color_e6thi224}};}',
                ),
            ),
        ), 

'dimension_syvcdy24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-img .accord-img{ padding: {{dimension_syvcdy24}}}',
                ),
            ),
        ), 
'dimension_88a3l424' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-img .accord-img{ margin: {{dimension_88a3l424}}}',
                ),
            ),
        ), 
'slider_dadjzn24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => '%',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-content .wkit-scroll-accord-img .accord-img{width:{{slider_dadjzn24}};}',
                ),
            ),
        ), 
'slider_61vm4524' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => '%',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-content .wkit-scroll-accord-img .accord-img{height:{{slider_61vm4524}};}',
                ),
            ),
        ), 
'border_nhzam524' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-img .accord-img',
                ),
            ),

        ), 
'dimension_tbkhcb24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-img .accord-img{ border-radius: {{dimension_tbkhcb24}}}',
                ),
            ),
        ), 
'border_lfv4px24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-img .accord-img:hover',
                ),
            ),

        ), 
'dimension_y6h5yw24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-img .accord-img:hover{ border-radius: {{dimension_y6h5yw24}}}',
                ),
            ),
        ), 

'choose_96mpyw24' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-content.scroll-cnt-style-2 .wkit-scroll-accord-desc{ justify-content: {{choose_96mpyw24}}; }',
                ),
            ),
        ), 
'dimension_dcgp7u24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-content .wkit-scroll-accord-desc{ padding: {{dimension_dcgp7u24}}}',
                ),
            ),
        ), 
'dimension_c08m6824' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-content .wkit-scroll-accord-desc{ margin: {{dimension_c08m6824}}}',
                ),
            ),
        ), 
'typography_qcwze924' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-content .wkit-scroll-accord-desc .accord-desc',
                ),
            ),
        ), 
'color_nl61id24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-content .wkit-scroll-accord-desc .accord-desc{color:{{color_nl61id24}};}',
                ),
            ),
        ), 
'color_zk1n9524' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-content:hover .wkit-scroll-accord-desc .accord-desc{color:{{color_zk1n9524}};}',
                ),
            ),
        ), 

'choose_50ax2524' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-content .wkit-scroll-accord-desc .wkit-scroll-accord-btn{ justify-content: {{choose_50ax2524}}; }',
                ),
            ),
        ), 
'dimension_opn0d924' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-btn{ padding: {{dimension_opn0d924}}}',
                ),
            ),
        ), 
'slider_lry7lr24' => array(
           'type' => 'string',
                'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-btn{margin-top:{{slider_lry7lr24}};}',
                ),
            ),
        ), 
'typography_lhzh4l24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-btn',
                ),
            ),
        ), 
'color_cgwrf324' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-btn{color:{{color_cgwrf324}};}',
                ),
            ),
        ), 
'background_0a5udy24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-btn',
                ),
            ),
        ), 
'border_rny3ed24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-btn',
                ),
            ),

        ), 
'dimension_7j8ntw24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-btn{ border-radius: {{dimension_7j8ntw24}}}',
                ),
            ),
        ), 
'boxshadow_7id4y524' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-btn',
                    ),
                ),
            ), 
'color_zbg5cx24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-btn:hover{color:{{color_zbg5cx24}};}',
                ),
            ),
        ), 
'background_8jayu424' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-btn:hover',
                ),
            ),
        ), 
'border_6zvy5c24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-btn:hover',
                ),
            ),

        ), 
'dimension_0dbqon24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-btn:hover{ border-radius: {{dimension_0dbqon24}}}',
                ),
            ),
        ), 
'boxshadow_76sgz524' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-btn:hover',
                    ),
                ),
            ), 

'dimension_f753p524' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-slide{ padding: {{dimension_f753p524}}}',
                ),
            ),
        ), 
'dimension_rhsk2l24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-slide{ margin: {{dimension_rhsk2l24}}}',
                ),
            ),
        ), 
'background_soejiz24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-slide',
                ),
            ),
        ), 
'border_nlojk324' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-slide',
                ),
            ),

        ), 
'dimension_lqn2id24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-slide{ border-radius: {{dimension_lqn2id24}}}',
                ),
            ),
        ), 
'boxshadow_xw2d9u24' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper .wkit-scroll-accord-slide',
                    ),
                ),
            ), 
'background_pxirnm24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper:hover .wkit-scroll-accord-slide',
                ),
            ),
        ), 
'border_qre92y24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper:hover .wkit-scroll-accord-slide',
                ),
            ),

        ), 
'dimension_7asgy324' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper:hover .wkit-scroll-accord-slide{ border-radius: {{dimension_7asgy324}}}',
                ),
            ),
        ), 
'boxshadow_8s5pc224' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-scroll-accord-wrapper:hover .wkit-scroll-accord-slide',
                    ),
                ),
            ), 


    
                ],
                'style'         => 'wbuilder-cgb-style_dfi0mk25-css',
                'editor_script' => 'wbuilder-cgb-block_s7hf6n25-js',
                'render_callback' => 'wkit_render_callback_rpm2pb25'
            )
        );
    
    }
    add_action( 'init', 'wb_Scroll_Accordion_b5bsl824' );
    
    function wkit_render_callback_rpm2pb25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }