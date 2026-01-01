<?php
/*
 * Inspired by ACFE reCAPTCHA field
 * https://wordpress.org/plugins/acf-extended/ by Konrad Chmielewski
 */

if ( !defined('ABSPATH') ) {
  exit;
}

if(!class_exists('amem_field_recaptcha')):

class amem_field_recaptcha extends amem_field_base {

  private $hide_badge = false;

  public $response = null;

  function initialize() {
      
    $this->name = 'amem_recaptcha';
    $this->label = __('Google reCAPTCHA', 'advanced-members');
    $this->category = 'Advanced Members';
    $this->public = false;
    $this->defaults = array(
      'required'      => 0,
      'disabled'      => 0,
      'readonly'      => 0,
      'version'       => 'v3',
      'theme'      => 'light',// for v2
      'size'       => 'normal',// for v2
      'hide_badge'  => false,// for v3
      'score' => '0.5',// for v3
      'site_key'      => '',
      'secret_key'    => '',
      'override_global' => 0,
    );
    
    $this->add_action('acf/input/admin_print_footer_scripts', array($this, 'admin_print_footer_scripts'));
  }
  
  function render_field_settings($field){
    acf_render_field_setting($field, array(
      'label'         => __('Override Global Settings', 'advanced-members'),
      'instructions'  => __('Use different settings from global.', 'advanced-members'),
      'type'          => 'true_false',
      'name'          => 'override_global',
      'default_value' => false,
    ));
    
    acf_render_field_setting($field, array(
      'label'         => __('Version', 'advanced-members'),
      /* translators: %s: Google document URL */
      'instructions'  => sprintf( __('Select the reCAPTCHA version. You can find details about verions from the <a href="%s" target="_blank">Google Guides</a>.', 'advanced-members'), 'https://developers.google.com/recaptcha/docs/versions' ),
      'type'          => 'select',
      'name'          => 'version',
      'default_value' => 'v3',
      'choices'       => array(
        'v3' => __('reCAPTCHA V3', 'advanced-members'),
        'v2' => __('reCAPTCHA V2', 'advanced-members'),
      ),
      'conditional_logic' => array(
        array(
          array(
            'field'     => 'override_global',
            'operator'  => '==',
            'value'     => '1',
          )
        )
      )
    ));
    
    acf_render_field_setting($field, array(
      'label'         => __('Theme', 'advanced-members'),
      'instructions'  => __('Select the reCAPTCHA theme for v2', 'advanced-members'),
      'type'          => 'select',
      'name'          => 'theme',
      'choices'       => array(
        'light' => __('Light', 'advanced-members'),
        'dark'  => __('Dark', 'advanced-members'),
      ),
      'conditional_logic' => array(
        array(
          array(
            'field'     => 'override_global',
            'operator'  => '==',
            'value'     => '1',
          ),
          array(
            'field'     => 'version',
            'operator'  => '==',
            'value'     => 'v2',
          )
        )
      )
    ));
    
    acf_render_field_setting($field, array(
      'label'         => __('Size', 'advanced-members'),
      'instructions'  => __('Select the reCAPTCHA size for v2', 'advanced-members'),
      'type'          => 'select',
      'name'          => 'size',
      'choices'       => array(
        'normal'        => __('Normal', 'advanced-members'),
        'compact'       => __('Compact', 'advanced-members'),
      ),
      'conditional_logic' => array(
        array(
          array(
            'field'     => 'override_global',
            'operator'  => '==',
            'value'     => '1',
          ),
          array(
            'field'     => 'version',
            'operator'  => '==',
            'value'     => 'v2',
          )
        )
      )
    ));
    
    acf_render_field_setting($field, array(
      'label'             => __('Hide Badge', 'advanced-members'),
      /* translators: %s: Google documnet URL */
      'instructions'      => sprintf( __('Hide the <a href="%s" target="_blank">reCAPTCHA v3 badge</a>', 'advanced-members'), 'https://developers.google.com/recaptcha/docs/faq#id-like-to-hide-the-recaptcha-badge.-what-is-allowed' ),
      'type'              => 'true_false',
      'name'              => 'hide_badge',
      'ui'                => true,
      'conditional_logic' => array(
        array(
          array(
            'field'     => 'override_global',
            'operator'  => '==',
            'value'     => '1',
          ),
          array(
            'field'     => 'version',
            'operator'  => '==',
            'value'     => 'v3',
          )
        )
      )
    ));

    acf_render_field_setting($field, array(
      'label'         => __('Score Threshold', 'advanced-members'),
      /* translators: %s: Google document URL */
      'instructions'  => sprintf( __('Select the score threshold to verify. 0.0 means very likely a bot and 1.0 means very likely a human. Google\'s default value is 0.5. Check the <a href="%s" target="_blank">Google guides</a>', 'advanced-members'), 'https://developers.google.com/recaptcha/docs/v3#interpreting_the_score' ),
      'type'          => 'select',
      'name'          => 'score',
      'choices' => [
        '0.1' => '0.1',
        '0.2' => '0.2',
        '0.3' => '0.3',
        '0.4' => '0.4',
        '0.5' => '0.5',
        '0.6' => '0.6',
        '0.7' => '0.7',
        '0.8' => '0.8',
        '0.9' => '0.9',
      ],
      'default' => true,
      'default_value' => '0.5',
      'conditional_logic' => array(
        array(
          array(
            'field'     => 'override_global',
            'operator'  => '==',
            'value'     => '1',
          ),
          array(
            'field'     => 'version',
            'operator'  => '==',
            'value'     => 'v3',
          )
        )
      )
    ));
    
    acf_render_field_setting($field, array(
      'label'         => __('Site Key', 'advanced-members'),
      /* translators: %s: reCAPTCHA console URL */
      'instructions'  => sprintf( __('Enter the site key. <a href="%s" target="_blank">reCAPTCHA API Admin</a>', 'advanced-members'), 'https://www.google.com/recaptcha/admin' ),
      'type'          => 'text',
      'name'          => 'site_key',
      'conditional_logic' => array(
        array(
          array(
            'field'     => 'override_global',
            'operator'  => '==',
            'value'     => '1',
          )
        )
      )
    ));
    
    acf_render_field_setting($field, array(
      'label'         => __('Secret Key', 'advanced-members'),
      /* translators: %s: reCAPTCHA console URL */
      'instructions'  => sprintf( __('Enter the secret key. <a href="%s" target="_blank">reCAPTCHA API Admin</a>', 'advanced-members'), 'https://www.google.com/recaptcha/admin' ),
      'type'          => 'text',
      'name'          => 'secret_key',
      'conditional_logic' => array(
        array(
          array(
            'field'     => 'override_global',
            'operator'  => '==',
            'value'     => '1',
          )
        )
      )
    ));

  }

