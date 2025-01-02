<?php

/**
 * @var $config DT\Autolink\CodeZone\WPSupport\Config\ConfigInterface
 */

use function DT\Autolink\routes_path;

$config->merge( [
    'routes' => [
        'rewrites' => [
            '^dt/autolink/api/?$' => 'index.php?dt-autolink-api=/',
            '^dt/autolink/api/(.+)/?' => 'index.php?dt-autolink-api=$matches[1]',
            '^dt/autolink/?$' => 'index.php?dt-autolink=/',
            '^dt/autolink/(.+)/?' => 'index.php?dt-autolink=$matches[1]',
        ],
        'files' => [
            'api' => [
                "file" => "api.php",
                'query' => 'dt-autolink-api',
                'path' => 'dt/autolink/api',
            ],
            'web' => [
                "file" => "web.php",
                'query' => 'dt-autolink',
                'path' => 'dt/autolink',
            ]
        ],
        'middleware' => [
            // CustomMiddleware::class,
        ],
    ],
] );
