<?php


class Disciple_Tools_Autolink_Admin_General_Controller extends Disciple_Tools_Autolink_Controller {

	/**
	 * Show the admin tab
	 *
	 * @param $params
	 *
	 * @return void
	 */
	public function show( $params = [] ): void {
		$default_training_videos = $this->settings->defaults()['disciple_tools_autolink_training_videos'];
		$old                     = [
			'disciple_tools_autolink_allow_parent_group_selection' => $this->settings->get_option( 'disciple_tools_autolink_allow_parent_group_selection' ),
			'disciple_tools_autolink_training_videos'              => $this->settings->get_option( 'disciple_tools_autolink_training_videos' ),
			'disciple_tools_autolink_show_in_menu'                 => $this->settings->get_option( 'disciple_tools_autolink_show_in_menu' ),
		];
		$error                   = $params['error'] ?? null;

		if ( ! $this->validate_videos( $old['disciple_tools_autolink_training_videos'] ) ) {
			$training_videos = $default_training_videos;
			update_option( 'disciple_tools_autolink_training_videos', $training_videos );
			$error = esc_attr( __( 'Training videos could not be loaded. Resetting to default content.', 'disciple-tools-autolink' ) );
		}

		$training_videos_translations = [
			'label'         => __( 'Training Videos', 'disciple-tools-autolink' ),
			'title'         => __( 'Title', 'disciple-tools-autolink' ),
			'embed'         => __( 'Embed', 'disciple-tools-autolink' ),
			'reset'         => __( 'Reset', 'disciple-tools-autolink' ),
			'add'           => __( 'Add', 'disciple-tools-autolink' ),
			'remove'        => __( 'Remove', 'disciple-tools-autolink' ),
			'resetConfirm'  => __( 'Are you sure you want to reset the training videos to default content?', 'disciple-tools-autolink' ),
			'removeConfirm' => __( 'Are you sure you want to remove this video?', 'disciple-tools-autolink' ),
			'up'            => __( 'Up', 'disciple-tools-autolink' ),
			'down'          => __( 'Down', 'disciple-tools-autolink' ),
		];

		$training_videos_url = $this->functions->get_training_url();

		include __DIR__ . '/../templates/admin/general.php';
	}

	private function validate_videos( $videos ): bool {
		if ( ! is_string( $videos ) ) {
			return false;
		}

		if ( ! str_contains( $videos, 'title' ) ) {
			return false;
		}

		try {
			$videos = json_decode( $videos );
		} catch ( Exception $e ) {
			return false;
		}

		if ( ! is_array( $videos ) ) {
			return false;
		}

		if ( count( $videos ) === 0 ) {
			return false;
		}

		foreach ( $videos as $video ) {
			if ( ! isset( $video->title ) || ! isset( $video->embed ) ) {
				return false;
			}

			if ( ! is_string( $video->title ) || ! is_string( $video->embed ) ) {
				return false;
			}

			if ( ! str_contains( $video->embed, 'iframe' ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Save the admin  settings
	 *
	 * @param $params
	 *
	 * @throws Exception
	 *
	 */
	public function save( $params = [] ): void {
		if ( ! isset( $_POST['dt_admin_form_nonce'] ) &&
		     ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['dt_admin_form_nonce'] ) ), 'dt_admin_form' ) ) {
			return;
		}

		$post_vars = dt_recursive_sanitize_array( $_POST );
		//Don't sanitize the training videos because we are saving HTML
		// phpcs:ignore
		$post_vars['disciple_tools_autolink_training_videos'] = stripslashes( preg_replace( '/[\x00-\x1F\x80-\xFF]/', '', $_POST['disciple_tools_autolink_training_videos'] ) ) ?? false;

		if ( isset( $post_vars['disciple_tools_autolink_training_videos'] ) ) {
			$is_valid = $this->validate_videos( $post_vars['disciple_tools_autolink_training_videos'] );
			if ( ! $is_valid ) {
				throw new Exception( esc_attr( __( 'Invalid training videos.', 'disciple-tools-autolink' ) ) );
			}
			update_option( 'disciple_tools_autolink_training_videos', $post_vars['disciple_tools_autolink_training_videos'] );
		}

		update_option( 'disciple_tools_autolink_allow_parent_group_selection', ( isset( $post_vars['disciple_tools_autolink_allow_parent_group_selection'] ) && $post_vars['disciple_tools_autolink_allow_parent_group_selection'] === '1' ) ? "1" : "0" );
		update_option( 'disciple_tools_autolink_show_in_menu', ( isset( $post_vars['disciple_tools_autolink_show_in_menu'] ) && $post_vars['disciple_tools_autolink_show_in_menu'] === '1' ) ? "1" : "0" );
	}
}
