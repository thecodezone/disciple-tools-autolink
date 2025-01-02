<?php

/**
 * @var $config DT\Autolink\CodeZone\WPSupport\Config\ConfigInterface
 */
use DT\Autolink\MagicLinks\CoachingLink;
use DT\Autolink\MagicLinks\GroupLeaderLink;
use DT\Autolink\MagicLinks\UserRedirect;

$config->merge( [
    'magic' => [
        'links' => [
	        UserRedirect::class,
	        CoachingLink::class,
	        GroupLeaderLink::class
        ]
    ]
] );
