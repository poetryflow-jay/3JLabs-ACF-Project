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

        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public static function safe_require( $path, $required = true ) {
            if ( ! file_exists( $path ) ) {
                if ( $required && function_exists( 'error_log' ) ) {
                    error_log( 'JJ Safe Loader: File not found - ' . $path );
                }
                return false;
            }

            try {
                require_once $path;
                return true;
            } catch ( Exception $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'JJ Safe Loader: Exception loading ' . $path . ' - ' . $e->getMessage() );
                }
                return false;
            } catch ( Error $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'JJ Safe Loader: Fatal Error loading ' . $path . ' - ' . $e->getMessage() );
                }
                return false;
            }
        }
    }
}

