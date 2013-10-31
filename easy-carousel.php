<?php
/*
  Plugin Name: Easy Responsive Carousel
  Plugin URI: http://matgargano.com
  Description: Adds an Image Carousel *Note* your theme MUST include & enqueue bootstrap 2.3.2+ (including 3+!) - as of right now, this ONLY works with images and they must all be the same size
  Version: 0.2
  Author: matstars
  Author URI: http://matgargano.com
  License: GPL2
 */


/**
 * Class easy_carousel
 */
class easy_carousel {

	/**
	 * @var string
	 */
	public static $post_type = 'easy_carousel';
	/**
	 * @var string
	 */
	public static $file_name = 'easy-carousel';
	/**
	 * @var string
	 */
	public static $shortcode = 'easy_carousel';
	/**
	 * @var int
	 */
	public static $incrementer = 0;
	/**
	 * @var string
	 */
	public static $ver = '0.2';
	/**
	 * @var
	 */
	public static $add_script;

	/**
	 *
	 */
	public static function init(){
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_shortcode( self::$shortcode, array( __CLASS__, 'shortcode' ) );
		add_action( 'init', array( __CLASS__, 'register_script' ) );
		add_action( 'wp_footer', array( __CLASS__, 'print_script' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_stylesheet' ) );
	}

	/**
	 *
	 */
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
			'supports'           => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		);

		register_post_type( self::$post_type, $args );
	}

	/**
	 * @param $atts
	 *
	 * @return string
	 */
	public static function shortcode( $atts ) {
		self::$add_script = true;
		/* initialize variables */
		$id = $timeout = $pause = $effect = $orderby = $order = $display_mobile = $show_content = '';
		extract( shortcode_atts( array(
			'id' => -1,
			'timeout' => 2000,
			'pause' => false,
			'effect' => '',
			'orderby' => 'menu_order',
			'order' => 'asc',
			'display_mobile' => true,
			'show_content' => true
			), $atts) );
		if ( ! $display_mobile && wp_is_mobile() ) {
			return false;
		} 
		static::$incrementer++;
		$html = $pause_att = '';
		$counter = 0;
		if ( $effect ) {
			$effect = ' ' . $effect;
		}
		if ( ! $pause ) {
			$pause_att = ' "pause" : false ';
		}
		if ( $id == -1 || !get_post( $id ) ) {
			return false;
		}
		if ( ! self::$post_type == get_post_type( $id ) ) {
			return false;
		}

		$html .= '<div class="easy-responsive-carousel carousel' . $effect . '" id="carousel-' . static::$incrementer . '">';
		$html .= '<div class="carousel-inner">';
		
		$children = get_posts( array( 'post_type' => self::$post_type, 'post_parent' => $id, 'orderby' => $orderby, 'order' => $order ) );

		foreach( $children as $post ) : setup_postdata($post);

			$counter++;
			$active = '';
			if ($counter === 1) {
				$active = ' active';
			}
			$html .= '<div class="item' . $active . '">';
			$html .= get_the_post_thumbnail( $post->ID, $size = 'full' );
			if ( $show_content ) {
				$html .= '<div class="content">';
				$html .= get_the_content();
				$html .= '</div>';
			}
			$html .= '</div>';
            wp_reset_postdata();
        endforeach;
		$html .= '</div>';
		$html .= '</div>';
		$html .= '<script>';
		$html .= 'jQuery(".carousel#carousel-' . static::$incrementer . '").carousel( { "interval" : ' . $timeout . ', ' . $pause_att . '} );';
		$html .= '</script>';
		
		return $html;
	}

	/**
	 *
	 */
	static function register_script() {

		wp_register_script( self::$file_name, plugins_url('js/' . self::$file_name .'.js', __FILE__), array('jquery'), self::$ver, true );
	}

	/**
	 *
	 */
	static function print_script() {
		if ( ! self::$add_script )
			return;
		wp_print_scripts( 'easy-carousel' );
	}

	/**
	 *
	 */
	static function enqueue_stylesheet(){
		global $post;
		if ( !empty( $post ) && has_shortcode( $post->post_content, self::$shortcode ) ){
			wp_enqueue_style( self::$file_name, plugins_url('css/' .self::$file_name .'.css', __FILE__), false, self::$ver );
			
		}
	}


}


easy_carousel::init();


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