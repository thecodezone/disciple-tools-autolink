<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

/**
 * Adds a non-object (neither post or user) magic link page.
 */
class Disciple_Tools_Autolink_Login_App extends DT_Magic_Url_Base
{
    public $magic = false;
    public $parts = false;
    public $page_title = 'Auto Link | Login';
    public $root = 'autolink';
    public $type = 'page';
    public $type_name = 'Auto Link';
    public static $token = 'autolink_login';
    public $functions;

    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    public function __construct() {
        parent::__construct();

        $url = dt_get_url_path();
        $this->functions = Disciple_Tools_Autolink_Magic_Functions::instance();

        if ( ( $this->root ) === $url ) {

            $this->magic = new DT_Magic_URL( $this->root );
            $this->parts = $this->magic->parse_url_parts();


            // register url and access
            add_action( "template_redirect", [ $this, 'theme_redirect' ] );
            add_filter( 'dt_blank_access', function (){ return true;
            }, 100, 1 );
            add_filter( 'dt_allow_non_login_access', function (){ return true;
            }, 100, 1 );
            add_filter( 'dt_override_header_meta', function (){ return true;
            }, 100, 1 );

            // header content
            add_filter( "dt_blank_title", [ $this, "page_tab_title" ] ); // adds basic title to browser tab
            add_action( 'wp_print_scripts', [ $this, 'print_scripts' ], 1500 ); // authorizes scripts
            add_action( 'wp_print_styles', [ $this, 'print_styles' ], 1500 ); // authorizes styles


            // page content
            add_action( 'dt_blank_head', [ $this, '_header' ] );
            add_action( 'dt_blank_footer', [ $this, '_footer' ] );
            add_action( 'dt_blank_body', [ $this, 'body' ] ); // body for no post key

            add_filter( 'dt_magic_url_base_allowed_css', [ $this->functions, 'dt_magic_url_base_allowed_css' ], 10, 1 );
            add_filter( 'dt_magic_url_base_allowed_js', [ $this->functions, 'dt_magic_url_base_allowed_js' ], 10, 1 );
            add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ], 100 );
        }

        if ( dt_is_rest() ) {
            add_action( 'rest_api_init', [ $this, 'add_endpoints' ] );
            add_filter( 'dt_allow_rest_access', [ $this, 'authorize_url' ], 10, 1 );
        }
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

    public function body() {
        include('templates/index.php');
    }

    /**
     * Register REST Endpoints
     * @link https://github.com/DiscipleTools/disciple-tools-theme/wiki/Site-to-Site-Link for outside of wordpress authentication
     */
    public function add_endpoints() {
        $namespace = $this->root . '/v1';
        register_rest_route(
            $namespace,
            '/'.$this->type,
            [
                [
                    'methods'  => WP_REST_Server::CREATABLE,
                    'callback' => [ $this, 'endpoint' ],
                ],
            ]
        );
    }

    public function endpoint( WP_REST_Request $request ) {
        $params = $request->get_params();

        if ( ! isset( $params['parts'], $params['action'] ) ) {
            return new WP_Error( __METHOD__, "Missing parameters", [ 'status' => 400 ] );
        }

        $params = dt_recursive_sanitize_array( $params );

        switch ( $params['action'] ) {
            case 'get':
                // do something
                return true;
            case 'excited':
                // do something else
            default:
                return true;
        }
    }

}
Disciple_Tools_Autolink_Login_App::instance();
