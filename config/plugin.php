<?php

/**
 * @var $config DT\Autolink\CodeZone\WPSupport\Config\ConfigInterface
 */
$config->merge( [
    'plugin' => [
        'text_domain' => 'dt-autolink',
        'nonce' => 'dt-autolink',
        'dt_admin_form_nonce' => 'dt_admin_form_nonce',
        'dt_version' => 1.19,
		'cookies' => [
			'leads_group' => 'dt_autolink_leads_group',
			'coached_by' => 'dt_autolink_coached_by',
		],
        'paths' => [
            'src' => 'src',
            'resources' => 'resources',
            'routes' => 'routes',
            'views' => 'resources/views',
        ]
    ]
]);
