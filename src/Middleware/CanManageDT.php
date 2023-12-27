<?php

namespace DT\Plugin\Middleware;


use DT\Plugin\Illuminate\Http\Request;
use WP_HTTP_Response;

class CanManageDT implements Middleware {

	public function handle( Request $request, WP_HTTP_Response $response, $next ) {
		if ( ! is_user_logged_in() && wp_get_current_user()->has_cap( 'manage_dt' ) ) {
			$response->set_status( 302 );
			$response->set_data( wp_login_url( $request->getUri() ) );
		}

		return $next( $request, $response );
	}
}