=== Ninja Forms - User Analytics ===
Contributors: never5
Donate link: http://www.never5.com
Tags: form, forms, user, analytics, geolocation, ip address
Requires at least: 4.0
Tested up to: 4.3
Stable tag: 1.2.5

License: GPLv2 or later

== Description ==

An extension for the Ninja Forms WordPress plugin that tracks user analytics automagically.

= Features =

Track all sorts of user analytics without having to bother the user.
* IP address
* Browser
* Browser Version
* Operating System
* Country
* State
* City
* Latitude
* Logitude
* UTM Campaign
* UTM Source
* UTM Medium
* UTM Content
* UTM Term

== Screenshots ==

To see up to date screenshots, visit the [Ninja Forms](http://wpninjas.com/ninja-forms/) page.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the `ninja-forms-user-analytics` directory to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Use ==

For help and video tutorials, please see the [documentation on the Ninja Forms website](http://wpninjas.com/ninja-forms/docs/).

== Changelog ==

= 1.2.5 =
* Tweak: Route hostip.info request via own server to add secure connections (443).

= 1.2.4 =
* Tweak - Added URL Referer field.

= 1.2.3 =
* Bug - Using NF 2.9 tab name. Fixes issue adding UA fields to forms. 

= 1.2.2 =
* Tweak   - No longer passing variables by reference. Causes issues in PHP 5.4+ (http://php.net/manual/en/language.references.pass.php)

= 1.2.1 =
* Bug     - Replacing GeoIP service with HostIp.info
* Tweak   - Using HTTP_X_FORWARDED_FOR to get IP address

= 1.2.0 =
* Feature - Adding UTM fields

= 1.1.0 =
* Feature - Allow other plugins to access UA fields

= 1.0.2 =
* Tweak - Implementing Ninja Forms licensing

= 1.0.1 =
* Tweak - Adding icons to updater
* Tweak - Changing main plugin file slug to be more consistent with other Ninja Forms extensions
* Tweak - Refactoring custom fields

= 1.0 =
* Initial release
