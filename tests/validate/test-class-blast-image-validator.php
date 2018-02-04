<?php

namespace Terescode\BlastCaster;

require_once 'includes/constants.php';
require_once 'includes/class-plugin-helper.php';
require_once 'includes/class-blastcaster-strings.php';
require_once 'includes/validate/class-blast-image-validator.php';

$g_is_uploaded_file = false;
function is_uploaded_file( $file ) {
	global $g_is_uploaded_file;
	return $g_is_uploaded_file;
}

/**
 * Class TcBlastImageValidatorTest
 *
 * @package Blastcaster
 */

class TcBlastImageValidatorTest extends \BcPhpUnitTestCase {
	function test_validate_should_return_code_given_missing_image_type() {
		// @setup
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$data_map = [];

		// @exercise
		$validator = new BcBlastImageValidator( $m_helper );
		$ret = $validator->validate( $data_map );
		$this->assertEquals( BcStrings::ABF_INVALID_BLAST_IMAGE_TYPE, $ret );
	}

	function test_validate_should_return_code_given_invalid_image_type() {
		// @setup
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$data_map = [];

		$m_helper->method( 'param' )
			->willReturn( 'not.a.type' );

		// @exercise
		$validator = new BcBlastImageValidator( $m_helper );
		$ret = $validator->validate( $data_map );
		$this->assertEquals( BcStrings::ABF_INVALID_BLAST_IMAGE_TYPE, $ret );
	}

	function test_validate_should_return_null_given_none_image_type() {
		// @setup
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$data_map = [];

		$m_helper->method( 'param' )
			->willReturn( 'none' );

		// @exercise
		$validator = new BcBlastImageValidator( $m_helper );
		$ret = $validator->validate( $data_map );
		$this->assertEquals( null, $ret );
	}

	function test_validate_should_return_code_given_missing_image_url() {
		// @setup
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$data_map = [];

		$m_helper->method( 'param' )
			->will( $this->onConsecutiveCalls(
				'url',
				null
			) );

		// @exercise
		$validator = new BcBlastImageValidator( $m_helper );
		$ret = $validator->validate( $data_map );
		$this->assertEquals( BcStrings::ABF_MISSING_BLAST_IMAGE_URL, $ret );
	}

	function test_validate_should_set_image_given_url_param() {
		// @setup
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$data_map = [];

		$m_helper->method( 'param' )
			->will( $this->onConsecutiveCalls(
				'url',
				'http://www.terescode.com/a-image.png'
			) );

		// @exercise
		$validator = new BcBlastImageValidator( $m_helper );
		$ret = $validator->validate( $data_map );
		$this->assertNull( $ret );
		$this->assertTrue( isset( $data_map['image'] ) );
		$this->assertEquals( 'http://www.terescode.com/a-image.png', $data_map['image'] );
	}

	function test_validate_should_return_code_given_missing_image_file() {
		// @setup
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$data_map = [];

		$m_helper->method( 'param' )
			->willReturn( 'file' );

		// @exercise
		$validator = new BcBlastImageValidator( $m_helper );
		$ret = $validator->validate( $data_map );
		$this->assertEquals( BcStrings::ABF_MISSING_BLAST_IMAGE_FILE, $ret );
	}

	function test_validate_should_return_code_given_is_uploaded_file_returns_false() {
		// @setup
		global $g_is_uploaded_file;
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$data_map = [];

		$m_helper->method( 'param' )
			->willReturn( 'file' );
		$_FILES['bc-add-image-file'] = [
			'tmp_name' => '/path/to/file',
		];
		$g_is_uploaded_file = false;

		// @exercise
		$validator = new BcBlastImageValidator( $m_helper );
		$ret = $validator->validate( $data_map );
		$this->assertEquals( BcStrings::ABF_MISSING_BLAST_IMAGE_FILE, $ret );
	}

	function test_validate_should_return_code_given_is_uploaded_file_returns_false_and_error_set() {
		// @setup
		global $g_is_uploaded_file;
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$data_map = [];

		$m_helper->method( 'param' )
			->willReturn( 'file' );
		$_FILES['bc-add-image-file'] = [
			'tmp_name' => '/path/to/file',
			'error' => 2,
		];
		$g_is_uploaded_file = false;

		// @exercise
		$validator = new BcBlastImageValidator( $m_helper );
		$ret = $validator->validate( $data_map );
		$this->assertEquals( BcStrings::ABF_UPLOAD_IMAGE_FAILED . '_2', $ret );
	}

	function test_validate_should_return_image_given_is_uploaded_file_returns_true() {
		// @setup
		global $g_is_uploaded_file;
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$data_map = [];

		$m_helper->method( 'param' )
			->willReturn( 'file' );
		$_FILES['bc-add-image-file'] = [
			'tmp_name' => '/path/to/file',
		];
		$g_is_uploaded_file = true;

		// @exercise
		$validator = new BcBlastImageValidator( $m_helper );
		$ret = $validator->validate( $data_map );
		$this->assertNull( $ret );
		$this->assertTrue( isset( $data_map['image'] ) );
		$this->assertEquals( '/path/to/file', $data_map['image']['tmp_name'] );
	}
}
