<?php

namespace DT\Plugin\Controllers\UserMagicLInk;

use DT\Plugin\MagicLinks\UserMagicLink;
use function DT\Plugin\plugin;

class UserMagicLinkSubpageController {

	public function __construct( UserMagicLink $magic_link ) {
		$this->magic_link = $magic_link;
	}

	public function show() {
		$home_url = $this->magic_link->url;
		include plugin()->templates_path . '/user-magic-link/subpage.php';
	}
}
