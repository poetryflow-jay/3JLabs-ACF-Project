<?php
/**
 * Support inserting Avatar field to amem forms
 *
 * @since 	1.0
 * 
 */
namespace AMem;

use AMem\Module;
use AMem\Log;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Avatar extends Module {

	protected $inc = '';
	protected $name = 'amem/avatar';

	// vars
	protected $settings;
	protected $user_settings;
	public $temp_path;

	// log
	public $logger;

	// avatar default settings
  public $crop_type;
  public $ratio_w;
  public $ratio_h;
  public $min_width;
  public $max_width;
  public $max_size;
  public $mime_types;

	function __construct() {
		if ( !amem()->options->getmodule('_use_avatar') ) {
			return;
		}

		$this->settings = [
			'version' => AMEM_VERSION,
			'url' => AMEM_URL,
			'path' => AMEM_PATH,
		];

		$this->initialize_settings();

		// include field
		add_action( 'amem/register_addons', [$this, 'include_field_types'] );

		add_action( 'rest_api_init', [$this, 'rest_api_init'] );

		add_action( 'wp_ajax_amem_avatar_crop', [$this, 'ajax_avatar_crop'] );

		add_filter( 'acf/upload_prefilter/type=amem_avatar', [$this, 'acf_upload_prefilter'], 10, 3 );
		add_filter( 'acf/validate_attachment/type=amem_avatar', [$this, 'acf_upload_prefilter'], 10, 3 );

		add_filter( "acf/upload_prefilter/type=amem_avatar", [$this, 'detect_avatar_upload'], 10, 3 );

		add_filter( "acf/update_value/type=amem_avatar", [$this, 'save_avatar_upload'], 10, 3 );

		add_filter( 'get_avatar_url', [$this, 'filter_get_avatar_url'], 20, 3 );
		add_filter( 'get_avatar_data', [$this, 'get_avatar_data'], 30, 2 );
		add_filter( 'avatar_defaults', [$this, 'avatar_defaults'], 99999 );


	}

	function avatar_defaults( $avatar_defaults ) {
		remove_filter( 'get_avatar_url', [$this, 'filter_get_avatar_url'], 20 );
		remove_filter( 'get_avatar_data', [$this, 'get_avatar_data'], 30 );
		return $avatar_defaults;
	}


	public function get_avatar( $size, $user_id = 0) {
		$user_id = $this->get_user_id( $user_id );

		if ( !$user_id )
			return '';

		if ( $user_id === 'default' ) {
			$sizes = (array) get_option( 'amem/avatar/default_avatar_data', [] );
		} else {
			$sizes = get_user_meta( $user_id, '_amem_avatar_sizes', true );
			if ( (!is_array($sizes) || empty($sizes) ) && amem()->options->get('avatar/set_default_avatar') ) {
				return $this->get_avatar( $size, 'default' );
			}

			$sizes = (array) $sizes;
		}

		$_size = $size;

		if ( !isset($sizes[$size]) ) {
			// remove 'full' and non-numeric sizes
			$size_names = array_filter( array_map( 'intval', array_keys( $sizes ) ) );
			if ( !empty($size_names) ) {
				if ( max($size_names) < $size ) {
					$size = 'full';
				} elseif ( min($size_names) > $size ) {
					$size = min($size_names);
				} else {
					$size = $this->get_closest_number( $size, $size_names );
				}
			}
		}

		$size = isset($sizes[$size]) ? $size : 'full';

		if ( isset($sizes[$size]) ) {
			$file = $sizes[$size]['file'];

			$upload_dir = $this->upload_dir();
			$avatar_url = $upload_dir['baseurl'] . '/' . $user_id . '/' . $file;

			return $avatar_url;
		}

		return '';
	}

	public function get_closest_number($num, $array) {
		$closest = null;
		$num = (int) $num;
		foreach ($array as $item) {
			$item = (int) $item;
		  if ($closest === null || abs($num - $closest) > abs($item - $num)) {
	      $closest = $item;
		  }
		}
		return $closest;
	}

	public function get_closest_larger($num, $array) {
    sort($array);
		$num = (int) $num;
    foreach ($array as $a) {
      if ($a >= $num) return $a;
    }
    return end($array);
	}

	public function get_avatar_dir($user_id = 0) {
		$upload_dir = $this->upload_dir();
		$avatar_dir = $upload_dir['basedir'];

		if ( $user_id ) {
			$avatar_dir .= DIRECTORY_SEPARATOR . $user_id;
		}

		return $avatar_dir;
	}

	public function get_user_id( $user_id = 0 ) {
		if ( !$user_id ) 
			$user_id = amem_user('ID');

		if ( is_a( $user_id, 'WP_User') )
			$user_id = $user_id->ID;

		if ( is_object($user_id) ) {
			if ( property_exists($user_id, 'ID') ) {
				$user_id = $user_id->ID;
			} elseif ( property_exists($user_id, 'user_id') ) {
				$user_id = $user_id->user_id;
			}
		}

		if ( is_email( $user_id ) ) {
			$user_id = email_exists( $user_id );
		}

		return $user_id === 'default' ? $user_id : (int) $user_id;
	}

	public function get_default_avatar($size) {
		return $this->get_avatar( $size, 'default' );
	}

	public function include_field_types() {
		amem_include( amem()->fields->inc . 'class-avatar.php' );
	}

	public function detect_avatar_upload($errors, $file, $field) {
		add_filter( 'upload_dir', [$this, 'upload_dir'] );
		return $errors;
	}

	public function upload_dir() {
		$avatar_dir = amem()->files->upload_dir();
		$avatar_dir['path'] .= 'avatar';
		$avatar_dir['url'] .= 'avatar';
		$avatar_dir['basedir'] .= 'avatar';
		$avatar_dir['baseurl'] .= 'avatar';

		return $avatar_dir;
	}

	public function temp_upload_dir() {
		return amem()->files->temp_upload_dir();
	}

	public function avatar_sizes() {
		$default_sizes = [ 96, 150, 300 ];
		$sizes = amem()->options->get('avatar/avatar_sizes' );
		$sizes = explode( ',', $sizes );
		$sizes = array_filter( array_map( 'intval', $sizes ) );
		$sizes = apply_filters( 'amem/avatar/sizes', $sizes );

		$sizes = array_filter( array_map('intval', $sizes), function($v) {
			return $v >= 80 && $v <= 512;
		} );

		if ( !$sizes )
			$sizes = $default_sizes;

		sort( $sizes );

		return $sizes;
	}

	protected function make_subsizes( $new_sizes, $file ) {
		$editor = wp_get_image_editor( $file );

		if ( is_wp_error( $editor ) ) {
			// The image cannot be edited.
			return [];
		}

		$error = new \WP_Error;
		$size_meta = [];

		if ( method_exists( $editor, 'make_subsize' ) ) {
			foreach ( $new_sizes as $new_size_name => $new_size_data ) {
				$new_size_meta = $editor->make_subsize( $new_size_data );

				if ( is_wp_error( $new_size_meta ) ) {
					/* translators: %s: Image subsize name */
					$error->add( "size_make_{$new_size_name}", sprintf( __( 'Failed to create subsize (%s)', 'advanced-members' ), $new_size_name ) );
				} else {
					// Save the size meta value.
					$size_meta[ $new_size_name ] = $new_size_meta;
				}
			}
		} else {
			// Fall back to `$editor->multi_resize()`.
			$created_sizes = $editor->multi_resize( $new_sizes );

			if ( ! empty( $created_sizes ) ) {
				$size_meta = array_merge( $size_meta, $created_sizes );
			}
		}

		if ( $error->get_error_codes() ) {
			$this->log_error( $error->get_error_messages() );
		}

		return $size_meta;
	}

	public function ajax_avatar_crop() {
		check_ajax_referer( 'amem-avatar' );

		// WTF WordPress
		$post = array_map('stripslashes_deep', $_POST);

		$data = json_decode($post['data'], true);

		$attachment = $this->create_crop($data);

		wp_send_json( $attachment );
	}

	protected function initialize_settings() {
		$default_user_settings = [
			'modal_type' => 'cropped',
			'delete_unused' => false,
			'rest_api_compat' => false,
		];

		$this->user_settings = array_merge($default_user_settings, $this->settings);
		$this->settings['user_settings'] = $this->user_settings;

		$this->crop_type = 'aspect_ratio';
		$this->ratio_w = 1;
		$this->ratio_h = 1;
		$this->min_width = 80;
		$this->max_width = 512;
		$this->max_size = 10;
		$this->mime_types = 'jpeg,jpg,png';
	}

	/**
	 * Clean up any temporary files
	 */
	private function cleanup() {
		if ($this->temp_path) {
			amem()->files->delete_file( $this->temp_path );
		}
	}

	public function logger() {
		if ( !isset($this->logger) ) {
			$this->logger = Log::getInstance( ['name' => 'avatar'] );
		}

		return $this->logger;
	}

	public function debug($message) {
		if ( defined('WP_DEBUG') && WP_DEBUG === true ) {
			$this->logger()->add( $message );
		}
	}

	private function log_error($description, $object = false) {
		$message = "AMem Avatar: $description" . PHP_EOL;
		if ( $object ) {
			$message .= print_r($object, true);
		}

		$this->logger()->add( $message );
	}

	public function save_default_avatar($value) {
		$new_data = get_option( 'amem/avatar/default_avatar_data/tmp' );

		if ( $value && $new_data ) {
			$new_dir = $this->get_avatar_dir( 'default' );

			// delete old images
			if ( $old = get_option( 'amem/avatar/default_avatar_data' ) ) {
				$old_name = pathinfo( $old['full']['file'], PATHINFO_FILENAME );
				amem()->files->delete_matched( $new_dir, $old_name );
			}

			delete_option( 'amem/avatar/default_avatar_data/tmp' );
			update_option( 'amem/avatar/default_avatar_data', $new_data );
		} elseif ( !$value ) {
			// delete old images
			if ( $old = get_option( 'amem/avatar/default_avatar_data' ) ) {
				$new_dir = $this->get_avatar_dir( 'default' );
				$old_name = pathinfo( $old['full']['file'], PATHINFO_FILENAME );
				amem()->files->delete_matched( $new_dir, $old_name );
			}
			delete_option( 'amem/avatar/default_avatar_data' );
		}

		return $value;
	}

	public function save_avatar_upload($value, $post_id, $field) {
		if ( strpos($post_id, 'user_') === 0 ) {
			$user_id = (int) str_replace('user_', '', $post_id);
			$new_data = get_user_meta( $user_id, '_amem_avatar_sizes_tmp', true );

			if ( $new_data ) {
				$new_dir = $this->get_avatar_dir( $this->get_user_id($user_id) );
				if ( !$value ) {
					// remove temp images
					$old_name = pathinfo( $new_data['full']['file'], PATHINFO_FILENAME );
					amem()->files->delete_matched( $new_dir, $old_name );
				} else {
					if ( $user_id === 'default' ) {

						$value = $this->save_default_avatar($value);
					} else {
						if ( !preg_match( '!([0-9]+)!', $new_data['full']['file'], $m ) ) {
							delete_user_meta( $user_id, '_amem_avatar_sizes_tmp' );
							return $value;
						}

						$img_id = $m[1];

						// delete old images
						if ( $old = get_user_meta( $user_id, '_amem_avatar_sizes', true ) ) {
							$old_name = pathinfo( $old['full']['file'], PATHINFO_FILENAME );
							amem()->files->delete_matched( $new_dir, $old_name );
						}

						delete_user_meta( $user_id, '_amem_avatar_sizes_tmp' );
						update_user_meta( $user_id, '_amem_avatar_sizes', $new_data );

						return $img_id;
					}					
				}
			} elseif ( !$value ) {
				$new_dir = $this->get_avatar_dir( $this->get_user_id($user_id) );
				// delete old images
				if ( $old = get_user_meta( $user_id, '_amem_avatar_sizes', true ) ) {
					$old_name = pathinfo( $old['full']['file'], PATHINFO_FILENAME );
					amem()->files->delete_matched( $new_dir, $old_name );
				}
				delete_user_meta( $user_id, '_amem_avatar_sizes' );
			}
		}
		return $value;
	}

	private function crop(\WP_Image_Editor $image, $data) {
		$image->crop($data['x'], $data['y'], $data['width'], $data['height']);
	}

	public function jpeg_quality($jpeg_quality) {
		return apply_filters('amem/avatar/jpeg_quality', $jpeg_quality);
	}

	public function rest_api_init() {
		register_rest_route('amem/avatar/v1', '/upload', [
			'methods' => 'POST',
			'callback' => [$this, 'rest_api_upload_callback'],
			'permission_callback' => function () {
				return true;
			},
		]);
		register_rest_route('amem/avatar/v1', '/crop', [
			'methods' => 'POST',
			'callback' => [$this, 'rest_api_crop_callback'],
			'permission_callback' => function () {
				return true;
			},
		]);
		register_rest_route('amem/avatar/v1', '/cancelCrop', [
			'methods' => 'POST',
			'callback' => [$this, 'rest_api_cancel_crop_callback'],
			'permission_callback' => function () {
				return true;
			},
		]);
	}

	public function rest_api_cancel_crop_callback(\WP_REST_Request $data) {
		$this->rest_api_check_nonce($data);

		$params = $data->get_json_params();

		if ( empty($params['file']) ) {
			return new \WP_Error( 'file_missing', __('Image file not provided', 'advanced-members' ) );
		}

		$file = amem()->files->upload_temp . DIRECTORY_SEPARATOR . $params['file'];

		amem()->files->delete_file( $file, true );

		return [ 'success' => true	];
	}

	public function rest_api_crop_callback(\WP_REST_Request $data) {
		$this->rest_api_check_nonce($data);

		$parameters = $data->get_json_params();
		$attachment = $this->create_crop($parameters);
		return $attachment;
	}

	public function rest_api_upload_callback(\WP_REST_Request $data) {
		$this->rest_api_check_nonce($data);

		if (empty($data->get_file_params()['image'])) {
			return new \WP_Error(
				'image_field_missing',
				__('Image field missing.', 'advanced-members')
			);
		}

		if (empty($data->get_param('key'))) {
			return new \WP_Error(
				'key_field_missing',
				__('Key field missing.', 'advanced-members')
			);
		}

		$key = $data->get_param('key');

		$mime_types = $this->mime_types;
		$max_size = $this->max_size;

		$min_width = $this->min_width;
		$max_width = $this->max_width;

		$min_height = $min_width;
		$max_height = $max_width;

		$crop_type = $this->crop_type;

		$user_id = $data->get_param('uid');
		if ( !$user_id )
			$user_id = amem_user('ID');

		// MIME validation

		$file_mime = mime_content_type(
			$data->get_file_params()['image']['tmp_name']
		);

		$allowed_mime_types = $this->extension_list_to_mime_array($mime_types);

		if (
			!empty($allowed_mime_types) &&
			!in_array($file_mime, $allowed_mime_types)
		) {
			return new \WP_Error(
				'invalid_mime_type',
				__('Invalid file type.', 'advanced-members')
			);
		}

		// File size validation
		if (
			!empty($max_size) &&
			$data->get_file_params()['image']['size'] > $max_size * 1000000
		) {
			return new \WP_Error(
				'file_too_large',
				sprintf(
					/* translators: %d: max file size */
					__(
						'File size too large. Maximum file size is %d megabytes.',
						'advanced-members'
					),
					$max_size
				),
				'advanced-members'
			);
		}

		// Image size validation
		$image_size = @getimagesize(
			$data->get_file_params()['image']['tmp_name']
		);

		if (!$image_size) {
			return new \WP_Error(
				'failed_to_parse_image',
				__('Failed to parse image.', 'advanced-members')
			);
		}

		$image_width = $image_size[0];
		$image_height = $image_size[1];

		if (
			!empty($min_width) &&
			($image_width < $min_width || $image_height < $min_height)
		) {
			return new \WP_Error(
				'image_too_small',
				sprintf(
					/* translators: 1: min file width 2: min file height */
					__(
						'Image too small. Minimum image dimensions are %1$dx%2$d pixels.',
						'advanced-members'
					),
					$min_width,
					$min_height
				),
				'amem-avatar'
			);
		}

		add_filter( 'upload_dir', [$this, 'temp_upload_dir'] );

		$wp_upload_dir = wp_upload_dir();
		$upload = wp_upload_bits(
			$data->get_file_params()['image']['name'],
			null,
			file_get_contents($data->get_file_params()['image']['tmp_name'])
		);
		$wp_filetype = wp_check_filetype(basename($upload['file']), null);

		$url = str_replace( $wp_upload_dir['basedir'], $wp_upload_dir['baseurl'], $upload['file'] );
		$size = wp_getimagesize( $upload['file'] );

		$attachment = [
			'id' => filemtime( $upload['file'] ),
			'filename' => wp_basename( $upload['file'] ),
			// hide absolute path for security
			'file' => str_replace( $wp_upload_dir['basedir'], '', $upload['file'] ),
			'url' => $url,
			'preview_url' => $url,// we don't have subsizes yet
			'alt' => '',
			'uid' => $user_id,
			'title' => preg_replace(
				'/\.[^.]+$/',
				'',
				basename($upload['file'])
			),
			'mime' => $wp_filetype['type'],
			'caption' => '',
			'description' => '',
			'width' => 0,
			'height' => 0,
		];

		if ( $size ) {
			$attachment['width'] = $size[0];
			$attachment['height'] = $size[1];
		}

		remove_filter( 'upload_dir', [$this, 'temp_upload_dir'] );

		return new \WP_REST_Response( $attachment );
	}

	/**
	 * @param $data
	 * @return array
	 */
	public function create_crop($data) {
		$image_data = false;
		if ( isset($data['data']) ) {
			$image_data = (array) json_decode( urldecode($data['data']) );
		}

		if ( $image_data === false ) {
			$error_text = __( 'Failed to get image data.', 'advanced-members' );
			$this->log_error($error_text);
			wp_send_json($error_text, 500);
		}

		$user_id = $image_data['uid'] === 'default' ? $image_data['uid'] : (int) $image_data['uid'];

		// If the difference between the images is less than half a percentage, use the original image
		// prettier-ignore
		// if ( 
		// 	$image_data['height'] - $data['height'] < $image_data['height'] * 0.005 &&
		// 	$image_data['width'] - $data['width'] < $image_data['width'] * 0.005 &&
		// 	$data['cropType'] !== 'pixel_size'
		// ) {
		// 	wp_send_json(['id' => $image_data['id']]);
		// }

		$avatar_dir = $this->upload_dir();

		// WP Smush compat: use original image if it exists
		$file = amem()->files->upload_temp . DIRECTORY_SEPARATOR . $image_data['file'];
		$parts = explode('.', $file);
		$extension = array_pop($parts);

		add_filter( 'jpeg_quality', [$this, 'jpeg_quality'] );

		$image = null;
		$scaled_data = null;
		if ( file_exists($file) ) {
			$image = wp_get_image_editor($file);

			// Get the scale
			$scale = 1;

			// Clone data array
			$scaled_data = $data;
		} else {
			// Let's attempt to get the file by URL
			$this->get_temp_path();

			try {
				$url = $image_data['url'];

				$request_options = [
					'stream' => true,
					'filename' => $this->temp_path,
					'timeout' => 25,
				];

				$result = wp_remote_get($url, $request_options);

				if ( is_wp_error($result) ) {
					throw new \Exception('Failed to save image');
				}
				$image = wp_get_image_editor($this->temp_path);
			} catch (\Exception $exception) {
				$this->cleanup();
				$error_text = __( 'Failed to fetch remote image', 'advanced-members' );
				$this->log_error($error_text, $exception);
				wp_send_json($error_text, 500);
			}
		}

		if ( is_wp_error($image) ) {
			$this->cleanup();
			$error_text = __( 'Failed to open image', 'advanced-members' );
			$this->log_error($error_text, $image);
			wp_send_json($error_text, 500);
		}

		// Use scaled coordinates if we have those
		$this->crop($image, $scaled_data ? $scaled_data : $data);

		$max_width = $this->max_width;
		$max_height = $max_width;

		if (
			$data['cropType'] === 'aspect_ratio' &&
			!empty($max_width) &&
			$data['width'] > $max_width &&
			$data['height'] > $max_height
		) {
			$image->resize($max_width, $max_height, true);
		}

		$width = $data['ratioW'];
		$height = $data['ratioH'];

		// Generate new base filename
		$new_file_name = $image_data['id'] . '.' . $extension;

		$new_dir = $this->get_avatar_dir( $this->get_user_id($image_data['uid']) );

		// Generate target path new file using existing media library
		$new_file =  $new_dir . DIRECTORY_SEPARATOR . $new_file_name;

		if ( file_exists( $new_file ) ) {
			amem()->files->delete_file( $new_file, true );
		}

		// Get the relative path to save as the actual image url
		$new_rel_path = str_replace( $avatar_dir['basedir'], '', $new_file );

		$save = $image->save($new_file);

		remove_filter( 'jpeg_quality', [$this, 'jpeg_quality'] );

		if ( is_wp_error($save) ) {
			$this->cleanup();
			$error_text = __( 'Failed to crop', 'advanced-members' );
			$this->log_error($error_text, $save);
			wp_send_json($error_text, 500);
		}

		$wp_filetype = wp_check_filetype($new_rel_path, null);

		$url = str_replace( $avatar_dir['basedir'], $avatar_dir['baseurl'], $new_file );
		$size = wp_getimagesize( $new_file );

		$attachment = [
			'id' => filemtime( $new_file ),
			'filename' => wp_basename( $new_file ),
			'file' => $new_file,
			'url' => $url,
			'preview_url' => $url,
			'alt' => '',
			'uid' => $image_data['uid'],
			'title' => preg_replace(
				'/\.[^.]+$/',
				'',
				basename($new_file)
			),
			'mime' => $wp_filetype['type'],
			'caption' => '',
			'description' => '',
			'width' => 0,
			'height' => 0,
		];

		if ( $size ) {
			$attachment['width'] = $size[0];
			$attachment['height'] = $size[1];
		}

		$new_sizes = $new_data = [];

		foreach( $this->avatar_sizes() as $s ) {
			if ( !is_int($s) )
				continue;
			$new_sizes[$s] = ['width' => $s, 'height' => $s, 'crop' => true];
		}

		$previewSize = empty($data['previewSize']) ? 150 : (int) $data['previewSize'];

		if ( $previewSize )
			$new_sizes[$previewSize] = [ 'width' => $previewSize, 'height' => $previewSize, 'crop' => true];

		if ( $new_sizes ) {
			$new_data = $this->make_subsizes($new_sizes, $new_file);
		}

		$new_data['full'] = [
			'path' => $new_file,
			'file' => wp_basename( $new_file ),
			'mime_type' => $wp_filetype['type'],
			'width' => $attachment['width'],
			'height' => $attachment['height'],
			'filesize' => wp_filesize($new_file),
		];

		if ( $previewSize && isset($new_data[$previewSize]) ) {
			$attachment['preview_url'] = str_replace( $avatar_dir['basedir'], $avatar_dir['baseurl'], $new_dir ) . '/' . $new_data[$previewSize]['file'];
		}

		// delete original image
		amem()->files->delete_file( $file );
		if ( $user_id === 'default' ) {
			// delete old images
			// if ( $old = get_option( 'amem/avatar/default_avatar_data' ) ) {
			// 	$old_name = pathinfo( $old['full']['file'], PATHINFO_FILENAME );
			// 	amem()->files->delete_matched( $new_dir, $old_name );
			// }

			update_option( 'amem/avatar/default_avatar_data/tmp', $new_data );
			// amem()->options->set( 'avatar/default_avatar', 1 );
		} else {
			// delete old images
			// if ( $old = get_user_meta( $user_id, '_amem_avatar_sizes', true ) ) {
			// 	$old_name = pathinfo( $old['full']['file'], PATHINFO_FILENAME );
			// 	amem()->files->delete_matched( $new_dir, $old_name );
			// }

			update_user_meta( $user_id, '_amem_avatar_sizes_tmp', $new_data );

			// update_field( $data['key'], $data['id'], "user_{$user_id}" );
		}

		$this->cleanup();

		return $attachment;
	}

	protected function get_temp_path() {
		if ( $this->temp_path )
			$this->cleanup();

		$temp_name = wp_generate_uuid4();
		$temp_directory = amem()->files->upload_temp;
		$this->temp_path = $temp_directory . $temp_name;

		return $this->temp_path;
	}

	/**
	 * @param WP_REST_Request $data
	 */
	public function rest_api_check_nonce(\WP_REST_Request $data) {
		$nonce = $data->get_header('X-AMem-Avatar-Nonce');

		if ( empty($nonce) ) {
			wp_send_json_error(
				new \WP_Error( 'nonce_missing', __('Nonce missing.', 'advanced-members') ),
				400
			);
		}

		if ( !wp_verify_nonce($nonce, 'amem-avatar') ) {
			wp_send_json_error(
				new \WP_Error( 'invalid_nonce', __('Invalid nonce.', 'advanced-members') ),
				400
			);
		}
	}

	/**
	 * @param $mime_types
	 * @return array
	 */
	public static function extension_list_to_mime_array($mime_types) {
		if (empty($mime_types)) {
			$mime_types = 'jpeg,png,gif';
		}
		$extension_array = explode(',', $mime_types);
		$extension_array = array_map(function ($extension) {
			return trim($extension);
		}, $extension_array);

		$allowed_mime_types = [];

		foreach ($extension_array as $extension) {
			if ($extension === 'jpeg' || $extension === 'jpg') {
				array_push($allowed_mime_types, 'image/jpeg');
			}
			if ($extension === 'png') {
				array_push($allowed_mime_types, 'image/png');
			}
			if ($extension === 'gif') {
				array_push($allowed_mime_types, 'image/gif');
			}
		}

		$allowed_mime_types = array_unique($allowed_mime_types);
		return $allowed_mime_types;
	}

	public function acf_upload_prefilter($errors, $file, $field) {
		// Suppress error about maximum height and width
		if (!empty($errors['max_width'])) {
			unset($errors['max_width']);
		}
		if (!empty($errors['max_height'])) {
			unset($errors['max_height']);
		}
		return $errors;
	}

	function filter_get_avatar_url( $url, $id_or_email, $args ) {

		if ( amem()->options->getmodule( '_use_avatar' ) && preg_match( '/gravatar/i', $url ) ) {
			$_url = $this->get_avatar( $args['size'], $id_or_email );
			if ( ! empty( $_url ) ) {
				$url = $_url;
			}
		}

		return $url;
	}

	function get_avatar_data($data, $id_or_email) {
		if ( amem()->options->getmodule( '_use_avatar' ) && preg_match( '/gravatar/i', $data['url'] ) ) {
			$_url = $this->get_avatar( $data['size'], $id_or_email );
			if ( ! empty( $_url ) ) {
				$data['url'] = $_url;
			}
		}

		return $data;
	}

}

amem()->register_module('avatar', Avatar::getInstance());