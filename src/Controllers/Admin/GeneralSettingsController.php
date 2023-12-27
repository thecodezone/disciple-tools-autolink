<?php

namespace DT\Plugin\Controllers\Admin;

use WP_HTTP_Response;
use function DT\Plugin\view;

class GeneralSettingsController {
	/**
	 * Show the general settings admin tab
	 */
	public function show() {
		if ( ! current_user_can( 'manage_dt' ) ) { // manage dt is a permission that is specific to Disciple.Tools and allows admins, strategists and dispatchers into the wp-admin
			wp_die( 'You do not have sufficient permissions to access this page.' );
		}

		$tab        = "general";
		$link       = 'admin.php?page=disciple_tools_autolink&tab=';
		$page_title = "Autolink Settings";

		return view( "settings/general", compact( 'tab', 'link', 'page_title' ) );
	}

	/**
	 * Submit the general settings admin tab form
	 * @return WP_HTTP_Response
	 */
	public function update() {
		if ( ! isset( $_POST['dt_admin_form_nonce'] ) &&
		     ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['dt_admin_form_nonce'] ) ), 'dt_admin_form' ) ) {
			wp_die( 'You do not have sufficient permissions.', 401 );
		}

		if ( ! current_user_can( 'manage_dt' ) ) {
			wp_die( 'You do not have sufficient permissions.', 401 );
		}

		return new WP_HTTP_Response( admin_url( 'admin.php?page=disciple_tools_autolink&tab=general&updated=true' ), 302 );
	}
}
