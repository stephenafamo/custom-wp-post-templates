=== Plugin Name ===
Contributors: stephenafamo
Donate link: http://stephenafamo.com
Tags: html, php, custom pages, custom templates, custom posts
Requires at least: 3.0.1
Tested up to: 4.7.1
Stable tag: 4.7.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Use your HTML or PHP files for any page or post.

== Description ==

This plugin allows you to use any HTML or PHP file as the template for any page or post. 

Simply upload the file and select it. 
You can upload custom js and css files into the media library and link to them from the HTML file.

Options:

* Overwrite All: You overwrite the entire theme and use your custom file
* Overwrite Content: Keeps the header, footer, sidebar, e.t.c. Simply overwrites the body of the page or post
* Above Content: Your custom content is simply added to the top of the page content
* Below Content: You custom content is placed just beneath the page content.

== Installation ==

1. Upload `html-php-pages-and-posts` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= What about external JS and CSS files? =

You can upload it all into the media library. Simply reference them properly in the html file. 
e.g. `<link rel='stylesheet' href='http://example.com/wp-content/2017/01/my_custom_stylesheet.css' type='text/css' />`

= Will template tags work in my custom templates? =

Yes.
All wordpress functions, and any installed plugin function will work if called properly

== Changelog ==

= 1.0.0 =
* First release