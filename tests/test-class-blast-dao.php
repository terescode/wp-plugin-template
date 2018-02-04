<?php

namespace Terescode\BlastCaster;

require_once 'includes/constants.php';
require_once 'includes/class-wp-helper.php';
require_once 'includes/class-plugin-helper.php';
require_once 'includes/class-wp-include-formatter.php';
require_once 'includes/class-blast-dao.php';

/**
 * Class BcBlastDaoTest
 *
 * @package Blastcaster
 */

class BcBlastDaoTest extends \BcPhpUnitTestCase {

	function test_create_post_should_return_false_given_formatter_fails() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_formatter = $this->mock( 'Terescode\BlastCaster\BcBlastFormatter' );
		$blast = new BcBlast( 'TDD is fun', 'TDD is test driven development!' );
		$dao = new BcBlastDao( $m_helper, $m_formatter );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_formatter->method( 'format' )
			->willReturn( false );

		// @exercise
		$ret = $dao->create_post( $blast );

		// @verify
		$this->assertFalse( $ret );
	}

	function test_create_post_should_return_wp_error_given_wp_insert_post_does() {
		// @setup
		$m_error = $this->mock( 'WP_Error' );
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_formatter = $this->mock( 'Terescode\BlastCaster\BcBlastFormatter' );
		$blast = new BcBlast( 'TDD is fun', 'TDD is test driven development!' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_formatter->method( 'format' )
			->willReturn( 'some content' );
		$m_wph->method( 'wp_insert_post' )
			->willReturn( $m_error );
		$m_wph->method( 'is_wp_error' )
			->willReturn( true );

		// @exercise
		$dao = new BcBlastDao( $m_helper, $m_formatter );
		$ret = $dao->create_post( $blast );

		// @verify
		$this->assertInstanceOf( 'WP_Error', $ret );
	}

	function test_create_post_should_return_post_id_given_wp_insert_post_does_and_no_image() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$formatter = new BcWpIncludeFormatter( 'tests/fixtures/sample-template.php' );
		$blast = new BcBlast( 'TDD is fun', 'TDD is test driven development!' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_wph->method( 'wp_insert_post' )
			->willReturn( 3456789 );

		// @exercise
		$dao = new BcBlastDao( $m_helper, $formatter );
		$ret = $dao->create_post( $blast );

		// @verify
		$this->assertEquals( 3456789, $ret );
	}

	function test_create_post_should_return_wp_error_given_wp_insert_attachment_does() {
		// @setup
		$m_error = $this->mock( 'WP_Error' );
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$formatter = new BcWpIncludeFormatter( 'tests/fixtures/sample-template.php' );
		$blast = new BcBlast(
			'TDD is fun',
			'TDD is test driven development!',
			[
				'file' => '/path/to/file.png',
				'type' => 'image/png',
			]
		);

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_wph->method( 'wp_insert_post' )
			->willReturn( 3456789 );
		$m_wph->method( 'wp_upload_dir' )
			->willReturn( [ 'url' => 'http://local.wordpress.dev/wp-content/uploads/2016/12' ] );
		$m_wph->method( 'wp_insert_attachment' )
			->willReturn( $m_error );
		$m_wph->method( 'is_wp_error' )
			->will( $this->onConsecutiveCalls( false, true ) );

		// @exercise
		$dao = new BcBlastDao( $m_helper, $formatter );
		$ret = $dao->create_post( $blast );

		// @verify
		$this->assertInstanceOf( 'WP_Error', $ret );
	}

	function test_create_post_should_return_false_given_wp_update_attachment_metadata_does() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$formatter = new BcWpIncludeFormatter( 'tests/fixtures/sample-template.php' );
		$blast = new BcBlast(
			'TDD is fun',
			'TDD is test driven development!',
			[
				'file' => '/path/to/file.png',
				'type' => 'image/png',
			]
		);

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_wph->method( 'wp_insert_post' )
			->willReturn( 3456789 );
		$m_wph->method( 'wp_upload_dir' )
			->willReturn( [ 'url' => 'http://local.wordpress.dev/wp-content/uploads/2016/12' ] );
		$m_wph->method( 'wp_insert_attachment' )
			->willReturn( 98765432 );
		$m_wph->method( 'is_wp_error' )
			->will( $this->onConsecutiveCalls( false, false ) );
		$m_wph->method( 'wp_generate_attachment_metadata' )
			->willReturn( [] );
		$m_wph->method( 'wp_update_attachment_metadata' )
			->willReturn( false );

		// @exercise
		$dao = new BcBlastDao( $m_helper, $formatter );
		$ret = $dao->create_post( $blast );

		// @verify
		$this->assertFalse( $ret );
	}

	function test_create_post_should_return_false_given_set_post_thumbnail_does() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$formatter = new BcWpIncludeFormatter( 'tests/fixtures/sample-template.php' );
		$blast = new BcBlast(
			'TDD is fun',
			'TDD is test driven development!',
			[
				'file' => '/path/to/file.png',
				'type' => 'image/png',
			]
		);

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_wph->method( 'wp_insert_post' )
			->willReturn( 3456789 );
		$m_wph->method( 'wp_upload_dir' )
			->willReturn( [ 'url' => 'http://local.wordpress.dev/wp-content/uploads/2016/12' ] );
		$m_wph->method( 'wp_insert_attachment' )
			->willReturn( 98765432 );
		$m_wph->method( 'is_wp_error' )
			->will( $this->onConsecutiveCalls( false, false ) );
		$m_wph->method( 'wp_generate_attachment_metadata' )
			->willReturn( [] );
		$m_wph->method( 'wp_update_attachment_metadata' )
			->willReturn( true );
		$m_wph->method( 'set_post_thumbnail' )
			->willReturn( false );

		// @exercise
		$dao = new BcBlastDao( $m_helper, $formatter );
		$ret = $dao->create_post( $blast );

		// @verify
		$this->assertFalse( $ret );
	}

	function test_create_post_should_return_post_id_given_set_post_thumbnail_does_not_return_false() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$formatter = new BcWpIncludeFormatter( 'tests/fixtures/sample-template.php' );
		$blast = new BcBlast(
			'TDD is fun',
			'TDD is test driven development!',
			[
				'file' => '/path/to/file.png',
				'type' => 'image/png',
			]
		);

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_wph->method( 'wp_insert_post' )
			->willReturn( 3456789 );
		$m_wph->method( 'wp_upload_dir' )
			->willReturn( [ 'url' => 'http://local.wordpress.dev/wp-content/uploads/2016/12' ] );
		$m_wph->method( 'wp_insert_attachment' )
			->willReturn( 98765432 );
		$m_wph->method( 'is_wp_error' )
			->will( $this->onConsecutiveCalls( false, false ) );
		$m_wph->method( 'wp_generate_attachment_metadata' )
			->willReturn( [] );
		$m_wph->method( 'wp_update_attachment_metadata' )
			->willReturn( true );
		$m_wph->method( 'set_post_thumbnail' )
			->willReturn( true );

		// @exercise
		$dao = new BcBlastDao( $m_helper, $formatter );
		$ret = $dao->create_post( $blast );

		// @verify
		$this->assertEquals( 3456789, $ret );
	}

	function test_create_post_should_set_post_category_given_blast_with_categories() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$formatter = new BcWpIncludeFormatter( 'tests/fixtures/sample-template.php' );
		$blast = new BcBlast(
			'TDD is fun',
			'TDD is test driven development!',
			[
				'file' => '/path/to/file.png',
				'type' => 'image/png',
			],
			[ 1, 2, 3 ]
		);

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_wph->expects( $this->once() )
			->method( 'wp_insert_post' )
			->with( $this->callback( function ( $subject ) {
				return isset( $subject['post_category'] ) &&
					3 === count( $subject['post_category'] ) &&
					1 === $subject['post_category'][0] &&
					2 === $subject['post_category'][1] &&
					3 === $subject['post_category'][2];
			}));

		// @exercise
		$dao = new BcBlastDao( $m_helper, $formatter );
		$dao->create_post( $blast );
	}

	function test_create_post_should_set_tags_input_given_blast_with_tags() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$formatter = new BcWpIncludeFormatter( 'tests/fixtures/sample-template.php' );
		$blast = new BcBlast(
			'TDD is fun',
			'TDD is test driven development!',
			[
				'file' => '/path/to/file.png',
				'type' => 'image/png',
			],
			[ 1, 2, 3 ],
			[ 425, 'test tag', 2341 ]
		);

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_wph->expects( $this->once() )
			->method( 'wp_insert_post' )
			->with( $this->callback( function ( $subject ) {
				return isset( $subject['tags_input'] ) &&
					3 === count( $subject['tags_input'] ) &&
					425 === $subject['tags_input'][0] &&
					'test tag' === $subject['tags_input'][1] &&
					2341 === $subject['tags_input'][2];
			}));

		// @exercise
		$dao = new BcBlastDao( $m_helper, $formatter );
		$dao->create_post( $blast );
	}
}
