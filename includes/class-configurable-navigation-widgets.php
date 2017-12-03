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
		require_once __DIR__ . '/widgets/walkers/class-current-page-walker.php';
	}

	/**
	 * Run the plugin hooks.
	 */
	public function run() {
		if ( false === $this->compatibility_check() ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_menu', [ $this, 'register_options_page' ] );
		add_action( 'widgets_init', [ $this, 'widgets_init' ] );
	}

	/**
	 * Enqueue admin stylesheets and scripts.
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'font-awesome', CNW_DIR_URL . 'css/font-awesome.min.css' );
		wp_enqueue_style( 'select2', CNW_DIR_URL . 'css/select2.min.css' );
		wp_enqueue_script( 'select2', CNW_DIR_URL . 'js/select2.min.js', [ 'jquery' ] );
		wp_enqueue_script( 'cnw-admin', CNW_DIR_URL . 'js/admin.js', [ 'jquery', 'select2' ] );
	}

	/**
	 * Enqueue stylesheets and scripts.
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'font-awesome', CNW_DIR_URL . 'css/font-awesome.min.css' );
	}

	/**
	 * Register plugin settings.
	 */
	public function register_settings() {
		register_setting( 'cnw', 'cnw_icon_collection' );
		register_setting( 'cnw', 'cnw_mobile_friendly_breakpoint' );

		add_settings_section(
			'default',
			'',
			'__return_null',
			'cnw'
		);

		add_settings_field(
			'cnw_icon_collection',
			__( 'Icon Collection', 'cnw' ),
			[ $this, 'field_icon_collection_cb' ],
			'cnw',
			'default',
			[
				'label_for' => 'cnw_icon_collection',
			]
		);

		add_settings_field(
			'cnw_mobile_friendly_breakpoint',
			__( 'Mobile Friendly Breakpoint', 'cnw' ),
			[ $this, 'field_mobile_friendly_breakpoint_cb' ],
			'cnw',
			'default',
			[
				'label_for' => 'cnw_mobile_friendly_breakpoint',
			]
		);
	}

	/**
	 * Render icon collection field.
	 *
	 * @param array $args Field arguments.
	 */
	public function field_icon_collection_cb( $args ) {
		$option = get_option( 'cnw_icon_collection' );
		?>
		<select id="<?php echo esc_attr( $args['label_for'] ); ?>"
				name="cnw_icon_collection"
		>
			<option value="font-awesome" <?php selected( $option, 'font-awesome' ); ?>>
				<?php esc_html_e( 'FontAwesome', 'cnw' ); ?>
			</option>
		</select>
		<p class="description">
			<?php esc_html_e( 'Choose the icon collection you would like to use for navigation item icons.', 'cnw' ); ?>
		</p>
		<?php
	}

	/**
	 * Render mobile friendly breakpoint field.
	 *
	 * @param array $args Field arguments.
	 */
	public function field_mobile_friendly_breakpoint_cb( $args ) {
		$option = get_option( 'cnw_mobile_friendly_breakpoint' );
		?>
		<input id="<?php echo esc_attr( $args['label_for'] ); ?>"
				name="cnw_mobile_friendly_breakpoint"
				type="text"
				class="small-text"
				value="<?php echo esc_attr( $option ); ?>">
		<p class="description">
			<?php esc_html_e( 'A pixel value to determine at what breakpoint the widget changes to mobile mode.', 'cnw' ); ?>
		</p>
		<?php
	}

	/**
	 * Register options page.
	 */
	public function register_options_page() {
		add_options_page(
			'Configurable Navigation Widgets',
			'Configurable Navigation Widgets',
			'manage_options',
			'cnw',
			[ $this, 'options_page' ]
		);
	}

	/**
	 * Options page markup.
	 */
	public function options_page() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'cnw' ); ?>
				<?php do_settings_sections( 'cnw' ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
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
