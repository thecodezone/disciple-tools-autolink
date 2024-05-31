<?php

namespace Tests;

/**
 * @test
 */
class PluginTest extends TestCase {
	/**
	 * @test
	 */
	public function can_install() {
		activate_plugin( 'disciple-tools-autolink/disciple-tools-autolink.php' );

		$this->assertContains(
			'disciple-tools-autolink/disciple-tools-autolink.php',
			get_option( 'active_plugins' )
		);
	}
}
