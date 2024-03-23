<?php

namespace test;

class PluginTest extends TestCase {
	public function test_plugin_installed() {
		activate_plugin( 'disciple-tools-autolink/disciple-tools-autolink.php' );

		$this->assertContains(
			'disciple-tools-autolink/disciple-tools-autolink.php',
			get_option( 'active_plugins' )
		);
	}
}
