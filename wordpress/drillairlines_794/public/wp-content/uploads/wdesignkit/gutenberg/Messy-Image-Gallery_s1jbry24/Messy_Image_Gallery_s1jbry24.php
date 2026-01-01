<?php
    function wb_Messy_Image_Gallery_s1jbry24() { 

        if ( ( !empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) || empty( $_GET['action'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post-new.php' ) !== false || ! is_admin() ){
            wp_enqueue_script( 'wd_ex_script_vfwe2p25', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js', [], '2.1.0.222567', true );

            

            if ( ! is_admin() ) { wp_enqueue_script("wbuilder-cgb-block_external8ntkj125-js", WDKIT_SERVER_PATH . '/gutenberg/Messy-Image-Gallery_s1jbry24/index.js', [],'2.1.0.245859', true);}

            
        }

        wp_register_script('wbuilder-cgb-block_8ntkj125-js', WDKIT_SERVER_PATH .'/gutenberg/Messy-Image-Gallery_s1jbry24/Messy_Image_Gallery_s1jbry24.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wkit-editor-block-pmgc' ), '2.1.0.349700', true
        );

        

        
    
        wp_register_style('wbuilder-cgb-style_24kjg325-css', WDKIT_SERVER_PATH .'/gutenberg/Messy-Image-Gallery_s1jbry24/Messy_Image_Gallery_s1jbry24.css', is_admin() ? array( 'wp-editor' ) : null, '2.1.0.673179', false );
    
        register_block_type(
            'wdkit/wb-s1jbry24', array(
                'attributes' => [
                    'block_id' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'gallery_akrzzp23' => array(
            'type' => 'array',
            'default' => array(
                (object) array('url' => '', 'Id' => ''),
            ),
        ), 
'number_vyg7w323' => array(
        'type' => 'string',
        'default' => '10',
        
    ),
'number_aavqr223' => array(
        'type' => 'string',
        'default' => '6',
        
    ),
'number_6lz7rh23' => array(
        'type' => 'string',
        'default' => '3',
        
    ),
'select_qw8bw523' => array(
            'type' => 'string',
            'default' => 'none'
        ),
'number_fzoo2323' => array(
        'type' => 'string',
        'default' => '0.5',
        
    ),
'number_bbcfm423' => array(
        'type' => 'string',
        'default' => '5',
        
    ),
'number_d461vm23' => array(
        'type' => 'string',
        'default' => '1.5',
        
    ),
'number_1u0iw823' => array(
        'type' => 'string',
        'default' => '5',
        
    ),
'number_zl0c0t23' => array(
        'type' => 'string',
        'default' => '100',
        
    ),
'number_xfs7ml23' => array(
        'type' => 'string',
        'default' => '200',
        
    ),
'dimension_bytsh423' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-image-grid-wrap{ margin: {{dimension_bytsh423}}}',
                ),
            ),
        ), 
'slider_yby6w923' => array(
           'type' => 'object',
            'default' => [ 
                // 'md' => 1,
                "unit" => 'px',
            ],
            'style' => array(
                (object) array(
                    'selector' => '{{PLUS_WRAP}} .wkit-image-grid-wrap{grid-gap:{{slider_yby6w923}};}',
                ),
            ),
        ), 
'border_so1gec23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-widget-gallery-item',
                ),
            ),

        ), 
'dimension_i8dqlc23' => array(
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
                    'selector' => '{{PLUS_WRAP}} .wkit-widget-gallery-item{ border-radius: {{dimension_i8dqlc23}}}',
                ),
            ),
        ), 
'boxshadow_cg04l323' => array(
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
                        'selector' => '{{PLUS_WRAP}} .wkit-widget-gallery-item',
                    ),
                ),
            ), 

    
                ],
                'style'         => 'wbuilder-cgb-style_24kjg325-css',
                'editor_script' => 'wbuilder-cgb-block_8ntkj125-js',
                'render_callback' => 'wkit_render_callback_lwdnj025'
            )
        );
    
    }
    add_action( 'init', 'wb_Messy_Image_Gallery_s1jbry24' );
    
    function wkit_render_callback_lwdnj025($atr, $cnt) { 
        $output = $cnt;
    
        return $output;
    }