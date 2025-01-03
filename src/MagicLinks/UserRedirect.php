<?php

namespace DT\Autolink\MagicLinks;

use function DT\Autolink\route_url;

/**
 * Redirect to the home route.
 * We aren't using the app, but still want
 * it to show up in the users magic app list.
**/
class UserRedirect extends MagicLink {
	public $page_title = 'Autolink';
	public $page_description = 'A simple user interface for users to add churches and groups into Disciple Tools.';
	public $root = 'autolink';
	public $type = 'app';
	public $post_type = 'user';
	public $show_bulk_send = false;
	public $show_app_tile = false;

	/**
	 * Do any action before the magic link is bootstrapped
	 * @return void
	 */
	public function init() {
		$this->whitelist_current_route();
	}

	public function boot() {
		wp_redirect( route_url() );
	}
}
