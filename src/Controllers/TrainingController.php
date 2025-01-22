<?php

namespace DT\Autolink\Controllers;

use DT\Autolink\GuzzleHttp\Psr7\Request;
use DT\Autolink\Repositories\TrainingVideoRepository;
use function DT\Autolink\container;
use function DT\Autolink\get_plugin_option;
use function DT\Autolink\template;

class TrainingController
{
    public function show( Request $request )
    {
        $options = container()->get( TrainingVideoRepository::class );
        $default_training_videos = $options->all();
        $videos = get_plugin_option( 'training_videos' );
        if ( !$videos ) {
            $videos = $default_training_videos;
        }
        $videos = json_decode( $videos );

        return template( 'training', compact( 'videos' ) );
    }
}
