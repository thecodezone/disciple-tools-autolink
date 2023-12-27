<?php

namespace DT\Plugin\Middleware;


use Illuminate\Http\Request;
use WP_HTTP_Response;

class LoggedIn implements Middleware {

	public function handle( Request $request, WP_HTTP_Response $response, $next ) {
		$response->set_status( 302 );
		$response->set_data( wp_login_url( $request->getUri() ) );
		
		return $next( $request, $response );
	}
}