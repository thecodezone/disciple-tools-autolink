<?php

class Disciple_Tools_Autolink_App_Controller extends Disciple_Tools_Autolink_Controller {
	/**
	 * Show the app template
	 */
	public function show( $params = [] ) {
		$data = $this->global_data();
		extract( $data );

		$post_type    = get_post_type_object( 'groups' );
		$group_labels = get_post_type_labels( $post_type );

		$action                  = '';
		$delete_group_nonce      = wp_create_nonce( 'dt_autolink_delete_group' );
		$delete_group_link       = $this->functions->get_app_link() . '?action=delete-group&_wpnonce=' . $delete_group_nonce;
		$delete_group_label      = __( 'Delete', 'disciple-tools-autolink' ) . ' app.php' . $group_labels->singular_name;
		$delete_group_confirm    = __( 'Are you sure you want to delete this ', 'disciple-tools-autolink' ) . $group_labels->singular_name . '?';
		$edit_group_label        = __( 'Edit', 'disciple-tools-autolink' ) . ' app.php' . $group_labels->singular_name;
		$view_group_label        = __( 'View', 'disciple-tools-autolink' ) . ' app.php' . $group_labels->singular_name;
		$group_link              = $this->functions->get_app_link() . '?action=group';
		$app_link                = $this->functions->get_app_link();
		$church_start_date_label = __( 'Church Start Date', 'disciple-tools-autolink' );
		$churches                = DT_Posts::list_posts( 'groups', [
			'assigned_to' => [ get_current_user_id() ],
		], false )['posts'] ?? [];

		$error = $params['error'] ?? false;

		if ( is_wp_error( $churches ) ) {
			$churches = [];
		}

		usort( $churches, function ( $a, $b ) {
			return $a['last_modified'] < $b['last_modified'] ? 1 : - 1;
		} );

		//Apply WP formatting to all date fields.
		$churches = array_map( function ( $church ) {
			foreach ( $church as $key => $value ) {
				if ( is_array( $value ) && isset( $value['timestamp'] ) ) {
					$church[ $key ]['formatted'] = dt_format_date( $value['timestamp'], get_option( 'date_format' ) );
				}
			}

			return $church;
		}, $churches );

		$group_fields                = DT_Posts::get_post_field_settings( 'groups' );
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
			//Fields can registered or deregistered by plugins,so check and make sure it exists
			if ( isset( $group_fields[ $field ] ) && ( ! isset( $group_fields[ $field ]['hidden'] ) || ! $group_fields[ $field ]['hidden'] ) ) {
				$church_count_fields[ $field ] = $group_fields[ $field ];
			}
		}

		include __DIR__ . '/../templates/app.php';
	}
}
