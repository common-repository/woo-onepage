<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// register activation hook
register_activation_hook( ACL_WOOSC_PLUGIN_FILE, 'acl_woosc_activation' );
function acl_woosc_activation() {
        global $wp_version;
        if ( version_compare( PHP_VERSION, ACL_WOOSC_REQUIRED_PHP_VERSION, '<' ) ) {
            wp_die('Minimum PHP Version required: ' . ACL_WOOSC_REQUIRED_PHP_VERSION );
        }
        if ( version_compare( $wp_version, ACL_WOOSC_WP_VERSION, '<' ) ) {
            wp_die('Minimum Wordpress Version required: ' . ACL_WOOSC_WP_VERSION );
        }
        if(!class_exists('WooCommerce')){
            wp_die('WooCommerce is required ');
        }
        // General Initial Options
        if(!get_option('acl_woosc_product_per_page')) {
            update_option('acl_woosc_product_per_page','10');
        }
        if(!get_option('acl_woosc_show_search_form')) {
            update_option('acl_woosc_show_search_form','on');
        }
        if(!get_option('acl_woosc_show_loarmore')) {
            update_option('acl_woosc_show_loarmore','on');
        }

        //Templates
        if(!get_option('acl_woosc_templates')) {
            update_option('acl_woosc_templates','1');
        }
        // Style Initial Options
        if(!get_option('acl_woosc_custom_css')) {
            update_option('acl_woosc_custom_css','');
        }
        flush_rewrite_rules( true );
    }
?>