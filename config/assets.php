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
        'javascript_globals' => [],
        'manifest_dir' => plugin_path( '/dist' )
    ]
] );



add_action('wp_loaded', function () use ( $config ) {
    $config->set( 'assets.javascript_globals', [
        'nonce' => wp_create_nonce( config( 'plugin.nonce_name' ) ),
        'urls' => [
            'root' => esc_url_raw( trailingslashit( route_url() ) ),
        ]
    ]);
});
