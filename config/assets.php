<?php

/**
 * @var $config DT\Autolink\CodeZone\WPSupport\Config\ConfigInterface
 */
use function DT\Autolink\plugin_path;

$config->merge( [
    'assets' => [
	    'allowed_styles'  => [
		    'dt-autolink',
		    'dt-autolink-admin',
		    'disciple-tools-autolink',
		    'magic_link_css',
		    'hint',
		    'group-styles',
		    'styles',
		    'chart-styles',
		    'mapbox-gl-css',
		    'portal-app-domenu-css',
            'orgchart_css'
	    ],
	    'allowed_scripts' => [
		    'jquery',
		    'dt-autolink',
		    'dt-autolink-admin',
		    'disciple-tools-autolink',
		    'magic_link_scripts',
		    'gen-template',
		    'genApiTemplate',
		    'genmapper',
		    'd3',
		    'dt_groups_wpApiGenmapper',
		    'wp-i18n',
		    'jquery-ui-core',
		    'dt_groups_script',
		    'mapbox-search-widget',
		    'mapbox-gl',
		    'mapbox-cookie',
		    'jquery-cookie',
		    'jquery-touch-punch',
		    'portal-app-domenu-js',
		    'google-search-widget',
		    'shared-functions',
		    'typeahead-jquery',
            'orgchart_js'
	    ],
        'javascript_global_scope' => '$autolink',
        'manifest_dir' => plugin_path( '/dist' )
    ]
] );
