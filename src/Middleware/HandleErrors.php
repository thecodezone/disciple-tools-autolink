<?php

namespace DT\Plugin\Middleware;

use Illuminate\Http\Request;
use WP_HTTP_Response;

class HandleErrors implements Middleware {

	public function handle( Request $request, WP_HTTP_Response $response, $next ) {

		$error_codes = apply_filters( 'dt/plugin/error-codes', [
			400 => "Bad Request",
			401 => "Unauthorized",
			403 => "Forbidden",
			404 => "Not Found",
			500 => "Internal Server Error",
			502 => "Bad Gateway",
			503 => "Service Unavailable",
			504 => "Gateway Timeout"
		] );

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( array_key_exists( $response->get_status(), $error_codes ) ) {
			wp_die( $error_codes[ $response->get_status() ], $response->get_status() . $response->get_status(), [
				'response'  => $response->get_data(),
				'back_link' => true
			] );
		}

		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped

		return $next( $request, $response );
	}
}