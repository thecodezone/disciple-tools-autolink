<?php

namespace DT\Autolink\Controllers\MagicLink;

use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Illuminate\Http\Response;
use function DT\Autolink\magic_url;
use function DT\Autolink\redirect;

class ShareController {
	public function show( Request $request, Response $response, $key ) {
		$root       = 'autolink';
		$type       = 'app';
		$magic_urls = new \DT_Magic_URL( $root );
		$user_id    = $magic_urls->get_user_id( $magic_urls::get_public_key_meta_key( $root, $type ), $key );
		$contact    = \Disciple_Tools_Users::get_contact_for_user( $user_id );
		if ( ! isset( $_COOKIE['dt_autolink_share'] ) ) {
			setcookie( 'dt_autolink_share', $contact, time() + ( 86400 * 30 ), "/" );
		}

		return redirect( magic_url( '', $key ) );
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