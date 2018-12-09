<?php
/*
Plugin Name: Force Login
Plugin URI: https://wordpress.org/plugins/wp-force-login/
Description: Easily hide your WordPress site from public viewing by requiring visitors to log in first. Activate to turn on.
Version: 5.3
Author: Kevin Vess
Author URI: http://vess.me/

Text Domain: wp-force-login
Domain Path: /languages

License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

function v_forcelogin() {

	// Exceptions for AJAX, Cron, or WP-CLI requests
	if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		return;
	}

	// Redirect unauthorized visitors
	if ( ! is_user_logged_in() ) {
		// Get visited URL
		$url  = isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http';
		$url .= '://' . $_SERVER['HTTP_HOST'];
		// port is prepopulated here sometimes
		if ( strpos( $_SERVER['HTTP_HOST'], ':' ) === FALSE ) {
			$url .= in_array( $_SERVER['SERVER_PORT'], array('80', '443') ) ? '' : ':' . $_SERVER['SERVER_PORT'];
		}
		$url .= $_SERVER['REQUEST_URI'];

		/**
		 * Bypass filters.
		 *
		 * @since 3.0.0 The `$whitelist` filter was added.
		 * @since 4.0.0 The `$bypass` filter was added.
		 * @since 5.2.0 The `$url` parameter was added.
		 */
		$bypass = apply_filters( 'v_forcelogin_bypass', false, $url );
		$whitelist = apply_filters( 'v_forcelogin_whitelist', array() );

		if ( preg_replace( '/\?.*/', '', $url ) !== preg_replace( '/\?.*/', '', wp_login_url() ) && ! $bypass && ! in_array( $url, $whitelist ) ) {
			// Determine redirect URL
			$redirect_url = apply_filters( 'v_forcelogin_redirect', $url );
			// Set the headers to prevent caching
			nocache_headers();
			// Redirect
			wp_safe_redirect( wp_login_url( $redirect_url ), 302 ); exit;
		}
	}
	elseif ( function_exists('is_multisite') && is_multisite() ) {
		// Only allow Multisite users access to their assigned sites
		if ( ! is_user_member_of_blog() && ! current_user_can('setup_network') ) {
			wp_die( __( "You're not authorized to access this site.", 'wp-force-login' ), get_option('blogname') . ' &rsaquo; ' . __( "Error", 'wp-force-login' ) );
		}
	}
}
add_action( 'template_redirect', 'v_forcelogin' );

/**
 * Restrict REST API for authorized users only
 *
 * @since 5.1.0
 * @param WP_Error|null|bool $result WP_Error if authentication error, null if authentication
 *                              method wasn't used, true if authentication succeeded.
 */
function v_forcelogin_rest_access( $result ) {
	if ( null === $result && ! is_user_logged_in() ) {
		return new WP_Error( 'rest_unauthorized', __( "Only authenticated users can access the REST API.", 'wp-force-login' ), array( 'status' => rest_authorization_required_code() ) );
	}
	return $result;
}
add_filter( 'rest_authentication_errors', 'v_forcelogin_rest_access', 99 );

/*
 * Localization
 */
function v_forcelogin_load_textdomain() {
	load_plugin_textdomain( 'wp-force-login', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'v_forcelogin_load_textdomain' );
