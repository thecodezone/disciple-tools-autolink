<?php

namespace DT\Autolink\Controllers;

use DT\Autolink\GuzzleHttp\Psr7\Request;
use DT_Posts;
use Exception;
use function DT\Autolink\namespace_string;
use function DT\Autolink\response;
class FieldController {
	/**
	 * Update a post.
	 *
	 * @param Request $request The request object.
	 */
	public function update( Request $request ) {
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
			return response( [ "message" => "Invalid request" ], 400 );
		}

		$id         = sanitize_key( wp_unslash( $body['id'] ) );
		$field_info = explode( "_", $id );

		if ( ! is_array( $field_info ) || count( $field_info ) < 3 ) {
			return response( [ "message" => "Invalid request" ], 400 );
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
			return response( [ "message" => "Invalid request" ], 400 );
		}

		$payload = [
			$field => $value
		];

		try {
			$result = DT_Posts::update_post( $post_type, $id, $payload );
		} catch ( Exception $e ) {
			return response( [ "message" => "Invalid request" ], 500 );
		}


		if ( ! is_wp_error( $result ) ) {
			return $result;
		}

		return response( [ "message" => "Invalid request" ], 500 );
	}
}
