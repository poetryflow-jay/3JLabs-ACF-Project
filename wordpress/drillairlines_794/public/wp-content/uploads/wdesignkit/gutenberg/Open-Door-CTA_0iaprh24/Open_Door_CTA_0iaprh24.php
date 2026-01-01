<?php
    function wb_Open_Door_CTA_0iaprh24() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_externalaw92u925-js", WDKIT_SERVER_PATH . '/gutenberg/Open-Door-CTA_0iaprh24/index.js', array('jquery'),'2.0.12.624333', true);}

            
        }

        wp_register_script('wbuilder-cgb-block_aw92u925-js', WDKIT_SERVER_PATH .'/gutenberg/Open-Door-CTA_0iaprh24/Open_Door_CTA_0iaprh24.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.783972', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_krpytj25-css', WDKIT_SERVER_PATH .'/gutenberg/Open-Door-CTA_0iaprh24/Open_Door_CTA_0iaprh24.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.183888', false );
    
        register_block_type(
            'wdkit/wb-0iaprh24', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'text_g2dr7v24' => array(
            'type' => 'string',
            'default' => 'Web'
        ),
'text_axcmuo24' => array(
            'type' => 'string',
            'default' => 'Developer'
        ),
'media_sxu09l24' => array(
            'type' => 'object',
            'default' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                'Id' => '',
            ),
        ), 
'url_xtmgq224' => array(
            'type' => 'object',
            'default' => array(
                'url' => '#',
                'target' => true,
                'nofollow' => 'no-follow'
            ),

        ),
'switcher_ffps9u25' => array(
            'type' => 'boolean',
                'default' => true,
        ), 
'switcher_92zvfd25' => array(
            'type' => 'boolean',
                'default' => true,
        ), 
'choose_bwfhr024' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-open-door-cta{ text-align: {{choose_bwfhr024}}; }',
                ),
            ),
        ), 
'dimension_h7dr0f25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-open-door-cta .open-door-cta-inner{ padding: {{dimension_h7dr0f25}}}',
                ),
            ),
        ), 
'typography_olwwb124' => array(
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
                    'selector' => '{{PLUS_WRAP}} .open-door-cta span',
                ),
            ),
        ), 
'color_oiycwp24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .open-door-cta span{color:{{color_oiycwp24}};}',
                ),
            ),
        ), 
'background_bwna2u24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-open-door-cta',
                ),
            ),
        ), 
'color_1n84rf24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .open-door-cta:hover span{color:{{color_1n84rf24}};}',
                ),
            ),
        ), 
'background_xc22jj24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-open-door-cta.active',
                ),
            ),
        ), 

'slider_an5fbo24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .open-door-cta:hover .main-cta-door{width:{{slider_an5fbo24}};}',
                ),
            ),
        ), 
'slider_3rx4x624' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .main-cta-door{height:{{slider_3rx4x624}};}',
                ),
            ),
        ), 
'border_urwr9h24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-open-door-cta.active .main-cta-door',
                ),
            ),

        ), 
'dimension_za7zv224' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-open-door-cta.active .main-cta-door{ border-radius: {{dimension_za7zv224}}}',
                ),
            ),
        ), 
'boxshadow_lbb3sm24' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-open-door-cta.active .main-cta-door',
                    ),
                ),
            ), 

    
                ],
                'style'         => 'wbuilder-cgb-style_krpytj25-css',
                'editor_script' => 'wbuilder-cgb-block_aw92u925-js',
                'render_callback' => 'wkit_render_callback_37z1oe25'
            )
        );
    
    }
    add_action( 'init', 'wb_Open_Door_CTA_0iaprh24' );
    
    function wkit_render_callback_37z1oe25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }