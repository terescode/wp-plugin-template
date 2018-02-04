<?php

namespace Terescode\BlastCaster;

use phpmock\phpunit\PHPMock;

require_once 'includes/constants.php';
require_once 'includes/class-wp-include-formatter.php';

/**
 * Class BcWpIncludeFormatterTest
 *
 * @package Blastcaster
 */

class BcWpIncludeFormatterTest extends \BcPhpUnitTestCase {

	use PHPMock;

	function test_format_should_return_false_given_invalid_template_path() {
		// @setup
		$blast = new BcBlast( 'TDD is fun', 'Article excerpt about TDD' );
		$formatter = new BcWpIncludeFormatter( 'tests/idonotexist.php' );

		// @exercise
		$ret = $formatter->format( $blast );

		// @verify
		$this->assertFalse( $ret );
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	function test_format_should_return_false_given_ob_start_returns_false() {
		// @setup
		/*$blast = new BcBlast( 'TDD is fun', 'Article excerpt about TDD' );
		$formatter = new BcWpIncludeFormatter( 'tests/fixtures/sample-template.php' );

		$ob_start = $this->getFunctionMock( __NAMESPACE__, 'ob_start' );
		$ob_start->expects( $this->once() )
			->willReturn( false );

		// @exercise
		$ret = $formatter->format( $blast );

		// @verify
		$this->assertFalse( $ret );*/
	}

	function test_format_should_return_false_given_include_does_not_return_1() {
		// @setup
		$blast = new BcBlast( 'TDD is fun', 'Article excerpt about TDD' );
		$formatter = new BcWpIncludeFormatter( 'tests/fixtures/sample-template-fail.php' );

		// @exercise
		$ret = $formatter->format( $blast );

		// @verify
		$this->assertFalse( $ret );
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	function test_format_should_return_false_given_ob_get_clean_returns_false() {
		// @setup
		$blast = new BcBlast( 'TDD is fun', 'Article excerpt about TDD' );
		$formatter = new BcWpIncludeFormatter( 'tests/fixtures/sample-template.php' );

		$ob_start = $this->getFunctionMock( __NAMESPACE__, 'ob_get_clean' );
		$ob_start->expects( $this->once() )
			->willReturn( false );

		// @exercise
		$ret = $formatter->format( $blast );

		// @verify
		$this->assertFalse( $ret );
	}

	function test_format_should_return_string_given_ob_get_clean_does() {
		// @setup
		$blast = new BcBlast( 'TDD is fun', 'Article excerpt about TDD' );
		$formatter = new BcWpIncludeFormatter( 'tests/fixtures/sample-template.php' );

		// @exercise
		$ret = $formatter->format( $blast );

		// @verify
		$this->assertStringStartsWith( '<h1>TDD is fun</h1>', $ret );
	}
}
