<?php

namespace DT\Autolink\Providers;

use DT\Autolink\Illuminate\Filesystem\Filesystem;
use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Illuminate\Translation\FileLoader;
use DT\Autolink\Illuminate\Translation\Translator;
use DT\Autolink\Illuminate\Validation\Factory;

class PluginServiceProvider extends ServiceProvider
{
    /**
     * List of providers to register
     *
     * @var array
     */
    protected $providers = [
        ViewServiceProvider::class,
        ConditionsServiceProvider::class,
        CapabilitiesServiceProvider::class,
        MiddlewareServiceProvider::class,
        AssetServiceProvider::class,
        AdminServiceProvider::class,
        MagicLinkServiceProvider::class,
        RouterServiceProvider::class,
        DtMenuServiceProvider::class,
    ];

    /**
     * Do any setup needed before the theme is ready.
     * DT is not yet registered.
     */
    public function register(): void
    {
        $this->container->singleton(Request::class, function () {
            return Request::capture();
        });

        foreach ( $this->providers as $provider ) {
            $provider = $this->container->make( $provider );
            $provider->register();
        }

        $this->registerValidator();
    }

    /**
     * Register the validator
     */
    protected function registerValidator(): void
    {
        $this->container->bind(FileLoader::class, function ( $container ) {
            return new FileLoader( $container->make( Filesystem::class ), 'lang' );
        });

        $this->container->bind(Factory::class, function ( $container ) {
            $loader = $container->make( FileLoader::class );
            $translator = new Translator( $loader, 'en' );

            return new Factory( $translator, $container );
        });
    }


    /**
     * Do any setup after services have been registered and the theme is ready
     */
    public function boot(): void
    {
        foreach ( $this->providers as $provider ) {
            $provider = $this->container->make( $provider );
            $provider->boot();
        }
    }
}
