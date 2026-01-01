<?php
/**
 * Abstract Class for Two Factor Code Providers
 * @since 3.0.5
 */

abstract class Nexter_Two_Factor_Management_Providers{
    /**
     * Maintains singleton property of the class
     * @return Nexter_Two_Factor_Management_Providers
     */
    public static function get_instance(){
        static $instances = array();

        $class_name = static::class;

        if ( ! isset( $instances[ $class_name ] ) ) {
            $instances[ $class_name ] = new $class_name;
        }

        return $instances[ $class_name ];
    }
    public function get_key() {
        return get_class( $this );
    }

    /**
     * Constuctor of the Class
     */
    protected function __construct(){
        return $this;
    }

    abstract public function get_label();

    /**
     * @param WP_User $user
     */
    abstract public function authenticate_form_prompter($user);

    /**get_enabled_providers_for_user
     * @param WP_User $user
     * @return boolean
     */
    abstract public function is_applicable_on( $user ) ;

    /**
     * @param $lenght
     * @param $chars
     * @return string
     */
    public static function generate_random_code($length = 8 , $chars = ''){
        $code = '';
        if ( is_array( $chars ) ) {
            $chars = implode( '', $chars );
        }
        for ( $i = 0; $i < $length; $i++ ) {
            $code .= substr( $chars, wp_rand( 0, strlen( $chars ) - 1 ), 1 );
        }
        return $code;
    }

    /**
     * @param $field
     * @param $length
     * @return false|string
     */
    public static function sanitize_code_from_request( $field, $length = 0 ) {
        if ( empty( $_REQUEST[ $field ] ) ) {
            return false;
        }
        $code = wp_unslash( $_REQUEST[ $field ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, handled by the core method already.
        $code = preg_replace( '/\s+/', '', $code );
        if ( $length && strlen( $code ) !== $length ) {
            return false;
        }
        return (string) $code;
    }
}
