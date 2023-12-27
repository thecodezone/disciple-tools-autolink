<?php

namespace DT\Plugin\Middleware;

use Illuminate\Http\Request;
use WP_HTTP_Response;
use function DT\Plugin\is_json;

/**
 * Class RedirectMiddleware
 *
 * This middleware is used to redirect the user to the URL specified in the response body.
 *
 * @package DT\Plugin\Middleware
 */
class HandleRedirects implements Middleware {
	public function handle( Request $request, WP_HTTP_Response $response, $next ) {
		if ( $response->get_status() === 301 || $response->get_status() === 302 ) {
			if ( is_string( $response->get_data() ) && ! is_json( $response->get_data() ) ) {
				if ( parse_url( $response->get_data() ) ) {
					wp_redirect( $response->get_data(), $response->get_status() );
					exit;
				}
				exit;
			}
		}

		return $next( $request, $response );
	}
}