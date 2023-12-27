<?php

namespace DT\Plugin\Services\Router;

use DT\Plugin\Factories\ConditionFactory;
use DT\Plugin\FastRoute\RouteCollector;
use DT\Plugin\Illuminate\Support\Collection;
use function DT\Plugin\container;

/**
 * Class Routes
 * Extension of FastRoute\RouteCollector that normalizes handlers
 * and allows for middleware and condition groups
 *
 * @see https://github.com/nikic/FastRoute
 *
 * phpcs:disable
 */
class Routes extends RouteCollector {
	public array $currentMiddleware = [];

	/**
	 * Register a route.
	 *
	 * @param $httpMethod
	 * @param $route
	 * @param $handler
	 *
	 * @return void
	 */
	public function addRoute( $httpMethod, $route, $handler ) {
		parent::addRoute( $httpMethod, $route, $this->normalizeHandler( $httpMethod, $route, $handler ) );
	}

	/**
	 * Normalize handler to array
	 *
	 * @param $httpMethod
	 * @param $route
	 * @param $handler
	 *
	 * @return array
	 */
	protected function normalizeHandler( $httpMethod, $route, $handler ): array {
		if ( is_string( $handler ) ) {
			$handler = explode( '@', $handler, 2 );
		}

		if ( ! isset( $handler[2] ) ) {
			$handler[2] = [];
		}

		if ( is_string( $handler[2]['middleware'] ?? false ) ) {
			$handler[2]['middleware'] = [ $handler[2]['middleware'] ];
		}

		if ( $this->currentMiddleware ) {
			if ( ! isset( $handler[2]['middleware'] ) ) {
				$handler[2]['middleware'] = [];
			}

			if ( ! is_array( $handler[2]['middleware'] ) ) {
				$handler[2]['middleware'] = [ $handler[2]['middleware'] ];
			}

			array_push( $handler[2]['middleware'], ...$this->currentMiddleware );
		}

		if ( is_array( $handler[2]['middleware'] ?? false ) ) {
			$handler[2]['middleware'] = array_unique( $handler[2]['middleware'] );
		}

		return $handler;
	}

	/**
	 * Register a middleware group.
	 *
	 * @param $callback
	 *
	 * @return void
	 */
	public function addMiddleware( $middleware, $callback ) {
		$middleware              = is_string( $middleware ) ? [ $middleware ] : $middleware;
		$this->currentMiddleware = $middleware;
		$callback( $this );
		$this->currentMiddleware = [];
	}

	/**
	 * Register a condition group.
	 *
	 * @param $callback
	 *
	 * @return void
	 */
	public function addCondition( $condition, callable $callback ) {
		$condition = container()->make( ConditionFactory::class )->make( $condition );
		if ( $condition instanceof Collection ) {
			$proceed = $condition->some( function ( $condition ) {
				return ! $condition->test();
			} );
		} else {
			$proceed = $condition->test();
		}

		if ( $proceed ) {
			$callback( $this );
		}
	}
}
// phpcs:enable