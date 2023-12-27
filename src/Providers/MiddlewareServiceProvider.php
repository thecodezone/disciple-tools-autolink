<?php

namespace DT\Plugin\Providers;

use DT\Plugin\Middleware\DispatchController;
use DT\Plugin\Middleware\HandleErrors;
use DT\Plugin\Middleware\HandleRedirects;
use DT\Plugin\Middleware\Render;
use DT\Plugin\Middleware\Route;
use DT\Plugin\Middleware\SetHeaders;
use DT\Plugin\Middleware\Stack;

/**
 * Request middleware to be used in the request lifecycle.
 *
 * Class MiddlewareServiceProvider
 * @package DT\Plugin\Providers
 */
class MiddlewareServiceProvider extends ServiceProvider {
	protected $middleware = [
		Route::class,
		DispatchController::class,
		SetHeaders::class,
		HandleRedirects::class,
		HandleErrors::class,
		Render::class,
	];

	public function register(): void {
		add_filter( 'dt/plugin/middleware', function ( Stack $stack ) {
			$stack->push( ...$this->middleware );

			return $stack;
		} );
	}

	public function boot(): void {
		// TODO: Implement boot() method.
	}
}
