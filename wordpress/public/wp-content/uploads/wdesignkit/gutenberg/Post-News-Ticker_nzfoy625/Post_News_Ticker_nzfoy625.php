<?php
    function wb_Post_News_Ticker_nzfoy625() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_externalpe3tp125-js", WDKIT_SERVER_PATH . '/gutenberg/Post-News-Ticker_nzfoy625/index.js', [],'2.1.0.795435', true);}

            
        }

        wp_register_script('wbuilder-cgb-block_pe3tp125-js', WDKIT_SERVER_PATH .'/gutenberg/Post-News-Ticker_nzfoy625/Post_News_Ticker_nzfoy625.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.1.0.73846', true
        );

        /**
            * Get post type list.
            *
            * @since 1.0.34
            */
            function set_options_nzfoy625()
            {
                $ctp = array();
                $order_list = array();
                $cpt_array = array();
                $order_array = array();

                if(class_exists('Wdkit_Dynamic_Listing_Files')){
                    $wdkit_widget = new Wdkit_Dynamic_Listing_Files();
        
                    if(method_exists($wdkit_widget, 'Get_post_list')){
                        $ctp = $wdkit_widget->Get_post_list();
                    }
        
                    if(method_exists($wdkit_widget, 'Get_orderBy_List')){
                        $order_list = $wdkit_widget->Get_orderBy_List();
                    }
                }

                foreach ($ctp as $key => $value) {
                    array_push($cpt_array, array( $value['name'],  $value['label'] ));
                }
                
                foreach ($order_list as $key => $value) {
                    array_push($order_array, array( $value['name'],  $value['label'] ));
                }

                return array(
                    'post_list' => $cpt_array,
                    'order_by' => $order_array,
                );
            }
    
            wp_localize_script(
                'wbuilder-cgb-block_pe3tp125-js',
                'wdkit_post_type', // The object name that will be available in JavaScript
                set_options_nzfoy625()
            );

        
    
        wp_register_style('wbuilder-cgb-style_7omfxg25-css', WDKIT_SERVER_PATH .'/gutenberg/Post-News-Ticker_nzfoy625/Post_News_Ticker_nzfoy625.css', is_admin() ? array( 'wp-editor' ) : null, '2.1.0.642669', false );
    
        register_block_type(
            'wdkit/wb-nzfoy625', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'select_zf67du24' => array(
            'type' => 'string',
            'default' => 'style-1'
        ),
'cpt_wblbl324' => array(
            'type' => 'string',
            'default' => 'post'
        ),

        
        'include_wblbl324' => array(
            'type' => 'string',
            'default' => ''
        ),
'exclude_wblbl324' => array(
            'type' => 'string',
            'default' => ''
        ),
'max_post_wblbl324' => array(
        'type' => 'string',
        'default' => '10',
        
    ),
'order_wblbl324' => array(
            'type' => 'string',
            'default' => 'desc'
        ),
'order_by_wblbl324' => array(
            'type' => 'string',
            'default' => 'date'
        ),
'max_cat_wblbl324' => array(
        'type' => 'string',
        'default' => '3',
        
    ),
'max_tag_wblbl324' => array(
        'type' => 'string',
        'default' => '3',
        
    ),

'text_lx4byh24' => array(
            'type' => 'string',
            'default' => 'Book Now'
        ),
'url_2ncmwl24' => array(
            'type' => 'object',
            'default' => array(
                'url' => '#',
                'target' => true,
                'nofollow' => 'no-follow'
            ),

        ),
'iconscontrol_41jkqf24' => array(
            'type' => 'string',
            'default'=> 'fas fa-arrow-left',
        ), 
'iconscontrol_91o61124' => array(
            'type' => 'string',
            'default'=> 'fas fa-arrow-right',
        ), 
'typography_gp54l824' => array(
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
                    'selector' => '{{PLUS_WRAP}} .w-slider-pnt .w-pntslider-content a',
                ),
            ),
        ), 
'color_wrjl2324' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-pnt-main-container .w-slider-pnt .wkit-post-slider-name{color:{{color_wrjl2324}};}',
                ),
            ),
        ), 
'color_wiclqb24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .w-pntslider-content .wkit-post-slider-name:hover{color:{{color_wiclqb24}};}',
                ),
            ),
        ), 

'dimension_ai6uxe24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container .wkit-pnt-news a{ padding: {{dimension_ai6uxe24}}}',
                ),
            ),
        ), 
'dimension_5owrw424' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container .wkit-pnt-news a{ margin: {{dimension_5owrw424}}}',
                ),
            ),
        ), 
