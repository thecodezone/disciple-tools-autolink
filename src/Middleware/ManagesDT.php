<?php

namespace DT\Plugin\Middleware;

use DT\Plugin\CodeZone\Router\Middleware\Middleware;
use DT\Plugin\Illuminate\Http\Request;
use DT\Plugin\Illuminate\Http\Response;

class ManagesDT implements Middleware {

	public function handle( Request $request, Response $response, $next ) {
		if ( ! is_user_logged_in() && wp_get_current_user()->has_cap( 'manage_dt' ) ) {
			$response->setStatusCode( 302 );
			$response->setContent( wp_login_url( $request->getUri() ) );
		}

		return $next( $request, $response );
	}
}