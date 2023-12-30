<?php

namespace DT\Plugin\Controllers\StarterMagicLink;

use DT\Plugin\Illuminate\Http\Request;
use DT_Magic_URL;
use WP_HTTP_Response;
use WP_REST_Response;
use function DT\Plugin\template;

class HomeController {
	public function show( Request $request, WP_HTTP_Response $response, $key ) {
		$user        = wp_get_current_user();
		$subpage_url = DT_Magic_URL::get_link_url( 'starter-magic-app', 'app', $key ) . '/subpage';

		$response->set_data( template( 'starter-magic-link/show', compact(
			'user',
			'subpage_url'
		) ) );

		return $response;
	}

	public function data( Request $request, WP_HTTP_Response $response, $key ) {
		$user = wp_get_current_user();
		$data = [
			'user_login' => $user->user_login,
		];

		return new WP_REST_Response( $data, 200 );
	}
}
