<?php
    function wb_Hover_3D_Effect_a5ewzl24() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            wp_enqueue_script( 'wd_ex_script_vz58ii25', 'https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.1/vanilla-tilt.min.js', [], '2.0.12.426353', true );

            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_external0bz95j25-js", WDKIT_SERVER_PATH . '/gutenberg/Hover-3D-Effect_a5ewzl24/index.js', array('jquery'),'2.0.12.834957', true);}

            
        }

        wp_register_script('wbuilder-cgb-block_0bz95j25-js', WDKIT_SERVER_PATH .'/gutenberg/Hover-3D-Effect_a5ewzl24/Hover_3D_Effect_a5ewzl24.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.815723', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_akrhzj25-css', WDKIT_SERVER_PATH .'/gutenberg/Hover-3D-Effect_a5ewzl24/Hover_3D_Effect_a5ewzl24.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.645792', false );
    
        register_block_type(
            'wdkit/wb-a5ewzl24', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'select_q1dkxv23' => array(
            'type' => 'string',
            'default' => 'style-1'
        ),
'text_f33d9d23' => array(
            'type' => 'string',
            'default' => 'Library '
        ),
'text_9zhu6023' => array(
            'type' => 'string',
            'default' => 'Discover Your Next Read'
        ),
'media_9q7zy323' => array(
            'type' => 'object',
            'default' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                'Id' => '',
            ),
        ), 
'number_6c07ds23' => array(
        'type' => 'string',
        'default' => '15',
        
    ),
'number_kfwyrk23' => array(
        'type' => 'string',
        'default' => '3000',
        
    ),
'number_s0qncc23' => array(
        'type' => 'string',
        'default' => '1500',
        
    ),
'number_8h56x523' => array(
        'type' => 'string',
        'default' => '1.2',
        
    ),
'url_fyl83823' => array(
            'type' => 'object',
            'default' => array(
                'url' => '#',
                'target' => true,
                'nofollow' => 'no-follow'
            ),

        ),
'switcher_hg44e825' => array(
            'type' => 'boolean',
                'default' => true,
        ), 
'switcher_3g4yqc25' => array(
            'type' => 'boolean',
                'default' => true,
        ), 
'choose_x0y5od24' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-imghover-wrapper .wkit-img-content{  align-items: {{choose_x0y5od24}}; }',
                ),
            ),
        ), 
'typography_y0ji3t23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-content > .wkit-img-title',
                ),
            ),
        ), 
'color_tjm16p23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-img-content > .wkit-img-title{color:{{color_tjm16p23}};}',
                ),
            ),
        ), 
'color_p8bn3223' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-img-box:hover .wkit-img-cover .wkit-img-content > .wkit-img-title{color:{{color_p8bn3223}};}',
                ),
            ),
        ), 

'typography_yab5j723' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-content > .wkit-img-label',
                ),
            ),
        ), 
'color_xflbg724' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-img-content > .wkit-img-label{color:{{color_xflbg724}};}',
                ),
            ),
        ), 
'color_3hwuec24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-img-box:hover .wkit-img-content > .wkit-img-label{color:{{color_3hwuec24}};}',
                ),
            ),
        ), 

'dimension_cg8tx423' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-img-box .wkit-img-content{ padding: {{dimension_cg8tx423}}}',
                ),
            ),
        ), 
'slider_yg9i7m23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-imghover-wrapper .wkit-img-box{width:{{slider_yg9i7m23}};}',
                ),
            ),
        ), 
'slider_259zwi23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-imghover-wrapper .wkit-img-box{height:{{slider_259zwi23}};}',
                ),
            ),
        ), 
'color_qcn9t824' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-imghover-wrapper .wkit-img-box.style-2{background-color:{{color_qcn9t824}};}',
                ),
            ),
        ), 
'color_im6piv23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-imghover-wrapper .wkit-img-glitch .wkit-glitch, .wkit-imghover-wrapper .wkit-img-glitch .wkit-glitch:nth-child(2), .wkit-imghover-wrapper .wkit-img-glitch .wkit-glitch:nth-child(5){background-color:{{color_im6piv23}};}',
                ),
            ),
        ), 
'color_9h0e8723' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-imghover-wrapper .wkit-img-glitch .wkit-glitch:nth-child(3), .wkit-imghover-wrapper .wkit-img-glitch .wkit-glitch:nth-child(4){background-color:{{color_9h0e8723}};}',
                ),
            ),
        ), 

    
                ],
                'style'         => 'wbuilder-cgb-style_akrhzj25-css',
                'editor_script' => 'wbuilder-cgb-block_0bz95j25-js',
                'render_callback' => 'wkit_render_callback_rzes2m25'
            )
        );
    
    }
    add_action( 'init', 'wb_Hover_3D_Effect_a5ewzl24' );
    
    function wkit_render_callback_rzes2m25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }