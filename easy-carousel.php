<?php
/*
  Plugin Name: Easy Responsive Carousel
  Plugin URI: http://matgargano.com
  Description: Adds an Image Carousel *Note* your theme MUST include & enqueue bootstrap 2.3.2+ (including 3+!) - as of right now, this ONLY works with images and they must all be the same size
  Version: 0.2
  Author: matstars
  Author URI: http://matgargano.com
  License: GPL2

@todo refactor meta into its own class
@todo refactor easy carousel class into its own class
@todo include libraries and instantiate plugin from this file

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
	 * @var string
	 */
	public static $text_domain = 'easy_carousel';
	/**
	 * @var string
	 */
	public static $nonce_name = 'easy_carousel';
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
	 * @var
	 */
	public static $post_meta;


	/**
	 *
	 */
	public static function init(){
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'init', array( __CLASS__, 'register_script' ) );
		add_action( 'wp_footer', array( __CLASS__, 'print_script' ) );
		add_action( 'load-post.php', array( __CLASS__, 'post_meta_boxes_setup' ) );
		add_action( 'load-post-new.php', array( __CLASS__, 'post_meta_boxes_setup' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_action( 'admin_print_scripts-post-new.php', array( __CLASS__, 'admin_enqueue' ), 11 );
		add_action( 'admin_print_scripts-post.php', array( __CLASS__, 'admin_enqueue' ), 11 );
		add_shortcode( self::$shortcode, array( __CLASS__, 'shortcode' ) );
		self::$post_meta = array(
			array(
				'name'=>'url',
				'sanitize'=>'esc_url',
				'description'=>'URL'
			),
			[
				'name'=>'content_color',
				'sanitize'=>'sanitize_color',
				'description'=>'Caption background color'
			],


		);
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
			'timeout' => 5000,
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
			if ( $show_content && get_the_content() ) {
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
	static function enqueue(){
		global $post;
		if ( !empty( $post ) && has_shortcode( $post->post_content, self::$shortcode ) ){
			wp_enqueue_style( self::$file_name, plugins_url('css/' .self::$file_name .'.css', __FILE__), false, self::$ver );

		}
	}

	/**
	 *
	 */
	static function admin_enqueue(){
		global $post_type;
		if( self::$post_type != $post_type ) return;
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( self::$file_name . '-admin', plugins_url('js/' .self::$file_name .'-admin.js', __FILE__), array( 'jquery', 'wp-color-picker' ), self::$ver );


	}


	public static function post_meta_boxes_setup() {

		/* Add meta boxes on the 'add_meta_boxes' hook. */
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_post_meta_boxes' ) );

		/* Save post meta on the 'save_post' hook. */
		add_action( 'save_post', array( __CLASS__, 'save_post_class_meta' ), 10, 2 );
	}

	public static function add_post_meta_boxes() {
		add_meta_box( 'easy-carousel', esc_html__( 'Easy Carousel Settings', self::$text_domain ), array( __CLASS__, 'post_class_meta_box' ), self::$post_type, 'normal', 'default' );
	}

	public static function post_class_meta_box( $post ) { ?>

		<?php wp_nonce_field( basename( __FILE__ ), self::$nonce_name ); ?>
		<?php foreach ( self::$post_meta as $meta ) : ?>
		<p>
			<label for="<?php echo $meta['name']; ?>"><?php echo $meta['description']; ?></label>
			<br />
			<input class="widefat" type="text" name="<?php echo $meta['name']; ?>" id="<?php echo $meta['name']; ?>" value="<?php echo esc_attr( get_post_meta( $post->ID, $meta['name'], true ) ); ?>" size="30" />
		</p>
		<?php endforeach; ?>
	<?php }


	public static function save_post_class_meta( $post_id, $post ) {

		/* Verify the nonce before proceeding. */
		if ( !isset( $_POST[ self::$nonce_name ] ) || ! wp_verify_nonce( $_POST[ self::$nonce_name ], basename( __FILE__ ) ) )
			return $post_id;

		/* Get the post type object. */
		$post_type = get_post_type_object( $post->post_type );

		/* Check if the current user has permission to edit the post. */
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
			return $post_id;



		foreach ( self::$post_meta as $meta ) :
			/* Get the posted data and sanitize it for use as an HTML class. */

			$new_meta_value = ( isset( $_POST[ $meta['name'] ] ) ? call_user_func( $meta['sanitize'], $_POST[ $meta['name'] ] ) : '' );
			/* Get the meta value of the custom field key. */
			$meta_value = get_post_meta( $post_id, $meta['name'], true );

			/* If a new meta value was added and there was no previous value, add it. */
			if ( $new_meta_value && '' == $meta_value ){
				add_post_meta( $post_id, $meta['name'], $new_meta_value, true );
			}

			/* If the new meta value does not match the old value, update it. */
			elseif ( $new_meta_value && $new_meta_value != $meta_value ){
				update_post_meta( $post_id, $meta['name'], $new_meta_value );
			}

			/* If there is no new meta value but an old value exists, delete it. */
			elseif ( '' == $new_meta_value && $meta_value ) {
				delete_post_meta( $post_id, $meta['name'], $meta_value );
			}
		endforeach;

		return true;
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






if ( ! function_exists( 'sanitize_color' ) ) {
	function sanitize_color( $hex_color ) {
		if( preg_match( '/^#[a-f0-9]{6}$/i', $hex_color ) )
			return $hex_color;
		return '#000000';
	}
}
