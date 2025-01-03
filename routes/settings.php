<?php

/**
 * @var RouteCollectionInterface $r
 */

use DT\Autolink\CodeZone\WPSupport\Middleware\HasCap;
use DT\Autolink\CodeZone\WPSupport\Middleware\Nonce;
use DT\Autolink\Controllers\Admin\GeneralSettingsController;
use DT\Autolink\League\Route\RouteCollectionInterface;

$r->group( '/wp-admin', function ( RouteCollectionInterface $r ) {
	$r->get( '/admin.php?page=dt-autolink', [ GeneralSettingsController::class, 'show' ] );
	$r->get( '/admin.php?page=dt-autolink&tab=general', [ GeneralSettingsController::class, 'show' ] );
	$r->post( '/admin.php?page=dt-autolink&tab=general', [ GeneralSettingsController::class, 'update' ] )->middleware( new Nonce( 'dt_admin_form_nonce' ) );
	$r->post( '/admin.php?page=dt-autolink', [ GeneralSettingsController::class, 'update' ] )->middleware( new Nonce( 'dt_admin_form_nonce' ) );
} )->middleware( new HasCap( 'manage_dt' ) );
