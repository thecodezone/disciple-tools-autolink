<?php

namespace DT\Autolink\Providers;

use DT\Autolink\League\Container\ServiceProvider\BootableServiceProviderInterface;
use DT\Autolink\League\Container\ServiceProvider\AbstractServiceProvider;
use function DT\Autolink\get_plugin_option;



class DtMenuServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface{
    /**
     * Provide the services that this provider is responsible for.
     *
     * @param string $id The ID to check.
     * @return bool Returns true if the given ID is provided, false otherwise.
     */
    public function provides( string $id ): bool
    {
        return false;
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        // Register the necessary bindings or singletons
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        static $filter_added = false;

        if ( !$filter_added && get_plugin_option( 'show_in_menu' ) ) {
            add_filter('desktop_navbar_menu_options', function ( $menu_options ) {
                $menu_options[] = [
                    'label' => __( 'Autolink', 'disciple-tools-autolink' ),
                    'link' => site_url( '/autolink' ),
                ];

                return $menu_options;
            }, 999, 1);
            $filter_added = true;
        }
    }
}
