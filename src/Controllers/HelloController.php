<?php

namespace CZ\Plugin\Controllers;

use CZ\Illuminate\View\View;

class HelloController {
	/**
	 * Show the hello world message
	 *
	 * @return \WP_REST_Response
	 */
	public function data() {
		return new \WP_REST_Response( [
			'status' => 'success',
			'message' => 'Hello World!'
		], 200 );
	}

	public function show() {
		$name = 'Friend';
		include __DIR__ . '/../../resources/templates/hello.php';
		exit;
	}
}