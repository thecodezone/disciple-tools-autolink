<?php

namespace DT\Plugin\Middleware;

use DT\Plugin\CodeZone\Router\Middleware\Middleware;
use DT\Plugin\Illuminate\Http\Request;
use DT\Plugin\Illuminate\Http\Response;

class LoggedIn implements Middleware {
	public function handle( Request $request, Response $response, $next ) {
		if ( ! is_user_logged_in() ) {
			$response->setStatusCode( 302 );
			$response->setContent( wp_login_url( $request->getUri() ) );
		}

		return $next( $request, $response );
	}
}