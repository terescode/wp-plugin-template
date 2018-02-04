<?php

namespace Terescode\WordPress;

require_once 'includes/constants.php';
require_once 'includes/class-plugin-helper.php';
require_once 'includes/class-generic-plugin.php';
require_once 'includes/interface-controller.php';

/**
 * Class TcGenericPluginTest
 *
 * @package Blastcaster
 */

class TcGenericPluginTest extends \BcPhpUnitTestCase {
	/**
	 * Test constructor and get_plugin_id
	 */
	function test_constructor_should_set_plugin_id_and_plugin_helper() {
		// @setup
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );

		// @sut @exercise
		$plugin = new TcGenericPlugin( 'generic-plugin-id', $m_helper );

		// @verify
		$this->assertEquals( 'generic-plugin-id', $plugin->get_plugin_id() );
		$this->assertEquals( $m_helper, $plugin->get_plugin_helper() );
	}

	/**
	 * Test init
	 */
	function test_init_should_call_init_admin_plugin_with_self() {
		// @setup
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		// @sut
		$plugin = new TcGenericPlugin( 'generic-plugin-id', $m_helper );

		$m_helper->expects( $this->once() )
			->method( 'init_admin_plugin' )
			->with( $this->equalTo( $plugin ) );

		// @exercise
		$plugin->init();
	}

	/**
	 * Test load
	 */
	function test_load_should_call_load_textdomain_with_self() {
		// @setup
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_controller = $this->mock( 'Terescode\WordPress\TcController' );
		// @sut
		$plugin = new TcGenericPlugin( 'generic-plugin-id', $m_helper, [ $m_controller ] );

		$m_helper->expects( $this->once() )
			->method( 'load_textdomain' )
			->with( $this->equalTo( $plugin ) );

		// @exercise
		$plugin->load();
	}

	/**
	 * Test install_admin_menus
	 */
	function test_install_admin_menus_should_add_zero_hooks_given_zero_controllers() {
		// @setup
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_controllers = array();
		$hooknames = null;

		// @exercise
		$plugin = new TcGenericPlugin( 'generic-plugin-id', $m_helper, $m_controllers );
		$hooknames = $plugin->install_admin_menus( $m_controllers );

		// @verify
		$this->assertNotNull( $hooknames );
		$this->assertInternalType( 'array', $hooknames );
		$this->assertEquals( 0, count( $hooknames ) );
	}

	/**
	 * Test install_admin_menus
	 */
	function test_install_admin_menus_should_add_zero_hooks_given_controller_init_returns_falsy() {
		// @setup
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_controllers = array();
		$hooknames = null;

		$m_controller = $this->mock( 'Terescode\WordPress\TcController' );
		$m_controller->expects( $this->once() )
			->method( 'register_menu' )
			->willReturn( false );
		$m_controllers[] = $m_controller;

		// @exercise
		$plugin = new TcGenericPlugin( 'generic-plugin-id', $m_helper, $m_controllers );
		$hooknames = $plugin->install_admin_menus( $m_controllers );

		// @verify
		$this->assertNotNull( $hooknames );
		$this->assertInternalType( 'array', $hooknames );
		$this->assertEquals( 0, count( $hooknames ) );
	}

	/**
	 * Helper function to test admin_menus
	 */
	function install_admin_menus_should_add_N_hooks_given_N_controllers( $count ) {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_controllers = array();
		$hooknames = null;

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );

		$add_action_expects = array();
		for ( $idx = 0; $idx < $count; $idx += 1 ) {
			$hookname = 'test_hookname_' . $idx;
			$m_controller = $this->mock( 'Terescode\WordPress\TcController' );
			$m_controller->expects( $this->once() )
				->method( 'register_menu' )
				->willReturn( $hookname );
			$m_controllers[] = $m_controller;
			$add_action_expects[] = array(
				$this->equalTo( 'load-' . $hookname ),
				$this->equalTo( array( $m_controller, 'load_pagenow' ) ),
			);
			$add_action_expects[] = array(
				$this->equalTo( 'admin_head-' . $hookname ),
				$this->equalTo( array( $m_controller, 'admin_head' ) ),
			);
			$add_action_expects[] = array(
				$this->equalTo( 'admin_footer-' . $hookname ),
				$this->equalTo( array( $m_controller, 'admin_footer' ) ),
			);
		}

		$builder = $m_wph->expects( $this->exactly( 3 * $count ) )
			->method( 'add_action' );

		call_user_func_array( array( $builder, 'withConsecutive' ), $add_action_expects );

		// @exercise
		$plugin = new TcGenericPlugin( 'generic-plugin-id', $m_helper, $m_controllers );
		$hooknames = $plugin->install_admin_menus( $m_controllers );

		// @verify
		$this->assertNotNull( $hooknames );
		$this->assertInternalType( 'array', $hooknames );
		$this->assertEquals( $count, count( $hooknames ) );
		for ( $idx = 0; $idx < $count; $idx += 1 ) {
			$this->assertEquals( 'test_hookname_' . $idx, $hooknames[ $idx ] );
		}
	}

	/**
	 * Test install_admin_menus
	 */
	function test_install_admin_menus_should_add_1_hook_given_1_controller() {
		$this->invoke_with_random_count(
			1,
			1,
			array( $this, 'install_admin_menus_should_add_N_hooks_given_N_controllers' )
		);
	}

	/**
	 * Test install_admin_menus
	 */
	function test_install_admin_menus_should_add_N_hooks_given_N_controllers() {
		$this->invoke_with_random_count(
			5,
			10,
			array( $this, 'install_admin_menus_should_add_N_hooks_given_N_controllers' )
		);
	}
}
