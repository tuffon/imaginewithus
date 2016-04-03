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
define('DB_NAME', 'imagiok2_wordpresse3d');

/** MySQL database username */
define('DB_USER', 'imagiok2_worde3d');

/** MySQL database password */
define('DB_PASSWORD', 'oWtVgGLjgXp4');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY', 'EfWqnNbf-_[l!F[Ymds>F!+xBEx+map<@yBUeqFl<Iee_YdOwypc}Mx[qqOMpj?[&&RVf!rYu=VKGuBQ!uOUf%f|b^Aos|SkFR^wu^AALmR[fTemrIIyT}[Z?SQ[c]lT');
define('SECURE_AUTH_KEY', 'L+)eWy>Iu$Swc$dpeqQooD/eZlX^%/lXD[iK}!=+Z@)S}wU[oteeseAKQHlL}qRn*mUck_yEV@SKV_wUTjQJ?bp=porM_MB?Ub=rEG$l?caiR=]JGT|&XPNw^IG&gp)&');
define('LOGGED_IN_KEY', 'Q%iSuNhBsS*UOtn!w;EOm>s$?CIiKwLZrUfiA$UhbuvbR|}pvJGCzRRZPfee^T[mUXMaGWSn-E!YG/s|Ri<NtJRROEYl-q|}p;sDbfxiw)od=qP/AGM%|S}/{O}tblfC');
define('NONCE_KEY', 'y+]!|ctCR|qMGww}mG+gJxHAvoqRz+sZ?Fq(^al{Y_SxHmtMGm!{)Gvd[CW*bYRDJWgHGN>%z_O&-InSUwQ$d=][G[Kos@u)wqLD;W<^;sz(WFkQq<*DslB%SdmxxBao');
define('AUTH_SALT', 'HXN<{pRm>du?Qv]lI=gfxspJg>]q?U-WN(xHK)HnWWHnjrvxv}GEnp*^ovS(S]YfKWHUpxqW$yrkOMQgKCT(yUaTf;g;sGI$^<iYs;XJt$@U$C{kIZglhT|phwFbb[<G');
define('SECURE_AUTH_SALT', 'n}/m%KeJsx(>];KN$LG%Sc/Js{x[h!PfCM(jWn@hGE(xew/%EJQ_V^z_$a&k!xXExu-hZPWdD>tDVkXW}TjJLr>bV/AlD(%lJzp>Nl)CHFGJdYW}MYhWkoNyYy$<CPMm');
define('LOGGED_IN_SALT', '+cWKX)lj*/dg=Q|cB{E@*r[bdX/$uKf=WyN!QS-Iuw-WKe>u/zbu&FI=AEkSQtU(Oe|oWhXTbWJ!{g_lfUlQerUePCY!L%p?rINV|^Xb$?bFkjY=n&J+N%TA^y[Kra@(');
define('NONCE_SALT', 'uu@FtJ%BXvD%^*+TN<Ts>c*)Kn}qkLLCd*+i]{N<u{TbUE/ldm_UTztuF&XfP;(-erfTSBvYwyKk-A]mo]Equcj=fg/zJX=dHF{%/kgzurxpqD/w+?*rwV<cwOdb]zO(');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_hmxg_';

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

/**
 * Include tweaks requested by hosting providers.  You can safely
 * remove either the file or comment out the lines below to get
 * to a vanilla state.
 */
if (file_exists(ABSPATH . 'hosting_provider_filters.php')) {
	include('hosting_provider_filters.php');
}
