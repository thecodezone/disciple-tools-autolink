<?php
/**
 * Plugin Name: Disciple.Tools - Autolink
 * Plugin URI: https://github.com/DiscipleTools/disciple-tools-autolink
 * Description: Provides a simplified interface for managing and visualizing your Disciple.Tools network. Share access with your downstream leaders to easily build your group tree.
 * Text Domain: disciple-tools-autolink
 * Domain Path: /languages
 * Version:  1.0.11
 * Author URI: https://www.eastwest.org/
 * GitHub Plugin URI: https://github.com/thecodezone/disciple-tools-autolink
 * Requires at least 4.7.0
 * (Requires 4.7+ because of the integration of the REST API at 4.7 and the security requirements of this milestone version.)
 * Tested up to: 5.6
 *
 * @package Disciple_Tools
 * @link    https://github.com/DiscipleTools
 * @license GPL-2.0 or later
 *          https://www.gnu.org/licenses/gpl-2.0.html
 */

use DT\Autolink\Illuminate\Container\Container;
use DT\Autolink\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once plugin_dir_path( __FILE__ ) . '/vendor-scoped/scoper-autoload.php';
require_once plugin_dir_path( __FILE__ ) . '/vendor-scoped/autoload.php';
require_once plugin_dir_path( __FILE__ ) . '/vendor/autoload.php';

$container = new Container();
$container->singleton( Container::class, function ( $container ) {
	return $container;
} );
$container->singleton( Plugin::class, function ( $container ) {
	return new Plugin( $container );
} );
$plugin_instance = $container->make( Plugin::class );
$plugin_instance->init();
