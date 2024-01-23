<?php

namespace DT\Plugin\Controllers\Admin;

use DT\Plugin\Illuminate\Http\RedirectResponse;
use DT\Plugin\Illuminate\Http\Request;
use DT\Plugin\Illuminate\Http\Response;
use function DT\Plugin\transaction;
use function DT\Plugin\validate;
use function DT\Plugin\view;


class GeneralSettingsController {
	/**
	 * Show the general settings admin tab
	 */
	public function show( Request $request, Response $response ) {
		$tab        = "general";
		$link       = 'admin.php?page=dt_plugin&tab=';
		$page_title = "DT Plugin Settings";

		return view( "settings/general", compact( 'tab', 'link', 'page_title' ) );
	}

	/**
	 * Submit the general settings admin tab form
	 */
	public function update( Request $request, Response $response ) {
		$error = false;

		// Add the settings update code here
		$errors = validate( $request->all(), [
			'option1' => 'required',
			'option2' => 'required',
		] );

		if ( count( $errors ) > 0 ) {
			$error = __( 'Please complete the required fields.', 'dt-plugin' );
		}

		if ( ! $error ) {
			//Perform update in a MYSQL transaction
			$result = transaction( function () use ( $request ) {
				set_option( 'option1', $request->post( 'option1' ) );
				set_option( 'option2', $request->post( 'option2' ) );
			} );

			if ( $result !== true ) {
				$error = __( 'The form could not be submitted.', 'dt-plugin' );
			}
		}


		if ( $error ) {
			return new RedirectResponse( 302, admin_url(
					'admin.php?page=dt_plugin&tab=general&' . http_build_query( [
						'error'  => $error,
						'fields' => $errors,
					] )
				)
			);
		}

		return new RedirectResponse( 302, admin_url( 'admin.php?page=dt_plugin&tab=general&updated=true' ) );
	}
}
