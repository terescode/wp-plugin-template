<?php

namespace Terescode\BlastCaster;

require_once 'includes/constants.php';
require_once 'includes/class-wp-helper.php';
require_once 'includes/class-plugin-helper.php';
require_once 'includes/class-blast-dao.php';
require_once 'admin/class-add-blast-page-helper.php';
require_once 'admin/class-add-blast-page.php';

use Terescode\WordPress\TcPluginHelper;

/**
 * Class WpAdminPluginTest
 *
 * @package Blastcaster
 */

class BcAddBlastPageTest extends \BcPhpUnitTestCase {

	/**
	 * Test init @should call add_posts_page
	 */
	function test_add_page_should_return_false_given_add_posts_page_does() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_page_helper = $this->mock( 'Terescode\BlastCaster\BcAddBlastPageHelper' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_wph->method( 'add_posts_page' )
			->willReturn( false );

		// @exercise
		$view = new BcAddBlastPage( $m_helper, $m_page_helper );
		$hook_suffix = $view->add_page();
		$this->assertFalse( $hook_suffix );
		$this->assertFalse( $view->get_hook_suffix() );
	}

	/**
	 * Test init @should call add_posts_page
	 */
	function test_add_page_should_return_hook_suffix_given_add_posts_page_returns_hook_suffix() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_page_helper = $this->mock( 'Terescode\BlastCaster\BcAddBlastPageHelper' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_wph->method( 'add_posts_page' )
			->willReturn( 'test_hook_suffix' );

		// @exercise
		$view = new BcAddBlastPage( $m_helper, $m_page_helper );
		$hook_suffix = $view->add_page();

		// @verify
		$this->assertEquals( 'test_hook_suffix', $hook_suffix );
		$this->assertEquals( 'test_hook_suffix', $view->get_hook_suffix() );
	}

	/**
	 * Test print_scripts
	 */
	function test_print_scripts_should_add_admin_notice_given_build_action_data_returns_false() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_page_helper = $this->mock( 'Terescode\BlastCaster\BcAddBlastPageHelper' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_helper->expects( $this->once() )
			->method( 'add_admin_notice' )
			->with(
				BcStrings::ABF_BUILD_ACTION_DATA_FAILED,
				TcPluginHelper::NOTICE_TYPE_ERROR,
				true
			);
		$m_page_helper->method( 'build_action_data' )
			->willReturn( false );

		// @exercise
		$view = new BcAddBlastPage( $m_helper, $m_page_helper );
		$view->print_scripts();
	}

	/**
	 * Test print_scripts
	 */
	function test_print_scripts_should_output_bc_data_given_build_action_data_returns_data() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_page_helper = $this->mock( 'Terescode\BlastCaster\BcAddBlastPageHelper' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_helper->expects( $this->never() )
			->method( 'add_admin_notice' );
		$m_page_helper->method( 'build_action_data' )
			->willReturn( '{"action":"awesome_action","foo":"bar"}' );
		$this->expectOutputString(
			'<script type="text/javascript">var terescode={bc_data:'
			. '{"action":"awesome_action","foo":"bar"}};</script>'
		);

		// @exercise
		$view = new BcAddBlastPage( $m_helper, $m_page_helper );
		$view->print_scripts();
	}

	/**
	 * Test render_add_blast
	 */
	function test_load_pagenow_should_not_set_page_data_given_no_post_data() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_page_helper = $this->mock( 'Terescode\BlastCaster\BcAddBlastPageHelper' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_wph->expects( $this->once() )
			->method( 'wp_enqueue_style' )
			->with(
				$this->equalTo( 'bc-styles' ),
				$this->equalTo( BC_PLUGIN_URL . 'admin/css/bundle.css' ),
				$this->equalTo( [] ),
				$this->equalTo( false ),
				$this->equalTo( 'all' )
			);
		$m_wph->expects( $this->once() )
			->method( 'wp_enqueue_script' )
			->with(
				$this->equalTo( 'bc-scripts' ),
				$this->equalTo( BC_PLUGIN_URL . 'admin/js/bundle.js' ),
				$this->equalTo( [ 'jquery' ] ),
				$this->equalTo( false ),
				$this->equalTo( true )
			);
		$m_wph->expects( $this->once() )
			->method( 'add_action' )
			->with(
				$this->stringContains( 'admin_print_scripts-' ),
				$this->callback( function( $subject ) {
				  	return is_array( $subject ) && 2 === count( $subject ) &&
					$subject[0] instanceof BcAddBlastPage && 'print_scripts' === $subject[1];
				})
			);

		// @exercise
		$view = new BcAddBlastPage( $m_helper, $m_page_helper );
		$view->load_pagenow();

		// @verify
		$this->assertNull( $view->get_page_data() );
	}

