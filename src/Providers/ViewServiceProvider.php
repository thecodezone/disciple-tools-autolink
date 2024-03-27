<?php

namespace DT\Autolink\Providers;

use DT\Autolink\League\Plates\Engine;
use DT\Autolink\Services\Plates\Escape;
use function DT\Autolink\namespace_string;
use function DT\Autolink\views_path;

/**
 * Register the plates view engine
 * @see https://platesphp.com/
 */
class ViewServiceProvider extends ServiceProvider {
	/**
	 * Register the view engine singleton and any extensions
	 *
	 * @return void
	 */
	public function register(): void {
		$this->container->singleton( Engine::class, function ( $container ) {
			return new Engine( views_path() );
		} );
		$this->container->make( Engine::class )->loadExtension(
			$this->container->make( Escape::class )
		);
		add_filter( namespace_string('allowed_styles'), function ( $allowed_css ) {
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
		add_filter( namespace_string('allowed_scripts'), function ( $allowed_js ) {
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
	}

	/**
	 * Do any setup needed before the theme is ready.
	 * DT is not yet registered.
	 */
	public function boot(): void {
	}
}
