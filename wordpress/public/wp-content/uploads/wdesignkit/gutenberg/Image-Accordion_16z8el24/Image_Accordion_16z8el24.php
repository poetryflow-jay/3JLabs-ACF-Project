<?php
    function wb_Image_Accordion_16z8el24() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            wp_enqueue_script( 'wd_ex_script_sz97sq25', 'https://cdnjs.cloudflare.com/ajax/libs/splidejs/4.1.4/js/splide.min.js', [], '2.0.12.521274', true );

            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_externalbiiunf25-js", WDKIT_SERVER_PATH . '/gutenberg/Image-Accordion_16z8el24/index.js', array('jquery'),'2.0.12.699100', true);}

            wp_enqueue_style( 'wd_css_ex_1_scmwsn25', 'https://cdnjs.cloudflare.com/ajax/libs/splidejs/4.1.4/css/splide.min.css', false, '2.0.12.658531', 'all' );

        }

        wp_register_script('wbuilder-cgb-block_biiunf25-js', WDKIT_SERVER_PATH .'/gutenberg/Image-Accordion_16z8el24/Image_Accordion_16z8el24.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.890781', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_202zfm25-css', WDKIT_SERVER_PATH .'/gutenberg/Image-Accordion_16z8el24/Image_Accordion_16z8el24.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.511867', false );
    
        register_block_type(
            'wdkit/wb-16z8el24', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'repeater_5soxy925' => array(
            'type' => 'array',
            'repeaterField' => array(
                (object) array(
                    'text_zp6ggl25' => array(
            'type' => 'string',
            'default' => 'Games'
        ),
'textarea_nli4fl25' => array(
            'type' => 'string',
            'default' => 'The game is a sequel to Defense of the
          Ancients, which was a community-created mod for Blizzard Entertainment Warcraft III.',
        ),
'media_r33bn225' => array(
            'type' => 'object',
            'default' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                'Id' => '',
            ),
        ), 
'color_kenc8y25' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .wkit-img-accord-item .wkit-img-accord-item-title{color:{{color_kenc8y25}};}',
                ),
            ),
        ), 
'color_v9ifi625' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .wkit-img-accord-item:hover .wkit-img-accord-item-title{color:{{color_v9ifi625}};}',
                ),
            ),
        ), 
'color_on9fie25' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .wkit-img-accord-item .wkit-img-accord-item-desc{color:{{color_on9fie25}};}',
                ),
            ),
        ), 
'color_m8tsz925' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .wkit-img-accord-item:hover .wkit-img-accord-item-desc{color:{{color_m8tsz925}};}',
                ),
            ),
        ), 
'heading_3pg6p825' => array(
            'type' => 'string',
        ), 
'background_pg495j25' => array(
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
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .wkit-img-accord-item .wkit-img-accord-items',
                ),
            ),
        ), 
'background_773ajn25' => array(
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
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .wkit-img-accord-item .wkit-img-accord-items:hover',
                ),
            ),
        ), 
'color_balhl625' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .wkit-img-accord-item .wkit-img-accord-items{border-color:{{color_balhl625}};}',
                ),
            ),
        ), 
'color_shybie25' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .wkit-img-accord-item .wkit-img-accord-items:hover{border-color:{{color_shybie25}};}',
                ),
            ),
        ), 
'slider_u5qjpo25' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .wkit-img-accord-item .wkit-img-accord-item-title{bottom:{{slider_u5qjpo25}};}',
                ),
            ),
        ), 

                )
            ),
            'default' => array(array(
 '_key' => '0', 
'text_zp6ggl25' => esc_html__('Games', 'wdesignkit'), 
'textarea_nli4fl25' => esc_html__('The game is a sequel to Defense of the
          Ancients, which was a community-created mod for Blizzard Entertainment Warcraft III.', 'wdesignkit'), 
'media_r33bn225' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                'Id' => '',
            ),'color_kenc8y25' => '', 
'color_v9ifi625' => '', 
'color_on9fie25' => '', 
'color_m8tsz925' => '', 
'heading_3pg6p825' => esc_html__('', 'wdesignkit'), 
'background_pg495j25' => '', 
'background_773ajn25' => '', 
'color_balhl625' => '', 
'color_shybie25' => '', 
'slider_u5qjpo25' => '',
),),
        ), 
'switcher_ndy3n025' => array(
            'type' => 'boolean',
                'default' => false,
        ), 
