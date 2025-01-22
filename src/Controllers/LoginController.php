<?php

namespace DT\Autolink\Controllers;

use DT\Autolink\GuzzleHttp\Psr7\Request;
use DT\Autolink\Services\Analytics;
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

    private Analytics $analytics;

    public function __construct( Analytics $analytics ) {
        $this->analytics = $analytics;
    }

	/**
	 * Processes the login request.
	 *
	 * @param Request $request The request object.
	 *
	 * @return Response The response object.
	 */
	public function process( Request $request ) {
		global $errors;

        $this->analytics->event( 'login', [ 'action' => 'start', 'lib_name' => __CLASS__ ] );

		$input = extract_request_input( $request );
		$username = $input['username'] ?? '';
		$password = $input['password'] ?? '';

		$user = wp_authenticate( $username, $password );

        $this->analytics->event( 'login', [ 'action' => 'stop' ] );

		if ( is_wp_error( $user ) ) {
			//phpcs:ignore
			$errors = $user;
			$error  = $errors->get_error_message();
			$error  = apply_filters( 'login_errors', $error );

			//If the error links to lost password, inject the 3/3rds redirect
			$error = str_replace( '?action=lostpassword', '?action=lostpassword?&redirect_to=/', $error );

            $this->analytics->event( 'login-error', [ 'action' => 'snapshot', 'lib_name' => __CLASS__, 'attributes' => [ 'error' => 'wp_error' ] ] );

			return $this->login( [ 'error' => $error, 'username' => $username, 'password' => $password ] );
		}

		wp_set_auth_cookie( $user->ID );

		if ( ! $user ) {
            $this->analytics->event( 'login-error', [ 'action' => 'snapshot', 'lib_name' => __CLASS__, 'attributes' => [ 'error' => 'invalid_user' ] ] );

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

        if (!is_array($params)) {
            $params = is_object($params) ? (array) $params : [];
        }
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

        $this->analytics->event( __FUNCTION__, [ 'action' => 'snapshot', 'lib_name' => __CLASS__ ] );

		return redirect( route_url( 'login' ) );
	}
}
