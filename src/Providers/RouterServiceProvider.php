<?php

namespace DT\Autolink\Providers;

use DT\Autolink\CodeZone\Router;
use DT\Autolink\CodeZone\Router\FastRoute\Routes;
use DT\Autolink\FastRoute\RouteCollector;
use DT\Autolink\Plugin;
use function DT\Autolink\routes_path;

class RouterServiceProvider extends ServiceProvider {
	/**
	 * Do any setup needed before the theme is ready.
	 * DT is not yet registered.
	 */
	public function register(): void {
		Router::register( [
			'container' => $this->container,
			'route_param' => Plugin::ROUTE_QUERY_PARAM,
		] );

		add_filter( Router\namespace_string( "routes" ), [ $this, 'include_route_file' ], 1 );
	}

	/**
	 * Do any setup needed before the theme is ready.
	 * DT is not yet registered.
	 */
	public function boot(): void {
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
