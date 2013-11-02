<?php
/*
  Plugin Name: Easy Responsive Carousel
  Plugin URI: http://matgargano.com
  Description: Adds an Image Carousel *Note* your theme MUST include & enqueue bootstrap 2.3.2+ (including 3+!) - as of right now, this ONLY works with images and they must all be the same size
  Version: 0.3
  Author: matstars
  Author URI: http://matgargano.com
  License: GPL2

*/


foreach ( glob( plugin_dir_path(__FILE__) . "lib/*.php" ) as $filename ) include $filename;

Easy_carousel::init();
Easy_carousel_admin::init();


/* if < WP 3.6 let's add in has_shortcode */

if ( !function_exists('has_shortcode') ) {
	/**
	 * @param $content
	 * @param $tag
	 *
	 * @return bool
	 */
	function has_shortcode( $content, $tag ) {
		if ( shortcode_exists( $tag ) ) {
			preg_match_all( '/' . get_shortcode_regex() . '/s', $content, $matches, PREG_SET_ORDER );
			if ( empty( $matches ) )
				return false;

			foreach ( $matches as $shortcode ) {
				if ( $tag === $shortcode[2] )
					return true;
			}
		}
		return false;
	}
}






if ( ! function_exists( 'sanitize_color' ) ) {
	/**
	 * @param $hex_color
	 *
	 * @return string
	 */
	function sanitize_color( $hex_color ) {
		if( preg_match( '/^#[a-f0-9]{6}$/i', $hex_color ) )
			return $hex_color;
		return '#000000';
	}
}


/* if < WP 3.6 let's add in has_shortcode */

if ( !function_exists('has_shortcode') ) {
	/**
	 * @param $content
	 * @param $tag
	 *
	 * @return bool
	 */
	function has_shortcode( $content, $tag ) {
         if ( shortcode_exists( $tag ) ) {
                 preg_match_all( '/' . get_shortcode_regex() . '/s', $content, $matches, PREG_SET_ORDER );
                 if ( empty( $matches ) )
                         return false;

                 foreach ( $matches as $shortcode ) {
                         if ( $tag === $shortcode[2] )
                                 return true;
                 }
         }
         return false;
	}
}






if ( ! function_exists( 'sanitize_color' ) ) {
	/**
	 * @param $hex_color
	 *
	 * @return string
	 */
	function sanitize_color( $hex_color ) {
		if( preg_match( '/^#[a-f0-9]{6}$/i', $hex_color ) )
			return $hex_color;
		return '#000000';
	}
}
