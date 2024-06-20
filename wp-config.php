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
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
//define( 'DB_NAME', 'wordpress' );
define( 'DB_NAME', 'woodwork' );
/** Database username */
//define( 'DB_USER', 'wordpress' );
define( 'DB_USER', 'woodwork' );
/** Database password */
//define( 'DB_PASSWORD', '2225843a11b5a9c5c4b74ed3b5d37a4befe2a1dc56bdd4fa' );
define( 'DB_PASSWORD', 'AVNS_c9p378rO0P9_2dnwWBa' );

/** Database hostname */
//define( 'DB_HOST', 'localhost' );
define( 'DB_HOST', 'dbaas-db-7971430-do-user-8921302-0.b.db.ondigitalocean.com:25060' );
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
define( 'AUTH_KEY',         '57N)<)](_L1S%!2~0~ZYJ4w9R6HQP2`GyAn#5y%$W3=}= xo?%rp5+N`[!cY*7J7' );
define( 'SECURE_AUTH_KEY',  'c!RCZD;;vC:Ep=E-hNGTrr])&3QBe4u>[NR10&D.B**w@MD.t^Bk)MwhCJv=P$7U' );
define( 'LOGGED_IN_KEY',    '?~YzU;(]i~TD=L3dtLI4bKD=lyCfT$NJ2Fl3B#!@vowf)ktv]<R{O_HhZ(s*:FD<' );
define( 'NONCE_KEY',        'xlw:v5ekPSid0efyQ{aqgx 5cy*9iLCzF{-lN[,OCK%Cx9gy@@;A6[{|Lw]r3&i.' );
define( 'AUTH_SALT',        'kR^]hBtK0`Z0k!/qG (Ceup`Q4^n/@}-MF5&v::GAO-.%/SVZ>~*u+=PYI,s66o*' );
define( 'SECURE_AUTH_SALT', 'Jo{6ioQU{uL05UAs.;$wxr7|2(0lEfG1I&]XhszC:Zz?vhKKq2tw&2v3lE0Q{o}8' );
define( 'LOGGED_IN_SALT',   '6PD!o4oXKFz.[?R]g)w4<8z|FJLs@@}I~Q_ uSW G-gxb$<O#BA8qhrB$. jh53o' );
define( 'NONCE_SALT',       ')agA7`-|7~zZR)(hq#@K>b8dx&`Z!m%ut#Sq3y/#%},n53>;(?,93-A<dcx) dE/' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', true );

/* Add any custom values between this line and the "stop editing" line. */

	
define( 'WP_DEBUG_LOG', true );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
