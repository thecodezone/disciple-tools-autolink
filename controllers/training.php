<?php

class Disciple_Tools_Autolink_Training_Controller extends Disciple_Tools_Autolink_Controller {

	public function show() {
		$data = $this->global_data();
		extract( $data );
		$videos = $this->settings->get_option( 'disciple_tools_autolink_training_videos' );
		$videos = json_decode( $videos );
		$action = "training";
		include __DIR__ . '/../templates/training.php';
	}
}
