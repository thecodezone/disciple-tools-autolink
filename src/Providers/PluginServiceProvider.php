<?php

namespace DT\Autolink\Providers;

use DT\Autolink\Illuminate\Filesystem\Filesystem;
use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Illuminate\Translation\FileLoader;
use DT\Autolink\Illuminate\Translation\Translator;
use DT\Autolink\Illuminate\Validation\Factory;
use function DT\Autolink\group_label;
use function DT\Autolink\groups_label;
use function DT\Autolink\namespace_string;
use function DT\Autolink\plugin_url;
use function DT\Autolink\route_url;

class PluginServiceProvider extends ServiceProvider {
	/**
	 * List of providers to register
	 *
	 * @var array
	 */
	protected $providers = [
		ViewServiceProvider::class,
		ConditionsServiceProvider::class,
		MiddlewareServiceProvider::class,
		AdminServiceProvider::class,
		MagicLinkServiceProvider::class,
		RouterServiceProvider::class,
	];

	/**
	 * Do any setup needed before the theme is ready.
	 * DT is not yet registered.
	 */
	public function register(): void {
		$this->container->singleton( Request::class, function () {
			return Request::capture();
		} );

		foreach ( $this->providers as $provider ) {
			$provider = $this->container->make( $provider );
			$provider->register();
		}

		add_filter( namespace_string( 'allowed_styles' ), function ( $allowed_css ) {
			$allowed_css[] = 'disciple-tools-autolink';
			$allowed_css[] = 'magic_link_css';
			$allowed_css[] = "hint";
			$allowed_css[] = 'group-styles';
			$allowed_css[] = "styles";
			$allowed_css[] = 'chart-styles';
			$allowed_css[] = 'mapbox-gl-css';
			$allowed_css[] = 'portal-app-domenu-css';
			return $allowed_css;
		} );
		add_filter( namespace_string( 'allowed_scripts' ), function ( $allowed_js ) {
			$allowed_js[] = 'disciple-tools-autolink';
			$allowed_js[] = 'magic_link_scripts';
			$allowed_js[] = 'gen-template';
			$allowed_js[] = 'genApiTemplate';
			$allowed_js[] = 'genmapper';
			$allowed_js[] = "d3";
			$allowed_js[] = "dt_groups_wpApiGenmapper";
			$allowed_js[] = 'wp-i18n';
			$allowed_js[] = 'jquery';
			$allowed_js[] = 'jquery-ui-core';
			$allowed_js[] = 'dt_groups_script';
			$allowed_js[] = 'mapbox-search-widget';
			$allowed_js[] = 'mapbox-gl';
			$allowed_js[] = 'mapbox-cookie';
			$allowed_js[] = 'jquery-cookie';
			$allowed_js[] = 'mapbox-search-widget';
			$allowed_js[] = 'jquery-touch-punch';
			$allowed_js[] = 'portal-app-domenu-js';
			$allowed_js[] = 'google-search-widget';
			$allowed_js[] = 'shared-functions';
			$allowed_js[] = 'typeahead-jquery';
			return $allowed_js;
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
					'more'                 => __( 'More', 'disciple-tools-autolink' )
				]
			]);
		});

		$this->registerValidator();
	}

	/**
	 * Register the validator
	 */
	protected function registerValidator(): void {
		$this->container->bind( FileLoader::class, function ( $container ) {
			return new FileLoader( $container->make( Filesystem::class ), 'lang' );
		} );

		$this->container->bind( Factory::class, function ( $container ) {
			$loader     = $container->make( FileLoader::class );
			$translator = new Translator( $loader, 'en' );

			return new Factory( $translator, $container );
		} );
	}


	/**
	 * Do any setup after services have been registered and the theme is ready
	 */
	public function boot(): void {
		foreach ( $this->providers as $provider ) {
			$provider = $this->container->make( $provider );
			$provider->boot();
		}
	}
}
