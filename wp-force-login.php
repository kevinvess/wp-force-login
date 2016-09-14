<?php
/*
Plugin Name: Force Login
Plugin URI: http://vess.me/
Description: Easily hide your WordPress site from public viewing by requiring visitors to log in first. Activate to turn on.
Version: 5.0
Author: Kevin Vess
Author URI: http://vess.me/

Text Domain: wp-force-login
Domain Path: /languages

License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

function v_forcelogin() {

  // Exceptions for AJAX, Cron, or WP-CLI requests
  if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
    return;
  }

  // Redirect unauthorized visitors
  if ( !is_user_logged_in() ) {
    // Get URL
    $url  = isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http';
    $url .= '://' . $_SERVER['HTTP_HOST'];
    // port is prepopulated here sometimes
    if ( strpos( $_SERVER['HTTP_HOST'], ':' ) === FALSE ) {
      $url .= in_array( $_SERVER['SERVER_PORT'], array('80', '443') ) ? '' : ':' . $_SERVER['SERVER_PORT'];
    }
    $url .= $_SERVER['REQUEST_URI'];

    // Apply filters
    $bypass = apply_filters ( 'v_forcelogin_bypass', false );
    $whitelist = apply_filters( 'v_forcelogin_whitelist', array() );
    $redirect_url = apply_filters( 'v_forcelogin_redirect', $url );

    // Redirect visitors
    if ( preg_replace('/\?.*/', '', $url) != preg_replace('/\?.*/', '', wp_login_url()) && !in_array($url, $whitelist) && !$bypass ) {
      wp_safe_redirect( wp_login_url( $redirect_url ), 302 ); exit();
    }
  }
  else {
    // Only allow Multisite users access to their assigned sites
    if ( function_exists('is_multisite') && is_multisite() ) {
      $current_user = wp_get_current_user();
      if ( !is_user_member_of_blog( $current_user->ID ) && !is_super_admin() )
        wp_die( __( "You're not authorized to access this site.", 'wp-force-login' ), get_option('blogname') . ' &rsaquo; ' . __( "Error", 'wp-force-login' ) );
    }
  }
}
add_action('template_redirect', 'v_forcelogin');