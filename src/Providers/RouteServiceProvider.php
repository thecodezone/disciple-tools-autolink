<?php

namespace DT\Plugin\Providers;

use DT\Plugin\FastRoute\RouteCollector;
use DT\Plugin\Middleware\Stack;
use DT\Plugin\Services\Router\Routes;
use function DT\Plugin\routes_path;

class RouteServiceProvider extends ServiceProvider {
	/**
	 * Do any setup needed before the theme is ready.
	 * DT is not yet registered.
	 */
	public function register(): void {
		add_filter( "dt/plugin/routes", [ $this, 'include_route_file' ], 1 );
	}

	/**
	 * Do any setup needed before the theme is ready.
	 * DT is not yet registered.
	 */
	public function boot(): void {
		if ( is_admin() ) {
			return;
		}

		apply_filters( 'dt/plugin/middleware', $this->container->make( Stack::class ) )
			->run();
	}

	/**
	 * Register the routes for the application.
	 *
	 * @param Routes $r
	 *
	 * @return Routes
	 */
	public function include_route_file( Routes $r ): RouteCollector {

		include routes_path( 'web.php' );

		return $r;
	}
}
