<?php
// Register and load the widget
function wpb_load_widget() {
    register_widget( 'product_category_widget' );
}
add_action( 'widgets_init', 'wpb_load_widget' );
 
// Creating the widget 
class product_category_widget extends WP_Widget {
 
    function __construct() {
        parent::__construct(
            // Base ID of your widget
            'product_category_widget', 
            
            // Widget name will appear in UI
            __('Popular Categories', 'product_category_widget_domain'), 
            
            // Widget description
            array( 'description' => __( 'This widget will list popular product categories', 'product_category_widget_domain' ), ) 
        );
    }
    
    // Creating widget front-end
    
    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );
        
        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if ( ! empty( $title ) ) echo $args['before_title'] . $title . $args['after_title'];
        

        echo "<ul>";

        $taxonomyArgs=array(
            'name'     => 'product_category',
            'public'   => true,
            '_builtin' => false
        );
        $output = 'names'; // or objects
        $operator = 'and';
        $taxonomies=get_taxonomies($taxonomyArgs,$output,$operator); 
        if  ($taxonomies) {
            foreach ($taxonomies  as $taxonomy ) {
                $terms = get_terms([
                    'taxonomy' => $taxonomy,
                    'hide_empty' => false
                ]);
                foreach ( $terms as $term) {
                ?>
                <li>
                <a class="cat-widget-link" href="<?=get_category_link($term->term_id)?>" style="background-image: url(<?=z_taxonomy_image_url($term->term_id)?>)"><?php echo $term->name; ?></a></li>
                <?php 
                }
            }
        }
        
        echo "</ul>";
        echo $args['after_widget'];
    }
            
    // Widget Backend 
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'New title', 'product_category_widget_domain' );
        }
        // Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>

        <p>
            <input type="checkbox" class="checkbox" id="widget-categories-3-dropdown" name="widget-categories[3][dropdown]">
            <label for="widget-categories-3-dropdown">Display as dropdown</label><br>

            <input type="checkbox" class="checkbox" id="widget-categories-3-count" name="widget-categories[3][count]">
            <label for="widget-categories-3-count">Show post counts</label><br>

            <input type="checkbox" class="checkbox" id="widget-categories-3-hierarchical" name="widget-categories[3][hierarchical]">
            <label for="widget-categories-3-hierarchical">Show hierarchy</label>
        </p>
        <?php 
    }
        
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }
} // Class product_category_widget ends here