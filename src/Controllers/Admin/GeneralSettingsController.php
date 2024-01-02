<?php

namespace DT\Plugin\Controllers\Admin;

use DT\Plugin\Illuminate\Http\RedirectResponse;
use DT\Plugin\Illuminate\Http\Request;
use DT\Plugin\Illuminate\Http\Response;
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

		// Add the settings update code here

		return new RedirectResponse( 302, admin_url( 'admin.php?page=dt_plugin&tab=general&updated=true' ) );
	}
}
