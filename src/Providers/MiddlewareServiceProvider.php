<?php

namespace DT\Autolink\Providers;

use DT\Autolink\CodeZone\Router;
use DT\Autolink\CodeZone\Router\Middleware\DispatchController;
use DT\Autolink\CodeZone\Router\Middleware\HandleErrors;
use DT\Autolink\CodeZone\Router\Middleware\HandleRedirects;
use DT\Autolink\CodeZone\Router\Middleware\Middleware;
use DT\Autolink\CodeZone\Router\Middleware\Render;
use DT\Autolink\CodeZone\Router\Middleware\Route;
use DT\Autolink\CodeZone\Router\Middleware\Stack;
use DT\Autolink\CodeZone\Router\Middleware\UserHasCap;
use DT\Autolink\CodeZone\Router\Middleware\SetHeaders;
use DT\Autolink\Middleware\CheckShareCookie;
use DT\Autolink\Middleware\Leader;
use DT\Autolink\Middleware\LoggedIn;
use DT\Autolink\Middleware\LoggedOut;
use DT\Autolink\Middleware\MagicLink;
use DT\Autolink\Middleware\Nonce;
use DT\Autolink\Middleware\SurveyCompleted;
use Exception;
use function DT\Autolink\namespace_string;

/**
 * Request middleware to be used in the request lifecycle.
 *
 * Class MiddlewareServiceProvider
 * @package DT\Autolink\Providers
 */
class MiddlewareServiceProvider extends ServiceProvider {
	protected $middleware = [
		Route::class,
		DispatchController::class,
		HandleErrors::class,
		HandleRedirects::class,
		Render::class,
		SetHeaders::class,
	];

	protected $route_middleware = [
		'auth' => LoggedIn::class,
		'can' => UserHasCap::class, // can:manage_dt
		'guest' => LoggedOut::class,
		'magic' => MagicLink::class,
		'nonce' => Nonce::class,  // nonce:disciple_tools_autolink_nonce
		'check_share' => CheckShareCookie::class,
		'survey' => SurveyCompleted::class,
        'leader' => Leader::class
	];

	/**
	 * Registers the middleware for the plugin.
	 *
	 * This method adds a filter to register middleware for the plugin.
	 * The middleware is added to the stack in the order it is defined above.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function register(): void {
		add_filter( namespace_string( 'middleware' ), function ( Stack $stack ) {
			$stack->push( ...$this->middleware );

			return $stack;
		} );

		add_filter( Router\namespace_string( 'middleware' ), function ( array $middleware ) {
			return array_merge( $middleware, $this->route_middleware );
		} );

		/**
		 * Parse named signature to instantiate any middleware that takes arguments.
		 * Signature format: "name:signature"
		 */
		add_filter( Router\namespace_string( 'middleware_factory' ), function ( Middleware|null $middleware, $attributes ) {
			$classname = $attributes['className'] ?? null;
			$name      = $attributes['name'] ?? null;
			$signature = $attributes['signature'] ?? null;

			switch ( $name ) {
				case 'magic':
					$magic_link_name       = $signature;
					$magic_link_class_name = $this->container->make( 'DT\Autolink\MagicLinks' )->get( $magic_link_name );
					if ( ! $magic_link_class_name ) {
						throw new Exception( esc_html( "Magic link not found: $magic_link_name" ) );
					}
					$magic_link = $this->container->make( $magic_link_class_name );

					//The signature is the part of the route name after the ":". We need to break it into an array.
					$middleware = $this->container->makeWith( $classname, [
						'magic_link' => $magic_link
					] );
					break;
				case 'nonce':
					$middleware = $this->container->makeWith( $classname, [
						'nonce_name' => $signature
					] );
					break;
			}

			return $middleware;
		}, 10, 2 );
	}

	/**
	 * Do anything we need to do after the theme loads.
	 *
	 * @return void
	 */
	public function boot(): void {
	}
}
