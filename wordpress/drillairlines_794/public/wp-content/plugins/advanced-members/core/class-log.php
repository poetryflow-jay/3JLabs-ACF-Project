<?php
/**
 * Log requests to the Marketing Mails API
 *
 * @package AMemPro\Marketing_Mails\Providers\Mailchimp
 */

namespace AMem;

use AMem\Module;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Class for Provider API requests logging
 *
 */
class Log extends Module {

	/**
	 * A file system pointer
	 *
	 * @var resource
	 */
	protected $file;

	/**
	 * Path to file
	 *
	 * @var string
	 */
	protected $file_path;

	/**
	 * Max size of the log file
	 *
	 * @var int
	 */
	protected $max_filesize = 1024 * 1024;

	protected $name = 'amem';

	protected $dir = '';

	protected $search = '';

	protected $auto_archive = false;

	protected $enabled = true;

	/**
	 * Class constructor
	 */
	public function __construct( $args='' ) {
		$args = wp_parse_args( $args, ['name' => '', 'dir' => ''] );

		if ( $args['name'] ) {
			$this->name = sanitize_file_name( $args['name'] );
		}
		if ( $args['dir'] )
			$this->dir = sanitize_file_name( $args['dir'] );

		$this->file_path = amem()->files->upload_basedir . 'logs' . DIRECTORY_SEPARATOR . ($this->dir ? $this->dir . DIRECTORY_SEPARATOR : '' ) . "{$this->name}.log";

		if ( ! file_exists( dirname($this->file_path) ) ) {
			amem()->files->filesystem()->mkdir( dirname($this->file_path), 0755 );
		}

		$this->search = dirname($this->file_path) . DIRECTORY_SEPARATOR . "{$this->name}*";

		$this->archive();
	}

	/**
	 * Add new record to the log
	 *
	 * @param  string $text
	 */
	public function add( $text ) {
		do_action( "amem/{$this->name}/log_add", $text );

		if ( !$this->enabled )
			return;

		if ( function_exists('is_wp_error') && is_wp_error($text) ) {
			$_text = "Caught exception: ". $text->get_error_message() ." Error code: ". $text->get_code();
			$text = $_text;
		} elseif ( is_object($text) || is_array($text) ) {
			$text = print_r($text, true);
		}

		$debug_string = "[".date("Y-m-d H:i:s",current_time('timestamp'));


		$debug_string .= "]: {$text}\n";


		if ( ! is_resource( $this->file ) ) {
			$this->file = fopen( $this->file_path, 'ab+' );
		}

		fwrite( $this->file, $debug_string );
	}

	/**
	 * Archive log if it is too big
	 */
	public function archive() {
		if ( is_file( $this->file_path ) && filesize( $this->file_path ) > $this->max_filesize ) {
			$dir      = dirname( $this->file_path );
			$datetime = gmdate( 'YmdHi' );
			$backup = "{$dir}/{$this->name}-{$datetime}.log";
			if ( is_writable( $dir ) && copy( $this->file_path, $backup ) ) {
				$this->clear();
				$this->backup( $backup, true );
			}
		}
	}

	public static function backup( $file, $remove = false ) {
		if ( !class_exists( 'PclZip' ) )
			require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');

		$archive = new \PclZip($file . '.zip');
		$v_list = $archive->add($file, PCLZIP_OPT_REMOVE_ALL_PATH, PCLZIP_OPT_ADD_TEMP_FILE_ON, PCLZIP_CB_PRE_ADD, 'amem/log/pclzip/pre_add');

		if ($v_list == 0) {
			return false;
		}

		if ( $remove ) 
			unlink( $file );

		return true;
	}

	/**
	 * Delete archive files
	 *
	 * @return int  The count of deleted files
	 */
	public function clear_archive() {
		$deleted = 0;
		$files   = glob( $this->search );

		foreach ( (array) $files as $file ) {
			if ( is_file( $file ) && unlink( $file ) ) {
				$deleted ++;
			}
		}

		return $deleted;
	}

