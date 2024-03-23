<?php

namespace DT\Autolink\Controllers;

use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Illuminate\Http\Response;
use function DT\Autolink\template;

/**
 * And example controller class.
 * Controllers are classes that are responsible for handling requests and returning responses.
 * Response objects can be modified by the controller methods, or you can return a string or array
 * from the method, and it will be automatically added to the response object.
 *
 * @package Controllers
 */
class HelloController {
	/**
	 * Sets the content of the response to a success message and returns the response object.
	 *
	 * @param Request $request The request object.
	 * @param Response $response The response object to be modified.
	 */
	public function data( Request $request, Response $response ) {
		$response->setContent( [
			'status'  => 'success',
			'message' => 'Hello World!'
		] );

		return $response;
	}

	/**
	 * You can also return a string or array from a controller method,
	 * it will be automatically added to the response object.
	 *
	 * @param Request $request The request object.
	 * @param Response $response The response object.
	 */
	public function show( Request $request, Response $response ) {
		return template( 'hello', [
			'name' => 'Friend'
		] );
	}
}
