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

require_once plugin_dir_path( __FILE__ ) . 'lib/core.php';

// Register hooks and actions
add_action( 'template_redirect', 'v_forcelogin' );
add_filter( 'rest_authentication_errors', 'v_forcelogin_rest_access', 99 );
add_action( 'plugins_loaded', 'v_forcelogin_load_textdomain' );
