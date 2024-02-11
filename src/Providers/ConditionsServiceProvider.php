<?php

namespace DT\Plugin\Providers;

use DT\Plugin\CodeZone\Router;
use DT\Plugin\CodeZone\Router\Conditions\HasCap;
use DT\Plugin\Conditions\Backend;
use DT\Plugin\Conditions\Frontend;
use DT\Plugin\Conditions\Plugin;

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
