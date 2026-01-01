<?php
    function wb_Banner_3D_With_Carousel_cp0dlj24() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            wp_enqueue_script( 'wd_ex_script_xmfyka25', 'https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.1/vanilla-tilt.min.js', [], '2.1.0.277072', true );
wp_enqueue_script( 'wd_ex_script_8fsq6q25', 'https://cdnjs.cloudflare.com/ajax/libs/splidejs/4.1.4/js/splide.min.js', [], '2.1.0.373255', true );

            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_externalh21ti325-js", WDKIT_SERVER_PATH . '/gutenberg/Banner-3D-With-Carousel_cp0dlj24/index.js', array('jquery'),'2.1.0.138486', true);}

            wp_enqueue_style( 'wd_css_ex_1_nz9itc25', 'https://cdnjs.cloudflare.com/ajax/libs/splidejs/4.1.4/css/splide.min.css', false, '2.1.0.247518', 'all' );

        }

        wp_register_script('wbuilder-cgb-block_h21ti325-js', WDKIT_SERVER_PATH .'/gutenberg/Banner-3D-With-Carousel_cp0dlj24/Banner_3D_With_Carousel_cp0dlj24.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.1.0.386453', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_w4arux25-css', WDKIT_SERVER_PATH .'/gutenberg/Banner-3D-With-Carousel_cp0dlj24/Banner_3D_With_Carousel_cp0dlj24.css', is_admin() ? array( 'wp-editor' ) : null, '2.1.0.894551', false );
    
        register_block_type(
            'wdkit/wb-cp0dlj24', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'repeater_27pwea23' => array(
            'type' => 'array',
            'repeaterField' => array(
                (object) array(
                    'media_d8rrzd23' => array(
            'type' => 'object',
            'default' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/uploads/sites/67/2024/05/placeholder-614.png',
                'Id' => '',
            ),
        ), 

                )
            ),
            'default' => array(array(
 '_key' => '0', 
'media_d8rrzd23' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/uploads/sites/67/2024/05/placeholder-614.png',
                'Id' => '',
            ),),),
        ), 
'text_69qbko23' => array(
            'type' => 'string',
            'default' => 'Digital daydreams brought to life'
        ),
'text_hrlhn723' => array(
            'type' => 'string',
            'default' => 'Watch More'
        ),
'iconscontrol_rj2zaq23' => array(
            'type' => 'string',
            'default'=> 'fas fa-chevron-right',
        ), 
'url_awskqt23' => array(
            'type' => 'object',
            'default' => array(
                'url' => '#',
                'target' => true,
                'nofollow' => 'no-follow'
            ),

        ),
'switcher_bwncwo23' => array(
            'type' => 'boolean',
                'default' => false,
        ), 
'select_zelziu23' => array(
            'type' => 'string',
            'default' => 'style-1'
        ),
'slider_4ti6kk23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => '%',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-heading{top:{{slider_4ti6kk23}};}',
                ),
            ),
        ), 
'slider_5hzq2r23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => '%',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-heading{left:{{slider_5hzq2r23}};}',
                ),
            ),
        ), 
'typography_fp3w7s23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-heading-inner-text',
                ),
            ),
        ), 
'color_8jyod623' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-heading-inner-text{color:{{color_8jyod623}};}',
                ),
            ),
        ), 
'slider_t8ll0e23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-media{max-width:{{slider_t8ll0e23}};}',
                ),
            ),
        ), 
'slider_pa81nu23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => 500,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-media-inner{height:{{slider_pa81nu23}};}',
                ),
            ),
        ), 
'background_zyhwm223' => array(
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
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-media-inner',
                ),
            ),
        ), 
'border_1etufw23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-media',
                ),
            ),

        ), 
'dimension_9mou2f23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-media{ border-radius: {{dimension_9mou2f23}}}',
                ),
            ),
        ), 
'typography_q6qnt923' => array(
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
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-btn-text',
                ),
            ),
        ), 
'slider_r5rjhe23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .tp-title-icon{width:{{slider_r5rjhe23}};}',
                ),
            ),
        ), 
'slider_2jq1bd23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .tp-title-icon{height:{{slider_2jq1bd23}};}',
                ),
            ),
        ), 
