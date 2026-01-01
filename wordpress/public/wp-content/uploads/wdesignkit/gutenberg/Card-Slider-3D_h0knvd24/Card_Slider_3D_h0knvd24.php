<?php
    function wb_Card_Slider_3D_h0knvd24() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            wp_enqueue_script( 'wd_ex_script_17qdpa25', 'https://unpkg.com/swiper@8/swiper-bundle.min.js', [], '2.1.0.940506', true );

            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_externalcl9vpa25-js", WDKIT_SERVER_PATH . '/gutenberg/Card-Slider-3D_h0knvd24/index.js', array('jquery'),'2.1.0.674995', true);}

            wp_enqueue_style( 'wd_css_ex_1_1yd2n625', 'https://unpkg.com/swiper@8/swiper-bundle.min.css', false, '2.1.0.732468', 'all' );

        }

        wp_register_script('wbuilder-cgb-block_cl9vpa25-js', WDKIT_SERVER_PATH .'/gutenberg/Card-Slider-3D_h0knvd24/Card_Slider_3D_h0knvd24.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.1.0.52043', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_4y9gqq25-css', WDKIT_SERVER_PATH .'/gutenberg/Card-Slider-3D_h0knvd24/Card_Slider_3D_h0knvd24.css', is_admin() ? array( 'wp-editor' ) : null, '2.1.0.762272', false );
    
        register_block_type(
            'wdkit/wb-h0knvd24', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'repeater_71q5n523' => array(
            'type' => 'array',
            'repeaterField' => array(
                (object) array(
                    'media_iq86lv23' => array(
            'type' => 'object',
            'default' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                'Id' => '',
            ),
        ), 
'text_u6ukfd23' => array(
            'type' => 'string',
            'default' => 'Title'
        ),
'text_qgpkp423' => array(
            'type' => 'string',
            'default' => 'Description'
        ),

                )
            ),
            'default' => array(array(
 '_key' => '0', 
'media_iq86lv23' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                'Id' => '',
            ),'text_u6ukfd23' => esc_html__('Title', 'wdesignkit'), 
'text_qgpkp423' => esc_html__('Description', 'wdesignkit'), 
),),
        ), 
'switcher_f0nzaz23' => array(
            'type' => 'boolean',
                'default' => true,
        ), 
'switcher_khmwnj23' => array(
            'type' => 'boolean',
                'default' => true,
        ), 
'iconscontrol_dh9nd023' => array(
            'type' => 'string',
            'default'=> 'fas fa-arrow-left',
        ), 
'iconscontrol_edu4rx23' => array(
            'type' => 'string',
            'default'=> 'fas fa-arrow-right',
        ), 
'heading_z5ug2n23' => array(
            'type' => 'string',
        ), 
'slider_a7xwz323' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => 370,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-cardslide-wrapper .wkit-slide{width:{{slider_a7xwz323}};}',
                ),
            ),
        ), 
'slider_mw7cvx23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => 420,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-cardslide-wrapper .wkit-slide{height:{{slider_mw7cvx23}};}',
                ),
            ),
        ), 
'slider_0m9cpt23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => 40,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} #tranding .wkit-slider{margin-bottom:{{slider_0m9cpt23}};}',
                ),
            ),
        ), 
'cssfilter_scfclt23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-slide .wkit-slide-img img',
                ),
            ),
        ), 
'cssfilter_gjj9ie23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-slide:hover img',
                ),
            ),
        ), 

'dimension_ixev8y23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-slide .wkit-slide-img img{ border-radius: {{dimension_ixev8y23}}}',
                ),
            ),
        ), 
'heading_hsdmfk23' => array(
            'type' => 'string',
        ), 
'typography_u6sifs23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-slide-content .wkit-slide-title',
                ),
            ),
        ), 
'color_upccoq23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-slide-content .wkit-slide-title{color:{{color_upccoq23}};}',
                ),
            ),
        ), 
'heading_u35ze923' => array(
            'type' => 'string',
        ), 
'dimension_jiu18123' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-slide .wkit-slide-content .wkit-slide-desc{ padding: {{dimension_jiu18123}}}',
                ),
            ),
        ), 
'typography_4bn02r23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-slide-content .wkit-slide-desc',
                ),
            ),
        ), 
'color_kvusvd23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-slide-content .wkit-slide-desc{color:{{color_kvusvd23}};}',
                ),
            ),
        ), 
'heading_llnnvj23' => array(
            'type' => 'string',
        ), 
'slider_1v0op923' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => 12,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-slider-control .swiper-pagination .swiper-pagination-bullet{width:{{slider_1v0op923}};}',
                ),
            ),
        ), 
'slider_abpyok23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => 12,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-slider-control .swiper-pagination .swiper-pagination-bullet{height:{{slider_abpyok23}};}',
                ),
            ),
        ), 
'color_j5oevx23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-slider-control .swiper-pagination .swiper-pagination-bullet{background:{{color_j5oevx23}};}',
                ),
            ),
        ), 
'heading_mjbet923' => array(
            'type' => 'string',
        ), 
'slider_5hxcyx23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => 12,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-slider-control .slider-arrow .tp-title-icon{font-size:{{slider_5hxcyx23}};}',
                ),
            ),
        ), 
'slider_z2m5eh23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => 50,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-slider-control .slider-arrow{width:{{slider_z2m5eh23}};}',
                ),
            ),
        ), 
'slider_oe3do423' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => 45,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-slider-control .slider-arrow{height:{{slider_oe3do423}};}',
                ),
            ),
        ), 
'color_uwboip23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-slider-control .slider-arrow .tp-title-icon{color:{{color_uwboip23}};}',
                ),
            ),
        ), 
'color_glkk4e23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-slider-control .slider-arrow{background-color:{{color_glkk4e23}};}',
                ),
            ),
        ), 
'color_vwzghk23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-slider-control .slider-arrow:hover .tp-title-icon{color:{{color_vwzghk23}};}',
                ),
            ),
        ), 
'color_4qy7sc24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-slider-control .slider-arrow:hover{background-color:{{color_4qy7sc24}};}',
                ),
            ),
        ), 


    
                ],
                'style'         => 'wbuilder-cgb-style_4y9gqq25-css',
                'editor_script' => 'wbuilder-cgb-block_cl9vpa25-js',
                'render_callback' => 'wkit_render_callback_uutz9f25'
            )
        );
    
    }
    add_action( 'init', 'wb_Card_Slider_3D_h0knvd24' );
    
    function wkit_render_callback_uutz9f25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }