<?php

namespace DT\Autolink\Controllers\Admin;

use DT\Autolink\Illuminate\Http\RedirectResponse;
use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Illuminate\Http\Response;
use DT\Autolink\Services\Options;
use Exception;
use function DT\Autolink\plugin_url;
use function DT\Autolink\redirect;
use function DT\Autolink\set_option;
use function DT\Autolink\set_plugin_option;
use function DT\Autolink\transaction;
use function DT\Autolink\validate;
use function DT\Autolink\view;
use function DT\Autolink\get_plugin_option;


class GeneralSettingsController {
	/**
	 * Submit the general settings admin tab form
	 */
	public function update( Request $request, Response $response ) {
		$error = false;
		$training_videos = $request->post( "training_videos" );
		if ( $training_videos ) {
			$training_videos = stripslashes( preg_replace( '/[\x00-\x1F\x80-\xFF]/', '', $training_videos ) );
		}

		$allow_parent_group_selection = $request->post( "allow_parent_group_selection" ) == "1" ? "1" : "0";
		$show_in_menu = $request->post( "show_in_menu" ) == "1" ? "1" : "0";


		if ( $training_videos ) {
			$is_valid = $this->validate_videos( $training_videos );
			if ( ! $is_valid ) {
				$error = __( 'Invalid training videos.', 'disciple-tools-autolink' );
			}
			set_plugin_option( 'training_videos', $training_videos);
		}

		set_plugin_option( 'allow_parent_group_selection', $allow_parent_group_selection );
		set_plugin_option( 'show_in_menu', $show_in_menu );

		if ( $error ) {
			return redirect( 'admin.php?page=disciple_tools_autolink&tab=general&error=' . $error );
		}

		return redirect( 'admin.php?page=disciple_tools_autolink&tab=general');
	}

	/**
	 * Show the admin settings view
	 *
	 * @param Request $request
	 * @param Response $response
	 * @param Options $options
	 *
	 * @return View
	 */
	public function show( Request $request, Response $response, Options $options) {
		$old                     = [
			'allow_parent_group_selection' => get_plugin_option( 'allow_parent_group_selection' ),
			'training_videos'              => get_plugin_option( 'training_videos' ),
			'show_in_menu'                 => get_plugin_option( 'show_in_menu' ),
		];

		$error = false;

		$tab = "general";

		$default_training_videos = $options->localized_training_videos();

		if ( ! $this->validate_videos( $old['training_videos'] ) ) {
			$old['training_videos'] = $default_training_videos;
			set_plugin_option( 'training_videos', wp_json_encode( $old['training_videos'] ) );
			$error = esc_attr( __( 'Training videos could not be loaded. Resetting to default content.', 'disciple-tools-autolink' ) );
		}

		if ( is_string( $old['training_videos'] ) ) {
			$old['training_videos'] = json_decode( $old['training_videos'], true );
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

		$training_videos_url = plugin_url( 'training' );

		return view( 'settings/general', compact( 'old', 'error', 'training_videos_translations', 'training_videos_url', 'tab', 'default_training_videos' ) );
	}

	/**
	 * Validates a string containing a list of videos.
	 *
	 * @param string $videos The string containing the list of videos in JSON format.
	 *
	 * @return bool Returns true if the videos are valid, otherwise false.
	 */
	private function validate_videos( $videos ): bool {
		if ( is_string( $videos ) ) {
			if ( ! str_contains( $videos, 'title' ) ) {
				return false;
			}

			try {
				$videos = json_decode( $videos );
			} catch ( Exception $e ) {
				return false;
			}
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
}