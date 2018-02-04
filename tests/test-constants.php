<?php

/**
 * Class ConstantsTest
 *
 * @package Blastcaster
 */

class ConstantsTest extends BcPhpUnitTestCase {

	/**
	 * Test including the constants file
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */

	public function test_constants_defined() {
		require_once( 'includes/constants.php' );
		$this->assertNotEmpty( 'BC_PLUGIN_ID' );
		$this->assertNotEmpty( 'BC_PLUGIN_DIR' );
		$this->assertNotEmpty( 'BC_PLUGIN_URL' );
	}
}
