<?php

namespace DT\Plugin\Providers;

use DT\Plugin\Services\Router;

class AdminServiceProvider extends ServiceProvider {
    /**
     * Register any services
     *
     * @return void
     */
    public function register(): void {
        add_action( 'admin_menu', array( $this, 'register_menu' ), 99 );
    }

    /**
     * Register the admin menu
     *
     * @return void
     */
    public function register_menu(): void {
        add_submenu_page( 'dt_extensions',
            __( 'DT Plugin', 'dt_plugin' ),
            __( 'DT Plugin', 'dt_plugin' ),
            'manage_dt',
            'dt_plugin',
            array( $this, 'register_admin_routes' )
        );
    }

    public function register_admin_routes(): void {
        $router = $this->container->make( Router::class );
        $router->from_file( 'web/admin.php', array(
            'param' => 'page',
        ) );
    }

    /**
     * Handle any logic that needs to be after WortabdPress boots
     *
     * @return void
     */
    public function boot(): void {
        /*
         * Array of plugin arrays. Required keys are name and slug.
         * If the source is NOT from the .org repo, then source is also required.
         */
        $plugins = array(
            array(
                'name'     => 'Disciple.Tools Dashboard',
                'slug'     => 'disciple-tools-dashboard',
                'source'   => 'https://github.com/DiscipleTools/disciple-tools-dashboard/releases/latest/download/disciple-tools-dashboard.zip',
                'required' => false,
            ),
            array(
                'name'     => 'Disciple.Tools Genmapper',
                'slug'     => 'disciple-tools-genmapper',
                'source'   => 'https://github.com/DiscipleTools/disciple-tools-genmapper/releases/latest/download/disciple-tools-genmapper.zip',
                'required' => true,
            ),
            array(
                'name'     => 'Disciple.Tools Autolink',
                'slug'     => 'disciple-tools-autolink',
                'source'   => 'https://github.com/DiscipleTools/disciple-tools-genmapper/releases/latest/download/disciple-tools-autolink.zip',
                'required' => true,
            ),
        );

        /*
         * Array of configuration settings. Amend each line as needed.
         *
         * Only uncomment the strings in the config array if you want to customize the strings.
         */
        $config = array(
            'id'           => 'disciple_tools',
            // Unique ID for hashing notices for multiple instances of TGMPA.
            'default_path' => '/includes/plugins/',
            // Default absolute path to bundled plugins.
            'menu'         => 'tgmpa-install-plugins',
            // Menu slug.
            'parent_slug'  => 'plugins.php',
            // Parent menu slug.
            'capability'   => 'manage_options',
            // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
            'has_notices'  => true,
            // Show admin notices or not.
            'dismissable'  => true,
            // If false, a user cannot dismiss the nag message.
            'dismiss_msg'  => 'These are recommended plugins to complement your Disciple.Tools system.',
            // If 'dismissable' is false, this message will be output at top of nag.
            'is_automatic' => true,
            // Automatically activate plugins after installation or not.
            'message'      => '',
            // Message to output right before the plugins table.
        );

        tgmpa( $plugins, $config );
    }
}
