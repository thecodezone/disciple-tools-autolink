<?php

class Disciple_Tools_Autolink_Admin_Controller extends Disciple_Tools_Autolink_Controller {
	public function show( $params = [] ) {
		if ( ! current_user_can( 'manage_dt' ) ) { // manage dt is a permission that is specific to Disciple.Tools and allows admins, strategists and dispatchers into the wp-admin
			wp_die( 'You do not have sufficient permissions to access this page.' );
		}

		if ( isset( $_GET['tab'] ) ) {
			$tab = sanitize_key( wp_unslash( $_GET['tab'] ) );
		} else {
			$tab = 'general';
		}

		$link       = 'admin.php?page=disciple_tools_autolink&tab=';
		$content    = "";
		$page_title = "Autolink Settings";

		switch ( $tab ) {
			case 'general':
				$tab = new Disciple_Tools_Autolink_Tab_General();
				break;
			default:
				break;
		}

		include __DIR__ . '/../templates/admin/settings.php';
	}
}
