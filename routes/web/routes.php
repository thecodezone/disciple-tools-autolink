<?php
/**
 * @var RouteCollector $r
 */

use DT\Plugin\Controllers\HelloController;
use DT\Plugin\FastRoute\RouteCollector;

$r->get( 'dt/plugin/hello', HelloController::class . '@show' );
