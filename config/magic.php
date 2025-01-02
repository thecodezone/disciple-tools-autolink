<?php

/**
 * @var $config DT\Autolink\CodeZone\WPSupport\Config\ConfigInterface
 */

use DT\Autolink\MagicLinks\ExampleMagicLink;

$config->merge( [
    'magic' => [
        'links' => [
            ExampleMagicLink::class
        ]
    ]
] );
