<?php
    function wb_Profile_Card_Hover_Effect_a86ns424() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            

            
        }

        wp_register_script('wbuilder-cgb-block_n2ydyx25-js', WDKIT_SERVER_PATH .'/gutenberg/Profile-Card-Hover-Effect_a86ns424/Profile_Card_Hover_Effect_a86ns424.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.221299', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_vpxmo625-css', WDKIT_SERVER_PATH .'/gutenberg/Profile-Card-Hover-Effect_a86ns424/Profile_Card_Hover_Effect_a86ns424.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.630373', false );
    
        register_block_type(
            'wdkit/wb-a86ns424', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'text_itcils23' => array(
            'type' => 'string',
            'default' => 'John Carter'
        ),
'text_gh3meu23' => array(
            'type' => 'string',
            'default' => 'Developer'
        ),
'media_dsfmhh23' => array(
            'type' => 'object',
            'default' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/uploads/sites/67/2024/05/placeholder-614.png',
                'Id' => '',
            ),
        ), 
'repeater_wwg5yt23' => array(
            'type' => 'array',
            'repeaterField' => array(
                (object) array(
                    'iconscontrol_br23qo23' => array(
            'type' => 'string',
            'default'=> 'fas fa-globe',
        ), 
'url_ualqto23' => array(
            'type' => 'object',
            'default' => array(
                'url' => '#',
                'target' => true,
                'nofollow' => 'no-follow'
            ),

        ),
'text_6a7np223' => array(
            'type' => 'string',
            'default' => 'Wdesignkit'
        ),

                )
            ),
            'default' => array(array(
 '_key' => '0', 
'iconscontrol_br23qo23' => 'fas fa-globe', 
'url_ualqto23' => array(
                    'url' => '#',
                    'target' => true,
                    'nofollow' => 'no-follow'
                ),'text_6a7np223' => esc_html__('Wdesignkit', 'wdesignkit'), 
),),
        ), 
'dimension_6n2wp424' => array(
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
                    'selector' => '{{PLUS_WRAP}} .profile-card-heading{ padding: {{dimension_6n2wp424}}}',
                ),
            ),
        ), 
'typography_uewobu23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .profile-card-heading',
                ),
            ),
        ), 
'color_1rzvkb23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .profile-card-heading{color:{{color_1rzvkb23}};}',
                ),
            ),
        ), 
'dimension_fyp4yb23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .profile-card-heading-sub-title{ padding: {{dimension_fyp4yb23}}}',
                ),
            ),
        ), 
'typography_bt4s6s23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .profile-card-heading-sub-title',
                ),
            ),
        ), 
'color_p77afe23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .profile-card-heading-sub-title{color:{{color_p77afe23}};}',
                ),
            ),
        ), 
'background_2lvv3t23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .profile-card-heading-sub-title',
                ),
            ),
        ), 
'border_7ejwlv23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .profile-card-heading-sub-title',
                ),
            ),

        ), 
'dimension_sj8v2423' => array(
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
                    'selector' => '{{PLUS_WRAP}} .profile-card-heading-sub-title{ border-radius: {{dimension_sj8v2423}}}',
                ),
            ),
        ), 
'slider_ssl32e24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .profile-card-icons .profile-icons .tp-title-icon i{font-size:{{slider_ssl32e24}};}',
                ),
            ),
        ), 
'color_h9jgtx23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-profile-card-image .tp-title-icon i{color:{{color_h9jgtx23}};}',
                ),
            ),
        ), 
'color_mvck9q23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .profile-icons:hover .tp-title-icon i{color:{{color_mvck9q23}};}',
                ),
            ),
        ), 

'dimension_xm2z6j23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .tooltiptext{ padding: {{dimension_xm2z6j23}}}',
                ),
            ),
        ), 
'typography_tu3dr323' => array(
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
                    'selector' => '{{PLUS_WRAP}} .tooltiptext',
                ),
            ),
        ), 
'color_d1idf623' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .tooltiptext::after{border-right-color:{{color_d1idf623}};}',
                ),
            ),
        ), 
'color_5yh2z523' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .tooltiptext{color:{{color_5yh2z523}};}',
                ),
            ),
        ), 
'color_mxxin023' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .tooltiptext{background-color:{{color_mxxin023}};}',
                ),
            ),
        ), 
'dimension_z26tl023' => array(
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
                    'selector' => '{{PLUS_WRAP}} .tooltiptext{ border-radius: {{dimension_z26tl023}}}',
                ),
            ),
        ), 
'boxshadow_44qzzr23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .tooltiptext',
                    ),
                ),
            ), 
'dimension_arsr5s23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-profile-card-image{ padding: {{dimension_arsr5s23}}}',
                ),
            ),
        ), 
'slider_5mvodt24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-profile-card-inner{width:{{slider_5mvodt24}};}',
                ),
            ),
        ), 
'slider_g1famz24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-profile-card-inner{height:{{slider_g1famz24}};}',
                ),
            ),
        ), 
'color_rh9fgn23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-profile-card-inner{background-color:{{color_rh9fgn23}};}',
                ),
            ),
        ), 
'color_qfaxuq23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-profile-card-inner:hover{background-color:{{color_qfaxuq23}};}',
                ),
            ),
        ), 

'border_we7ov125' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-profile-card-border',
                ),
            ),

        ), 
'dimension_0bbgj425' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-profile-card-border{ border-radius: {{dimension_0bbgj425}}}',
                ),
            ),
        ), 
'border_0578wv25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-profile-card-inner:hover .wkit-profile-card-border',
                ),
            ),

        ), 

'border_x678uj25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-profile-card-inner',
                ),
            ),

        ), 
'dimension_v9zx8u25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-profile-card-inner{ border-radius: {{dimension_v9zx8u25}}}',
                ),
            ),
        ), 
'boxshadow_scnpbl25' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-profile-card-inner',
                    ),
                ),
            ), 
'border_ajb40e25' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-profile-card-inner:hover',
                ),
            ),

        ), 
'boxshadow_ozmpjn25' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-profile-card-inner:hover',
                    ),
                ),
            ), 


    
                ],
                'style'         => 'wbuilder-cgb-style_vpxmo625-css',
                'editor_script' => 'wbuilder-cgb-block_n2ydyx25-js',
                'render_callback' => 'wkit_render_callback_ktae7x25'
            )
        );
    
    }
    add_action( 'init', 'wb_Profile_Card_Hover_Effect_a86ns424' );
    
    function wkit_render_callback_ktae7x25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }