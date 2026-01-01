<?php 
/*
 * Performance & Security Extension
 * @since 4.1.0
 */
defined('ABSPATH') or die();

function nxt_string_encode_str($input, $useHex = false) {
    $characters = str_split($input);
    $length = strlen($input);

    // Avoid division by zero, ensure $length > 0
    if ($length === 0) {
        return '';
    }

    // Seed based on crc32 hash and input length
    $seed = mt_rand(0, (int) abs(crc32($input) / $length));

    foreach ($characters as $index => $char) {
        $ascii = ord($char);

        // Process only ASCII characters (<128)
        if ($ascii < 128) {
            // Generate a pseudo-random number based on seed and position
            $randValue = ($seed * ($index + 1)) % 100;

            // Encode according to conditions:
            if ($randValue > 75 && $char !== '@' && $char !== '.') {
                // Leave character as is (plain)
                continue;
            }

            if ($useHex && $randValue < 25) {
                // Encode as %xx (URL hex encoding style)
                $characters[$index] = '%' . bin2hex($char);
            } elseif ($randValue < 45) {
                // Encode as hexadecimal entity
                $characters[$index] = '&#x' . dechex($ascii) . ';';
            } else {
                // Encode as decimal entity
                $characters[$index] = '&#' . $ascii . ';';
            }
        }
    }

    return implode('', $characters);
}


class Nexter_Ext_Pro_Performance_Security {
    
    /**
     * Constructor
     */
    public function __construct() {
		
		$extension_option = get_option( 'nexter_site_security' );

		$adv_sec_opt = $extension_option;
		if(isset($adv_sec_opt['advance-security']) && !empty($adv_sec_opt['advance-security']) && isset($adv_sec_opt['advance-security']['switch']) && !empty($adv_sec_opt['advance-security']['switch'])){
			if(isset($adv_sec_opt['advance-security']['values']) && !empty($adv_sec_opt['advance-security']['values'])){
				$adv_sec_opt = $adv_sec_opt['advance-security']['values'];
			}
		}
		//Email Address Obfuscator
		if(isset($adv_sec_opt) && !empty($adv_sec_opt) && in_array('obfuscator_email_address',$adv_sec_opt)){
			add_shortcode( 'obfuscate', [$this, 'obfuscate_data_render'] );
            add_filter( 'safe_style_css', [$this, 'add_attributes_to_safe_css'] );
            add_filter( 'widget_text', 'shortcode_unautop' );
            add_filter( 'widget_text', 'do_shortcode' );
		}

		//Obfuscate Author Slugs
		if(isset($adv_sec_opt) && !empty($adv_sec_opt) && in_array('obfuscator_author_slug',$adv_sec_opt)){
			add_action( 'pre_get_posts', [$this, 'modify_author_query'], 10 ); 
			add_filter( 'author_link', [$this, 'modify_author_link'], 10, 3 );
			add_filter( 'rest_prepare_user', [$this, 'modify_json_user'], 10, 3 );
		}

		//Encode Telephone Secure
		if(isset($adv_sec_opt) && !empty($adv_sec_opt) && in_array('hide_telephone_secure',$adv_sec_opt)){
			add_shortcode( 'nxt_encode', [$this, 'nxt_encode_data_render'] );
		}
    }
	
