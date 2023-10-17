<?php

class Disciple_Tools_Autolink_Group_Controller extends Disciple_Tools_Autolink_Controller {
	const NONCE = 'dt_autolink_group';

	/**
	 * Show the edit group form
	 */
	public function edit( $params = [] ) {
		$group_id         = sanitize_key( wp_unslash( $_GET['post'] ?? "" ) );
		$params['action'] = $this->functions->get_edit_group_url();

		if ( ! $group_id ) {
			$this->functions->redirect_to_app();
			exit;
		}
		$this->form( $params );
	}

	/**
	 * Show the edit/create group form
	 * Expects $params['action'] to be set
	 */
	private function form( $params = [] ) {
		if ( ! isset( $params['action'] ) ) {
			$this->functions->redirect_to_app();
			exit;
		}
		$group    = null;
		$group_id = sanitize_key( wp_unslash( $_GET['post'] ?? $params['post'] ?? null ) );
		if ( $group_id ) {
			$group = DT_Posts::get_post( 'groups', $group_id, true, false );
			if ( ! $group || is_wp_error( $group ) ) {
				$this->functions->redirect_to_app();
				exit;
			}
		}

		$magic_link = Disciple_Tools_Autolink_Magic_User_App::instance();

		DT_Mapbox_API::load_mapbox_header_scripts();
		DT_Mapbox_API::load_mapbox_search_widget();

		wp_localize_script(
			'mapbox-search-widget', 'dtMapbox', [
			'post_type'      => 'groups',
			'post_id'        => $group_id ?? 0,
			'post'           => $group ?? false,
			'map_key'        => DT_Mapbox_API::get_key(),
			'mirror_source'  => dt_get_location_grid_mirror( true ),
			'google_map_key' => ( class_exists( 'Disciple_Tools_Google_Geocode_API' ) && Disciple_Tools_Google_Geocode_API::get_key() ) ? Disciple_Tools_Google_Geocode_API::get_key() : false,
			'spinner_url'    => get_stylesheet_directory_uri() . '/spinner.svg',
			'theme_uri'      => get_stylesheet_directory_uri(),
			'translations'   => [
				'add'             => __( 'add', 'disciple_tools' ),
				'use'             => __( 'Use', 'disciple_tools' ),
				'search_location' => __( 'Search Location', 'disciple_tools' ),
				'delete_location' => __( 'Delete Location', 'disciple_tools' ),
				'open_mapping'    => __( 'Open Mapping', 'disciple_tools' ),
				'clear'           => __( 'Clear', 'disciple_tools' )
			]
		] );

		$group_fields = DT_Posts::get_post_settings( 'groups' )['fields'];
		$post_type    = get_post_type_object( 'groups' );
		$group_labels = get_post_type_labels( $post_type );
		$group        = $group ?? [];
		$user         = wp_get_current_user();
		$contact_id   = Disciple_Tools_Users::get_contact_for_user( $user->ID, true );
		if ( $group ) {
			$heading = __( 'Edit', 'disciple-tools-autolink' ) . ' ' . $group_labels->singular_name;
		} else {
			$heading = __( 'Create', 'disciple-tools-autolink' ) . ' ' . $group_labels->singular_name;
		}
		$name_label                  = $group_fields['name']['name'];
		$name_placeholder            = $group_fields['name']['name'];
		$start_date_label            = $group_fields['start_date']['name'];
		$leaders_label               = $group_fields['leaders']['name'];
		$nonce                       = self::NONCE;
		$action                      = $params['action'];
		$cancel_url                  = $this->functions->get_app_link();
		$cancel_label                = __( 'Cancel', 'disciple-tools-autolink' );
		$submit_label                = __( 'Save', 'disciple-tools-autolink' );
		$error                       = $params['error'] ?? '';
		$name                        = sanitize_text_field( wp_unslash( $params['name'] ?? "" ) );
		$contacts                    = [ DT_Posts::get_post( 'contacts', $contact_id ) ];
		$leader_ids                  = $params['leaders'] ?? array_map( function ( $leaders ) {
			return (string) $leaders['ID'];
		}, $group['leaders'] ?? [] );
		$leader_options              = array_map( function ( $contact ) {
			return [
				'id'    => (string) $contact['ID'],
				'label' => $contact['name'],
			];
		}, $contacts );
		$parent_group_field_callback = '/wp-json/autolink/v1/' . $magic_link->parts['type'] . "?" . http_build_query( [
				'action' => 'parent_group_field',
				'parts'  => $magic_link->parts,
        ] );

		$show_location_field = DT_Mapbox_API::is_active_mapbox_key();

		if ( ! $name ) {
			$name = $group['name'] ?? '';
		}

		// phpcs:ignore
		$start_date = sanitize_text_field( wp_unslash( $_POST['start_date'] ?? "" ) );

		if ( ! $start_date ) {
			$start_date = $group['start_date'] ?? '';
		}

		if ( $start_date && is_array( $start_date ) ) {
			$start_date = $start_date ? dt_format_date( $start_date['timestamp'] ) : '';
		}

		include __DIR__ . '/../templates/group-form.php';
	}

	/**
	 * Ajax callback to get the parent group field.
	 * Renders when the leaders change.
	 * @return false|void
	 */
	public function parent_group_field() {
		$leaders = dt_recursive_sanitize_array( $_GET['leaders'] ?? [] );

		//Filter out new leaders
		$leaders = array_filter( $leaders, function ( $leader ) {
			return is_numeric( $leader );
		} );

		$leader_groups = DT_Posts::list_posts( 'groups', [
			'assigned_to' => $leaders,
		], false );


		$id                           = sanitize_text_field( wp_unslash( $_GET['id'] ?? '' ) );
		$group                        = $id ? DT_Posts::get_post( 'groups', $id, true, false ) : null;
		$default_parent_group         = count( $leader_groups['posts'] ) ? $leader_groups['posts'][0]['ID'] ?? null : null;
		$allow_parent_group_selection = $this->settings->get_option( 'disciple_tools_autolink_allow_parent_group_selection' );
		$allow_parent_group_selection = $allow_parent_group_selection === '1' || $allow_parent_group_selection === true;

		$parent_group_options = array_map( function ( $group ) {
			return [
				'id'    => (string) $group['ID'],
				'label' => $group['post_title'],
			];
		}, $leader_groups['posts'] );

		if ( ! count( $parent_group_options ) ) {
			return false;
		}

		$parent_group = $default_parent_group;

		if ( $group ) {
			$parent_group = count( $group['parent_groups'] ) ? $group['parent_groups'][0]["ID"] : $default_parent_group;
		}

		$parent_group_label = __( 'Parent group', 'disciple-tools-autolink' );

		include __DIR__ . '/../templates/parts/parent-group-field.php';
	}

	/**
	 * Show the create group form
	 */
	public function create( $params = [] ) {
		$group_id         = sanitize_key( wp_unslash( $_GET['post'] ?? "" ) );
		$params['action'] = $this->functions->get_create_group_url();

		// Default the current user as the leader
		$params['leaders'] = [
			(string) Disciple_Tools_Users::get_contact_for_user( get_current_user_id() )
		];

		if ( $group_id ) {
			$this->functions->redirect_to_app();
			exit;
		}
		$this->form( $params );
	}

	/**
	 * Delete a group
	 */
	public function delete( $params = [] ) {
		$app_controller = new Disciple_Tools_Autolink_App_Controller();
		$nonce          = sanitize_key( wp_unslash( $_GET['_wpnonce'] ?? '' ) );
		$verify_nonce   = $nonce && wp_verify_nonce( $nonce, 'dt_autolink_delete_group' );
		$group_id       = sanitize_text_field( wp_unslash( $_GET['post'] ?? '' ) );

		if ( ! $verify_nonce ) {
			$app_controller->show( [ 'error' => __( 'Unauthorized action. Please refresh the page and try again.', 'disciple-tools-autolink' ) ] );

			return;
		}

		$group_id = (int) $group_id;

		$result = DT_Posts::delete_post( 'groups', $group_id, false );

		if ( is_wp_error( $result ) ) {
			$app_controller->show( [ 'error' => $result->get_error_message() ] );
		}

		$app_controller->show();
	}

	/**
	 * Show the DT group in an iframe
	 */
	public function show( $params = [] ) {
		$post_id    = sanitize_key( wp_unslash( $_GET['post'] ?? '' ) );
		$back_link  = $this->functions->get_app_link();
		$back_label = __( 'Back to AutoLink', 'disciple-tools-autolink' );

		if ( ! $post_id || ! $back_link ) {
			$this->functions->redirect_to_app();

			return;
		}

		$group = DT_Posts::get_post( 'groups', $post_id );

		if ( is_wp_error( $group ) ) {
			$this->functions->redirect_to_app();

			return;
		}

		$src = get_the_permalink( $group['ID'] );

		include __DIR__ . '/../templates/frame.php';
	}

	public function update( $params = [] ) {
		$nonce            = sanitize_key( wp_unslash( $_POST['_wpnonce'] ?? '' ) );
		$verify_nonce     = $nonce && wp_verify_nonce( $nonce, self::NONCE );
		$id               = sanitize_key( wp_unslash( $_POST['id'] ?? '' ) );
		$action           = $this->functions->get_edit_group_url();
		$params['action'] = $action;
		$params['post']   = $id;

		if ( ! $verify_nonce ) {
			$this->form( [
				'error'  => 'Invalid request',
				'action' => $action
			] );

			return;
		}

		if ( ! $id ) {
			wp_redirect( $this->functions->get_app_link() );

			return;
		}

		$this->process( $params );
	}

	/**
	 * Process the edit/create group form
	 */
	private function process( $params = [] ) {

		$nonce        = sanitize_key( wp_unslash( $_POST['_wpnonce'] ?? '' ) );
		$verify_nonce = $nonce && wp_verify_nonce( $nonce, self::NONCE );

		$id           = sanitize_key( wp_unslash( $_POST['id'] ?? '' ) );
		$name         = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
		$start_date   = strtotime( sanitize_text_field( wp_unslash( $_POST['start_date'] ?? '' ) ) );
		$location     = sanitize_text_field( wp_unslash( $_POST['location'] ?? '' ) );
		$leaders      = dt_recursive_sanitize_array( $_POST['leaders'] ?? '' );
		$location     = $location ? json_decode( $location, true ) : '';
		$user         = wp_get_current_user();
		$contact_id   = Disciple_Tools_Users::get_contact_for_user( $user->ID, true );
		$parent_group = sanitize_text_field( wp_unslash( $_POST['parent_group'] ?? 0 ) );
		$action       = $params['action'];

		$get_params = [
			'action'  => $action,
			'name'    => $name,
			'leaders' => $leaders,
		];

		if ( isset( $location['location_grid_meta'] ) && isset( $location['location_grid_meta']['values'] ) ) {
			$location = $location['location_grid_meta']['values'];
		}

		if ( ! $verify_nonce || ! $name ) {
			$this->form( array_merge( $get_params, [
				'error' => 'Invalid request',
				'post'  => $id,
			] ) );

			return;
		}

		foreach ( $leaders as $idx => $value ) {
			if ( ! is_numeric( $value ) ) {
				$title           = $value;
				$contact         = DT_Posts::create_post( 'contacts',
					[
						'name'       => $title,
						'coached_by' => [
							"values" => [
								[ "value" => $contact_id ]
							]
						]
                ], true, false );
				$leaders[ $idx ] = $contact['ID'];
				wp_publish_post( $contact['ID'] );
			}
		}

		// Leaders with a negative number need to be removed.
		$leaders = array_reduce( $leaders, function ( $leaders, $leader_id ) {
			$leader_id = (int) $leader_id;
			if ( $leader_id > 0 ) {
				$leaders[] = [ 'value' => $leader_id ];
			}

			return $leaders;
		}, [] );

		$fields = [
			"title"         => $name,
			"leaders"       => [
				"force_values" => true,
				"values"       => $leaders
			],
			"members"       => [
				"force_values" => true,
				"values"       => $leaders
			],
			"parent_groups" => [
				"force_values" => true,
				"values"       => [
					[ "value" => $parent_group ]
				]
			],
			"start_date"    => $start_date,
		];

		if ( ! empty( $location ) ) {
			$fields['location_grid_meta'] = [
				'force_values' => true,
				'values'       => $location
			];
		}

		if ( $id ) {
			$group = DT_Posts::update_post( 'groups', (int) $id, $fields, false, false );
			if ( is_wp_error( $group ) ) {
				$this->form( array_merge( $get_params, [
					'error' => $group->get_error_message(),
					'post'  => (int) $id,
				] ) );

				return;
			}
			do_action( 'dt_autolink_group_updated', $group );
		} else {
			$group = DT_Posts::create_post( 'groups', $fields, false, false );
			if ( is_wp_error( $group ) ) {
				$this->form( array_merge( $get_params, [
					'error' => $group->get_error_message()
				] ) );

				return;
			}
			do_action( 'dt_autolink_group_created', $group );
		}

		if ( is_wp_error( $group ) ) {
			$this->form( array_merge( $get_params, [
				'error' => $group->get_error_message()
			] ) );

			return;
		}

		wp_redirect( $this->functions->get_app_link() );
	}

	public function store( $params = [] ) {
		$nonce            = sanitize_key( wp_unslash( $_POST['_wpnonce'] ?? '' ) );
		$verify_nonce     = $nonce && wp_verify_nonce( $nonce, self::NONCE );
		$id               = sanitize_key( wp_unslash( $_POST['id'] ?? '' ) );
		$params['action'] = $this->functions->get_create_group_url();

		if ( ! $verify_nonce || $id ) {
			$this->form( [
				'error' => 'Invalid request'
			] );

			return;
		}

		$this->process( $params );
	}

	public function index( $params = [] ) {
		$nonce        = sanitize_key( wp_unslash( $_GET['_wpnonce'] ?? '' ) );
		$verify_nonce = $nonce && wp_verify_nonce( $nonce, 'wp_rest' );

		if ( ! $verify_nonce ) {
			return new WP_REST_Response( __( 'Unauthorized', 'disciple-tools-autolink' ), 401 );
		}

		$limit  = sanitize_key( wp_unslash( $_GET['limit'] ) );
		$offset = sanitize_key( wp_unslash( $_GET['offset'] ) );

		$result = DT_Posts::list_posts( 'groups', [
			'assigned_to' => [ get_current_user_id() ],
			'limit'       => $limit,
			'offset'      => $offset,
			'sort'        => '-last_modified'
		], false );


		if ( ! $result ) {
			return new WP_REST_Response( __( 'Could not fetch groups', 'disciple-tools-autolink' ), 400 );
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
