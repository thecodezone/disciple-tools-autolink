<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.


/**
 * Class Disciple_Tools_Autolink_Magic_Link
 */
class Disciple_Tools_Autolink_Magic_Contact_App extends DT_Magic_Url_Base {
    public $magic = false;
    public $parts = false;
    public $page_title = 'Starter - Magic Links - Post Type';
    public $page_description = 'Post Type - Magic Links.';
    public $root = "autolink-contact";
    public $type = 'autolink-contact';
    public $post_type = 'contacts';
    private $meta_key = '';
    public $show_bulk_send = false;
    public $show_app_tile = true; // show this magic link in the Apps tile on the post record
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
        $this->functions = Disciple_Tools_Autolink_Magic_Functions::instance();
        $this->meta = [
            'app_type'      => 'magic_link',
            'post_type'     => $this->post_type,
            'contacts_only' => true,
            'fields'        => [
                [
                    'id'    => 'name',
                    'label' => 'Name'
                ]
            ]
        ];

        $this->meta_key = $this->root . '_' . $this->type . '_magic_key';
        parent::__construct();

        /**
         * post type and module section
         */
//        add_action( 'dt_details_additional_section', [ $this, 'dt_details_additional_section' ], 30, 2 );
//        add_filter( 'dt_details_additional_tiles', [ $this, 'dt_details_additional_tiles' ], 10, 2 );
        add_action( 'rest_api_init', [ $this, 'add_endpoints' ] );


        /**
         * tests if other URL
         */
        $url = dt_get_url_path();
        if ( strpos( $url, $this->root . '/' . $this->type ) === false ) {
            return;
        }
        /**
         * tests magic link parts are registered and have valid elements
         */
        if ( !$this->check_parts_match() ){
            return;
        }

        // load if valid url
        add_action( 'dt_blank_body', [ $this, 'body' ] ); // body for no post key
        add_filter( 'dt_magic_url_base_allowed_css', [ $this->functions, 'dt_magic_url_base_allowed_css' ], 10, 1 );
        add_filter( 'dt_magic_url_base_allowed_js', [ $this->functions, 'dt_magic_url_base_allowed_js' ], 10, 1 );
        add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ], 100 );

    }

    public function wp_enqueue_scripts() {
        $this->functions->wp_enqueue_scripts();
        wp_localize_script(
            'magic_link_scripts', 'magic', [
                'parts' => $this->parts,
                'rest_namespace' => $this->root . '/v1/' . $this->type,
            ]
        );
    }

    /**
     * Post Type Tile Examples
     */
    public function dt_details_additional_tiles( $tiles, $post_type = "" ) {
        if ( $post_type === $this->post_type ){
            $tiles["dt_starters_magic_url"] = [
                "label" => __( "Magic Url", 'disciple-tools-autolink' ),
                "description" => "The Magic URL sets up a page accessible without authentication, only the link is needed. Useful for small applications liked to this record, like quick surveys or updates."
            ];
        }
        return $tiles;
    }

    public function dt_details_additional_section( $section, $post_type ) {
        // test if campaigns post type and campaigns_app_module enabled
        if ( $post_type === $this->post_type ) {
            if ( 'dt_starters_magic_url' === $section ) {
                $link = DT_Magic_URL::get_link_url_for_post( $post_type, get_the_ID(), $this->root, $this->type )
                ?>
                <p>See help <img class="dt-icon" src="<?php echo esc_html( get_template_directory_uri() . '/dt-assets/images/help.svg' ) ?>"/> for description.</p>
                <a class="button" href="<?php echo esc_html( $link ); ?>" target="_blank">Open magic link</a>
                <?php
            }
        }
    }

    public function body(){
        include('templates/index.php');
    }

    /**
     * Register REST Endpoints
     * @link https://github.com/DiscipleTools/disciple-tools-theme/wiki/Site-to-Site-Link for outside of wordpress authentication
     */
    public function add_endpoints() {
        $namespace = $this->root . '/v1';
        register_rest_route(
            $namespace, '/' . $this->type, [
                [
                    'methods'  => "GET",
                    'callback' => [ $this, 'endpoint_get' ],
                    'permission_callback' => function( WP_REST_Request $request ){
                        $magic = new DT_Magic_URL( $this->root );

                        return $magic->verify_rest_endpoint_permissions_on_post( $request );
                    },
                ],
            ]
        );
        register_rest_route(
            $namespace, '/' . $this->type, [
                [
                    'methods'  => "POST",
                    'callback' => [ $this, 'update_record' ],
                    'permission_callback' => function( WP_REST_Request $request ){
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
        if ( !is_user_logged_in() ){
            $args["comment_author"] = "Magic Link Submission";
            wp_set_current_user( 0 );
            $current_user = wp_get_current_user();
            $current_user->add_cap( "magic_link" );
            $current_user->display_name = "Magic Link Submission";
        }

        if ( isset( $params["update"]["comment"] ) && !empty( $params["update"]["comment"] ) ){
            $update = DT_Posts::add_post_comment( $this->post_type, $post_id, $params["update"]["comment"], "comment", $args, false );
            if ( is_wp_error( $update ) ){
                return $update;
            }
        }

        if ( isset( $params["update"]["start_date"] ) && !empty( $params["update"]["start_date"] ) ){
            $update = DT_Posts::update_post( $this->post_type, $post_id, [ "start_date" => $params["update"]["start_date"] ], false, false );
            if ( is_wp_error( $update ) ){
                return $update;
            }
        }

        return true;
    }

    public function endpoint_get( WP_REST_Request $request ) {
        $params = $request->get_params();
        if ( ! isset( $params['parts'], $params['action'] ) ) {
            return new WP_Error( __METHOD__, "Missing parameters", [ 'status' => 400 ] );
        }

        $data = [];

        $data[] = [ 'name' => 'List item' ]; // @todo remove example
        $data[] = [ 'name' => 'List item' ]; // @todo remove example

        return $data;
    }
}
Disciple_Tools_Autolink_Magic_Contact_App::instance();
