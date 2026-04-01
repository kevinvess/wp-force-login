<?php
/*
Plugin Name: Force Login
Plugin URI: https://wordpress.org/plugins/wp-force-login/
Description: Easily hide your WordPress site from public viewing by requiring visitors to log in first. Activate to turn on.
Version: 5.6.3
Author: Kevin Vess
Author URI: https://brightlightmedia.co/

Text Domain: wp-force-login
Domain Path: /languages

License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Define plugin constants
if ( ! defined( 'V_FORCELOGIN_OPTION' ) ) {
	define( 'V_FORCELOGIN_OPTION', 'v_forcelogin_allow_paths' );
	define( 'V_FORCELOGIN_SETTINGS_GROUP', 'v_forcelogin_settings_group' );
	define( 'V_FORCELOGIN_MENU_SLUG', 'v_forcelogin_settings' );
	define( 'V_FORCELOGIN_NONCE_ACTION', 'v_forcelogin_settings' );
	define( 'V_FORCELOGIN_FORM_FIELD', 'v_forcelogin_allow_paths_textarea' );
	define( 'V_FORCELOGIN_SUBMIT_BUTTON', 'v_forcelogin_submit' );
	define( 'V_FORCELOGIN_NONCE_FIELD', 'v_forcelogin_nonce' );
}

require_once plugin_dir_path( __FILE__ ) . 'lib/core.php';
require_once plugin_dir_path( __FILE__ ) . 'lib/admin.php';

// Register hooks and actions
add_action( 'template_redirect', 'v_forcelogin' );
add_filter( 'rest_authentication_errors', 'v_forcelogin_rest_access', 99 );
add_action( 'plugins_loaded', 'v_forcelogin_load_textdomain' );
add_action( 'admin_menu', 'v_forcelogin_add_admin_page' );
add_action( 'admin_init', 'v_forcelogin_register_settings' );


