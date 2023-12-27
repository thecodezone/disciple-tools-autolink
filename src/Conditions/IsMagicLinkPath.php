<?php

namespace DT\Plugin\Conditions;

use DT_Magic_Url_Base;
use function DT\Plugin\container;

/**
 * Check if the current path is a magic link path.
 */
class IsMagicLinkPath implements Condition {
	protected DT_Magic_Url_Base $magic_link;

	/**
	 * @var DT_Magic_Url_Base
	 */
	public function __construct( DT_Magic_Url_Base|string $magic_link ) {
		if ( is_string( $magic_link ) ) {
			$magic_link = container()->make( $magic_link );
		}
		$this->magic_link = $magic_link;
	}

	public function test(): bool {
		return $this->magic_link->check_parts_match();
	}
}