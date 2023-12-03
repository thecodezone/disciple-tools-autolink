<?php

namespace DT\Plugin\Providers;

use DT\Plugin\Services\Router;

class RouteServiceProvider extends ServiceProvider {

	public function register(): void {
		// TODO: Implement register() method.
	}

	/**
	 * @return void
	 */
	public function boot(): void {
		$this->registerWebRoutes();
		$this->registerRestRoutes();
	}

	/**
	 * Register web-based routes
	 *
	 * @return void
	 */
	public function registerWebRoutes(): void {
		$router = $this->container->make( Router::class );
		$router->register_file( 'web.php' );
	}

	/**
	 * @return void
	 */
	public function registerRestRoutes() {
		add_action( 'rest_api_init', function () {
			require_once __DIR__ . '/../../routes/rest.php';
		} );
	}
}
