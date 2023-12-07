<?php

namespace DT\Plugin\Controllers\UserMagicLInk;

use DT\Plugin\MagicLinks\UserMagicLink;
use function DT\Plugin\view;

class UserMagicLinkSubpageController {

	public function __construct( UserMagicLink $magic_link ) {
		$this->magic_link = $magic_link;
	}

	public function show() {
		$home_url = $this->magic_link->url;
		view( 'user-magic-link/subpage', compact( 'home_url' ) );
	}
}
