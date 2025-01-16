<?php

namespace DT\Autolink\Controllers;

use DT\Autolink\GuzzleHttp\Psr7\Request;
use DT\Autolink\Services\Options;
use function DT\Autolink\container;
use function DT\Autolink\template;

class TrainingController
{
	public function show( Request $request )
	{
		$options = container()->get( Options::class );
		$videos = $options->get( 'training_videos' );
		$videos = json_decode( $videos ?? '[]' );
		return template( 'training', compact( 'videos' ) );
	}
}
