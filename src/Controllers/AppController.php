<?php

namespace DT\Autolink\Controllers;

use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Illuminate\Http\Response;
use DT\Autolink\Services\Options;
use function DT\Autolink\route_url;
use function DT\Autolink\template;

class AppController {
	public function show( Request $request, Response $response, Options $options ) {
		$limit              = 10;
		$churches           = \DT_Posts::list_posts( 'groups', [
			'assigned_to' => [ get_current_user_id() ],
			'limit'       => $limit,
			'sort'        => '-post_date'
		], false );

		//Apply WP formatting to all date fields.
		$churches['posts'] = array_map( function ( $church ) {
			foreach ( $church as $key => $value ) {
				if ( is_array( $value ) && isset( $value['timestamp'] ) ) {
					$church[ $key ]['formatted'] = dt_format_date( $value['timestamp'], get_option( 'date_format' ) );
				}
			}

			return $church;
		}, $churches['posts'] ?? [] );

		$churches['total'] = $churches['total'] ?? 0;

		$group_url = route_url( 'groups' );

		$error = $request->input( 'e', false );

		if ( is_wp_error( $churches ) ) {
			$churches = [];
		}

		$group_fields                = \DT_Posts::get_post_field_settings( 'groups' );
		$church_fields               = [
			'health_metrics' => $group_fields['health_metrics']['default'] ?? [],
		];
		$church_health_field         = $church_fields['health_metrics'];
		$allowed_church_count_fields = [
			'member_count',
			'leader_count',
			'believer_count',
			'baptized_count',
			'baptized_in_group_count'
		];
		$church_count_fields         = [];

		foreach ( $allowed_church_count_fields as $field ) {
			//Fields can be registered or deregistered by plugins,so check and make sure it exists
			if ( isset( $group_fields[ $field ] ) && ( ! isset( $group_fields[ $field ]['hidden'] ) || ! $group_fields[ $field ]['hidden'] ) ) {
				$church_count_fields[ $field ] = $group_fields[ $field ];
			}
		}
		$response->setContent( template( 'app', compact(
			'limit',
			'churches',
			'group_url',
			'error',
			'group_fields',
			'church_fields',
			'church_health_field',
			'church_count_fields'
		) ) );
		return $response;
	}

	public function data( Request $request, Response $response ) {
		$user = wp_get_current_user();
		$data = [
			'user_login' => $user->user_login,
		];
		$response->setContent( $data );

		return $response;
	}
}