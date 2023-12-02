<?php
/**
 * @var \CZ\FastRoute\RouteCollector $r
 */

$r->get( 'cz/plugin/hello', \CZ\Plugin\Controllers\HelloController::class . '@show' );