	/**
	 * Test render_add_blast
	 */
	function test_load_pagenow_should_not_set_page_data_given_empty_post_data() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_page_helper = $this->mock( 'Terescode\BlastCaster\BcAddBlastPageHelper' );

		$_POST['pageData'] = '';
		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_wph->expects( $this->once() )
			->method( 'wp_enqueue_style' )
			->with(
				$this->equalTo( 'bc-styles' ),
				$this->equalTo( BC_PLUGIN_URL . 'admin/css/bundle.css' ),
				$this->equalTo( [] ),
				$this->equalTo( false ),
				$this->equalTo( 'all' )
			);
		$m_wph->expects( $this->once() )
			->method( 'wp_enqueue_script' )
			->with(
				$this->equalTo( 'bc-scripts' ),
				$this->equalTo( BC_PLUGIN_URL . 'admin/js/bundle.js' ),
				$this->equalTo( [ 'jquery' ] ),
				$this->equalTo( false ),
				$this->equalTo( true )
			);
		$m_wph->expects( $this->once() )
			->method( 'add_action' )
			->with(
				$this->stringContains( 'admin_print_scripts-' ),
				$this->callback( function( $subject ) {
				  	return is_array( $subject ) && 2 === count( $subject ) &&
					$subject[0] instanceof BcAddBlastPage && 'print_scripts' === $subject[1];
				})
			);

		// @exercise
		$view = new BcAddBlastPage( $m_helper, $m_page_helper );
		$view->load_pagenow();

