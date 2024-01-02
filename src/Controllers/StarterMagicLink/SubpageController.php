<?php

namespace DT\Plugin\Controllers\StarterMagicLink;

use DT\Plugin\Illuminate\Http\Request;
use DT\Plugin\Illuminate\Http\Response;
use DT_Magic_URL;
use function DT\Plugin\template;

class SubpageController {
	public function show( Request $request, Response $response, $key ) {
		$user     = wp_get_current_user();
		$home_url = DT_Magic_URL::get_link_url( 'starter', 'app', $key );

		return template( 'starter-magic-link/subpage', compact(
			'user',
			'home_url'
		) );
	}
}
