<?php
    function wb_Scroll_Banner_d0l3gf24() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            wp_enqueue_script( 'wd_ex_script_shnvw425', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/gsap.min.js', [], '2.0.12.919815', true );
wp_enqueue_script( 'wd_ex_script_fb42my25', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/ScrollTrigger.min.js', [], '2.0.12.434130', true );

            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_externalqn34li25-js", WDKIT_SERVER_PATH . '/gutenberg/Scroll-Banner_d0l3gf24/index.js', array('jquery'),'2.0.12.48030', true);}

            
        }

        wp_register_script('wbuilder-cgb-block_qn34li25-js', WDKIT_SERVER_PATH .'/gutenberg/Scroll-Banner_d0l3gf24/Scroll_Banner_d0l3gf24.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.916737', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_cyej8p25-css', WDKIT_SERVER_PATH .'/gutenberg/Scroll-Banner_d0l3gf24/Scroll_Banner_d0l3gf24.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.818927', false );
    
        register_block_type(
            'wdkit/wb-d0l3gf24', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'select_maa9l124' => array(
            'type' => 'string',
            'default' => 'style-1'
        ),
'text_yrgf8h23' => array(
            'type' => 'string',
            'default' => 'Available on Spotify.'
        ),
'textarea_5s0pvo24' => array(
            'type' => 'string',
            'default' => 'Spotify is one of the world\'s leading music streaming services, offering users access to millions of songs and podcasts from various artists and creators',
        ),
'text_wjncvf24' => array(
            'type' => 'string',
            'default' => 'Read More'
        ),
'url_8nbad724' => array(
            'type' => 'object',
            'default' => array(
                'url' => '#',
                'target' => true,
                'nofollow' => 'no-follow'
            ),

        ),
'iconscontrol_ysenzi24' => array(
            'type' => 'string',
            'default'=> 'fas fa-chevron-right',
        ), 
'choose_dayp7w24' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-scroll-banner, {{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-description{ text-align: {{choose_dayp7w24}}; }',
                ),
            ),
        ), 
'choose_m609eh24' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-scroll-banner{ justify-content: {{choose_m609eh24}}; }',
                ),
            ),
        ), 
'number_3g0yfs24' => array(
        'type' => 'string',
        'default' => '0.8',
        
    ),
'number_fthuwx24' => array(
        'type' => 'string',
        'default' => '0.7',
        
    ),
'number_3aeero24' => array(
        'type' => 'string',
        'default' => '0.6',
        
    ),
'heading_6bn6va24' => array(
            'type' => 'string',
        ), 
'heading_sp6a3x24' => array(
            'type' => 'string',
        ), 
'typography_z30reh24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-title',
                ),
            ),
        ), 
'color_9mrdfv24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-title{color:{{color_9mrdfv24}};}',
                ),
            ),
        ), 
'slider_krejxj24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => 10,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-title{margin-bottom:{{slider_krejxj24}};}',
                ),
            ),
        ), 
'heading_iuiaj924' => array(
            'type' => 'string',
        ), 
'typography_b3qci424' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-description',
                ),
            ),
        ), 
'color_kpvmxz24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-description{color:{{color_kpvmxz24}};}',
                ),
            ),
        ), 
'slider_zojp0v24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => 10,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-description{margin-bottom:{{slider_zojp0v24}};}',
                ),
            ),
        ), 
'slider_d07uf624' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => 100,
                "unit" => '%',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-description{width:{{slider_d07uf624}};}',
                ),
            ),
        ), 
'dimension_3hx4sb24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-description{ padding: {{dimension_3hx4sb24}}}',
                ),
            ),
        ), 
'choose_30vc8f24' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-scroll-banner .wkit-scroll-banner-btn-wrap{ justify-content: {{choose_30vc8f24}}; }',
                ),
            ),
        ), 
'slider_xknxkv24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => 10,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}}  .wkit-scroll-wrapper .wkit-read-more{gap:{{slider_xknxkv24}};}',
                ),
            ),
        ), 
'slider_wpyc8b24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-read-more .wkit-read-icon .tp-title-icon{font-size:{{slider_wpyc8b24}};}',
                ),
            ),
        ), 
'slider_lph0je24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-read-more .wkit-read-icon .tp-title-icon svg{width:{{slider_lph0je24}};}',
                ),
            ),
        ), 
'dimension_2ivujb24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-read-more{ padding: {{dimension_2ivujb24}}}',
                ),
            ),
        ), 
'typography_o6gdfm24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-read-more',
                ),
            ),
        ), 
'color_mds39e24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-read-more{color:{{color_mds39e24}};}',
                ),
            ),
        ), 
'color_ti4a4a24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-read-more .tp-title-icon{color:{{color_ti4a4a24}};}',
                ),
            ),
        ), 
'color_hdswtq24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-read-more svg{fill:{{color_hdswtq24}};}',
                ),
            ),
        ), 
'background_hcwr3z24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-read-more',
                ),
            ),
        ), 
'border_rd4der24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-read-more',
                ),
            ),

        ), 
'dimension_t60onu24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-read-more{ border-radius: {{dimension_t60onu24}}}',
                ),
            ),
        ), 
'boxshadow_nzl2ji24' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-read-more',
                    ),
                ),
            ), 
'color_8w9wmg24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-read-more:hover{color:{{color_8w9wmg24}};}',
                ),
            ),
        ), 
'color_06or9n24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-read-more:hover .tp-title-icon{color:{{color_06or9n24}};}',
                ),
            ),
        ), 
'color_ca5les24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-read-more:hover svg{fill:{{color_ca5les24}};}',
                ),
            ),
        ), 
'background_37b2um24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-read-more:hover',
                ),
            ),
        ), 
'border_91b75824' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-read-more:hover',
                ),
            ),

        ), 
'dimension_vlqyk224' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-read-more:hover{ border-radius: {{dimension_vlqyk224}}}',
                ),
            ),
        ), 
'boxshadow_aol69g24' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-read-more:hover',
                    ),
                ),
            ), 

'slider_2n6tro24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-scroll-banner{height:{{slider_2n6tro24}};}',
                ),
            ),
        ), 
'dimension_fakwy624' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-scroll-banner{ padding: {{dimension_fakwy624}}}',
                ),
            ),
        ), 
'background_6kkskj24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-scroll-banner',
                ),
            ),
        ), 
'border_83ln3024' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-scroll-banner',
                ),
            ),

        ), 
'dimension_qqh70n24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-scroll-banner{ border-radius: {{dimension_qqh70n24}}}',
                ),
            ),
        ), 
'boxshadow_7eywv524' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-scroll-wrapper .wkit-scroll-banner',
                    ),
                ),
            ), 

    
                ],
                'style'         => 'wbuilder-cgb-style_cyej8p25-css',
                'editor_script' => 'wbuilder-cgb-block_qn34li25-js',
                'render_callback' => 'wkit_render_callback_g538b625'
            )
        );
    
    }
    add_action( 'init', 'wb_Scroll_Banner_d0l3gf24' );
    
    function wkit_render_callback_g538b625($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }