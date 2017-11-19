<?php
/**
 * Base
 *
 * @package Configurable_Navigation_Widgets
 */

namespace ConfigurableNavigationWidgets\Widgets;

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

/**
 * Class Base
 */
class Base extends \WP_Widget {

	/**
	 * The default widget options.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Retrieve the title value.
	 *
	 * @param array $instance The settings for the particular instance of the widget.
	 *
	 * @return string
	 */
	public function get_title( $instance ) {
		$title = empty( $instance['title'] ) ? $this->defaults['title'] : $instance['title'];
		$title = apply_filters( 'widget_title', $title );

		return $title;
	}

	/**
	 * Update the current instance title.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $instance Current settings for this instance.
	 */
	public function update_title( $new_instance, &$instance ) {
		$instance['title'] = strip_tags( $new_instance['title'] );
	}

	/**
	 * Output the title form fields.
	 *
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function form_title( $instance ) {
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>"/>
		</p>
		<?php
	}
}
