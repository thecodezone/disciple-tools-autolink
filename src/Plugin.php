<?php

namespace CZ\Plugin;

use CZ\Illuminate\Container\Container;
use CZ\Illuminate\Support\Facades\App;
use CZ\Illuminate\Support\Facades\Config;
use CZ\Plugin\Providers\PluginServiceProvider;
use CZ\Roots\Acorn\Bootloader;
use CZ\Roots\Acorn\Bootstrap\LoadConfiguration;
use Safe\Exceptions\LdapException;
use function CZ\Roots\bootloader;

class Plugin {
	const required_dt_version = '1.19';

	/**
	 * Dependency injection container
	 * @var Container
	 */
	protected $container;
	protected $application;
	protected $bootloader;

	public function __construct(Container $container, PluginServiceProvider $provider) {
		$this->container = $container;
		$this->provider = $provider;

		add_action( 'after_setup_theme', [ $this, 'after_setup_theme' ], 20 );
		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ] );
		add_filter( 'dt_plugins', [ $this, 'dt_plugins' ] );

		$this->provider->register();
	}

	/**
	 * Is the DT Theme installed?
	 * @return bool
	 */
	protected function is_dt_theme(): bool {
		return class_exists( 'Disciple_Tools' );
	}

	/**
	 * is DT up-to-date?
	 * @return bool
	 */
	public function is_dt_version(): bool {
		if ( ! $this->is_dt_theme() ) {
			return false;
		}
		$wp_theme = wp_get_theme();

		return version_compare( $wp_theme->version, self::required_dt_version, '>=' );
	}

	/**
	 * Runs after_theme_setup
	 * @return void
	 */
	public function after_setup_theme(): void {
		if ( ! $this->is_dt_version() ) {
			add_action( 'admin_notices', [ $this, 'admin_notices' ] );
			add_action( 'wp_ajax_dismissed_notice_handler', [ $this, 'ajax_notice_handler' ] );

			return;
		}

		if ( ! $this->is_dt_theme() ) {
			return;
		}

		if ( ! defined( 'DT_FUNCTIONS_READY' ) ) {
			require_once get_template_directory() . '/dt-core/global-functions.php';
		}

		$this->provider->boot();
	}

	/**
	 * Register the plugin
	 * @return array
	 */
	public function dt_plugins(): array {
		$plugin_data = get_file_data( __FILE__, [ 'Version' => 'Version', 'Plugin Name' => 'Plugin Name' ], false );
		$plugins['disciple-tools-plugin-starter-template'] = [
			'plugin_url' => trailingslashit( plugin_dir_url( __FILE__ ) ),
			'version' => $plugin_data['Version'] ?? null,
			'name' => $plugin_data['Plugin Name'] ?? null,
		];
		return $plugins;
	}

	public function wp_enqueue_scripts(): void {
		\CZ\Kucrut\Vite\enqueue_asset(
			__DIR__ . '/../dist',
			'resources/js/plugin.js',
			[
				'handle' => 'cz-plugin',
				'css-media' => 'all', // Optional.
				'css-only' => false, // Optional. Set to true to only load style assets in production mode.
				'in-footer' => true, // Optional. Defaults to false.
			]
		);
	}
}
