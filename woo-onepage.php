<?php
/*
 * Plugin Name: Woo OnePage Checkout Shop
 * Version: 1.6.5
 * Plugin URI: https://amadercode.com/premium-products/woocommerce-one-page-checkout-shop/
 * Description: Create your onepage store where a customer can make his order by choosing products add to cart and checkout.
 * Author: AmaderCode Lab
 * Author URI: http://www.amadercode.com/
 * Requires at least: 4.0
 * Tested up to: 5.3
 * WC tested up to: 3.8
 * WC requires at least: 3.0
 * Text Domain: woocommerce-one-page-checkout-shop
 * Domain Path: /lang/
 * @package WordPress
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined( 'ACL_WOOSC_PLUGIN_FILE' ) ) {
    define( 'ACL_WOOSC_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'ACL_WOOSC_VERSION' ) ) {
    define('ACL_WOOSC_VERSION', '1.0.0');
}
if ( ! defined( 'ACL_WOOSC_REQUIRED_PHP_VERSION' ) ) {
    define('ACL_WOOSC_REQUIRED_PHP_VERSION', '5.4.0');
}
if ( ! defined( 'ACL_WOOSC_WP_VERSION' ) ) {
    define('ACL_WOOSC_WP_VERSION', '4.0');
}
if ( ! defined( 'ACL_WOOSC_ABSPATH' ) ) {
    define('ACL_WOOSC_ABSPATH', dirname(ACL_WOOSC_PLUGIN_FILE) . '/');
}
if ( ! defined( 'ACL_WOOSC_URL' ) ) {
    define('ACL_WOOSC_URL', plugin_dir_url(__FILE__));
}
if ( ! defined( 'ACL_WOOSC_PATH' ) ) {
    define('ACL_WOOSC_PATH', plugin_dir_path(__FILE__));
}
if ( ! defined( 'ACL_WOOSC_IMG_URL' ) ) {
    define('ACL_WOOSC_IMG_URL', plugin_dir_url(__FILE__) . 'assets/images/');
}
// Load plugin basic class files
include_once( 'includes/class-woosc-plugin.php');
include_once( 'includes/class-woosc-install.php');

/**
 * Returns the main instance of WOOSC_Plugin to prevent the need to use globals.
 *@since  1.0.0
 * @return object WOOSC_Plugin
 */
function acl_woo_onepage_template (){
	$instance = ACL_Woo_Onepage_Plugin::instance( __FILE__, ACL_WOOSC_VERSION );
	if ( is_null( $instance->settings ) ) {
		$instance->settings = ACL_Woo_Onepage_Settings::instance( $instance );
	}

	return $instance;
}

add_action('plugins_loaded', 'acl_woo_onepage_template');

