<?php

namespace DT\Autolink\Providers;

use DT\Autolink\League\Container\ServiceProvider\AbstractServiceProvider;
use DT\Autolink\CodeZone\WPSupport\Assets\AssetQueue;
use DT\Autolink\CodeZone\WPSupport\Assets\AssetQueueInterface;
use DT\Autolink\Services\Assets;
use function DT\Autolink\config;
use function DT\Autolink\namespace_string;
use function DT\Autolink\route_url;
use function DT\Autolink\plugin_url;
use function DT\Autolink\groups_label;
use function DT\Autolink\group_label;

/**
 * Class AssetServiceProvider
 *
 * The AssetServiceProvider class provides asset-related services.
 */
class AssetServiceProvider extends AbstractServiceProvider {

	/**
	 * Provide the services that this provider is responsible for.
	 *
	 * @param string $id The ID to check.
	 * @return bool Returns true if the given ID is provided, false otherwise. */
	public function provides( string $id ): bool
	{
		return in_array($id, [
			AssetQueue::class,
			Assets::class
		]);
	}

	/**
	 * Register method.
	 *
	 * This method is used to register filters and dependencies for the plugin.
	 *
	 * @return void
	 */
	public function register(): void{
		add_filter( namespace_string( 'allowed_styles' ), function ( $allowed_css ) {
			return array_merge( $allowed_css, config( 'assets.allowed_styles' ) );
		} );

		add_filter( namespace_string( 'allowed_scripts' ), function ( $allowed_js ) {
			return array_merge( $allowed_js, config( 'assets.allowed_scripts' ) );
		} );

		add_filter( namespace_string( 'javascript_globals' ), function ( $data ) {
			return array_merge($data, [
				'nonce'        => wp_create_nonce( 'disciple-tools-autolink' ),
				'map_key'      => \DT_Mapbox_API::get_key(),
				'urls'         => [
					'root'           => esc_url_raw( trailingslashit( site_url() ) ),
					'route'          => esc_url_raw( trailingslashit( route_url() ) ),
					'plugin'           => esc_url_raw( trailingslashit( plugin_url() ) ),
					'current'        => esc_url_raw( dt_get_url_path( true ) ),
					'survey'         => esc_url_raw( route_url( "survey" ) ),
					'logout'         => esc_url_raw( route_url( "logout" ) ),
					'reset_password' => wp_lostpassword_url( plugin_url() ),
					'training'       => esc_url_raw( route_url( 'training' ) ),
				],
				'translations' => [
					'add'                => __( 'Add Magic', 'disciple-tools-autolink' ),
					'dt_nav_label'       => __( 'Go to Disciple.Tools', 'disciple-tools-autolink' ),
					'survey_nav_label'   => __( 'Update Survey Answers', 'disciple-tools-autolink' ),
					'feedback_nav_label' => __( 'Give Feedback', 'disciple-tools-autolink' ),
					'logout_nav_label'   => __( 'Log Out', 'disciple-tools-autolink' ),
					'training_nav_label' => __( 'Training', 'disciple-tools-autolink' ),
					'toggle_menu'        => __( 'Toggle Menu', 'disciple-tools-autolink' ),
					'user_greeting,'     => __( 'Hello,', 'disciple-tools-autolink' ),
					'coached_by'         => __( 'Coached by', 'disciple-tools-autolink' ),
					'my_link'            => __( 'My Link', 'disciple-tools-autolink' ),
					'my_churches'        => __( 'My Churches', 'disciple-tools-autolink' ),
					'groups_heading'       => __( 'My', 'disciple-tools-autolink' ) . ' ' . groups_label(),
					'start_date_label'     => __( 'Church Start Date', 'disciple-tools-autolink' ),
					'view_group'           => __( 'View', 'disciple-tools-autolink' ) . ' ' . group_label(),
					'delete_group'         => __( 'Delete', 'disciple-tools-autolink' ) . ' ' .group_label(),
					'delete_group_confirm' => __( 'Are you sure you want to delete this ', 'disciple-tools-autolink' ) . strtolower( group_label() ) . '?',
					'edit_group'           => __( 'Edit', 'disciple-tools-autolink' ) . ' ' . group_label(),
					'save'                 => __( 'Save', 'disciple-tools-autolink' ),
					'more'                 => __( 'More', 'disciple-tools-autolink' ),
					'close'                => __( 'Close', 'disciple-tools-autolink' ),
				]
			]);
		});

		$this->getContainer()->add( AssetQueueInterface::class, function () {
			return new AssetQueue();
		} );

		$this->getContainer()->add( Assets::class, function () {
			return new Assets(
				$this->getContainer()->get( AssetQueueInterface::class )
			);
		} );
	}
}
