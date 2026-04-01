<?php

/**
 * Core Force Login Logic
 *
 * Main functionality for forcing users to log in before accessing the site.
 */

/**
 * Force user to log in before accessing the site
 *
 * Checks various conditions (AJAX, Cron, CLI, login page) and applies filters
 * for path-based whitelisting and custom bypass logic.
 *
 * @since 1.0.0
 * @hook template_redirect
 */
function v_forcelogin() {

	// Exceptions for AJAX, Cron, or WP-CLI requests
	if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		return;
	}

	// Bail if the current visitor is a logged in user, unless Multisite is enabled
	if ( is_user_logged_in() && ! is_multisite() ) {
		return;
	}

	// Get visited URL
	$schema = is_ssl() ? 'https://' : 'http://';
	$host = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

	if ( ! $host || ! $request_uri ) {
		return;
	}

	$url = $schema . $host . $request_uri;

	// Bail if visiting the login URL. Fix for custom login URLs
	$login_url = wp_login_url();
	$login_url_path = preg_replace( '/\?.*/', '', $login_url );
	$current_url_path = preg_replace( '/\?.*/', '', $url );
	if ( $login_url_path === $current_url_path ) {
		return;
	}

	$parsed = parse_url( $url );
	if ( false === $parsed ) {
		return;
	}

	$path = isset( $parsed['path'] ) ? trim( $parsed['path'], '/' ) : '';

	// Include query string if present
	if ( ! empty( $parsed['query'] ) ) {
		$path = $path ? $path . '?' . $parsed['query'] : '?' . $parsed['query'];
	}

	$path_rules = apply_filters( 'v_forcelogin_path_whitelist', array(), $path, $url );
	$path_rules = array_merge( $path_rules, (array) get_option( V_FORCELOGIN_OPTION, array() ) );

	// Remove duplicates and empty values
	$path_rules = array_filter( array_unique( array_map( 'trim', $path_rules ) ) );

	foreach ( $path_rules as $rule ) {
		if ( v_forcelogin_path_matches_rule( $path, $rule ) ) {
			return;
		}
	}

	/**
	 * Whitelist filter. (DEPRECATED)
	 *
	 * @since 3.0.0
	 * @deprecated 5.5.0 Use {@see 'v_forcelogin_bypass'} instead.
	 *
	 * @param array An array of absolute URLs.
	 */
	$allowed = apply_filters_deprecated( 'v_forcelogin_whitelist', array( array() ), '5.5.0', 'v_forcelogin_bypass' );

	/**
	 * Bypass filter.
	 *
	 * @since 5.0.0
	 * @since 5.2.0 Added the `$url` parameter.
	 *
	 * @param bool Whether to disable Force Login. Default false.
	 * @param string $url The visited URL.
	 */
	$bypass = apply_filters( 'v_forcelogin_bypass', in_array( $url, $allowed ), $url );

	// Bail if bypass is enabled
	if ( $bypass ) {
		return;
	}

	// Only allow Multisite users access to their assigned sites
	if ( is_multisite() && is_user_logged_in() ) {
		if ( ! is_user_member_of_blog() && ! current_user_can( 'setup_network' ) ) {
			$message = apply_filters( 'v_forcelogin_multisite_message', __( "You're not authorized to access this site.", 'wp-force-login' ), $url );
			wp_die( esc_html( $message ), esc_html( get_option( 'blogname' ) . ' › ' . __( 'Error', 'wp-force-login' ) ) );
		}
		return;
	}

	// Determine redirect URL
	$redirect_url = apply_filters( 'v_forcelogin_redirect', $url );

	// Set the headers to prevent caching
	nocache_headers();

	// Redirect unauthorized visitors
	wp_safe_redirect( wp_login_url( $redirect_url ), 302 );
	exit;
}

/**
 * Restrict REST API for authorized users only
 *
 * @since 5.1.0
 * @param WP_Error|null|bool $result WP_Error if authentication error, null if authentication
 *                              method wasn't used, true if authentication succeeded.
 *
 * @return WP_Error|null|bool
 */
function v_forcelogin_rest_access( $result ) {
	if ( null === $result && ! is_user_logged_in() ) {
		return new WP_Error( 'rest_unauthorized', __( 'Only authenticated users can access the REST API.', 'wp-force-login' ), array( 'status' => rest_authorization_required_code() ) );
	}
	return $result;
}

/*
 * Localization
 */
function v_forcelogin_load_textdomain() {
	load_plugin_textdomain( 'wp-force-login', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

/**
 * Check if a path matches a wildcard rule
 *
 * Supports wildcard patterns:
 * - `*` matches exactly one path segment
 * - `**` matches multiple path segments
 *
 * Properly escapes regex special characters to prevent ReDoS attacks.
 *
 * @since 5.6.4
 * @param string $path The path to test
 * @param string $rule The wildcard rule pattern
 * @return bool True if path matches rule
 */
function v_forcelogin_path_matches_rule( $path, $rule ) {
	$rule  = trim( $rule, '/' );

	// Validate rule content (prevent malicious regex patterns)
	if ( empty( $rule ) ) {
		return false;
	}

	$regex = preg_quote( $rule, '#' );
	// Use non-greedy matching (.+?) to prevent ReDoS and limit backtracking
	$regex = str_replace( '\*\*', '(.+?)', $regex );
	$regex = str_replace( '\*', '[^/]+', $regex );
	$regex = '#^' . $regex . '$#';

	return (bool) preg_match( $regex, $path );
}
