<?php

use DT\Plugin\Controllers\UserMagicLInk\UserMagicLinkController;
use DT\Plugin\Controllers\UserMagicLInk\UserMagicLinkSubpageController;
use DT\Plugin\MagicLinks\UserMagicLink;
use function DT\Plugin\container;

$container  = container();
$magic_link = $container->make( UserMagicLink::class );

$r->get( $magic_link->path, UserMagicLinkController::class . '@show' );
$r->get( $magic_link->path . '?page=subpage', UserMagicLinkSubpageController::class . '@show' );
