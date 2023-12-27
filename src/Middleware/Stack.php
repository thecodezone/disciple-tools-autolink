<?php

namespace DT\Plugin\Middleware;

use DT\Plugin\Illuminate\Support\Collection;
use Exception;
use Illuminate\Http\Request;
use WP_HTTP_Response;
use function DT\Plugin\container;

class Stack extends Collection {
	public function run( $request = null, $response = null ): WP_HTTP_Response|null|string|Collection {
		return $this->next(
			$request ? $request : container()->make( Request::class ),
			$response ? $response : container()->make( WP_HTTP_Response::class )
		);
	}

	protected function next( Request $request, WP_HTTP_Response $response ) {
		if ( $this->isEmpty() ) {
			return $request;
		}

		$middleware = $this->first();

		if ( is_string( $middleware ) ) {
			$middleware = container()->make( $middleware );
		}

		if ( ! $middleware ) {
			return new Collection( [
				$request,
				$response
			] );
		}

		if ( ! $middleware instanceof Middleware ) {
			throw( new Exception( __( $this->first() . ' is not an instance of middleware', 'dt-plugin' ) ) );
		}

		return $middleware->handle(
			$request,
			$response,
			$this->callback()
		);
	}

	protected function callback(): callable {
		return function ( Request $request, WP_HTTP_Response $response ) {
			$this->shift();

			return $this->next( $request, $response );
		};
	}
}
