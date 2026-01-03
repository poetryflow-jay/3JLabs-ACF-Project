<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * HMAC-based Authentication for WP Bulk Manager
 * - Replaces plain-text secret key with HMAC-SHA256 signatures
 * - Prevents replay attacks with timestamp + nonce
 * 
 * @package WP_Bulk_Manager
 * @version 22.2.0
 * @author Mikael (Security) + Jason (Implementation)
 */
class JJ_Bulk_HMAC_Auth {
    private static $instance = null;
    private $secret_key_option = 'jj_bulk_hmac_secret_key';
    private $nonce_cache_option = 'jj_bulk_hmac_nonces';
    private $max_time_diff = 300; // 5 minutes
    private $nonce_cache_size = 100;
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get or create secret key
     */
    public function get_secret_key() {
        $key = get_option( $this->secret_key_option );
        
        if ( empty( $key ) ) {
            $key = $this->generate_secret_key();
            update_option( $this->secret_key_option, $key, false );
        }
        
        return $key;
    }

    /**
     * Generate cryptographically secure key
     */
    private function generate_secret_key() {
        if ( function_exists( 'random_bytes' ) ) {
            return bin2hex( random_bytes( 32 ) ); // 64 hex characters
        } else {
            // Fallback for older PHP
            return bin2hex( openssl_random_pseudo_bytes( 32 ) );
        }
    }

    /**
     * Generate HMAC signature
     * 
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $path Request path
     * @param array $params Request parameters
     * @param int $timestamp Unix timestamp
     * @param string $nonce Random nonce
     * @return string HMAC signature
     */
    public function generate_signature( $method, $path, $params, $timestamp, $nonce ) {
        $secret_key = $this->get_secret_key();
        
        // Sort params by key for consistent signature
        ksort( $params );
        
        // Build string to sign
        $string_to_sign = implode( '&', array(
            strtoupper( $method ),
            $path,
            http_build_query( $params ),
            $timestamp,
            $nonce,
        ) );
        
        return hash_hmac( 'sha256', $string_to_sign, $secret_key );
    }

    /**
     * Verify HMAC signature from request
     * 
     * @param WP_REST_Request|array $request Request object or array
     * @return bool|WP_Error True if valid, WP_Error otherwise
     */
    public function verify_request( $request ) {
        // Extract headers
        if ( is_a( $request, 'WP_REST_Request' ) ) {
            $signature = $request->get_header( 'X-JJ-Signature' );
            $timestamp = $request->get_header( 'X-JJ-Timestamp' );
            $nonce = $request->get_header( 'X-JJ-Nonce' );
            $method = $request->get_method();
            $path = $request->get_route();
            $params = $request->get_params();
        } else {
            // Array format (for AJAX)
            $signature = isset( $_SERVER['HTTP_X_JJ_SIGNATURE'] ) ? $_SERVER['HTTP_X_JJ_SIGNATURE'] : '';
            $timestamp = isset( $_SERVER['HTTP_X_JJ_TIMESTAMP'] ) ? $_SERVER['HTTP_X_JJ_TIMESTAMP'] : '';
            $nonce = isset( $_SERVER['HTTP_X_JJ_NONCE'] ) ? $_SERVER['HTTP_X_JJ_NONCE'] : '';
            $method = isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : 'POST';
            $path = isset( $_SERVER['REQUEST_URI'] ) ? parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) : '';
            $params = $request;
        }
        
        // Check required fields
        if ( empty( $signature ) || empty( $timestamp ) || empty( $nonce ) ) {
            return new WP_Error( 
                'missing_auth_headers', 
                __( '인증 헤더가 누락되었습니다.', 'wp-bulk-manager' ),
                array( 'status' => 401 )
            );
        }
        
        // Check timestamp (prevent replay attacks)
        $time_diff = abs( time() - intval( $timestamp ) );
        if ( $time_diff > $this->max_time_diff ) {
            return new WP_Error(
                'timestamp_expired',
                sprintf( 
                    __( '요청이 만료되었습니다. (시간 차이: %d초)', 'wp-bulk-manager' ),
                    $time_diff
                ),
                array( 'status' => 401 )
            );
        }
        
        // Check nonce (prevent reuse)
        if ( $this->is_nonce_used( $nonce ) ) {
            return new WP_Error(
                'nonce_reused',
                __( '이미 사용된 nonce입니다. 재사용 공격이 감지되었습니다.', 'wp-bulk-manager' ),
                array( 'status' => 401 )
            );
        }
        
        // Generate expected signature
        $expected_signature = $this->generate_signature( $method, $path, $params, $timestamp, $nonce );
        
        // Constant-time comparison to prevent timing attacks
        if ( ! hash_equals( $expected_signature, $signature ) ) {
            return new WP_Error(
                'invalid_signature',
                __( '서명이 유효하지 않습니다.', 'wp-bulk-manager' ),
                array( 'status' => 401 )
            );
        }
        
        // Mark nonce as used
        $this->mark_nonce_used( $nonce, $timestamp );
        
        return true;
    }

    /**
     * Check if nonce has been used
     */
    private function is_nonce_used( $nonce ) {
        $used_nonces = get_option( $this->nonce_cache_option, array() );
        return isset( $used_nonces[ $nonce ] );
    }

    /**
     * Mark nonce as used
     */
    private function mark_nonce_used( $nonce, $timestamp ) {
        $used_nonces = get_option( $this->nonce_cache_option, array() );
        
        // Add current nonce
        $used_nonces[ $nonce ] = intval( $timestamp );
        
        // Clean up old nonces (older than max_time_diff)
        $cutoff_time = time() - ( $this->max_time_diff * 2 );
        $used_nonces = array_filter( $used_nonces, function( $ts ) use ( $cutoff_time ) {
            return $ts > $cutoff_time;
        } );
        
        // Limit cache size
        if ( count( $used_nonces ) > $this->nonce_cache_size ) {
            // Keep only the most recent nonces
            arsort( $used_nonces );
            $used_nonces = array_slice( $used_nonces, 0, $this->nonce_cache_size, true );
        }
        
        update_option( $this->nonce_cache_option, $used_nonces, false );
    }

    /**
     * Generate client request headers
     * Helper for making authenticated requests
     */
    public function generate_client_headers( $method, $path, $params ) {
        $timestamp = time();
        $nonce = bin2hex( random_bytes( 16 ) );
        $signature = $this->generate_signature( $method, $path, $params, $timestamp, $nonce );
        
        return array(
            'X-JJ-Signature' => $signature,
            'X-JJ-Timestamp' => $timestamp,
            'X-JJ-Nonce' => $nonce,
        );
    }

    /**
     * Regenerate secret key (use with caution)
     */
    public function regenerate_secret_key() {
        $new_key = $this->generate_secret_key();
        update_option( $this->secret_key_option, $new_key, false );
        
        // Clear nonce cache
        delete_option( $this->nonce_cache_option );
        
        return $new_key;
    }

    /**
     * Get statistics about authentication
     */
    public function get_stats() {
        $used_nonces = get_option( $this->nonce_cache_option, array() );
        
        return array(
            'cached_nonces' => count( $used_nonces ),
            'max_cache_size' => $this->nonce_cache_size,
            'max_time_diff' => $this->max_time_diff,
            'oldest_nonce' => ! empty( $used_nonces ) ? min( $used_nonces ) : null,
            'newest_nonce' => ! empty( $used_nonces ) ? max( $used_nonces ) : null,
        );
    }
}
