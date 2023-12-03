<?php

namespace DT\Plugin\Services;

use DT\Plugin\FastRoute;
use function DT\Plugin\container;
use function DT\Plugin\plugin;

class Router {

	/**
	 * @param $path
	 * @param $options
	 *
	 * @return void
	 */
	public function register_file( $file, $options = [] ) {
		$http_method          = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ?? 'GET' ) );
		$uri                  = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ?? '/' ) );
		$include_query_string = $options['query_string'] ?? false;
		$directory            = $options['directory'] ?? plugin()->routes_path;
		$path                 = '/' . trim( $directory, '/' ) . '/' . $file;
		$param                = $options['param'] ?? null;
		$param_value          = null;
		$prefix               = $options['prefix'] ?? null;

		if ( $param ) {
			$param_value          = sanitize_text_field( wp_unslash( $_GET[ $param ] ?? null ) );
			$include_query_string = false;
		}

		if ( $prefix ) {
			dd( $prefix );

		}

		if ( ! $include_query_string ) {
			$pos = strpos( $uri, '?' );
			// Strip query string (?foo=bar) and decode URI
			if ( $pos !== false ) {
				$uri = substr( $uri, 0, $pos );
			}

			if ( $param && $param_value ) {
				$uri = $uri . '?' . http_build_query( [ $param => $param_value ] );
			}
		}

		$uri = trim( rawurldecode( $uri ), '/' );

		$dispatcher = FastRoute\simpleDispatcher( function ( FastRoute\RouteCollector $r ) use ( $path ) {
			require_once $path;
		} );

		$route_info = $dispatcher->dispatch( $http_method, $uri );

		switch ( $route_info[0] ) {
			case FastRoute\Dispatcher::FOUND:
				$handler = $route_info[1];
				$vars    = $route_info[2];
				[ $class, $method ] = explode( "@", $handler, 2 );
				call_user_func_array( [ container()->make( $class ), $method ], $vars );
				break;
		}
	}
}
