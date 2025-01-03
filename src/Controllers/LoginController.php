<?php

namespace DT\Autolink\Controllers;

use DT\Autolink\GuzzleHttp\Psr7\Request;
use function DT\Autolink\extract_request_input;
use function DT\Autolink\redirect;
use function DT\Autolink\route_url;
use function DT\Autolink\template;
use function DT\Autolink\plugin_url;

/**
 * Class LoginController
 *
 * This class handles user login and authentication.
 */
class LoginController {

	/**
	 * Processes the login request.
	 *
	 * @param Request $request The request object.
	 *
	 * @return Response The response object.
	 */
	public function process( Request $request ) {
		global $errors;

		$input = extract_request_input( $request );
		$username = $input['username'] ?? '';
		$password = $input['password'] ?? '';

		$user = wp_authenticate( $username, $password );

		if ( is_wp_error( $user ) ) {
			//phpcs:ignore
			$errors = $user;
			$error  = $errors->get_error_message();
			$error  = apply_filters( 'login_errors', $error );

			//If the error links to lost password, inject the 3/3rds redirect
			$error = str_replace( '?action=lostpassword', '?action=lostpassword?&redirect_to=/', $error );

			return $this->login( [ 'error' => $error, 'username' => $username, 'password' => $password ] );
		}

		wp_set_auth_cookie( $user->ID );

		if ( ! $user ) {
			return $this->login( [ 'error' => esc_html_e( 'An unexpected error has occurred.', 'dt_home' ) ] );
		}

		wp_set_current_user( $user->ID );

		return redirect( "/autolink" );
	}

	/**
	 * Renders the login template with the provided parameters.
	 *
	 * @param array $params {
	 *     An array of parameters.
	 *
	 * @type string $username The username input value. Default empty string.
	 * @type string $password The password input value. Default empty string.
	 * @type string $error The error message to display. Default empty string.
	 * }
	 *
	 * @return Response The response object.
	 */
	public function login( $params = [] ) {
		$register_url = route_url( 'register' );
		$form_action  = route_url( 'login' );
		$username     = $params['username'] ?? '';
		$password     = $params['password'] ?? '';
		$error        = $params['error'] ?? '';
		$reset_url    = wp_lostpassword_url( plugin_url() );

		return template( 'auth/login', [
			'register_url' => $register_url,
			'form_action'  => $form_action,
			'username'     => $username,
			'password'     => $password,
			'reset_url'    => $reset_url,
			'error'        => $error
		] );
	}

	/**
	 * Logs the user out and redirects them to the login page.
	 *
	 * @param array $params Additional parameters (optional).
	 * */
	public function logout( $params = [] ) {
		wp_logout();

		return redirect( route_url( 'login' ) );
	}
}
