<?php

use DT\Plugin\Controllers\Admin\GeneralSettingsController;

$r->get( 'wp-admin/admin.php?page=dt_plugin', GeneralSettingsController::class . '@show' );
$r->get( 'wp-admin/admin.php?page=dt_plugin&tab=general', GeneralSettingsController::class . '@show' );
$r->post( 'wp-admin/admin.php?page=dt_plugin', GeneralSettingsController::class . '@update' );
$r->post( 'wp-admin/admin.php?page=dt_plugin&tab=general', GeneralSettingsController::class . '@update' );
