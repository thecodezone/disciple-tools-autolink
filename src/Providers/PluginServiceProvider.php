<?php

namespace DT\Plugin\Providers;

class PluginServiceProvider extends ServiceProvider {
	protected $providers = [
		RouteServiceProvider::class,
		AdminServiceProvider::class,
		MagicLinkServiceProvider::class
	];

	public function register(): void {
		foreach ( $this->providers as $provider ) {
			$provider = $this->container->make( $provider );
			$provider->register();
		}
	}

	public function boot(): void {
		foreach ( $this->providers as $provider ) {
			$provider = $this->container->make( $provider );
			$provider->boot();
		}
	}
}
