<?php
/**
 * Current Page
 *
 * @package Configurable_Navigation_Widgets
 */

namespace ConfigurableNavigationWidgets\Widgets;

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
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {
		// If we cant retrieve the current page/post object then we're done here.
		$queried_object = get_queried_object();
		if ( ! $queried_object ) {
			return;
		}

		$post_id = $queried_object->ID;

		$title   = $this->get_title( $instance );
		$sortby  = $this->get_sortby( $instance );
		$exclude = $this->get_exclude( $instance );

		echo $args['before_widget'];

		echo $args['before_title'], $title, $args['after_title'];


        // @todo: Consider the logic for here.


		echo $args['after_widget'];
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
	}

}
