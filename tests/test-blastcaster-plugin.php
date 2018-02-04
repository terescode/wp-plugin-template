<?php

namespace Terescode\BlastCaster;

require_once 'includes/constants.php';
require_once 'includes/blastcaster-plugin.php';

/**
 * Class BlastCasterPluginTest
 *
 * @package Blastcaster
 */

class BlastCasterPluginTest extends \BcPhpUnitTestCase {

	/**
	 * Test install_admin_menus
	 */
	function test_create_plugin_should_return_initialized_plugin() {
		// @exercise
		$plugin = create_plugin();

		// @verify
		$this->assertNotNull( $plugin );
		$this->assertEquals( BC_PLUGIN_ID, $plugin->get_plugin_id() );
		$this->assertInstanceOf( 'Terescode\WordPress\TcGenericPlugin', $plugin );
	}
}
