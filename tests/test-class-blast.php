<?php

namespace Terescode\BlastCaster;

require_once 'includes/constants.php';
require_once 'includes/class-blast.php';

/**
 * Class BcBlastTest
 *
 * @package Blastcaster
 */

class BcBlastTest extends \BcPhpUnitTestCase {

	function test_construct_should_set_properties_given_required_arguments() {
		// @exercise
		$blast = new BcBlast( 'TDD is fun', 'Article excerpt about TDD' );

		// @verify
		$this->assertEquals( 'TDD is fun', $blast->get_title() );
		$this->assertEquals( 'Article excerpt about TDD', $blast->get_description() );
		$this->assertNull( $blast->get_image_data() );
		$this->assertCount( 0, $blast->get_categories() );
		$this->assertCount( 0, $blast->get_tags() );
		$this->assertNull( $blast->get_link() );
		$this->assertNull( $blast->get_link_text() );
		$this->assertNull( $blast->get_link_intro() );
	}

	function test_construct_should_set_properties_given_optional_arguments() {
		// @exercise
		$blast = new BcBlast(
			'TDD is fun',
			'Article excerpt about TDD',
			[
				'file' => 'image.png',
				'url' => 'http://www.terescode.com/path/to/image.png',
				'type' => 'image/png',
			],
			[ 567 ],
			[ 'TDD', 'test', 4, 'agile' ],
			'http://www.terescode.com',
			'Click here',
			'Checkout this totally whacky article, here!'
		);

		// @verify
		$this->assertEquals( 'TDD is fun', $blast->get_title() );
		$this->assertEquals( 'Article excerpt about TDD', $blast->get_description() );
		$this->assertInternalType( 'array', $blast->get_image_data() );
		$image_data = $blast->get_image_data();
		$this->assertEquals( 3, count( $image_data ) );
		$this->assertEquals( 'http://www.terescode.com/path/to/image.png', $image_data['url'] );
		$this->assertEquals( 'image.png', $image_data['file'] );
		$this->assertEquals( 'image/png', $image_data['type'] );
		$categories = $blast->get_categories();
		$this->assertCount( 1, $categories );
		$this->assertEquals( [ 567 ], $categories );
		$tags = $blast->get_tags();
		$this->assertCount( 4, $tags );
		$this->assertEquals( [ 'TDD', 'test', 4, 'agile' ], $tags );
		$link = $blast->get_link();
		$this->assertEquals( 'http://www.terescode.com', $link );
		$link_text = $blast->get_link_text();
		$this->assertEquals( 'Click here', $link_text );
		$link_intro = $blast->get_link_intro();
		$this->assertEquals( 'Checkout this totally whacky article, here!', $link_intro );
	}

	function test_set_title_should_set_title_given_argument() {
		// @setup
		$blast = new BcBlast( 'TDD is fun', 'Article excerpt about TDD' );

		// @exercise
		$blast->set_title( 'TDD is very fun' );

		// @verify
		$this->assertEquals( 'TDD is very fun', $blast->get_title() );
	}

	function test_set_description_should_set_description_given_argument() {
		// @setup
		$blast = new BcBlast( 'TDD is fun', 'Article excerpt about TDD' );

		// @exercise
		$blast->set_description( 'This article is about TDD' );

		// @verify
		$this->assertEquals( 'This article is about TDD', $blast->get_description() );
	}

	function test_set_image_should_set_image_given_argument() {
		// @setup
		$blast = new BcBlast( 'TDD is fun', 'Article excerpt about TDD' );

		// @exercise
		$blast->set_image_data(
			[
				'file' => 'image.png',
				'url' => 'http://www.terescode.com/path/to/image.png',
				'type' => 'image/png',
			]
		);

		// @verify
		$image_data = $blast->get_image_data();
		$this->assertEquals( 3, count( $image_data ) );
		$this->assertEquals( 'http://www.terescode.com/path/to/image.png', $image_data['url'] );
		$this->assertEquals( 'image.png', $image_data['file'] );
		$this->assertEquals( 'image/png', $image_data['type'] );

	}

	function test_set_categories_should_set_categories_given_argument() {
		// @setup
		$blast = new BcBlast( 'TDD is fun', 'Article excerpt about TDD' );

		// @exercise
		$blast->set_categories( [ 'Software Development' ] );

		// @verify
		$categories = $blast->get_categories();
		$this->assertCount( 1, $categories );
		$this->assertEquals( [ 'Software Development' ], $categories );
	}

	function test_set_tags_should_set_tags_given_argument() {
		// @setup
		$blast = new BcBlast( 'TDD is fun', 'Article excerpt about TDD' );

		// @exercise
		$blast->set_tags( [ 'TDD', 'test', 'development', 'agile' ] );

		// @verify
		$tags = $blast->get_tags();
		$this->assertCount( 4, $tags );
		$this->assertEquals( [ 'TDD', 'test', 'development', 'agile' ], $tags );
	}

	function test_set_link_should_set_link_given_argument() {
		// @setup
		$blast = new BcBlast( 'TDD is fun', 'Article excerpt about TDD' );

		// @exercise
		$blast->set_link( 'http://www.terescode.com/about' );

		// @verify
		$link = $blast->get_link();
		$this->assertEquals( 'http://www.terescode.com/about', $link );
	}

	function test_set_link_text_should_set_link_text_given_argument() {
		// @setup
		$blast = new BcBlast( 'TDD is fun', 'Article excerpt about TDD' );

		// @exercise
		$blast->set_link_text( 'Click here' );

		// @verify
		$link = $blast->get_link_text();
		$this->assertEquals( 'Click here', $link );
	}

	function test_set_link_intro_should_set_link_intro_given_argument() {
		// @setup
		$blast = new BcBlast( 'TDD is fun', 'Article excerpt about TDD' );

		// @exercise
		$blast->set_link_intro( 'Checkout this totally whacky article, here!' );

		// @verify
		$link_intro = $blast->get_link_intro();
		$this->assertEquals( 'Checkout this totally whacky article, here!', $link_intro );
	}
}
