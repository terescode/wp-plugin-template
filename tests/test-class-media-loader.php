<?php

namespace Terescode\BlastCaster;

require_once 'includes/constants.php';
require_once 'includes/class-wp-helper.php';
require_once 'includes/class-plugin-helper.php';
require_once 'includes/class-media-loader.php';

/**
 * Class BcMediaLoaderTest
 *
 * @package Blastcaster
 */

class BcMediaLoaderTest extends \BcPhpUnitTestCase {

	/**
	 * @given a file array as input
	 * @method load_media
	 * @should call wp_handle_upload and return its result
	 */

	public function test_load_media_should_call_wp_handle_upload_and_return_its_result_given_file() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$file = [
			'tmp_name' => '/path/to/file',
		];
		$return_1 = [ 'file' => '/path/to/uploaded/file.png' ];
		$return_2 = -1;

		$m_wph->expects( $this->exactly( 2 ) )
			->method( 'wp_handle_upload' )
			->with(
				$this->equalTo( [
					'tmp_name' => '/path/to/file',
				] ),
				$this->equalTo( [
					'action' => 'test_action',
				] )
			)
			->will( $this->onConsecutiveCalls(
				$return_1,
				$return_2
			));

		// @exercise
		$loader = new BcMediaLoader( $m_helper, 'test_action' );
		$ret = $loader->load_media( $file );
		$this->assertEquals( $return_1, $ret );
		$ret = $loader->load_media( $file );
		$this->assertEquals( $return_2, $ret );
	}

	/**
	 * @given a URL string as input and download_url returns error
	 * @method load_media
	 * @should return a wp_error
	 */

	public function test_load_media_should_return_wp_error_given_string_and_download_url_returns_error() {
		// @setup
		$m_error = $this->mock( 'WP_Error' );
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_wph->method( 'is_wp_error' )
			->willReturn( true );
		$m_wph->method( 'download_url' )
			->willReturn( $m_error );
		$url = 'http://www.terescode.com/favico.ico';

		// @exercise
		$loader = new BcMediaLoader( $m_helper, 'test_action' );
		$ret = $loader->load_media( $url );
		$this->assertEquals( $m_error, $ret );
	}

	/**
	 * @given an invalid URL string as input and download_url returns a filename
	 * @method load_media
	 * @should return an array with 'error' key
	 */

	public function test_load_media_should_return_array_with_error_key_given_wp_parse_url_returns_false() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_wph->method( 'is_wp_error' )
			->willReturn( false );
		$m_wph->method( 'download_url' )
			->willReturn( 'tests/fixtures/sample.json' );
		$m_wph->method( 'wp_parse_url' )
			->willReturn( false );
		$m_helper->method( 'string' )
			->willReturn( 'The error string' );
		$url = 'http333:/i.do.not.parse^:30';

		// @exercise
		$loader = new BcMediaLoader( $m_helper, 'test_action' );
		$ret = $loader->load_media( $url );
		$this->assertInternalType( 'array', $ret );
		$this->assertTrue( isset( $ret['error'] ) );
		$this->assertEquals( 'The error string', $ret['error'] );
	}

	public function test_load_media_should_return_array_with_error_key_given_check_image_filename_returns_false() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_wph->method( 'is_wp_error' )
			->willReturn( false );
		$m_wph->method( 'download_url' )
			->willReturn( 'tests/fixtures/sample.json' );
		$m_wph->method( 'wp_parse_url' )
			->willReturn( '/favico.ico' );
		$m_helper->method( 'check_image_filename' )
			->willReturn( false );
		$m_helper->method( 'string' )
			->willReturn( 'The error string' );
		$url = 'http://www.terescode.com/favico.ico';

		// @exercise
		$loader = new BcMediaLoader( $m_helper, 'test_action' );
		$ret = $loader->load_media( $url );
		$this->assertInternalType( 'array', $ret );
		$this->assertTrue( isset( $ret['error'] ) );
		$this->assertEquals( 'The error string', $ret['error'] );
	}

	/**
	 * @given a URL string as input and download_url returns a filename
	 * @method load_media
	 * @should call wp_handle_sideload and return its result
	 */

	public function test_load_media_should_call_wp_handle_sideload_and_return_its_result_given_string_and_download_url_returns_file() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_wph->method( 'is_wp_error' )
			->willReturn( false );
		$m_wph->method( 'download_url' )
			->willReturn( 'tests/fixtures/sample.json' );
		$m_wph->method( 'wp_parse_url' )
			->willReturn( '/favico.ico' );
		$m_helper->method( 'check_image_filename' )
			->will( $this->returnArgument( 1 ) );
		$url = 'http://www.terescode.com/favico.ico';
		$return_1 = [ 'file' => '/path/to/uploaded/file.png' ];
		$return_2 = -1;

		$m_wph->expects( $this->exactly( 2 ) )
			->method( 'wp_handle_sideload' )
			->with(
				$this->equalTo( [
					'name' => 'favico.ico',
					'tmp_name' => 'tests/fixtures/sample.json',
					'error' => 0,
					'size' => 3559
				] ),
				$this->equalTo( [
					'test_form' => false,
					'test_size' => true
				] )
			)
			->will( $this->onConsecutiveCalls(
				$return_1,
				$return_2
			));

		// @exercise
		$loader = new BcMediaLoader( $m_helper, 'test_action' );
		$ret = $loader->load_media( $url );
		$this->assertEquals( $return_1, $ret );
		$ret = $loader->load_media( $url );
		$this->assertEquals( $return_2, $ret );
	}
}
