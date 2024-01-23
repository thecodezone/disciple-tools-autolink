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
		activate_plugin( 'dt-plugin/dt-plugin.php' );

		$this->assertContains(
			'dt-plugin/dt-plugin.php',
			get_option( 'active_plugins' )
		);
	}

	/**
	 * @test
	 */
	public function can_access_dashboard() {
		$user = $this->factory()->user->create( [
			'role' => 'administrator',
		] );
		wp_set_current_user( $user );

		$response = $this->get( 'dt/plugin/api/hello' );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertStringContainsString( 'Hello World!', $response->getContent() );
	}
}
