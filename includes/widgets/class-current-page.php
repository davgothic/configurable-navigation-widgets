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
			'highlight_options'               => '000',
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

		// @todo: Add include_parent_item functionality.
		// @todo: Add highlight_options functionality.
		$title                           = $this->get_title( $instance );
		$sortby                          = $this->get_sortby( $instance );
		$exclude                         = $this->get_exclude( $instance );
		$levels_deep                     = $this->get_levels_deep( $instance );
		$include_parent_item             = $this->get_include_parent_item( $instance );
		$show_current_tree_children_only = $this->get_show_current_tree_children_only( $instance );
		$mobile_friendly                 = $this->get_mobile_friendly( $instance );
		$fixed_positioning               = $this->get_fixed_positioning( $instance );
		$height_aware                    = $this->get_height_aware( $instance );
		$list_item_icon                  = $this->get_list_item_icon( $instance );
		$highlight_options               = $this->get_highlight_options( $instance );

		echo $widget_args['before_widget'];

		echo $widget_args['before_title'], $title, $widget_args['after_title'];

		// @todo: Consider the logic for here.
		$args = array(
			'depth'          => 0,
			'exclude'        => '',
			'sort_column'    => $sortby,
			'link_before'    => '',
			'link_after'     => '',
			'item_spacing'   => 'preserve',
			'list_item_icon' => $list_item_icon,
			'walker'         => new Current_Page_Walker(),
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

		if ( $height_aware ) {
			$classes[] = 'cnw-height_aware';
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
		$this->update_include_parent_item( $new_instance, $instance );
		$this->update_show_current_tree_children_only( $new_instance, $instance );
		$this->update_mobile_friendly( $new_instance, $instance );
		$this->update_fixed_positioning( $new_instance, $instance );
		$this->update_height_aware( $new_instance, $instance );
		$this->update_list_item_icon( $new_instance, $instance );
		$this->update_highlight_options( $new_instance, $instance );

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
		$this->form_levels_deep( $instance );
		$this->form_include_parent_item( $instance );
		$this->form_show_current_tree_children_only( $instance );
		$this->form_mobile_friendly( $instance );
		$this->form_fixed_positioning( $instance );
		$this->form_height_aware( $instance );
		$this->form_list_item_icon( $instance );
		$this->form_highlight_options( $instance );
	}

}
