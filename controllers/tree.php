<?php

use jobs\DiscipleToolsAutolinkSaveTreeJob;

class Disciple_Tools_Autolink_Tree_Controller extends Disciple_Tools_Autolink_Controller {
	const NONCE = 'dt_autolink_tree';
	private $tree_chart = null;

	public function __construct() {
		$this->functions  = Disciple_Tools_Autolink_Magic_Functions::instance();
		$this->tree_chart = Disciple_Tools_Autolink_Groups_Tree::instance();
	}

	public function show( $params = [] ) {
		$magic_link = Disciple_Tools_Autolink_Magic_User_App::instance();
		$data       = $this->global_data();
		extract( $data );
		$action       = 'tree';
		$fetch_url    = '/wp-json/autolink/v1/' . $magic_link->parts['type'];
		$parts        = $magic_link->parts;
		$translations = [
			'tree_title'       => $church_label . ' ' . $tree_label,
			'unassigned_title' => __( 'Unassigned', 'disciple-tools-autolink' ) . ' ' . $churches_label,
			'unassigned_tip'   => __( 'Move these to the', 'disciple-tools-autolink' ) . ' ' . $church_label . ' ' . $tree_label . __( 'to assign them to a', 'disciple-tools-autolink' ) . ' ' . $church_label . '.',
			'key_title'        => __( 'Key', 'disciple-tools-autolink' ),
			'assigned_label'   => $churches_label . ' ' . __( 'you lead', 'disciple-tools-autolink' ),
		];

		include __DIR__ . '/../templates/tree.php';
	}

	public function process( WP_REST_Request $request, $params, $user_id ) {
		global $wpdb;

		if ( ! isset( $params['data']['previous_parent'] ) ) {
			$params['data']['previous_parent'] = 'root';
		}
		if ( ( ! isset( $params['data']['new_parent'] ) || ( ! isset( $params['data']['self'] ) ) ) ) {
			return 'false';
		}

		$group     = DT_Posts::get_post( 'groups', $params['data']['self'], true, false );
		$new_group = ( $params['data']['new_parent'] != 'root' ) ? DT_Posts::get_post( 'groups', $params['data']['new_parent'], true, false ) : null;

		if ( ! $new_group
		     && ( (int) $group["assigned_to"]["id"] !== $user_id ) ) {
			return "reload";
		}

		$wpdb->query( "START TRANSACTION" );

		if ( $params['data']['previous_parent'] ) {
			$wpdb->query( $wpdb->prepare(
				"DELETE
                FROM $wpdb->p2p
                WHERE p2p_from = %s
                    AND p2p_to = %s
                    AND p2p_type = 'groups_to_groups'",
				$params['data']['self'],
				$params['data']['previous_parent']
			) );
		}


		if ( $params['data']['new_parent'] && $params['data']['new_parent'] !== 'root' ) {
			$response = $wpdb->query( $wpdb->prepare(
				"INSERT INTO $wpdb->p2p (p2p_from, p2p_to, p2p_type)
                    VALUES (%s, %s, 'groups_to_groups');
            ",
				$params['data']['self'],
				$params['data']['new_parent']
			) );


			if ( ! $response ) {
				$wpdb->query( "ROLLBACK" );

				return false;
			}
		}

		$wpdb->query( "COMMIT" );

		return true;
	}

	public function data( WP_REST_Request $request, $params, $user_id ) {
		echo wp_json_encode( $this->tree_chart->tree() );
	}
}
