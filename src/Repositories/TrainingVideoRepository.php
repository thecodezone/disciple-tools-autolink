<?php

namespace DT\Autolink\Repositories;

use DT\Autolink\Services\Options;

class TrainingVideoRepository {
	private Options $options;

	public function __construct( Options $options ) {
		$this->options = $options;
	}

	public function all() {
		return $this->options->get( 'training_videos' );
	}

	public function localized( $locale = null ): array {
		$locale = $locale ?? get_user_locale();

		return array_map( function ( $video ) use ( $locale ) {
			return [
				'title' => $video['title'],
				'embed' => $video[ $locale ] ?? $video['embed'],
			];
		}, $this->all() );
	}
}
