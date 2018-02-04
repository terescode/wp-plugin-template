<?php

require_once 'includes/constants.php';
require_once 'includes/class-wp-helper.php';

/**
 * Class BcRendererTest
 *
 * @package Blastcaster
 */

class AddBlastPageTest extends BcPhpUnitTestCase {

	/**
	 * Test render
	 */
	function test_render() {
		// @setup
		$this->expect_html(
			function ( $result ) {
				$xpath = new \DOMXPath( $result );
				$elements = $xpath->query( '//div[@id="blastcaster-root"]' );
				$this->assertEquals( 1, $elements->length, 'Could not find blastcaster root' );
			}
		);
		// @test
		include( 'admin/views/add-blast-page.php' );
	}
}
