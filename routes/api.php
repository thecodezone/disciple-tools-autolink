<?php

/**
 * @var \DT\Autolink\League\Route\Router $r
 */

use DT\Autolink\Controllers\CoachingTreeController;
use DT\Autolink\Controllers\FieldController;
use DT\Autolink\Controllers\GenMapController;
use DT\Autolink\Controllers\GroupController;
use DT\Autolink\League\Route\RouteCollectionInterface;
use DT\Autolink\Middleware\CheckShareCookie;
use DT\Autolink\Middleware\LoggedIn;

$r->group('', function ( RouteCollectionInterface $r ) {
	$r->post( '/coaching-tree', [ CoachingTreeController::class, 'update' ] );
	$r->get( '/coaching-tree', [ CoachingTreeController::class, 'index' ] );
	$r->get( '/groups', [ GroupController::class, 'index' ] );
	$r->post( '/field', [ FieldController::class, 'update' ] );
	$r->get( '/genmap', [ GenMapController::class, 'index' ] );
})->middlewares( [ new LoggedIn(), new CheckShareCookie() ] );
