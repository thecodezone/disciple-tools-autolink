<?php

namespace DT\Autolink\Services;

use function DT\Autolink\set_option;

/**
 * Class Options
 *
 * This class provides methods for retrieving options from the database.
 * Keys are scoped to the plugin to avoid conflicts with other plugins.
 * Default values may be provided for each option to avoid duplication.
 */
class Options {
	/**
	 * Returns an array of default option values.
	 *
	 * @return array An associative array of default option values.
	 */
	private function defaults(): array {
		return [
			'allow_parent_group_selection' => true,
			'show_in_menu'                 => true,
			'training_videos'              => json_encode( $this->localized_training_videos() ),
		];
	}

	/**
	 * Retrieves an array of localized training videos.
	 *
	 * @param string|null $locale The locale of the videos to retrieve. Default is null.
	 *
	 * @return array An array of video titles and embed codes based on the specified locale. If the specified locale is not found, the default locale is used.
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

	/**
	 * Retrieves the list of training videos.
	 *
	 * @return array The list of training videos in the following format:
	 *               - title: The title of the video.
	 *               - embed: The embedded HTML code of the video.
	 *               - hi_IN: The embedded HTML code of the video with Hindi subtitles.
	 */
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
	 * Determines the scope key for a given key.
	 *
	 * @param string $key The key for which to determine the scope key.
	 *
	 * @return string The scope key for the given key.
	 */
	public function scope_key( string $key ): string {
		return "disciple_tools_autolink_{$key}";
	}

	/**
	 * Retrieves the value of the specified option.
	 *
	 * @param string $key The key of the option to retrieve.
	 * @param mixed|null $default The default value to return if the option is not found. Default is null.
	 *
	 * @return mixed The value of the option if found, otherwise returns the default value.
	 */
	public function get( string $key, mixed $default = null, $required = false ) {
		if ( $default !== null ) {
			$default = Arr::get( $this->defaults(), $key );
		}

		$key = $this->scope_key( $key );


		$result = get_option( $key, $default );


		if ( $required && ! $result ) {
			set_plugin_option( $key, $default );

			return $default;
		}

		return $result;
	}

	/**
	 * Sets the value of the specified option.
	 *
	 * @param string $key The key of the option to set.
	 * @param $value // The value to set for the option.
	 *
	 * @return bool Returns true if the option was set successfully, otherwise returns false.
	 */
	public function set( string $key, $value ): bool {
		$key = $this->scope_key( $key );

		return set_option( $key, $value );
	}
}