'slider_vsvkp425' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container .wkit-pnt-news a{width:{{slider_vsvkp425}};}',
                ),
            ),
        ), 
'slider_0di8a525' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container .wkit-pnt-news a{height:{{slider_0di8a525}};}',
                ),
            ),
        ), 
'typography_dtc79d24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container .wkit-pnt-news a',
                ),
            ),
        ), 
'color_dul1x924' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container .wkit-pnt-news a{color:{{color_dul1x924}};}',
                ),
            ),
        ), 
'background_m90yb124' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container .wkit-pnt-news a',
                ),
            ),
        ), 
'border_c2a5zz25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container .wkit-pnt-news a',
                ),
            ),

        ), 
'dimension_zpk2vl24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container .wkit-pnt-news a{ border-radius: {{dimension_zpk2vl24}}}',
                ),
            ),
        ), 
'boxshadow_590ulz25' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container .wkit-pnt-news a',
                    ),
                ),
            ), 
'color_1yrvb424' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container .wkit-pnt-news a:hover{color:{{color_1yrvb424}};}',
                ),
            ),
        ), 
'background_ez26kn24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container .wkit-pnt-news a:hover',
                ),
            ),
        ), 
'border_ifpsy925' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container .wkit-pnt-news a:hover',
                ),
            ),

        ), 
'boxshadow_plgzuf25' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container .wkit-pnt-news a:hover',
                    ),
                ),
            ), 

'dimension_53il9m24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container .wkit-pnt-main-container .w-pnt-button .w-pnt-prevBtn, {{PLUS_WRAP}} .wkit-news-ticker-container .wkit-pnt-main-container .w-pnt-button .w-pnt-nextBtn{ padding: {{dimension_53il9m24}}}',
                ),
            ),
        ), 
'slider_8okhk024' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container  .wkit-pnt-main-container .w-pnt-button{gap:{{slider_8okhk024}};}',
                ),
            ),
        ), 
'slider_bsk1hb24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container .wkit-pnt-main-container  .tp-title-icon i{font-size:{{slider_bsk1hb24}};}',
                ),
            ),
        ), 
'color_74gcj224' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container .wkit-pnt-main-container .tp-title-icon i{color:{{color_74gcj224}};}',
                ),
            ),
        ), 
'background_92d34g24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container .wkit-pnt-main-container .w-pnt-button .w-pnt-prevBtn, {{PLUS_WRAP}} .wkit-news-ticker-container .wkit-pnt-main-container .w-pnt-button .w-pnt-nextBtn',
                ),
            ),
        ), 
'color_5aau1a24' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container .wkit-pnt-main-container .w-pnt-button .w-pnt-prevBtn:hover .tp-title-icon i, {{PLUS_WRAP}} .wkit-news-ticker-container .wkit-pnt-main-container .w-pnt-button .w-pnt-nextBtn:hover .tp-title-icon i{color:{{color_5aau1a24}};}',
                ),
            ),
        ), 
'background_5itug524' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container .wkit-pnt-main-container .w-pnt-button .w-pnt-prevBtn:hover, {{PLUS_WRAP}} .wkit-news-ticker-container .wkit-pnt-main-container .w-pnt-button .w-pnt-nextBtn:hover',
                ),
            ),
        ), 

'dimension_j7md5125' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-pnt-main-container{ padding: {{dimension_j7md5125}}}',
                ),
            ),
        ), 
'slider_yj60wm24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-pnt-main-container{height:{{slider_yj60wm24}};}',
                ),
            ),
        ), 
'background_u4h1kb24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container  .wkit-pnt-main-container',
                ),
            ),
        ), 
'border_57fid325' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container  .wkit-pnt-main-container',
                ),
            ),

        ), 
'dimension_fjv6nk24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container  .wkit-pnt-main-container{ border-radius: {{dimension_fjv6nk24}}}',
                ),
            ),
        ), 
'boxshadow_04x5yu24' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container  .wkit-pnt-main-container',
                    ),
                ),
            ), 
'border_772iem25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container  .wkit-pnt-main-container:hover',
                ),
            ),

        ), 
'boxshadow_10n2cg24' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-news-ticker-container  .wkit-pnt-main-container:hover',
                    ),
                ),
            ), 


    
                ],
                'style'         => 'wbuilder-cgb-style_7omfxg25-css',
                'editor_script' => 'wbuilder-cgb-block_pe3tp125-js',
                'render_callback' => 'wkit_render_callback_l4da9o25'
            )
        );
    
    }
    add_action( 'init', 'wb_Post_News_Ticker_nzfoy625' );
    
    function wkit_render_callback_l4da9o25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }