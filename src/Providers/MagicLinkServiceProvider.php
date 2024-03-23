<?php

namespace DT\Autolink\Providers;

use DT\Autolink\MagicLinks\StarterMagicApp;
use function DT\Autolink\collect;

class MagicLinkServiceProvider extends ServiceProvider {
	protected $container;

	protected $magic_links = [
		'starter/app' => StarterMagicApp::class,
	];

	/**
	 * Do any setup needed before the theme is ready.
	 * DT is not yet registered.
	 */
	public function register(): void {
		$this->container->bind( 'DT\Autolink\MagicLinks', function () {
			return collect( $this->magic_links );
		} );
	}

	/**
	 * Do any setup after services have been registered and the theme is ready
	 */
	public function boot(): void {
		$this->container->make( 'DT\Autolink\MagicLinks' )
		                ->each( function ( $magic_link ) {
			                $this->container->singleton( $magic_link );
			                $this->container->make( $magic_link );
		                } );
	}
}
