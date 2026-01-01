<?php
    function wb_Horizontal_Level_tb0xmk24() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            
            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_externalu06uzb25-js", WDKIT_SERVER_PATH . '/gutenberg/Horizontal-Level_tb0xmk24/index.js', array('jquery'),'2.0.12.810122', true);}

            wp_enqueue_style( 'wd_css_ex_1_yakf0z25', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css', false, '2.0.12.658268', 'all' );
wp_enqueue_style( 'wd_css_ex_1_u2tehu25', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', false, '2.0.12.687061', 'all' );

        }

        wp_register_script('wbuilder-cgb-block_u06uzb25-js', WDKIT_SERVER_PATH .'/gutenberg/Horizontal-Level_tb0xmk24/Horizontal_Level_tb0xmk24.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.0.12.329130', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_kagspp25-css', WDKIT_SERVER_PATH .'/gutenberg/Horizontal-Level_tb0xmk24/Horizontal_Level_tb0xmk24.css', is_admin() ? array( 'wp-editor' ) : null, '2.0.12.242752', false );
    
        register_block_type(
            'wdkit/wb-tb0xmk24', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'select_abg0cy23' => array(
            'type' => 'string',
            'default' => 'style-1'
        ),
'repeater_9qo8go23' => array(
            'type' => 'array',
            'repeaterField' => array(
                (object) array(
                    'select_fstai223' => array(
            'type' => 'string',
            'default' => 'text'
        ),
'text_83fo9323' => array(
            'type' => 'string',
            'default' => 'Acquire'
        ),
'iconscontrol_391bl823' => array(
            'type' => 'string',
            'default'=> 'fas fa-home',
        ), 
'media_7czjjy23' => array(
            'type' => 'object',
            'default' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                'Id' => '',
            ),
        ), 
'wysiwyg_y0zki523' => array(
            'type' => 'string',
            'default' => 'Discover all available features in WDesignKit.',
        ),
'switcher_vdtbtv23' => array(
            'type' => 'boolean',
                'default' => false,
        ), 
'heading_ih8txk23' => array(
            'type' => 'string',
        ), 
'color_3gtq5223' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper {{TP_REPEAT_ID}} .wkit-hz-step-title{color:{{color_3gtq5223}};}',
                ),
            ),
        ), 
'color_kvwfob23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper {{TP_REPEAT_ID}} .wkit-hz-progress-icon{color:{{color_kvwfob23}};}',
                ),
            ),
        ), 
'color_cpt8q623' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper {{TP_REPEAT_ID}} .wkit-hz-step-desc{color:{{color_cpt8q623}};}',
                ),
            ),
        ), 
'heading_zp6dmt23' => array(
            'type' => 'string',
        ), 
'color_fdwdr923' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper {{TP_REPEAT_ID}} .wkit-hz-step-title:hover{color:{{color_fdwdr923}};}',
                ),
            ),
        ), 
'color_id73mt23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper {{TP_REPEAT_ID}} .wkit-hz-progress-icon:hover{color:{{color_id73mt23}};}',
                ),
            ),
        ), 
'color_czo5ce23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper {{TP_REPEAT_ID}} .wkit-hz-step-desc:hover{color:{{color_czo5ce23}};}',
                ),
            ),
        ), 
'color_5iswbp23' => array(
            'type' => 'string',
            'default' => '#808080',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper {{TP_REPEAT_ID}} .wkit-hz-pix-levels-dot{background-color:{{color_5iswbp23}};}',
                ),
            ),
        ), 
'color_2f4wn225' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper {{TP_REPEAT_ID}}.wkit-hz-active-yes .wkit-hz-progress-icon{color:{{color_2f4wn225}};}',
                ),
            ),
        ), 
'color_xazzfl23' => array(
            'type' => 'string',
            'default' => '#1818e8',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper {{TP_REPEAT_ID}}.wkit-hz-active-yes .wkit-hz-pix-levels-dot{background-color:{{color_xazzfl23}};}',
                ),
            ),
        ), 
