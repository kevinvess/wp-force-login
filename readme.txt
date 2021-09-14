=== Force Login ===
Contributors: kevinvess
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=forcelogin%40vess%2eme&lc=US&item_name=Force%20Login%20for%20WordPress&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: privacy, private, protected, registered only, restricted, access, closed, force user login, hidden, login, password
Requires at least: 4.6
Tested up to: 5.8
Stable tag: 5.6.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

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
- Restrict REST API to authenticated users.
- Translation Ready & WPML certified.

**Bug Reports**

Bug reports for [Force Login are welcomed on GitHub](https://github.com/kevinvess/wp-force-login). Please note that GitHub is _not_ a support forum.


== Installation ==

Upload the Force Login plugin to your site, then Activate it.

1, 2: You're done!


== Frequently Asked Questions ==

= 1. How can I specify a redirect URL on login? =

By default, the plugin sends visitors back to the URL they tried to access. However, you can redirect users to a specific URL by adding the built-in WordPress filter [login_redirect](https://developer.wordpress.org/reference/hooks/login_redirect/) to your functions.php file.

= 2. How can I add exceptions for certain pages or posts? =

You can bypass Force Login based on any condition or specify an array of URLs to allow by adding the following filter to your functions.php file.

You may also use the WordPress [Conditional Tags](https://developer.wordpress.org/themes/references/list-of-conditional-tags/).

`
/**
 * Bypass Force Login to allow for exceptions.
 *
 * @param bool $bypass Whether to disable Force Login. Default false.
 * @param string $visited_url The visited URL.
 * @return bool
 */
function my_forcelogin_bypass( $bypass, $visited_url ) {

  // Allow all single posts
  if ( is_single() ) {
    $bypass = true;
  }

  // Allow these absolute URLs
  $allowed = array(
    home_url( '/mypage/' ),
    home_url( '/2015/03/post-title/' ),
  );
  if ( ! $bypass ) {
    $bypass = in_array( $visited_url, $allowed );
  }

  return $bypass;
}
add_filter( 'v_forcelogin_bypass', 'my_forcelogin_bypass', 10, 2 );
`

Checkout the [Force Login Wiki on GitHub](https://github.com/kevinvess/wp-force-login/wiki/Bypass-Dynamic-URLs) for additional examples of some different methods for allowing dynamic URLs.

= 3. How do I hide the "← Back to {sitename}" link? =

The WordPress login screen includes a "← Back to {sitename}" link below the login form; which may not actually take you back to the site while Force Login is activated. You can hide this link by adding the following action to your functions.php file.

**Requires:** WordPress 2.5 or higher

`
// Hide the 'Back to {sitename}' link on the login screen.
function my_forcelogin_hide_backtoblog() {
  echo '<style type="text/css">#backtoblog{display:none;}</style>';
}
add_action( 'login_enqueue_scripts', 'my_forcelogin_hide_backtoblog' );
`


== Changelog ==

= 5.6.3 =
* Fix - Fixed issue for sites with a custom login URL.

= 5.6.2 =
* Fix - Fixed issue for sites with a custom login URL.

= 5.6.1 =
* Fix - Fixed too many redirects issue for Multisite users.

= 5.6 =
* Feature - Added filter for Multisite unauthorized error message.
* Tweak - Allow logged-in Multisite users to access bypassed pages of other sites.

= 5.5 =
* Tweak - Deprecated whitelist filter, use v_forcelogin_bypass instead.

= 5.4 =
* Tweak - Improved the visited $url variable.
* Tweak - Changed code to comply with WordPress standards - props [Alex Bordei](https://github.com/kevinvess/wp-force-login/pull/43).

= 5.3 =
* Feature - Added nocache_headers() to prevent caching for the different browsers - props [Chris Harmoney](https://github.com/kevinvess/wp-force-login/pull/42).
* Tweak - Removed $url parameter from whitelist filter.

= 5.2 =
* Feature - Added $url parameter to bypass and whitelist filters.
* Tweak - Updated Multisite conditionals which determine user access to sites.
* Tweak - Moved 'v_forcelogin_redirect' filter to improve performance.

= 5.1.1 =
* Fix - Improved the REST API restriction to allow alternative modes of authentication.

= 5.1 =
* Tweak - Restrict access to the REST API for authorized users only - props [Andrew Duthie](https://github.com/kevinvess/wp-force-login/pull/34).
* Tweak - Added load_plugin_textdomain() to properly prepare for localization at translate.wordpress.org.

= 5.0 =
* Feature - Added filter to bypass Force Login redirect for allowing pages without specifying a URL.
* Tweak - Changed the hook for Force Login to run at a later stage in the WordPress tree.
* Fix - Replaced deprecated function - props [Just-Johnny](https://github.com/kevinvess/wp-force-login/issues/26).

= 4.2 =
* Tweak - Made plugin translation ready.

= 4.1 =
* Fix - Multisite 'Super Admin' users do not need assigned sites to access the network.

= 4.0 =
* Feature - Added exceptions for AJAX, Cron and WP-CLI requests.
* Fix - Only allow Multisite users access to their assigned sites.

= 3.3 =
* Fix - Check for existence of explicit port number before appending port - props [Björn Ali Göransson](https://github.com/kevinvess/wp-force-login/pull/10).

= 3.2 =
* Tweak - Removed v_getUrl() function to reduce possible duplicates of global functions - props [Joachim Happel](https://github.com/johappel).

= 3.1 =
* Fix - Rewrote v_getUrl() function to use HTTP_HOST instead of SERVER_NAME - props [Arlen22](https://github.com/kevinvess/wp-force-login/issues/7).

= 3.0 =
* Feature - Added filter for the redirect URL on login.
* Feature - Added filter to allow whitelisting of additional URLs.

= 2.1 =
* Fix - Rewrote v_getUrl function to include the server port - props [Nicolas](https://wordpress.org/support/topic/infinite-loop-when-server-port-is-not-standard/).

= 2.0 =
* Feature - Added redirect to send visitors back to the URL they tried to visit before logging in.

= 1.3 =
* Fix - Fixed password reset URL from being blocked - props [estebillan](https://wordpress.org/support/topic/password-reset-url-is-blocked/).

= 1.2 =
* Tweak - Streamlined code

= 1.1 =
* Fix - Allow access to the registration and the lost password page URLs - props [jabdo](http://profiles.wordpress.org/jabdo).


== Upgrade Notice ==

= 5.5 =
Deprecated whitelist filter, use v_forcelogin_bypass instead.

= 5.1 =
Restricts access to the REST API for authorized users only.

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
