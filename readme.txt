=== mPress Banners ===
Contributors: woodent
Donate link: https://www.paypal.me/wpscholar/15
Tags: banner ad, banners, rotate, carousel, slider, ads, advertising
Requires at least: 3.2
Tested up to: 4.5.2
Stable tag: 1.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Easily create slide-up or slide-down banners on your site with a simple shortcode.

== Description ==

The **mPress Banners** plugin allows you to easily create and customize slide-up or slide-down banners on your website with a simple shortcode.

= Why? =

Sometimes you want to show a persistent banner ad.  Other times you want to rotate continuously through a few different banner ads.  Maybe you just want to show a single ad for a few seconds and then have it disappear.  The mPress Banners plugin gives you the ability to tastefully catch the attention of your users by giving you control over how the ads behave.

= How? =

Using this plugin is simple:

1. Install the plugin
2. Activate the plugin
3. Go to 'Banners' in the WordPress admin menu and create a new banner. Use the 'Set Featured Image' link to upload your banner image.
4. Save your changes
5. In your posts, just use the [mpress_banner] shortcode where you want to display your banner ad.  You will need to pass the banner id(s) to the shortcode like this: [mpress_banner banner="41, 42"].  The banner ids can be found on the banner listing page in the admin.

= Features =

* Works with custom post types.
* No settings page, just adds an easy way for you to display banner ads.
* Clean, well written code that won't bog down your site.

== Installation ==

= Prerequisites =
If you don't meet the below requirements, I highly recommend you upgrade your WordPress install or move to a web host
that supports a more recent version of PHP.

* Requires WordPress version 3.2 or greater
* Requires PHP version 5 or greater ( PHP version 5.2.4 is required to run WordPress version 3.2 )

= The Easy Way =

1. In your WordPress admin, go to 'Plugins' and then click on 'Add New'.
2. In the search box, type in 'mPress Banners' and hit enter.  This plugin should be the first and likely the only result.
3. Click on the 'Install' link.
4. Once installed, click the 'Activate this plugin' link.

= The Hard Way =

1. Download the .zip file containing the plugin.
2. Upload the file into your `/wp-content/plugins/` directory and unzip.
3. Find the plugin in the WordPress admin on the 'Plugins' page and click 'Activate'.

= Usage Instructions =

Once the plugin is installed and activated, go to 'Banners' in the WordPress admin menu.  Click on 'Add New Banner' to create a new banner. Use the 'Set Featured Image' link to upload or set your banner image.  Return to the main banner listing page by clicking on 'Banners' in the admin menu once more.  Take note of the banner id you just created.  Navigate to a post where you want to display a banner and paste in the banner shortcode.  If the banner id is 14, then the shortcode would be: `[mpress_banner banner="14"]`.  For more details on how you can use the shortcode to customize your banner display, see the FAQ page.

== Frequently Asked Questions ==

= Can visitors dismiss unwanted ads? =

Yes. By default, a visitor can dismiss an ad by clicking the close box in the upper right corner.  If you don't want visitors to be able to dismiss ads, then you can disable this using the shortcode.

= What is required to make the shortcode work? =

The `[mpress_banner]` shortcode requires only the 'banner' attribute to work properly.  All other attributes are optional.  The shortcode will only display a banner if the banner id is passed to the 'banner' attribute, like this: `[mpress_banner banner="12"]`.  You can pass in multiple banner ids like this: `[mpress_banner banner="12, 14, 21"]`.  Banners will display in the order that the ids are listed.  Banner ids are listed in the admin on the banner listing screen.

Optionally, you can use the shortcode like this if you wish to simply provide your own html markup: `[mpress_banner]<a href="{link_url}"><img src="{image_url}" /></a>[/mpress_banner]`.

If you simply use the `[mpress_banner]` shortcode with no attributes, then admins will see a help message.

= What are the optional settings that allow me to customize my banner ads? =

The shortcode provides several optional settings, or attributes, with which you can customize your banner ads:

* **id** - Set a custom HTML id for the banner wrapper so you apply custom CSS to specific banners.  Example: `[mpress_banner banner="1" id="my-banner"]`

* **time_delay** - Customize the time delay in seconds for your ads.  The default is 5 seconds.  Example: `[mpress_banner banner="1, 2" time_delay="10"]`

* **direction** - Set the banner to slide up or down.  Can be set to 'down'.  The default is 'up'.  Example: `[mpress_banner banner="1" direction="down"]`

* **persistent** - By setting this attribute to 'true', no animation will be applied and the ad won't disappear after a set time delay.  Basically, the ad will behave more like an image inserted into a post. *Note: If you pass in multiple banner ids, only the first banner will be displayed since no animations are applied.* Example: `[mpress_banner banner="1" persistent="true"]`

* **loop** - Setting this attribute to 'true' will cause the banner(s) to continuously rotate.  This feature is turned off by default.  Example: `[mpress_banner banner="1" loop="true"]`

* **dismissible** - Set this attribute to 'false' to prevent users from being able to dismiss ads.  Example: `[mpress_banner banner="1" dismissible="false"]`

* **random** - Set this attribute to 'true' to shuffle the banner order on each page load.  Obviously, you will need to pass in multiple banner ids for this to work.  Example: `[mpress_banner banner="1, 2, 3, 4, 5" random="true"]`

= Can I display a banner or set of banners on a sitewide basis? =

Yes. Just use this code to insert banners in your theme template files: `<?php echo do_shortcode('[mpress_banner banner="1, 2"]'); ?>`.

Additionally, you can use a filter such as 'the_content' to append or prepend the shortcode to all of your posts or to a specific set of posts.

== Changelog ==

= 1.0 =

* Tested in WordPress version 4.5.2

= 0.1 =

* Initial commit

== Upgrade Notice ==

= 1.0 =

* Plugin updated to reflect that it works with WordPress version 4.5.2