'color_rpbmum23' => array(
            'type' => 'string',
            'default' => '#000000',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper {{TP_REPEAT_ID}} .wkit-hz-pix-levels-dot-inner{background-color:{{color_rpbmum23}};}',
                ),
            ),
        ), 
'color_ybqtct23' => array(
            'type' => 'string',
            'default' => '#ffffff',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper {{TP_REPEAT_ID}}.wkit-hz-active-yes .wkit-hz-pix-levels-dot-inner{background-color:{{color_ybqtct23}};}',
                ),
            ),
        ), 
'color_73oeht23' => array(
            'type' => 'string',
            'default' => '#808080',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .wkit-bg-gray-2,{{PLUS_WRAP}} {{TP_REPEAT_ID}} .wkit-hz-bg-gradient-primary{background-color:{{color_73oeht23}};}',
                ),
            ),
        ), 
'color_2gfe2b23' => array(
            'type' => 'string',
            'default' => '#1818e8',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.wkit-hz-active-yes .wkit-bg-gray-2,{{PLUS_WRAP}} {{TP_REPEAT_ID}}.wkit-hz-active-yes .wkit-hz-bg-gradient-primary{background-color:{{color_2gfe2b23}};}',
                ),
            ),
        ), 

                )
            ),
            'default' => array(array(
 '_key' => '0', 
'select_fstai223' => 'text',
'text_83fo9323' => esc_html__('Acquire', 'wdesignkit'), 
'iconscontrol_391bl823' => 'fas fa-home', 
'media_7czjjy23' => array(
                'url' => 'https://gtemplates.wdesignkit.com/widgets/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                'Id' => '',
            ),'wysiwyg_y0zki523' => esc_html__('Discover all available features in WDesignKit.', 'wdesignkit'), 
'switcher_vdtbtv23' => false,
'heading_ih8txk23' => esc_html__('', 'wdesignkit'), 
'color_3gtq5223' => '', 
'color_kvwfob23' => '', 
'color_cpt8q623' => '', 
'heading_zp6dmt23' => esc_html__('', 'wdesignkit'), 
'color_fdwdr923' => '', 
'color_id73mt23' => '', 
'color_czo5ce23' => '', 
'color_5iswbp23' => '#808080', 
'color_2f4wn225' => '', 
'color_xazzfl23' => '#1818e8', 
'color_rpbmum23' => '#000000', 
'color_ybqtct23' => '#ffffff', 
'color_73oeht23' => '#808080', 
'color_2gfe2b23' => '#1818e8', 
),),
        ), 
'select_9cj4zf23' => array(
            'type' => 'string',
            'default' => 'dcl-4'
        ),
'select_595scc23' => array(
            'type' => 'string',
            'default' => 'tcl-3'
        ),
'select_rnc7lt23' => array(
            'type' => 'string',
            'default' => 'mcl-2'
        ),
'slider_l41s3h24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => '%',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hz-step-content.wkit-hz-pix-levels-step{opacity:{{slider_l41s3h24}};}',
                ),
            ),
        ), 
'slider_x9qrcx24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => '%',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-step-content.wkit-hz-pix-levels-step.wkit-hz-active-yes{opacity:{{slider_x9qrcx24}};}',
                ),
            ),
        ), 

'dimension_ppyvov23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-step-title{ padding: {{dimension_ppyvov23}}}',
                ),
            ),
        ), 
'dimension_yiohpe23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-step-title{ margin: {{dimension_yiohpe23}}}',
                ),
            ),
        ), 
'slider_k0pgbq25' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-text .wkit-hz-step-title{margin-top:{{slider_k0pgbq25}};}',
                ),
            ),
        ), 
'slider_szqvmo25' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-text .wkit-hz-step-title{margin-bottom:{{slider_szqvmo25}};}',
                ),
            ),
        ), 
'typography_j2tc9o23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-step-title',
                ),
            ),
        ), 
'color_csrfiz23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-text .wkit-hz-step-title{color:{{color_csrfiz23}};}',
                ),
            ),
        ), 
