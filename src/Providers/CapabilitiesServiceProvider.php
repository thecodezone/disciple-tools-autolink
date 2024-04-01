<?php

namespace DT\Autolink\Providers;

use DT\Autolink\Conditions\Plugin as IsPlugin;

class CapabilitiesServiceProvider extends ServiceProvider {
	public $route_capabilities = [ "access_contacts", "view_any_contacts" ];

	public function register(): void {
		add_filter( 'user_has_cap', [ $this, 'user_has_cap' ], 10, 3 );
	}

	/**
	 * Boots the software component.
	 *
	 * This method is responsible for initializing and starting the software component. It does not take any arguments
	 * and does not return any value.
	 *
	 * The method is typically called once during the software's lifecycle, either when the software starts up or when
	 * the component is dynamically instantiated.
	 *
	 * Example usage:
	 * ```
	 * $component = new Component();
	 * $component->boot();
	 * ```
	 */
	public function boot(): void {
	}


	/**
	 * Determines whether a user has a specific capability.
	 *
	 * @param boolean $all Whether the user has all capabilities.
	 * @param string $cap The capability to check.
	 * @param array $args Optional arguments.
	 *
	 * @return array The updated capabilities.
	 */
	public function user_has_cap( $all, $cap, $args ) {

		// Add some capabilities to the user if this is a plugin route.
		if ( $this->container->make( IsPlugin::class )->test() ) {
			foreach ( $this->route_capabilities as $cap ) {
				$all_caps[ $cap ] = true;
			}
		}

		return $all_caps;
	}
}
