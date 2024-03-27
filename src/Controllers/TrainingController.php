<?php

namespace DT\Autolink\Controllers;

use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Illuminate\Http\Response;
use DT\Autolink\Services\Options;
use function DT\Autolink\template;

class TrainingController
{
	public function show( Request $request, Response $response, Options $options )
	{
		$videos = $options->get( 'training_videos' );
		$videos = json_decode( $videos );
		return template( 'training', compact( 'videos' ) );
	}
}