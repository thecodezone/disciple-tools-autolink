<?php

class Disciple_Tools_Autolink_Genmap_Controller extends Disciple_Tools_Autolink_Controller {
	public function show( $params = [] ) {
		if ( ! class_exists( 'DT_Genmapper_Groups_chart' ) ) {
			wp_redirect( $this->functions->get_app_link() );
		}

		$data = $this->global_data();
		extract( $data );
		$action    = 'genmap';
		$app_class = 'app--genmap';

		include __DIR__ . '/../templates/genmap.php';
	}
}
