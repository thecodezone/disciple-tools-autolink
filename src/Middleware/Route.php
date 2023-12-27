<?php

namespace DT\Plugin\Middleware;

use DT\Plugin\FastRoute;
use DT\Plugin\Illuminate\Http\Request;
use DT\Plugin\Services\Router\Data;
use DT\Plugin\Services\Router\Dispatcher;
use DT\Plugin\Services\Router\Routes;
use WP_HTTP_Response;

/**
 * Route Middleware
 *
 * @see https://github.com/nikic/FastRoute
 */
class Route implements Middleware {
	protected $group;

	public function handle( Request $request, WP_HTTP_Response $response, $next ) {
		$http_method         = $request->getMethod();
		$uri                 = $request->getRequestUri();
		$routable_param_keys = apply_filters( 'dt/plugin/routable_params', [ 'page', 'action', 'tab' ] ) ?? [];
		$routable_params     = collect( $request->query->all() )->only( $routable_param_keys );

		// Strip query string (?foo=bar) and decode URI
		$pos = strpos( $uri, '?' );
		if ( $pos !== false ) {
			$uri = substr( $uri, 0, $pos );
		}

		//Allow for including certain params in the route,
		//Like page=general
		//or action=save
		//or tab=general
		if ( count( $routable_params ) ) {
			$uri = $uri . '?' . http_build_query( $routable_params->toArray() );
		}

		$uri = trim( rawurldecode( $uri ), '/' );

		//Get the matching route data from the router
		$router = FastRoute\simpleDispatcher( function ( Routes $r ) {
			apply_filters( 'dt/plugin/routes', $r );
		}, [
			'routeCollector' => Routes::class,
			'dataGenerator'  => Data::class,
			'dispatcher'     => Dispatcher::class,
		] );

		$matches = apply_filters( 'dt/plugin/matched_routes',
			$router->dispatch( $http_method, $uri )
		);

		if ( ! $matches || $matches[0] === FastRoute\Dispatcher::NOT_FOUND ) {
			return false;
		}

		//Apply the matching route data to the request
		$request->routes = $matches;

		return $next( $request, $response );
	}
}
