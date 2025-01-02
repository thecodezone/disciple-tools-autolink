<?php

namespace DT\Autolink\Controllers;

use DT\Autolink\GuzzleHttp\Psr7\Request;
use DT\Autolink\Services\Charts\TreeChart;
use DT_Posts;
use function DT\Autolink\extract_request_input;
use function DT\Autolink\group_label;
use function DT\Autolink\groups_label;
use function DT\Autolink\response;
use function DT\Autolink\route_url;
use function DT\Autolink\template;

/**
 * Class CoachingTreeController
 *
 * The CoachingTreeController class is responsible for managing the coaching tree functionality.
 */
class CoachingTreeController {
	private TreeChart $tree_chart;

	public function __construct( TreeChart $tree_chart ) {
		$this->tree_chart = $tree_chart;
	}

	/**
	 * Retrieves the coaching tree template.
	 *
	 * This function fetches the coaching tree template from the API and returns it.
	 * The template includes various translations used in the tree display.
	 *
	 * @return string The coaching tree template.
	 */
	public function show() {
		$fetch_url    = route_url( 'api/tree' );
		$tree_label = __( 'Tree', 'disciple-tools-autolink' );
		$translations = [
			'tree_title'        => groups_label() . ' ' . $tree_label,
			'unassigned_title'  => __( 'Unassigned', 'disciple-tools-autolink' ) . ' ' . groups_label(),
			'unassigned_tip'    => __( 'Move these to the', 'disciple-tools-autolink' ) . ' ' . group_label() . ' ' . $tree_label . ' ' . __( 'to assign them to a', 'disciple-tools-autolink' ) . ' ' . group_label() . '.',
			'key_title'         => __( 'Key', 'disciple-tools-autolink' ),
			'assigned_label'    => groups_label() . ' ' . __( 'you are assigned', 'disciple-tools-autolink' ),
			'coached_label'     => groups_label() . ' ' . __( 'assigned to those you coach', 'disciple-tools-autolink' ),
			'leading_label'     => groups_label() . ' ' . __( 'you lead', 'disciple-tools-autolink' ),
			'generation_label'  => __( 'Generation Number', 'disciple-tools-autolink' ),
			'no_groups_message' => __( 'No ', 'disciple-tools-autolink' ) . ' ' . Str::lower( groups_label() ) . ' found.',
		];

		return template( 'coaching-tree', compact( 'fetch_url', 'translations' ) );
	}

	/**
	 * Retrieves the index of the coaching tree.
	 *
	 * This method returns the index of the coaching tree by calling the tree() method
	 * from the `$tree_chart` object. The index represents the hierarchy and structure of
	 * the coaching tree.
	 *
	 * @return array The index of the coaching tree.
	 */
	public function index() {
		return $this->tree_chart->tree();
	}

	/**
	 * Updates the group parent in the database.
	 *
	 * This function is used to update the parent of a group in the database.
	 * It takes a Request object and a Response object as parameters.
	 * The function retrieves the necessary data from the Request object,
	 * performs the necessary database queries to update the parent of the group,
	 * and returns the result information in an array.
	 *
	 * @param Request $request The Request object.
	 */
	public function update( Request $request ) {
		global $wpdb;

		$user_id = get_current_user_id();
		$params = extract_request_input( $request );
		$params  = dt_recursive_sanitize_array( $params );

		if ( ! isset( $params['previous_parent'] ) ) {
			$params['previous_parent'] = 'root';
		}
		if ( ( ! isset( $params['new_parent'] ) || ( ! isset( $params['self'] ) ) ) ) {
			return response( $params, 400 );
		}

		$group     = DT_Posts::get_post( 'groups', $params['self'], true, false );
		$new_group = ( $params['new_parent'] != 'root' ) ? DT_Posts::get_post( 'groups', $params['new_parent'], true, false ) : null;

		if ( ! $new_group
		     && ( (int) $group["assigned_to"]["id"] !== $user_id ) ) {

			return response( [
				'success' => false,
				'message' => __( 'You do not have permission to move this group.', 'disciple-tools-autolink' ),
			], 403 );

		}

		if ( $params['previous_parent'] ) {
			$wpdb->query( $wpdb->prepare(
				"DELETE
                FROM $wpdb->p2p
                WHERE p2p_from = %s
                    AND p2p_to = %s
                    AND p2p_type = 'groups_to_groups'",
				(string) $params['self'],
				(string) $params['previous_parent']
			) );
		}

		if ( $params['new_parent'] && $params['new_parent'] !== 'root' ) {
			$result = $wpdb->query( $wpdb->prepare(
				"INSERT INTO $wpdb->p2p (p2p_from, p2p_to, p2p_type)
                    VALUES (%s, %s, 'groups_to_groups');
            ",
				(string) $params['self'],
				(string) $params['new_parent']
			) );


			if ( ! $result ) {
				return response( [
					'success' => false,
					'message' => __( 'An error occurred while moving the group.', 'disciple-tools-autolink' ),
				], 500 );
			}
		}

		return [
			'success' => true,
			'message' => __( 'Group moved successfully.', 'disciple-tools-autolink' ),
		];
	}
}