'choose_bif8k123' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item-title{ justify-content: {{choose_bif8k123}}; }',
                ),
            ),
        ), 
'dimension_233f5323' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item .wkit-img-accord-item-title{ padding: {{dimension_233f5323}}}',
                ),
            ),
        ), 
'typography_1h3ige23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item .wkit-img-accord-item-title',
                ),
            ),
        ), 
'color_n20mja23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item:not(.wkit-item-active) .wkit-img-accord-item-title{color:{{color_n20mja23}};}',
                ),
            ),
        ), 
'background_8ogj2523' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item:not(.wkit-item-active) .wkit-img-accord-item-title',
                ),
            ),
        ), 
'border_zktpcq25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item:not(.wkit-item-active) .wkit-img-accord-item-title',
                ),
            ),

        ), 
'dimension_9bnmu323' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item:not(.wkit-item-active) .wkit-img-accord-item-title{ border-radius: {{dimension_9bnmu323}}}',
                ),
            ),
        ), 
'boxshadow_cxzbii23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item:not(.wkit-item-active) .wkit-img-accord-item-title',
                    ),
                ),
            ), 
'color_3c9xeb23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item:hover .wkit-img-accord-item-title{color:{{color_3c9xeb23}};}',
                ),
            ),
        ), 
'background_mx4zmv23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item:hover .wkit-img-accord-item-title',
                ),
            ),
        ), 
'border_o3pr2p25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item:hover .wkit-img-accord-item-title',
                ),
            ),

        ), 
'dimension_lkfruq23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item:hover .wkit-img-accord-item-title{ border-radius: {{dimension_lkfruq23}}}',
                ),
            ),
        ), 
'boxshadow_53icu923' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item:hover .wkit-img-accord-item-title',
                    ),
                ),
            ), 
'color_3o5j1323' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item.wkit-item-active .wkit-img-accord-item-title{color:{{color_3o5j1323}};}',
                ),
            ),
        ), 
'background_pyssax23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item.wkit-item-active .wkit-img-accord-item-title',
                ),
            ),
        ), 
'border_bigkmu25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item.wkit-item-active .wkit-img-accord-item-title',
                ),
            ),

        ), 
'dimension_86eja723' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item.wkit-item-active .wkit-img-accord-item-title{ border-radius: {{dimension_86eja723}}}',
                ),
            ),
        ), 
'boxshadow_lkm4x623' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item.wkit-item-active .wkit-img-accord-item-title',
                    ),
                ),
            ), 

'choose_3z6knn23' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item.wkit-item-active .wkit-img-accord-items .wkit-img-accord-item-desc{ justify-content: {{choose_3z6knn23}}; }',
                ),
            ),
        ), 
'dimension_b0h9jc23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item .wkit-img-accord-item-desc{ padding: {{dimension_b0h9jc23}}}',
                ),
            ),
        ), 
'typography_vzlmnv23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item .wkit-img-accord-item-desc',
                ),
            ),
        ), 
'color_680ioy23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item .wkit-img-accord-item-desc{color:{{color_680ioy23}};}',
                ),
            ),
        ), 
'background_k6fbox23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item .wkit-img-accord-item-desc',
                ),
            ),
        ), 
'border_zhnveg25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item .wkit-img-accord-item-desc',
                ),
            ),

        ), 
'dimension_v44q3m23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item .wkit-img-accord-item-desc{ border-radius: {{dimension_v44q3m23}}}',
                ),
            ),
        ), 
'boxshadow_0i4x0723' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item .wkit-img-accord-item-desc',
                    ),
                ),
            ), 
'color_w4r0a323' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item:hover .wkit-img-accord-item-desc{color:{{color_w4r0a323}};}',
                ),
            ),
        ), 
'background_mc4qm323' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item:hover .wkit-img-accord-item-desc',
                ),
            ),
        ), 
'border_u2ryg125' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item:hover .wkit-img-accord-item-desc',
                ),
            ),

        ), 
'dimension_nm4ole23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item:hover .wkit-img-accord-item-desc{ border-radius: {{dimension_nm4ole23}}}',
                ),
            ),
        ), 
'boxshadow_157mrw23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item:hover .wkit-img-accord-item-desc',
                    ),
                ),
            ), 
'color_89cfrb23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item.wkit-item-active .wkit-img-accord-item-desc{color:{{color_89cfrb23}};}',
                ),
            ),
        ), 
