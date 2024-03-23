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
      'disciple_tools_autolink_show_in_menu'                 => true,
			'disciple_tools_autolink_training_videos'              => json_encode( $this->localized_training_videos() )
		];
	}

	/**
	 * @param $locale
	 *
	 * @return array|array[]
	 */
	public function localized_training_videos( $locale = null ): array {
		$locale = $locale ?? get_user_locale();

		return array_map( function ( $video ) use ( $locale ) {
			return [
				'title' => $video['title'],
				'embed' => $video[ $locale ] ?? $video['embed'],
			];
		}, $this->training_videos() );
	}

	public function training_videos() {
		return [
			[
				'title' => __( 'Video 1: Intro to Digital Gen Maps', 'disciple-tools-autolink' ),
				'embed' => '<iframe src="https://player.vimeo.com/video/854487980?h=ac4b27f8f7" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/854487980">Video 1: Intro to Digital Gen Maps</a> from <a href="https://vimeo.com/user188171893">Darien Clark</a> on <a href="https://vimeo.com">Vimeo</a>.</p>',
				'hi_IN' => '<iframe src="https://player.vimeo.com/video/855088128?h=349a4ddd75" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/855088128">वीडियो 1: डिजिटल जनरल मैप्स का परिचय</a> from <a href="https://vimeo.com/user188171893">Darien Clark</a> on <a href="https://vimeo.com">Vimeo</a>.</p>'
			],
			[
				'title' => __( 'Video 2: Creating an Account', 'disciple-tools-autolink' ),
				'embed' => '<iframe src="https://player.vimeo.com/video/854487348?h=133cf84f7f" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/854487348">Video 2: Creating an Account</a> from <a href="https://vimeo.com/user188171893">Darien Clark</a> on <a href="https://vimeo.com">Vimeo</a>.</p>',
				'hi_IN' => '<iframe src="https://player.vimeo.com/video/855089401?h=f741bc4e96" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/855089401">वीडियो 2: एक खाता बनाना</a> from <a href="https://vimeo.com/user188171893">Darien Clark</a> on <a href="https://vimeo.com">Vimeo</a>.</p>'
			],
			[
				'title' => __( 'Video 3: Creating Your Group', 'disciple-tools-autolink' ),
				'embed' => '<iframe src="https://player.vimeo.com/video/854480462?h=fcc489580c" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/854480462">Video 3: Creating Your Group</a> from <a href="https://vimeo.com/user188171893">Darien Clark</a> on <a href="https://vimeo.com">Vimeo</a>.</p>',
				'hi_IN' => '<iframe src="https://player.vimeo.com/video/855091689?h=7a75f350cd" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/855091689">वीडियो 3: अपना समूह बनाना</a> from <a href="https://vimeo.com/user188171893">Darien Clark</a> on <a href="https://vimeo.com">Vimeo</a>.</p>'
			],
			[
				'title' => __( 'Video 4: Entering Group Information', 'disciple-tools-autolink' ),
				'embed' => '<iframe src="https://player.vimeo.com/video/854482195?h=8aba1757e1" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/854482195">Video 4: Entering Group Information</a> from <a href="https://vimeo.com/user188171893">Darien Clark</a> on <a href="https://vimeo.com">Vimeo</a>.</p>',
				'hi_IN' => '<iframe src="https://player.vimeo.com/video/855094970?h=675402fff5" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/855094970">वीडियो 4: समूह जानकारी दर्ज करना</a> from <a href="https://vimeo.com/user188171893">Darien Clark</a> on <a href="https://vimeo.com">Vimeo</a>.</p>'
			],
			[
				'title' => __( 'Video 5: Sharing Your Group Link', 'disciple-tools-autolink' ),
				'embed' => '<iframe src="https://player.vimeo.com/video/854811711?h=dceeaf9ff2" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/854811711">Video 5: Sharing Your Group Link</a> from <a href="https://vimeo.com/user188171893">Darien Clark</a> on <a href="https://vimeo.com">Vimeo</a>.</p>',
				'hi_IN' => '<iframe src="https://player.vimeo.com/video/855096198?h=28f9c98dd1" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/855096198">वीडियो 5: अपना समूह लिंक साझा करना</a> from <a href="https://vimeo.com/user188171893">Darien Clark</a> on <a href="https://vimeo.com">Vimeo</a>.</p>'
			],
			[
				'title' => __( 'Video 6: Creating and Sharing Your Group Gen Map', 'disciple-tools-autolink' ),
				'embed' => '<iframe src="https://player.vimeo.com/video/854806627?h=5f22794e4e" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/854806627">Video 6: Creating and Sharing Your Group Gen Map</a> from <a href="https://vimeo.com/user188171893">Darien Clark</a> on <a href="https://vimeo.com">Vimeo</a>.</p>',
				'hi_IN' => '<iframe src="https://player.vimeo.com/video/871549933?h=5af4199b78" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/871549933">वीडियो 6: अपना ग्रुप जेन मैप बनाना और साझा करना</a> from <a href="https://vimeo.com/user188171893">Darien Clark</a> on <a href="https://vimeo.com">Vimeo</a>.</p>'
			]
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
