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

	/**
	 * Retrieve the levels_deep value.
	 *
	 * @param array $instance The settings for the particular instance of the widget.
	 *
	 * @return string
	 */
	public function get_levels_deep( $instance ) {
		$levels_deep = empty( $instance['levels_deep'] ) ? $this->defaults['levels_deep'] : (int) $instance['levels_deep'];

		return $levels_deep;
	}

	/**
	 * Update the current instance levels_deep.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $instance Current settings for this instance.
	 */
	public function update_levels_deep( $new_instance, &$instance ) {
		if ( in_array( (int) $new_instance['levels_deep'], range( 1, 5 ), true ) ) {
			$instance['levels_deep'] = $new_instance['levels_deep'];
		} else {
			$instance['levels_deep'] = $this->defaults['levels_deep'];
		}
	}

	/**
	 * Output the levels_deep form fields.
	 *
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function form_levels_deep( $instance ) {
		?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'levels_deep' ) ); ?>"><?php _e( 'Levels deep:' ); ?></label>
            <select name="<?php echo esc_attr( $this->get_field_name( 'levels_deep' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'levels_deep' ) ); ?>" class="widefat">
				<?php foreach ( range( 1, 5 ) as $i ) : ?>
                    <option value="<?php echo $i; ?>"<?php selected( $instance['levels_deep'], $i ); ?>><?php echo $i; ?></option>
				<?php endforeach; ?>
            </select>
        </p>
		<?php
	}

	/**
	 * Retrieve the include_parent_item value.
	 *
	 * @param array $instance The settings for the particular instance of the widget.
	 *
	 * @return string
	 */
	public function get_include_parent_item( $instance ) {
		$include_parent_item = empty( $instance['include_parent_item'] ) ? $this->defaults['include_parent_item'] : 'on' === $instance['include_parent_item'];

		return $include_parent_item;
	}

	/**
	 * Update the current instance include_parent_item.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $instance Current settings for this instance.
	 */
	public function update_include_parent_item( $new_instance, &$instance ) {
		$instance['include_parent_item'] = $new_instance['include_parent_item'];
	}

	/**
	 * Output the include_parent_item form fields.
	 *
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function form_include_parent_item( $instance ) {
        ?>
        <p>
            <input class="checkbox" type="checkbox" <?php checked( $instance['include_parent_item'], 'on' ); ?> id="<?php echo $this->get_field_id( 'include_parent_item' ); ?>" name="<?php echo $this->get_field_name( 'include_parent_item' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'include_parent_item' ); ?>">Include parent item?</label>
        </p>
        <?php
	}

	/**
	 * Retrieve the show_current_tree_children_only value.
	 *
	 * @param array $instance The settings for the particular instance of the widget.
	 *
	 * @return string
	 */
	public function get_show_current_tree_children_only( $instance ) {
		$show_current_tree_children_only = empty( $instance['show_current_tree_children_only'] ) ? $this->defaults['show_current_tree_children_only'] : 'on' === $instance['show_current_tree_children_only'];

		return $show_current_tree_children_only;
	}

	/**
	 * Update the current instance show_current_tree_children_only.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $instance Current settings for this instance.
	 */
	public function update_show_current_tree_children_only( $new_instance, &$instance ) {
		$instance['show_current_tree_children_only'] = 'on' === $new_instance['show_current_tree_children_only'] ? 'on' : 'off';
	}

	/**
	 * Output the show_current_tree_children_only form fields.
	 *
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function form_show_current_tree_children_only( $instance ) {
        ?>
        <p>
            <input class="checkbox" type="checkbox" <?php checked( $instance['show_current_tree_children_only'], 'on' ); ?> id="<?php echo $this->get_field_id( 'show_current_tree_children_only' ); ?>" name="<?php echo $this->get_field_name( 'show_current_tree_children_only' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'show_current_tree_children_only' ); ?>">Show current tree children only?</label>
        </p>
        <?php
	}

	/**
	 * Retrieve the mobile_friendly value.
	 *
	 * @param array $instance The settings for the particular instance of the widget.
	 *
	 * @return string
	 */
	public function get_mobile_friendly( $instance ) {
		$mobile_friendly = empty( $instance['mobile_friendly'] ) ? $this->defaults['mobile_friendly'] : 'on' === $instance['mobile_friendly'];

		return $mobile_friendly;
	}

	/**
	 * Update the current instance mobile_friendly.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $instance Current settings for this instance.
	 */
	public function update_mobile_friendly( $new_instance, &$instance ) {
		$instance['mobile_friendly'] = 'on' === $new_instance['mobile_friendly'] ? 'on' : 'off';
	}

	/**
	 * Output the mobile_friendly form fields.
	 *
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function form_mobile_friendly( $instance ) {
		?>
        <p>
            <input class="checkbox" type="checkbox" <?php checked( $instance['mobile_friendly'], 'on' ); ?> id="<?php echo $this->get_field_id( 'mobile_friendly' ); ?>" name="<?php echo $this->get_field_name( 'mobile_friendly' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'mobile_friendly' ); ?>">Mobile friendly?</label>
        </p>
		<?php
	}

	/**
	 * Retrieve the fixed_positioning value.
	 *
	 * @param array $instance The settings for the particular instance of the widget.
	 *
	 * @return string
	 */
	public function get_fixed_positioning( $instance ) {
		$fixed_positioning = empty( $instance['fixed_positioning'] ) ? $this->defaults['fixed_positioning'] : 'on' === $instance['fixed_positioning'];

		return $fixed_positioning;
	}

	/**
	 * Update the current instance fixed_positioning.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $instance Current settings for this instance.
	 */
	public function update_fixed_positioning( $new_instance, &$instance ) {
		$instance['fixed_positioning'] = 'on' === $new_instance['fixed_positioning'] ? 'on' : 'off';
	}

	/**
	 * Output the fixed_positioning form fields.
	 *
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function form_fixed_positioning( $instance ) {
		?>
        <p>
            <input class="checkbox" type="checkbox" <?php checked( $instance['fixed_positioning'], 'on' ); ?> id="<?php echo $this->get_field_id( 'fixed_positioning' ); ?>" name="<?php echo $this->get_field_name( 'fixed_positioning' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'fixed_positioning' ); ?>">Fixed positioning?</label>
        </p>
		<?php
	}

	/**
	 * Retrieve the height_aware value.
	 *
	 * @param array $instance The settings for the particular instance of the widget.
	 *
	 * @return string
	 */
	public function get_height_aware( $instance ) {
		$height_aware = empty( $instance['height_aware'] ) ? $this->defaults['height_aware'] : 'on' === $instance['height_aware'];

		return $height_aware;
	}

	/**
	 * Update the current instance height_aware.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $instance Current settings for this instance.
	 */
	public function update_height_aware( $new_instance, &$instance ) {
		$instance['height_aware'] = 'on' === $new_instance['height_aware'] ? 'on' : 'off';
	}

	/**
	 * Output the height_aware form fields.
	 *
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function form_height_aware( $instance ) {
		?>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['height_aware'], 'on' ); ?> id="<?php echo $this->get_field_id( 'height_aware' ); ?>" name="<?php echo $this->get_field_name( 'height_aware' ); ?>"/>
			<label for="<?php echo $this->get_field_id( 'height_aware' ); ?>">Height aware?</label>
		</p>
		<?php
	}

}