		// @verify
		$this->assertNull( $view->get_page_data() );
	}

	/**
	 * Test render_add_blast
	 */
	function test_load_pagenow_should_not_set_page_data_given_wsonly_post_data() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_page_helper = $this->mock( 'Terescode\BlastCaster\BcAddBlastPageHelper' );

		$_POST['pageData'] = "\t\t\t\r\n\n\t     \t\t\r\n";
		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_wph->expects( $this->once() )
			->method( 'wp_enqueue_style' )
			->with(
				$this->equalTo( 'bc-styles' ),
				$this->equalTo( BC_PLUGIN_URL . 'admin/css/bundle.css' ),
				$this->equalTo( [] ),
				$this->equalTo( false ),
				$this->equalTo( 'all' )
			);
		$m_wph->expects( $this->once() )
			->method( 'wp_enqueue_script' )
			->with(
				$this->equalTo( 'bc-scripts' ),
				$this->equalTo( BC_PLUGIN_URL . 'admin/js/bundle.js' ),
				$this->equalTo( [ 'jquery' ] ),
				$this->equalTo( false ),
				$this->equalTo( true )
			);
		$m_wph->expects( $this->once() )
			->method( 'add_action' )
			->with(
				$this->stringContains( 'admin_print_scripts-' ),
				$this->callback( function( $subject ) {
				  	return is_array( $subject ) && 2 === count( $subject ) &&
					$subject[0] instanceof BcAddBlastPage && 'print_scripts' === $subject[1];
				})
			);

		// @exercise
		$view = new BcAddBlastPage( $m_helper, $m_page_helper );
		$view->load_pagenow();

		// @verify
		$this->assertNull( $view->get_page_data() );
	}

	/**
	 * Test render_add_blast
	 */
	function test_load_pagenow_should_set_page_data_given_valid_post_data() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_page_helper = $this->mock( 'Terescode\BlastCaster\BcAddBlastPageHelper' );

		$json_file = file_get_contents( 'tests/fixtures/sample.json', true );
		$_POST['pageData'] = $json_file;
		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_wph->expects( $this->once() )
			->method( 'wp_enqueue_style' )
			->with(
				$this->equalTo( 'bc-styles' ),
				$this->equalTo( BC_PLUGIN_URL . 'admin/css/bundle.css' ),
				$this->equalTo( [] ),
				$this->equalTo( false ),
				$this->equalTo( 'all' )
			);
		$m_wph->expects( $this->once() )
			->method( 'wp_enqueue_script' )
			->with(
				$this->equalTo( 'bc-scripts' ),
				$this->equalTo( BC_PLUGIN_URL . 'admin/js/bundle.js' ),
				$this->equalTo( [ 'jquery' ] ),
				$this->equalTo( false ),
				$this->equalTo( true )
			);
		$m_wph->expects( $this->once() )
			->method( 'add_action' )
			->with(
				$this->stringContains( 'admin_print_scripts-' ),
				$this->callback( function( $subject ) {
				  	return is_array( $subject ) && 2 === count( $subject ) &&
					$subject[0] instanceof BcAddBlastPage && 'print_scripts' === $subject[1];
				})
			);

		// @test
		$view = new BcAddBlastPage( $m_helper, $m_page_helper );
		$view->load_pagenow();

		// @verify
		$page_data = $view->get_page_data();
		$this->assertNotNull( $page_data );
		$this->assertNotNull( $page_data->urls );
		$this->assertNotNull( $page_data->images );
		$this->assertNotNull( $page_data->allImages );
		$this->assertNotNull( $page_data->titles );
		$this->assertNotNull( $page_data->descriptions );
		$this->assertNotNull( $page_data->tags );
		$this->assertEquals( 2, count( $page_data->titles ) );
	}

	/**
	 * Test render_add_blast
	 */
	function test_load_pagenow_should_add_admin_notice_given_invalid_post_data() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_page_helper = $this->mock( 'Terescode\BlastCaster\BcAddBlastPageHelper' );

		$json_file = file_get_contents( 'tests/fixtures/sample-invalid.json', true );
		$_POST['pageData'] = $json_file;

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_wph->expects( $this->once() )
			->method( 'wp_enqueue_style' )
			->with(
				$this->equalTo( 'bc-styles' ),
				$this->equalTo( BC_PLUGIN_URL . 'admin/css/bundle.css' ),
				$this->equalTo( [] ),
				$this->equalTo( false ),
				$this->equalTo( 'all' )
			);
		$m_wph->expects( $this->once() )
			->method( 'wp_enqueue_script' )
			->with(
				$this->equalTo( 'bc-scripts' ),
				$this->equalTo( BC_PLUGIN_URL . 'admin/js/bundle.js' ),
				$this->equalTo( [ 'jquery' ] ),
				$this->equalTo( false ),
				$this->equalTo( true )
			);
		$m_wph->expects( $this->once() )
			->method( 'add_action' )
			->with(
				$this->stringContains( 'admin_print_scripts-' ),
				$this->callback( function( $subject ) {
				  	return is_array( $subject ) && 2 === count( $subject ) &&
					$subject[0] instanceof BcAddBlastPage && 'print_scripts' === $subject[1];
				})
			);
		$m_helper->expects( $this->once() )
			->method( 'add_admin_notice' )
			->with(
				$this->isType( 'string' )
			);

		// @exercise
		$view = new BcAddBlastPage( $m_helper, $m_page_helper );
		$view->load_pagenow();

		// @verify
		$this->assertNull( $view->get_page_data() );
	}

	/**
	 * Test render
	 */
	function test_render_should_call_render() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_page_helper = $this->mock( 'Terescode\BlastCaster\BcAddBlastPageHelper' );

		$json_file = file_get_contents( 'tests/fixtures/sample-invalid.json', true );
		$_POST['pageData'] = $json_file;

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_helper->expects( $this->once() )
			->method( 'render' )
			->with(
				$this->isInstanceOf( '\Terescode\BlastCaster\BcAddBlastPage' ),
				'admin/views/add-blast-page',
				'edit_posts'
			);

		// @exercise
		$view = new BcAddBlastPage( $m_helper, $m_page_helper );
		$view->render();
	}

	/**
	 * Test is_metabox_page
	 */
	function test_is_metabox_page_should_return_false() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_page_helper = $this->mock( 'Terescode\BlastCaster\BcAddBlastPageHelper' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );

		// @exercise
		$view = new BcAddBlastPage( $m_helper, $m_page_helper );
		$this->assertFalse( $view->is_metabox_page() );
	}

	/**
	 * Test add blast boxes
	 */
	function test_add_meta_boxes_should_do_nothing() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_page_helper = $this->mock( 'Terescode\BlastCaster\BcAddBlastPageHelper' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );

		// @exercise
		$view = new BcAddBlastPage( $m_helper, $m_page_helper );
		$view->add_meta_boxes();
	}
}
