<?php

namespace Terescode\WordPress;

require_once 'includes/constants.php';
require_once 'includes/class-wp-helper.php';
require_once 'includes/class-plugin-helper.php';
require_once 'includes/validate/class-capability-validator.php';

/**
 * Class TcStringValidatorTest
 *
 * @package Blastcaster
 */

class TcCapabilityValidatorTest extends \BcPhpUnitTestCase {
	function test_validate_should_return_code_given_current_user_can_returns_false() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$data_map = [];

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_wph->method( 'current_user_can' )
			->willReturn( false );

		// @exercise
		$validator = new TcCapabilityValidator( $m_helper, 'edit_posts', -1 );
		$ret = $validator->validate( $data_map );
		$this->assertEquals( -1, $ret );
	}

	function test_validate_should_return_null_given_current_user_can_returns_true() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$data_map = [];

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_wph->method( 'current_user_can' )
			->willReturn( true );

		// @exercise
		$validator = new TcCapabilityValidator( $m_helper, 'edit_posts', -1 );
		$ret = $validator->validate( $data_map );
		$this->assertNull( $ret );
	}
}
