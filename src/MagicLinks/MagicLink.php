<?php

namespace DT\Autolink\MagicLinks;

use DT\Autolink\CodeZone\WPSupport\Router\RouteInterface;
use DT\Autolink\League\Route\Http\Exception\NotFoundException;
use DT\Autolink\League\Route\RouteCollectionInterface;
use DT_Magic_Url_Base;
use function DT\Autolink\request;


abstract class MagicLink extends DT_Magic_Url_Base {
	private static $_instance = null;
	public $page_title = 'Magic App';
	public $page_description = 'Magic Link';
	public $root = 'magic';
	public $type = 'app';
	public $post_type = 'user';
	public $show_bulk_send = false;
	public $show_app_tile = false;
	public $meta = [];
	private $meta_key = ''; // Allows for instance specific data.

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

		$this->meta_key  = $this->root . '_' . $this->type . '_magic_key';

		$this->init();

		parent::__construct();

		/**
		 * user_app and module section
		 */
		add_filter( 'dt_settings_apps_list', [ $this, 'dt_settings_apps_list' ], 10, 1 );
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
		if ( ! $this->check_parts_match() ) {
			return;
		}

		$this->boot();
	} // End instance()

	/**
	 * Bootstraps the necessary components or services for the application.
	 *
	 * This method should be implemented to initialize the required resources
	 * or perform setup actions when the application starts.
	 *
	 * @return void
	 */
	abstract public function boot();

	/**
	 * Retrieves the singleton instance of the class.
	 *
	 * @return static The singleton instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new static();
		}

		return self::$_instance;
	}

	/**
	 * Automatically whitelist the current route
	 *
	 * @return void
	 */
	public function whitelist_current_route() {
		$this->type_actions[ $this->get_current_action() ] = 'Current Route';
	}

	/**
	 * Retrieves the current action from the requested URI by parsing and extracting the relevant portion of the URL path.
	 *
	 * @return string The extracted action from the URI.
	 */
	public function get_current_action() {
		$request = request();
        $site_url = site_url();
        $site_url_parts = parse_url( site_url() );
        $site_uri = $site_url_parts['path'] ?? '';
        $uri = str_replace( [ $site_url, $site_uri ], '', $request->getUri()->__toString() );
        $uri = explode( '?', $uri )[0];
        $uri = trim( $uri, '/' );
        $url_parts = explode( '/', $uri );
        $required_parts = array_slice( $url_parts, 3, 1 );
        return implode( '/', $required_parts );
	}

 /**
     * Adds custom routes to the magic link
     *
     * @return void
     */
	public function add_endpoints() {
		// Extend this function to add custom endpoints
	}

	/**
	 * Renders the response for the current request using the router and renderer actions
	 *
	 * @return void
	 */
	public function render() {
		$route = container()->get( RouteInterface::class );
		$route->routes( function ( RouteCollectionInterface $r ) {
			$this->routes( $r );
		} );

		if ( WP_DEBUG ) {
			$route->dispatch();
		} else {
			try {
				$route->dispatch();
			} catch ( NotFoundException $e ) {
				wp_die( esc_html( $e->getMessage() ), esc_attr( $e->getCode() ) );
			}
		}

		$renderer = apply_filters( namespace_string( 'response_renderer' ), false );

		if ( $renderer ) {
			$route->render_with( $renderer );
		}

		$route->resolve();
	}

	/**
	 * Defines the routes for the application.
	 *
	 * @param RouteCollectionInterface $r The route collection instance to define the routes.
	 *
	 * @return void
	 */
	public function routes( RouteCollectionInterface $r ) {}

	/**
	 * Prints the registered scripts for the current context.
	 *
	 * @return void
	 */
	public function print_scripts() {}

	/**
	 * Outputs the styles to the appropriate stream or context.
	 *
	 * @return void
	 */
	public function print_styles() {}
}
