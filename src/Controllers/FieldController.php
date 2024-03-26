<?php

namespace DT\Autolink\Controllers;

use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Illuminate\Http\Response;
use DT_Posts;
use Exception;
use function DT\Autolink\namespace_string;

class FieldController {
	/**
	 * Update a post.
	 *
	 * @param Request $request The request object.
	 * @param Response $response The response object.
	 *
	 * @return mixed The updated post or an error message.
	 */
	public function update( Request $request, Response $response ) {
		$body      = $request->all();
		$whitelist = apply_filters( namespace_string( 'updatable_group_fields' ), [
			'health_metrics',
			'member_count',
			'leader_count',
			'believer_count',
			'baptized_count',
			'baptized_in_group_count'
		] );
		if ( ! isset( $body['id'] ) || ! isset( $body['value'] ) ) {
			return $response->setStatusCode( 400 )->setContent( [ "message" => "Invalid request" ] );
		}

		$id         = sanitize_key( wp_unslash( $body['id'] ) );
		$field_info = explode( "_", $id );

		if ( ! is_array( $field_info ) || count( $field_info ) < 3 ) {
			return $response->setStatusCode( 400 )->setContent( [ "message" => "Invalid request" ] );
		}

		$post_type = array_shift( $field_info );
		$id        = array_shift( $field_info );
		$field     = implode( "_", $field_info );

		$value = wp_unslash( $body['value'] );
		if ( ! is_array( $value ) ) {
			$value = sanitize_text_field( $value );
		}

		$is_allowed = in_array( $field, $whitelist );

		if ( ! $is_allowed ) {
			return $response->setStatusCode( 400 )->setContent( [ "message" => "Invalid request" ] );
		}

		$payload = [
			$field => $value
		];

		try {
			$result = DT_Posts::update_post( $post_type, $id, $payload );
		} catch ( Exception $e ) {
			return $response->setStatusCode( 500 )->setContent( [ "message" => $e->getMessage() ] );
		}


		if ( ! is_wp_error( $result ) ) {
			return $result;
		}

		return $response->setStatusCode( 500 )->setContent( [ "message" => $result->get_error_message() ] );
	}
}
