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
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */
define( 'QM_ENABLE_CAPS_PANEL', true );

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'okakimov_wp475' );

/** Database username */
define( 'DB_USER', 'okakimov_wp475' );

/** Database password */
define( 'DB_PASSWORD', '-27Uq8Spj@' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'pcsqy0u57skhcx0bzr04mkaqeipzf0ttydh22hbhesdvgtg5tewlwepdyidj7ccx' );
define( 'SECURE_AUTH_KEY',  '6xbln0vdvudsb2dgoxdw4y98ayhdaheitbnv14jd8cgawznxhusevokwr72aaztv' );
define( 'LOGGED_IN_KEY',    'amtld0gah16gr5vtf3y0dc7iwp7lwq6dqrnoawrkrlnilwdbc57ncjiwbw3nbv0b' );
define( 'NONCE_KEY',        'mbrb6tgazcilbzulkfmzf9obozz9hlq3nsjnhzlpb8kxuv0qubhy0foivuilt7w4' );
define( 'AUTH_SALT',        'lqnxp88uxsxwrzi9jjjolkc4keabhzkufpvqmgnrsxl70pkbwivfpgamzxsgdoui' );
define( 'SECURE_AUTH_SALT', 'ywxqhldniirxaaxsite43qfzk3swlc1tctmdky8cfndxttnhuek1yh5ihv4qzymc' );
define( 'LOGGED_IN_SALT',   'jlbatf0dkgh5euxblbbyw2ag7yahmohe7mnhusohmsz8u4titkqzy1zamiylihvj' );
define( 'NONCE_SALT',       'qdta8sznrqffzbtljg3zrjchpceuopvmsdcuumbjbxadzmtqmcac1oqw3cdxsf7b' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpp4_';

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
define( 'WP_DEBUG', true );  
define( 'WP_DEBUG_LOG', true ); 
define( 'WP_DEBUG_DISPLAY', true ); 

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
