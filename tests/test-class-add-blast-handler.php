<?php

namespace Terescode\BlastCaster;

require_once 'includes/constants.php';
require_once 'includes/class-wp-helper.php';
require_once 'includes/class-plugin-helper.php';
require_once 'includes/class-blast-dao.php';
require_once 'includes/class-media-loader.php';
require_once 'admin/class-add-blast-handler.php';

/**
 * Class BcAddBlastHandlerTest
 *
 * @package Blastcaster
 */

class BcAddBlastHandlerTest extends \BcPhpUnitTestCase {
	/**
	 * Test handle_error
	 */
	function test_handle_error_should_add_admin_notice_with_error_given_err_is_not_null() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_loader = $this->mock( 'Terescode\BlastCaster\BcMediaLoader' );
		$m_dao = $this->mock( 'Terescode\BlastCaster\BcBlastDao' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_helper->method( 'string' )
			->with( 'a.error.code' )
			->willReturn( 'a string' );
		$m_helper->expects( $this->once() )
			->method( 'add_admin_notice' )
			->with( 'a string' );

		// @exercise
		$controller = new BcAddBlastHandler( $m_helper, $m_loader, $m_dao );
		$controller->handle_error( 'a.error.code' );
	}

	/**
	 * Test handle
	 */
	function test_handle_calls_create_post_with_required_data_given_required_data() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_loader = $this->mock( 'Terescode\BlastCaster\BcMediaLoader' );
		$m_dao = $this->mock( 'Terescode\BlastCaster\BcBlastDao' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_dao->expects( $this->once() )
			->method( 'create_post' )
			->with(
				$this->callback( function ( $subject ) {
					$cats = $subject->get_categories();
					$tags = $subject->get_tags();
					$link = $subject->get_link();
					$link_text = $subject->get_link_text();
					$link_intro = $subject->get_link_intro();
					return $subject instanceof \Terescode\BlastCaster\BcBlast &&
						'An Article Title' === $subject->get_title() &&
						'This is some description of the article.' === $subject->get_description() &&
						null === $subject->get_image_data() &&
						is_array( $cats ) &&
						0 === count( $cats ) &&
						is_array( $tags ) &&
						0 === count( $tags ) &&
						null === $link &&
						null === $link_text &&
						null === $link_intro;
				} )
			);
		$m_helper->expects( $this->once() )
			->method( 'add_admin_notice_raw' )
			->with(
				$this->isType( 'string' ),
				\Terescode\WordPress\TcPluginHelper::NOTICE_TYPE_UPDATED,
				true
			);

		// @exercise
		$controller = new BcAddBlastHandler( $m_helper, $m_loader, $m_dao );
		$controller->handle( [
			'bc-add-title' => 'An Article Title',
			'bc-add-desc' => 'This is some description of the article.',
		] );
	}

	/**
	 * Test handle
	 */
	function test_handle_calls_create_post_with_image_data_given_image_data() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_loader = $this->mock( 'Terescode\BlastCaster\BcMediaLoader' );
		$m_dao = $this->mock( 'Terescode\BlastCaster\BcBlastDao' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_loader->method( 'load_media' )
			->willReturn(
				[
					'file' => 'image.png',
					'url' => 'http://www.terescode.com/path/to/image.png',
					'type' => 'image/png',
				]
			);
		$m_dao->expects( $this->once() )
			->method( 'create_post' )
			->with(
				$this->callback( function ( $subject ) {
					$image_data = $subject->get_image_data();
					return $subject instanceof \Terescode\BlastCaster\BcBlast &&
						null !== $image_data &&
						3 === count( $image_data ) &&
						'image.png' === $image_data['file'] &&
						'http://www.terescode.com/path/to/image.png' === $image_data['url'] &&
						'image/png' === $image_data['type'];
				} )
			);
		$m_helper->expects( $this->once() )
			->method( 'add_admin_notice_raw' )
			->with(
				$this->isType( 'string' ),
				\Terescode\WordPress\TcPluginHelper::NOTICE_TYPE_UPDATED,
				true
			);

		// @exercise
		$controller = new BcAddBlastHandler( $m_helper, $m_loader, $m_dao );
		$controller->handle( [
			'bc-add-title' => 'An Article Title',
			'bc-add-desc' => 'This is some description of the article.',
			'image' => 'yada yada',
		] );
	}

