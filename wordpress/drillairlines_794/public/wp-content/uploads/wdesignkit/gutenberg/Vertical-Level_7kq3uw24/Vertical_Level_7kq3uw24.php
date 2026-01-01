<?php
    function wb_Vertical_Level_7kq3uw24() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_externalp4jrhf25-js", WDKIT_SERVER_PATH . '/gutenberg/Vertical-Level_7kq3uw24/index.js', array('jquery'),'2.0.12.107474', true);}

            wp_enqueue_style( 'wd_css_ex_1_t32hum25', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css', false, '2.0.12.307075', 'all' );
wp_enqueue_style( 'wd_css_ex_1_q3ncyf25', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', false, '2.0.12.984737', 'all' );

        }

        wp_register_script('wbuilder-cgb-block_p4jrhf25-js', WDKIT_SERVER_PATH .'/gutenberg/Vertical-Level_7kq3uw24/Vertical_Level_7kq3uw24.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.625972', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_8mf7j325-css', WDKIT_SERVER_PATH .'/gutenberg/Vertical-Level_7kq3uw24/Vertical_Level_7kq3uw24.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.409105', false );
    
        register_block_type(
            'wdkit/wb-7kq3uw24', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'select_nhr9qd23' => array(
            'type' => 'string',
            'default' => 'style-1'
        ),
'repeater_8rn0w223' => array(
            'type' => 'array',
            'repeaterField' => array(
                (object) array(
                    'select_ej7gme23' => array(
            'type' => 'string',
            'default' => 'text'
        ),
'text_vx4ivy23' => array(
            'type' => 'string',
            'default' => 'Acquire'
        ),
'iconscontrol_e0tko023' => array(
            'type' => 'string',
            'default'=> 'fas fa-home',
        ), 
'media_8061n023' => array(
            'type' => 'object',
            'default' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                'Id' => '',
            ),
        ), 
'wysiwyg_4wbmqg23' => array(
            'type' => 'string',
            'default' => 'Discover all available features in WDesignKit .',
        ),
'switcher_lryedf23' => array(
            'type' => 'boolean',
                'default' => false,
        ), 
'heading_ur4f3t23' => array(
            'type' => 'string',
        ), 
'color_2ynyfv23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper {{TP_REPEAT_ID}} .wkit-vl-step-title{color:{{color_2ynyfv23}};}',
                ),
            ),
        ), 
'color_lrsetc23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper {{TP_REPEAT_ID}} .wkit-vl-progress-icon{color:{{color_lrsetc23}};}',
                ),
            ),
        ), 
'color_xp4j5c23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper {{TP_REPEAT_ID}} .wkit-vl-step-desc{color:{{color_xp4j5c23}};}',
                ),
            ),
        ), 
'heading_iypfma23' => array(
            'type' => 'string',
        ), 
'color_jhnu2423' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper {{TP_REPEAT_ID}}:hover .wkit-vl-step-title{color:{{color_jhnu2423}};}',
                ),
            ),
        ), 
'color_c38r5j23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper {{TP_REPEAT_ID}}:hover .wkit-vl-progress-icon{color:{{color_c38r5j23}};}',
                ),
            ),
        ), 
'color_a1phqu23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper {{TP_REPEAT_ID}}:hover .wkit-vl-step-desc{color:{{color_a1phqu23}};}',
                ),
            ),
        ), 
'color_iyhbs023' => array(
            'type' => 'string',
            'default' => '#808080',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .wkit-vl-pix-levels-dot{background-color:{{color_iyhbs023}};}',
                ),
            ),
        ), 
'color_sxy5ra25' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper {{TP_REPEAT_ID}}.wkit-vl-active-yes .wkit-vl-progress-icon .tp-title-icon{color:{{color_sxy5ra25}};}',
                ),
            ),
        ), 
'color_r50bao23' => array(
            'type' => 'string',
            'default' => '#1818e8',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.wkit-vl-active-yes .wkit-vl-pix-levels-dot{background-color:{{color_r50bao23}};}',
                ),
            ),
        ), 
'color_0hfwjr23' => array(
            'type' => 'string',
            'default' => '#000000',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .wkit-vl-pix-levels-dot-inner{background-color:{{color_0hfwjr23}};}',
                ),
            ),
        ), 
'color_pobie923' => array(
            'type' => 'string',
            'default' => '#ffffff',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.wkit-vl-active-yes .wkit-vl-pix-levels-dot-inner{background-color:{{color_pobie923}};}',
                ),
            ),
        ), 
'color_fzwbk523' => array(
            'type' => 'string',
            'default' => '#808080',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .wkit-bg-gray-2,{{TP_REPEAT_ID}} .wkit-vl-bg-gradient-primary{background-color:{{color_fzwbk523}};}',
                ),
            ),
        ), 
