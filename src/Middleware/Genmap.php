<?php


namespace DT\Autolink\Middleware;

use DT\Autolink\CodeZone\Router\Middleware\Middleware;
use DT\Autolink\Illuminate\Http\RedirectResponse;
use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Symfony\Component\HttpFoundation\Response;
use function DT\Autolink\route_url;

class Genmap implements Middleware
{
    public function handle( Request $request, Response $response, $next )
    {

        if ( ! function_exists( 'dt_genmapper_metrics' ) ) {

            $response = new RedirectResponse( route_url( "groups" ), 302 );
        }

        return $next( $request, $response );
    }
}
