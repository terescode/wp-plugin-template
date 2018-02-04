<?php

namespace Terescode\WordPress;

// @SUT
require_once 'includes/class-callback-wrapper.php';

/**
 * Class TcPluginHelperTest
 *
 * @package Blastcaster
 */

class TcCallbackWrapperTest extends \BcPhpUnitTestCase {

	function user_func_1() {
		$this->assertEquals( 0, func_num_args() );
	}

	function user_func_2( $val ) {
		return $val;
	}

	function user_func_3( $count ) {
		$this->assertEquals( $count, func_num_args() - 1 );
		for ( $index = 0; $index < $count; $index += 1 ) {
			$this->assertEquals( $index, func_get_arg( $index + 1 ) );
		}
	}

	function user_func_exc() {
		throw new \Exception( 'mangatsika' );
	}

	/**
	 * Test wrapper invokes function with no arguments
	 */
	function test_wrapper_should_invoke_function_with_no_args_given_no_args() {
		// @exercise
		$wrapper = new TcCallbackWrapper( array( $this, 'user_func_1' ) );
		$return = $wrapper->call();

		// @verify
		$this->assertNull( $return );
	}

	/**
	 * Test wrapper invokes function with no arguments
	 */
	function test_wrapper_should_invoke_function_with_one_arg_given_one_arg() {
		// @exercise
		$wrapper = new TcCallbackWrapper( array( $this, 'user_func_2' ), 'beluga' );
		$return = $wrapper->call();

		// @verify
		$this->assertEquals( 'beluga', $return );
	}

	/**
	 * Test wrapper invokes function with N arguments
	 */
	function test_wrapper_should_invoke_function_with_N_args_given_N_args() {
		$this->invoke_with_random_count( 5, 10, function ( $count ) {
			// @setup
			$args = array(
				array( $this, 'user_func_3' ),
				$count,
			);
			for ( $index = 0; $index < $count; $index += 1 ) {
				$args[] = $index;
			}
			// @exercise
			$reflect  = new \ReflectionClass( __NAMESPACE__ . '\TcCallbackWrapper' );
			$wrapper = $reflect->newInstanceArgs( $args );
			$wrapper->call();
		});
	}

	/**
	 * Test wrapper fails with invalid function
	 */
	function test_wrapper_should_return_false_given_invalid_function() {
		// @exercise
		$wrapper = new TcCallbackWrapper( array( $this, 'i_dont_exist' ) );
		$return = $wrapper->call();

		// @verify
		$this->assertFalse( $return );
	}

	/**
	 * Test wrapper throws exception if function does
	 */
	function test_wrapper_should_throw_exception_given_callable_does() {
		// @exercise
		$wrapper = new TcCallbackWrapper( array( $this, 'user_func_exc' ) );
		try {
			$return = $wrapper->call();
			$this->assertTrue( false );
		} catch ( \Exception $e ) {
			$this->assertNotNull( $e );
			$this->assertEquals( 'mangatsika', $e->getMessage() );
		}
	}
}
