<?php

/**
 * A class for Email as provider for 2fa codes
 * An Implementation of Nexter_Two_Factor_Management_Providers
 *
 */

class Nexter_Two_Factor_Management_Provider_Email extends Nexter_Two_Factor_Management_Providers{
	/**
	 * OTP
	 */
    const NEXTER_TWO_FACTOR_EMAIL_TOKEN = '_nexter_two_factor_email_token';
	/**
	 * Timestamp of when token was generated for Email
	 */
    const NEXTER_TWO_FACTOR_EMAIL_TOKEN_TIMESTAMP = '_nexter_two_factor_email_token_timestamp';

    const NEXTER_2_FA_CODE_RESEND_FIELD_NAME = 'nexter_2fa_code_resend_field_name';

    public function get_label() {
        return _x( 'Email', 'Provider Label', 'nexter-pro-extensions' );
    }

    public function get_alternative_provider_label() {
        return __( 'Send a 2FA code to your email', 'nexter-pro-extensions' );
    }

    /**
     * Generate the user token.
     *
     * @since 0.1-dev
     *
     * @param int $user_id User ID.
     * @return string
     */
    public function generate_token( $user_id ) {
        $token = $this->get_code();

        update_user_meta( $user_id, self::NEXTER_TWO_FACTOR_EMAIL_TOKEN_TIMESTAMP, time() );
        update_user_meta( $user_id, self::NEXTER_TWO_FACTOR_EMAIL_TOKEN, wp_hash( $token ) );

        return $token;
    }

    /**
     * Check if user has a valid token already.
     *
     * @param  int $user_id User ID.
     * @return boolean      If user has a valid email token.
     */
    public function user_has_token( $user_id ) {
        $hashed_token = $this->get_user_token( $user_id );

        if ( ! empty( $hashed_token ) ) {
            return true;
        }

        return false;
    }

    /**
     * Has the user token validity timestamp expired.
     *
     * @param integer $user_id User ID.
     *
     * @return boolean
     */
    public function user_token_has_expired( $user_id ) {
        $token_lifetime = $this->user_token_lifetime( $user_id );
        $token_ttl      = $this->user_token_ttl( $user_id );

        // Invalid token lifetime is considered an expired token.
        if ( is_int( $token_lifetime ) && $token_lifetime <= $token_ttl ) {
            return false;
        }

        return true;
    }


    /**
     * Get the lifetime of a user token in seconds.
     *
     * @param integer $user_id User ID.
     *
     * @return integer|null Return `null` if the lifetime can't be measured.
     */
    public function user_token_lifetime( $user_id ) {
        $timestamp = intval( get_user_meta( $user_id, self::NEXTER_TWO_FACTOR_EMAIL_TOKEN_TIMESTAMP, true ) );

        if ( ! empty( $timestamp ) ) {
            return time() - $timestamp;
        }

        return null;
    }
    /**
     * Return the token time-to-live for a user.
     *
     * @param integer $user_id User ID.
     *
     * @return integer
     */
    public function user_token_ttl( $user_id ) {
        $token_ttl = 15 * MINUTE_IN_SECONDS;

        /**
         * Number of seconds the token is considered valid
         * after the generation.
         *
         * @param integer $token_ttl Token time-to-live in seconds.
         * @param integer $user_id User ID.
         */
        return (int) apply_filters( 'two_factor_token_ttl', $token_ttl, $user_id );
    }

    /**
     * Get the authentication token for the user.
     *
     * @param  int $user_id    User ID.
     *
     * @return string|boolean  User token or `false` if no token found.
     */
    public function get_user_token( $user_id ) {
        $hashed_token = get_user_meta( $user_id, self::NEXTER_TWO_FACTOR_EMAIL_TOKEN, true );

        if ( ! empty( $hashed_token ) && is_string( $hashed_token ) ) {
            return $hashed_token;
        }

        return false;
    }


