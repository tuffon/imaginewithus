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
define('DB_NAME', 'imagiok2_wordpress743');

/** MySQL database username */
define('DB_USER', 'imagiok2_word743');

/** MySQL database password */
define('DB_PASSWORD', 'eyX57vUCvke7');

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
define('AUTH_KEY', 'pk_RJb(^VV$*%]JY*TfZ;pG}N%dzwISRRsEUamTPVA)N^nAvxzD$Lx+_(cv&/}TH&&XYaTlOn$(gOVMXm!sesn?D{stCgkwjAz/>]j!Bq{F||=_z]ms{rRv(}RJN?HLL');
define('SECURE_AUTH_KEY', 'RC;-fJO$}l_^fZR>T/ko+umuE}SE_sv]G{ZKGnIXw]dQtH?F&enxZLVrsNBv_CK?f[f_DTR!svdUa]/VMWlV%PfATob]UOhA!{v-leoR[EWx$A/lf([@vYbwpCg[g)?c');
define('LOGGED_IN_KEY', 'Z[VUHuAGc*fdPx_}{K[{tSTshLHhOjhCWDyKX+nc;q!bi&^VhP_&MLBqalz&zE(<_hOvV)YMIiSI^me}}ZMZ@s<)C;YXT<VQsKb{]s{LJ%qhOZ}CK}e{z]fUR}tvDl{q');
define('NONCE_KEY', '[)!HhUR^Kn-VTiXaZ+mhOBUS[xvnG!XVaZs}mMNi[Sp(WPjkS=z]h/MISL$a$GH$IEb^HnMsoHR_aIIJLinD{[_dEOlPqB-aOu&sD/!N]ZaPrTB_rCQ_hCAHOr;HrcS]');
define('AUTH_SALT', 'uHwRSITNo%t/EAgZvzer?|wt]Pf$e/$|-pL}F-H{(-qd]=K;SX;;cz^a!s+%|+={Ka]LL)E;_;Ao$UKO>VqfqaP_io*M=!hDm|OX&Yxi/xKu|$)bXK-|IMU/KsFNH/V_');
define('SECURE_AUTH_SALT', 'LMt-]HozC}AoLwONG!z[gTNgUlZ&ApgfWtM<}%K?wL{]tj]kJ!iudK|PvrxNIH@Uq&k|>bJT=(V}yIJFp)fKDTSVj(?mVF?*d$NKXh)HpxQD=wlomMM;R/LU$AtK}&rS');
define('LOGGED_IN_SALT', 'LCr^^bMca$WpODGTqJe|Ci]v)@jh|d])g<NHde>Z]^YxT$oqaH{!acfhaNWAF<nRFPY*Wy-exnnCgsP|ej|TKh_{)[ctD*BQKI|bfp<<gQ-V(LN_KQzB;l>dsxiy*lBW');
define('NONCE_SALT', 'v{Npqm%k*!=Y^l|r(Rf>N^@s>_zNNMRivFhTA&hq{WgWyWF{cX^(jKc+ShI@!dU/ghhmfgPWL=]rr)>up?>$f$qM!Ob_lj[MY-UMLhZ_cz$nuGWIojql|k<&[V<p!Teh');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_vfsf_';

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
define('WP_MEMORY_LIMIT', '64M');