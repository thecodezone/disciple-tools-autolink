<?php

namespace DT\Autolink\Middleware;

use DT\Autolink\CodeZone\Router\Middleware\Middleware;
use DT\Autolink\Illuminate\Http\RedirectResponse;
use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Plugin;
use DT\Autolink\Symfony\Component\HttpFoundation\Response;

class LoggedOut implements Middleware {

	public function handle( Request $request, Response $response, $next ) {
		if ( is_user_logged_in() ) {
			$response = new RedirectResponse( '/' . Plugin::HOME_ROUTE, 302 );

		}

		return $next( $request, $response );
	}
}
