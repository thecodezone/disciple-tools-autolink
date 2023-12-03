<?php

use DT\Plugin\Controllers\HelloController;
use function DT\Plugin\container;

register_rest_route( 'dt/plugin/v1', 'hello', [
	[
		'methods'             => 'GET',
		'callback'            => [ container()->make( HelloController::class ), 'data' ],
		'permission_callback' => '__return_true',
	]
] );
