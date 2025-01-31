<?php

namespace DT\Autolink\Controllers;

use Disciple_Tools_Users;
use DT\Autolink\GuzzleHttp\Psr7\Request;
use DT\Autolink\Psr\Http\Message\ResponseInterface;
use DT\Autolink\Services\Assets;
use DT\Autolink\Services\Options;
use DT\Autolink\Services\Analytics;
use DT_Mapbox_API;
use DT_Posts;
use Exception;
use function DT\Autolink\container;
use function DT\Autolink\extract_request_input;
use function DT\Autolink\group_label;
use function DT\Autolink\redirect;
use function DT\Autolink\render;
use function DT\Autolink\render_view;
use function DT\Autolink\request_wants_json;
use function DT\Autolink\route_url;
use function DT\Autolink\template;
use function DT\Autolink\view;
use function DT\Autolink\response;
use function DT\Autolink\get_plugin_option;

/**
 * Ajax callback to get the parent group field.
 * Renders when the leaders change.
 *
 * @param Request $request The request object.
 * @param Response $response The response object.
 * @param string $group_id The ID of the group.
 *
 * @return mixed The result of the edit operation.
 */
class GroupController {
    /**
     * Ajax callback to get the index of groups.
     * Retrieves a list of groups assigned to the current user.
     *
     * @param Request $request The HTTP request object.
     */
    public function index( Request $request ) {
		$params = extract_request_input( $request );
        $limit  = $params['limit'] ?? 10;
        $offset = $params['offset'] ?? 0;

        $result = DT_Posts::list_posts( 'groups', [
            'assigned_to' => [ get_current_user_id() ],
            'limit'       => $limit,
            'offset'      => $offset,
            'sort'        => '-last_modified'
        ], false );

        if ( ! $result ) {
            return response( 'Invalid request', 400 );
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

        // Capture frontend group count metrics
        container()->get( Analytics::class )->metric( 'total-group-count', [
            'lib_name' => __CLASS__,
            'value' => $result['total'],
            'unit' => 'group_count',
            'description' => 'Total Group Count'
        ] );

        //$analytics->event( 'group_count', [ 'action' => 'stop' ] );
        return response( $result );
    }

	/**
	 * Show modal method returns the content and post data for a specific group modal.
	 *
	 * @param Request $request The HTTP request object.
	 * @param array $params The route params
	 *
	 * @return ResponseInterface The array containing the content, post, and code.
	 */
	public function show_modal( Request $request, array $params ) {
		$group_id = $params['group_id'] ?? null;

		return response( [
			'content' => render( 'groups/modal', $this->form_view_params( $request, $params ) ),
			'post' => $group_id ? DT_Posts::get_post( 'groups', $group_id, true, false ) : null,
			'code' => 200
		] );
	}

    /**
     * Generate the view parameters for the form view.
     *
     * @param Request $request The HTTP request object.
     * @param array $params The route params
     *
     * @return array The view parameters.
     * @throws Exception
     */
    private function form_view_params( Request $request, array $params = null ): array {
        $group               = null;
		$group_id            = $params['group_id'] ?? null;
	    $input               = extract_request_input( $request );
        $action              = route_url( "/groups" );
        $leaders             = null;

        if ( $group_id ) {
            $group = DT_Posts::get_post( 'groups', $group_id, true, false );
            if ( is_wp_error( $group ) ) {
                return [
                    'error' => group_label() . ' ' . __( 'not found', 'disciple-tools-autolink' )
                ];
            }
            $action = route_url( "/groups/" . $group_id );
        } else {
            $leaders = [ (string) Disciple_Tools_Users::get_contact_for_user( get_current_user_id() ) ];
        }

        $group_fields = DT_Posts::get_post_settings( 'groups' )['fields'];
        $user = wp_get_current_user();
        $contact_id = Disciple_Tools_Users::get_contact_for_user( $user->ID );
        $heading = $group_id ? __( 'Edit', 'disciple-tools-autolink' ) . ' ' . group_label() : __( 'Create', 'disciple-tools-autolink' ) . ' ' . group_label();
        $name_label = $group_fields['name']['name'];
        $name_placeholder = $group_fields['name']['name'];
        $start_date_label = $group_fields['start_date']['name'];
        $leaders_label = $group_fields['leaders']['name'];
        $cancel_url = route_url();
        $cancel_label = __( 'Cancel', 'disciple-tools-autolink' );
        $submit_label        = __( 'Save', 'disciple-tools-autolink' );
        $error               = $input['e'] ?? '';
        $name                = $group['name'] ?? '';
        $contacts = [ DT_Posts::get_post( 'contacts', $contact_id ) ];
        $leader_ids = $leaders ?? array_map(function ( $leader ) {
            return (string) $leader['ID'];
        }, $group['leaders'] ?? []);
        $leader_options = array_map(function ( $contact ) {
            return [
                'id' => (string) $contact['ID'],
                'label' => $contact['name'],
            ];
        }, $contacts);
        $parent_group = count( $group['parent_groups'] ?? [] ) ? $group['parent_groups'][0]["ID"] : '';
        $parent_group_field_callback = route_url( '/groups/parent-group-field' );
        $show_location_field = DT_Mapbox_API::is_active_mapbox_key();
        $start_date          = $input['start_date'] ?? $group['start_date'] ?? '';

        if ( $start_date && is_array( $start_date ) ) {
            $start_date = dt_format_date( $start_date['timestamp'] );
        }

        // Additional data from previous context
        $limit = 10;
        $churches = DT_Posts::list_posts('groups', [
            'assigned_to' => [ get_current_user_id() ],
            'limit' => $limit,
            'sort' => '-post_date'
        ], false);

        $group_fields = DT_Posts::get_post_field_settings( 'groups' );
        $church_fields = [
            'health_metrics' => $group_fields['health_metrics']['default'] ?? [],
        ];
	    $allowed_church_count_fields = [
		    'member_count',
		    'leader_count',
		    'believer_count',
		    'baptized_count',
		    'baptized_in_group_count'
	    ];
	    $church_count_fields = [];
	    foreach ( $allowed_church_count_fields as $field ) {
		    // Fields can be registered or deregistered by plugins, so check and make sure it exists
		    if ( isset( $group_fields[$field] ) && ( !isset( $group_fields[$field]['hidden'] ) || !$group_fields[$field]['hidden'] ) ) {
			    // Assign group_id to each church_count_field
			    $church_count_fields[$field] = array_merge( $group_fields[$field], [ 'group_id' => $group_id ] );
		    }
	    }

        $opened = true;

        // Compact and return all relevant data
        return compact(
            'group', 'action', 'group_id', 'heading', 'name_label', 'name_placeholder', 'start_date_label', 'leaders_label',
            'cancel_url', 'cancel_label', 'submit_label', 'error', 'name', 'leader_ids', 'leader_options', 'parent_group',
            'parent_group_field_callback', 'show_location_field', 'start_date', 'group_fields', 'church_fields', 'churches',
            'opened', 'church_count_fields'
        );
    }

	/**
	 * Create modal method returns the content and HTTP code for a create modal view.
	 *
	 * @param Request $request The HTTP request object.
	 *
	 * @return ResponseInterface The array containing the content and HTTP code.
	 */
	public function create_modal( Request $request ) {
        return response( [
            'content' => render( 'groups/create-modal', $this->form_view_params( $request, [] ) ),
            'code' => 200
        ] );
    }

	/**
	 * Edit method retrieves the data for a group from the database.
	 *
	 * @param Request $request The HTTP request object.
	 *
	 * @return mixed The form content or an error message.
	 */
	public function create( Request $request ) {
		$assets = container()->get( Assets::class );
		$assets->register_mapbox();

		return template( 'groups/page', $this->form_view_params( $request, [] ) );
	}

    /**
     * Form method handles the generation of the form.
     *
     * @param Request $request The HTTP request object.
     * @param array $params The route params
     *
     * @return mixed The form content or an error message.
     */
    public function edit( Request $request, array $params ): mixed {
	    $assets = container()->get( Assets::class );
		$group_id = $params['group_id'] ?? null;
	    $assets->register_mapbox( $group_id );

	    return template( 'groups/page', $this->form_view_params( $request, $params ) );
    }

    /**
     * Retrieves the parent group field options and data for a group.
     *
     * @param Request $request The HTTP request object.
     *
     * @return mixed The view with the parent group field options and data.
     */
    public function parent_group_field( Request $request ) {
		$options = container()->get( Options::class );
	    $params = extract_request_input( $request );
	    $group_fields = DT_Posts::get_post_settings( 'groups' )['fields'];
        $post_type    = get_post_type_object( 'groups' );
        $group_labels = get_post_type_labels( $post_type );
        $leaders_ids  = $params['leaders'] ?? [];

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

        $id                           = $params['id'] ?? null;
        $group                        = $id ? DT_Posts::get_post( 'groups', $id, true, false ) : null;
        $default_parent_group         = count( $groups ) ? $groups[0]['ID'] ?? null : null;
        $allow_parent_group_selection = get_plugin_option( 'allow_parent_group_selection' );
        $allow_parent_group_selection = $allow_parent_group_selection === '1' || $allow_parent_group_selection === true;


        $parent_group_options = array_map( function ( $group ) {
            return [
                'id'    => (string) $group['ID'],
                'label' => $group['post_title'],
            ];
        }, $groups );

        if ( ! count( $parent_group_options ) ) {
            return response( "" );
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


        return view( "groups/parent-group-field", compact( 'parent_group_options', 'parent_group', 'parent_group_label', 'allow_parent_group_selection', 'id', 'group', 'group_labels', 'group_fields', 'leaders' ) );
    }

    /**
     * Update method updates the data for a group in the database.
     *
     * @param Request $request The HTTP request object.
     *
     * @return mixed The result of the "process" method.
     * @throws Exception
     */
    public function store( Request $request ) {
		$input = extract_request_input( $request );
		$wants_json = request_wants_json( $request );

		if ( !isset( $input['name'] ) ) {
			if ( $wants_json ) {
				return response( [
					'error' => __( 'Invalid field: name' )
				], 400 );
			} else {
				return redirect( route_url( "/groups/create?" . http_build_query(
                    array_merge( [ 'e' => __( 'Invalid field: name' ) ], $input )
                ) ) );
			}
		}

        $fields = $this->group_fields_from_request( $input );
        $group = DT_Posts::create_post( 'groups', $fields, false, false );
        if ( is_wp_error( $group ) ) {
            if ( $wants_json ) {
                return response( [
                    'content' => $group->get_error_message()
                ], 400 );
            } else {
                return redirect( route_url( "/groups/create?e=" . $group->get_error_message() ) );
            }
        }

        do_action( 'dt_autolink_group_created', $group );

        if ( $wants_json ) {
            return response( [
                'success' => true,
                'group' => $group
            ] );
        } {
            return redirect( route_url( 'groups' ) );
        }
    }

    /**
     * group_fields_from_request method extracts and processes the fields from the request object.
     *
     * @param mixed $request The request object from which to extract the fields.
     *
     * @return array The processed fields extracted from the request.
     *
     * @throws Exception When there are validation errors in the request fields.
     */
    private function group_fields_from_request( $input ) {
//		$input = extract_request_input( $request );
        $id           = $input['group_id'] ?? null;
        $name         = $input['name'] ?? '';

        $start_date   = strtotime(
            $input['start_date'] ?? '',
        );
        $location = $input['location'] ?? '';
        $leaders  = $input['leaders'] ?? [];
        $location     = $location ? json_decode( $location, true ) : '';
        $user         = wp_get_current_user();
        $contact_id   = Disciple_Tools_Users::get_contact_for_user( $user->ID );
        $parent_group = $input['parent_group'] ?? '';
	    $group_type = $input['group_type'] ?? 'pre-group';

        if ( isset( $location['location_grid_meta'] ) && isset( $location['location_grid_meta']['values'] ) ) {
            $location = $location['location_grid_meta']['values'];
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
            "start_date"    => $start_date,
        ];

        if ( $parent_group ) {
            $fields['parent_groups'] = [
                'force_values' => true,
                'values'       => [ [ 'value' => $parent_group ] ]
            ];
        }

        if ( ! empty( $location ) ) {
            $fields['location_grid_meta'] = [
                'force_values' => true,
                'values'       => $location
            ];
        }

        return $fields;
    }

    /**
     * Update method updates the data for a group in the database.
     *
     * @param Request $request The HTTP request object.
     * @param array $params The route params
     *
     * @return mixed The result of the "process" method.
     * @throws Exception
     */
    public function update( Request $request, array $params ) {
		$group_id = $params['group_id'];
	    $input = extract_request_input( $request );
	    $wants_json = request_wants_json( $request );

	    if ( !isset( $input['name'] ) ) {
		    if ( $wants_json ) {
			    return response( [
				    'error' => __( 'Invalid field: name' )
			    ], 400 );
		    } else {
			    return redirect( route_url( "/groups/create?" . http_build_query(
                    array_merge( [ 'e' => __( 'Invalid field: name' ) ], $input )
                ) ) );
		    }
	    }


        $fields = $this->group_fields_from_request( $input );
        $group = DT_Posts::update_post( 'groups', (int) $group_id, $fields, false, false );
        if ( is_wp_error( $group ) ) {
            if ( $wants_json ) {
                return response( [
                    'error' => $group->get_error_message(),
                    'success' => false
                ] );
            } else {
                return redirect( route_url( "/groups/" . $group_id . "/edit?e=" . $group->get_error_message() ) );
            }
        }

        do_action( 'dt_autolink_group_updated', $group );

        if ( $wants_json ) {
            return response( [
                'success' => true
            ] );
        } {
            return redirect( route_url( 'groups ' ) );
        }
    }

    /**
     * Delete a group
     *
     * @param Request $request The request object from which to extract the fields.
     * @param array $params The route params
     */
    public function destroy( Request $request, array $params ) {
        $group_id = (int) $params['group_id'] ?? null;

        $result = DT_Posts::delete_post( 'groups', $group_id, false );


        if ( is_wp_error( $result ) ) {
            return redirect( route_url( "?e=" . $result->get_error_message() ) );
        }

        return redirect( route_url( 'groups' ) );
    }

    /**
     * Show the DT group in an iframe
     *
     * @param Request $request The request object from which to extract the fields.
     * @param array $params The route params
     */
    public function show( Request $request, array $params ) {
        $post_id    = (int) $params['group_id'] ?? null;
        $back_link  = route_url();
        $back_label = __( 'Back to AutoLink', 'disciple-tools-autolink' );

        if ( ! $post_id || ! $back_link ) {
            return redirect( route_url() );
        }

        $group = DT_Posts::get_post( 'groups', $post_id );

        if ( is_wp_error( $group ) ) {
            return redirect( route_url() );
        }

        $src = get_the_permalink( $group['ID'] );

        return template( "frame", compact( 'src', 'back_link', 'back_label' ) );
    }
}