'color_sjl3vk23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-text .wkit-hz-step-title:hover{color:{{color_sjl3vk23}};}',
                ),
            ),
        ), 
'color_0ifsza23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-hz-text.wkit-hz-active-yes .wkit-hz-step-title{color:{{color_0ifsza23}};}',
                ),
            ),
        ), 

'dimension_f3d64j23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-step-desc{ padding: {{dimension_f3d64j23}}}',
                ),
            ),
        ), 
'dimension_hw0owm23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-step-desc{ margin: {{dimension_hw0owm23}}}',
                ),
            ),
        ), 
'typography_colu3g23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-step-desc',
                ),
            ),
        ), 
'color_07s30123' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-step-desc{color:{{color_07s30123}};}',
                ),
            ),
        ), 
'color_4wstaj23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-step-desc:hover{color:{{color_4wstaj23}};}',
                ),
            ),
        ), 
'color_pjmfoh23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-active-yes .wkit-hz-step-desc{color:{{color_pjmfoh23}};}',
                ),
            ),
        ), 

'slider_ijas5623' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-step-content .wkit-hz-progress-icon i{font-size:{{slider_ijas5623}};}',
                ),
            ),
        ), 
'slider_2lvnux25' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-icon .wkit-hz-progress-icon{margin-top:{{slider_2lvnux25}};}',
                ),
            ),
        ), 
'slider_ig6lr025' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-icon .wkit-hz-progress-icon{margin-bottom:{{slider_ig6lr025}};}',
                ),
            ),
        ), 
'color_1y7tk223' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-step-content .wkit-hz-progress-icon i{color:{{color_1y7tk223}};}',
                ),
            ),
        ), 
'color_cgtr8b23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-step-content:hover .wkit-hz-progress-icon i{color:{{color_cgtr8b23}};}',
                ),
            ),
        ), 

'slider_12dfcq23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-step-content .wkit-hz-progress-img{width:{{slider_12dfcq23}};}',
                ),
            ),
        ), 
'slider_qmpnwd23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-step-content.wkit-hz-image .wkit-hz-progress-img .tp-hz-title-image{width:{{slider_qmpnwd23}};}',
                ),
            ),
        ), 
'slider_55eqx325' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-image .wkit-hz-progress-img{margin-top:{{slider_55eqx325}};}',
                ),
            ),
        ), 
'slider_ja6oq825' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-image .wkit-hz-progress-img{margin-bottom:{{slider_ja6oq825}};}',
                ),
            ),
        ), 
'slider_msm9fl24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-pix-levels-dot{width:{{slider_msm9fl24}};}',
                ),
            ),
        ), 
'slider_tto3tx24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-pix-levels-dot{height:{{slider_tto3tx24}};}',
                ),
            ),
        ), 
'slider_iunpeg24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-pix-levels-dot-inner{width:{{slider_iunpeg24}};}',
                ),
            ),
        ), 
'slider_3gaoy324' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-pix-levels-dot-inner{height:{{slider_3gaoy324}};}',
                ),
            ),
        ), 
'slider_2mh27d24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-pix-levels-dot-inner{top:{{slider_2mh27d24}};}',
                ),
            ),
        ), 
'slider_00pqga24' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-pix-levels-dot-inner{left:{{slider_00pqga24}};}',
                ),
            ),
        ), 
'heading_l6skcp23' => array(
            'type' => 'string',
        ), 
'color_phbrh123' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-pix-levels-dot{background-color:{{color_phbrh123}};}',
                ),
            ),
        ), 
'heading_coxi2v23' => array(
            'type' => 'string',
        ), 
'color_cb9phj23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-pix-levels-dot-inner{background-color:{{color_cb9phj23}};}',
                ),
            ),
        ), 
'heading_8t3wco23' => array(
            'type' => 'string',
        ), 
'color_dgdb5r23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-active-yes .wkit-hz-pix-levels-dot{background-color:{{color_dgdb5r23}};}',
                ),
            ),
        ), 
