<?php

namespace DT\Plugin\Providers;

class MagicLinkServiceProvider extends ServiceProvider {
	public function register(): void {
	}

	public function boot(): void {
		//  $this->container->singleton(
		//      UserMagicLink::class
		//  );
		//  $this->container->make( UserMagicLink::class );
	}
}
