<?php
    function wb_CTA_Image_Box_sj5wmm24() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_externalk1xk7h25-js", WDKIT_SERVER_PATH . '/gutenberg/CTA-Image-Box_sj5wmm24/index.js', [],'2.0.12.989547', true);}

            
        }

        wp_register_script('wbuilder-cgb-block_k1xk7h25-js', WDKIT_SERVER_PATH .'/gutenberg/CTA-Image-Box_sj5wmm24/CTA_Image_Box_sj5wmm24.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.992697', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_mu1yzt25-css', WDKIT_SERVER_PATH .'/gutenberg/CTA-Image-Box_sj5wmm24/CTA_Image_Box_sj5wmm24.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.6574', false );
    
        register_block_type(
            'wdkit/wb-sj5wmm24', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'text_8wm4ah23' => array(
            'type' => 'string',
            'default' => 'Join Our Community'
        ),
'textarea_90fwfc23' => array(
            'type' => 'string',
            'default' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed eiusmod tempor labore.',
        ),
'media_g1sl5u23' => array(
            'type' => 'object',
            'default' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/uploads/sites/67/2024/05/placeholder-614.png',
                'Id' => '',
            ),
        ), 
'choose_dtkucc23' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .cta-content-title{ text-align: {{choose_dtkucc23}}; }',
                ),
            ),
        ), 
'typography_05gw5v23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .cta-content-title',
                ),
            ),
        ), 
'color_sk0una23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .cta-content-title{color:{{color_sk0una23}};}',
                ),
            ),
        ), 
'choose_ouoj6i25' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-cta-image-box .cta-content-desc{ text-align: {{choose_ouoj6i25}}; }',
                ),
            ),
        ), 
'typography_xqhovj23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .cta-content-desc',
                ),
            ),
        ), 
'color_3z99ek23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .cta-content-desc{color:{{color_3z99ek23}};}',
                ),
            ),
        ), 
'heading_t7w26y23' => array(
            'type' => 'string',
        ), 
'choose_jew2pz23' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .cta-content{ align-items: {{choose_jew2pz23}}; }',
                ),
            ),
        ), 
'heading_tsks1123' => array(
            'type' => 'string',
        ), 
'choose_thjhop23' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .cta-image-inner{ align-items: {{choose_thjhop23}}; }',
                ),
            ),
        ), 
'dimension_tylq7223' => array(
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
                    'selector' => '{{PLUS_WRAP}} .cta-image-inner{ Padding: {{dimension_tylq7223}}}',
                ),
            ),
        ), 
'slider_xxnafn23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .cta-image-inner{height:{{slider_xxnafn23}};}',
                ),
            ),
        ), 
'border_97e15a23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .cta-image-inner',
                ),
            ),

        ), 
'dimension_y8d75s23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .cta-image-inner{ border-radius: {{dimension_y8d75s23}}}',
                ),
            ),
        ), 
'boxshadow_etwxh323' => array(
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
                        'selector' => '{{PLUS_WRAP}} .cta-image-inner',
                    ),
                ),
            ), 
'background_bnilhn23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .cta-image-inner',
                ),
            ),
        ), 
'border_cij2ye23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .cta-image-inner:hover',
                ),
            ),

        ), 
'boxshadow_e2ep8223' => array(
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
                        'selector' => '{{PLUS_WRAP}} .cta-image-inner:hover',
                    ),
                ),
            ), 


    
                ],
                'style'         => 'wbuilder-cgb-style_mu1yzt25-css',
                'editor_script' => 'wbuilder-cgb-block_k1xk7h25-js',
                'render_callback' => 'wkit_render_callback_1mge2r25'
            )
        );
    
    }
    add_action( 'init', 'wb_CTA_Image_Box_sj5wmm24' );
    
    function wkit_render_callback_1mge2r25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }