<?php

namespace DT\Autolink\MagicLinks;

use DT_Magic_Url_Base;
use DT_Posts;
use function DT\Autolink\namespace_string;
use function DT\Autolink\route_url;


class GroupLeaderLink extends MagicLink
{

    public $page_title = 'Group leader autolink';
    public $page_description = 'Share this link with the leader of this group.';
    public $root = 'autolink';
    public $type = 'group_leader';
    public $post_type = 'group';
    public $show_bulk_send = true;
    public $show_app_tile = true;

	/**
	 * Do any action before the magic link is bootstrapped
	 * @return void
	 */
	public function init() {
		$this->whitelist_current_route();
	}

    public function boot()
    {
        $group = DT_Posts::get_post( $this->post_type, $this->parts['post_id'], true, false );
        $cookie_name = namespace_string( 'leads_group' );

        if ( !isset( $cookie_name ) ) {
            setcookie( $cookie_name, $group['ID'], time() + ( 86400 * 30 ), "/" );
        }

        if ( request()->has( 'coached_by' ) ) {
            $cookie_name = namespace_string( 'coached_by' );
            setcookie( $cookie_name, request()->get( 'coached_by' ), time() + ( 86400 * 30 ), "/" );
        }

        if ( request()->has( 'contact' ) ) {
            $cookie_name = namespace_string( 'contact' );
            setcookie( $cookie_name, request()->get( 'contact' ), time() + ( 86400 * 30 ), "/" );
        }

        wp_redirect( route_url() );
    }
}
