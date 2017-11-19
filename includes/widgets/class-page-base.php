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
class Page_Base extends Base {

	/**
	 * Retrieve the sortby value.
	 *
	 * @param array $instance The settings for the particular instance of the widget.
	 *
	 * @return string
	 */
	public function get_sortby( $instance ) {
		return empty( $instance['sortby'] ) ? $this->defaults['sortby'] : $instance['sortby'];
	}

	/**
	 * Retrieve the exclude value.
	 *
	 * @param array $instance The settings for the particular instance of the widget.
	 *
	 * @return string
	 */
	public function get_exclude( $instance ) {
		return empty( $instance['exclude'] ) ? $this->defaults['exclude'] : $instance['exclude'];
	}

	/**
	 * Update the current instance sortby.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $instance Current settings for this instance.
	 */
	public function update_sortby( $new_instance, &$instance ) {
		if ( in_array( $new_instance['sortby'], [ 'post_title', 'menu_order', 'ID' ], true ) ) {
			$instance['sortby'] = $new_instance['sortby'];
		} else {
			$instance['sortby'] = 'menu_order';
		}
	}

	/**
	 * Update the current instance exclude.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $instance Current settings for this instance.
	 */
	public function update_exclude( $new_instance, &$instance ) {
		$instance['exclude'] = sanitize_text_field( $new_instance['exclude'] );
	}

	/**
	 * Output the sortby form fields.
	 *
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function form_sortby( $instance ) {
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'sortby' ) ); ?>"><?php _e( 'Sort by:' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'sortby' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'sortby' ) ); ?>" class="widefat">
				<option value="post_title"<?php selected( $instance['sortby'], 'post_title' ); ?>><?php _e('Page title'); ?></option>
				<option value="menu_order"<?php selected( $instance['sortby'], 'menu_order' ); ?>><?php _e('Page order'); ?></option>
				<option value="ID"<?php selected( $instance['sortby'], 'ID' ); ?>><?php _e( 'Page ID' ); ?></option>
			</select>
		</p>
		<?php
	}

	/**
	 * Output the exclude form fields.
	 *
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function form_exclude( $instance ) {
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'exclude' ) ); ?>"><?php _e( 'Exclude:' ); ?></label>
			<input type="text" value="<?php echo esc_attr( $instance['exclude'] ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'exclude' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'exclude' ) ); ?>" class="widefat" />
			<br />
			<small><?php _e( 'Page IDs, separated by commas.' ); ?></small>
		</p>
		<?php
	}
}
