<?php

namespace DT\Autolink\Providers;

use DT\Autolink\League\Container\ServiceProvider\AbstractServiceProvider;
use DT\Autolink\League\Plates\Engine;
use function DT\Autolink\views_path;

/**
 * Class ViewServiceProvider
 *
 * This class is a service provider responsible for registering the view engine singleton and any extensions.
 *
 * @see https://platesphp.com/
 */
class ViewServiceProvider extends AbstractServiceProvider {

    public function views_path() {
        return views_path();
    }

    /**
     * Provide the services that this provider is responsible for.
     *
     * @param string $id The ID to check.
     * @return bool Returns true if the given ID is provided, false otherwise.
     */
    public function provides( string $id ): bool
    {
        return in_array($id, [
            Engine::class
        ]);
    }

    /**
     * Register the view engine singleton and any extensions
     *
     * @return void
     */
    public function register(): void {
        $this->getContainer()->addShared( Engine::class, function () {
            return new Engine( $this->views_path() );
        } );
        $this->getContainer()->get( Engine::class );
    }
}
