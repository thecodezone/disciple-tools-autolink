<?php

/**
 * @var RouteCollector $r
 * @see https://github.com/nikic/FastRoute
 */

use DT\Plugin\Controllers\Admin\GeneralSettingsController;
use DT\Plugin\FastRoute\RouteCollector;

$r->get( 'wp-admin/admin.php?page=dt_plugin', [ GeneralSettingsController::class, 'show' ] );
$r->get( 'wp-admin/admin.php?page=dt_plugin&tab=general', [ GeneralSettingsController::class, 'show' ] );
$r->post( 'wp-admin/admin.php?page=dt_plugin', [ GeneralSettingsController::class, 'update' ] );
$r->post( 'wp-admin/admin.php?page=dt_plugin&tab=general', [ GeneralSettingsController::class, 'update' ] );