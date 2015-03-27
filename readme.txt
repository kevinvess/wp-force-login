=== Force Login ===
Contributors: kevinvess
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=kevin%40vess%2eme&lc=US&item_name=Kevin%20Vess%20-%20WordPress&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: access, closed, force user login, hidden, login, password, privacy, private, protected, registered only, restricted
Requires at least: 2.7
Tested up to: 4.1
Stable tag: 3.0
License: GPLv2 or later

Force Login is a simple lightweight plugin that requires visitors to log in to interact with the website.


== Description ==

Easily hide your WordPress site from public viewing by requiring visitors to log in first. As simple as flipping a switch.

Make your website private until it's ready to share publicly, or keep it private for members only.

**Features**

- WordPress Multisite compatible.
- Login redirects visitors back to the url they tried to visit.
- Extensive Developer API (hooks & filters).
- Customizable. Set a specific URL to always redirect to on login
- Filter exceptions for certain pages or posts.

**Bug Reports**

Bug reports for [Force Login are welcomed on GitHub](https://github.com/kevinvess/wp-force-login). Please note that GitHub is _not_ a support forum.


== Installation ==

Upload the Force Login plugin to your site, then Activate it.

1, 2: You're done!


== Frequently Asked Questions ==

= How can I specify a URL to redirect to on login? =

By default, the plugin sends visitors back to the URL they tried to visit. However, you can set a specific URL to always redirect users to by adding the following filter to your functions.php file.

The URL must be absolute (as in, http://example.com/mypage/). Recommended: site_url( '/mypage/' ).

`
/**
 * Set the URL to redirect to on login.
 *
 * @return string URL to redirect to on login. Must be absolute.
 **/
function my_forcelogin_redirect() {
  return site_url( '/mypage/' );
}
add_filter('v_forcelogin_redirect', 'my_forcelogin_redirect', 10, 1);
`

= How can I add exceptions for certain pages or posts? =

You can specify an array of URLs to whitelist by adding the following filter to your functions.php file. Each URL must be absolute (as in, http://example.com/mypage/). Recommended: site_url( '/mypage/' ).

`
/**
 * Filter Force Login to allow exceptions for specific URLs.
 *
 * @return array An array of URLs. Must be absolute.
 **/
function my_forcelogin_whitelist() {
  return array(
    site_url( '/mypage/' ),
    site_url( '/2015/03/post-title/' )
  );
}
add_filter('v_forcelogin_whitelist', 'my_forcelogin_whitelist', 10, 1);
`


== Changelog ==

= 3.0 =
- Added hook to set a specific URL to redirect to on login.
- Added hook to allow whitelisting of additional URLs.

= 2.1 =
- Rewrote v_getUrl function to include the server port - props [Nicolas](http://profiles.wordpress.org/nottavi).

= 2.0 =
- Added redirect to send visitors back to the URL they tried to visit after logging in.

= 1.3 =
- Fixed password reset URL from being blocked - props [estebillan](http://profiles.wordpress.org/estebillan).

= 1.2 =
- Streamlined code

= 1.1 =
- Whitelisted the registration page and the Lost Password page - props [jabdo](http://profiles.wordpress.org/jabdo).


== Upgrade Notice ==

= 3.0 =
New features: added developer hooks for customizing the plugin.

= 2.0 =
New feature: added redirect to send visitors back to the URL they tried to visit after logging in.

= 1.3 =
Fixes bug with password reset URL from being blocked.