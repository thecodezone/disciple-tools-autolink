<?php

namespace DT\Plugin\Factories;

use DT\Plugin\Illuminate\Container\Container;
use DT\Plugin\Middleware\Middleware;

class MiddlewareFactory {
	protected Container $container;

	public function __construct( Container $container ) {
		$this->container = $container;
	}

	public function make( $value ): Middleware {
		if ( is_object( $value ) && $value instanceof Middleware ) {
			return $value;
		}

		return $this->container->make( $value );
	}
}