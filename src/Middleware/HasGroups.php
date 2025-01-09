<?php


namespace DT\Autolink\Middleware;

use DT\Autolink\Psr\Http\Message\ResponseInterface;
use DT\Autolink\Psr\Http\Message\ServerRequestInterface;
use DT\Autolink\Psr\Http\Server\MiddlewareInterface;
use DT\Autolink\Psr\Http\Server\RequestHandlerInterface;
use function DT\Autolink\redirect;
use function DT\Autolink\route_url;

class HasGroups implements MiddlewareInterface
{
    public function process( ServerRequestInterface $request, RequestHandlerInterface $handler ): ResponseInterface
    {
        $groups  = \DT_Posts::list_posts( 'groups', [
            'assigned_to' => [ get_current_user_id() ],
            'limit'       => 1
        ], false );

        if ( $groups['total'] <= 1 ) {
            return redirect( route_url( "groups" ) );
        }

        return $handler->handle( $request );
    }
}
