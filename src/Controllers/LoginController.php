<?php

namespace DT\Autolink\Controllers;

use DT\Autolink\CodeZone\WPSupport\Router\ServerRequestFactory;
use DT\Autolink\GuzzleHttp\Psr7\Request;
use DT\Autolink\GuzzleHttp\Psr7\Response;
use DT\Autolink\Psr\Http\Message\ResponseInterface;
use DT\Autolink\Services\Analytics;
use DT_Login_Fields;
use function DT\Autolink\container;
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
     * LoginController constructor.
     * This constructor initializes the login controller.
     * It also sets up the login failed action and removes the default login redirect filter.
     * @return void
     */
    public function __construct()
    {
        if ( !class_exists( 'DT_Login_Fields' ) || ( DT_Login_Fields::get( 'login_enabled' ) === 'on' ) ) {
            add_action( 'wp_login_failed', [ $this, 'dt_home_login_failed' ], 9, 1 );
        }
    }

    /**
     * Redirect the user to the login page with a failed login message.
     * This method redirects the user to the login page with a failed login message.
     * It is triggered when the login fails.
     * @return void
     */
    public function dt_home_login_failed()
    {
      wp_redirect( route_url( 'login' ) . '?login=failed' );
      exit;
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

        $analytics = container()->get( Analytics::class );
        $analytics->event( 'login', [ 'action' => 'start', 'lib_name' => __CLASS__ ] );

		$input = extract_request_input( $request );
		$username = $input['username'] ?? '';
		$password = $input['password'] ?? '';

		$user = wp_authenticate( $username, $password );

        $analytics->event( 'login', [ 'action' => 'stop' ] );

		if ( is_wp_error( $user ) ) {
			//phpcs:ignore
			$errors = $user;
			$error  = $errors->get_error_message();
			$error  = apply_filters( 'login_errors', $error );

			//If the error links to lost password, inject the 3/3rds redirect
			$error = str_replace( '?action=lostpassword', '?action=lostpassword?&redirect_to=/', $error );

            $analytics->event( 'login-error', [ 'action' => 'snapshot', 'lib_name' => __CLASS__, 'attributes' => [ 'error' => 'wp_error' ] ] );

            return $this->show_error( $error );
		}

		wp_set_auth_cookie( $user->ID );

		if ( ! $user ) {
            $analytics->event( 'login-error', [ 'action' => 'snapshot', 'lib_name' => __CLASS__, 'attributes' => [ 'error' => 'invalid_user' ] ] );

			return $this->show_error( __( 'An unexpected error has occurred.', 'disciple-tools-autolink' ) );
		}

		wp_set_current_user( $user->ID );

		return redirect( "/autolink" );
	}

    /**
     * Show the login page with an error.
     *
     * @param string $error The error message.
     * @param array $params Additional parameters for the request.
     * @param string $method The HTTP method for the request.
     * @param string $endpoint The endpoint to send the request to.
     * @param array $headers The headers for the request.
     * @return Response The response of the request.
     */
    private function show_error($error, $params = [], $method = "GET", $endpoint = "", $headers = [
        'Content-Type' => 'application/HTML',
    ]): ResponseInterface
    {
        $params = array_merge( $params, [ 'error' => $error ] );
        if ( !empty( $endpoint ) ) {
            $endpoint = route_url( 'login' );
        }
        return $this->login( ServerRequestFactory::request( $method, $endpoint, $params, $headers ) );
    }

	/**
	 * Renders the login template with the provided parameters.
	 *
	 * @param Request $request The request object.
	 *
	 * @return Response The response object.
	 */
	public function login( Request $request ) {

        $params = extract_request_input( $request );
        // If the login failed, display an error message.
        if ( $params['login'] ?? null === 'failed' && !array_key_exists( 'error', $params ) ) {
            $params['error'] = __( 'ERROR: Invalid username/password combination. Lost your password?', 'disciple-tools-autolink' );
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

        container()->get( Analytics::class )->event( __FUNCTION__, [ 'action' => 'snapshot', 'lib_name' => __CLASS__ ] );

		return redirect( route_url( 'login' ) );
	}
}
