<?php
/**
 * Created by PhpStorm.
 * User: mat
 * Date: 11/5/13
 * Time: 4:25 PM
 */

class Easy_carousel_admin_sort {


	/**
	 * @var string
	 */
	public static $text_domain = 'easy_carousel';
	/**
	 * @var string
	 */
	public static $nonce_name = 'easy_carousel_sort';

	/**
	 * @var string
	 */
	public static $ver;

	/**
	 * @var string
	 */
	public static $file_name;

	/**
	 * @var
	 */
	public static $post_meta;

	/**
	 * @var
	 */

	public static $post_type;


	/**
	 *
	 * Initialize plugin
	 *
	 * @return void
	 *
	 */

	public static function init(){
		self::$post_type = Easy_carousel::$post_type;
		self::$ver = Easy_carousel::$ver;
		self::$file_name = Easy_carousel::$file_name;
		add_action( 'load-post.php', array( __CLASS__, 'post_meta_boxes_setup' ) );
		add_action( 'load-post-new.php', array( __CLASS__, 'post_meta_boxes_setup' ) );
  }

	/**
	 *
	 * Set up metaboxes
	 *
	 * @return void
	 *
	 */
	public static function post_meta_boxes_setup() {

		/* Add meta boxes on the 'add_meta_boxes' hook. */
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_post_meta_boxes' ) );

		/* Save post meta on the 'save_post' hook. */
		add_action( 'save_post', array( __CLASS__, 'save_post_class_meta' ), 10, 2 );
	}

	/**
	 *
	 * Add meta boxes
	 *
	 * @return void
	 *
	 */
	public static function add_post_meta_boxes() {
		global $post;
		if ( $post->post_parent != 0 )
			return;
		add_meta_box( 'easy-carousel-sort', esc_html__( 'Sort Slides', self::$text_domain ), array( __CLASS__, 'post_class_meta_box' ), self::$post_type, 'normal', 'default' );
	}

	/**
	 *
	 * Output meta elements
	 *
	 * @param $post
	 * @return void
	 *
	 */
	public static function post_class_meta_box( $post ) { ?>
				<?php wp_nonce_field( basename( __FILE__ ), self::$nonce_name ); ?>
				<?php


				if ( $post->post_parent != 0 )
					return;
				$list_items = $post_order = '';
				$args = array(
					'posts_per_page' => -1,
					'orderby' => 'menu_order',
					'order' => 'asc',
					'post_status' => 'publish',
					'post_parent' => $post->ID,
					'post_type' => self::$post_type

				);
				$children = get_posts($args);
				if ( ! $children ) {
					echo __( 'There must be children posts in order to sort.', self::$text_domain );
				}
				foreach ( $children as $child ) :
					$title = get_the_title( $child->ID );
					$id = $child->ID;
					if ( ! $title ) {
						$title = '(no title)';
					}
					$list_items .= '<li data-post-id="' . $id . '">' . $title . '</li>';
					if ( $post_order ) $post_order .= ';';
					$post_order .= $id;

				endforeach;

				?>
				<ol id="slideshow-sortable">
					<?php
					echo $list_items;
					?>
				</ol>
				<input id="post-order" name="post-order" type="hidden" value="<?php echo $post_order; ?>">

	<?php }


	/**
	 *
	 * Hook onto save action
	 *
	 * @param $post_id
	 * @param $post
	 *
	 * @return bool
	 */
	public static function save_post_class_meta( $post_id, $post ) {
		/* Verify the nonce before proceeding. */
		if ( !isset( $_POST[ self::$nonce_name ] ) || ! wp_verify_nonce( $_POST[ self::$nonce_name ], basename( __FILE__ ) ) )
			return $post_id;

		/* Get the post type object. */
		$post_type = get_post_type_object( $post->post_type );

		/* Check if the current user has permission to edit the post. */
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
			return $post_id;


		$new_meta_value = $_POST[ 'post-order' ];

		/* Get the meta value of the custom field key. */
		$meta_value = get_post_meta( $post_id, 'post-order', true );

		/* If a new meta value was added and there was no previous value, add it. */
		if ( $new_meta_value && '' == $meta_value ){
			add_post_meta( $post_id, 'post-order', $new_meta_value, true );
		}

		/* If the new meta value does not match the old value, update it. */
		elseif ( $new_meta_value && $new_meta_value != $meta_value ){
			$posts = explode(';', $new_meta_value);
			$counter = 1;
			foreach($posts as $post){
				self::set_menu_order( $post, $counter );
				$counter++;
			}

			update_post_meta( $post_id, 'post-order', $new_meta_value );
		}

		/* If there is no new meta value but an old value exists, delete it. */
		elseif ( '' == $new_meta_value && $meta_value ) {
			delete_post_meta( $post_id, 'post-order', $meta_value );
		}


		return true;
	}


	/**
	 * Helper function to update the menu_order on the posts table
	 *
	 * @method set_menu_order
	 * @param int $post_id
	 * @param int $counter
	 * @return void
	 */


	public static function set_menu_order( $post_id, $counter ) {
		global $wpdb;
		$wpdb->update( $wpdb->posts, array( 'menu_order' => $counter ), array( 'ID' => $post_id ) );
	}
} 