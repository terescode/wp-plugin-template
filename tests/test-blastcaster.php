<?php

namespace Terescode\BlastCaster;

/**
 * Class WpAdminPluginTest
 *
 * @package Blastcaster
 */

class BlastCasterTest extends \BcPhpUnitTestCase {

	/**
	 * Test including the main plugin file should succeed
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */

	public function test_include_plugin_file_should_succeed_given_WPINC_is_set() {

		function create_plugin() {
			$mock_gen = new \PHPUnit_Framework_MockObject_Generator();
			$mock = $mock_gen->getMock( 'Terescode\WordPress\TcPluginHelper', array(), array(), '', false );
			return new \Terescode\WordPress\TcGenericPlugin( BC_PLUGIN_ID, $mock );
		}

		$ret = include_once( 'blastcaster.php' );
		$this->assertEquals( 1, $ret );
	}

	/**
	 * Test including the main plugin file should fail if WPINC is not set
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */

	public function test_include_plugin_file_should_fail_given_WPINC_not_set() {

		function is_wpinc_defined() {
			return false;
		}

		$ret = include_once( 'blastcaster.php' );
		$this->assertEquals( -1, $ret );
	}
}
