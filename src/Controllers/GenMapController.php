<?php

namespace DT\Autolink\Controllers;

use DT\Autolink\GuzzleHttp\Psr7\Request;
use DT\Autolink\GuzzleHttp\Psr7\Response;
use DT\Autolink\Services\Charts\GenmapChart;
use function DT\Autolink\container;
use function DT\Autolink\template;
use function DT\Autolink\response;

class GenMapController {
	public function show( Request $request ) {
		$chart = container()->get( GenmapChart::class );
		if ( ! class_exists( 'DT_Genmapper_Groups_chart' ) ) {
			return response( __( 'Not Found', 'disciple-tools-autolink' ), 404 );
		}

		return template( 'genmap' );
	}

	public function index( Request $request, Response $response, GenmapChart $chart ) {
		return $chart->groups_tree( $request->all() );
	}
}
