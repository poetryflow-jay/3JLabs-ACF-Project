<?php
/*
 * Inspired by ACF Image Aspect Ratio Crop Field
 * https://github.com/joppuyo/acf-image-aspect-ratio-crop by Johannes Siipola
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 


class amem_field_avatar extends amem_field_base {
  /** @var string */
  public $temp_user_id;
  public $settings;

  /** @var amem()->avatar settings cloned */
  public $crop_type;
  public $ratio_w;
  public $ratio_h;
  public $min_width;
  public $max_width;
  public $max_size;
  public $mime_types;

  function initialize() {
    /** @var avatar default settings */
    $this->temp_user_id = wp_generate_uuid4();
    $this->crop_type = amem()->avatar->crop_type;
    $this->ratio_w = amem()->avatar->ratio_w;
    $this->ratio_h = amem()->avatar->ratio_h;
    $this->min_width = amem()->avatar->min_width;
    $this->max_width = amem()->avatar->max_width;
    $this->max_size = amem()->avatar->max_size;
    $this->mime_types = amem()->avatar->mime_types;

    $this->name = 'amem_avatar';
    $this->label = __( 'Avatar', 'advanced-members' );
    $this->category = 'Advanced Members';
    $this->defaults = [
      'return_format' => 'array',
      'preview_size' => 150,
      'display_size' => 80,
      'library' => 'all',
      'min_width' => 80,
      'max_width' => 512,
      'max_size' => 10,
      'mime_types' => 'jpeg,jpg,png',
      'crop_type' => $this->crop_type,
      'ratio_w' => $this->ratio_w,
      'ratio_h' => $this->ratio_h,
    ];

    $this->l10n = [
      'select' => __('Select Avatar', 'advanced-members'),
      'edit' => __('Edit Avatar', 'advanced-members'),
      'update' => __('Update Avatar', 'advanced-members'),
      'uploadedTo' => __('Uploaded to this post', 'advanced-members'),
      'all' => __('All Avatars', 'advanced-members'),
    ];

    // Store temporary post id in a hidden field
    add_action( 'acf/input/form_data', function () {
      echo "<input type='hidden' name='amem_temp_user_id' value='$this->temp_user_id'>";
    }, 10, 1 );

    $this->settings = (array) amem()->avatar->settings;
  }

  function render_field_settings($field) {

    acf_render_field_setting($field, [
      'label' => __('Preview Size', 'advanced-members'),
      'instructions' => __('Shown when entering data. Default: 150', 'advanced-members'),
      'type' => 'number',
      'name' => 'preview_size',
      'default_value' => 150,
      'placeholder' => 150,
    ]);

    // acf_render_field_setting($field, [
    //   'label' => __('Display Size', 'advanced-members'),
    //   'instructions' => __('Shown when entering data. default: 150', 'advanced-members'),
    //   'type' => 'number',
    //   'name' => 'display_size',
    //   'default_value' => 150,
    //   'placeholder' => 150,
    // ]);
  }

  function render_field($field) {
    // Avatar field currently only works with AMem Forms
    if ( !$this->mode() && (empty($field['prefix']) || $field['prefix'] !== 'amem_options[avatar]') )
      return '';

    $is_settings = false;
    if ( $field['prefix'] === 'amem_options[avatar]' ) {
      $is_settings = true;
    }

    // vars
    $uploader = 'basic';// $field['uploader']

    // enqueue
    if ($uploader == 'dropzone') {
      // @todo supports dropzone
    }

    // vars
    $url = '';
    $preview_size = $field['preview_size'] ? (int)$field['preview_size'] : 150;

    $original = null;
    if ( $is_settings ) {
      $user_id = 'default';
      if ( $field['value'] )
        $url = amem()->avatar->get_default_avatar($field['preview_size']);
      $this->input_admin_enqueue_scripts();
    } else {
      $user_id = amem_user('ID');
      if ( !$user_id )
        $user_id = $this->temp_user_id;

      // has value?
      if ( $field['value'] ) {
        $url = amem()->avatar->get_avatar( $preview_size, $user_id );
      }

    }

    // set aspect width and height to zero for free cropping
    $wrapper = [
      'class' => 'amem-image-avatar-uploader',
      'data-preview-size' => $preview_size,
      'data-key' => $field['key'],
      'data-uid' => $user_id,
      'data-library' => $field['library'],
      'data-mime_types' => $this->mime_types,
      'data-uploader' => $uploader,
      'data-crop_type' => $this->crop_type,
      'data-ratio_w' => $this->ratio_w,
      'data-ratio_h' => $this->ratio_h,
      'data-min_width' => $this->min_width,
      'data-max_width' => $this->max_width,
    ];

    // url exists
    if ( $url ) {
      $wrapper['class'] .= ' has-value';
    }

    ?>
    <div <?php acf_esc_attr_e($wrapper); ?>>
      <?php acf_hidden_input( [ 'name' => $field['name'], 'value' => $field['value'], 'class' => 'amem-avatar' ] ); ?>
      <div class="show-if-value image-wrap"<?php echo ( $preview_size ? ' style="' . esc_attr( 'max-width: ' . $preview_size . 'px' ) . '"' : '' ) ?>>
        <img data-name="image" src="<?php echo esc_url( $url ); ?>" alt="<?php esc_attr_e( 'User Avatar', 'advanced-members' ) ?>"/>
        <div class="acf-actions -hover">
          <a class="acf-icon -cancel-custom dark" data-name="remove" href="#" title="<?php esc_attr_e('Remove', 'advanced-members'); ?>"></a>
        </div>
      </div>
      <div class="hide-if-value">
        <?php if ($uploader == 'basic') { ?>

        <!-- basic uploader start -->
        <?php $mime_array = Amem\Avatar::extension_list_to_mime_array( $this->mime_types ); ?>
        <div class="js-amem-avatar-upload-progress" style="display: none"></div>

        <input type="file" class="amem-avatar-upload js-amem-avatar-upload" data-id="<?php echo $field['name']; ?>" accept="<?php echo implode(',', $mime_array); ?>">
        <?php // if ( $mime_array ) { ?>
        <?php /* translators: %s: list of mime types */ ?>
        <?php /* ?><p class="description"><?php echo sprintf( esc_html( 'Allowed Types: %s', 'advanced-members' ), implode(', ', $mime_array) ) ?></p><?php */ ?>
        <?php /*}*/ ?>
        <!-- basic uploader end -->

        <?php } elseif ( $uploader == 'dropzone' ) { ?>

        <!-- dropzone uploader start -->
        <?php $mime_array = Amem\Avatar::extension_list_to_mime_array( $this->mime_types ); ?>
        <div id="dropzone-<?php echo esc_attr( $field['key'] ) ?>" class="dropzone">
          <div class="dropzone-title">
            <h3><?php esc_html_e( 'Upload file', 'advanced-members' ) ?></h3>
            <p><?php esc_html_e( 'Drag and drop files here', 'advanced-members' ) ?></p>
            <?php /*if ( $mime_array ) {*/ ?>
            <?php /* translators: %s: list of mime types */ ?>
            <?php /* ?><p class="description"><?php echo sprintf( esc_html( 'Allowed Types: %s', 'advanced-members' ), implode(', ', $mime_array) ) ?></p>
            <?php } ?><?php */ ?>
          </div>
          <input type="file" class="amem-avatar-upload js-amem-avatar-upload" data-id="<?php echo $field['name']; ?>" accept="<?php echo implode(',', $mime_array); ?>">
        </div>
        <?php /*if ( $mime_array ) {*/ ?>
        <?php /* translators: %s: list of mime types */ ?>
        <?php /* ?><p class="description"><?php echo sprintf( esc_html( 'Allowed Types: %s', 'advanced-members' ), implode(', ', $mime_array) ) ?></p>
        <?php } ?><?php */ ?>
        <!-- dropzone uploader end -->

        <?php } else { ?>

          <!-- @todo dropzone uploader start -->
          <p><?php _e( 'No Avatar selected', 'advanced-members' ); ?> <a data-name="add" class="acf-button button" href="#"><?php _e( 'Add Avatar', 'advanced-members' ); ?></a></p>
          <!-- dropzone uploader end -->

        <?php } ?>
      </div>
    </div>
    <?php
  }

  function input_admin_enqueue_scripts() {
    global $post;

    amem_register_script( 'amem-avatar', amem_get_url("avatar.js", 'assets/avatar'), ['acf-input', 'backbone'], AMEM_VERSION, ['asset_path' => amem_get_path('', 'assets/avatar')] );
    $translations = [
      'cropping_in_progress' => __( 'Cropping image...', 'advanced-members' ),
      'cropping_failed' => __( 'Failed to crop image', 'advanced-members' ),
      'crop' => __( 'Crop', 'advanced-members' ),
      'cancel' => __( 'Cancel', 'advanced-members' ),
      'modal_title' => __( 'Crop image', 'advanced-members' ),
      'reset' => __( 'Reset crop', 'advanced-members'),
      /* translators: %d: Upload progress percantage */
      'upload_progress' => __( 'Uploading image. Progress %d%%.', 'advanced-members' ),
      'upload_failed' => __( 'Upload failed.', 'advanced-members' ),
      'updated' => __( 'Avatatar updated.', 'advanced-members' ),
    ];
    $options = [
      'modal_type' => 'cropped',
      'rest_api_compat' => '',//$this->settings['user_settings']['rest_api_compat'],
    ];

    $data_array = [
      'temp_user_id' => $this->temp_user_id,
      'nonce' => wp_create_nonce('amem-avatar'),
      'rest_nonce' => wp_create_nonce('wp_rest'),
      'api_root' => untrailingslashit( get_rest_url(null, 'amem/avatar/v1/') ),
      'ajaxurl' => admin_url( 'admin-ajax.php' ),
      'modal_type' => $this->settings['user_settings']['modal_type'],
      'rest_api_compat' => $this->settings['user_settings']['rest_api_compat'],
      'ratioW' => $this->ratio_w,
      'ratioH' => $this->ratio_h,
      'cropType' => $this->crop_type,
      'l10n' => $translations,
    ];

    wp_localize_script( 'amem-avatar', 'amemAvatar', $data_array );

    wp_enqueue_script( 'amem-avatar' );
    wp_register_style( 'amem-avatar', amem_get_url( "avatar.css", 'assets/avatar' ), ['acf-input'], $version );
    wp_enqueue_style( 'amem-avatar' );
  }

  function format_value($value, $post_id, $field) {
    // bail early if no value
    if ( empty($value) || !($user_id = $this->_user_id($post_id)) ) {
      return false;
    }

    $image_id = null;
    $decoded = @json_decode($value);

    if ( is_array($value) ) {
      if ( isset($value['full']) ) {
        $file = $sizes['full']['file'];

        $upload_dir = amem()->avatar->upload_dir();
        $image_id = $upload_dir['baseurl'] . '/default/' . $file;

      }

    }

    // For migration compatibility with acf-image-crop plugin.
    // Retrieves the image from that plugin which it has saved inside JSON encoded value.
    if (is_numeric($value)) {
      $image_id = $value;
    } elseif ( $decoded !== false && !empty($decoded->cropped_image) ) {
      $image_id = $decoded->cropped_image;
    }

    // return
    return $image_id;
  }

}

// initialize
acf_register_field_type( 'amem_field_avatar' );

?>
