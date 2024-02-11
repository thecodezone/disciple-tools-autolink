<?php

namespace Tests;

use DT\Plugin\CodeZone\Router\Middleware\HandleErrors;
use DT\Plugin\CodeZone\Router\Middleware\HandleRedirects;
use DT\Plugin\CodeZone\Router\Middleware\Render;
use DT\Plugin\CodeZone\Router\Middleware\Stack;
use DT\Plugin\Illuminate\Http\Request;
use WP_UnitTestCase;
use function DT\Plugin\container;
use function DT\Plugin\namespace_string;

abstract class TestCase extends WP_UnitTestCase {
	/**
	 * Set up the test case by starting a transaction and calling the parent's setUp method.
	 *
	 * This method is called before each test method.
	 * It starts a transaction using the global $wpdb object and then calls the parent's setUp method.
	 *
	 * @return void
	 */
	public function setUp(): void {
		global $wpdb;
		$wpdb->query( 'START TRANSACTION' );
		parent::setUp();
	}

	/**
	 * The tearDown method is used to clean up any resources or connections after each test case is executed.
	 * In this specific case, it performs a rollback in the database using the global $wpdb variable of WordPress.
	 * It then calls the tearDown method of the parent class to ensure any additional cleanup tasks are performed.
	 * @return void
	 */
	public function tearDown(): void {
		global $wpdb;
		$wpdb->query( 'ROLLBACK' );
		parent::tearDown();
	}

	/**
	 * Sends a GET request to the specified URI with optional parameters and headers.
	 *
	 * @param string $uri The URI to send the GET request to.
	 * @param mixed $parameters The optional parameters to include in the GET request.
	 * @param array $headers The optional headers to include in the GET request.
	 *
	 * @return mixed The response returned from the GET request.
	 */
	public function get( $uri, $parameters, array $headers = [] ) {
		return $this->request( 'GET', $uri, $parameters, $headers );
	}

	/**
	 * Performs a request using the given method and URI.
	 *
	 * @param string $method The HTTP method to use for the request.
	 * @param string $uri The URI to request.
	 * @param array $parameters An array of parameters to send with the request (optional).
	 * @param array $headers An array of headers to include in the request (optional).
	 * @param array $cookies An array of cookies to send with the request (optional).
	 * @param array $files An array of files to send with the request (optional).
	 * @param array $server An array of server variables to include in the request (optional).
	 * @param mixed $content The content to include in the request body (optional).
	 *
	 * @return mixed The response from the request.
	 */
	public function request( $method, $uri, array $parameters = [], $headers = [], array $cookies = [], array $files = [], array $server = [], $content = null ) {
		$initial_request = container()->make( Request::class );
		$request         = Request::create( $uri, $method, $parameters, $cookies, $files, $server, $content );
		foreach ( $headers as $key => $value ) {
			$request->headers->set( $key, $value );
		}
		$blacklisted_middleware = [
			HandleErrors::class,
			HandleRedirects::class,
			Render::class
		];

		container()->bind( Request::class, function () use ( $request ) {
			return $request;
		} );

		add_filter( namespace_string( 'middleware' ), function ( $stack ) use ( $blacklisted_middleware ) {
			return $stack->filter( function ( $middleware ) use ( $blacklisted_middleware ) {
				return ! in_array( $middleware, $blacklisted_middleware );
			} );
		} );
		$stack    = apply_filters( namespace_string( 'middleware' ), container()->make( Stack::class ) );
		$response = $stack->run();

		container()->bind( Request::class, function () use ( $initial_request ) {
			return $initial_request;
		} );

		return $response;
	}

	/**
	 * Send a POST request to the specified URI with the given data and headers.
	 *
	 * @param string $uri The URI to send the request to.
	 * @param array $data An array of data to include in the request body.
	 * @param array $headers An array of headers to include in the request.
	 *
	 * @return mixed The response from the request.
	 */
	public function post( $uri, array $data = [], array $headers = [] ) {
		return $this->request( 'POST', $uri, $data, $headers );
	}
}
