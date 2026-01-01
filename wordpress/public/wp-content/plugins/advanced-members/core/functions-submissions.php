<?php

/**
* Encrypt an array using PHP
* https://bhoover.com/using-php-openssl_encrypt-openssl_decrypt-encrypt-decrypt-data/
*
* @since  1.0
* @param   $data (array)
* @return  (string)
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

function amem_encrypt( $data = array() ) {
  if ( empty( $data ) ) {
    return false;
  }

  $data = wp_json_encode( $data );

  // bail early if no encrypt function
  if ( ! function_exists( 'openssl_encrypt' ) ) {
    return base64_encode( $data );
  }

  // generate a key
  $key = wp_hash( 'amem_encrypt' );

  // Generate an initialization vector
  $iv = openssl_random_pseudo_bytes( openssl_cipher_iv_length( 'aes-256-cbc' ) );

  // Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
  $encrypted_data = openssl_encrypt( $data, 'aes-256-cbc', $key, 0, $iv );

  // The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)
  return base64_encode( $encrypted_data . '::' . $iv );

}

/**
* Decrypt an encrypted string using PHP
* https://bhoover.com/using-php-openssl_encrypt-openssl_decrypt-encrypt-decrypt-data/
*
* @since   1.0
*
* @param   $data (string)
* @return  (array)
*/
function amem_decrypt( $data = '' ) {
  if ( empty( $data ) ) {
    return false;
  }

  // bail early if no decrypt function
  if ( ! function_exists( 'openssl_decrypt' ) ) {
    return json_decode( base64_decode( $data ), true );
  }

  // generate a key
  $key = wp_hash( 'amem_encrypt' );

  // To decrypt, split the encrypted data from our IV - our unique separator used was "::"
  list($encrypted_data, $iv) = explode( '::', base64_decode( $data ), 2 );

  // decrypt
  $data = openssl_decrypt( $encrypted_data, 'aes-256-cbc', $key, 0, $iv );

  return json_decode( $data, true );

}

/**
 * Return true if a submission success
 *
 * @since 1.0
 */
function amem_has_submission( $hash = false ) {
  $submission = amem()->submission;

  if ( empty( $submission ) ) {
    return false;
  }
  
  if ( $hash && $hash != amem_form_instance_hash( $submission['form']['key'], $submission['args'] ) ) {
    return false;
  }
  
  return true;
}


/**
 * Return true if submission failed
 *
 * @since 1.0
 */
function amem_submission_failed( $key = false ) {
  $submission = amem()->submission;

  if ( empty( $submission ) ) {
    return false;
  }

  if ( $key && $key != $submission['form']['key'] ) {
    return false;
  }

  if ( isset( $submission['errors'] ) && ! empty( $submission['errors'] ) ) {
    return true;
  }

  if ( amem()->errors->has_errors() )
    return true;

  return false;
}

/**
 * Adds a general error for a form submission.
 * Used during the before_submission hook to stop submission.
 *
 * @since 1.0
 */
function amem_add_submission_error( $message, $input=null ) {
  return amem()->errors->add($input, $message);
}

/**
 * AMem errors are basically controlled by ACF
 *
 * @since 1.0
 *
 */
function amem_add_error( $field_key_or_name, $message ) {
  $field = amem_get_field_object( $field_key_or_name );

  if ( $field ) {
    if ( $field['prefix'] )
    $input_name = sprintf( '%s[%s]', $field['prefix'], $field['key'] );
    acf_add_validation_error( $input_name, $message );
  } else {
    acf_add_validation_error( false, $message );
  }
}

/**
 * Add inline error message without submission
 * @since  1.0.0
 * @param  string $code
 * @param  string $message
 */
function amem_add_form_error( $code, $message ) {
  return amem()->errors->form_error($code, $message);
}

/**
 * Calculates a unique hash for a form instance, based on the form key and arguments.
 *
 * @since 1.0
 *
 */
function amem_form_instance_hash( $form_key, $args ) {
  $args['form'] = $form_key;

  // Sort args to make the hash order-independent
  ksort( $args );

  return md5( wp_json_encode( $args ) );
}