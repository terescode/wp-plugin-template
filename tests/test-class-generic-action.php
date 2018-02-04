<?php

namespace Terescode\WordPress;

require_once 'includes/constants.php';
require_once 'includes/class-wp-helper.php';
require_once 'includes/class-plugin-helper.php';
require_once 'includes/class-generic-action.php';
require_once 'includes/interface-input-validator.php';
require_once 'includes/interface-action-handler.php';

use Terescode\WordPress\TcInputValidator;

class InputValidatorStub implements TcInputValidator {
	private $values;

	function __construct( $values ) {
		$this->values = $values;
	}

	function validate( &$map ) {
		foreach ( $this->values as $key => $value ) {
			$map[ $key ] = $value;
		}
	}
}

class ActionHandlerStub implements TcActionHandler {
	private $count;
	public $err;

	function __construct( $count ) {
		$this->count = $count;
		$this->err = null;
	}

	function handle_error( $err ) {
		$this->err = $err;
	}

	function handle( $data ) {
		for ( $i = 0; $i < $this->count; $i += 1 ) {
			if ( ! isset( $data[ 'foo' . $i ] ) ) {
				return 'error';
			}
			if ( 'bar' . $i !== $data[ 'foo' . $i ] ) {
				return 'error';
			}
		}
	}
}

/**
 * Class TcGenericActionTest
 *
 * @package Blastcaster
 */

class TcGenericActionTest extends \BcPhpUnitTestCase {

	function test_get_name_should_return_value_given_in_constructor() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );

		// @exercise
		$action = new TcGenericAction( $m_helper, 'my_action', array(), null );
		$ret = $action->get_name();
		$this->assertEquals( 'my_action', $ret );
	}

	function test_do_action_should_call_1_input_validator_and_return_given_validator_returns_non_null() {
		// @setup
		$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
		$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
		$m_validator = $this->mock( 'Terescode\WordPress\TcInputValidator' );
		$m_handler = new ActionHandlerStub( 0 );

		$m_helper->method( 'get_wp_helper' )
			->willReturn( $m_wph );
		$m_validator->method( 'validate' )
			->willReturn( 'a.error.code' );

		// @exercise
		$action = new TcGenericAction( $m_helper, 'my_action', array( $m_validator ), $m_handler );
		$action->do_action();
		$this->assertEquals( 'a.error.code', $m_handler->err );
	}

	/**
	 * Test register_handlers
	 */
	function test_do_action_should_call_N_input_validator_and_return_given_validator_returns_non_null() {
		$this->invoke_with_random_count( 5, 5, function ( $count ) {
			// @setup
			$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
			$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
			$m_handler = new ActionHandlerStub( 0 );

			$which = rand( 0, $count - 1 );
			$m_validators = array();
			for ( $i = 0; $i < $count; $i += 1 ) {
				$m_validator = $this->mock( 'Terescode\WordPress\TcInputValidator' );
				if ( $which === $i ) {
					$m_validator->method( 'validate' )
						->willReturn( 'error.code' . $which );
				}
				$m_validators[] = $m_validator;
			}

			$m_helper->method( 'get_wp_helper' )
				->willReturn( $m_wph );

			// @exercise
			$action = new TcGenericAction( $m_helper, 'my_action', $m_validators, $m_handler );
			$action->do_action();
			$this->assertEquals( 'error.code' . $which, $m_handler->err );
		});
	}

	/**
	 * Test register_handlers
	 */
	function test_do_action_should_call_N_input_validator_and_handler_and_return_non_null_given_all_validators_succeed_and_handler_returns_non_null() {
		$this->invoke_with_random_count( 5, 5, function ( $count ) {
			// @setup
			$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
			$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
			$m_handler = $this->mock( 'Terescode\WordPress\TcActionHandler' );
			$m_validators = array();
			for ( $i = 0; $i < $count; $i += 1 ) {
				$m_validator = $this->mock( 'Terescode\WordPress\TcInputValidator' );
				$m_validators[] = $m_validator;
			}

			$m_helper->method( 'get_wp_helper' )
				->willReturn( $m_wph );
			$m_handler->expects( $this->once() )
				->method( 'handle' )
				->willReturn( 'a.error.code' . $count );
			$m_handler->expects( $this->once() )
				->method( 'handle_error' )
				->with( 'a.error.code' . $count );

			// @exercise
			$action = new TcGenericAction( $m_helper, 'my_action', $m_validators, $m_handler );
			$action->do_action();
		});
	}

	/**
	 * Test register_handlers
	 */
	function test_do_action_should_call_N_input_validator_and_handler_given_all_validators_succeed() {
		$this->invoke_with_random_count( 5, 5, function ( $count ) {
			// @setup
			$m_wph = $this->mock( 'Terescode\WordPress\TcWpHelper' );
			$m_helper = $this->mock( 'Terescode\WordPress\TcPluginHelper' );
			$m_handler = new ActionHandlerStub( $count );
			$m_validators = array();
			for ( $i = 0; $i < $count; $i += 1 ) {
				$m_validator = new InputValidatorStub(
					array(
						'foo' . $i => 'bar' . $i,
					)
				);
				$m_validators[] = $m_validator;
			}

			$m_helper->method( 'get_wp_helper' )
				->willReturn( $m_wph );

			// @exercise
			$action = new TcGenericAction( $m_helper, 'my_action', $m_validators, $m_handler );
			$action->do_action();
			$this->assertNull( $m_handler->err );
		});
	}
}