	/**
	 * Test handle
	 */
	function test_handle_calls_create_post_with_categories_given_categories() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_loader = $this->mock( 'Terescode\BlastCaster\BcMediaLoader' );
		$m_dao = $this->mock( 'Terescode\BlastCaster\BcBlastDao' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_dao->expects( $this->once() )
			->method( 'create_post' )
			->with(
				$this->callback( function ( $subject ) {
					$cats = $subject->get_categories();
					return $subject instanceof \Terescode\BlastCaster\BcBlast &&
						is_array( $cats ) &&
						2 === count( $cats ) &&
						1 === $cats[0] && 26 === $cats[1];
				} )
			);
		$m_helper->expects( $this->once() )
			->method( 'add_admin_notice_raw' )
			->with(
				$this->isType( 'string' ),
				\Terescode\WordPress\TcPluginHelper::NOTICE_TYPE_UPDATED,
				true
			);

		// @exercise
		$controller = new BcAddBlastHandler( $m_helper, $m_loader, $m_dao );
		$controller->handle( [
			'bc-add-title' => 'An Article Title',
			'bc-add-desc' => 'This is some description of the article.',
			'image' => 'yada yada',
			'bc-add-cat' => [ 1, 26 ],
		] );
	}

	/**
	 * Test handle
	 */
	function test_handle_calls_create_post_with_tags_given_tags() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_loader = $this->mock( 'Terescode\BlastCaster\BcMediaLoader' );
		$m_dao = $this->mock( 'Terescode\BlastCaster\BcBlastDao' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_dao->expects( $this->once() )
			->method( 'create_post' )
			->with(
				$this->callback( function ( $subject ) {
					$tags = $subject->get_tags();
					return $subject instanceof \Terescode\BlastCaster\BcBlast &&
						is_array( $tags ) &&
						3 === count( $tags ) &&
						23 === $tags[0] && 'bar' === $tags[1] && 123 === $tags[2];
				} )
			);
		$m_helper->expects( $this->once() )
			->method( 'add_admin_notice_raw' )
			->with(
				$this->isType( 'string' ),
				\Terescode\WordPress\TcPluginHelper::NOTICE_TYPE_UPDATED,
				true
			);

		// @exercise
		$controller = new BcAddBlastHandler( $m_helper, $m_loader, $m_dao );
		$controller->handle( [
			'bc-add-title' => 'An Article Title',
			'bc-add-desc' => 'This is some description of the article.',
			'image' => 'yada yada',
			'bc-add-cat' => [ 1, 26 ],
			'bc-add-tag' => [ 23, 'bar', 123 ],
		] );
	}

	/**
	 * Test handle
	 */
	function test_handle_calls_create_post_with_link_given_link() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_loader = $this->mock( 'Terescode\BlastCaster\BcMediaLoader' );
		$m_dao = $this->mock( 'Terescode\BlastCaster\BcBlastDao' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_dao->expects( $this->once() )
			->method( 'create_post' )
			->with(
				$this->callback( function ( $subject ) {
					$url = $subject->get_link();
					return $subject instanceof \Terescode\BlastCaster\BcBlast &&
						null !== $url &&
						'http://www.terescode.com/about' === $url;
				} )
			);
		$m_helper->expects( $this->once() )
			->method( 'add_admin_notice_raw' )
			->with(
				$this->isType( 'string' ),
				\Terescode\WordPress\TcPluginHelper::NOTICE_TYPE_UPDATED,
				true
			);

		// @exercise
		$controller = new BcAddBlastHandler( $m_helper, $m_loader, $m_dao );
		$controller->handle( [
			'bc-add-title' => 'An Article Title',
			'bc-add-desc' => 'This is some description of the article.',
			'image' => 'yada yada',
			'bc-add-cat' => [ 1, 26 ],
			'bc-add-tag' => [ 23, 'bar', 123 ],
			'bc-add-link' => 'http://www.terescode.com/about',
		] );
	}

	/**
	 * Test handle
	 */
	function test_handle_calls_create_post_with_link_text_given_link_text() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_loader = $this->mock( 'Terescode\BlastCaster\BcMediaLoader' );
		$m_dao = $this->mock( 'Terescode\BlastCaster\BcBlastDao' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_dao->expects( $this->once() )
			->method( 'create_post' )
			->with(
				$this->callback( function ( $subject ) {
					$link_text = $subject->get_link_text();
					return $subject instanceof \Terescode\BlastCaster\BcBlast &&
						null !== $link_text &&
						'Click here' === $link_text;
				} )
			);
		$m_helper->expects( $this->once() )
			->method( 'add_admin_notice_raw' )
			->with(
				$this->isType( 'string' ),
				\Terescode\WordPress\TcPluginHelper::NOTICE_TYPE_UPDATED,
				true
			);

		// @exercise
		$controller = new BcAddBlastHandler( $m_helper, $m_loader, $m_dao );
		$controller->handle( [
			'bc-add-title' => 'An Article Title',
			'bc-add-desc' => 'This is some description of the article.',
			'image' => 'yada yada',
			'bc-add-cat' => [ 1, 26 ],
			'bc-add-tag' => [ 23, 'bar', 123 ],
			'bc-add-link' => 'http://www.terescode.com/about',
			'bc-add-link-text' => 'Click here',
		] );
	}

