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

	/**
	 * @test
	 */
	public function example_http_test() {
		$response = $this->get( 'dt/autolnk/api/hello' );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertStringContainsString( 'Hello World!', $response->getContent() );
	}
}
