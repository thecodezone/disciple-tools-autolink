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
use DT\Autolink\Controllers\AppController;
use DT\Autolink\Controllers\CoachingTreeController;
use DT\Autolink\Controllers\FieldController;
use DT\Autolink\Controllers\GenMapController;
use DT\Autolink\Controllers\GroupController;
use DT\Autolink\Controllers\LoginController;
use DT\Autolink\Controllers\RegisterController;
use DT\Autolink\Controllers\SurveyController;
use DT\Autolink\Controllers\TrainingController;
use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Symfony\Component\HttpFoundation\Response;

$r->get( '/', [ GenMapController::class, 'show', [ 'middleware' => [ 'genmap', 'auth', 'check_share' ] ] ] );
$r->get( 'login', [ LoginController::class, 'login', [ 'middleware' => 'guest' ] ] );
$r->post( 'login', [ LoginController::class, 'process', [ 'middleware' => 'guest' ] ] );
$r->get( 'register', [ RegisterController::class, 'register' ] );
$r->post( 'register', [ RegisterController::class, 'process' ] );

$r->middleware( [ 'auth', 'check_share' ], function ( Routes $r ) {
	$r->middleware('survey', function ( Routes $r ) {
		$r->get( 'groups', [ AppController::class, 'show' ] );
		$r->get( 'training', [ TrainingController::class, 'show' ] );
		$r->get( 'coaching-tree', [ CoachingTreeController::class, 'show' ] );
	});

	$r->get( 'logout', [ LoginController::class, 'logout' ] );
	$r->get( 'survey', [ SurveyController::class, 'show' ] );
	$r->get( 'survey/{page}', [ SurveyController::class, 'show' ] );
	$r->get( 'groups/create', [ GroupController::class, 'create' ] );
	$r->get( 'groups/modal/create', [ GroupController::class, 'create_modal' ] );
	$r->get( 'groups/{group_id}/edit', [ GroupController::class, 'edit' ] );
	$r->get( 'groups/{group_id}/modal', [ GroupController::class, 'show_modal' ] );

	$r->middleware( 'nonce:disciple-tools-autolink', function ( Routes $r ) {
		$r->get( 'groups/parent-group-field', [ GroupController::class, 'parent_group_field' ] );
		$r->post( 'groups', [ GroupController::class, 'store' ] );
		$r->post( 'groups/{group_id}', [ GroupController::class, 'update' ] );
		$r->get( 'groups/{group_id}/delete', [ GroupController::class, 'destroy' ] );
		$r->post( 'survey/{page}', [ SurveyController::class, 'update' ] );
		$r->group("api", function ( Routes $r ) {
			$r->post( '/coaching-tree', [ CoachingTreeController::class, 'update' ] );
			$r->get( '/coaching-tree', [ CoachingTreeController::class, 'index' ] );
			$r->get( '/groups', [ GroupController::class, 'index' ] );
			$r->post( '/field', [ FieldController::class, 'update' ] );
			$r->get( '/genmap', [ GenMapController::class, 'index' ] );
		});
	});

	$r->get( 'groups/{group_id}', [ GroupController::class, 'show' ] );
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
