<?php

namespace DT\Autolink\Services;

use DT\Autolink\Illuminate\Support\Str;
use DT_Mapbox_API;
use function DT\Autolink\group_label;
use function DT\Autolink\groups_label;
use function DT\Autolink\Kucrut\Vite\enqueue_asset;
use function DT\Autolink\plugin_path;
use function DT\Autolink\namespace_string;
use function DT\Autolink\plugin_url;
use function DT\Autolink\route_url;
use function DT\Autolink\share_url;
use const DT\Autolink\Kucrut\Vite\VITE_CLIENT_SCRIPT_HANDLE;

class Assets {
	private static $enqueued = false;

	/**
	 * Register method to add necessary actions for enqueueing scripts and adding cloaked styles
	 *
	 * @return void
	 */
	public function enqueue() {
		if ( self::$enqueued ) {
			return;
		}
		self::$enqueued = true;

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ], 1000 );
			add_action( 'admin_head', [ $this, 'cloak_style' ] );
		} else {
			add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ], 1000 );
			add_action( "wp_head", [ $this, 'cloak_style' ] );
		}
	}

	/**
	 * Reset asset queue
	 * @return void
	 */
	private function filter_asset_queue() {
		global $wp_scripts;
		global $wp_styles;

    $whitelist = apply_filters( namespace_string( 'allowed_scripts' ), [] );
		foreach ( $wp_scripts->registered as $script ) {
			if ( in_array( $script->handle, $whitelist ) ) {
			  continue;
			}
			wp_dequeue_script( $script->handle );
		}

	  $whitelist = apply_filters( namespace_string( 'allowed_styles' ), [] );
		foreach ( $wp_styles->registered as $style ) {
			if ( in_array( $script->handle, $whitelist ) ) {
				continue;
			}
			wp_dequeue_style( $style->handle );
		}
	}

	private function whitelist_vite() {
		global $wp_scripts;
		global $wp_styles;

	  $scripts = [];
	  $styles = [];

		foreach ( $wp_scripts->registered as $script ) {
			if ( $this->is_vite_asset( $script->handle ) ) {
				$scripts[] = $script->handle;
			}
		}

    add_filter( namespace_string( 'allowed_scripts' ), function ( $allowed ) use ( $scripts ) {
			  return array_merge( $allowed, $scripts );
    });

		foreach ( $wp_styles->registered as $style ) {
			if ( $this->is_vite_asset( $style->handle ) ) {
				$styles[] = $style->handle;
			}
		}

    add_filter( namespace_string( 'allowed_styles' ), function ( $allowed ) use ( $styles ) {
			  return array_merge( $allowed, $styles );
    });
	}

	/**
	 * Determines if the given asset handle is allowed.
	 *
	 * This method checks if the provided asset handle is contained in the list of allowed handles.
	 * Allows the Template script file and the Vite client script file for dev use.
	 *
	 * @param string $asset_handle The asset handle to check.
	 *
	 * @return bool True if the asset handle is allowed, false otherwise.
	 */
	private function is_vite_asset( $asset_handle ) {
		if ( Str::contains( $asset_handle, [
			'disciple-tools-autolink',
			VITE_CLIENT_SCRIPT_HANDLE
		] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Enqueues scripts and styles for the frontend.
	 *
	 * This method enqueues the specified asset(s) for the frontend. It uses the "enqueue_asset" function to enqueue
	 * the asset(s) located in the provided plugin directory path with the given filename. The asset(s) can be JavaScript
	 * or CSS files. Optional parameters can be specified to customize the enqueue behavior.
	 *
	 * @return void
	 */
	public function wp_enqueue_scripts() {
		enqueue_asset(
			plugin_path( '/dist' ),
			'resources/js/plugin.js',
			[
				'handle'    => 'disciple-tools-autolink',
				'css-media' => 'all', // Optional.
				'css-only'  => false, // Optional. Set to true to only load style assets in production mode.
				'in-footer' => true, // Optional. Defaults to false.
			]
		);
    $this->whitelist_vite();
	  $this->filter_asset_queue();
    wp_localize_script( 'disciple-tools-autolink', '$autolink', apply_filters( namespace_string( 'javascript_globals' ), [] ) );
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