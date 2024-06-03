<?php

namespace DT\Autolink\Providers;

use function DT\Autolink\get_plugin_option;


class DtMenuServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {

        // Register the necessary bindings or singletons
        if ( get_plugin_option( 'show_in_menu' ) ) {

            add_filter('desktop_navbar_menu_options', function ( $menu_options ) {
                $menu_options[] = [
                    'label' => __( 'Autolink', 'disciple-tools-autolink' ),
                    'link' => site_url( '/autolink' ),
                ];

                return $menu_options;
            }, 999, 1);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {

        // Check if the menu should be displayed and add it if necessary
    }
}
