<?php

/**
 * @var $config DT\Autolink\CodeZone\WPSupport\Config\ConfigInterface
 */

use function DT\Autolink\routes_path;

$config->merge( [
    'routes' => [
        'rewrites' => [
            '^autolink/api/?$' => 'index.php?dt-autolink-api=/',
            '^autolink/api/(.+)/?' => 'index.php?dt-autolink-api=$matches[1]',
            '^autolink/?$' => 'index.php?dt-autolink=/',
            '^autolink/(.+)/?' => 'index.php?dt-autolink=$matches[1]',
        ],
        'files' => [
            'api' => [
                "file" => "api.php",
                'query' => 'dt-autolink-api',
                'path' => 'autolink/api',
            ],
            'web' => [
                "file" => "web.php",
                'query' => 'dt-autolink',
                'path' => 'autolink',
            ]
        ],
        'middleware' => [
            // CustomMiddleware::class,
        ],
    ],
] );