	/**
	 * Clear log file
	 */
	public function clear() {
		if ( file_exists( $this->file_path ) ) {
			file_put_contents( $this->file_path, '' );
		}
	}

	/**
	 * Temporary files quantity
	 *
	 * @return int
	 */
	public function count_files() {
		$files = glob( $this->search );
		return count( (array) $files );
	}

	/**
	 * Get log content
	 *
	 * @return string
	 */
	public function get() {
		$content = (string) file_get_contents( $this->file_path );
		return $content;
	}

	/**
	 * Get log content as HTML
	 *
	 * @return string
	 */
	public function get_html() {

		if ( ! file_exists( $this->file_path ) ) {
			/* translators: %s: Provider name */
			return sprintf( esc_html__( 'No file "%s.log".', 'advanced-members' ), $this->name );
		}

		$log_arr = file( $this->file_path );
		foreach ( $log_arr as $key => $value ) {
			$log_arr[ $key ] = $this->format_html( trim( $value ) );
		}

		$content = implode( '</br>', $log_arr );

		return $content;
	}

	/**
	 * Format error as HTML text
	 *
	 * @param  array|string $error  An error data.
	 * @return string
	 */
	public function error_html( $error ) {
		if ( is_string( $error ) ) {
			return trim( $error );
		}

		$error_text = '';
		if ( ! empty( $error['title'] ) ) {
			$error_text .= '<strong>' . $error['title'] . '</strong> ';
		}
		if ( ! empty( $error['title'] ) && ! empty( $error['detail'] ) ) {
			$error_text .= ' - ';
		}
		if ( ! empty( $error['detail'] ) ) {
			$error_text .= $error['detail'] . ' ';
		}
		if ( ! empty( $error['errors'] ) && is_array( $error['errors'] ) ) {
			$error_text .= '<br><strong>' . __( 'Errors', 'advanced-members-pro' ) . ':</strong> ';
			foreach ( $error['errors'] as $error ) {
				if ( isset( $error['field'] ) && isset( $error['message'] ) ) {
					$error_text .= sprintf( "<br>'%s' - %s,", $error['field'], $error['message'] );
				}
			}
		}
		return trim( $error_text, ' ,;' );
	}

	/**
	 * Format log string
	 *
	 * @param  string $content  A log string.
	 * @return string
	 */
	public function format_html( $content ) {

		$content_b = str_replace(
			apply_filters( 'amem/log/format/string', array(
				'ERROR',
				'SUCCESS',
				'NOTICE',
				'ARGS:',
				'REFERER:',
				'RESPONSE:',
				'BACKTRACE:',
				'[GET]',
				'[POST]',
				'[PATCH]',
				'[DELETE]',
			) ),
			apply_filters( 'amem/log/format/replace', array(
				'<b style="color:darkred;">ERROR</b>',
				'<b style="color:darkgreen;">SUCCESS</b>',
				'<b style="color:darkgoldenrod;">NOTICE</b>',
				'<b>ARGS:</b>',
				'<b>REFERER:</b>',
				'<b>RESPONSE:</b>',
				'<b>BACKTRACE:</b>',
				'<b>[GET]</b>',
				'<b>[POST]</b>',
				'<b>[PATCH]</b>',
				'<b>[DELETE]</b>',
			) ),
			$content
		);

		return nl2br( $content_b );
	}

	/**
	 * Recursive remove links
	 *
	 * @param  mixed $arr  An array.
	 * @return mixed
	 */
	public function remove_links( $arr ) {
		if ( is_array( $arr ) ) {
			foreach ( $arr as $k => $v ) {
				if ( '_links' === $k ) {
					unset( $arr[ $k ] );
				} else {
					$arr[ $k ] = $this->remove_links( $v );
				}
			}
		}

		return $arr;
	}

	public function is_enabled() {
		return $this->enabled;
	}

	public function enable() {
		$this->enabled = true;
	}

	public function disable() {
		$this->enabled = false;
	}

}

// Initialize default log with amem.log
amem()->register_module( 'log', Log::getInstance() );