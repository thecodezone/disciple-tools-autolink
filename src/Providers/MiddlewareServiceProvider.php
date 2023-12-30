<?php

namespace DT\Plugin\Providers;

use DT\Plugin\CodeZone\Router\Middleware\DispatchController;
use DT\Plugin\CodeZone\Router\Middleware\HandleErrors;
use DT\Plugin\CodeZone\Router\Middleware\HandleRedirects;
use DT\Plugin\CodeZone\Router\Middleware\Render;
use DT\Plugin\CodeZone\Router\Middleware\Route;
use DT\Plugin\CodeZone\Router\Middleware\SetHeaders;
use DT\Plugin\CodeZone\Router\Middleware\Stack;

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
		HandleErrors::class,
		HandleRedirects::class,
		Render::class,
	];

	/**
	 * Registers the middleware for the plugin.
	 *
	 * This method adds a filter to register middleware for the plugin.
	 * The middleware is added to the stack in the order it is defined above.
	 *
	 * @return void
	 */
	public function register(): void {
		add_filter( 'dt/plugin/middleware', function ( Stack $stack ) {
			$stack->push( ...$this->middleware );

			return $stack;
		} );
	}

	/**
	 * Do anything we need to do after the theme loads.
	 *
	 * @return void
	 */
	public function boot(): void {
	}
}
