<?php
/**
 * Plugin Name: YITH WooCommerce Name Your Price
 * Plugin URI:  http://yithemes.com/themes/plugins/yith-woocommerce-name-your-price/
 * Description: YITH WooCommerce Name Your Price allow your users to choose how much they want to pay.
 * Version: 1.0.4
 * Author: YIThemes
 * Author URI: http://yithemes.com/
 * Text Domain: yith-woocommerce-name-your-price
 * Domain Path: /languages/
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Name Your Price
 * @version 1.0.4
 */

/*
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
*/
if( !defined( 'ABSPATH' ) ){
    exit;
}
if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}


    function yith_wc_name_your_price_install_woocommerce_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e( 'YITH WooCommerce Name Your Price is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-name-your-price' ); ?></p>
        </div>
    <?php
    }

    function yith_wc_name_your_price_install_free_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e( 'You can\'t activate the free version of YITH WooCommerce Name Your Price while you are using the premium one.', 'yith-woocommerce-name-your-price' ); ?></p>
        </div>
    <?php
    }

if ( !function_exists( 'yith_plugin_registration_hook' ) ) {
    require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );


if ( !defined( 'YWCNP_VERSION' ) ) {
    define( 'YWCNP_VERSION', '1.0.4' );
}

if ( !defined( 'YWCNP_FREE_INIT' ) ) {
    define( 'YWCNP_FREE_INIT', plugin_basename( __FILE__ ) );
}

if ( !defined( 'YWCNP_FILE' ) ) {
    define( 'YWCNP_FILE', __FILE__ );
}

if ( !defined( 'YWCNP_DIR' ) ) {
    define( 'YWCNP_DIR', plugin_dir_path( __FILE__ ) );
}

if ( !defined( 'YWCNP_URL' ) ) {
    define( 'YWCNP_URL', plugins_url( '/', __FILE__ ) );
}

if ( !defined( 'YWCNP_ASSETS_URL' ) ) {
    define( 'YWCNP_ASSETS_URL', YWCNP_URL . 'assets/' );
}

if ( !defined( 'YWCNP_ASSETS_PATH' ) ) {
    define( 'YWCNP_ASSETS_PATH', YWCNP_DIR . 'assets/' );
}

if ( !defined( 'YWCNP_TEMPLATE_PATH' ) ) {
    define( 'YWCNP_TEMPLATE_PATH', YWCNP_DIR . 'templates/' );
}

if ( !defined( 'YWCNP_INC' ) ) {
    define( 'YWCNP_INC', YWCNP_DIR . 'includes/' );
}
if( !defined('YWCNP_SLUG' ) ){
    define( 'YWCNP_SLUG', 'yith-woocommerce-name-your-price' );
}

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YWCNP_DIR . 'plugin-fw/init.php' ) ) {
    require_once( YWCNP_DIR . 'plugin-fw/init.php' );
}

yit_maybe_plugin_fw_loader(YWCNP_DIR);

if ( ! function_exists( 'yith_name_your_price_init' ) ) {
    /**
     * Unique access to instance of YITH_Name_Your_Price class
     *
     * @return YITH_WooCommerce_Name_Your_Price
     * @since 1.0.0
     */
    function yith_name_your_price_init() {

        load_plugin_textdomain( 'yith-woocommerce-name-your-price', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

        // Load required classes and functions

        require_once( YWCNP_INC.'functions.yith-name-your-price.php' );
        require_once( YWCNP_INC.'classes/class.yith-wc-name-your-price-admin.php' );
        require_once( YWCNP_INC.'classes/class.yith-wc-name-your-price-frontend.php' );
        require_once( YWCNP_INC.'classes/class.yith-wc-name-your-price.php' );

        global $YWC_Name_Your_Price;
        $YWC_Name_Your_Price = YITH_WooCommerce_Name_Your_Price::get_instance();

    }
}

add_action( 'ywcnp_init', 'yith_name_your_price_init' );

if( !function_exists( 'yith_name_your_price_install' ) ){

    function yith_name_your_price_install(){

        if( !function_exists( 'WC' ) ){
            add_action( 'admin_notices', 'yith_wc_name_your_price_install_woocommerce_admin_notice' );
        }elseif( defined( 'YWCNP_PREMIUM' ) ){
            add_action( 'admin_notices', 'yith_wc_name_your_price_install_free_admin_notice' );
            deactivate_plugins( plugin_basename( __FILE__ ) );
        }else
            do_action( 'ywcnp_init' );
    }
}

add_action( 'plugins_loaded', 'yith_name_your_price_install' ,11 );