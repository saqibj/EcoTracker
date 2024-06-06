<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function ecopower_tracker_register_widgets() {
    register_widget( 'EcoPower_Tracker_Widget' );
}
add_action( 'widgets_init', 'ecopower_tracker_register_widgets' );

class EcoPower_Tracker_Widget extends WP_Widget {
    function __construct() {
        parent::__construct(
            'ecopower_tracker_widget',
            __( 'EcoPower Tracker Widget', 'ecopower-tracker' ),
            [ 'description' => __( 'A widget to display EcoPower Tracker data.', 'ecopower-tracker' ) ]
        );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        echo __( 'EcoPower Tracker data will be displayed here.', 'ecopower-tracker' );
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'ecopower-tracker' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'ecopower-tracker' ); ?></label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php 
    }

    public function update( $new_instance, $old_instance ) {
        $instance = [];
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

        return $instance;
    }
}
?>