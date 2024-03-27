<?php

namespace DT\Autolink\Controllers;

use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Illuminate\Http\Response;
use DT\Autolink\Services\Charts\GenmapChart;
use function DT\Autolink\template;

class GenMapController {
	public function show( Request $request, Response $response, GenmapChart $chart ) {
		if ( ! class_exists( 'DT_Genmapper_Groups_chart' ) ) {
			return $response->setStatusCode( 404 );
		}

		return template( 'genmap' );
	}

	public function index( Request $request, Response $response, GenmapChart $chart ) {
		return $chart->groups_tree( $request->all() );
	}
}
