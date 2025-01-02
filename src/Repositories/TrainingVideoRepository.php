<?php

namespace DT\Autolink\Repositories;

use DT\Autolink\CodeZone\WPSupport\Config\Config;

class TrainingVideoRepository {
	private Config $config;

	public function __construct( Config $config ) {
		$this->config = $config;
	}

	public function all() {
		return $this->config->get( 'training.videos' );
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