'color_jl0z0m23' => array(
            'type' => 'string',
            'default' => '#1818e8',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.wkit-vl-active-yes .wkit-bg-gray-2,{{TP_REPEAT_ID}}.wkit-vl-active-yes .wkit-vl-bg-gradient-primary{background-color:{{color_jl0z0m23}};}',
                ),
            ),
        ), 

                )
            ),
            'default' => array(array(
 '_key' => '0', 
'select_ej7gme23' => 'text',
'text_vx4ivy23' => esc_html__('Acquire', 'wdesignkit'), 
'iconscontrol_e0tko023' => 'fas fa-home', 
'media_8061n023' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                'Id' => '',
            ),'wysiwyg_4wbmqg23' => esc_html__('Discover all available features in WDesignKit .', 'wdesignkit'), 
'switcher_lryedf23' => false,
'heading_ur4f3t23' => esc_html__('', 'wdesignkit'), 
'color_2ynyfv23' => '', 
'color_lrsetc23' => '', 
'color_xp4j5c23' => '', 
'heading_iypfma23' => esc_html__('', 'wdesignkit'), 
'color_jhnu2423' => '', 
'color_c38r5j23' => '', 
'color_a1phqu23' => '', 
'color_iyhbs023' => '#808080', 
'color_sxy5ra25' => '', 
'color_r50bao23' => '#1818e8', 
'color_0hfwjr23' => '#000000', 
'color_pobie923' => '#ffffff', 
'color_fzwbk523' => '#808080', 
'color_jl0z0m23' => '#1818e8', 
),),
        ), 
'slider_b5wt1p24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => '%',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-vl-step-content.wkit-vl-pix-levels-step{opacity:{{slider_b5wt1p24}};}',
                ),
            ),
        ), 
'slider_spv9a524' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => '%',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-step-content.wkit-vl-pix-levels-step.wkit-vl-active-yes{opacity:{{slider_spv9a524}};}',
                ),
            ),
        ), 

'dimension_5chiij23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-step-title{ padding: {{dimension_5chiij23}}}',
                ),
            ),
        ), 
'dimension_ev1anw23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-step-title{ margin: {{dimension_ev1anw23}}}',
                ),
            ),
        ), 
'typography_o0oh3t23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-step-title',
                ),
            ),
        ), 
'color_9y0up723' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-text .wkit-vl-step-title{color:{{color_9y0up723}};}',
                ),
            ),
        ), 
'color_chjm5523' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-step-title:hover{color:{{color_chjm5523}};}',
                ),
            ),
        ), 
'color_ulpqga23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-active-yes .wkit-vl-step-title{color:{{color_ulpqga23}};}',
                ),
            ),
        ), 

'dimension_vtvtbr23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-step-desc{ padding: {{dimension_vtvtbr23}}}',
                ),
            ),
        ), 
'dimension_51azh123' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-step-desc{ margin: {{dimension_51azh123}}}',
                ),
            ),
        ), 
'typography_s8eoip23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-step-desc',
                ),
            ),
        ), 
'color_1eyj9i23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-step-desc{color:{{color_1eyj9i23}};}',
                ),
            ),
        ), 
'color_ffmmbb23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-step-desc:hover{color:{{color_ffmmbb23}};}',
                ),
            ),
        ), 
'color_67p52023' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-active-yes .wkit-vl-step-desc{color:{{color_67p52023}};}',
                ),
            ),
        ), 

'slider_4f9ksm23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-step-content .wkit-vl-progress-icon i{font-size:{{slider_4f9ksm23}};}',
                ),
            ),
        ), 
'color_l9hrrk23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-step-content .wkit-vl-progress-icon i{color:{{color_l9hrrk23}};}',
                ),
            ),
        ), 
'color_wwnfak23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-step-content:hover .wkit-vl-progress-icon i{color:{{color_wwnfak23}};}',
                ),
            ),
        ), 

'slider_17rycx23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-step-content .wkit-vl-progress-img{width:{{slider_17rycx23}};}',
                ),
            ),
        ), 
'slider_tdgput25' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-step-content.wkit-vl-image .wkit-vl-progress-img .tp-vl-title-image{width:{{slider_tdgput25}};}',
                ),
            ),
        ), 
'slider_bsl6pe23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-step-content.wkit-vl-image .wkit-vl-progress-img .tp-vl-title-image{height:{{slider_bsl6pe23}};}',
                ),
            ),
        ), 
'slider_2mmjo823' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-step-content.wkit-vl-image .wkit-vl-progress-img .tp-vl-title-image{margin-bottom:{{slider_2mmjo823}};}',
                ),
            ),
        ), 
'slider_humxqr24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-pix-levels-dot{width:{{slider_humxqr24}};}',
                ),
            ),
        ), 
'slider_44n3jp24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-pix-levels-dot{height:{{slider_44n3jp24}};}',
                ),
            ),
        ), 
'slider_cl6adz24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-pix-levels-dot-inner{width:{{slider_cl6adz24}};}',
                ),
            ),
        ), 
