<?php 
/**
 * Nexter Migration Theme Customizer Option Update
 *
 * @package	Nexter
 * @since	4.2.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', 'nexter_theme_upgrade_handler' );

function nexter_theme_upgrade_handler() {
    $saved_version   = get_option( 'nxt_theme_version', '' );

    // Case 1: Fresh install (no saved version + no old options yet)
    if ( empty( $saved_version ) && ! nexter_has_old_options() ) {
        
        nexter_set_default_options();
        update_option( 'nxt_theme_version', NXT_VERSION );
        return;
    }

    // Case 2: Upgrade from old (<4.2.0) → no nxt_theme_version set
    if ( empty( $saved_version ) && nexter_has_old_options() ) {
        nexter_migrate_header_footer_container_option();
        update_option( 'nxt_theme_version', NXT_VERSION );
    }
}

function nexter_has_old_options() {
    $options = get_option( 'nxt-theme-options', [] );

    if ( empty( $options ) ) {
        return false;
    }

    return (
        ! empty( $options['site-header-container-width'] )
        || ! empty( $options['site-header-container'] )
        || ! empty( $options['site-footer-container-width'] )
        || ! empty( $options['site-footer-container'] )
    );
}

function nexter_set_default_options() {
     $options = get_option( 'nxt-theme-options', [] );
        if ( empty( $options['site-header-container'] ) ) {
            $options['site-header-container'] = '';
        }
        if ( empty( $options['site-footer-container'] ) ) {
            $options['site-footer-container'] = '';
        }
    update_option( 'nxt-theme-options', $options );
}
/**
 * Migrate Header and Footer Container Option
 */
function nexter_migrate_header_footer_container_option() {
    $options = get_option( 'nxt-theme-options', [] );

    //Header Default Container Migration
    if ( !empty($options) && ( empty( $options['site-header-container'] ) || ! isset( $options['site-header-container'] ) ) ) {
        // Set default as "container-block-editor"
        $options['site-header-container'] = 'container-block-editor';
    }

    //Footer Default Container Migration
    if ( !empty($options) && ( empty( $options['site-footer-container'] ) || ! isset( $options['site-footer-container'] ) ) ) {
        // Set default as "container-block-editor"
        $options['site-footer-container'] = 'container-block-editor';
    }
    update_option( 'nxt-theme-options', $options );
}