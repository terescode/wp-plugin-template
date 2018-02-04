<?php

namespace Terescode\BlastCaster;

require_once 'includes/constants.php';
require_once 'includes/class-wp-helper.php';
require_once 'includes/class-plugin-helper.php';
require_once 'admin/class-add-blast-page.php';
require_once 'admin/class-add-blast-page-helper.php';

/**
 * Class BcAddBlastPageHelperTest
 *
 * @package Blastcaster
 */

class BcAddBlastPageHelperTest extends \BcPhpUnitTestCase {

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	function test_build_action_data_returns_false_given_json_encode_does() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		global $json_encode_called;

		$json_encode_called = false;
		function json_encode() {
			global $json_encode_called;
			$json_encode_called = true;
			return false;
		}

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_wph->method( 'wp_create_nonce' )
			->willReturn( '123941924731234' );

		// @exercise
		$page_helper = new BcAddBlastPageHelper( $m_helper );
		$action_data = $page_helper->build_action_data( 'awesome_action' );

		// @verify
		$this->assertTrue( $json_encode_called );
		$this->assertFalse( $action_data );
	}

	function test_build_action_data_returns_valid_json_given_required_args() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_wph->method( 'wp_create_nonce' )
			->willReturn( '123941924731234' );

		// @exercise
		$page_helper = new BcAddBlastPageHelper( $m_helper );
		$action_data = $page_helper->build_action_data( 'awesome_action' );

		// @verify
		$this->assertNotNull( $action_data );
		$this->assertEquals(
			'{"action":"awesome_action","action_nonce":"123941924731234"}',
			$action_data
		);
	}

	function test_build_action_data_returns_valid_json_given_optional_args() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$foo = new \stdClass();

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_wph->method( 'wp_create_nonce' )
			->willReturn( '123941924731234' );
		$foo->bar = 'baz';
		$foo->bop = 1234;

		// @exercise
		$page_helper = new BcAddBlastPageHelper( $m_helper );
		$action_data = $page_helper->build_action_data(
			'awesome_action',
			[
				'foo' => $foo,
			]
		);

		// @verify
		$this->assertNotNull( $action_data );
		$this->assertEquals(
			'{"action":"awesome_action","action_nonce":"123941924731234","foo":{"bar":"baz","bop":1234}}',
			$action_data
		);
	}

	function test_forward_param_sets_nothing_given_param_returns_null() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_helper->method( 'param' )
			->willReturn( null );

		// @exercise
		$data = [];
		$page_helper = new BcAddBlastPageHelper( $m_helper );
		$page_helper->forward_param( $data, 'type' );

		// @verify
		$this->assertEmpty( $data );
	}

	function test_forward_param_sets_value_given_param_returns_value() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_helper->method( 'param' )
			->willReturn( 'foo' );

		// @exercise
		$data = [];
		$page_helper = new BcAddBlastPageHelper( $m_helper );
		$page_helper->forward_param( $data, 'type' );

		// @verify
		$this->assertTrue( isset( $data['type'] ) );
		$this->assertEquals( 'foo', $data['type'] );
	}

	function test_forward_param_sets_key_value_given_key_and_param_returns_value() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_helper->method( 'param' )
			->willReturn( 'foo' );

		// @exercise
		$data = [];
		$page_helper = new BcAddBlastPageHelper( $m_helper );
		$page_helper->forward_param( $data, 'type', 'not_type' );

		// @verify
		$this->assertFalse( isset( $data['type'] ) );
		$this->assertTrue( isset( $data['not_type'] ) );
		$this->assertEquals( 'foo', $data['not_type'] );
	}
}
