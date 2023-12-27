<?php

use DT\Plugin\Controllers\UserMagicLInk\UserMagicLinkController;
use DT\Plugin\Controllers\UserMagicLInk\UserMagicLinkSubpageController;
use FastRoute\RouteCollector;
use function DT\Plugin\container;

/**
 * @var RouteCollector $r
 * @see https://fastroute.thephpleague.com/1.x/advanced-usage/
 */

$container = container();

$r->addGroup( $this->path, function ( $r ) {
	$r->get( '', [ UserMagicLinkController::class, 'show' ] );
	$r->get( '?page=subpage', UserMagicLinkSubpageController::class . 'show' );
} );

$r->addGroup( $this->root . '/v1', function ( $r ) {
	$r->get( '', [ UserMagicLinkController::class, 'index' ] );
} );