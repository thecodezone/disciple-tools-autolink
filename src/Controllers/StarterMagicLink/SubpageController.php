<?php

namespace DT\Plugin\Controllers\StarterMagicLink;

use DT\Plugin\Illuminate\Http\Request;
use DT_Magic_URL;
use WP_HTTP_Response;
use function DT\Plugin\template;

class SubpageController {
	public function show( Request $request, WP_HTTP_Response $response, $key ) {
		$user     = wp_get_current_user();
		$home_url = DT_Magic_URL::get_link_url( 'starter-magic-app', 'app', $key );

		$response->set_data( template( 'starter-magic-link/subpage', compact(
			'user',
			'home_url'
		) ) );

		return $response;
	}
}
