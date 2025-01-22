<?php

namespace DT\Autolink\Services;

use Disciple_Tools_Google_Geocode_API;
use DT\Autolink\CodeZone\WPSupport\Assets\AssetQueue;
use DT\Autolink\CodeZone\WPSupport\Assets\AssetQueueInterface;
use DT_Mapbox_API;
use function DT\Autolink\config;
use function DT\Autolink\Kucrut\Vite\enqueue_asset;
use function DT\Autolink\namespace_string;
use function DT\Autolink\plugin_path;
use function DT\Autolink\plugin_url;

/**
 * Class Assets
 *
 * This class is responsible for registering necessary actions for enqueueing scripts and styles,
 * whitelisting specific assets, and providing methods for enqueueing scripts and styles for the frontend and admin area.
 *
 * @see https://github.com/kucrut/vite-for-wp
 *
 */
class Assets
{
	/**
	 * AssetQueue Service.
	 *
	 * @var AssetQueue $asset_queue The AssetQueue instance.
	 */
	private AssetQueueInterface $asset_queue;

	public function __construct( AssetQueueInterface $asset_queue )
	{
		$this->asset_queue = $asset_queue;
	}

	/**
	 * Registers the Mapbox functionality by enqueueing scripts and initializing configurations.
	 *
	 * @param int|bool $post_id The ID of the post to associate with Mapbox, or false if not specified.
	 *
	 * @return void
	 */
	public function register_mapbox( $post_id = false ) {
		add_action( 'wp_enqueue_scripts', function () use ( $post_id ) {
			$this->enqueue_mapbox(
				$post_id,
				$post_id ? \DT_Posts::get_post( 'groups', $post_id ) : false
			);
		}, 1 );
	}

	/**
	 * Enqueue the necessary Mapbox scripts and resources for the search widget.
	 *
	 * This method ensures the inclusion of Mapbox header scripts, localization of translation strings,
	 * and enqueues the required JavaScript for the Mapbox search widget. Additionally, it also conditionally
	 * includes Google Geocoder scripts if its API key is available.
	 *
	 * @param int $post_id The ID of the current post being processed.
	 * @param mixed $post The post object or false if not available.
	 *
	 * @return void
	 */
	public function enqueue_mapbox( $post_id, $post ) {
		DT_Mapbox_API::load_mapbox_header_scripts();

		if ( ! function_exists( 'dt_get_location_grid_mirror' ) ) {
			require_once get_template_directory() . '/dt-mapping/globals.php';
		}

	  wp_enqueue_script( 'mapbox-search-widget', plugin_url( '/resources/js/mapbox-search-widget.js' ), [ 'disciple-tools-autolink' ], null, true );
    wp_localize_script(
        'mapbox-search-widget', 'dtMapbox', [
		  'post_type'      => 'groups',
		  'post_id'        => $post_id ?? 0,
		  'post'           => $post ?? false,
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
	  add_action( 'wp_head', [ 'DT_Mapbox_API', 'mapbox_search_widget_css' ] );

	  // load Google Geocoder if key is present.
		if ( class_exists( 'Disciple_Tools_Google_Geocode_API' ) && Disciple_Tools_Google_Geocode_API::get_key() ){
			Disciple_Tools_Google_Geocode_API::load_google_geocoding_scripts();
		}
	}

	/**
	 * Register method to add necessary actions for enqueueing scripts and adding cloaked styles
	 *
	 * @return void
	 */
	public function enqueue() {
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ], 1000 );
			add_action( 'admin_head', [ $this, 'cloak_style' ] );
		} else {
			add_action( 'wp_print_styles', [ $this, 'wp_print_styles' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ] );
			add_action( "wp_head", [ $this, 'cloak_style' ] );
		}
	}

	/**
	 * Reset asset queue
	 *
	 * @return void
	 */
	public function wp_print_styles() {
		$this->asset_queue->filter(
			apply_filters( namespace_string( 'allowed_scripts' ), [] ),
			apply_filters( namespace_string( 'allowed_styles' ), [] )
		);
	}

	/**
	 * Enqueues scripts and styles for the frontend.
	 *
	 * This method enqueues the specified asset(s) for the frontend. It uses the "enqueue_asset" function to enqueue
	 * the asset(s) located in the provided plugin directory path with the given filename. The asset(s) can be JavaScript
	 * or CSS files. Optional parameters can be specified to customize the enqueue behavior.
	 *
	 * @return void
	 * @see https://github.com/kucrut/vite-for-wp
	 */
	public function wp_enqueue_scripts() {
		enqueue_asset(
			config( 'assets.manifest_dir' ),
			'resources/js/plugin.js',
			[
				'handle'    => 'dt-autolink',
				'css-media' => 'all', // Optional.
				'css-only'  => false, // Optional. Set to true to only load style assets in production mode.
				'in-footer' => true, // Optional. Defaults to false.
			]
		);
		wp_localize_script( 'dt-autolink', config( 'assets.javascript_global_scope' ), apply_filters( namespace_string( 'javascript_globals' ), [] ) );
	}



	/**
	 * Enqueues the necessary assets for the admin area.
	 *
	 * This method is responsible for enqueuing the necessary JavaScript and CSS
	 * assets for the admin area. It should be called during the 'admin_enqueue_scripts'
	 * action hook.
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		enqueue_asset(
			plugin_path( '/dist' ),
			'resources/js/admin.js',
			[
				'handle'    => 'disciple-tools-autolink',
				'css-media' => 'all', // Optional.
				'css-only'  => false, // Optional. Set to true to only load style assets in production mode.
				'in-footer' => false, // Optional. Defaults to false.
			]
		);
        wp_localize_script( 'disciple-tools-autolink', config( 'assets.javascript_global_scope' ), apply_filters( namespace_string( 'javascript_globals' ), [] ) );
	}

	/**
	 * Outputs the CSS style for cloaking elements.
	 *
	 * This method outputs the necessary CSS style declaration for cloaking elements
	 * in the HTML markup. The style declaration hides the elements by setting the
	 * "display" property to "none". This method should be called within the HTML
	 * document where cloaking is required.
	 *
	 * @return void
	 */
	public function cloak_style(): void {
		?>
		<style>
        .al-cloak {
            visibility: hidden;
        }
		</style>
		<?php
	}
}
