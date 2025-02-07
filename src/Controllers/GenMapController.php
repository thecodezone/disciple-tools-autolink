<?php

namespace DT\Autolink\Controllers;

use DT\Autolink\Psr\Http\Message\ResponseInterface;
use DT\Autolink\GuzzleHttp\Psr7\Request;
use DT\Autolink\Services\Analytics;
use DT\Autolink\Services\Charts\GenmapChart;
use Exception;
use function DT\Autolink\container;
use function DT\Autolink\extract_request_input;
use function DT\Autolink\get_plugin_option;
use function DT\Autolink\set_plugin_option;
use function DT\Autolink\template;
use function DT\Autolink\response;

/**
 * This controller handles requests related to the GenMap functionality.
 */
class GenMapController {
    /**
     * Handles the changing of chart types before showing the Genmap template.
     *
     * @param Request $request The HTTP request instance.
     *
     * @return ResponseInterface The response object containing the rendered template or a 404 error.
     */
    public function switch( Request $request ): ResponseInterface {
        $input = extract_request_input( $request );

        $analytics = container()->get( Analytics::class );
        $chartType = $input['chart'] ?? 'circles';
        $analytics->event( $chartType, [ 'action' => 'start', 'lib_name' => __CLASS__ ] );

        // Determine new chart type to be switched to.
        $show_tree_genmap = 0;
        switch ( $chartType ) {
            case 'tree':
                $show_tree_genmap = 1;
                $analytics->event( 'tree', [ 'action' => 'snapshot' ] );
                $analytics->event( 'tree', [ 'action' => 'stop' ] );
                break;
            case 'circles':
            default:
                $analytics->event( 'circles', [ 'action' => 'snapshot' ] );
                $analytics->event( 'circles', [ 'action' => 'stop' ] );
                break;
        }

        set_plugin_option( 'show_tree_genmap', $show_tree_genmap );

        return $this->show( $request );
    }

	/**
	 * Handles the showing of the Genmap chart template.
	 *
	 * @param Request $request The HTTP request instance.
	 *
	 * @return ResponseInterface The response object containing the rendered template or a 404 error.
	 */
	public function show( Request $request ) {
		container()->get( GenmapChart::class );
		if ( ! class_exists( 'DT_Genmapper_Groups_chart' ) ) {
			return response( __( 'Not Found', 'disciple-tools-autolink' ), 404 );
		}

        $show_tree_genmap = get_plugin_option( 'show_tree_genmap', 0 );
        $chart_type = ( !$show_tree_genmap || ( $show_tree_genmap == 0 ) ) ? 'circles' : 'tree';

        return template( 'genmap', compact( 'chart_type' ) );
	}

	/**
	 * Handles the process of retrieving and formatting the groups tree data using the Genmap chart.
	 *
	 * @param Request $request The HTTP request instance containing input parameters.
	 *
	 * @return ResponseInterface The HTTP response containing the formatted groups tree data or an error message with appropriate status code.
	 */
	public function index( Request $request ) {
		$chart = container()->get( GenmapChart::class );
		$input = extract_request_input( $request );
		try {
			$result = $chart->groups_tree( $input );
		} catch ( Exception $e ) {
			return response( $e->getMessage(), 500 );
		}
		return response( $result, 200 );
	}
}
