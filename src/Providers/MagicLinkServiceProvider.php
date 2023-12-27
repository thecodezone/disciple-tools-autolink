<?php

namespace DT\Plugin\Providers;

use DT\Plugin\MagicLinks\StarterMagicApp;

class MagicLinkServiceProvider extends ServiceProvider {
	/**
	 * Do any setup needed before the theme is ready.
	 * DT is not yet registered.
	 */
	public function register(): void {
	}

	/**
	 * Do any setup after services have been registered and the theme is ready
	 */
	public function boot(): void {
		$this->container->singleton(
			StarterMagicApp::class
		);
		$this->container->make( StarterMagicApp::class );
	}
}
