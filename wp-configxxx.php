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
define('DB_NAME', 'wp_wp1');

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

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'mO<=YJA7vj?Zk-^tVHUN@QT<.nhlK3:q7(q,dTGE2maKRA5|2Q7rDx89Gw#8ZW2Q');
define('SECURE_AUTH_KEY',  '2)HI}C$w BBU2w2fdLq`Z-9fAzQQ3%G<J/NyRv[|4U3tWTdf3tN?xQU%?UuF`jdZ');
define('LOGGED_IN_KEY',    '%TV,CBqxOiez!-e#eBrk{qssNe=P/-AKUX|sS7F$#>3,*o^E[OVtqU`#)Io`N,-(');
define('NONCE_KEY',        'q[%T= HfuXE7pYMZR,=fCLJ-)kAVQ$% >(8^nsSc8K %t-i>|Md_pMh1x8XZ@0k6');
define('AUTH_SALT',        'I_qI+ub#IdA+S.D{yFWE9R:*DSK+U|6E_aXAIg|]K[Ey1>VKBbUmPWC/,_A_ujU_');
define('SECURE_AUTH_SALT', '.ddz(RC%+|CsfMO_T&E0LV]f9m%.e%tX,(RV|&xzD;PSF}b yGhl*lQDhZ5))TtX');
define('LOGGED_IN_SALT',   'j^J;[z6>[t!x^jYDitZsx0EbWf~A6<e/L-aADuVAJk.x((Pu-rGLU4),tH9`!<yo');
define('NONCE_SALT',       'y}yxh-7`/QjKNWm^q)a-;,`|8!gAl?Cj[0%uME=6in`z2;O;>NANd/&7mZMJ0>)8');

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
