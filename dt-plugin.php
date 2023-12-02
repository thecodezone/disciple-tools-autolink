<?php
/**
 * Plugin Name: Disciple.Tools - Plugin
 * Plugin URI: https://github.com/TheCodeZone/dt-plugin
 * Description: A modern disciple.tools plugin starter template.
 * Text Domain: dt-plugin
 * Domain Path: /languages
 * Version:  0.1
 * Author URI: https://github.com/TheCodeZone
 * GitHub Plugin URI: https://github.com/TheCodeZone/dt-plugin
 * Requires at least: 4.7.0
 * (Requires 4.7+ because of the integration of the REST API at 4.7 and the security requirements of this milestone version.)
 * Tested up to: 5.6
 *
 * @package Disciple_Tools
 * @link    https://github.com/DiscipleTools
 * @license GPL-2.0 or later
 *          https://www.gnu.org/licenses/gpl-2.0.html
 */

use CZ\Illuminate\Support\Facades\App;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once __DIR__ . '/vendor-scoped/scoper-autoload.php';
require_once __DIR__ . '/vendor-scoped/autoload.php';
require_once __DIR__ . '/vendor/autoload.php';

$container = \CZ\Illuminate\Container\Container::getInstance();
$container->singleton(\CZ\Plugin\Plugin::class, function ( $container ) {
	return new \CZ\Plugin\Plugin(
		$container,
		$container->make( \CZ\Plugin\Providers\PluginServiceProvider::class )
	);
});

$container->make( \CZ\Plugin\Plugin::class );
