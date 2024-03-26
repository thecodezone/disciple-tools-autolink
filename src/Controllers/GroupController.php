<?php

namespace DT\Autolink\Controllers;

use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Illuminate\Http\Response;

class GroupController {
	/**
	 * Performs an index operation.
	 *
	 * @param Request $request The request object.
	 * @param Response $response The response object.
	 *
	 * @return mixed The result of the index operation.
	 */
	public function index( Request $request, Response $response ) {
		$limit  = $request->get( 'limit', 10 );
		$offset = $request->get( 'offset', 0 );

		$result = \DT_Posts::list_posts( 'groups', [
			'assigned_to' => [ get_current_user_id() ],
			'limit'       => $limit,
			'offset'      => $offset,
			'sort'        => '-last_modified'
		], false );

		if ( ! $result ) {
			$response->setStatusCode( 400 );
			return $response;
		}

		$result['posts'] = array_map( function ( $church ) {
			foreach ( $church as $key => $value ) {
				if ( is_array( $value ) && isset( $value['timestamp'] ) ) {
					$church[ $key ]['formatted'] = dt_format_date( $value['timestamp'], get_option( 'date_format' ) );
				}
			}

			return $church;
		}, $result['posts'] ?? [] );
		$result['total'] = $result['total'] ?? 0;

		return $result;
	}
}
