<?php

class PluginTest extends TestCase {
    public function test_plugin_installed() {
        activate_plugin( 'dt-plugin/dt-plugin.php' );

        $this->assertContains(
            'dt-plugin/dt-plugin.php',
            get_option( 'active_plugins' )
        );
    }
}
