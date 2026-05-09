<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
//define('DB_NAME', 'wordpress');

/** Database username */
//define('DB_USER', 'wordpress');

/** Database password */
//define('DB_PASSWORD', 'wordpress');

/** Database hostname */
//define('DB_HOST', 'localhost');

/** Database charset to use in creating database tables. */
//define('DB_CHARSET', 'utf8mb4');

/** The database collate type. Don't change this if in doubt. */
//define('DB_COLLATE', '');

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
define('AUTH_KEY', '!3?#BW8#8p*@KC]NZ/^9dYRFyzy&7 Angs3?i<mnd-mS5MV8eyoyuZM.JI:XPuy*');
define('SECURE_AUTH_KEY', 'H<jYc5CcmtTy e7v|wK-?B%:Uv,f^:Z]AdY>-^2LCs}{|7@GAtH)B+;?Yjrm|,(A');
define('LOGGED_IN_KEY', 'nt]ijHk*;nl>$L0eZF-&<)$Fr6z|RJjfvj%Gv?096xs_^I[<EZ6X?c6).=1C@r4l');
define('NONCE_KEY', 'bmnz7v9;,O/c~&W]&5-bgSpY1RLxd*[oMgTy/Y]7Qi%^@ZY|>[dKhiP$0J1jQeT[');
define('AUTH_SALT', 'J{z0X#4q,K~`]H0QMfp|VAxi(Ej+Yvpgx!AnRwcjt5,lY3S)sN5F+tUV*NK<;|]~');
define('SECURE_AUTH_SALT', '*6:?#n=a$s5o`ehAe;+&+U1+l @88V)w1sGi|NQ!29qnio+N n!km.(JRiPceWgl');
define('LOGGED_IN_SALT', '?TSVgIErfPE*(AfvlR`go8r+93W2>s<`s-|+B0U$;icaBTdU&>@Ff*{AkUEdU&De');
define('NONCE_SALT', '8lA|mciSqQ|(NplPY6 !<jHFa3j^JNr:9~/9]O40`*HrwOCY>kfx1!2jBQtMJ36u');
/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define('WP_DEBUG', false);

/* Add any custom values between this line and the "stop editing" line. */


/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

// Include for ddev-managed settings in wp-config-ddev.php.
$ddev_settings = dirname(__FILE__) . '/wp-config-ddev.php';
if (is_readable($ddev_settings) && !defined('DB_USER')) {
    require_once($ddev_settings);
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
