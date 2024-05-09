<?php

namespace DT\Autolink\Controllers;

use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Illuminate\Http\Response;
use DT\Autolink\Plugin;
use function DT\Autolink\redirect;
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
	 * @param Response $response The response object.
	 *
	 * @return Response The response object.
	 */
	public function process( Request $request, Response $response ) {
		global $errors;

		$username = $request->input( 'username' ?? '' );
		$password = $request->input( 'password' ?? '' );

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

        $churches           = \DT_Posts::list_posts( 'groups', [
           'assigned_to' => [ get_current_user_id() ],
           'sort'        => '-post_date'
       ], false );

        if ($churches['total'] > 0) {
            return redirect( "/autolink/genmap/" );
              // $response = new RedirectResponse( "/autolink/genmap/", 302 );

        }else{
            return redirect( "/autolink" );
             //  $response = new RedirectResponse( "/autolink", 302 );
        }

		// return redirect( "/autolink" );
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
		$register_url = '/autolink/register';
		$form_action  = '/autolink/login';
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
	 *
	 * @return \DT\Autolink\Illuminate\Http\RedirectResponse The response object.
	 */
	public function logout( $params = [] ) {
		wp_logout();

		return redirect( '/autolink/login' );
	}
}
