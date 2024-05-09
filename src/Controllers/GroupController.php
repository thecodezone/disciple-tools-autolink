<?php

namespace DT\Autolink\Controllers;

use Disciple_Tools_Users;
use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Illuminate\Http\Response;
use DT\Autolink\Illuminate\Support\Arr;
use DT\Autolink\Repositories\GroupTreeRepository;
use DT\Autolink\Services\Assets;
use DT\Autolink\Services\Options;
use DT_Mapbox_API;
use DT_Posts;
use function DT\Autolink\container;
use function DT\Autolink\group_label;
use function DT\Autolink\redirect;
use function DT\Autolink\route_url;
use function DT\Autolink\template;
use function DT\Autolink\validate;
use function DT\Autolink\view;

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
     * @param Response $response The HTTP response object.
     *
     * @return false|array Returns an array containing the list of groups and the total count.
     *                    Each group may have additional formatted date information.
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

    /**
     * Form method handles the generation of the form.
     *
     * @param Request $request The HTTP request object.
     * @param Assets $assets The assets object used to enqueue assets.
     *
     * @return mixed The form content or an error message.
     */
    public function form( Request $request, Assets $assets, $group_id = null ): mixed {
        add_action( 'wp_enqueue_scripts', function () use ( $assets, $group_id ) {
            $assets->enqueue_mapbox(
                $group_id,
                $group_id ? DT_Posts::get_post( 'groups', $group_id ) : false
            );
        }, 1 );


        $params = $this->form_view_params( $request, $group_id );

        if ( $request->wantsJson() ) {
            return [
                'content' => view( 'groups/modal', $params ),
                'post' => $group_id ? DT_Posts::get_post( 'groups', $group_id, true, false ) : null,
                'code' => 200
            ];
        } else {
            return template( 'groups/page', $params );
        }
    }


    /**
     * Generate the view parameters for the form view.
     *
     * @param Request $request The HTTP request object.
     * @param int|null $group_id The group ID (optional).
     *
     * @return array The view parameters.
     * @throws \Exception
     */
    private function form_view_params(Request $request, $group_id = null): array {
        $group = null;
        $action = route_url("/groups");
        $leaders = null;

        if ($group_id) {
            $group = DT_Posts::get_post('groups', $group_id, true, false);
            if (is_wp_error($group)) {
                return [
                    'error' => group_label() . ' ' . __('not found', 'disciple-tools-autolink')
                ];
            }
            $action = route_url("/groups/" . $group_id);
        } else {
            $leaders = [(string) Disciple_Tools_Users::get_contact_for_user(get_current_user_id())];
        }

        $group_fields = DT_Posts::get_post_settings('groups')['fields'];
        $user = wp_get_current_user();
        $contact_id = Disciple_Tools_Users::get_contact_for_user($user->ID);
        $heading = $group_id ? __('Edit', 'disciple-tools-autolink') . ' ' . group_label() : __('Create', 'disciple-tools-autolink') . ' ' . group_label();
        $name_label = $group_fields['name']['name'];
        $name_placeholder = $group_fields['name']['name'];
        $start_date_label = $group_fields['start_date']['name'];
        $leaders_label = $group_fields['leaders']['name'];
        $cancel_url = route_url();
        $cancel_label = __('Cancel', 'disciple-tools-autolink');
        $submit_label = __('Save', 'disciple-tools-autolink');
        $error = $request->get('e', $request->get('error'));
        $name = $group['name'] ?? '';
        $contacts = [DT_Posts::get_post('contacts', $contact_id)];
        $leader_ids = $leaders ?? array_map(function ($leader) {
            return (string) $leader['ID'];
        }, $group['leaders'] ?? []);
        $leader_options = array_map(function ($contact) {
            return [
                'id' => (string) $contact['ID'],
                'label' => $contact['name'],
            ];
        }, $contacts);
        $parent_group = count($group['parent_groups'] ?? []) ? $group['parent_groups'][0]["ID"] : '';
        $parent_group_field_callback = route_url('/groups/parent-group-field');
        $show_location_field = DT_Mapbox_API::is_active_mapbox_key();
        $start_date = $request->get('start_date') ?? $group['start_date'] ?? '';

        if ($start_date && is_array($start_date)) {
            $start_date = dt_format_date($start_date['timestamp']);
        }

        // Additional data from previous context
        $limit = 10;
        $churches = DT_Posts::list_posts('groups', [
            'assigned_to' => [get_current_user_id()],
            'limit' => $limit,
            'sort' => '-post_date'
        ], false);

        $group_fields = DT_Posts::get_post_field_settings('groups');
        $church_fields = [
            'health_metrics' => $group_fields['health_metrics']['default'] ?? [],
        ];

        $opened =  true;

        // Compact and return all relevant data
        return compact(
            'group', 'action', 'group_id', 'heading', 'name_label', 'name_placeholder', 'start_date_label', 'leaders_label',
            'cancel_url', 'cancel_label', 'submit_label', 'error', 'name', 'leader_ids', 'leader_options', 'parent_group',
            'parent_group_field_callback', 'show_location_field', 'start_date', 'group_fields', 'church_fields', 'churches',
            'opened'
        );
    }


    /**
     * Retrieves the parent group field options and data for a group.
     *
     * @param Request $request The HTTP request object.
     * @param Options $options The options object.
     *
     * @return mixed The view with the parent group field options and data.
     */
    public function parent_group_field( Request $request, Options $options ) {
        $group_fields = DT_Posts::get_post_settings( 'groups' )['fields'];
        $post_type    = get_post_type_object( 'groups' );
        $group_labels = get_post_type_labels( $post_type );
        $leaders_ids  = $request->get( 'leaders', [] );

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

        $id                           = $request->get( 'id' );
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
            return "";
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
     * @param Response $response The HTTP response object.
     *
     * @return mixed The result of the "process" method.
     * @throws \Exception
     */
    public function store( Request $request, Response $response ) {
        $errors = validate($request->all(), [
            'name' => 'required'
        ]);

        if ( $errors ) {
            $error = __( 'Invalid field: ' ) . array_key_first( $errors );
            if ( $request->wantsJson() ) {
                return [
                    'error' => $error,
                    'success' => false
                ];
            } else {
                return redirect( route_url( "/groups/create?" . http_build_query(
                        array_merge( [ 'e' => $error ], $request->all() )
                    ) ) );
            }
        }


        $fields = $this->group_fields_from_request( $request );
        $group = DT_Posts::create_post( 'groups', $fields, false, false );
        if ( is_wp_error( $group ) ) {
            if ( $request->wantsJson() ) {
                return [
                    'content' => $group->get_error_message(),
                    'code' => 400
                ];
            } else {
                return redirect( route_url( "/groups/create?e=" . $group->get_error_message() ) );
            }
        }

        do_action( 'dt_autolink_group_created', $group );

        if ( $request->wantsJson() ) {
            return [
                'success' => true,
                'group' => $group
            ];
        } {
            return redirect( route_url() );
        }
    }

    /**
     * Update method updates the data for a group in the database.
     *
     * @param Request $request The HTTP request object.
     * @param Response $response The HTTP response object.
     * @param int $group_id The ID of the group to be updated.
     *
     * @return mixed The result of the "process" method.
     * @throws \Exception
     */
    public function update( Request $request, Response $response, $group_id ) {
        $errors = validate($request->all(), [
            'name' => 'required'
        ]);

        if ( $errors ) {
            $error = __( 'Invalid field: ' ) . array_key_first( $errors );
            if ( $request->wantsJson() ) {
                return [
                    'error' => $error,
                    'success' => false
                ];
            } else {
                return redirect( route_url( "/groups/" . $group_id . "/edit?" . http_build_query(
                        array_merge( [ 'e' => $error ], $request->all() )
                    ) ) );
            }
        }


        $fields = $this->group_fields_from_request( $request );
        $group = DT_Posts::update_post( 'groups', (int) $group_id, $fields, false, false );
        if ( is_wp_error( $group ) ) {
            if ( $request->wantsJson() ) {
                return [
                    'error' => $group->get_error_message(),
                    'success' => false
                ];
            } else {
                return redirect( route_url( "/groups/" . $group_id . "/edit?e=" . $group->get_error_message() ) );
            }
        }

        do_action( 'dt_autolink_group_updated', $group );

        if ( $request->wantsJson() ) {
            return [
                'success' => true
            ];
        } {
            return redirect( route_url() );
        }
    }


    /**
     * group_fields_from_request method extracts and processes the fields from the request object.
     *
     * @param mixed $request The request object from which to extract the fields.
     *
     * @return array The processed fields extracted from the request.
     *
     * @throws \Exception When there are validation errors in the request fields.
     */
    private function group_fields_from_request( $request ) {
        $id           = $request->get( 'id', '' );
        $name         = $request->get( 'name', '' );

        $start_date   = strtotime(
            $request->get( 'start_date', '' )
        );
        $location = $request->get( 'location', '' );
        $leaders  = $request->get( 'leaders', [] );
        $location     = $location ? json_decode( $location, true ) : '';
        $user         = wp_get_current_user();
        $contact_id   = Disciple_Tools_Users::get_contact_for_user( $user->ID );
        $parent_group = $request->get( 'parent_group', 0 );


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
     * Delete a group
     */
    public function destroy( Request $request, Response $response, $group_id ) {
        $group_id = (int) $group_id;

        $result = DT_Posts::delete_post( 'groups', $group_id, false );


        if ( is_wp_error( $result ) ) {
            return redirect( route_url( "?e=" . $result->get_error_message() ) );
        }

        return redirect( route_url() );
    }

    /**
     * Show the DT group in an iframe
     */
    public function show( Request $request, Response $response, $group_id ) {
        $post_id    = $group_id;
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
