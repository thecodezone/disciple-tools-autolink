<?php
/**
 * Conditions are used to determine if a group of routes should be registered.
 *
 * Groups are used to register a group of routes with a common URL prefix.
 *
 * Middleware is used to modify requests before they are handled by a controller, or to modify responses before they are returned to the client.
 *
 * Routes are used to bind a URL to a controller.
 *
 * @var Routes $r
 * @see https://github.com/thecodezone/wp-router
 */

use DT\Plugin\CodeZone\Router\FastRoute\Routes;
use DT\Plugin\Conditions\IsAdminPath;
use DT\Plugin\Conditions\IsMagicLinkPath;
use DT\Plugin\Conditions\IsPluginPath;
use DT\Plugin\Controllers\Admin\GeneralSettingsController;
use DT\Plugin\Controllers\HelloController;
use DT\Plugin\Controllers\StarterMagicLink\HomeController;
use DT\Plugin\Controllers\StarterMagicLink\SubpageController;
use DT\Plugin\Controllers\UserController;
use DT\Plugin\MagicLinks\StarterMagicApp;
use DT\Plugin\Middleware\LoggedIn;
use DT\Plugin\Middleware\ManagesDT;
use DT\Plugin\Plugin;

$r->condition( IsPluginPath::class, function ( $r ) {
	$r->group( Plugin::HOME_ROUTE, function ( Routes $r ) {
		$r->get( '/hello', [ HelloController::class, 'show' ] );
		$r->get( '/user', [ UserController::class, 'show', [ 'middleware' => LoggedIn::class ] ] );
	} );

	$r->group( Plugin::HOME_ROUTE . '/api', function ( Routes $r ) {
		$r->get( '/hello', [ HelloController::class, 'show' ] );
	} );
} );

$r->condition( IsAdminPath::class, function ( Routes $r ) {
	$r->middleware( ManagesDT::class, function ( Routes $r ) {
		$r->group( 'wp-admin/admin.php', function ( Routes $r ) {
			$r->get( '?page=dt_plugin', [ GeneralSettingsController::class, 'show' ] );
			$r->post( '?page=dt_plugin', [ GeneralSettingsController::class, 'update' ] );
			$r->get( '?page=dt_plugin&tab=general', [ GeneralSettingsController::class, 'show' ] );
			$r->post( '?page=dt_plugin&tab=general', [ GeneralSettingsController::class, 'update' ] );
		} );
	} );
} );

$r->condition( new IsMagicLinkPath( StarterMagicApp::class ), function ( Routes $r ) {
	$r->group( 'starter-magic-app/app/{key}', function ( Routes $r ) {
		$r->get( '', [ HomeController::class, 'show' ] );
		$r->get( '/subpage', [ SubpageController::class, 'show' ] );
	} );
} );
