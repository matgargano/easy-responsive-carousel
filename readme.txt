=== Easy Responsive Carousel ===
Contributors: matstars
Tags: custom post types, CPT, post, types, post type, order post types
Requires at least: 3.6.1
Tested up to: 3.6
Stable tag: 0.1
License: GPLv2

Adds an Image Carousel shortcode. Note your theme MUST have bootstrap 2.3.2 - this ONLY works with images and they must all be the same size

== Description ==

Creates a post type called Easy Carousel. It's hierarchical - parent post is the main post and the children are the slideshow slides. Upload a featured image to each child post. Each image needs to be the same size 

Adds a shortcode [easy_carousel] with the optional variables:
id => ID of the parent Easy Carousel Post
timeout => milliseconds to pause in between slides
pause => if set to true - the slideshow will pause on hover; set to false - the slideshow does not pause on hover.
effect => set to slide to have the slides "slide" effect
orderby => what to order the children posts
order => direction to order the posts
display_mobile => will hide on mobile if true.
    

== Changelog ==

= 0.1 =

Initial release