'heading_u4z29t23' => array(
            'type' => 'string',
        ), 
'color_u6uwgr23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-active-yes .wkit-hz-pix-levels-dot-inner{background-color:{{color_u6uwgr23}};}',
                ),
            ),
        ), 

'slider_n1w3iv23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-inner-style-3 .wkit-hz-pix-levels-dot-inner.wkit-hz-check-mark:before{font-size:{{slider_n1w3iv23}};}',
                ),
            ),
        ), 
'color_qel2yq25' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-inner-style-3 .wkit-hz-pix-levels-dot-inner.wkit-hz-check-mark:hover:before{color:{{color_qel2yq25}};}',
                ),
            ),
        ), 
'color_mknsfl25' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-inner-style-3 .wkit-hz-pix-levels-dot-inner.wkit-hz-check-mark:before{color:{{color_mknsfl25}};}',
                ),
            ),
        ), 

'slider_y7vfau23' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-inner-style-2 .wkit-prg-two:before{font-size:{{slider_y7vfau23}};}',
                ),
            ),
        ), 
'slider_k1755025' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => ,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-inner-style-2 .wkit-prg-two:before{top:{{slider_k1755025}};}',
                ),
            ),
        ), 
'color_e953u023' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-inner-style-2 .wkit-prg-two:before{color:{{color_e953u023}};}',
                ),
            ),
        ), 
'color_frajh523' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-inner-style-2 .wkit-prg-two:hover:before, .wkit-multi-step-hz-wrapper .wkit-hz-inner-style-2 .wkit-hz-active-yes .wkit-prg-two:hover:before{color:{{color_frajh523}};}',
                ),
            ),
        ), 
'color_7yc8dx23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-inner-style-2 .wkit-hz-active-yes .wkit-prg-two:before{color:{{color_7yc8dx23}};}',
                ),
            ),
        ), 

'slider_xz0rw923' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => 8,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-pix-levels-step .progress{height:{{slider_xz0rw923}};}',
                ),
            ),
        ), 
'color_8sg7hj23' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-bg-gray-2,.wkit-multi-step-hz-wrapper .wkit-hz-bg-gradient-primary{background-color:{{color_8sg7hj23}};}',
                ),
            ),
        ), 
'color_y4257t25' => array(
            'type' => 'string',
            'default' => '',
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-active-yes .wkit-bg-gray-2,{{PLUS_WRAP}} .wkit-hz-active-yes .wkit-hz-bg-gradient-primary{background-color:{{color_y4257t25}};}',
                ),
            ),
        ), 

'dimension_y4lx1023' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-content-bg{ padding: {{dimension_y4lx1023}}}',
                ),
            ),
        ), 
'dimension_oyasnz23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-content-bg{ margin: {{dimension_oyasnz23}}}',
                ),
            ),
        ), 
'background_27rlp723' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-step-content',
                ),
            ),
        ), 
'border_yp8c5723' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-content-bg',
                ),
            ),

        ), 
'dimension_4cxerm23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-content-bg{ border-radius: {{dimension_4cxerm23}}}',
                ),
            ),
        ), 
'boxshadow_4z5n1c23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper .wkit-hz-content-bg',
                    ),
                ),
            ), 
'background_n6jf3h23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper:hover .wkit-hz-step-content',
                ),
            ),
        ), 
'border_e5gwsa23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper:hover .wkit-hz-content-bg',
                ),
            ),

        ), 
'boxshadow_9wzyqt23' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-multi-step-hz-wrapper:hover .wkit-hz-content-bg',
                    ),
                ),
            ), 


    
                ],
                'style'         => 'wbuilder-cgb-style_kagspp25-css',
                'editor_script' => 'wbuilder-cgb-block_u06uzb25-js',
                'render_callback' => 'wkit_render_callback_7wqowu25'
            )
        );
    
    }
    add_action( 'init', 'wb_Horizontal_Level_tb0xmk24' );
    
    function wkit_render_callback_7wqowu25($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }