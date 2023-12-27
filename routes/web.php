<?php
/**
 * @var Routes $r
 * @see https://github.com/nikic/FastRoute
 */

use DT\Plugin\Conditions\IsAdminPath;
use DT\Plugin\Conditions\IsMagicLinkPath;
use DT\Plugin\Conditions\IsPluginPath;
use DT\Plugin\Controllers\Admin\GeneralSettingsController;
use DT\Plugin\Controllers\HelloController;
use DT\Plugin\Controllers\StarterMagicLink\HomeController;
use DT\Plugin\Controllers\StarterMagicLink\SubpageController;
use DT\Plugin\Controllers\UserController;
use DT\Plugin\MagicLinks\StarterMagicApp;
use DT\Plugin\Middleware\CanManageDT;
use DT\Plugin\Middleware\LoggedIn;
use DT\Plugin\Plugin;
use DT\Plugin\Services\Router\Routes;

//Only load these routes at /dt/plugin paths
$r->condition( IsPluginPath::class, function ( $r ) {
	//All these routes should prepend /dt/plugin to the path
	$r->group( Plugin::HOME_ROUTE, function ( Routes $r ) {
		$r->get( '/hello', [ HelloController::class, 'show' ] );

		$r->get( '/user', [ UserController::class, 'show', [ 'middleware' => LoggedIn::class ] ] );
	} );

	$r->group( Plugin::HOME_ROUTE . '/api', function ( Routes $r ) {
		$r->get( '/hello', [ HelloController::class, 'show' ] );
	} );
} );

//Only load these routes in the WordPress admin
$r->condition( IsAdminPath::class, function ( Routes $r ) {
	$r->middleware( CanManageDT::class, function ( Routes $r ) {
		$r->group( 'wp-admin/admin.php', function ( Routes $r ) {

			//If no tab is specified, load to the general tab
			$r->get( '?page=dt_plugin', [ GeneralSettingsController::class, 'show' ] );
			$r->post( '?page=dt_plugin', [ GeneralSettingsController::class, 'update' ] );

			//General tab
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
