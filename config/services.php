<?php

/**
 * @var $config DT\Autolink\CodeZone\WPSupport\Config\ConfigInterface
 */

use DT\Autolink\Providers\AdminServiceProvider;
use DT\Autolink\Providers\ConfigServiceProvider;
use DT\Autolink\Providers\MagicLinkServiceProvider;
use DT\Autolink\Providers\OptionsServiceProvider;
use DT\Autolink\Providers\RouteServiceProvider;
use DT\Autolink\Providers\ViewServiceProvider;
use DT\Autolink\Providers\AssetServiceProvider;

$config->merge( [
    'services' => [
        'providers' => [
            OptionsServiceProvider::class,
            AssetServiceProvider::class,
            ViewServiceProvider::class,
            RouteServiceProvider::class,
            MagicLinkServiceProvider::class,
            AdminServiceProvider::class
        ],
        'tgmpa' => [
            'plugins' => [
                [
                    'name'     => 'Disciple.Tools Dashboard',
                    'slug'     => 'disciple-tools-dashboard',
                    'source'   => 'https://github.com/DiscipleTools/disciple-tools-dashboard/releases/latest/download/disciple-tools-dashboard.zip',
                    'required' => false,
                ],
                [
                    'name'     => 'Disciple.Tools Genmapper',
                    'slug'     => 'disciple-tools-genmapper',
                    'source'   => 'https://github.com/DiscipleTools/disciple-tools-genmapper/releases/latest/download/disciple-tools-genmapper.zip',
                    'required' => true,
                ],
                [
                    'name'     => 'Disciple.Tools Autolink',
                    'slug'     => 'disciple-tools-autolink',
                    'source'   => 'https://github.com/DiscipleTools/disciple-tools-genmapper/releases/latest/download/disciple-tools-autolink.zip',
                    'required' => true,
                ]
            ],
            'config' => [
                'id'           => 'disciple_tools',
                'default_path' => '/partials/plugins/',
                'menu'         => 'tgmpa-install-plugins',
                'parent_slug'  => 'plugins.php',
                'capability'   => 'manage_options',
                'has_notices'  => true,
                'dismissible'  => true,
                'dismiss_msg'  => 'These are recommended plugins to complement your Disciple.Tools system.',
                'is_automatic' => true,
                'message'      => '',
            ],
        ]
    ]
]);