'slider_ngxjcy24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-pix-levels-dot-inner{height:{{slider_ngxjcy24}};}',
                ),
            ),
        ), 
'slider_5l0r9k24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-pix-levels-dot-inner {top:{{slider_5l0r9k24}};}',
                ),
            ),
        ), 
'slider_hiw30v24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-pix-levels-dot-inner {left:{{slider_hiw30v24}};}',
                ),
            ),
        ), 
'heading_80ntvk23' => array(
            'type' => 'string',
        ), 
'color_1q7id323' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-pix-levels-dot{background-color:{{color_1q7id323}};}',
                ),
            ),
        ), 
'heading_z2n3ui23' => array(
            'type' => 'string',
        ), 
'color_av4gsa23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-pix-levels-dot-inner{background-color:{{color_av4gsa23}};}',
                ),
            ),
        ), 
'heading_d0oggu23' => array(
            'type' => 'string',
        ), 
'color_xqa3oq23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-active-yes .wkit-vl-pix-levels-dot{background-color:{{color_xqa3oq23}};}',
                ),
            ),
        ), 
'heading_huci9g23' => array(
            'type' => 'string',
        ), 
'color_tag0qi23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-active-yes .wkit-vl-pix-levels-dot-inner{background-color:{{color_tag0qi23}};}',
                ),
            ),
        ), 

'slider_j8anjs23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-inner-style-2 .wkit-prg-two{font-size:{{slider_j8anjs23}};}',
                ),
            ),
        ), 
'slider_24zrck25' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-inner-style-2 .wkit-prg-two{top:{{slider_24zrck25}};}',
                ),
            ),
        ), 
'color_p0839w23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-inner-style-2 .wkit-prg-two{color:{{color_p0839w23}};}',
                ),
            ),
        ), 
'color_0fsle923' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-inner-style-2 .wkit-vl-step-content:hover .wkit-prg-two, .wkit-multi-step-vl-wrapper .wkit-vl-inner-style-2 wkit-vl-step-content.wkit-vl-active-yes:hover .wkit-prg-two{color:{{color_0fsle923}};}',
                ),
            ),
        ), 
'color_qgd3pi23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-inner-style-2 .wkit-vl-active-yes .wkit-prg-two{color:{{color_qgd3pi23}};}',
                ),
            ),
        ), 

'slider_zasg0323' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-inner-style-3 .wkit-vl-pix-levels-dot-inner.wkit-vl-check-mark:before{font-size:{{slider_zasg0323}};}',
                ),
            ),
        ), 
'color_yn7qqw23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-inner-style-3 .wkit-vl-pix-levels-dot-inner.wkit-vl-check-mark:before{color:{{color_yn7qqw23}};}',
                ),
            ),
        ), 
'color_vkzjwf23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-inner-style-3 .wkit-vl-pix-levels-dot-inner.wkit-vl-check-mark:hover:before{color:{{color_vkzjwf23}};}',
                ),
            ),
        ), 

'slider_jy7s4h24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => 8,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-pix-levels-step .progress{height:{{slider_jy7s4h24}};}',
                ),
            ),
        ), 
'color_3vcprb23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-bg-gray-2,.wkit-multi-step-vl-wrapper .wkit-vl-bg-gradient-primary{background-color:{{color_3vcprb23}};}',
                ),
            ),
        ), 
'color_7hl08c25' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-active-yes .wkit-bg-gray-2,{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-active-yes .wkit-vl-bg-gradient-primary{background-color:{{color_7hl08c25}};}',
                ),
            ),
        ), 

'dimension_h90zss23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-content-bg{ padding: {{dimension_h90zss23}}}',
                ),
            ),
        ), 
'dimension_z0q74o23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-content-bg{ margin: {{dimension_z0q74o23}}}',
                ),
            ),
        ), 
'background_z81eny23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-step-content',
                ),
            ),
        ), 
'border_asyv9l23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-content-bg',
                ),
            ),

        ), 
'dimension_ql5iwo23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-content-bg{ border-radius: {{dimension_ql5iwo23}}}',
                ),
            ),
        ), 
'boxshadow_waz3yx23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper .wkit-vl-content-bg',
                    ),
                ),
            ), 
'background_tna86r23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper:hover .wkit-vl-step-content',
                ),
            ),
        ), 
'border_idqltp23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper:hover .wkit-vl-content-bg',
                ),
            ),

        ), 
'boxshadow_8vghll23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-multi-step-vl-wrapper:hover .wkit-vl-content-bg',
                    ),
                ),
            ), 


    
                ],
                'style'         => 'wbuilder-cgb-style_8mf7j325-css',
                'editor_script' => 'wbuilder-cgb-block_p4jrhf25-js',
                'render_callback' => 'wkit_render_callback_qy6hdl25'
            )
        );
    
    }
    add_action( 'init', 'wb_Vertical_Level_7kq3uw24' );
    
    function wkit_render_callback_qy6hdl25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }