<?php

namespace DT\Plugin\Factories;

use DT\Plugin\Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use WP_Error;
use WP_HTTP_Response;
use WP_REST_Response;
use function DT\Plugin\is_json;

/**
 * Class ResponseFactory
 */
class ResponseFactory {
	protected $container;

	public function __construct( Container $container ) {
		$this->container = $container;
	}

	/**
	 * Create a new response instance from the given value.
	 *
	 * @param null $value
	 * @param WP_HTTP_Response|null $response
	 *
	 * @return WP_HTTP_Response
	 */
	public function make( $value = null, WP_HTTP_Response|null $response = null ): WP_HTTP_Response {
		add_filter( 'dt/plugin/response', [ $this, 'convert_to_rest_response' ] );

		return apply_filters( 'dt/plugin/response', $this->map_response( $value, $response ) );
	}

	/**
	 * Map the given value to a response.
	 *
	 * @param null $value
	 * @param WP_HTTP_Response|null $response
	 *
	 * @return WP_HTTP_Response
	 * @throws BindingResolutionException
	 */
	private function map_response( $value = null, WP_HTTP_Response|null $response = null ): WP_HTTP_Response {
		if ( $value instanceof WP_HTTP_Response ) {
			return $value;
		}

		if ( ! $response ) {
			$response = $this->container->make( WP_HTTP_Response::class );
		}

		if ( is_numeric( $value ) || is_string( $value ) || is_array( $value ) ) {
			$response->set_data( $value );
		}

		if ( $value instanceof WP_Error ) {
			$response->set_status( $value->get_error_code() );
			$response->set_data( $value->get_error_message() );
		}

		return $response;
	}

	/**
	 * Convert a JSON response to a WP_REST_Response.
	 *
	 * @param $response
	 *
	 * @return WP_HTTP_Response
	 */
	public function convert_to_rest_response( $response ): WP_HTTP_Response {
		if ( is_string( $response->get_data() ) && is_json( $response->get_data() ) ) {
			$response->set_data( json_decode( $response->get_data(), true ) );
		}

		if ( is_array( $response->get_data() ) && ! $response instanceof WP_REST_Response ) {
			$response = new WP_REST_Response( $response->get_data(), $response->get_status(), $response->get_headers() );
		}

		return $response;
	}
}