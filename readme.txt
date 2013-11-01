=== Easy Responsive Carousel ===
Contributors: matstars
Tags: custom post types, CPT, post, types, post type, order post types
Requires at least: 3.6.1
Tested up to: 3.6
Stable tag: 0.2
License: GPLv2

Adds an Image Carousel post type and shortcode. Note your theme MUST include & enqueue bootstrap 2.3.2+ (including 3+!) - as of right now, this ONLY works with images and they must all be the same size

== Description ==

Creates a post type called Easy Carousel. It's hierarchical - parent post is the main post and the children are the slideshow slides. Upload a featured image to each child post. Each image needs to be the same size.

N.B. Your theme must have 'post-thumbnails' enabled ( see http://codex.wordpress.org/Function_Reference/add_theme_support for more information ).

Adds a shortcode [easy_carousel id=N ] with the required variables:

- id => ID of the parent Easy Carousel Post

...and the optional variables:

- timeout => milliseconds to pause in between slides
- pause => if set to true - the slideshow will pause on hover; set to false - the carousel does not pause on hover.
- effect => "slide" or "fade"
- orderby => what to order the children posts
- order => direction to order the posts
- display_mobile => will hide on mobile if true.
    

== Changelog ==

= 0.2 =

Added LESS support
Added fade as an effect for transitions

= 0.1.1 =

Bug fixes

= 0.1 =

Initial release