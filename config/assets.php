<?php

/**
 * @var $config DT\Autolink\CodeZone\WPSupport\Config\ConfigInterface
 */

use function DT\Autolink\config;
use function DT\Autolink\plugin_path;
use function DT\Autolink\route_url;

$config->merge( [
    'assets' => [
        'allowed_styles' => [
            'dt-autolink',
            'dt-autolink-admin',
        ],
        'allowed_scripts' =>[
            'dt-autolink',
            'dt-autolink-admin',
        ],
        'javascript_global_scope' => '$dt_autolink',
        'manifest_dir' => plugin_path( '/dist' )
    ]
] );
