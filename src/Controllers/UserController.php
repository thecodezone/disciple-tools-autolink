<?php

namespace DT\Plugin\Controllers;

use DT\Plugin\Illuminate\Http\Request;
use DT\Plugin\Illuminate\Http\Response;
use DT\Plugin\Services\Template;
use function DT\Plugin\template;

class UserController {

	/**
	 * You can also return a string or array from a controller method,
	 * it will be automatically added to the response object.
	 *
	 * @param Request $request The request object.
	 * @param Response $response The response object.
	 * @param Template $template Controller method dependencies are automatically resolved from the container.
	 */
	public function current( Request $request, Response $response, Template $template ) {
		return $template->render( 'user', [
			'user' => wp_get_current_user()
		] );
	}

	/**
	 * Fetches and displays the details of a user.
	 *
	 * @param Request $request The request object.
	 * @param Response $response The response object.
	 * @param int $id Mapped from the ID route parameter.
	 */
	public function show( Request $request, Response $response, $id ) {
		$user = get_user_by( 'id', $id );

		return template( 'user', [
			'user' => $user
		] );
	}
}