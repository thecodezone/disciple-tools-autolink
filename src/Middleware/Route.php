<?php

namespace DT\Plugin\Middleware;

use DT\Plugin\FastRoute;
use DT\Plugin\Services\Router\Data;
use DT\Plugin\Services\Router\Dispatcher;
use DT\Plugin\Services\Router\Routes;
use Illuminate\Http\Request;
use WP_HTTP_Response;

/**
 * Route Middleware
 *
 * @see https://github.com/nikic/FastRoute
 */
class Route implements Middleware {
	protected $group;

	public function handle( Request $request, WP_HTTP_Response $response, $next ) {
		$http_method     = $request->getMethod();
		$uri             = $request->getRequestUri();
		$routable_params = apply_filters( 'dt/plugin/routable_params', [ 'page', 'action', 'tab' ] ) ?? [];
		$param_value     = null;

		//Allow for including certain params in the route,
		//Like page=general
		//or action=save
		//or tab=general
		if ( $routable_params && count( $routable_params ) ) {
			foreach ( $routable_params as $param ) {
				if ( $request->get( $param ) ) {
					$param_value = $request->get( $param );
					break;
				}
			}
		}

		$pos = strpos( $uri, '?' );
		// Strip query string (?foo=bar) and decode URI
		if ( $pos !== false ) {
			$uri = substr( $uri, 0, $pos );
		}

		if ( $param && $param_value ) {
			$uri = $uri . '?' . http_build_query( [ $param => $param_value ] );
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