    /**
     * Validate the user token.
     *
     * @since 0.1-dev
     *
     * @param int    $user_id User ID.
     * @param string $token User token.
     * @return boolean
     */
    public function validate_token( $user_id, $token ) {
        $hashed_token = $this->get_user_token( $user_id );

        // Bail if token is empty or it doesn't match.
        if ( empty( $hashed_token ) || ! hash_equals( wp_hash( $token ), $hashed_token ) ) {
            return false;
        }

        if ( $this->user_token_has_expired( $user_id ) ) {
            return false;
        }

        // Ensure the token can be used only once.
        $this->delete_token( $user_id );

        return true;
    }


    /**
     * Delete the user token.
     *
     * @since 0.1-dev
     *
     * @param int $user_id User ID.
     */
    public function delete_token( $user_id ) {
        delete_user_meta( $user_id, self::NEXTER_TWO_FACTOR_EMAIL_TOKEN );
    }


    public function replace_hot_tags($message,$token,$user){
	    $username = $user->user_login;
	    $nicename = $user->user_nicename;
	    $time = date_i18n(get_option('date_format'), current_time('timestamp'));
        $site_title = get_bloginfo( 'name' );
	    $tagReplacements = array(
		    '{username}' => $username,
		    '[username]' => $username,
		    '{nicename}' => $nicename,
            '[nicename]' => $nicename,
		    '{token}' => $token,
            '[token]' => $token,
		    '{time}' => $time,
            '{Time}' => $time,
		    '[time]' => $time,
            '[Time]' => $time,
		    '{SiteName}' => $site_title,
		    '[SiteName]' => $site_title,
	    );
	    $replacedText = str_replace(array_keys($tagReplacements), array_values($tagReplacements), $message);
        return $replacedText;
    }
    /**
     * Generate and email the user token.
     *
     * @since 0.1-dev
     *
     * @param WP_User $user WP_User object of the logged-in user.
     * @return bool Whether the email contents were sent successfully.
     */
    public function generate_and_email_token( $user ) {
        $token = $this->generate_token( $user->ID );
	    $site_security_options = get_option( 'nexter_site_security',array() );
	    $custom_email = (!isset($site_security_options['2-fac-authentication']['values']['email_customisations']) && empty($site_security_options['2-fac-authentication']['values']['email_customisations']) ) ? array() : $site_security_options['2-fac-authentication']['values']['email_customisations'] ;

        /* translators: %s: site name */
        $subject = wp_strip_all_tags( sprintf( __( "Your 2FA Verification Code for %s", 'nexter-pro-extensions' ), wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) ) );
        /* translators: %s: token */
        $message = wp_strip_all_tags( sprintf( __( "Hello %1\$s,\r\nTo complete your login, please enter the following Two-Factor Authentication code:\nCode: %2\$s\nThis code is valid for the next 10 minutes. If this wasn't you, please change your password immediately or contact the site admin.\nBest regards,\n%3\$s\nTime Sent: %4\$s", 'nexter-pro-extensions' ),$user->user_login,$token,wp_specialchars_decode( get_option( 'blogname')) ,current_time('mysql') ) );
        if(isset($custom_email['subject']) && !empty($custom_email['subject'])){
            $subject = $custom_email['subject'];
        }
        if(isset($custom_email['body']) && !empty($custom_email['body'])){
	        $message = $this->replace_hot_tags($custom_email['body'] , $token,$user);
        }

        /**
         * Filter the token email subject.
         *
         * @param string $subject The email subject line.
         * @param int    $user_id The ID of the user.
         */
        $subject = apply_filters( 'nexter_two_factor_token_email_subject', $subject, $user->ID );

        /**
         * Filter the token email message.
         *
         * @param string $message The email message.
         * @param string $token   The token.
         * @param int    $user_id The ID of the user.
         */
        $message = apply_filters( 'nexter_two_factor_token_email_message', $message, $token, $user->ID );
	    /**
	     * Check if the mail have been sent.
	     */
//        $super_admin = get_super_admin();
//	    $super_admin_user = get_user_by('login', $super_admin);
        $admin_email = get_option('admin_email');

	    $headers = array(
		    'From: ' . get_bloginfo('name') . ' <' . $admin_email . '>',
		    'Content-Type: text/html; charset=UTF-8',
	    );
        $mail_sent = wp_mail( $user->user_email, $subject, $message ,$headers); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.wp_mail_wp_mail