	/**
	 * Obfuscate an email address on the frontend using WordPress's antispambot().
	 */
	public function obfuscate_data_render( $atts ) {
		$atts = shortcode_atts( [
			'email'   => '',
			'subject' => '',
			'text'    => '',
			'display' => 'newline',
			'link'    => 'no',
			'class'   => '',
		], $atts );

		$email = $atts['email'];
		if ( ! is_email( $email ) ) {
			return;
		}

		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '';
		$is_firefox_or_iphone = stripos( $user_agent, 'firefox' ) !== false || stripos( $user_agent, 'iphone' ) !== false;

		if ( $is_firefox_or_iphone ) {
			$reversed_email = $email;
			$bidi_styles = '';
			$direction = 'direction:rtl;';
		} else {
			$reversed_email = strrev( $email );
			$bidi_styles = 'unicode-bidi:bidi-override;';
			$direction = 'direction:rtl;';
		}

		$email_parts = explode( '@', $reversed_email );

		if ( ! empty( $atts['text'] ) ) {
			$text = esc_html( $atts['text'] );
			$bidi_styles = $direction = '';
		} else {
			$random_id = dechex( rand( 1000000, 9999999 ) );
			$text = esc_html( $email_parts[0] ) . '<span style="display:none;">obfsctd-' . esc_html( $random_id ) . '</span>&#64;' . esc_html( $email_parts[1] );
		}

		$display_style = $atts['display'] === 'inline' ? 'display:inline;' : 'display:flex;justify-content:flex-end;';
		$class = esc_attr( $atts['class'] );

		$styles = esc_attr( $display_style . $bidi_styles . $direction );

		return sprintf( '<span style="%s" class="%s">%s</span>', $styles, $class, $text );
	}

	
	/**
	 * Allow CSS properties when sanitizing email output.
	 */
	public function add_attributes_to_safe_css( $css_attributes ) {
		$css_attributes[] = 'display';
		$css_attributes[] = 'unicode-bidi';
		$css_attributes[] = 'direction'; // Also used in the code above
		return $css_attributes;
	}

	
	/**
	 * Modifies the author query to decrypt the author slug when querying for an author.
	 */
	public function modify_author_query( $query ) {
		if ( $query->is_author() && !empty($query->query_vars['author_name']) ) {
			$author_slug = $query->query_vars['author_name'];

			// Check if the author slug is a hexadecimal string
			if ( ctype_xdigit( $author_slug ) ) {
				// Decrypt the author slug and get the user by ID
				$user_id = $this->decrypt_data( $author_slug );
				$user = get_user_by( 'id', $user_id );

				if ( $user ) {
					// Set the decrypted user nicename in the query
					$query->set( 'author_name', $user->user_nicename );
				} else {
					// If user is not found, show 404
					$query->set_404();
				}
			} else {
				// If not a valid encrypted slug, show 404
				$query->set_404();
			}
		}
	}

	
	/**
	 * Modifies the author link to use the encrypted user ID as the author slug.
	 */
	public function modify_author_link( $link, $user_id, $author_slug ) {
		// Encrypt the user ID to replace the author slug
		$encrypted_slug = $this->encrypt_data( $user_id );
		
		// Replace the original author slug with the encrypted one
		return str_replace( "/$author_slug", "/$encrypted_slug", $link );
	}
	
	/**
	 * Modifies the REST API user endpoint response to use the encrypted user ID as the slug.
	 */
	public function modify_json_user( $response, $user, $request ) {
		$data = $response->get_data();
		// Encrypt the user ID to modify the slug
		$data['slug'] = $this->encrypt_data( $data['id'] );
		$response->set_data( $data );

		return $response;
	}

	/**
	 * Encrypts the user ID to create a unique, secure author slug.
	 */
	private function encrypt_data( $user_id ) {
		return bin2hex( openssl_encrypt( base_convert( $user_id, 10, 36 ), 'DES-EDE3', md5( NXT_PRO_EXT_URI ), OPENSSL_RAW_DATA ) );
	}

	/**
	 * Decrypts the encrypted author slug to retrieve the user ID.
	 */
	private function decrypt_data( $encrypted_slug ) {
		return base_convert( openssl_decrypt( pack('H*', $encrypted_slug), 'DES-EDE3', md5( NXT_PRO_EXT_URI ), OPENSSL_RAW_DATA ), 36, 10 );
	}

	public function nxt_encode_data_render( $attributes, $content = '' ) {
		$atts = shortcode_atts( array(
			'link'  => null,
			'class' => null,
		), $attributes, 'nxt_encode' );

		$link = $atts['link'];
    	$class = $atts['class'];

		if (empty($atts['link']) || empty($content)) return '';

		// Multibyte-safe character split
		if (!function_exists('mb_str_split')) {
			function mb_str_split($string) {
				return preg_split('/(?<!^)(?!$)/u', $string);
			}
		}

		if (!function_exists('mb_ord')) {
			function mb_ord($char, $encoding = 'UTF-8') {
				if (function_exists('iconv')) {
					$char = iconv($encoding, 'UTF-32BE', $char);
					$code = unpack('N', $char);
					return $code[1];
				}
				return ord($char);
			}
		}

		// Encode each character into HTML entity
		$encoded_href = '';
		foreach (mb_str_split($atts['link']) as $char) {
			$encoded_href .= '&#' . mb_ord($char) . ';';
		}

		// Avoid using esc_attr() to preserve entity codes
		$class_attr = $atts['class'] ? ' class="' . esc_attr($atts['class']) . '"' : '';

		return '<a href="' . $encoded_href . '"' . $class_attr . '>' . esc_html($content) . '</a>';
	}

}

new Nexter_Ext_Pro_Performance_Security();