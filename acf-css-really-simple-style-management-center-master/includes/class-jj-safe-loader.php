<?php
/**
 * Safe Loader Class
 * 
 * Prevents fatal errors during file loading by checking existence and handling exceptions.
 * 
 * @package JJ_Style_Guide
 * @since 5.4.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'JJ_Safe_Loader' ) ) {
    class JJ_Safe_Loader {
        private static $instance = null;
        private $loaded_files = array();

        /**
         * 로더 진단 정보 (런타임 누적)
         * - safe_require는 static이므로 static 저장소로 기록합니다.
         *
         * @var array<string,array{path:string,required:bool,loaded:bool,error:string}>
         */
        private static $attempts = array();

        /**
         * @var array<string,array{path:string,required:bool,count:int}>
         */
        private static $missing_files = array();

        /**
         * @var array<string,array{path:string,required:bool,error:string}>
         */
        private static $load_errors = array();

        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public static function safe_require( $path, $required = true ) {
            $path = (string) $path;
            $required = (bool) $required;

            $attempt = array(
                'path'     => $path,
                'required' => $required,
                'loaded'   => false,
                'error'    => '',
            );

            if ( ! file_exists( $path ) ) {
                $key = md5( $path . '|' . ( $required ? '1' : '0' ) );
                if ( ! isset( self::$missing_files[ $key ] ) ) {
                    self::$missing_files[ $key ] = array(
                        'path'     => $path,
                        'required' => $required,
                        'count'    => 1,
                    );
                } else {
                    self::$missing_files[ $key ]['count']++;
                }

                $attempt['error'] = 'file_not_found';
                self::$attempts[ $path ] = $attempt;

                if ( $required && function_exists( 'error_log' ) ) {
                    error_log( 'JJ Safe Loader: File not found - ' . $path );
                }
                return false;
            }

            try {
                require_once $path;
                $attempt['loaded'] = true;
                self::$attempts[ $path ] = $attempt;
                return true;
            } catch ( Exception $e ) {
                $attempt['error'] = $e->getMessage();
                self::$attempts[ $path ] = $attempt;
                self::$load_errors[ $path ] = array(
                    'path'     => $path,
                    'required' => $required,
                    'error'    => $attempt['error'],
                );
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'JJ Safe Loader: Exception loading ' . $path . ' - ' . $e->getMessage() );
                }
                return false;
            } catch ( Error $e ) {
                $attempt['error'] = $e->getMessage();
                self::$attempts[ $path ] = $attempt;
                self::$load_errors[ $path ] = array(
                    'path'     => $path,
                    'required' => $required,
                    'error'    => $attempt['error'],
                );
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'JJ Safe Loader: Fatal Error loading ' . $path . ' - ' . $e->getMessage() );
                }
                return false;
            }
        }

        /**
         * @return array<int,array{path:string,required:bool,loaded:bool,error:string}>
         */
        public static function get_attempts() {
            return array_values( self::$attempts );
        }

        /**
         * @return array<int,array{path:string,required:bool,count:int}>
         */
        public static function get_missing_files() {
            return array_values( self::$missing_files );
        }

        /**
         * @return array<int,array{path:string,required:bool,error:string}>
         */
        public static function get_load_errors() {
            return array_values( self::$load_errors );
        }
    }
}

