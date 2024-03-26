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

use DT\Autolink\CodeZone\Router\FastRoute\Routes;
use DT\Autolink\Controllers\Admin\GeneralSettingsController;
use DT\Autolink\Controllers\HelloController;
use DT\Autolink\Controllers\LoginController;
use DT\Autolink\Controllers\MagicLink\HomeController;
use DT\Autolink\Controllers\MagicLink\ShareController;
use DT\Autolink\Controllers\MagicLink\SubpageController;
use DT\Autolink\Controllers\MagicLink\TrainingController;
use DT\Autolink\Controllers\RedirectController;
use DT\Autolink\Controllers\RegisterController;
use DT\Autolink\Controllers\UserController;
use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Symfony\Component\HttpFoundation\Response;


$r->condition( 'plugin', function ( Routes $r ) {
	$r->get( 'autolink', [ RedirectController::class, 'show', [ 'middleware' => 'auth' ] ] );

	$r->group( 'autolink', function ( Routes $r ) {
		$r->get( '/login', [ LoginController::class, 'login', [ 'middleware' => 'guest' ] ] );
		$r->post( '/login', [ LoginController::class, 'process', [ 'middleware' => 'guest' ] ] );
		$r->get( '/register', [ RegisterController::class, 'register' ] );
		$r->post( '/register', [ RegisterController::class, 'process' ] );
	} );

	$r->middleware( 'magic:autolink/app', function ( Routes $r ) {
		$r->group( 'autolink/app/{key}', function ( Routes $r ) {
			$r->middleware( [ 'auth', 'check_share' ], function ( Routes $r ) {
				$r->get( '', [ HomeController::class, 'show' ] );
				$r->get( '/training', [ TrainingController::class, 'show' ] );
				$r->get( '/logout', [ LoginController::class, 'logout' ] );
			} );

			$r->get( '/share', [ ShareController::class, 'show' ] );

			$r->get( '/{path:.*}', fn( Request $request, Response $response ) => $response->setStatusCode( 404 ) );
		} );
	} );
} );

$r->condition( 'backend', function ( Routes $r ) {
	$r->middleware( 'can:manage_dt', function ( Routes $r ) {
		$r->group( 'wp-admin/admin.php', function ( Routes $r ) {
			$r->get( '?page=disciple_tools_autolink', [ GeneralSettingsController::class, 'show' ] );
			$r->get( '?page=disciple_tools_autolink&tab=general', [ GeneralSettingsController::class, 'show' ] );

			$r->middleware( 'nonce:dt_admin_form_nonce', function ( Routes $r ) {
				$r->post( '?page=disciple_tools_autolink', [ GeneralSettingsController::class, 'update' ] );
				$r->post( '?page=disciple_tools_autolink&tab=general', [ GeneralSettingsController::class, 'update' ] );
			} );
		} );
	} );
} );