'background_x8w26v23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item.wkit-item-active .wkit-img-accord-item-desc',
                ),
            ),
        ), 
'border_dxl2pf25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item.wkit-item-active .wkit-img-accord-item-desc',
                ),
            ),

        ), 
'dimension_g1pgu623' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item.wkit-item-active .wkit-img-accord-item-desc{ border-radius: {{dimension_g1pgu623}}}',
                ),
            ),
        ), 
'boxshadow_6ql7u123' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item.wkit-item-active .wkit-img-accord-item-desc',
                    ),
                ),
            ), 

'background_aspodx23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accord-item::after',
                ),
            ),
        ), 
'border_4x0w2w25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item',
                ),
            ),

        ), 
'dimension_topbdb23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accord-item{ border-radius: {{dimension_topbdb23}}}',
                ),
            ),
        ), 
'boxshadow_lksfpm23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-img-accord-item',
                    ),
                ),
            ), 
'background_49etgq23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accord-item:hover::after',
                ),
            ),
        ), 
'border_id88ei25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item:hover',
                ),
            ),

        ), 
'dimension_pviklb23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accord-item:hover{ border-radius: {{dimension_pviklb23}}}',
                ),
            ),
        ), 
'boxshadow_68jo8123' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-img-accord-item:hover',
                    ),
                ),
            ), 
'background_jrkmwe23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accord-item.wkit-item-active::after',
                ),
            ),
        ), 
'border_7qh4n025' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item.wkit-item-active',
                ),
            ),

        ), 
'dimension_acct6i23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accord-item.wkit-item-active{ border-radius: {{dimension_acct6i23}}}',
                ),
            ),
        ), 
'boxshadow_semqb423' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item.wkit-item-active',
                    ),
                ),
            ), 

'dimension_n07xme23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item{ padding: {{dimension_n07xme23}}}',
                ),
            ),
        ), 
'dimension_jm7euq23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-items{ padding: {{dimension_jm7euq23}}}',
                ),
            ),
        ), 
'dimension_5yu1lh23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-items{ margin: {{dimension_5yu1lh23}}}',
                ),
            ),
        ), 
'background_oxyjao23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-items',
                ),
            ),
        ), 
'border_gagobg25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-items',
                ),
            ),

        ), 
'dimension_7l2uiy23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-items{ border-radius: {{dimension_7l2uiy23}}}',
                ),
            ),
        ), 
'boxshadow_aao2ma23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-items',
                    ),
                ),
            ), 
'background_k1erq723' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-items:hover',
                ),
            ),
        ), 
'border_4wubbb25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-items:hover',
                ),
            ),

        ), 
'dimension_hscjnl23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-items:hover{ border-radius: {{dimension_hscjnl23}}}',
                ),
            ),
        ), 
'boxshadow_km1yt823' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-items:hover',
                    ),
                ),
            ), 
'background_qn45xl23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item.wkit-item-active .wkit-img-accord-items',
                ),
            ),
        ), 
'border_e9dbnc25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item.wkit-item-active .wkit-img-accord-items',
                ),
            ),

        ), 
'dimension_3c6vmm23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item.wkit-item-active .wkit-img-accord-items{ border-radius: {{dimension_3c6vmm23}}}',
                ),
            ),
        ), 
'boxshadow_g92rip23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-img-accrod-main-wrap .wkit-img-accord-item.wkit-item-active .wkit-img-accord-items',
                    ),
                ),
            ), 

'heading_yiezxy23' => array(
            'type' => 'string',
        ), 
'color_1116ag23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .splide__pagination__page{background-color:{{color_1116ag23}};}',
                ),
            ),
        ), 
'color_n7pjum23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .splide__pagination__page:hover{background-color:{{color_n7pjum23}};}',
                ),
            ),
        ), 
'color_9lb9qf23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .splide__pagination__page.is-active{background-color:{{color_9lb9qf23}};}',
                ),
            ),
        ), 


    
                ],
                'style'         => 'wbuilder-cgb-style_202zfm25-css',
                'editor_script' => 'wbuilder-cgb-block_biiunf25-js',
                'render_callback' => 'wkit_render_callback_m2j0qj25'
            )
        );
    
    }
    add_action( 'init', 'wb_Image_Accordion_16z8el24' );
    
    function wkit_render_callback_m2j0qj25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }