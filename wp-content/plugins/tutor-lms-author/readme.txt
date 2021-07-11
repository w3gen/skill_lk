=== Tutor LMS Author Ownership Changer - Migrate your Course Author Ownership ===
Contributors: FahimMurshed
Donate link: http://www.murshidalam.com/donation
Tags: Tutor LMS, Tutor LMS Author, Change Courses Author, Tutor, Change Course Author Ownership
Requires at least: 5.0
Tested up to: 5.5
Requires PHP: 7.0
Stable tag: trunk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html

Easily change the Tutor LMS course author ownership.

== Description ==

Tutor Learning Management Systems was primarily designed to help you administer and teach eLearning courses online. Now you can change the authorship with this plugin.

Go to modify plugin file: `/wp-content/plugins/tutor/blob/master/classes/Course.php` Line# 40 and remove this code

`add_filter('wp_insert_post_data', array($this, 'tutor_add_gutenberg_author'), '99', 2);`

Now you can change the Author with this plugin.

If you need more assistance, <a href="https://murshidalam.com/contact/">contact me</a>

== Installation ==
a.
1. Go to the Plugins menu
2. Add new plugin
3. Search "Tutor LMS Author"
4. Install and activate
5. Go to Tutor LMS > Courses > change the author
6. Enjoy


b.  
1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Now check "Product" tab and click any Product "Quick Edit" button and change the Author.
4. Install and activate
5. Go to Tutor LMS > Courses > change the author
6. Enjoy

== Frequently Asked Questions ==

= Only for Tutor LMS =

Yes, this plugin only works for Tutor LMS

= Do I need another account user =

Yes, this plugin only works when you have another Administrator/Editor/Author account.

= Do I need to do anything after installation =

No, just install and activate. Enjoy.

== Screenshots ==

1. How to change Tutor LMS author from Courses.

== Upgrade Notice ==
Just click on the update button.

== Changelog ==

= 1.0.3 =
* Tutor LMS bug

= 1.0.2 =
* WordPress 5.4 compatible

= 1.0.1 =
* SVG Icon Change
* Title Change

= 1.0.0 =
* Initial release
