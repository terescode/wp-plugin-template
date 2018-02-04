<?php

namespace Terescode\BlastCaster;

require_once 'includes/constants.php';
require_once 'includes/class-image-type.php';

/**
 * Class BcBlastTest
 *
 * @package Blastcaster
 */

class BcImageTypesTest extends \BcPhpUnitTestCase {
	function test_as_type_returns_null_given_invalid_type() {
		// @exercise
		$type = BcImageType::as_type( 'not_a_type' );
		$this->assertNull( $type );
	}

	function test_as_type_returns_type_instance_given_valid_type() {
		// @exercise
		$type = BcImageType::as_type( BcImageType::BC_IMAGE_TYPE_NONE );
		$this->assertTrue( $type->equals( BcImageType::BC_IMAGE_TYPE_NONE ) );
	}

	function test_as_type_returns_type_instance_given_valid_type_string() {
		// @exercise
		$type = BcImageType::as_type( 'url' );
		$this->assertTrue( $type->equals( BcImageType::BC_IMAGE_TYPE_URL ) );
	}
}
