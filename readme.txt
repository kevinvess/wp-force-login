=== Force Login ===
Contributors: kevinvess
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=forcelogin%40vess%2eme&lc=US&item_name=Force%20Login%20for%20WordPress&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: privacy, private, protected, registered only, restricted, access, closed, force user login, hidden, login, password
Requires at least: 2.7
Tested up to: 4.7
Stable tag: 5.0
License: GPLv2 or later

Force Login is a simple lightweight plugin that requires visitors to log in to interact with the website.


== Description ==

Easily hide your WordPress site from public viewing by requiring visitors to log in first. As simple as flipping a switch.

Make your website private until it's ready to share publicly, or keep it private for members only.

**Features**

- WordPress Multisite compatible.
- Login redirects visitors back to the url they tried to visit.
- Extensive Developer API (hooks & filters).
- Customizable. Set a specific URL to always redirect to on login.
- Filter exceptions for certain pages or posts.
- Translation Ready & WPML certified.

**Bug Reports**

Bug reports for [Force Login are welcomed on GitHub](https://github.com/kevinvess/wp-force-login). Please note that GitHub is _not_ a support forum.


== Installation ==

Upload the Force Login plugin to your site, then Activate it.

1, 2: You're done!


== Frequently Asked Questions ==

= 1. How can I specify a URL to redirect to on login? =

By default, the plugin sends visitors back to the URL they tried to visit. However, you can set a specific URL to always redirect users to by adding the following filter to your functions.php file.

The URL must be absolute (as in, <http://example.com/mypage/>). Recommended: [site_url( '/mypage/' )](https://codex.wordpress.org/Function_Reference/site_url).

`
/**
 * Set the URL to redirect to on login.
 *
 * @return string URL to redirect to on login. Must be absolute.
 */
function my_forcelogin_redirect() {
  return site_url( '/mypage/' );
}
add_filter('v_forcelogin_redirect', 'my_forcelogin_redirect', 10, 1);
`

= 2. How can I add exceptions for certain pages or posts? =

You can bypass Force Login based on any condition or specify an array of URLs to whitelist by adding either of the following filters to your functions.php file. You may also use the WordPress [Conditional Tags](http://codex.wordpress.org/Conditional_Tags).

**Bypass Force Login**

`
/**
 * Bypass Force Login to allow for exceptions.
 *
 * @return bool Whether to disable Force Login. Default false.
 */
function my_forcelogin_bypass( $bypass ) {
  if ( is_single() ) {
    $bypass = true;
  }
  return $bypass;
}
add_filter('v_forcelogin_bypass', 'my_forcelogin_bypass', 10, 1);
`

**Whitelist URLs**

Each URL must be absolute (as in, <http://example.com/mypage/>). Recommended: [site_url( '/mypage/' )](https://codex.wordpress.org/Function_Reference/site_url).

`
/**
 * Filter Force Login to allow exceptions for specific URLs.
 *
 * @return array An array of URLs. Must be absolute.
 */
function my_forcelogin_whitelist( $whitelist ) {
  $whitelist[] = site_url( '/mypage/' );
  $whitelist[] = site_url( '/2015/03/post-title/' );
  return $whitelist;
}
add_filter('v_forcelogin_whitelist', 'my_forcelogin_whitelist', 10, 1);
`

= 3. How can I add exceptions for dynamic URLs? =

Some URLs have unique query strings appended to the end of it, which is composed of a series of parameter-value pairs. 

For example:
<http://example.com/mypage/?parameter=value>

Checkout the [Force Login Wiki on GitHub](https://github.com/kevinvess/wp-force-login/wiki/Whitelist-Dynamic-URLs) for examples of the different methods for whitelisting dynamic URLs.

= 4. How do I get the WordPress mobile app to work? =

By default, the plugin blocks access to all page URLs; you may need to whitelist the XML-RPC page to allow the WordPress app to access your site for remote publishing.

`
/**
 * Filter Force Login to allow exceptions for specific URLs.
 *
 * @return array An array of URLs. Must be absolute.
 */
function my_forcelogin_whitelist( $whitelist ) {
  $whitelist[] = site_url( '/xmlrpc.php' );
  return $whitelist;
}
add_filter('v_forcelogin_whitelist', 'my_forcelogin_whitelist', 10, 1);
`

= 5. How do I hide the "← Back to {sitename}" link on the login screen? =

The WordPress login screen includes a "← Back to {sitename}" link below the login form; which may not actually take you back to the site while Force Login is activated. You can hide this link by adding the following action to your functions.php file.

**Requires:** WordPress 2.5 or higher

`
// Hide the 'Back to {sitename}' link on the login screen.
function my_forcelogin_hide_backtoblog() {
  echo '<style type="text/css">#backtoblog{display:none;}</style>';
}
add_action('login_enqueue_scripts', 'my_forcelogin_hide_backtoblog');
`


== Changelog ==

= 5.0 =
* Feature - Added filter to bypass Force Login redirect for whitelisting pages without specifying a URL.
* Tweak - Changed the hook for Force Login to run at a later stage in the WordPress tree.
* Fix - Replaced deprecated function - props [Just-Johnny](https://github.com/Just-Johnny).

= 4.2 =
* Tweak - Made plugin translation ready.

= 4.1 =
* Fix - Multisite 'Super Admin' users do not need assigned sites to access the network.

= 4.0 =
* Feature - Added exceptions for AJAX, Cron and WP-CLI requests.
* Fix - Only allow Multisite users access to their assigned sites.

= 3.3 =
* Fix - Check for existence of explicit port number before appending port - props [Björn Ali Göransson](https://github.com/bjorn-ali-goransson).

= 3.2 =
* Tweak - Removed v_getUrl() function to reduce possible duplicates of global functions - props [Joachim Happel](https://github.com/johappel).

= 3.1 =
* Fix - Rewrote v_getUrl() function to use HTTP_HOST instead of SERVER_NAME - props [Arlen22](https://github.com/Arlen22).

= 3.0 =
* Feature - Added filter to set a specific URL to redirect to on login.
* Feature - Added filter to allow whitelisting of additional URLs.

= 2.1 =
* Fix - Rewrote v_getUrl function to include the server port - props [Nicolas](http://profiles.wordpress.org/nottavi).

= 2.0 =
* Feature - Added redirect to send visitors back to the URL they tried to visit after logging in.

= 1.3 =
* Fix - Fixed password reset URL from being blocked - props [estebillan](http://profiles.wordpress.org/estebillan).

= 1.2 =
* Tweak - Streamlined code

= 1.1 =
* Fix - Whitelisted the registration page and the Lost Password page - props [jabdo](http://profiles.wordpress.org/jabdo).


== Upgrade Notice ==

= 5.0 =
New feature: added bypass filter. Tweak: changed hook for Force Login to run later.

= 4.1 =
Multisite users can only access their assigned sites, except 'Super Admin' users.

= 4.0 =
New feature: added exceptions for AJAX, Cron, and WP-CLI requests. Fix: Multisite users can only access their assigned sites.

= 3.2 =
Removed function v_getUrl().

= 3.0 =
New features: added filters for customizing the plugin.

= 2.0 =
New feature: added redirect to send visitors back to the URL they tried to visit after logging-in.

= 1.3 =
Fixes bug with password reset URL from being blocked.