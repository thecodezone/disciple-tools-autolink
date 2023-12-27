<?php

namespace DT\Plugin\Middleware;

use Illuminate\Http\Request;
use WP_HTTP_Response;

interface Middleware {

	public function handle( Request $request, WP_HTTP_Response $response, $next );
}
