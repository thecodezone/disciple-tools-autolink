<?php

namespace DT\Autolink\Providers;

use DT\Autolink\CodeZone\Router;
use DT\Autolink\CodeZone\Router\Conditions\HasCap;
use DT\Autolink\Conditions\Backend;
use DT\Autolink\Conditions\Frontend;
use DT\Autolink\Conditions\Plugin;

class ConditionsServiceProvider extends ServiceProvider {
	protected $conditions = [
		'can'      => HasCap::class,
		'backend'  => Backend::class,
		'frontend' => Frontend::class,
		'plugin'   => Plugin::class
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
		add_filter( Router\namespace_string( 'conditions' ), function ( array $middleware ) {
			return array_merge( $middleware, $this->conditions );
		} );
	}

	public function boot(): void {
		// TODO: Implement boot() method.
	}
}
