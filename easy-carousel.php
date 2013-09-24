<?php
/*
  Plugin Name: Easy Responsive Carousel
  Plugin URI: http://vocecommunications.com
  Description: Adds an Image Carousel MUST have bootstrap 2.3.2 - this ONLY works with images and they must all be the same size
  Version: 0.1.0
  Author: matstars, voceplatforms
  Author URI: http://vocecommunications.com
  License: GPL2
 */


class easy_carousel {
	const POST_TYPE = 'easy_carousel';
	static $incrementer = 0;

	public static function init(){
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_shortcode( 'easy_carousel', array( __CLASS__, 'shortcode' ) );
	}

	public static function register_post_type(){
		$labels = array(
			'name'               => 'Easy Carousel',
			'singular_name'      => 'Easy Carousel',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Carousel',
			'edit_item'          => 'Edit Carousel',
			'new_item'           => 'New Carousel',
			'all_items'          => 'All Carousels',
			'view_item'          => 'View Carousel',
			'search_items'       => 'Search Carousels',
			'not_found'          => 'No Carousels found',
			'not_found_in_trash' => 'No Carousels found in Trash',
			'parent_item_colon'  => '',
			'menu_name'          => 'Easy Carousel'
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'hierarchical'       => true,
			'has_archive'        => true,
			'rewrite'            => false,
			'supports'           => array( 'title', 'thumbnail', 'page-attributes' ),
		);

		$__CLASS__ = __CLASS__;
		register_post_type( $__CLASS__::POST_TYPE, $args );
	}

	public static function shortcode( $atts ) {
		extract( shortcode_atts( array(
			'id' => -1,
			'timeout' => 5000,
			'pause' => 'false',
			'effect' => '',
			'orderby' => 'menu_order',
			'order' => 'asc',
			'display_mobile' => true
			), $atts) );
		if ( !$display_mobile && wp_is_mobile() ) {
			return '';	
		} 
		static::$incrementer++;
		$script_html = $html = $pause_att = '';
		$counter = 0;
		if ( $effect != '' ) {
			$effect = ' ' . $effect;
		}
		if ( $pause == 'false' ) {
			$pause_att = ' "pause" : true ';
		}
		if ( $id == -1 || !get_post( $id ) ) {
			return;
		}
		$__CLASS__ = __CLASS__;
		if ( !$__CLASS__::POST_TYPE == get_post_type( $id ) ) {
			return;
		}

		$children = get_posts( array( 'post_type' => $__CLASS__::POST_TYPE, 'post_parent' => $id, 'orderby' => $orderby, 'order' => $order ) );
		$html .= '<div class="carousel' . $effect . '" id="carousel-' . static::$incrementer . '">';
		$html .= '<div class="carousel-inner">';
		foreach( $children as $post ) {
			$counter++;
			$active = '';
			if ($counter === 1) {
				$active = ' active';
			}
			$html .= '<div class="item' . $active . '">';
			$html .= get_the_post_thumbnail( $post->ID, $size = 'full' );
			$html .= '</div>';
		}
		$html .= '</div>';
		$html .= '</div>';
		$script_html .= '<script>';
		$script_html .= 'jQuery(".carousel#carousel-' . static::$incrementer . '").carousel( { "interval" : ' . $timeout . ', ' . $pause_att . '} );';
		$script_html .= '</script>';
		add_action( 'wp_footer', function() use ( $script_html ) {
			echo $script_html;
		}, 10000 );
		return $html;
	}
}


easy_carousel::init();
