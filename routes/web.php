<?php
/**
 * @var Routes $r
 * @see https://github.com/nikic/FastRoute
 */

use DT\Plugin\Conditions\IsAdminPath;
use DT\Plugin\Conditions\IsPluginPath;
use DT\Plugin\Controllers\HelloController;
use DT\Plugin\Controllers\UserController;
use DT\Plugin\Middleware\LoggedIn;
use DT\Plugin\Services\Router\Routes;

//Only load these routes at /dt/plugin paths
$r->addCondition( IsPluginPath::class, function ( $r ) {
	//All these routes should prepend /dt/plugin to the path
	$r->addGroup( 'dt/plugin', function ( Routes $r ) {
		$r->get( '/hello', [ HelloController::class, 'show' ] );

		$r->get( '/user', [ UserController::class, 'show', [ 'middleware' => LoggedIn::class ] ] );
	} );
} );

//Only load these routes in the WordPress admin
$r->addCondition( IsAdminPath::class, function ( Routes $r ) {

	//All these routes should prepend the settings page path
	$r->addGroup( 'wp-json/dt/plugin/v1', function ( Routes $r ) {

		$r->get( '/hello', [ HelloController::class, 'data' ] );
	} );
} );
