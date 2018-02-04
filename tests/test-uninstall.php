<?php

/**
 * Class ConstantsTest
 *
 * @package Blastcaster
 */

class UninstallTest extends BcPhpUnitTestCase {

	/**
	 * Test including the uninstall file when WP_UNINSTALL_PLUGIN is not set.
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */

	public function test_uninstall_should_fail_given_WP_UNINSTALL_PLUGIN_not_set() {
		$ret = require_once( 'uninstall.php' );
		$this->assertEquals( -1, $ret );
	}

	/**
	 * Test including the uninstall file when WP_UNINSTALL_PLUGIN is set.
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */

	public function test_uninstall_should_succeed_given_WP_UNINSTALL_PLUGIN_is_set() {
		define( 'WP_UNINSTALL_PLUGIN', 1 );
		$ret = require_once( 'uninstall.php' );
		$this->assertEquals( 1, $ret );
	}
}
