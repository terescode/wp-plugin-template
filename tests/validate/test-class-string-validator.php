<?php

namespace Terescode\WordPress;

require_once 'includes/constants.php';
require_once 'includes/class-plugin-helper.php';
require_once 'includes/validate/class-string-validator.php';

/**
 * Class TcStringValidatorTest
 *
 * @package Blastcaster
 */

class TcStringValidatorTest extends \BcPhpUnitTestCase {
	function test_validate_should_return_code_given_param_returns_empty() {
		// @setup
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$data_map = [];

		// @exercise
		$validator = new TcStringValidator( $m_helper, 'foo', -1 );
		$ret = $validator->validate( $data_map );
		$this->assertEquals( -1, $ret );
	}

	function test_validate_should_return_null_given_null_code_and_param_returns_empty() {
		// @setup
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$data_map = [];

		// @exercise
		$validator = new TcStringValidator( $m_helper, 'foo' );
		$ret = $validator->validate( $data_map );
		$this->assertNull( $ret );
	}

	function test_validate_should_set_value_and_return_null_given_param_returns_value() {
		// @setup
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$data_map = [];

		$m_helper->expects( $this->once() )
			->method( 'param' )
			->with( 'foo', 'text' )
			->willReturn( 'bar' );

		// @exercise
		$validator = new TcStringValidator( $m_helper, 'foo', -1 );
		$ret = $validator->validate( $data_map );
		$this->assertNull( $ret );
		$this->assertTrue( isset( $data_map['foo'] ) );
		$this->assertEquals( 'bar', $data_map['foo'] );
	}

	function test_validate_should_call_param_with_url_type_given_type_url() {
		// @setup
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$data_map = [];

		$m_helper->expects( $this->once() )
			->method( 'param' )
			->with( 'foo', 'url' )
			->willReturn( 'http://www.terescode.com/I%20am%20a%20parameter' );

		// @exercise
		$validator = new TcStringValidator( $m_helper, 'foo', -1, 'url' );
		$ret = $validator->validate( $data_map );
		$this->assertNull( $ret );
		$this->assertTrue( isset( $data_map['foo'] ) );
		$this->assertEquals( 'http://www.terescode.com/I%20am%20a%20parameter', $data_map['foo'] );
	}
}
