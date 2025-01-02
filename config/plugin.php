<?php

/**
 * @var $config DT\Autolink\CodeZone\WPSupport\Config\ConfigInterface
 */
$config->merge( [
    'plugin' => [
        'text_domain' => 'dt-autolink',
        'nonce_name' => 'dt-autolink',
        'dt_version' => 1.19,
        'paths' => [
            'src' => 'src',
            'resources' => 'resources',
            'routes' => 'routes',
            'views' => 'resources/views',
        ]
    ]
]);
