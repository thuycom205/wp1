<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');
define('FS_METHOD', 'direct');
/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'f`c[BZz8p,eB/c#yHt,DY5k-8_Ee4z#nQwwk BUr{QJJIj_hz:GxOU)%zXN6,F?-');
define('SECURE_AUTH_KEY',  'Vt2?VE{Jw{d.]s?]ge5x o:C%:pHWP@O$iYQ$0@#9b.YMm;L+1(<dTq:LQgB@uwW');
define('LOGGED_IN_KEY',    'U|!m-ap-hmC=X&B3dCLP[tJJ1T1RSi_d_^caY.CPj(fZ}7ROSud;+:Bl3V%d[.92');
define('NONCE_KEY',        'JnS3We<?#7Ic50~n<h~#6gy+Ac-,.W8mQ9o2+1+SF-sBnEyjA8$LTi7`99JD B#R');
define('AUTH_SALT',        'HnOUv#TSJ~wfE<S}79xQFK(f6pvz5t4je={QsF0,s+)Lx`f|igo6]@_WeLdgrFjg');
define('SECURE_AUTH_SALT', '%${o-dianL@b/ L<E=+{-/QM=5O&omE$Y]Tjbe8bxbRd-ww4<{7W cW3hON@G{>u');
define('LOGGED_IN_SALT',   'RJ!xw=q28xSByFXY&?o7&j-0q;7-x#pQ;B5;U{R](LE{v2](d#ED6,FM066-`_o*');
define('NONCE_SALT',       ':S0,`HQqW@;zeW tl)!s*f${NT+iJQ,&s,3JfL=^1R[7M{c9A{G+P^Ftk!QbL7BR');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
