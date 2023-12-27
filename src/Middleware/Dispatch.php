<?php

namespace DT\Plugin\Middleware;

use DT\Plugin\Factories\ResponseFactory;
use Illuminate\Http\Request;
use WP_HTTP_Response;
use function DT\Plugin\container;

/**
 * Class Dispatch
 *
 * Connects the request to the appropriate controller method.
 * Apply any route-specific middleware.
 *
 * @see https://nikic.github.io/fast-route/
 */
class Dispatch implements Middleware {
	protected $response_factory;

	public function __construct( ResponseFactory $response_factory ) {
		$this->response_factory = $response_factory;
	}

	public function handle( Request $request, WP_HTTP_Response $response, $next ) {
		$route_info = $request->routes;

		if ( ! $route_info ) {
			return $next( $request );
		}

		[ $is_match, $handler, $vars ] = $route_info;

		if ( ! $is_match ) {
			return $next( $request );
		}

		[ $class, $method, $config ] = $handler;

		$middleware = $config['middleware'] ?? [];

		$response_before_controller = $response;

		//Apply route-specific middleware
		if ( ! empty( $middleware ) ) {
			$response = container()->make( Stack::class )
			                       ->push( ...$middleware )
			                       ->push( HandleRedirects::class )
			                       ->push( HandleErrors::class )
			                       ->run( $request, $response );
		}

		//Call the  controller method
		$response = $this->response_factory->make(
			container()->call( [ container()->make( $class ), $method ], [
				...$vars,
				$request,
				$response
			] ),
			$response_before_controller
		);

		return $next( $request, $response );
	}
}
