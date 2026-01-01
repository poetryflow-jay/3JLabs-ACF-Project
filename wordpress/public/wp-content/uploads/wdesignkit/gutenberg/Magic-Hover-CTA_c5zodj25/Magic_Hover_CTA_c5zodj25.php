<?php
    function wb_Magic_Hover_CTA_c5zodj25() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_external4k1ts425-js", WDKIT_SERVER_PATH . '/gutenberg/Magic-Hover-CTA_c5zodj25/index.js', [],'2.0.12.359549', true);}

            
        }

        wp_register_script('wbuilder-cgb-block_4k1ts425-js', WDKIT_SERVER_PATH .'/gutenberg/Magic-Hover-CTA_c5zodj25/Magic_Hover_CTA_c5zodj25.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.32553', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_c2xat025-css', WDKIT_SERVER_PATH .'/gutenberg/Magic-Hover-CTA_c5zodj25/Magic_Hover_CTA_c5zodj25.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.483141', false );
    
        register_block_type(
            'wdkit/wb-c5zodj25', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'title_22o96g24' => array(
            'type' => 'string',
            'default' => 'Wdesignkit'
        ),
'descarea_va04vf24' => array(
            'type' => 'string',
            'default' => 'WDesignKit has completely transformed the way I design websites! The widgets are incredibly versatile and easy to customize, saving me hours of work on each project. I can’t recommend it enough for anyone looking to streamline.',
        ),
'iconscontrol_5ogz2q24' => array(
            'type' => 'string',
            'default'=> 'fas fa-chevron-down',
        ), 
'btntext_af3w9224' => array(
            'type' => 'string',
            'default' => 'click me'
        ),
'url_04b0z724' => array(
            'type' => 'object',
            'default' => array(
                'url' => '#',
                'target' => true,
                'nofollow' => 'no-follow'
            ),

        ),
'color_vmdio024' => array(
            'type' => 'string',
            'default' => '',
            
        ), 
'color_fw1sjo24' => array(
            'type' => 'string',
            'default' => '',
            
        ), 
'color_fyu6d324' => array(
            'type' => 'string',
            'default' => '',
            
        ), 
'number_7ulqur25' => array(
        'type' => 'string',
        'default' => '0.2',
        
    ),
'number_m6kdhq25' => array(
        'type' => 'string',
        'default' => '1',
        
    ),
'choose_4mgz8d24' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta .w-magic-hover-container{ align-items: {{choose_4mgz8d24}}; }',
                ),
            ),
        ), 
'dimension_9srg4y24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta  .w-magic-hover-container .w-magic-title{ margin: {{dimension_9srg4y24}}}',
                ),
            ),
        ), 
'typography_5jo5q324' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta  .w-magic-hover-container .w-magic-title',
                ),
            ),
        ), 
'color_osiroq24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta  .w-magic-hover-container .w-magic-title{color:{{color_osiroq24}};}',
                ),
            ),
        ), 
'dimension_c5q6ka24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta  .w-magic-hover-container .w-magic-desc{ margin: {{dimension_c5q6ka24}}}',
                ),
            ),
        ), 
'typography_n22svy24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta  .w-magic-hover-container .w-magic-desc',
                ),
            ),
        ), 
'color_bel38a24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta  .w-magic-hover-container .w-magic-desc{color:{{color_bel38a24}};}',
                ),
            ),
        ), 
'slider_hn9ctu24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta  .w-magic-hover-container  .w-magic-icon i{font-size:{{slider_hn9ctu24}};}',
                ),
            ),
        ), 
'color_sxinvm24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta .w-magic-hover-container .w-magic-icon i{color:{{color_sxinvm24}};}',
                ),
            ),
        ), 
'dimension_t0yzyz24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta .w-magic-hover-container .w-magic-btn{ padding: {{dimension_t0yzyz24}}}',
                ),
            ),
        ), 
'typography_556az724' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta .w-magic-hover-container .w-magic-btn',
                ),
            ),
        ), 
'color_vg24xc24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta .w-magic-hover-container .w-magic-btn{color:{{color_vg24xc24}};}',
                ),
            ),
        ), 
'background_o53zgn24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta .w-magic-hover-container .w-magic-btn',
                ),
            ),
        ), 
'border_gsoax424' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta .w-magic-hover-container .w-magic-btn',
                ),
            ),

        ), 
'dimension_5ngaw524' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta .w-magic-hover-container .w-magic-btn{ border-radius: {{dimension_5ngaw524}}}',
                ),
            ),
        ), 
'boxshadow_ifjk3b24' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta .w-magic-hover-container .w-magic-btn',
                    ),
                ),
            ), 
'color_9gk35224' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta .w-magic-hover-container .w-magic-btn:hover{color:{{color_9gk35224}};}',
                ),
            ),
        ), 
'background_c2v67s24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta .w-magic-hover-container .w-magic-btn:hover',
                ),
            ),
        ), 
'border_0kvzn924' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta .w-magic-hover-container .w-magic-btn:hover',
                ),
            ),

        ), 
'boxshadow_cz5s9224' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta .w-magic-hover-container .w-magic-btn:hover',
                    ),
                ),
            ), 

'dimension_gxtvn624' => array(
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
                    'selector' => '{{PLUS_WRAP}} .w-magic-hover-container{ padding: {{dimension_gxtvn624}}}',
                ),
            ),
        ), 
'slider_p6x6aa24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => '%',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta {width:{{slider_p6x6aa24}};}',
                ),
            ),
        ), 
'slider_5v5gcc24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta {height:{{slider_5v5gcc24}};}',
                ),
            ),
        ), 
'background_pb4d4824' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta',
                ),
            ),
        ), 
'border_24b8i624' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta ',
                ),
            ),

        ), 
'dimension_4lwgcl24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta { border-radius: {{dimension_4lwgcl24}}}',
                ),
            ),
        ), 
'boxshadow_2tmaij24' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta ',
                    ),
                ),
            ), 
'border_z0pxba24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta:hover',
                ),
            ),

        ), 
'boxshadow_f37dfv24' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-magic-hover-cta:hover',
                    ),
                ),
            ), 


    
                ],
                'style'         => 'wbuilder-cgb-style_c2xat025-css',
                'editor_script' => 'wbuilder-cgb-block_4k1ts425-js',
                'render_callback' => 'wkit_render_callback_yv6bfe25'
            )
        );
    
    }
    add_action( 'init', 'wb_Magic_Hover_CTA_c5zodj25' );
    
    function wkit_render_callback_yv6bfe25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }