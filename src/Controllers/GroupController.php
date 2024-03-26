<?php

namespace DT\Autolink\Controllers;

use Disciple_Tools_Google_Geocode_API;
use Disciple_Tools_Users;
use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Illuminate\Http\Response;
use DT\Autolink\Repositories\GroupTreeRepository;
use DT\Autolink\Services\Options;
use DT_Mapbox_API;
use DT_Posts;
use function DT\Autolink\container;
use function DT\Autolink\redirect;
use function DT\Autolink\route_url;
use function DT\Autolink\template;
use function DT\Autolink\view;

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

	public function create( Request $request, Response $response ) {
		$params['action'] = route_url( '/groups' );

		// Default the current user as the leader
		$params['leaders'] = [
			(string) \Disciple_Tools_Users::get_contact_for_user( get_current_user_id() )
		];

		return $this->form( $request, $response, $params );
	}

	public function edit( Request $request, Response $response, $group_id ) {
		$params['action'] = route_url( '/groups/' . $group_id );

		$params['post'] = $group_id;

		$group = DT_Posts::get_post( 'groups', $params['post'], true, false );

		$params['name']    = $group['name'];
		$params['start_date'] = $group['start_date'] ?? '';
		$params['leaders'] = array_map( function ( $leader ) {
			return (string) $leader['ID'];
		}, $group['leaders'] );

		return $this->form( $request, $response, $params );
	}

	private function form( Request $request, Response $response, $params = [] ) {
		$group    = null;
		$group_id = sanitize_key( wp_unslash( $_GET['post'] ?? $params['post'] ?? null ) );
		if ( $group_id ) {
			$group = DT_Posts::get_post( 'groups', $group_id, true, false );
			if ( ! $group || is_wp_error( $group ) ) {
				$this->functions->redirect_to_app();
				exit;
			}
		}

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

		$group_tree_repository = container()->make( GroupTreeRepository::class );
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
		$name_label       = $group_fields['name']['name'];
		$name_placeholder = $group_fields['name']['name'];
		$start_date_label = $group_fields['start_date']['name'];
		$leaders_label    = $group_fields['leaders']['name'];
		$nonce            = wp_create_nonce( 'disciple-tools-autolink' );
		$action           = $params['action'];
		$cancel_url       = route_url();
		$cancel_label     = __( 'Cancel', 'disciple-tools-autolink' );
		$submit_label     = __( 'Save', 'disciple-tools-autolink' );
		$error            = $params['error'] ?? '';
		$name             = sanitize_text_field( wp_unslash( $params['name'] ?? "" ) );
		$contacts         = [ DT_Posts::get_post( 'contacts', $contact_id ) ];
		$coaching         = $group_tree_repository->tree( 'coaching', [
			'check_health' => false,
			'id'           => $contact_id,
		] );
		$leader_ids       = $params['leaders'] ?? array_map( function ( $leaders ) {
			return (string) $leaders['ID'];
		}, $group['leaders'] ?? [] );
		$leader_options   = array_map( function ( $contact ) {
			return [
				'id'    => (string) $contact['ID'],
				'label' => $contact['name'],
			];
		}, $contacts );
		foreach ( $coaching as $coached ) {
			$leader_options[] = [
				'id'    => $coached['id'],
				'label' => $coached['name'],
			];
		}
		$parent_group_field_callback = route_url( '/groups/parent-group-field' );

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

		return template( 'groups/form', compact( 'heading', 'name_label', 'name_placeholder', 'start_date_label', 'leaders_label', 'nonce', 'action', 'cancel_url', 'cancel_label', 'submit_label', 'error', 'name', 'leader_ids', 'leader_options', 'parent_group_field_callback', 'show_location_field', 'start_date', 'group_fields', 'group' ) );
	}

	/**
	 * Ajax callback to get the parent group field.
	 * Renders when the leaders change.
	 * @return false|void
	 */
	public function parent_group_field(Options $options) {
		$group_fields = DT_Posts::get_post_settings( 'groups' )['fields'];
		$post_type    = get_post_type_object( 'groups' );
		$group_labels = get_post_type_labels( $post_type );
		$leaders_ids  = dt_recursive_sanitize_array( $_GET['leaders'] ?? [] );

		//Filter out new leaders
		$leader_ids = array_filter( $leaders_ids, function ( $leader ) {
			return is_numeric( $leader ) && $leader > 0;
		} );


		$leaders = array_map( function ( $leader_id ) {
			return DT_Posts::get_post( 'contacts', $leader_id, false, false );
		}, $leader_ids );

		$groups = [];

		foreach ( $leaders as $leader ) {
			array_push( $groups, ...$leader['groups'] ?? [] );
		}

		$coached_by = [ 'posts' => [] ];

		if ( count( $leaders ) ) {
			$coached_by = count( $leaders ) ? DT_Posts::list_posts( 'contacts', [
				'coaching' => array_map( function ( $leader ) {
					return $leader['ID'];
				}, $leaders )
			], false ) : $coached_by;
		}

		foreach ( $coached_by['posts'] as $leader ) {
			array_push( $groups, ...$leader['groups'] ?? [] );
		}

		$groups  = array_unique( $groups, SORT_REGULAR );
		$leaders = count( $leaders ) ? DT_Posts::list_posts( 'groups', [
			'assigned_to' => [ 340 ],
			'limit'       => 1000
		], false ) : [ 'posts' => [] ];

		$id                           = sanitize_text_field( wp_unslash( $_GET['id'] ?? '' ) );
		$group                        = $id ? DT_Posts::get_post( 'groups', $id, true, false ) : null;
		$default_parent_group         = count( $groups ) ? $groups[0]['ID'] ?? null : null;
		$allow_parent_group_selection = $options->get( 'allow_parent_group_selection' );
		$allow_parent_group_selection = $allow_parent_group_selection === '1' || $allow_parent_group_selection === true;


		$parent_group_options = array_map( function ( $group ) {
			return [
				'id'    => (string) $group['ID'],
				'label' => $group['post_title'],
			];
		}, $groups );

		if ( ! count( $parent_group_options ) ) {
			return false;
		}

		array_unshift( $parent_group_options, [
			'id'    => '',
			'label' => __( 'Select a', 'disciple-tools-autolink' ) . ' ' . strtolower( $group_labels->singular_name ) . '...',
		] );

		$parent_group = $default_parent_group;

		if ( $group ) {
			$parent_group = count( $group['parent_groups'] ) ? $group['parent_groups'][0]["ID"] : '';
		}

		$parent_group_label = __( 'Parent', 'disciple-tools-autolink' ) . ' ' . $group_labels->singular_name;

		return view( "groups/parent-group-field", compact( 'parent_group_options', 'parent_group', 'parent_group_label', 'allow_parent_group_selection', 'id', 'group', 'group_labels', 'group_fields' ) );
	}

	public function store( Request $request, Response $response, $params = [] ) {
		$params['action'] = route_url( '/groups' );

		return $this->process( $request, $response, $params );
	}

	public function update( Request $request, Response $response, $group_id ) {
		$action           = route_url( '/groups/' . $group_id );
		$params['action'] = $action;
		$params['post']   = $group_id;

		return $this->process( $request, $response, $params );
	}

	/**
	 * Process the edit/create group form
	 */
	private function process( $request, $response, $params = [] ) {
		$id           = sanitize_key( wp_unslash( $_POST['id'] ?? '' ) );
		$name         = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
		$start_date   = strtotime( sanitize_text_field( wp_unslash( $_POST['start_date'] ?? '' ) ) );
		$location     = sanitize_text_field( wp_unslash( $_POST['location'] ?? '' ) );
		$leaders      = dt_recursive_sanitize_array( $_POST['leaders'] ?? [] );
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
		if ( ! $name ) {
			return $this->form( array_merge( $get_params, [
				'error' => 'Invalid request',
				'post'  => $id,
			] ) );
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
				"values"       => $parent_group ? [
					[ "value" => $parent_group ]
				] : []
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
				return $this->form( array_merge( $get_params, [
					'error' => $group->get_error_message(),
					'post'  => (int) $id,
				] ) );
			}
			do_action( 'dt_autolink_group_updated', $group );
		} else {
			$group = DT_Posts::create_post( 'groups', $fields, false, false );
			if ( is_wp_error( $group ) ) {
				return $this->form( array_merge( $get_params, [
					'error' => $group->get_error_message()
				] ) );
			}
			do_action( 'dt_autolink_group_created', $group );
		}


		if ( is_wp_error( $group ) ) {
			return $this->form( array_merge( $get_params, [
				'error' => $group->get_error_message()
			] ) );
		}

		return redirect(route_url());
	}
}
