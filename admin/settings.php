<?php

class Disciple_Tools_Autolink_Settings {
	public function __construct() {
		$this->setup_options();
	}

	/**
	 * Make sure all the options existing in the database
	 * @return void
	 */
	protected function setup_options(): void {
		foreach ( $this->defaults() as $key => $value ) {
			if ( get_option( $key, 'MIA' ) === 'MIA' ) {
				add_option( $key, $value );
			}
		}
	}

	/**
	 * Return a map of the fields and their default values
	 * @return array
	 */
	public function defaults(): array {
		return [
			'disciple_tools_autolink_allow_parent_group_selection' => true,
			'disciple_tools_autolink_training_videos'              => json_encode( [
				[
					'title' => __( 'How to use the Autolink Plugin', 'disciple-tools-autolink' ),
					'embed' => '<div style="position: relative; padding-bottom: 62.5%; height: 0;"><iframe src="https://www.loom.com/embed/7fcab7eb8d9f40288c62ef8a422dfdc4?sid=5bed3a53-649a-4787-a8e3-25811cd7d78b" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe></div>'
				]
			] )
		];
	}

	/**
	 * Get an option and fall back to the default if it doesn't exist
	 *
	 * @param $name
	 *
	 * @return false|mixed
	 */
	public function get_option( $name ) {
		return get_option( $name, $this->defaults()[ $name ] );
	}
}
