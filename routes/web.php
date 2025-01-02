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
use DT\Autolink\CodeZone\WPSupport\Middleware\HasCap;
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
use DT\Autolink\League\Route\RouteCollectionInterface;
use DT\Autolink\Middleware\CheckShareCookie;
use DT\Autolink\Middleware\LoggedIn;
use DT\Autolink\Middleware\SurveyCompleted;
use DT\Autolink\Symfony\Component\HttpFoundation\Response;

$r->get( '/', [ GenMapController::class, 'show', [ 'middleware' => [ 'genmap', 'auth' ] ] ] );
$r->get( 'login', [ LoginController::class, 'login', [ 'middleware' => 'guest' ] ] );
$r->post( 'login', [ LoginController::class, 'process', [ 'middleware' => 'guest' ] ] );
$r->get( 'register', [ RegisterController::class, 'register' ] );
$r->post( 'register', [ RegisterController::class, 'process' ] );

$r->group('', function ( RouteCollectionInterface $r ) {
	$r->get( 'groups', [ AppController::class, 'show' ] )->middleware( new SurveyCompleted() );
	$r->get( 'training', [ TrainingController::class, 'show' ] )->middleware( new SurveyCompleted() );
	$r->get( 'coaching-tree', [ CoachingTreeController::class, 'show' ] )->middleware( new SurveyCompleted() );

	$r->get( 'logout', [ LoginController::class, 'logout' ] );
	$r->get( 'survey', [ SurveyController::class, 'show' ] );
	$r->get( 'survey/{page}', [ SurveyController::class, 'show' ] );
	$r->get( 'groups/create', [ GroupController::class, 'create' ] );
	$r->get( 'groups/modal/create', [ GroupController::class, 'create_modal' ] );
	$r->get( 'groups/{group_id}/edit', [ GroupController::class, 'edit' ] );
	$r->get( 'groups/{group_id}/modal', [ GroupController::class, 'show_modal' ] );

	$r->get( 'groups/{group_id}', [ GroupController::class, 'show' ] );
})->middlewares( [ new LoggedIn(), new CheckShareCookie(), new HasCap( 'manage_contacts' ) ] );
