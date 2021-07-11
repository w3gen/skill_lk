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
 * @link https://skill.lk/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'lankaho4_wp986' );

/** MySQL database username */
define( 'DB_USER', 'lankaho4_wp986' );

/** MySQL database password */
define( 'DB_PASSWORD', 'p4W1SLG7' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.skill.lk/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '5VF<Z9fZC!(~-7, bo~RDRc 7F7[s--=$SlG/SEyK{Zz=Mj3q|f#z<~:<4@k:#qO' );
define( 'SECURE_AUTH_KEY',  'WMoA)b&<LBpvW%,|uD%)9tL1RRMo]sr!uUl3=BWNM;+LOZ`#; hlXi{V]R`[y{IM' );
define( 'LOGGED_IN_KEY',    'Ci{B(IToU,|0}@WGb1jQNu^LVWa%n. N9S0=*Ef2XveY.rwwTW5/PZ)Zvi!K#C=P' );
define( 'NONCE_KEY',        'c(fwKZ#md1*L3rbo/Y6xJrNEtc.B$549GGI+XjhrZNsTi^&Zo-8+zKa%r1]pX/NS' );
define( 'AUTH_SALT',        '*o)Y8)dBz!(apA(HsW%+7x2 n ,,rE0M<*~Y{:sWoi4yd0Z=Z:z%<wd,N=<t:sDO' );
define( 'SECURE_AUTH_SALT', 'qb9sVFu/UU=T18C/.;=8@2=(j[| EsJyl_gyl]?DhJ]v^PAv!:G;8wsB8e.0~0ZH' );
define( 'LOGGED_IN_SALT',   '5Z<]7r2YZ|L?z8syRLd>$p;[2wU<d?t_SBO>U6{U}F^LpyBIdu+Lu<bA}F?ilQcW' );
define( 'NONCE_SALT',       ':s_8*,<GX+F&3D_6}PdcJG#U2}cMaTR/mO;<pDf8^UbT:),Cc{61J_1D5nbp^54)' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'skill_lk_';

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
 * @link https://skill.lk/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
