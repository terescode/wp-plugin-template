<?php

namespace Terescode\BlastCaster;

require_once 'includes/constants.php';
require_once 'includes/class-blastcaster-strings.php';
require_once 'includes/class-plugin-helper.php';

/**
 * Class WpAdminPluginTest
 *
 * @package Blastcaster
 */

class BcStringsTest extends \BcPhpUnitTestCase {

	function test_get_string_should_return_empty_given_invalid_code() {
		// @exercise
		$strings = new BcStrings();
		$str = $strings->get_string( 'bc.i.do.not.exist' );
		$this->assertEquals( '', $str );
	}

	function test_get_string_should_return_value_given_valid_code() {
		// @exercise
		$strings = new BcStrings();
		$str = $strings->get_string( BcStrings::ABF_NO_ACCESS );
		$this->assertEquals( 'You do not have access to add blasts.', $str );
	}

	function test_get_string_should_return_value_with_conversions_given_valid_code_and_arguments() {
		// @exercise
		$strings = new BcStrings();
		$str = $strings->get_string( BcStrings::ABF_INVALID_PAGE_DATA, [ 12345, 'bar' ] );
		$this->assertEquals( 'The page data received from the original source could not be decoded. (12345 - bar)', $str );
	}
}
