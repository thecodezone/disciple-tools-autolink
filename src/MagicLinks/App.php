<?php

namespace DT\Autolink\MagicLinks;

use DT\Autolink\Services\Template;
use function DT\Autolink\container;
use function DT\Autolink\namespace_string;
use function DT\Autolink\plugin;

class App extends UserMagicApp {
	public $page_title = 'Autolink';
	public $page_description = 'Autolink';
	public $root = 'autolink';
	public $type = 'app';
	public $post_type = 'user';
	public $show_bulk_send = false;
	public $show_app_tile = false;

	public function boot() {
		add_action( 'template_redirect', [ plugin(), 'template_redirect' ], 9 );
		add_filter( 'dt_magic_url_base_allowed_css', [ $this, 'allowed_css' ], 10, 1 );
		add_filter( 'dt_magic_url_base_allowed_js', [ $this, 'allowed_js' ], 10, 1 );
	}

	/**
	 * Tests the url and if it matches as an approved magic link it loads the appropriate template.
	 * @param $template_for_url
	 * @return mixed
	 */
	public function register_url( $template_for_url ){
		return $template_for_url;
	}

	/**
	 * @param $allowed_css
	 * @return mixed
	 */
	public function allowed_css( $allowed_css ){
		return apply_filters( namespace_string( 'allowed_styles' ), $allowed_css );
	}

	/**
	 * @param $allowed_scripts
	 * @return mixed
	 */
	public function allowed_js( $allowed_js ){
		return apply_filters( namespace_string( 'allowed_scripts' ), $allowed_js );
	}
}
