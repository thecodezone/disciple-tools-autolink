<?php

namespace DT\Plugin\Controllers\StarterMagicLink;

use DT\Plugin\Illuminate\Http\Request;
use DT\Plugin\Illuminate\Http\Response;
use DT_Magic_URL;
use function DT\Plugin\template;

class HomeController {
	public function show( Request $request, Response $response, $key ) {
		$user        = wp_get_current_user();
		$subpage_url = DT_Magic_URL::get_link_url( 'starter', 'app', $key ) . '/subpage';

		return template( 'starter-magic-link/show', compact(
			'user',
			'subpage_url'
		) );
	}

	public function data( Request $request, Response $response, $key ) {
		$user = wp_get_current_user();
		$data = [
			'user_login' => $user->user_login,
		];
		$response->setContent( $data );

		return $response;
	}
}
