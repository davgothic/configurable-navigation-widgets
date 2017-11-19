<?php
/**
 * Configurable Navigation Widgets
 *
 * @package Configurable_Navigation_Widgets
 */

namespace ConfigurableNavigationWidgets;

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

/**
 * Class Configurable_Navigation_Widgets
 */
class Configurable_Navigation_Widgets {

	/**
	 * The plugin version.
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * Initialise the plugin.
	 */
	public function __construct() {
		$this->version = CNW_VERSION;

		$this->load_plugin_textdomain();
		$this->load_dependencies();
	}

	/**
	 * Load the plugin text domain for translation.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'cnw',
			false,
			__DIR__ . '/../languages/'
		);
	}

	/**
	 * Load required dependencies
	 */
	public function load_dependencies() {
		require_once __DIR__ . '/widgets/class-base.php';
		require_once __DIR__ . '/widgets/class-page-base.php';
		require_once __DIR__ . '/widgets/class-current-page.php';
	}

	/**
	 * Run the plugin hooks.
	 */
	public function run() {
		if ( false === $this->compatibility_check() ) {
			return;
		}

		add_action( 'widgets_init', [ $this, 'widgets_init' ] );
	}

	/**
	 * Check the environment for compatibility.
	 *
	 * @return bool Whether the environment is compatible or not.
	 */
	public function compatibility_check() {
		if ( PHP_VERSION_ID < 50400 ) {
			add_action( 'admin_notices', function() {
				$message = __(
					'Navigation Widgets requires PHP 5.4. Please upgrade PHP or deactivate the plugin.',
					'cnw'
				);
				echo '<div class="error"><p>' . esc_html( $message ) . '</p></div>';
			} );

			return false;
		}

		return true;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Initialise widgets.
	 */
	public static function widgets_init() {
		register_widget( Widgets\Current_Page::class );
	}

}
