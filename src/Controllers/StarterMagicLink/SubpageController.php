<?php

namespace DT\Autolink\Controllers\StarterMagicLink;

use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Illuminate\Http\Response;
use DT_Magic_URL;
use function DT\Autolink\template;

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
