<?php

namespace DT\Plugin\Controllers;

use function DT\Plugin\template;

class UserController {

	public function show() {
		return template( 'user', [
			'user' => wp_get_current_user()
		] );
	}
}