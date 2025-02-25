<?php
/**
 * Plugin Name: Disciple.Tools - Autolink
 * Plugin URI: https://github.com/DiscipleTools/disciple-tools-autolink
 * Description: Provides a simplified interface for managing and visualizing your Disciple.Tools network. Share access with your downstream leaders to easily build your group tree.
 * Text Domain: disciple-tools-autolink
 * Domain Path: /languages
 * Version: 2.0.0-rc.4
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

use Dotenv\Dotenv;
use DT\Autolink\CodeZone\WPSupport\Config\ConfigInterface;
use DT\Autolink\Plugin;
use DT\Autolink\Providers\ConfigServiceProvider;
use DT\Autolink\Providers\PluginServiceProvider;
use DT\Autolink\Providers\RewritesServiceProvider;
use DT\Autolink\CodeZone\WPSupport\Container\ContainerFactory;
use DT\Autolink\Services\Analytics;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

//Load dependencies

require_once plugin_dir_path( __FILE__ ) . 'vendor-scoped/scoper-autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'vendor-scoped/autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

// Load environmental variables.
Dotenv::createUnsafeImmutable( __DIR__ )->load();

// Create the IOC container
$container = ContainerFactory::singleton();

require_once plugin_dir_path( __FILE__ ) . 'src/helpers.php';

// Add any services providers required to init the plugin
$boot_providers = [
    ConfigServiceProvider::class,
    RewritesServiceProvider::class,
    PluginServiceProvider::class
];

foreach ( $boot_providers as $provider ) {
    $container->addServiceProvider( $container->get( $provider ) );
}

// Init the plugin
$dt_autolink = $container->get( Plugin::class );
$dt_autolink->init();

$container->get( Analytics::class )->init();

// Add the rest of the service providers
$config = $container->get( ConfigInterface::class );
foreach ( $config->get( 'services.providers' ) as $provider ) {
    $container->addServiceProvider( $container->get( $provider ) );
}
