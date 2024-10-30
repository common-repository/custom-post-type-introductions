=== Custom Post Type Introductions ===
Contributors: jamesckemp
Donate link: 
Tags: custom post type, options page, introduction
Requires at least: 3.3
Tested up to: 3.5.1
Stable tag: v1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add a title and introduction to display on your custom post type archive.

== Description ==

Custom Post Type Introductions adds a new "Introduction" menu under all publicly viewable custom post types on your site. Here you can set a title and intro text to display on your post type archive.

This functionality is currently missing from WordPress and the only workaround I've found is to create a page to hold the data. My idea with this is to keep everything under the most logical menus for the best user experience.

== Installation ==

1. Upload the `custom-post-type-introductions` folder to the `/wp-content/plugins/` directory, or install via **Plugins > Add New > Upload** and upload the `custom-post-type-introductions.zip` file.
1. Activate the plugin through the 'Plugins' menu in WordPress.

== Changelog ==

= v1.0.1 =
* Swapped constructors round to remove strict waring.

= v1.0.0 =
* Initial Release

== Usage ==

Once installed, a new link will appear under all publicly visible custom post types, entitle `Introduction`. Here you can enter a title and introduction.

To show the title or content in your theme, you can either use the shortcode on a page:

`[post_type_intro field="title" posttype="your_posttype"]`

The options are:

* **field**: title/content
* **posttype**: your_posttype

Alternatively, you can use the shortcode directly in a template:

`<?php echo do_shortcode('[post_type_intro field="title" posttype="your_posttype"]'); ?>`

The best place to use this is in your `archive-your_posttype.php` file.