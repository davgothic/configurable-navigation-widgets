<?php
/**
 * Current Page Walker
 *
 * @package Configurable_Navigation_Widgets
 */

namespace ConfigurableNavigationWidgets\Widgets\Walkers;

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

/**
 * Class Current_Page_Walker
 */
class Current_Page_Walker extends \Walker {

	/**
	 * Current_Page_Walker constructor.
	 */
	public function __construct() {
		$this->tree_type = 'page';
		$this->db_fields = [
			'parent' => 'post_parent',
			'id'     => 'ID',
		];
	}

	/**
	 * Outputs the beginning of the current level in the tree before elements are output.
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Optional. Depth of page. Used for padding. Default 0.
	 * @param array  $args   Optional. Arguments for outputting the next level.
	 *                       Default empty array.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$t = '';
		$n = '';
		if ( isset( $args['item_spacing'] ) && 'preserve' === $args['item_spacing'] ) {
			$t = "\t";
			$n = "\n";
		}
		$indent = str_repeat( $t, $depth );
		$output .= "{$n}{$indent}<ul class='children'>{$n}";
	}

	/**
	 * Outputs the end of the current level in the tree after elements are output.
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Optional. Depth of page. Used for padding. Default 0.
	 * @param array  $args   Optional. Arguments for outputting the end of the current level.
	 *                       Default empty array.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$t = '';
		$n = '';
		if ( isset( $args['item_spacing'] ) && 'preserve' === $args['item_spacing'] ) {
			$t = "\t";
			$n = "\n";
		}
		$indent = str_repeat( $t, $depth );
		$output .= "{$indent}</ul>{$n}";
	}

	/**
	 * Outputs the beginning of the current element in the tree.
	 *
	 * @param string  $output       Used to append additional content. Passed by reference.
	 * @param WP_Post $page         Page data object.
	 * @param int     $depth        Optional. Depth of page. Used for padding. Default 0.
	 * @param array   $args         Optional. Array of arguments. Default empty array.
	 * @param int     $current_page Optional. Page ID. Default 0.
	 */
	public function start_el( &$output, $page, $depth = 0, $args = array(), $current_page = 0 ) {
		$t         = ( isset( $args['item_spacing'] ) && 'preserve' === $args['item_spacing'] ) ? "\t" : '';
		$indent    = $depth ? str_repeat( $t, $depth ) : '';
		$css_class = array( 'page_item', 'page-item-' . $page->ID );

		if ( isset( $args['pages_with_children'][ $page->ID ] ) ) {
			$css_class[] = 'page_item_has_children';
		}

		if ( ! empty( $current_page ) ) {
			$_current_page = get_post( $current_page );
			if ( $_current_page && in_array( $page->ID, $_current_page->ancestors, true ) ) {
				$css_class[] = 'current_page_ancestor';
			}
			if ( $page->ID === $current_page ) {
				$css_class[] = 'current_page_item';
			} elseif ( $_current_page && $page->ID === $_current_page->post_parent ) {
				$css_class[] = 'current_page_parent';
			}
		} elseif ( get_option( 'page_for_posts' ) === $page->ID ) {
			$css_class[] = 'current_page_parent';
		}

		/**
		 * Filters the list of CSS classes to include with each page item in the list.
		 *
		 * @see wp_list_pages()
		 *
		 * @param array   $css_class    An array of CSS classes to be applied
		 *                              to each list item.
		 * @param WP_Post $page         Page data object.
		 * @param int     $depth        Depth of page, used for padding.
		 * @param array   $args         An array of arguments.
		 * @param int     $current_page ID of the current page.
		 */
		$css_classes = implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page ) );

		if ( '' === $page->post_title ) {
			/* translators: %d: ID of a post */
			$page->post_title = sprintf( __( '#%d (no title)' ), $page->ID );
		}

		$args['link_before'] = empty( $args['link_before'] ) ? '' : $args['link_before'];
		$args['link_after'] = empty( $args['link_after'] ) ? '' : $args['link_after'];

		$atts = array();
		$atts['href'] = get_permalink( $page->ID );

		/**
		 * Filters the HTML attributes applied to a page menu item's anchor element.
		 *
		 * @param array $atts {
		 *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
		 *
		 *     @type string $href The href attribute.
		 * }
		 * @param WP_Post $page         Page data object.
		 * @param int     $depth        Depth of page, used for padding.
		 * @param array   $args         An array of arguments.
		 * @param int     $current_page ID of the current page.
		 */
		$atts = apply_filters( 'page_menu_link_attributes', $atts, $page, $depth, $args, $current_page );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$output .= $indent . '<li class="' . $css_classes . '">';
		$output .= '<a' . $attributes . '>';
		$output .= $args['link_before'];

		if ( $args['list_item_icon'] ) {
			$output .= '<i class="fa ' . $args['list_item_icon'] . '"></i> ';
		}

		$output .= apply_filters( 'the_title', $page->post_title, $page->ID );
		$output .= $args['link_after'];
		$output .= '</a>';
	}

	/**
	 * Outputs the end of the current element in the tree.
	 *
	 * @param string  $output Used to append additional content. Passed by reference.
	 * @param WP_Post $page   Page data object. Not used.
	 * @param int     $depth  Optional. Depth of page. Default 0 (unused).
	 * @param array   $args   Optional. Array of arguments. Default empty array.
	 */
	public function end_el( &$output, $page, $depth = 0, $args = array() ) {
		$n = ( isset( $args['item_spacing'] ) && 'preserve' === $args['item_spacing'] ) ? "\n" : '';
		$output .= "</li>{$n}";
	}
}
