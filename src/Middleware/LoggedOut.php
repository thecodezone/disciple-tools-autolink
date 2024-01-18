<?php

namespace DT\Plugin\Middleware;

use DT\Plugin\CodeZone\Router\Middleware\Middleware;
use DT\Plugin\Illuminate\Http\RedirectResponse;
use DT\Plugin\Illuminate\Http\Request;
use DT\Plugin\Plugin;
use DT\Plugin\Symfony\Component\HttpFoundation\Response;

class LoggedOut implements Middleware {

	public function handle( Request $request, Response $response, $next ) {
		if ( is_user_logged_in() ) {
			$response = new RedirectResponse( '/' . Plugin::HOME_ROUTE, 302 );

		}

		return $next( $request, $response );
	}
}