'slider_1434fi23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .tp-title-icon{font-size:{{slider_1434fi23}};}',
                ),
            ),
        ), 
'slider_11yb9r24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .tp-title-icon svg{width:{{slider_11yb9r24}};}',
                ),
            ),
        ), 
'slider_gg19h423' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => '%',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-btn{top:{{slider_gg19h423}};}',
                ),
            ),
        ), 
'slider_77vw0223' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => '%',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-btn{left:{{slider_77vw0223}};}',
                ),
            ),
        ), 
'color_38ls0223' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .banner-3d-inner-btn-text{color:{{color_38ls0223}};}',
                ),
            ),
        ), 
'color_a8b17k23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .tp-title-icon{color:{{color_a8b17k23}};}',
                ),
            ),
        ), 
'background_qm19p523' => array(
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
                    'selector' => '{{PLUS_WRAP}} .tp-title-icon',
                ),
            ),
        ), 
'border_hcof4z23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .tp-title-icon',
                ),
            ),

        ), 
'boxshadow_78qwh323' => array(
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
                        'selector' => '{{PLUS_WRAP}} .tp-title-icon',
                    ),
                ),
            ), 
'color_2ogt6b23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .btn-banner:hover .banner-3d-inner-btn-text{color:{{color_2ogt6b23}};}',
                ),
            ),
        ), 
'color_q3t4d323' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .btn-banner:hover .tp-title-icon{color:{{color_q3t4d323}};}',
                ),
            ),
        ), 
'background_ogbchk23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .btn-banner:hover .tp-title-icon',
                ),
            ),
        ), 
'border_kqx21a23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .btn-banner:hover .tp-title-icon',
                ),
            ),

        ), 
'boxshadow_hchi9h23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .btn-banner:hover .tp-title-icon',
                    ),
                ),
            ), 

'slider_p79h6523' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-banner-3d-with-carousel .splide__arrow,.wkit-banner-3d-with-carousel .splide__arrow svg{font-size:{{slider_p79h6523}};}',
                ),
            ),
        ), 
'slider_02718v23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => '%',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-banner-3d-with-carousel .splide__arrow{top:{{slider_02718v23}};}',
                ),
            ),
        ), 
'slider_a7vfig23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => '%',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-banner-3d-with-carousel .splide__arrow{left:{{slider_a7vfig23}};}',
                ),
            ),
        ), 
'color_8pfe5y23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-banner-3d-with-carousel .splide__arrow svg{fill:{{color_8pfe5y23}};}',
                ),
            ),
        ), 
'background_u64xdg23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-banner-3d-with-carousel .splide__arrow',
                ),
            ),
        ), 
'border_agg1jv23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-banner-3d-with-carousel .splide__arrow',
                ),
            ),

        ), 
'dimension_ybjj7223' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-banner-3d-with-carousel .splide__arrow{ border-radius: {{dimension_ybjj7223}}}',
                ),
            ),
        ), 
'boxshadow_73n6ak23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-banner-3d-with-carousel .splide__arrow',
                    ),
                ),
            ), 
'color_5cu1qd23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-banner-3d-with-carousel .splide__arrow svg:hover{fill:{{color_5cu1qd23}};}',
                ),
            ),
        ), 
'background_lxtc3b23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-banner-3d-with-carousel .splide__arrow:hover',
                ),
            ),
        ), 
'border_pk1a8w23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-banner-3d-with-carousel .splide__arrow:hover',
                ),
            ),

        ), 
'dimension_zs3olu23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-banner-3d-with-carousel .splide__arrow:hover{ border-radius: {{dimension_zs3olu23}}}',
                ),
            ),
        ), 
'boxshadow_kit0yu23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-banner-3d-with-carousel .splide__arrow:hover',
                    ),
                ),
            ), 


    
                ],
                'style'         => 'wbuilder-cgb-style_w4arux25-css',
                'editor_script' => 'wbuilder-cgb-block_h21ti325-js',
                'render_callback' => 'wkit_render_callback_bce7bl25'
            )
        );
    
    }
    add_action( 'init', 'wb_Banner_3D_With_Carousel_cp0dlj24' );
    
    function wkit_render_callback_bce7bl25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }