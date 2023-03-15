<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly.


/**
 * Class Disciple_Tools_Autolink_Magic_User_App
 */
class Disciple_Tools_Autolink_Magic_User_App extends DT_Magic_Url_Base
{
    public $page_title = 'Autolink';
    public $page_description = 'Autolink user app';
    public $root = "autolink";
    public $type = 'app';
    public $post_type = 'user';
    private $meta_key = 'autolink-app';
    public $show_bulk_send = false;
    public $show_app_tile = false;
    public $functions;

    private static $_instance = null;
    public $meta = []; // Allows for instance specific data.

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    public function __construct() {
        /**
         * Specify metadata structure, specific to the processing of current
         * magic link type.
         *
         * - meta:              Magic link plugin related data.
         *      - app_type:     Flag indicating type to be processed by magic link plugin.
         *      - post_type     Magic link type post type.
         *      - contacts_only:    Boolean flag indicating how magic link type user assignments are to be handled within magic link plugin.
         *                          If True, lookup field to be provided within plugin for contacts only searching.
         *                          If false, Dropdown option to be provided for user, team or group selection.
         *      - fields:       List of fields to be displayed within magic link frontend form.
         */
        $this->meta = [
            'app_type'      => 'magic_link',
            'post_type'     => $this->post_type,
            'contacts_only' => false,
            'fields'        => [
                [
                    'id'    => 'name',
                    'label' => 'Name'
                ]
            ]
        ];

        $this->meta_key = $this->root . '_' . $this->type . '_magic_key';

        parent::__construct();


        $this->functions = Disciple_Tools_Autolink_Magic_Functions::instance();

        $action = sanitize_key( wp_unslash( $_GET['action'] ?? '' ) );
        if ( dt_is_rest() || $action === 'genmap'
            && class_exists( 'DT_Genmapper_Metrics' ) ) {
            require_once( __DIR__ . "/../charts/groups-genmap.php" );
            new DT_Genmapper_Groups_Genmap();
        }

        /**
         * user_app and module section
         */
        add_filter( 'dt_settings_apps_list', [ $this, 'dt_settings_apps_list' ], 10, 1 );
        add_action( 'rest_api_init', [ $this, 'add_endpoints' ] );

        /**
         * tests if other URL
         */
        $url = dt_get_url_path();
        $current_url = $this->root . '/' . $this->type;

        if ( strpos( $url, $current_url ) === false ) {
            return;
        }

        /**
         * tests magic link parts are registered and have valid elements
         */
        if ( !$this->check_parts_match() ) {
            return;
        }

        // if the user is not logged in, redirect to login page.
        if ( !is_user_logged_in() ) {
            $this->functions->redirect_to_link();
        }

        // load if valid url
        wp_set_current_user( $this->parts['post_id'] );
        add_filter( 'user_has_cap', [ $this, 'user_has_cap' ], 100, 3 );
        add_action('dt_blank_body', function () {
            $this->ready();
            $this->routes();
        });
        add_filter( 'dt_magic_url_base_allowed_css', [ $this->functions, 'dt_magic_url_base_allowed_css' ], 10, 1 );
        add_filter( 'dt_magic_url_base_allowed_js', [ $this->functions, 'dt_magic_url_base_allowed_js' ], 10, 1 );
        add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ], 100 );
    }

    public function ready() {
        wp_set_current_user( $this->parts['post_id'] );
        $this->functions->add_session_leader();
    }

    public function wp_enqueue_scripts() {
        $this->functions->wp_enqueue_scripts();
        wp_localize_script(
            'magic_link_scripts',
            'magic',
            [
                'parts' => $this->parts,
                'rest_namespace' => $this->root . '/v1/' . $this->type,
            ]
        );
    }

    /**
     * Builds magic link type settings payload:
     * - key:               Unique magic link type key; which is usually composed of root, type and _magic_key suffix.
     * - url_base:          URL path information to map with parent magic link type.
     * - label:             Magic link type name.
     * - description:       Magic link type description.
     * - settings_display:  Boolean flag which determines if magic link type is to be listed within frontend user profile settings.
     *
     * @param $apps_list
     *
     * @return mixed
     */
    public function dt_settings_apps_list( $apps_list ) {
        $apps_list[$this->meta_key] = [
            'key'              => $this->meta_key,
            'url_base'         => $this->root . '/' . $this->type,
            'label'            => $this->page_title,
            'description'      => $this->page_description,
            'settings_display' => true
        ];

        return $apps_list;
    }

    public function header_style() {
        DT_Mapbox_API::mapbox_search_widget_css();
    }

    public function header_javascript() {
        DT_Mapbox_API::load_mapbox_header_scripts();
        DT_Mapbox_API::load_mapbox_search_widget_users();
    }

    public function routes() {
        $action = sanitize_key( wp_unslash( $_GET['action'] ?? '' ) );
        $type = strtoupper( sanitize_key( wp_unslash( $_SERVER['REQUEST_METHOD'] ?? 'GET' ) ) );

        if ( $type === 'GET' ) {
            switch ( $action ) {
                case 'survey':
                    $this->show_survey();
                    break;
                case 'create-group':
                    $this->show_create_group();
                    break;
                case 'genmap':
                    $this->show_genmap();
                    break;
                case 'tree':
                    $this->show_tree();
                    break;
                default:
                    if ( !$this->functions->survey_completed() ) {
                        return wp_redirect( $this->functions->get_app_link() . '?action=survey' );
                    }
                    $this->show_app();
                    break;
            }
            return;
        }

        if ( $type === 'POST' ) {
            switch ( $action ) {
                case 'survey':
                    $this->submit_survey();
                    break;
                case 'create-group':
                    $this->create_group();
                    break;
                default:
                    wp_redirect( '/' . $this->root );
            }
            return;
        }
    }

    public function app_view_data() {
        $data = [];
        $post_type = get_post_type_object( 'groups' );
        $group_labels = get_post_type_labels( $post_type );

        $data['logo_url'] = $this->functions->fetch_logo();
        $data['greeting'] = __( 'Hello,', 'disciple-tools-autolink' );
        $data['user_name'] = dt_get_user_display_name( get_current_user_id() );
        $data['app_url'] = $this->functions->get_app_link();
        $data['coached_by_label'] = __( 'Coached by', 'disciple-tools-autolink' );
        $data['link_heading'] = __( 'My Link', 'disciple-tools-autolink' );
        $data['share_link_help_text'] = __( 'Copy this link and share it with people you are coaching.', 'disciple-tools-autolink' );
        $data['churches_heading'] = __( "My ", 'disciple-tools-autolink' ) . $group_labels->name;
        $data['share_link'] = $this->functions->get_share_link();
        $data['group_fields'] = DT_Posts::get_post_field_settings( 'groups' );
        $data['create_church_link'] = $this->functions->get_app_link() . '?action=create-group';
        $data['contact'] = Disciple_Tools_Users::get_contact_for_user( get_current_user_id() );
        $data['coach'] = null;
        $data['coach_name'] = '';
        $data['view_church_label'] = __( 'View', 'disciple-tools-autolink' ) . ' ' . $group_labels->singular_name;
        $data['churches'] = [];
        $data['church_health_label'] = $group_labels->singular_name . ' ' . __( 'Health', 'disciple-tools-autolink' );
        $data['tree_label'] = __( 'Tree', 'disciple-tools-autolink' );
        $data['genmap_label'] = __( 'GenMap', 'disciple-tools-autolink' );

        if ( $data['contact'] ) {
            $result = null;
            $data['contact'] = DT_Posts::get_post( 'contacts', $data['contact'], false, false );
            if ( !is_wp_error( $result ) ) {
                $data['contact'] = $result;
            }
            $posts_response = $data['churches'] = DT_Posts::list_posts('groups', [
                'assigned_to' => [ get_current_user_id() ],
                'orderby' => 'modified',
                'order' => 'DESC',
            ], false);
            if ( is_wp_error( $result ) ) {
                $data['churches'] = $posts_response['posts'] ?? [];
            } else {
                $data['churches'] = [];
            }
        }

        if ( $data['contact'] && count( $data['contact']['coached_by'] ) ) {
            $coach = $data['contact']['coached_by'][0] ?? null;
            if ( $coach ) {
                $coach = DT_Posts::get_post( 'contacts', $coach['ID'], false, false );
                if ( is_wp_error( $coach ) ) {
                    $coach = '';
                }
                $coach_name = $coach['name'] ?? '';
            }
            $data['coach'] = $coach;
        }

        return $data;
    }

    public function show_app() {
        $data = $this->app_view_data();
        extract( $data );
        $action = '';

        $churches = DT_Posts::list_posts('groups', [
                'assigned_to' => [ get_current_user_id() ],
        ], false)['posts'] ?? [];


        if ( is_wp_error( $churches ) ) {
            $churches = [];
        }

        usort( $churches, function ( $a, $b ) {
            return $a['last_modified'] < $b['last_modified'] ? 1 : -1;
        });

        //Apply WP formatting to all date fields.
        $churches = array_map( function ( $church ) {
            foreach ( $church as $key => $value ) {
                if ( is_array( $value ) && isset( $value['timestamp'] ) ) {
                    $church[$key]['formatted'] = dt_format_date( $value['timestamp'], get_option( 'date_format' ) );
                }
            }
            return $church;
        }, $churches );

        $group_fields = DT_Posts::get_post_field_settings( 'groups' );
        $church_fields = [
            'health_metrics' => $group_fields['health_metrics']['default'] ?? [],
        ];
        $church_health_field = $church_fields['health_metrics'];
        $allowed_church_count_fields = [
            'member_count',
            'leader_count',
            'believer_count',
            'baptized_count',
            'baptized_in_group_count'
        ];
        $church_count_fields = [];

        foreach ( $allowed_church_count_fields as $field ) {
            //Fields can registered or deregistered by plugins,so check and make sure it exists
            if ( isset( $group_fields[$field] ) ) {
                $church_count_fields[$field] = $group_fields[$field];
            }
        }

        include( 'templates/app.php' );
    }

    public function show_genmap() {
        if ( !class_exists( 'DT_Genmapper_Groups_chart' ) ) {
            wp_redirect( $this->functions->get_app_link() );
        }

        $data = $this->app_view_data();
        extract( $data );
        $action = 'genmap';

        include( 'templates/genmap.php' );
    }

    public function show_tree() {
        $data = $this->app_view_data();
        extract( $data );
        $action = 'tree';
        $fetch_url = '/wp-json/autolink/v1/' . $this->parts['type'];
        $parts = $this->parts;

        include( 'templates/tree.php' );
    }

    public function build_tree( WP_REST_Request $request, $params, $user_id ) {
        $tree = [];
        $title_list = [];
        $pre_tree = [];
        $groups = DT_Posts::list_posts('groups', [
            'assigned_to' => [ get_current_user_id() ],
        ], false );

        $groups = $groups['posts'] ?? [];

        $contact = DT_Posts::list_posts('contacts', [
            'corresponds_to_user' => get_current_user_id(),
        ], false )['posts'][0];
        $allowed_contact_ids = [
            $contact['ID']
        ];
        $allowed_group_ids = array_map( function ( $group ) {
            return (int) $group['ID'];
        }, $groups );

        if ( isset( $contact['coaching'] ) ) {
            foreach ( $contact['coaching'] as $child_contact ) {
                $allowed_contact_ids[] = $child_contact['ID'];
                $child_contact = DT_Posts::get_post( 'contacts', $child_contact['ID'], false );
                $child_groups = DT_Posts::list_posts('groups', [
                    'assigned_to' => [ $child_contact['corresponds_to_user'] ],
                ], false );

                if ( count( $child_groups['posts'] ) ) {
                    foreach ( $child_groups['posts'] as $child_group ) {
                        $allowed_group_ids[] = $child_group['ID'];
                        $groups[] = $child_group;
                    }
                }
            }
        }

        if ( ! empty( $groups ) ) {
            foreach ( $groups as $p ) {
                $assigned_to_user = $p['assigned_to'] ?? [];
                $assigned_to_contact = DT_Posts::list_posts('contacts', [
                    'corresponds_to_user' => $assigned_to_user['id'],
                ], false )['posts'][0];
                $is_allowed_contact = in_array( $assigned_to_contact['ID'], $allowed_contact_ids );

                $has_allowed_parent = !empty( $p['parent_groups'] ) && array_filter($p['parent_groups'], function( $parent ) use ( $allowed_group_ids ) {
                    return in_array( $parent['ID'], $allowed_group_ids );
                });

                if ( !$is_allowed_contact ) {
                    continue;
                }

                if ( isset( $p['child_groups'] ) && ! empty( $p['child_groups'] ) ) {
                    foreach ( $p['child_groups'] as $children ) {
                        $pre_tree[$children['ID']] = $p['ID'];
                    }
                }
                if ( !$has_allowed_parent ) {
                    $pre_tree[$p['ID']] = null;
                }
                $title = $p['name'];
                $title_list[$p['ID']] = $title;
            }

            $tree = $this->parse_tree( $pre_tree, $title_list, null, $allowed_group_ids );
        }


        if ( is_null( $tree ) ) {
            $tree = [];
        }

        echo wp_json_encode([
            'parent_list' => $pre_tree,
            'title_list' => $title_list,
            'tree' => $tree
        ]);
    }

    public function show_survey() {
        $survey = $this->functions->survey();
        $page = sanitize_key( wp_unslash( $_GET['paged'] ?? 0 ) );
        $question = $survey[$page] ?? null;
        if ( !$question ) {
            wp_redirect( $this->functions->get_app_link() . '?action=survey' );
            return;
        }
        $answer = get_user_meta( get_current_user_id(), $question['name'], true );
        $answer = $answer ? $answer : 0;
        $action = $this->functions->get_app_link() . '?action=survey&paged=' . $page;
        $previous_url = $page > 0 ? $this->functions->get_app_link() . '?action=survey&paged=' . ( $page - 1 ) : null;
        $progress = ( $page + 1 ) / count( $survey );
        $progress = number_format( $progress * 100, 0 ) . '%';
        include( 'templates/survey.php' );
    }

    public function submit_survey() {
        $survey = $this->functions->survey();
        $page = (int) sanitize_text_field( wp_unslash( $_GET['paged'] ?? 0 ) );
        $question = $survey[$page] ?? null;
        $next_page = $page + 1;
        $question_name = $question['name'];
        $nonce = sanitize_key( wp_unslash( $_POST['_wpnonce'] ?? '' ) );
        $verify_nonce = $nonce && wp_verify_nonce( $nonce, 'dt_autolink_survey' );

        if ( !$verify_nonce || !$question ) {
            wp_redirect( $this->functions->get_app_link() . '?action=survey' );
            return;
        }

        $answer = sanitize_key( wp_unslash( $_POST[$question_name] ?? null ) );

        if ( $answer === null ) {
            wp_redirect( $this->functions->get_app_link() . '?action=survey&paged=' . $page );
            return;
        }
        update_user_meta( get_current_user_id(), $question['name'], $answer );

        if ( isset( $survey[$next_page] ) ) {
            wp_redirect( $this->functions->get_app_link() . '?action=survey&paged=' . $next_page );
            return;
        }

        wp_redirect( $this->functions->get_app_link() );
    }

    public function show_create_group( $params = '' ) {
        $heading = __( 'Create a Church', 'disciple-tools-autolink' );
        $name_label = __( 'Church Name', 'disciple-tools-autolink' );
        $name_placeholder = __( 'Enter name...', 'disciple-tools-autolink' );
        $start_date_label = __( 'Church Start Date', 'disciple-tools-autolink' );
        $nonce = 'dt_autolink_create_group';
        $action = $this->functions->get_app_link() . '?action=create-group';
        $cancel_url = $this->functions->get_app_link();
        $cancel_label = __( 'Cancel', 'disciple-tools-autolink' );
        $submit_label = __( 'Create Church', 'disciple-tools-autolink' );
        $error = $params['error'] ?? '';
        $group_fields = DT_Posts::get_post_settings( 'groups' )['fields'];

        include( 'templates/create-group.php' );
    }

    public function create_group() {

        $nonce = sanitize_key( wp_unslash( $_POST['_wpnonce'] ?? '' ) );
        $verify_nonce = $nonce && wp_verify_nonce( $nonce, 'dt_autolink_create_group' );
        $name = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
        $start_date = strtotime( sanitize_text_field( wp_unslash( $_POST['start_date'] ?? '' ) ) );
        $location = sanitize_text_field( wp_unslash( $_POST['location'] ?? '' ) );
        $location = $location ? json_decode( $location, true ) : '';

        if ( isset( $location['user_location'] )
        && isset( $location['user_location']['location_grid_meta'] ) ) {
            $location = $location['user_location']['location_grid_meta'];
        }

        if ( !$verify_nonce || !$name ) {
            $this->show_create_group( [ 'error' => 'Invalid request' ] );
            return;
        }

        $users_contact_id = Disciple_Tools_Users::get_contact_for_user( get_current_user_id() );

        $fields = [
        "title" => $name,
        "members" => [
            "values" => [
                [ "value" => $users_contact_id ]
            ]
        ],
        "leaders" => [
            "values" => [
                [ "value" => $users_contact_id ]
            ]
        ],
        "start_date" => $start_date
        ];

        if ( !empty( $location ) ) {
            $fields['location_grid_meta'] = [
                "values" => $location
            ];
        }

        $group = DT_Posts::create_post( 'groups', $fields, false, false );

        if ( is_wp_error( $group ) ) {
            $this->show_create_group( [ 'error' => $group->get_error_message() ] );
            return;
        }

        wp_redirect( $this->functions->get_app_link() );
    }

    /**
     * Register REST Endpoints
     * @link https://github.com/DiscipleTools/disciple-tools-theme/wiki/Site-to-Site-Link for outside of wordpress authentication
     */
    public function add_endpoints() {
        $namespace = $this->root . '/v1';
        register_rest_route(
            $namespace,
            '/' . $this->type,
            [
            [
                'methods'  => "GET",
                'callback' => [ $this, 'endpoint_get' ],
                'permission_callback' => function ( WP_REST_Request $request ) {
                    $magic = new DT_Magic_URL( $this->root );

                    return $magic->verify_rest_endpoint_permissions_on_post( $request );
                },
            ],
            ]
        );
        register_rest_route(
            $namespace,
            '/' . $this->type,
            [
            [
                'methods'  => "POST",
                'callback' => [ $this, 'endpoint_post' ],
                'permission_callback' => function ( WP_REST_Request $request ) {
                    $magic = new DT_Magic_URL( $this->root );

                    return $magic->verify_rest_endpoint_permissions_on_post( $request );
                },
            ],
            ]
        );
    }

    public function endpoint_post( WP_REST_Request $request ) {
        $params = $request->get_params();
        $params = dt_recursive_sanitize_array( $params );
        $$user_id = get_current_user_id();

        if ( !isset( $params['parts'], $params['action'] ) ) {
            return new WP_Error( __METHOD__, "Missing parameters", [ 'status' => 400 ] );
        }

        switch ( $params['action'] ) {
            case 'tree':
                return $this->build_tree( $request, $params, $user_id );
            case 'onItemDrop':
                return $this->update_tree_group( $request, $params, $user_id );
            case 'update_group_title':
                $new_value = $params['data']['new_value'];
                return DT_Posts::update_post( 'groups', $group_id, [ 'title' => trim( $new_value ) ], false, false );
            default:
                return new WP_Error( __METHOD__, "Invalid action", [ 'status' => 400 ] );
        }
    }

    /**
     * Route REST endpointS by action
     *
     * @param WP_REST_Request $request
     */
    public function endpoint_get( WP_REST_Request $request ) {
        $params = $request->get_params();
        if ( !isset( $params['parts'], $params['action'] ) ) {
            return new WP_Error( __METHOD__, "Missing parameters", [ 'status' => 400 ] );
        }

        switch ( $params['action'] ) {
            //case 'tree':fetch
            //    return $this->build_tree( $request, $params );
            default:
                return new WP_Error( __METHOD__, "Invalid action", [ 'status' => 400 ] );
        }
    }

    public function parse_tree( $tree, $title_list, $root = null, $allowed_group_ids = [] ) {
        $return = [];
        # Traverse the tree and search for direct children of the root
        foreach ( $tree as $child => $parent ) {
            # A direct child is found
            if ( $parent == $root && in_array( $child, $allowed_group_ids ) ) {
                # Remove item from tree (we don't need to traverse this again)
                unset( $tree[$child] );
                # Append the child into result array and parse its children
                $return[] = [
                    'id' => $child,
                    'title' => $child,
                    'name' => $title_list[$child] ?? 'No Name',
                    'children' => $this->parse_tree( $tree, $title_list, $child, $allowed_group_ids ),
                    '__domenu_params' => []
                ];
            }
        }

        return empty( $return ) ? null : $return;
    }

    public function update_tree_group( WP_REST_Request $request, $params, $user_id ) {
        if ( !isset( $params['data']['previous_parent'] ) ) {
            $params['data']['previous_parent'] = 'domenu-0';
        }
        if ( ( ! isset( $params['data']['new_parent'] ) || ( ! isset( $params['data']['self'] ) ) ) ) {
            return 'false';
        }

        global $wpdb;
        if ( 'domenu-0' !== $params['data']['previous_parent'] ) {
            $wpdb->query( $wpdb->prepare(
                "DELETE
                FROM $wpdb->p2p
                WHERE p2p_from = %s
                    AND p2p_to = %s
                    AND p2p_type = 'groups_to_groups'", $params['data']['self'], $params['data']['previous_parent'] ) );
        }

        $wpdb->query( $wpdb->prepare(
            "INSERT INTO $wpdb->p2p (p2p_from, p2p_to, p2p_type)
                    VALUES (%s, %s, 'groups_to_groups');
            ", $params['data']['self'], $params['data']['new_parent'] ) );
        return true;
    }

    /**
     * Make sure the user can do everything we need them to do during this request.
     *
     * @see WP_User::has_cap() in wp-includes/capabilities.php
     * @param  array  $allcaps Existing capabilities for the user
     * @param  string $caps    Capabilities provided by map_meta_cap()
     * @param  array  $args    Arguments for current_user_can()
     * @return array
     */
    public function user_has_cap( $allcaps, $caps, $args ) {
        $allcaps['view_any_contacts'] = true;
        return $allcaps;
    }
}
Disciple_Tools_Autolink_Magic_User_App::instance();
