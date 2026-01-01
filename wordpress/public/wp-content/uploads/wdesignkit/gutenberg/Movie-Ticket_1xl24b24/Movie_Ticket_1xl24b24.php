<?php
    function wb_Movie_Ticket_1xl24b24() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            

            
        }

        wp_register_script('wbuilder-cgb-block_47l0u425-js', WDKIT_SERVER_PATH .'/gutenberg/Movie-Ticket_1xl24b24/Movie_Ticket_1xl24b24.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.61448', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_4c7yp925-css', WDKIT_SERVER_PATH .'/gutenberg/Movie-Ticket_1xl24b24/Movie_Ticket_1xl24b24.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.53755', false );
    
        register_block_type(
            'wdkit/wb-1xl24b24', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'media_5ew97023' => array(
            'type' => 'object',
            'default' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/uploads/sites/67/2024/05/placeholder-614.png',
                'Id' => '',
            ),
        ), 
'text_nj60eg23' => array(
            'type' => 'string',
            'default' => 'CinemaPass'
        ),
'text_l04wm823' => array(
            'type' => 'string',
            'default' => 'Your Ticket to the Best Seat in the House.'
        ),
'text_3vks9823' => array(
            'type' => 'string',
            'default' => '200'
        ),
'text_q1iku523' => array(
            'type' => 'string',
            'default' => '400'
        ),
'text_18lpdk23' => array(
            'type' => 'string',
            'default' => 'Get More'
        ),
'url_tt1jdu23' => array(
            'type' => 'object',
            'default' => array(
                'url' => '#',
                'target' => true,
                'nofollow' => 'no-follow'
            ),

        ),
'slider_el20dn23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .movie-ticket-poster{width:{{slider_el20dn23}};}',
                ),
            ),
        ), 
'slider_da86n223' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => '%',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}}  .movie-poster-image{height:{{slider_da86n223}};}',
                ),
            ),
        ), 
'slider_3gpds123' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .movie-ticket-poster{left:{{slider_3gpds123}};}',
                ),
            ),
        ), 
'border_7qfj5523' => array(
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
                    'selector' => '{{PLUS_WRAP}} .movie-ticket-poster .movie-poster-image',
                ),
            ),

        ), 
'dimension_5lo1ji23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .movie-ticket-poster .movie-poster-image{ border-radius: {{dimension_5lo1ji23}}}',
                ),
            ),
        ), 
'boxshadow_kx0prm23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .movie-ticket-poster .movie-poster-image',
                    ),
                ),
            ), 
'dimension_c6ix7t23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .ticket-content .ticket-title{ padding: {{dimension_c6ix7t23}}}',
                ),
            ),
        ), 
'typography_jndkwi23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .ticket-title',
                ),
            ),
        ), 
'color_x7r6v825' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .ticket-container .ticket-title{color:{{color_x7r6v825}};}',
                ),
            ),
        ), 
'dimension_08bop623' => array(
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
                    'selector' => '{{PLUS_WRAP}} .ticket-content .ticket-slogan{ padding: {{dimension_08bop623}}}',
                ),
            ),
        ), 
'typography_jhb47r23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .ticket-content .ticket-slogan',
                ),
            ),
        ), 
'color_yy2n0a23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .ticket-content .ticket-slogan{color:{{color_yy2n0a23}};}',
                ),
            ),
        ), 
'choose_nb8bjc23' => array(
            'type' => 'string',
                'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .ticket-content .ticket-inner{ flex-direction: {{choose_nb8bjc23}}; }',
                ),
            ),
        ), 
'dimension_1yoc6h23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .ticket-inner  .ticket-current-price{ padding: {{dimension_1yoc6h23}}}',
                ),
            ),
        ), 
'typography_7uujav23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .ticket-current-price',
                ),
            ),
        ), 
'color_bclpzw23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .ticket-current-price{color:{{color_bclpzw23}};}',
                ),
            ),
        ), 
'dimension_mlzfrb23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .ticket-inner .ticket-old-price{ padding: {{dimension_mlzfrb23}}}',
                ),
            ),
        ), 
'typography_jlm39a23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .ticket-old-price',
                ),
            ),
        ), 
'color_3lj9q123' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .ticket-old-price{color:{{color_3lj9q123}};}',
                ),
            ),
        ), 
'dimension_06cbx123' => array(
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
                    'selector' => '{{PLUS_WRAP}} .ticket-content .ticket-btn-link{ padding: {{dimension_06cbx123}}}',
                ),
            ),
        ), 
'typography_cta7no23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .ticket-btn-text',
                ),
            ),
        ), 
'color_ocrj2723' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .ticket-btn-text{color:{{color_ocrj2723}};}',
                ),
            ),
        ), 
'color_q337ix23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .ticket-content .ticket-btn-link{background-color:{{color_q337ix23}};}',
                ),
            ),
        ), 
'border_cjr70k24' => array(
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
                    'selector' => '{{PLUS_WRAP}} .ticket-content .ticket-btn-link',
                ),
            ),

        ), 
'dimension_pqd9mk23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .ticket-content .ticket-btn-link{ border-radius: {{dimension_pqd9mk23}}}',
                ),
            ),
        ), 
'dimension_ah2cyr23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .ticket-container .ticket-content { padding: {{dimension_ah2cyr23}}}',
                ),
            ),
        ), 
'slider_wwmma923' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .movie-ticket-container{width:{{slider_wwmma923}};}',
                ),
            ),
        ), 
'slider_9fdm6h23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .movie-ticket-container{height:{{slider_9fdm6h23}};}',
                ),
            ),
        ), 
'slider_qs9qfk23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .ticket-container{top:{{slider_qs9qfk23}};}',
                ),
            ),
        ), 
'background_li9rdk23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .ticket-container',
                ),
            ),
        ), 
'border_qmcgik23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .ticket-container',
                ),
            ),

        ), 
'dimension_4ti40q23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .ticket-container{ border-radius: {{dimension_4ti40q23}}}',
                ),
            ),
        ), 
'boxshadow_zszd4w23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .ticket-container',
                    ),
                ),
            ), 

    
                ],
                'style'         => 'wbuilder-cgb-style_4c7yp925-css',
                'editor_script' => 'wbuilder-cgb-block_47l0u425-js',
                'render_callback' => 'wkit_render_callback_he4p4k25'
            )
        );
    
    }
    add_action( 'init', 'wb_Movie_Ticket_1xl24b24' );
    
    function wkit_render_callback_he4p4k25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }