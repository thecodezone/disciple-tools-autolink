<?php

namespace DT\Autolink\Middleware;

use DT\Autolink\CodeZone\Router\Middleware\Middleware;
use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Symfony\Component\HttpFoundation\Response;

class UnCached implements Middleware {

    protected $value;

    public function handle(Request $request, Response $response, callable $next)
    {
        $response->headers->set( 'Cache-Control', 'uncached' );

        return $next( $request, $response );
    }
}
