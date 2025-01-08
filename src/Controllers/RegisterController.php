<?php

namespace DT\Autolink\Controllers;

use DT\Autolink\GuzzleHttp\Psr7\Request;
use function DT\Autolink\extract_request_input;
use function DT\Autolink\redirect;
use function DT\Autolink\route_url;
use function DT\Autolink\template;

class RegisterController {

	/**
	 * Process the register form
	 */
	public function process( Request $request ) {

		$input = extract_request_input( $request );
		$username         = $input['username'] ?? [];
		$email            = $input['email'] ?? '';
		$password         = $input['password'] ?? '';
		$confirm_password = $input['confirm_password'] ?? '';

		if ( ! $username || ! $password || ! $email ) {
			return $this->register( [
				'error'    => 'Please fill out all fields.',
				'username' => $username,
				'email'    => $email,
				'password' => $password
			] );
		}

		if ( $confirm_password !== $password ) {
			return $this->register( [
				'error'    => 'Passwords do not match',
				'username' => $username,
				'email'    => $email,
				'password' => $password
			] );
		}

		$user = wp_create_user( $username, $password, $email );

		if ( is_wp_error( $user ) ) {
			$error = $user->get_error_message();

			return $this->register( [ 'error' => $error ] );
		}

		$user_obj = get_user_by( 'id', $user );
		wp_set_current_user( $user );
		wp_set_auth_cookie( $user_obj->ID );


		if ( ! $user ) {
			return $this->register( [ 'error' => esc_html_e( 'An unexpected error has occurred.', 'dt_home' ) ] );
		}

		return redirect( '/autolink' );
	}

	/**
	 * Show the register template
	 */
	public function register( $params = [] ) {
        if (!is_array($params)) {
            $params = is_object($params) ? (array) $params : [];
        }
		$form_action = route_url( 'register' );
		$login_url   = route_url( 'login' );
		$error       = $params['error'] ?? '';
		$username    = $params['username'] ?? '';
		$email       = $params['email'] ?? '';
		$password    = $params['password'] ?? '';

		return template( 'auth/register', [

			'form_action' => $form_action,
			'login_url'   => $login_url,
			'username'    => $username,
			'email'       => $email,
			'password'    => $password,
			'error'       => $error
		] );
	}
}
