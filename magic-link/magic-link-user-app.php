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

        if ( class_exists( 'DT_Genmapper_Metrics' ) ) {
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
                'rest_namespace' => $this->root . '/v1/' . $this->type
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

    public function show_app() {
        $logo_url = $this->functions->fetch_logo();
        $greeting = __( 'Hello,', 'disciple-tools-autolink' );
        $user_name = dt_get_user_display_name( get_current_user_id() );
        $coached_by_label = __( 'Coached by', 'disciple-tools-autolink' );
        $link_heading = __( 'My Link', 'disciple-tools-autolink' );
        $share_link_help_text = __( 'Copy this link and share it with people you are coaching.', 'disciple-tools-autolink' );
        $churches_heading = __( 'My Churches', 'disciple-tools-autolink' );
        $share_link = $this->functions->get_share_link();
        $create_church_link = $this->functions->get_app_link() . '?action=create-group';
        $churches = [];
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

        $contact = Disciple_Tools_Users::get_contact_for_user( get_current_user_id() );
        $coach = null;
        $coach_name = '';
        $view_church_label = __( 'View Group', 'disciple-tools-autolink' );

        if ( $contact ) {
            $result = DT_Posts::get_post( 'contacts', $contact, false, false );
            if ( !is_wp_error( $result ) ) {
                $contact = $result;
            }
            $churches = DT_Posts::list_posts('groups', [
                'assigned_to' => [ get_current_user_id() ],
                'sort' => 'last_modified'
            ], false)['posts'] ?? [];
            if ( is_wp_error( $churches ) ) {
                $churches = [];
            }
        }
        if ( $contact && count( $contact['coached_by'] ) ) {
            $coach = $contact['coached_by'][0] ?? null;
            if ( $coach ) {
                $coach = DT_Posts::get_post( 'contacts', $coach['ID'], false, false );
                if ( is_wp_error( $coach ) ) {
                    $coach = '';
                }
                $coach_name = $coach['name'] ?? '';
            }
        }

        include( 'templates/app.php' );
    }

    public function show_genmap() {
        if ( !class_exists( 'DT_Genmapper_Groups_chart' ) ) {
            wp_redirect( $this->functions->get_app_link() );
        }

        include( 'templates/genmap.php' );
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

        include( 'templates/create-group.php' );
    }

    public function create_group() {
        $nonce = sanitize_key( wp_unslash( $_POST['_wpnonce'] ?? '' ) );
        $verify_nonce = $nonce && wp_verify_nonce( $nonce, 'dt_autolink_create_group' );
        $name = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
        $start_date = strtotime( sanitize_text_field( wp_unslash( $_POST['start_date'] ?? '' ) ) );

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
                    'callback' => [ $this, 'update_record' ],
                    'permission_callback' => function ( WP_REST_Request $request ) {
                        $magic = new DT_Magic_URL( $this->root );

                        return $magic->verify_rest_endpoint_permissions_on_post( $request );
                    },
                ],
            ]
        );
    }

    public function update_record( WP_REST_Request $request ) {
        $params = $request->get_params();
        $params = dt_recursive_sanitize_array( $params );
        $post_id = $params["parts"]["post_id"]; //has been verified in verify_rest_endpoint_permissions_on_post()


        $args = [];
        if ( !is_user_logged_in() ) {
            $args["comment_author"] = "Magic Link Submission";
            wp_set_current_user( 0 );
            $current_user = wp_get_current_user();
            $current_user->add_cap( "magic_link" );
            $current_user->display_name = "Magic Link Submission";
        }

        if ( isset( $params["update"]["comment"] ) && !empty( $params["update"]["comment"] ) ) {
            $update = DT_Posts::add_post_comment( $this->post_type, $post_id, $params["update"]["comment"], "comment", $args, false );
            if ( is_wp_error( $update ) ) {
                return $update;
            }
        }

        if ( isset( $params["update"]["start_date"] ) && !empty( $params["update"]["start_date"] ) ) {
            $update = DT_Posts::update_post( $this->post_type, $post_id, [ "start_date" => $params["update"]["start_date"] ], false, false );
            if ( is_wp_error( $update ) ) {
                return $update;
            }
        }

        return true;
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
            //case 'groups_tree':
            //    return $this->get_groups_tree( $request, $params );
            default:
                return new WP_Error( __METHOD__, "Invalid action", [ 'status' => 400 ] );
        }
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
