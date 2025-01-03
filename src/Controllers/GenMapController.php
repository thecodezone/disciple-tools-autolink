<?php

namespace DT\Autolink\Controllers;

use DT\Autolink\GuzzleHttp\Psr7\Request;
use DT\Autolink\Services\Charts\GenmapChart;
use function DT\Autolink\container;
use function DT\Autolink\extract_request_input;
use function DT\Autolink\template;
use function DT\Autolink\response;

/**
 * This controller handles requests related to the GenMap functionality.
 */
class GenMapController {
	/**
	 * Handles the showing of the Genmap chart template.
	 *
	 * @param Request $request The HTTP request instance.
	 *
	 * @return \DT\Autolink\Psr\Http\Message\ResponseInterface The response object containing the rendered template or a 404 error.
	 */
	public function show( Request $request ) {
		container()->get( GenmapChart::class );
		if ( ! class_exists( 'DT_Genmapper_Groups_chart' ) ) {
			return response( __( 'Not Found', 'disciple-tools-autolink' ), 404 );
		}

		return template( 'genmap' );
	}

	/**
	 * Handles the process of retrieving and formatting the groups tree data using the Genmap chart.
	 *
	 * @param Request $request The HTTP request instance containing input parameters.
	 *
	 * @return \DT\Autolink\Psr\Http\Message\ResponseInterface The HTTP response containing the formatted groups tree data or an error message with appropriate status code.
	 */
	public function index( Request $request ) {
		$chart = container()->get( GenmapChart::class );
		$input = extract_request_input( $request );
		try {
			$result = $chart->groups_tree( $input );
		} catch ( \Exception $e ) {
			return response( $e->getMessage(), 500 );
		}
		return response( $result, 200 );
	}
}
