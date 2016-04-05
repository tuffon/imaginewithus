<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link https://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'imagiok2_wp1');

/** MySQL database username */
define('DB_USER', 'imagiok2_admin');

/** MySQL database password */
define('DB_PASSWORD', 'Hustlehard123!');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('WP_HOME', 'wp.local/');

define('WP_ADMIN_DIR', 'wp-admin');
define( 'ADMIN_COOKIE_PATH', SITECOOKIEPATH . WP_ADMIN_DIR);
/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'ese1haghcd5dl2t7mbchd6l7s71fq1ipls4aniw1oxlevb3kyqiqfd6mcj9pziwm');
define('SECURE_AUTH_KEY',  'euczystdieinwsn2cjxqypu1alosq8sw5pibwix90dgatnjdbikm6pkvgrf5zwzi');
define('LOGGED_IN_KEY',    'etpbbtyal2kmxkfrl9fqp0fu8ni5ivqfae2xdatqm5ekezw7oaplvdwy7hwlivc4');
define('NONCE_KEY',        'ggyrkvzx6xxzoabhesfcbsnycbcgdt5nvwzfye5mtzehg6nhujhqgrilu10ry4j5');
define('AUTH_SALT',        'q4ej2tbxowscwcrckt2qiw3yzytmlwm4ddzxbusngdhuwr5qvp2rlapfsjqkbezf');
define('SECURE_AUTH_SALT', 'blr7avopgj8jbxoyt6ffyzshhmywav0w1ogekj1hzclokze4hn4cpkktrpvl8kqx');
define('LOGGED_IN_SALT',   'xqwp32mkzomfhjxxdjupqmwkll9gn9xr62hzblceokoa4bjproxdfz7jgjyoomir');
define('NONCE_SALT',       'mby3vyq5cjkt5sgha3q5myzykuovff1oevgv1x7wqvochcp5kafzwalwo0i5ifmh');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

define('WP_MEMORY_LIMIT', '64M');