	/**
	 * Test handle
	 */
	function test_handle_calls_create_post_with_link_intro_given_link_intro() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_loader = $this->mock( 'Terescode\BlastCaster\BcMediaLoader' );
		$m_dao = $this->mock( 'Terescode\BlastCaster\BcBlastDao' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_dao->expects( $this->once() )
			->method( 'create_post' )
			->with(
				$this->callback( function ( $subject ) {
					$link_intro = $subject->get_link_intro();
					return $subject instanceof \Terescode\BlastCaster\BcBlast &&
						null !== $link_intro &&
						'Read this awesome article here' === $link_intro;
				} )
			);
		$m_helper->expects( $this->once() )
			->method( 'add_admin_notice_raw' )
			->with(
				$this->isType( 'string' ),
				\Terescode\WordPress\TcPluginHelper::NOTICE_TYPE_UPDATED,
				true
			);

		// @exercise
		$controller = new BcAddBlastHandler( $m_helper, $m_loader, $m_dao );
		$controller->handle( [
			'bc-add-title' => 'An Article Title',
			'bc-add-desc' => 'This is some description of the article.',
			'image' => 'yada yada',
			'bc-add-cat' => [ 1, 26 ],
			'bc-add-tag' => [ 23, 'bar', 123 ],
			'bc-add-link' => 'http://www.terescode.com/about',
			'bc-add-link-text' => 'Click here',
			'bc-add-link-intro' => 'Read this awesome article here',
		] );
	}

	/**
	 * Test handle
	 */
	function test_handle_returns_error_given_media_loader_returns_error() {
		// @setup
		$m_error = $this->mock( 'WP_Error' );
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_loader = $this->mock( 'Terescode\BlastCaster\BcMediaLoader' );
		$m_dao = $this->mock( 'Terescode\BlastCaster\BcBlastDao' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_loader->method( 'load_media' )
			->will(	$this->onConsecutiveCalls(
				[
					'error' => 'Could not load image!',
				],
				$m_error
			));
		$m_wph->method( 'is_wp_error' )
			->will( $this->onConsecutiveCalls(
				false,
				true
			));
		$m_dao->expects( $this->never() )
			->method( 'create_post' );

		// @exercise
		$controller = new BcAddBlastHandler( $m_helper, $m_loader, $m_dao );
		$ret = $controller->handle( [
			'bc-add-title' => 'An Article Title',
			'bc-add-desc' => 'This is some description of the article.',
			'image' => 'yada yada',
		] );
		$this->assertEquals( \Terescode\BlastCaster\BcStrings::ABF_BUILD_ACTION_DATA_FAILED, $ret );
		$ret = $controller->handle( [
			'bc-add-title' => 'An Article Title',
			'bc-add-desc' => 'This is some description of the article.',
			'image' => 'yada yada',
		] );
		$this->assertEquals( \Terescode\BlastCaster\BcStrings::ABF_BUILD_ACTION_DATA_FAILED, $ret );
	}

	/**
	 * Test handle
	 */
	function test_handle_returns_error_given_create_post_returns_error() {
		// @setup
		$m_error = $this->mock( 'WP_Error' );
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_loader = $this->mock( 'Terescode\BlastCaster\BcMediaLoader' );
		$m_dao = $this->mock( 'Terescode\BlastCaster\BcBlastDao' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_wph->method( 'is_wp_error' )
			->willReturn( true );
		$m_dao->method( 'create_post' )
			->willReturn( $m_error );

		// @exercise
		$controller = new BcAddBlastHandler( $m_helper, $m_loader, $m_dao );
		$ret = $controller->handle( [
			'bc-add-title' => 'An Article Title',
			'bc-add-desc' => 'This is some description of the article.',
		] );
		$this->assertEquals( \Terescode\BlastCaster\BcStrings::ABF_INSERT_POST_FAILED, $ret );
	}
}
