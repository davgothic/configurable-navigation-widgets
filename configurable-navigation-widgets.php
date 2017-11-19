<?php
/**
 * Plugin Name: Configurable Navigation Widgets
 * Plugin URI: https://www.makedo.net/
 * Description:
 * Version: 1.0
 * Author: MKDO
 * Author URI: https://www.makedo.net/
 * License: GPLv2 or later
 * Text Domain: cnw
 *
 * @package Navigation_Widgets
 */

use ConfigurableNavigationWidgets\Configurable_Navigation_Widgets;

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

define( 'CNW_VERSION', '1.0.0-pre' );

require_once __DIR__ . '/includes/class-configurable-navigation-widgets.php';

/**
 * Fire up the plugin
 */
(new Configurable_Navigation_Widgets())->run();
