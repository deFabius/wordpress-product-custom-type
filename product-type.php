<?php 
/*
Plugin Name: Products custom post Types
Description: This is a plugin that provides the Custom Post Types 
  to include pdf as main content of the post.
Author: Fabio Valle
Version: 0.1
Author URI: http://sitepoint.com/
*/

if ( ! function_exists('write_log')) {
   function write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}


function product_post_type_register() {
	register_post_type( 'product',
		array(
			'labels' => array(
				'name' => __( 'Products' ),
				'singular_name' => __( 'Product' ),
				'add_new' => __( 'Add New Product' ),
				'add_new_item' => __( 'Add New Product' ),
				'edit' => __( 'Edit' ),
				'edit_item' => __( 'Edit Product' ),
				'new_item' => __( 'New Product' ),
				'view' => __( 'View Product' ),
				'view_item' => __( 'View Product' ),
				'search_items' => __( 'Search Products' ),
				'not_found' => __( 'No product' ),
				'not_found_in_trash' => __( 'No products in the Trash' ),
				),
			'has_archive' => true,
			'hierarchical' => false,
			'taxonomies' => array('category'),
			'public' => true,
			'menu_position' => 5,
			'menu_icon' => 'dashicons-media-interactive',
			'has_archive' => 'products',
			'rewrite' => array('slug' => 'products'),
			'supports' => array( 'title', 'excerpt', 'editor', 'thumbnail'),
			'description' => "A product page."

			)
		);
	flush_rewrite_rules();
}

add_action( 'init', 'product_post_type_register' );

function product_fields (){
	global $post;
	$custom = get_post_custom($post->ID);
	$selected_product = isset($custom["selected_product"]) ? $custom["selected_product"][0] : "";
	?>

	<p>
		<label for="product_price">Price</label>
		<input id="product_price" type="text" name="product-price" value="<?= get_post_meta(get_the_ID(), "product-price", true) ?>" /> €
	</p>

	<p>
		<label for="select_product_images">Gallery</label>
		<input id="select_product_images_button" type="button" value="Select images" />
		<div id="media_thumbnails">
			<?php 
            $images = get_post_meta(get_the_ID(), "product-images", true);
            
            if(is_array($images)) {
                foreach ($images as $index => $imageId) {
                    $image = wp_get_attachment_image( $imageId, 'thumbnail') ;
                    echo '<div class="thumbnail-container">';
                    echo '	<button class="image-delete"><span class="dashicons dashicons-trash"></span></button>';
                    echo '	' . $image;
                    echo '	<input type="hidden" name="product-images[' . $index . ']" value="' . $imageId . '">';
                    echo '</div>';
                }
            }
			?>
		</div>
	</p>
	<?php
}

function add_product_box(){
	add_meta_box(
		"product_info",
		"Product Details",
		"product_fields",
		"product"
		);
}

function save_product_attributes($post_id, $post){
	$request = stripslashes_deep( $_POST );

	write_log( $request );
	$keyImgs = "product-images";
	$keyPrice = "product-price";

	save_meta($post_id, $request, $keyImgs);
	save_meta($post_id, $request, $keyPrice);
}

function save_meta($post_id, $request, $key) {

	$saved_data = get_post_meta( $post_id, $key, true );

	$submitted_data = isset($request[$key]) ? $request[$key] : null;

	if ($submitted_data && '' == $saved_data) {
		add_post_meta ($post_id, $key, $submitted_data);
	} elseif ($submitted_data && $submitted_data != $saved_data){
		update_post_meta($post_id, $key, $submitted_data);
	} elseif (empty($submitted_data) && $saved_data) {
		delete_post_meta( $post_id, $key);
	}	
}

add_action('admin_init', 'add_product_box' ); 
add_action('save_post', 'save_product_attributes', 10, 2);
add_action('publish_post', 'save_product_attributes', 10, 2);

function remove_product_custom_fields() {
	remove_meta_box( 'postexcerpt' , 'product' , 'normal' );
	remove_meta_box( 'postcustom' , 'product' , 'normal' );
	
}
add_action( 'admin_menu' , 'remove_product_custom_fields' );

function my_admin_scripts() {
	if (get_current_screen()->post_type == 'product') {
		wp_register_script('my-upload', plugins_url() . '/product-type/js/product-cp-script.js', array('jquery'));
		wp_enqueue_script('my-upload');
		wp_enqueue_style('admin-style', plugins_url() . '/product-type/css/admin-style.css');
	}
}

function my_admin_styles() {
}

add_action('admin_enqueue_scripts', 'my_admin_scripts');
add_action('admin_enqueue_styles', 'my_admin_styles');
?>