<?php
/*
Plugin Name: Force Login
Plugin URI: http://vess.me/
Description: Easily hide your WordPress site from public viewing by requiring visitors to log in first. Activate to turn on.
Version: 2.0
Author: Kevin Vess
Author URI: http://vess.me/
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

function v_getUrl() {
  $url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] : 'https://'.$_SERVER["SERVER_NAME"];

  // Redirect to root if no REQUEST_URI set:
  if ( empty($_SERVER['REQUEST_URI']) ) {
    $url .= '/';
  } else {
    $url .= $_SERVER['REQUEST_URI'];
  }

  return $url;
}
function v_forcelogin() {
  $url = v_getUrl();
  if( !is_user_logged_in() && preg_replace('/\?.*/', '', $url) != preg_replace('/\?.*/', '', wp_login_url()) ) {
    wp_safe_redirect( wp_login_url( $url ), 302 ); exit();
  }
}
add_action('init', 'v_forcelogin');