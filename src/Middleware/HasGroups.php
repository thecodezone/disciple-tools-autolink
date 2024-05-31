<?php


namespace DT\Autolink\Middleware;

use DT\Autolink\CodeZone\Router\Middleware\Middleware;
use DT\Autolink\Illuminate\Http\RedirectResponse;
use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Symfony\Component\HttpFoundation\Response;
use DT\Autolink\Conditions\HasGroups as UserHasGroups;
use function DT\Autolink\route_url;

class HasGroups implements Middleware
{
    protected $has_groups;

    public function __construct( UserHasGroups $has_groups  )
    {
        $this->has_groups = $has_groups;
    }

    public function handle( Request $request, Response $response, $next )
    {
        if ( ! $this->has_groups->test() ) {
            $response = new RedirectResponse( route_url( "groups" ), 302 );
        }

        return $next( $request, $response );
    }
}
