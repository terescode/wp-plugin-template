<?php

namespace Terescode\WordPress;

require_once 'includes/constants.php';
require_once 'includes/class-wp-helper.php';
require_once 'includes/class-plugin-helper.php';
require_once 'includes/interface-action.php';
require_once 'includes/class-generic-controller.php';

/**
 * Class TcGenericControllerTest
 *
 * @package Blastcaster
 */

class TcGenericControllerTest extends \BcPhpUnitTestCase {

	/**
	 * Test register_handlers
	 */
	function test_process_actions_should_not_call_action_given_no_action_param() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_action = $this->mock( 'Terescode\WordPress\TcAction' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_action->method( 'get_name' )
			->willReturn( 'my_action' );
		$m_helper->expects( $this->once() )
			->method( 'param' )
			->willReturn( null );
		$m_wph->expects( $this->never() )
			->method( 'wp_verify_nonce' );
		$m_action->expects( $this->never() )
			->method( 'do_action' );

		// @exercise
		$controller = new TcGenericController( $m_helper, null, array( $m_action ) );
		$controller->process_actions();
	}

	/**
	 * Test register_handlers
	 */
	function test_process_actions_should_not_call_action_given_wp_verify_nonce_fails() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_action = $this->mock( 'Terescode\WordPress\TcAction' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_action->method( 'get_name' )
			->willReturn( 'my_action' );
		$m_helper->expects( $this->exactly( 2 ) )
			->method( 'param' )
			->will(
				$this->onConsecutiveCalls( 'my_action', '123456' )
			);
		$m_wph->expects( $this->once() )
			->method( 'wp_verify_nonce' )
			->willReturn( false );
		$m_action->expects( $this->never() )
			->method( 'do_action' );

		// @exercise
		$controller = new TcGenericController( $m_helper, null, array( $m_action ) );
		$controller->process_actions();
	}

		/**
	 * Test register_handlers
	 */
	function test_process_actions_should_call_action_given_wp_verify_nonce_succeeds() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_action = $this->mock( 'Terescode\WordPress\TcAction' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_action->method( 'get_name' )
			->willReturn( 'my_action' );
		$m_helper->expects( $this->exactly( 2 ) )
			->method( 'param' )
			->will(
				$this->onConsecutiveCalls( 'my_action', '123456' )
			);
		$m_wph->expects( $this->once() )
			->method( 'wp_verify_nonce' )
			->willReturn( 2 );
		$m_action->expects( $this->once() )
			->method( 'do_action' );

		// @exercise
		$controller = new TcGenericController( $m_helper, null, array( $m_action ) );
		$controller->process_actions();
	}

	/**
	 * Test register_menu
	 */
	function test_register_menu_should_call_add_page_and_not_add_meta_boxes_and_return_hook_suffix_given_no_hook_suffix() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_view = $this->mock( 'Terescode\WordPress\TcView' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_view->method( 'add_page' )
			->willReturn( null );

		// @exercise
		$controller = new TcGenericController( $m_helper, $m_view, array() );
		$hook_suffix = $controller->register_menu();
		$this->assertNull( $hook_suffix );
	}

	/**
	 * Test register_menu
	 */
	function test_register_menu_should_call_add_page_and_not_add_meta_boxes_and_return_hook_suffix_given_is_not_metabox_page() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_view = $this->mock( 'Terescode\WordPress\TcView' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_view->method( 'add_page' )
			->willReturn( 'hook_suffix_1' );
		$m_view->method( 'is_metabox_page' )
			->willReturn( false );

		// @exercise
		$controller = new TcGenericController( $m_helper, $m_view, array() );
		$hook_suffix = $controller->register_menu();
		$this->assertEquals( 'hook_suffix_1', $hook_suffix );
	}

	/**
	 * Test register_menu
	 */
	function test_register_menu_should_call_add_page_and_add_meta_boxes_and_return_hook_suffix_given_is_metabox_page() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_view = $this->mock( 'Terescode\WordPress\TcView' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_view->method( 'add_page' )
			->willReturn( 'hook_suffix_1' );
		$m_view->method( 'is_metabox_page' )
			->willReturn( true );
		$m_wph->expects( $this->once() )
			->method( 'add_action' )
			->with(
				'add_meta_boxes_hook_suffix_1',
				array( $m_view, 'add_meta_boxes' )
			);

		// @exercise
		$controller = new TcGenericController( $m_helper, $m_view, array() );
		$hook_suffix = $controller->register_menu();
		$this->assertEquals( 'hook_suffix_1', $hook_suffix );
	}

	/**
	 * Test load_{$pagenow} hook
	 */
	function test_load_pagenow_should_not_call_add_meta_boxes_enqueue_script_and_call_load_given_view_is_not_metabox_page() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_view = $this->mock( 'Terescode\WordPress\TcView' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_view->method( 'is_metabox_page' )
			->willReturn( false );
		$m_view->expects( $this->once() )
			->method( 'load_pagenow' );

		// @exercise
		$controller = new TcGenericController( $m_helper, $m_view, array() );
		$controller->load_pagenow();
	}

	/**
	 * Test load_{$pagenow} hook
	 */
	function test_load_pagenow_should_call_add_meta_boxes_enqueue_script_and_call_load_given_controller() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_view = $this->mock( 'Terescode\WordPress\TcView' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_view->method( 'is_metabox_page' )
			->willReturn( true );
		$m_view->method( 'get_hook_suffix' )
			->willReturn( 'hook_suffix_1' );
		$m_wph->expects( $this->exactly( 2 ) )
			->method( 'do_action' )
			->withConsecutive(
				[
					$this->equalTo( 'add_meta_boxes_hook_suffix_1' ),
					$this->equalTo( null ),
				],
				[
					$this->equalTo( 'add_meta_boxes' ),
					$this->equalTo( 'hook_suffix_1' ),
					$this->equalTo( null ),
				]
			);
		$m_wph->expects( $this->once() )
			->method( 'wp_enqueue_script' )
			->with(
				$this->equalTo( 'postbox' )
			);
		$m_view->expects( $this->once() )
			->method( 'load_pagenow' );

		// @exercise
		$controller = new TcGenericController( $m_helper, $m_view, array() );
		$controller->load_pagenow();
	}

	/**
	 * Test admin_head hook
	 */
	function test_admin_head_noop() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_view = $this->mock( 'Terescode\WordPress\TcView' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_view->method( 'is_metabox_page' )
			->willReturn( false );

		// @exercise
		$controller = new TcGenericController( $m_helper, $m_view, array() );
		$controller->admin_head();
	}

	/**
	 * Test admin_footer
	 */
	function test_admin_footer_should_output_postbox_script_given_is_metabox_page() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_view = $this->mock( 'Terescode\WordPress\TcView' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_view->method( 'is_metabox_page' )
			->willReturn( true );

		$this->expect_html(
			function ( $result ) {
				// @verify
				$xpath = new \DOMXPath( $result );
				$elements = $xpath->query( '//script' );
				$this->assertEquals( 1, $elements->length, 'Should only be one script in output!' );
				$node = $elements->item( 0 );
				$this->assertEquals( XML_ELEMENT_NODE, $node->nodeType );
				$this->assertRegExp( '/postboxes\.add_postbox_toggles/', $node->textContent );
			}
		);

		// @exercise
		$controller = new TcGenericController( $m_helper, $m_view, array() );
		$controller->admin_footer();
	}
}
