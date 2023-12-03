<?php

namespace DT\Plugin;

use DT\Plugin\Illuminate\Container\Container;
use DT\Plugin\Illuminate\Support\Str;
use DT\Plugin\Providers\PluginServiceProvider;
use function DT\Plugin\Kucrut\Vite\enqueue_asset;

class Plugin {
	const REQUIRED_PHP_VERSION = '1.19';
	public $base_path;
	public $src_path;
	public $resources_path;
	public $routes_path;
	public $templates_path;
	protected $container;
	protected $application;

	public function __construct( Container $container, PluginServiceProvider $provider ) {
		$this->container = $container;
		$this->provider  = $provider;

		$this->base_path      = '/' . trim( Str::remove( '/src', plugin_dir_path( __FILE__ ) ), '/' );
		$this->src_path       = $this->base_path . '/src';
		$this->resources_path = $this->base_path . '/resources';
		$this->routes_path    = $this->base_path . '/routes';
		$this->templates_path = $this->base_path . '/resources/templates';

		add_action( 'after_setup_theme', [ $this, 'after_setup_theme' ], 20 );
		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ] );
		add_filter( 'dt_plugins', [ $this, 'dt_plugins' ] );

		$this->provider->register();
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
	 * is DT up-to-date?
	 * @return bool
	 */
	public function is_dt_version(): bool {
		if ( ! $this->is_dt_theme() ) {
			return false;
		}
		$wp_theme = wp_get_theme();

		return version_compare( $wp_theme->version, self::REQUIRED_PHP_VERSION, '>=' );
	}

	/**
	 * Is the DT Theme installed?
	 * @return bool
	 */
	protected function is_dt_theme(): bool {
		return class_exists( 'Disciple_Tools' );
	}

	/**
	 * Register the plugin
	 * @return array
	 */
	public function dt_plugins(): array {
		$plugin_data = get_file_data( __FILE__, [
			'Version'     => 'Version',
			'Plugin Name' => 'Plugin Name'
		], false );

		$plugins['disciple-tools-plugin-starter-template'] = [
			'plugin_url' => trailingslashit( plugin_dir_url( __FILE__ ) ),
			'version'    => $plugin_data['Version'] ?? null,
			'name'       => $plugin_data['Plugin Name'] ?? null,
		];

		return $plugins;
	}

	public function wp_enqueue_scripts(): void {
		enqueue_asset(
			__DIR__ . '/../dist',
			'resources/js/plugin.js',
			[
				'handle'    => 'dt_plugin',
				'css-media' => 'all', // Optional.
				'css-only'  => false, // Optional. Set to true to only load style assets in production mode.
				'in-footer' => true, // Optional. Defaults to false.
			]
		);
	}
}
