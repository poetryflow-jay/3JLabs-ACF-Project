<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'drillairlines' );

/** Database username */
define( 'DB_USER', 'drillairlines' );

/** Database password */
define( 'DB_PASSWORD', 'fmr6bv9xvrMaUJz' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          '{Y;mq<$l?$XF4VY`y@=i(4$ B6`Iq2D:4Sv+!EH}+:~XO@|Zk@p5;7ycN4Um@4:e' );
define( 'SECURE_AUTH_KEY',   '.c%@Iw2HC,EPwFnE7*w}AODTF1e-k=EH4Wh0.O/P_F_Ss }D~7MW^d2e.sAmM-$v' );
define( 'LOGGED_IN_KEY',     'Q5>6NNo&[~O#6; MBnkM$gRgqZHLeW*^PgQ>yHV~UyB=n`$mB%=.-DaPy7X-mn7T' );
define( 'NONCE_KEY',         'l:o^AaU#?^BnF^q{.%zVc>$1{!EH!pYa&PI(/S[Oa>Et?&qsT`c]SdOXNs qGJ@b' );
define( 'AUTH_SALT',         'eLW^yPk-|p+]M_?0R`R/Au(N$q/]hL.5J+>}m<$/X`RDMn)4agx);#V?M3Gy!!/q' );
define( 'SECURE_AUTH_SALT',  'p4E+y#_MKDKR|t5$Xv)8LgjxA!A9.V/3Z {Bg?io<Rm:M<U9$@&3W}gnH[Qw|,fU' );
define( 'LOGGED_IN_SALT',    ',(2:ihY^qsDdoTDCIp^CV9?H<~_%Zn$F.0jb<:[g&}_bp 2S1nVR,1F`Zj+o!_AG' );
define( 'NONCE_SALT',        'qhV<#=g@v(PrVw=THO* Caa$9/YI*7c6]RPusn9|A)%,Dr!PMapXi&wX)z;Xk`=^' );
define( 'WP_CACHE_KEY_SALT', ',{1#8^7g4?WP$@gXu;^5S$z~(T{?.CUEt5a/R,+KG0},Gc=6d [ggSZO/~3Wfl.X' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
