<?php

namespace DT\Plugin\Providers;

use DT\Plugin\Services\Router;
use DT\Plugin\Services\Template;
use function DT\Plugin\plugin;

class RouteServiceProvider extends ServiceProvider {

	public function register(): void {
	}

	public function boot(): void {
		add_action( 'rest_api_init', [ $this, 'registerRestRoutes' ], 1 );
		$this->registerWebRoutes();
	}

	/**
	 * Register web-based routes
	 *
	 * @return void
	 */
	public function registerWebRoutes(): void {
		$router = $this->container->make( Router::class );
		$router->from_file( 'web/routes.php' );

		if ( $router->is_match() ) {
			$template = $this->container->make( Template::class );
			$template->make(
				function () use ( $router ) {
					$router->make();
				}
			);
		}
	}

	/**
	 * @return void
	 */
	public function registerRestRoutes() {
		require_once plugin()->routes_path . '/rest/routes.php';
	}
}
