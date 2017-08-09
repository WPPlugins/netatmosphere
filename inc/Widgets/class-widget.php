<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

include_once(ABSPATH . 'wp-includes/widgets.php');

/**
 * Adds NAS_Widget widget.
 */
class NAS_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'netatmosphere_widget', // Base ID
			__( 'NetAtmoSphere', 'netatmosphere' ), // Name
			array( 'description' => __( 'Widget to display actual weather data', 'netatmosphere' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}

		$showLatest = $instance['show_latest'] === 'on';
		$showDailyAvg = $instance['show_daily_avg'] === 'on';
		$showMap = $instance['show_map'] === 'on';
        /*echo "showLatest: " . $showLatest . "<br/>";
        echo "showDailyAvg: " . $showDailyAvg . "<br/>";
        echo "showMap: " . $showMap . "<br/>";*/

        $showTable = $showLatest || $showDailyAvg;
        
        $latest = null; $dailyAvg = null; $coordinates = null;
        // prepare variables
        if( $showLatest )
            $latest = NAS_Data_Adapter::getLatest(NAS_Device_Locations::Outdoor);
        if( $showDailyAvg )
            $dailyAvg = NAS_Data_Adapter::getAvgOfDay(null, NAS_Device_Locations::Outdoor);
        if( $showMap ) 
            $coordinates = NAS_Devices_Adapter::getLocation();
        
        if( $showTable ) {
            
            $table = new NAS_Data_Table();
            
            if( null !== $latest && count ( $latest ) > 0 )
                $table->addRow( NAS_Data_Adapter::results2row($latest, 'Latest') );
            if( null !== $dailyAvg && count ( $dailyAvg ) > 0 )
                $table->addRow( NAS_Data_Adapter::results2row($dailyAvg, 'Daily') );
            
            $table->mergeColumns();
            
            echo $table->htmlTable();
        }
        
        if( $showMap ) {
            
            echo '<iframe frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?q=' . $coordinates['lat'] . '%2C%20' . $coordinates['lng'] . '&amp;key=AIzaSyAerG7YMfNlrQpWRyG1wvH1O9wcmNWOSHs&amp;zoom=12" allowfullscreen></iframe>';
        }
        
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Weather @ home', 'netatmosphere' );
        $instance['show_latest']    = ! empty( $instance['show_latest'] ) ? $instance['show_latest'] : false;
        $instance['show_daily_avg'] = ! empty( $instance['show_daily_avg'] ) ? $instance['show_daily_avg'] : false;
        $instance['show_map']       = ! empty( $instance['show_map'] ) ? $instance['show_map'] : false;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		
		<p>
			<input class="checkbox" type="checkbox" <?php checked($instance['show_latest'], 'on'); ?> id="<?php echo $this->get_field_id('show_latest'); ?>" name="<?php echo $this->get_field_name('show_latest'); ?>" /> 
			<label for="<?php echo $this->get_field_id('show_latest'); ?>"><?php _e('Show latest measures', 'netatmosphere'); ?></label>
		</p>
        <p>
			<input class="checkbox" type="checkbox" <?php checked($instance['show_daily_avg'], 'on'); ?> id="<?php echo $this->get_field_id('show_daily_avg'); ?>" name="<?php echo $this->get_field_name('show_daily_avg'); ?>" /> 
			<label for="<?php echo $this->get_field_id('show_daily_avg'); ?>"><?php _e('Show daily avg. measures', 'netatmosphere'); ?></label>
		</p>
        <p>
			<input class="checkbox" type="checkbox" <?php checked($instance['show_map'], 'on'); ?> id="<?php echo $this->get_field_id('show_map'); ?>" name="<?php echo $this->get_field_name('show_map'); ?>" /> 
			<label for="<?php echo $this->get_field_id('show_map'); ?>"><?php _e('Show location on map', 'netatmosphere'); ?></label>
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        
		$instance['show_latest']    = filter_var ( $new_instance['show_latest'], FILTER_SANITIZE_STRING );
		$instance['show_daily_avg'] = filter_var ( $new_instance['show_daily_avg'], FILTER_SANITIZE_STRING );
		$instance['show_map']       = filter_var ( $new_instance['show_map'], FILTER_SANITIZE_STRING );
		
		return $instance;
	}
}

?>