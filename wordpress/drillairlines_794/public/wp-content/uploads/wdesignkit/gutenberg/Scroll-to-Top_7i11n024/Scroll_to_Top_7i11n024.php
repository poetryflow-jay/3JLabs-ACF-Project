<?php
    function wb_Scroll_to_Top_7i11n024() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_externalgfqa0c25-js", WDKIT_SERVER_PATH . '/gutenberg/Scroll-to-Top_7i11n024/index.js', [],'2.0.12.898982', true);}

            
        }

        wp_register_script('wbuilder-cgb-block_gfqa0c25-js', WDKIT_SERVER_PATH .'/gutenberg/Scroll-to-Top_7i11n024/Scroll_to_Top_7i11n024.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.659849', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_b4g0pr25-css', WDKIT_SERVER_PATH .'/gutenberg/Scroll-to-Top_7i11n024/Scroll_to_Top_7i11n024.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.982950', false );
    
        register_block_type(
            'wdkit/wb-7i11n024', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'text_84wm4g23' => array(
            'type' => 'string',
            'default' => 'Back to Top'
        ),
'select_llua2q23' => array(
            'type' => 'string',
            'default' => 'icon'
        ),
'select_f8h0cz23' => array(
            'type' => 'string',
            'default' => 'after-text'
        ),
'select_lwh4n325' => array(
            'type' => 'string',
            'default' => 'inline'
        ),
'iconscontrol_4w93sa23' => array(
            'type' => 'string',
            'default'=> 'fas fa-arrow-right',
        ), 
'media_7uav9v23' => array(
            'type' => 'object',
            'default' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                'Id' => '',
            ),
        ), 
'select_j6s46n23' => array(
            'type' => 'string',
            'default' => 'horizontal'
        ),
'choose_1c8ct825' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop-main{ justify-content: {{choose_1c8ct825}}; }',
                ),
            ),
        ), 
'select_7r268k24' => array(
            'type' => 'string',
            'default' => 'body'
        ),
'text_9yzuwi24' => array(
            'type' => 'string',
            'default' => ''
        ),
'select_qf9zw924' => array(
            'type' => 'string',
            'default' => 'relative'
        ),
'heading_4b4qzx24' => array(
            'type' => 'string',
        ), 
'number_pv2byx23' => array(
        'type' => 'string',
        'default' => '100',
        
    ),
'number_7bs6qp23' => array(
        'type' => 'string',
        'default' => '50',
        
    ),
'number_ao6cgb23' => array(
        'type' => 'string',
        'default' => '25',
        
    ),
'heading_3zyj7o24' => array(
            'type' => 'string',
        ), 
'slider_novwyy23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop-main img.tp-title-icon{width:{{slider_novwyy23}};}',
                ),
            ),
        ), 
'slider_tum7ix23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop-main .tp-title-icon{height:{{slider_tum7ix23}};}',
                ),
            ),
        ), 
'slider_sw0lj923' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop-main .tp-title-icon{font-size:{{slider_sw0lj923}};}',
                ),
            ),
        ), 
'color_rbsesr23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop-main .tp-title-icon{color:{{color_rbsesr23}};}',
                ),
            ),
        ), 
'background_qmlxm923' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop-main .tp-title-icon',
                ),
            ),
        ), 
'border_wcck2y25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop-main .tp-title-icon',
                ),
            ),

        ), 
'dimension_ip34nn23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop-main .tp-title-icon{ border-radius: {{dimension_ip34nn23}}}',
                ),
            ),
        ), 
'color_13qqje23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop:hover .tp-title-icon{color:{{color_13qqje23}};}',
                ),
            ),
        ), 
'background_dg1yyt23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop:hover .tp-title-icon',
                ),
            ),
        ), 
'border_h1qezt25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop:hover .tp-title-icon',
                ),
            ),

        ), 
'dimension_jj2irr23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop:hover .tp-title-icon{ border-radius: {{dimension_jj2irr23}}}',
                ),
            ),
        ), 

'typography_yhmtpu23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop',
                ),
            ),
        ), 
'choose_56a2em24' => array(
            'type' => 'object',
                'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  'column' ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .pos-relative{ justify-content: {{choose_56a2em24}}; }',
                ),
            ),
        ), 
'slider_nn09fl24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .pos-fixed .wkit-scrolltotop{top:{{slider_nn09fl24}};}',
                ),
            ),
        ), 
'slider_nv168324' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .pos-fixed .wkit-scrolltotop{left:{{slider_nv168324}};}',
                ),
            ),
        ), 
'slider_bvoss823' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop{width:{{slider_bvoss823}};}',
                ),
            ),
        ), 
'slider_0xu7cs23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop{height:{{slider_0xu7cs23}};}',
                ),
            ),
        ), 
'slider_lk6xgl23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop-main .wkit-scrolltotop{gap:{{slider_lk6xgl23}};}',
                ),
            ),
        ), 
'dimension_94pekm23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop-main .wkit-scrolltotop{ padding: {{dimension_94pekm23}}}',
                ),
            ),
        ), 
'color_8y24hh23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop-main .wkit-scrolltotop{color:{{color_8y24hh23}};}',
                ),
            ),
        ), 
'background_t0baor23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop-main .wkit-scrolltotop',
                ),
            ),
        ), 
'border_6m6kaq25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop-main .wkit-scrolltotop',
                ),
            ),

        ), 
'dimension_j9uani23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop-main .wkit-scrolltotop{ border-radius: {{dimension_j9uani23}}}',
                ),
            ),
        ), 
'boxshadow_y7k2qr23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop-main .wkit-scrolltotop',
                    ),
                ),
            ), 
'color_ylaper23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop-main .wkit-scrolltotop:hover{color:{{color_ylaper23}};}',
                ),
            ),
        ), 
'background_17oavs23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop-main .wkit-scrolltotop:hover',
                ),
            ),
        ), 
'border_hmeirc25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop-main .wkit-scrolltotop:hover',
                ),
            ),

        ), 
'dimension_t76rta23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop-main .wkit-scrolltotop:hover{ border-radius: {{dimension_t76rta23}}}',
                ),
            ),
        ), 
'boxshadow_vw9akj23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-scrolltotop-main .wkit-scrolltotop:hover',
                    ),
                ),
            ), 


    
                ],
                'style'         => 'wbuilder-cgb-style_b4g0pr25-css',
                'editor_script' => 'wbuilder-cgb-block_gfqa0c25-js',
                'render_callback' => 'wkit_render_callback_0hcolw25'
            )
        );
    
    }
    add_action( 'init', 'wb_Scroll_to_Top_7i11n024' );
    
    function wkit_render_callback_0hcolw25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }