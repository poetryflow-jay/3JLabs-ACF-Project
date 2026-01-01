<?php
namespace AMem;

use AMem\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'AMem\Files' ) ) {

	/**
	 * Class Files
	 * @package um\core
	 */
	class Files extends Module {

		/**
		 * @var
		 */
		public $upload_temp;

		/**
		 * @var
		 */
		public $upload_baseurl;

		/**
		 * @var
		 */
		public $upload_basedir;

		/**
		 * @var array|array[]
		 */
		public $fonticon = array();

		/**
		 * @var null|string
		 */
		public $upload_dir = null;

		/**
		 * @var null
		 */
		public $upload_temp_url = null;

		/**
		 * @var string
		 */
		public $default_file_fonticon = 'amem-faicon-file-o';

		/**
		 * Files constructor.
		 */
		public function __construct() {
			$this->setup_paths();
		}

		/**
		 * Setup upload directory
		 */
		function setup_paths() {
			$this->upload_dir = wp_upload_dir();

			$this->upload_basedir = $this->upload_dir['basedir'] . '/amem/';
			$this->upload_baseurl = $this->upload_dir['baseurl'] . '/amem/';

			$this->upload_basedir = apply_filters( 'amem/upload_basedir', $this->upload_basedir );
			$this->upload_baseurl = apply_filters( 'amem/upload_baseurl', $this->upload_baseurl );

			// @note : is_ssl() doesn't work properly for some sites running with load balancers
			// Check the links for more info about this bug
			// https://codex.wordpress.org/Function_Reference/is_ssl
			// http://snippets.webaware.com.au/snippets/wordpress-is_ssl-doesnt-work-behind-some-load-balancers/
			if ( is_ssl() || stripos( get_option( 'siteurl' ), 'https://' ) !== false
			     || ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) ) {
				$this->upload_baseurl = str_replace("http://", "https://",  $this->upload_baseurl);
			}

			$this->upload_temp = $this->upload_basedir . 'temp/';
			$this->upload_temp_url = $this->upload_baseurl . 'temp/';

			if ( ! file_exists( $this->upload_basedir ) ) {
				$old = umask(0);
				// @mkdir( $this->upload_basedir, 0755, true );
				$this->filesystem()->mkdir( $this->upload_basedir, 0755 );
				umask( $old );
			}

			if ( ! file_exists( $this->upload_temp ) ) {
				$old = umask(0);
				// @mkdir( $this->upload_temp , 0755, true );
				$this->filesystem()->mkdir( $this->upload_temp, 0755 );
				umask( $old );
			}

			$this->upload_dir['path'] = $this->upload_basedir;
			$this->upload_dir['url'] = $this->upload_baseurl;
			$this->upload_dir['basedir'] = $this->upload_basedir;
			$this->upload_dir['baseurl'] = $this->upload_baseurl;
			$this->upload_dir['subdir'] = '';
		}

		function filesystem() {
			global $wp_filesystem;

			if ( !is_a( $wp_filesystem, 'WP_Filesystem_Base') ) {
				if ( !function_exists('WP_Filesystem') ) {
					require_once( ABSPATH . '/wp-admin/includes/file.php' );
				}
				WP_Filesystem();
			}

			return $wp_filesystem;
		}

		/**
		 * Get path only without file name
		 *
		 * @param $file
		 *
		 * @return string
		 */
		function path_only( $file ) {
			return trailingslashit( dirname( $file ) );
		}

		/**
		 * Make a Folder
		 *
		 * @param $dir
		 */
		function make_dir( $dir ) {
			$old = umask(0);
			// @mkdir( $dir, 0755, true);
			$this->filesystem()->mkdir( $dir, 0755 );
			umask( $old );
		}


		/**
		 * Get extension by mime type
		 *
		 * @param $mime
		 *
		 * @return mixed
		 */
		function get_extension_by_mime_type( $mime ) {
			$split = explode('/', $mime );
			return $split[1];
		}


		/**
		 * Get file data
		 *
		 * @param $file
		 *
		 * @return mixed
		 */
		function get_file_data( $file ) {
			$array['size'] = filesize( $file );
			return $array;
		}

		/**
		 * If a value exists in comma seperated list
		 *
		 * @param $value
		 * @param $array
		 *
		 * @return bool
		 */
		function in_array( $value, $array ) {

			if ( in_array( $value, explode(',', $array ) ) ){
				return true;
			}

			return false;
		}

		function is_temp_upload( $url ) {
			if ( is_string( $url ) ) {
				$url = trim( $url );
			}

			if ( filter_var( $url, FILTER_VALIDATE_URL ) === false ) {
				$url = realpath( $url );
			}

			if ( ! $url ) {
				return false;
			}

			$url = explode( '/amem/temp/', $url );
			if ( isset( $url[1] ) ) {

				if ( strstr( $url[1], '../' ) || strstr( $url[1], '%' ) ) {
					return false;
				}

				$src = $this->upload_temp . $url[1];
				if ( ! file_exists( $src ) ) {
					return false;
				}

				return $src;
			}

			return false;
		}

		function rename_file( $src, $dest, $overwrite = false ) {
			return $this->filesystem()->move( $src, $dest, $overwrite );
		}

		/**
		 * This function will delete file upload from server
		 *
		 * @param string $src
		 *
		 * @return bool
		 */
		function delete_file( $src, $force=false ) {
			// only remove files for amem
			if ( strstr( $src, '?' ) ) {
				$splitted = explode( '?', $src );
				$src = $splitted[0];
			}

			$is_temp = $this->is_temp_upload( $src );
			if ( $is_temp ) {
				$this->filesystem()->delete( $is_temp );
				return true;
			} elseif( $force && strpos( $src, $this->upload_basedir ) === 0 ) {
				$this->filesystem()->delete( $src );
			} else {
				return false;
			}
		}

		/**
		 * Remove a directory
		 *
		 * @param $dir
		 */
		function remove_dir( $dir ) {
			// only remove files for amem
			if ( file_exists( $dir ) && strpos( $dir, $this->upload_basedir ) === 0 ) {
				foreach ( glob($dir . '/*') as $file ) {
					if ( is_dir( $file ) ) {
						$this->remove_dir( $file );
					} else {
						$this->filesystem()->delete( $file );
					}
				}

				$this->filesystem()->rmdir( $dir );
			}
		}

		/**
		 * Remove a directory
		 *
		 * @param $dir
		 */
		function delete_matched($dir, $match, $include_dir=true) {
			if ( file_exists( $dir ) && strpos( $dir, $this->upload_basedir ) === 0 ) {
				foreach ( glob($dir . '/' . $match . '*') as $file ) {
					if ( is_dir( $file ) && $include_dir ) {
						$this->remove_dir( $file );
					} else {
						$this->filesystem()->delete( $file );
					}
				}
			}
		}

		/**
		 * Remove old files
		 * @param string $dir							Path to directoty.
		 * @param int|string $timestamp		Unix timestamp or PHP relative time. All older files will be removed.
		 */
		function remove_old_files( $dir, $timestamp = NULL ) {
			// only remove files for amem
			if ( strpos( $dir, $this->upload_basedir ) !== 0 )
				return;

			$removed_files = array();

			if ( empty( $timestamp ) ) {
				$timestamp = strtotime( '-1 day' );
			}
			elseif ( is_string( $timestamp ) && !is_numeric( $timestamp ) ) {
				$timestamp = strtotime( $timestamp );
			}

			if ( $timestamp && is_dir( $dir ) ) {

				$files = glob( $dir . '/*' );

				foreach ( (array) $files as $file ) {
					if ( in_array( wp_basename( $file ), array('.', '..') ) ) {
						continue;
					}
					elseif ( is_dir( $file ) ) {
						$this->remove_old_files( $file, $timestamp );
					}
					elseif ( is_file( $file ) ) {
						$fileatime = fileatime( $file );
						if ( $fileatime && $fileatime < (int) $timestamp ) {
							$this->filesystem()->delete( $file );
							$removed_files[] = $file;
						}
					}
				}
			}

			return $removed_files;
		}


		/**
		 * Format Bytes
		 *
		 * @param $size
		 * @param int $precision
		 *
		 * @return string
		 */
		function format_bytes( $size, $precision = 1 ) {
			if ( is_numeric( $size ) ) {
				$base = log( $size, 1024 );
				$suffixes = array( '', 'kb', 'MB', 'GB', 'TB' );
				$computed_size = round( pow( 1024, $base - floor( $base ) ), $precision );
				$unit = $suffixes[ floor( $base ) ];

				return $computed_size.' '.$unit;
			}

			return '';
		}

		function upload_dir() {
			return $this->upload_dir;
		}

		function temp_upload_dir() {
			$upload_dir = $this->upload_dir();
			$upload_dir['path'] = $this->upload_temp;
			$upload_dir['url'] = $this->upload_temp_url;
			$upload_dir['basedir'] = $this->upload_temp;
			$upload_dir['baseurl'] = $this->upload_temp_url;

			return $upload_dir;
		}

	}
}

amem()->register_module( 'files', Files::getInstance() );
