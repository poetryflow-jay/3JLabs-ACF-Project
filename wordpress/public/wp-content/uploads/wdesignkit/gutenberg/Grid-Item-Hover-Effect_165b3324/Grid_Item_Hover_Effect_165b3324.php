<?php
    function wb_Grid_Item_Hover_Effect_165b3324() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            wp_enqueue_script( 'wd_ex_script_1c9qf525', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js', [], '2.0.12.370280', true );

            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_external0oupsu25-js", WDKIT_SERVER_PATH . '/gutenberg/Grid-Item-Hover-Effect_165b3324/index.js', [],'2.0.12.617310', true);}

            wp_enqueue_style( 'wd_css_ex_1_9pzo7e25', 'https://fonts.googleapis.com/css?family=Roboto:400,400i,700&display=swap', false, '2.0.12.694321', 'all' );
wp_enqueue_style( 'wd_css_ex_1_lcr0ky25', 'https://example-cdn.com/path/to/tt-autonomous.css', false, '2.0.12.234705', 'all' );

        }

        wp_register_script('wbuilder-cgb-block_0oupsu25-js', WDKIT_SERVER_PATH .'/gutenberg/Grid-Item-Hover-Effect_165b3324/Grid_Item_Hover_Effect_165b3324.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.182299', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_2igape25-css', WDKIT_SERVER_PATH .'/gutenberg/Grid-Item-Hover-Effect_165b3324/Grid_Item_Hover_Effect_165b3324.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.737487', false );
    
        register_block_type(
            'wdkit/wb-165b3324', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'select_yz2a6g23' => array(
            'type' => 'string',
            'default' => 'style-1'
        ),
'media_b9lx0423' => array(
            'type' => 'object',
            'default' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/uploads/sites/67/2024/05/placeholder-614.png',
                'Id' => '',
            ),
        ), 
'select_osz0gl23' => array(
            'type' => 'string',
            'default' => 'left'
        ),
'text_mqfxni23' => array(
            'type' => 'string',
            'default' => '0 1'
        ),
'textarea_xkdmvy23' => array(
            'type' => 'string',
            'default' => 'Web Development',
        ),
'select_r31rb323' => array(
            'type' => 'string',
            'default' => 'left'
        ),
'text_nb262d23' => array(
            'type' => 'string',
            'default' => '0 2'
        ),
'textarea_ffw1jh23' => array(
            'type' => 'string',
            'default' => 'Web Design',
        ),
'background_tmv7dc23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .top-inner-box',
                ),
            ),
        ), 
'heading_xmxjp923' => array(
            'type' => 'string',
        ), 
'typography_s6vwsy23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .pos-top-title',
                ),
            ),
        ), 
'choose_4tnkwo23' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .pos-top-title{ justify-content: {{choose_4tnkwo23}}; }',
                ),
            ),
        ), 
'color_l18c0823' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .pos-top-title{color:{{color_l18c0823}};}',
                ),
            ),
        ), 
'heading_o89l0623' => array(
            'type' => 'string',
        ), 
'typography_ist8gn23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .pos-top-desc',
                ),
            ),
        ), 
'choose_c1w0o323' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .pos-top-desc{ justify-content: {{choose_c1w0o323}}; }',
                ),
            ),
        ), 
'color_rtgj1n23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .pos-top-desc{color:{{color_rtgj1n23}};}',
                ),
            ),
        ), 
'background_lcpy8923' => array(
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
                    'selector' => '{{PLUS_WRAP}} .bottom-inner-box',
                ),
            ),
        ), 
'heading_vlnfoc23' => array(
            'type' => 'string',
        ), 
'typography_z8y6rq23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .pos-bottom-title',
                ),
            ),
        ), 
'choose_f7s9n123' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .pos-bottom-title{ justify-content: {{choose_f7s9n123}}; }',
                ),
            ),
        ), 
'color_h146hi23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .pos-bottom-title{color:{{color_h146hi23}};}',
                ),
            ),
        ), 
'heading_sjxht823' => array(
            'type' => 'string',
        ), 
'typography_rq9coe23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .pos-bottom-desc',
                ),
            ),
        ), 
'choose_oco3ax23' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .pos-bottom-desc{ text-align: {{choose_oco3ax23}}; }',
                ),
            ),
        ), 
'color_pza92p23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .pos-bottom-desc{color:{{color_pza92p23}};}',
                ),
            ),
        ), 
'background_gws5h423' => array(
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
                    'selector' => '{{PLUS_WRAP}} .grid-inner',
                ),
            ),
        ), 
'color_pnz90023' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .grid-inner{color:{{color_pnz90023}};}',
                ),
            ),
        ), 
'dimension_jimc3h23' => array(
            'type' => 'object',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .grid-inner{ border-radius: {{dimension_jimc3h23}}}',
                ),
            ),
        ), 
'border_e2di3m23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .grid-inner',
                ),
            ),

        ), 
'boxshadow_zah1aw23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .grid-inner',
                    ),
                ),
            ), 
'background_84w2cm23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .grid-inner:hover',
                ),
            ),
        ), 
'color_nuqv1e23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} grid-inner-image:hover{color:{{color_nuqv1e23}};}',
                ),
            ),
        ), 
'dimension_l80vh023' => array(
            'type' => 'object',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .grid-inner:hover{ border-radius: {{dimension_l80vh023}}}',
                ),
            ),
        ), 
'border_znmnp323' => array(
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
                    'selector' => '{{PLUS_WRAP}} .grid-inner:hover',
                ),
            ),

        ), 
'boxshadow_14o8qb23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .grid-inner:hover',
                    ),
                ),
            ), 


    
                ],
                'style'         => 'wbuilder-cgb-style_2igape25-css',
                'editor_script' => 'wbuilder-cgb-block_0oupsu25-js',
                'render_callback' => 'wkit_render_callback_ozbe3225'
            )
        );
    
    }
    add_action( 'init', 'wb_Grid_Item_Hover_Effect_165b3324' );
    
    function wkit_render_callback_ozbe3225($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }