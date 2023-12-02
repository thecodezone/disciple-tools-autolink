<?php

register_rest_route( 'cz/plugin/v1', 'hello', [
	[
		'methods'   => 'GET',
		'callback'  => [ \CZ\Plugins\container()->make( \CZ\Plugin\Controllers\HelloController::class ), 'data' ]
	]
] );
