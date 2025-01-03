<?php

namespace DT\Autolink\Controllers;

use DT\Autolink\GuzzleHttp\Psr7\Request;
use function DT\Autolink\response;
use function DT\Autolink\route_url;
use function DT\Autolink\template;
use function DT\Autolink\extract_request_input;

class AppController {
	public function show( Request $request ) {
		$limit              = 10;
		$params = extract_request_input( $request );

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

			return response( $church );
		}, $churches['posts'] ?? [] );

		$churches['total'] = $churches['total'] ?? 0;

		$group_url = route_url( 'groups' );

		$error = $params['e'] ?? null;

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
		return template( 'app', compact(
			'limit',
			'churches',
			'group_url',
			'error',
			'group_fields',
			'church_fields',
			'church_health_field',
			'church_count_fields'
		) );
	}
}
