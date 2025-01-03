<?php

namespace DT\Autolink\MagicLinks;

use function DT\Autolink\namespace_string;
use function DT\Autolink\route_url;


class CoachingLink extends MagicLink {

	public $page_title = 'Coached by autolink';
	public $page_description = 'Share this link with someone this contact is coaching.';
	public $root = 'autolink';
	public $type = 'coached_by';
	public $post_type = 'contact';
	public $show_bulk_send = true;
	public $show_app_tile = true;

	/**
	 * Do any action before the magic link is bootstrapped
	 * @return void
	 */
	public function init() {
		$this->whitelist_current_route();
	}

	public function boot() {
		$coach = \DT_Posts::get_post( $this->post_type, $this->parts['post_id'], true, false );
		$cookie_name = namespace_string( 'coached_by' );
		if ( !isset( $_COOKIE[$cookie_name] ) ) {
			setcookie( $cookie_name, $coach['ID'], time() + ( 86400 * 30 ), "/" );
		}

		wp_redirect( route_url() );
	}
}
