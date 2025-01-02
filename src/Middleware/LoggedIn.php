<?php

namespace DT\Autolink\Middleware;

use DT\Autolink\CodeZone\Router\Middleware\Middleware;
use DT\Autolink\Illuminate\Http\RedirectResponse;
use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Symfony\Component\HttpFoundation\Response;
use function DT\Autolink\route_url;

class LoggedIn implements Middleware, \DT\Autolink\Psr\Http\Server\MiddlewareInterface {
	public function handle( Request $request, Response $response, $next ) {
		if ( ! is_user_logged_in() ) {
			$response = new RedirectResponse( route_url( "login" ), 302 );
		}

		return $next( $request, $response );
	}
}
