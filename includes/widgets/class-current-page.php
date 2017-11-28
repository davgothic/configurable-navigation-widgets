<?php
/**
 * Current Page
 *
 * @package Configurable_Navigation_Widgets
 */

namespace ConfigurableNavigationWidgets\Widgets;

use ConfigurableNavigationWidgets\Widgets\Walkers\Current_Page_Walker;

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

/**
 * Class Current_Page
 */
class Current_Page extends Page_Base {

	/**
	 * Construct a new instance of the widget
	 */
	public function __construct() {
		$widget_options = [
			'classname'   => 'Current_Page',
			'description' => __( 'Displays a menu with ancestors and children of the current page.', 'cnw' ),
		];

		parent::__construct( 'Current_Page', __( 'CNW - Current Page', 'cnw' ), $widget_options );

		// Set up the widget defaults.
		$this->defaults = array(
			'title'                           => __( 'Navigation', 'cnw' ),
			'sortby'                          => 'menu_order',
			'exclude'                         => '',
			'include_parent_item'             => true,
			'levels_deep'                     => 5,
			'show_current_tree_children_only' => false,
			'mobile_friendly'                 => true,
			'fixed_positioning'               => false,
			'height_aware'                    => false,
			'list_item_icon'                  => '-',
			'highlight_options'               => '',
		);
	}

	/**
	 * Display the widget
	 *
	 * @param array $widget_args     Display arguments including 'before_title', 'after_title',
	 *                               'before_widget', and 'after_widget'.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $widget_args, $instance ) {
		// If we cant retrieve the current page/post object then we're done here.
		$queried_object = get_queried_object();
		if ( ! $queried_object ) {
			return;
		}

		$post_id = $queried_object->ID;

		$title   = $this->get_title( $instance );
		$sortby  = $this->get_sortby( $instance );
		$exclude = $this->get_exclude( $instance );

		// @todo: Add include_parent_item functionality.
		$include_parent_item             = empty( $instance['include_parent_item'] ) ? $this->defaults['include_parent_item'] : 'on' === $instance['include_parent_item'];
		$levels_deep                     = empty( $instance['levels_deep'] ) ? $this->defaults['levels_deep'] : (int) $instance['levels_deep'];
		$show_current_tree_children_only = empty( $instance['show_current_tree_children_only'] ) ? $this->defaults['show_current_tree_children_only'] : 'on' === $instance['show_current_tree_children_only'];
		$mobile_friendly                 = empty( $instance['mobile_friendly'] ) ? $this->defaults['mobile_friendly'] : 'on' === $instance['mobile_friendly'];
		$fixed_positioning               = empty( $instance['fixed_positioning'] ) ? $this->defaults['fixed_positioning'] : 'on' === $instance['fixed_positioning'];

		echo $widget_args['before_widget'];

		echo $widget_args['before_title'], $title, $widget_args['after_title'];

		// @todo: Consider the logic for here.
		$args = array(
			'depth'        => 0,
			'exclude'      => '',
			'sort_column'  => $sortby,
			'link_before'  => '',
			'link_after'   => '',
			'item_spacing' => 'preserve',
			'walker'       => new Current_Page_Walker(),
		);

		// Get all page objects.
		$tmp_pages = get_pages( $args );

		if ( true === $show_current_tree_children_only ) {
			$queried_object_root_id = ( 0 !== $queried_object->post_parent )
				? end( $queried_object->ancestors )
				: $queried_object->ID;

			$queried_object_root_children = get_page_children( $queried_object_root_id, $tmp_pages );
			$queried_object_root_children = array_map( function ( $n ) {
				return $n->ID;
			}, $queried_object_root_children );

			$pages = [];
			foreach ( $tmp_pages as $key => $page ) {
				if (
					0 === $page->post_parent
					|| in_array( $page->ID, $queried_object_root_children, true )
				) {
					$pages[] = $page;
				}
			}
		} else {
			$pages = $tmp_pages;
		}

		// sanitize, mostly to keep spaces out.
		$args['exclude']      = preg_replace( '/[^0-9,]/', '', $exclude );
		$args['exclude']      = apply_filters( 'cnw_excludes', $args['exclude'] );
		$args['exclude']      = $args['exclude'] ? explode( ',', $args['exclude'] ) : [];
		$args['hierarchical'] = 0;

		$output = walk_page_tree( $pages, $levels_deep, $post_id, $args );

		$classes = [ 'cnw-navigation' ];

		if ( $mobile_friendly ) {
			$classes[] = 'cnw-mobile-fiendly';
		}

		if ( $fixed_positioning ) {
			$classes[] = 'cnw-fixed_positioning';
		}

		echo '<ul class="' . esc_attr( implode( ' ', $classes ) ) . '">';
		echo $output;
		echo '</ul>';

		echo $widget_args['after_widget'];
	}

	/**
	 * Handle widget settings update
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$this->update_title( $new_instance, $instance );
		$this->update_sortby( $new_instance, $instance );
		$this->update_exclude( $new_instance, $instance );

		$instance['include_parent_item'] = $new_instance['include_parent_item'];

		if ( in_array( (int) $new_instance['levels_deep'], range( 1, 5 ), true ) ) {
			$instance['levels_deep'] = $new_instance['levels_deep'];
		} else {
			$instance['levels_deep'] = $this->defaults['levels_deep'];
		}

		$instance['show_current_tree_children_only'] = 'on' === $new_instance['show_current_tree_children_only'] ? 'on' : 'off';
		$instance['mobile_friendly']                 = 'on' === $new_instance['mobile_friendly'] ? 'on' : 'off';
		$instance['fixed_positioning']               = 'on' === $new_instance['fixed_positioning'] ? 'on' : 'off';

		return $instance;
	}

	/**
	 * The widget settings form
	 *
	 * @param array $instance Current settings.
	 *
	 * @return string|void
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );

		$this->form_title( $instance );
		$this->form_sortby( $instance );
		$this->form_exclude( $instance );

		?>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['include_parent_item'], 'on' ); ?> id="<?php echo $this->get_field_id( 'include_parent_item' ); ?>" name="<?php echo $this->get_field_name( 'include_parent_item' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'include_parent_item' ); ?>">Include parent item?</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'levels_deep' ) ); ?>"><?php _e( 'Levels deep:' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'levels_deep' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'levels_deep' ) ); ?>" class="widefat">
				<?php foreach ( range( 1, 5 ) as $i ) : ?>
					<option value="<?php echo $i; ?>"<?php selected( $instance['levels_deep'], $i ); ?>><?php echo $i; ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_current_tree_children_only'], 'on' ); ?> id="<?php echo $this->get_field_id( 'show_current_tree_children_only' ); ?>" name="<?php echo $this->get_field_name( 'show_current_tree_children_only' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_current_tree_children_only' ); ?>">Show current tree children only?</label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['mobile_friendly'], 'on' ); ?> id="<?php echo $this->get_field_id( 'mobile_friendly' ); ?>" name="<?php echo $this->get_field_name( 'mobile_friendly' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'mobile_friendly' ); ?>">Mobile friendly?</label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['fixed_positioning'], 'on' ); ?> id="<?php echo $this->get_field_id( 'fixed_positioning' ); ?>" name="<?php echo $this->get_field_name( 'fixed_positioning' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'fixed_positioning' ); ?>">Fixed positioning?</label>
		</p>
		<?php
	}

}