	    return (bool)$mail_sent;
    }

	public function get_key() {
		return get_class( $this );
	}


    /**
     * Prints the form that prompts the user to authenticate.
     *
     * @since 0.1-dev
     *
     * @param WP_User $user WP_User object of the logged-in user.
     */
    public function authentication_page( $user ) {
        if ( ! $user ) {
            return;
        }

        if ( ! $this->user_has_token( $user->ID ) || $this->user_token_has_expired( $user->ID ) ) {
            $this->generate_and_email_token( $user );
        }

        require_once ABSPATH . '/wp-admin/includes/template.php';
        ?>
        <p class="two-factor-prompt"><?php esc_html_e( 'A verification code has been dispatched to the email address linked to your account.', 'nexter-pro-extensions' ); ?></p>
        <p>
            <label for="authcode"><?php esc_html_e( 'Verification Code:', 'nexter-pro-extensions' ); ?></label>
            <input type="text" inputmode="numeric" name="two-factor-email-code" id="authcode" class="input authcode" value="" size="20" pattern="[0-9 ]*" placeholder="1234 5678" data-digits="8" />
            <?php submit_button( __( 'Submit', 'nexter-pro-extensions' ) ); ?>
        </p>
        <p class="two-factor-email-resend">
            <input type="submit" class="button" name="<?php echo esc_attr( self::NEXTER_2_FA_CODE_RESEND_FIELD_NAME ); ?>" value="<?php esc_attr_e( 'Resend Code', 'nexter-pro-extensions' ); ?>" />
        </p>
        <script type="text/javascript">
            setTimeout( function(){
                var d;
                try{
                    d = document.getElementById('authcode');
                    d.value = '';
                    d.focus();
                } catch(e){}
            }, 200);
        </script>
        <?php
    }

    /**
     * Send the email code if missing or requested. Stop the authentication
     * validation if a new token has been generated and sent.
     *
     * @param  WP_USer $user WP_User object of the logged-in user.
     * @return boolean
     */
    public function pre_process_authentication( $user ) {
        if ( isset( $user->ID ) && isset( $_REQUEST[ self::NEXTER_2_FA_CODE_RESEND_FIELD_NAME ] ) ) {
            $this->generate_and_email_token( $user );
            return true;
        }

        return false;
    }



    /**
     * Validates the users input token.
     *
     * @since 0.1-dev
     *
     * @param WP_User $user WP_User object of the logged-in user.
     * @return boolean
     */
    public function validate_authentication( $user ) {
        $code = $this->sanitize_code_from_request( 'two-factor-email-code' );
        if ( ! isset( $user->ID ) || ! $code ) {
            return false;
        }

        return $this->validate_token( $user->ID, $code );
    }


    /**
     * Whether this Two Factor provider is configured and available for the user specified.
     *
     * @since 0.1-dev
     *
     * @param WP_User $user WP_User object of the logged-in user.
     * @return boolean
     */
    public function is_applicable_on( $user ) {
        return true;
    }


    /**
     * Inserts markup at the end of the user profile field for this provider.
     * @param WP_User $user WP_User object of the logged-in user.
     */
    public function user_options( $user ) {
        $email = $user->user_email;
        ?>
        <div>
            <?php
            echo esc_html(
                sprintf(
                /* translators: %s: email address */
                    __( 'Authentication codes will be sent to %s.', 'nexter-pro-extensions' ),
                    $email
                )
            );
            ?>
        </div>
        <?php
    }

    public function authenticate_form_prompter($user)
    {
        // TODO: Implement authenticate_form_prompter() method.
    }
    public function is_available_for_user( $user ) {
        return true;
    }
    public static function get_code( $length = 8, $chars = '1234567890' ) {
        $code = '';
        if ( is_array( $chars ) ) {
            $chars = implode( '', $chars );
        }
        for ( $i = 0; $i < $length; $i++ ) {
            $code .= substr( $chars, wp_rand( 0, strlen( $chars ) - 1 ), 1 );
        }
        return $code;
    }
}