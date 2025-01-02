<?php


namespace DT\Autolink\Middleware;

use DT\Autolink\Psr\Http\Message\ResponseInterface;
use DT\Autolink\Psr\Http\Message\ServerRequestInterface;
use DT\Autolink\Psr\Http\Server\MiddlewareInterface;
use DT\Autolink\Psr\Http\Server\RequestHandlerInterface;
use function DT\Autolink\redirect;
use function DT\Autolink\route_url;

class Genmap implements MiddlewareInterface
{
	public function process( ServerRequestInterface $request, RequestHandlerInterface $handler ): ResponseInterface
    {
        if ( ! function_exists( 'dt_genmapper_metrics' ) ) {
			return redirect( route_url( "groups" ), 302 );
        }

	    return $handler->handle( $request );
    }
}
