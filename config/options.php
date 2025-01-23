<?php

/**
 * @var $config DT\Autolink\CodeZone\WPSupport\Config\ConfigInterface
 */

    $config->merge( [
        'options' => [
            'prefix' => 'dt_autolink',
            'defaults' => [
	            'allow_parent_group_selection' => true,
	            'show_in_menu'                 => true,
	            'training_videos'              => [
		            [
			            'title' => __( 'Video 1: Intro to Digital Gen Maps', 'disciple-tools-autolink' ),
			            'embed' => '<iframe src="https://player.vimeo.com/video/854487980?h=ac4b27f8f7" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/854487980">Video 1: Intro to Digital Gen Maps</a> from <a href="https://vimeo.com/user188171893">Darien Clark</a> on <a href="https://vimeo.com">Vimeo</a>.</p>',
			            'hi_IN' => '<iframe src="https://player.vimeo.com/video/855088128?h=349a4ddd75" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/855088128">वीडियो 1: डिजिटल जनरल मैप्स का परिचय</a> from <a href="https://vimeo.com/user188171893">Darien Clark</a> on <a href="https://vimeo.com">Vimeo</a>.</p>'
		            ],
		            [
			            'title' => __( 'Video 2: Creating an Account', 'disciple-tools-autolink' ),
			            'embed' => '<iframe src="https://player.vimeo.com/video/854487348?h=133cf84f7f" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/854487348">Video 2: Creating an Account</a> from <a href="https://vimeo.com/user188171893">Darien Clark</a> on <a href="https://vimeo.com">Vimeo</a>.</p>',
			            'hi_IN' => '<iframe src="https://player.vimeo.com/video/855089401?h=f741bc4e96" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/855089401">वीडियो 2: एक खाता बनाना</a> from <a href="https://vimeo.com/user188171893">Darien Clark</a> on <a href="https://vimeo.com">Vimeo</a>.</p>'
		            ],
		            [
			            'title' => __( 'Video 3: Creating Your Group', 'disciple-tools-autolink' ),
			            'embed' => '<iframe src="https://player.vimeo.com/video/854480462?h=fcc489580c" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/854480462">Video 3: Creating Your Group</a> from <a href="https://vimeo.com/user188171893">Darien Clark</a> on <a href="https://vimeo.com">Vimeo</a>.</p>',
			            'hi_IN' => '<iframe src="https://player.vimeo.com/video/855091689?h=7a75f350cd" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/855091689">वीडियो 3: अपना समूह बनाना</a> from <a href="https://vimeo.com/user188171893">Darien Clark</a> on <a href="https://vimeo.com">Vimeo</a>.</p>'
		            ],
		            [
			            'title' => __( 'Video 4: Entering Group Information', 'disciple-tools-autolink' ),
			            'embed' => '<iframe src="https://player.vimeo.com/video/854482195?h=8aba1757e1" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/854482195">Video 4: Entering Group Information</a> from <a href="https://vimeo.com/user188171893">Darien Clark</a> on <a href="https://vimeo.com">Vimeo</a>.</p>',
			            'hi_IN' => '<iframe src="https://player.vimeo.com/video/855094970?h=675402fff5" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/855094970">वीडियो 4: समूह जानकारी दर्ज करना</a> from <a href="https://vimeo.com/user188171893">Darien Clark</a> on <a href="https://vimeo.com">Vimeo</a>.</p>'
		            ],
		            [
			            'title' => __( 'Video 5: Sharing Your Group Link', 'disciple-tools-autolink' ),
			            'embed' => '<iframe src="https://player.vimeo.com/video/854811711?h=dceeaf9ff2" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/854811711">Video 5: Sharing Your Group Link</a> from <a href="https://vimeo.com/user188171893">Darien Clark</a> on <a href="https://vimeo.com">Vimeo</a>.</p>',
			            'hi_IN' => '<iframe src="https://player.vimeo.com/video/855096198?h=28f9c98dd1" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/855096198">वीडियो 5: अपना समूह लिंक साझा करना</a> from <a href="https://vimeo.com/user188171893">Darien Clark</a> on <a href="https://vimeo.com">Vimeo</a>.</p>'
		            ],
		            [
			            'title' => __( 'Video 6: Creating and Sharing Your Group Gen Map', 'disciple-tools-autolink' ),
			            'embed' => '<iframe src="https://player.vimeo.com/video/854806627?h=5f22794e4e" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/854806627">Video 6: Creating and Sharing Your Group Gen Map</a> from <a href="https://vimeo.com/user188171893">Darien Clark</a> on <a href="https://vimeo.com">Vimeo</a>.</p>',
			            'hi_IN' => '<iframe src="https://player.vimeo.com/video/871549933?h=5af4199b78" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/871549933">वीडियो 6: अपना ग्रुप जेन मैप बनाना और साझा करना</a> from <a href="https://vimeo.com/user188171893">Darien Clark</a> on <a href="https://vimeo.com">Vimeo</a>.</p>'
		            ]
	            ],
                'dt_autolink_analytics_permission' => false
            ],
        ]
    ] );
