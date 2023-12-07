<?php

namespace DT\Plugin\Providers;

use DT\Plugin\League\Plates\Engine;
use DT\Plugin\Plates\Escape;
use function DT\Plugin\views_path;

/**
 * Register the plates view engine
 * @see https://platesphp.com/
 */
class ViewServiceProvider extends ServiceProvider {
	/**
	 * Do any setup needed before the theme is ready.
	 * DT is not yet registered.
	 */
	public function register(): void {
		$this->container->singleton( Engine::class, function ( $container ) {
			return new Engine( views_path() );
		} );
		$engine = $this->container->make( Engine::class )->loadExtension(
			$this->container->make( Escape::class )
		);
	}

	/**
	 * Do any setup after services have been registered and the theme is ready
	 */
	public function boot(): void {
	}
}
