<?php

namespace DT\Autolink\Providers;

use DT\Autolink\CodeZone\Router;
use DT\Autolink\CodeZone\Router\FastRoute\Routes;
use DT\Autolink\CodeZone\Router\Middleware\Stack;
use DT\Autolink\FastRoute\RouteCollector;
use DT\Autolink\Illuminate\Http\Response;
use function DT\Autolink\is_plugin_route;
use function DT\Autolink\namespace_string;
use function DT\Autolink\routes_path;

class RouterServiceProvider extends ServiceProvider {
	/**
	 * Do any setup needed before the theme is ready.
	 * DT is not yet registered.
	 */
	public function register(): void {
		Router::register( [
			'container' => $this->container,
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