  private function validate_settings($field) {
    $settings = amem()->recaptcha->get_settings();
    $override = ['version', 'v2_type', 'theme', 'size', 'hide_badge', 'score', 'site_key', 'secret_key'];
    $settings = array_intersect_key($settings, array_flip($override));
    if ( $settings['version'] === 'v2' && $settings['v2_type'] === 'invisible' ) {
      $settings['size'] = 'invisible';
    }
    // if ( $field['override_global'] ) {
    //   foreach( $override as $k ) {
    //     if ( isset($field[$k]) ) {
    //       if ( 'hide_badge' == $k )
    //         $settings[$k] = $field[$k];
    //       elseif ( !empty($field[$k]) )
    //         $settings[$k] = $field[$k];
    //     }
    //   }
    // }
    $field = array_merge( $field, $settings );

    $this->hide_badge = $field['version'] === 'v3' && $field['hide_badge'];
    return $field;
  }
  
  function load_field($field) {
    $field = $this->validate_settings($field);

    // if( $field['version'] === 'v3' ) {
    //   $field['wrapper']['class'] = 'acf-hidden';
    // }
    
    return $field;
  }
  
  
  /**
   * render_field
   *
   * @param $field
   */
  function render_field($field){
    // vars
    $site_key = $field['site_key'];
    $version = $field['version'];
    
    // wrapper attributes
    $wrapper = array(
      'class'         => 'acf-input-wrap',
      'data-site-key' => $site_key,
      'data-version'  => $version,
    );
    
    // v2
    if ($version === 'v2') {
      // wrapper attributes
      $wrapper['data-size'] = $field['size'];
      $wrapper['data-theme'] = $field['theme'];
    }

    if ( $version === 'v3' ) {
      $wrapper['data-hide_badge'] = $field['hide_badge'];
    }
    
    // hidden input
    $hidden_input = array(
      'id'    => $field['id'],
      'name'  => $field['name'],
    );
    
    ?>
    <div <?php echo acf_esc_atts($wrapper); ?>>
    
      <?php if ($version === 'v2') { ?>
        <div></div>
      <?php } ?>
      <?php acf_hidden_input($hidden_input); ?>

    </div>
    <?php

    amem()->recaptcha->input_enqueue_scripts();
  }

  function validate_value($valid, $value, $field, $input) {
    // bail early if not required
    if ( !$field['required'] ) {
      return $valid;
    }

    // bail early in ajax validation
    // token can only be verified once then becomes invalid
    $should_validate = apply_filters('amem/recpatcha/should_validate_value', acf_is_ajax(), $value, $field, $input);
    
    if ( !$should_validate ) {
      return $valid;
    }

    if ( !amem()->recaptcha->is_ready() ) {
      $error = __('reCAPTCHA is not ready. Site key or Secret key is not set yet', 'advanced-members' );
      // amem_add_error( 'amem_field_error', $error );
      return $error;
    }

    $response = amem()->recaptcha->validate($value, $field);
    if ( true !== $response ) {
      // amem_add_error( 'amem_field_error', $response );
      return $response;
    }

    return $valid;
  }
  
  
  /**
   * update_value
   *
   * @param $value
   * @param $post_id
   * @param $field
   *
   * @return null
   */
  function update_value($value, $post_id, $field){
    // do not save value
    return null;
  }
  
  
  /**
   * admin_print_footer_scripts
   *
   * @return void
   */
  function admin_print_footer_scripts(){
    
    if ( $this->hide_badge ) {
      ?>
      <style>
      .grecaptcha-badge{
        display: none;
        visibility: hidden;
      }
      </style>
      <?php
    }
    
  }

}

// initialize
acf_register_field_type('amem_field_recaptcha');

endif;