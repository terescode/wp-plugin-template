<?php

require_once 'includes/constants.php';
require_once 'includes/class-blast.php';

use Terescode\BlastCaster\BcBlast;

/**
 * Class WpAdminPluginTest
 *
 * @package Blastcaster
 */

class WpPostTplTest extends \BcPhpUnitTestCase {

	/**
	 * Test the default template
	 */
	public function test_default_template_outputs_description_no_image_description_no_link_given_blast() {
		// @setup
		$blast = new BcBlast( 'Trump wins big', 'In a stunning upset today, Trump will be the next president and will Make America Great Again!' );

		$this->expectOutputRegex( '/<p>In a stunning.+Again[!]<\/p>/s' );

		// @exercise
		include( 'admin/templates/wp-post-tpl.php' );
	}

	/**
	 * Test the default template
	 */
	public function test_default_template_outputs_image_description_no_link_given_blast() {
		// @setup
		$blast = new BcBlast( 'Trump wins big', 'In a stunning upset today, Trump will be the next president and will Make America Great Again!', [ 'url' => 'http://www.terescode.com/favico.ico' ] );

		$this->expectOutputRegex( '/<img src="http:\/\/www\.terescode\.com\/favico\.ico" width="100%" class="aligncenter" \/><p>In a stunning.+Again[!]<\/p>/s' );

		// @exercise
		include( 'admin/templates/wp-post-tpl.php' );
	}

	/**
	 * Test the default template
	 */
	public function test_default_template_outputs_image_description_link_no_link_text_given_blast() {
		// @setup
		$blast = new BcBlast( 'Trump wins big', 'In a stunning upset today, Trump will be the next president and will Make America Great Again!', [ 'url' => 'http://www.terescode.com/favico.ico' ], [], [], 'http://www.terescode.com' );

		$this->expectOutputRegex( '/<img src="http:\/\/www\.terescode\.com\/favico\.ico" width="100%" class="aligncenter" \/><p>In a stunning.+Again[!]<\/p><span[^>]*>Continue reading at:<\/span><strong><a target="_blank" href="http:\/\/www.terescode.com">Trump wins big<\/a><\/strong>/s' );

		// @exercise
		include( 'admin/templates/wp-post-tpl.php' );
	}

	/**
	 * Test the default template
	 */
	public function test_default_template_outputs_image_description_link_link_text_given_blast() {
		// @setup
		$blast = new BcBlast( 'Trump wins big', 'In a stunning upset today, Trump will be the next president and will Make America Great Again!', [ 'url' => 'http://www.terescode.com/favico.ico' ], [], [], 'http://www.terescode.com', 'Click here' );

		$this->expectOutputRegex( '/<img src="http:\/\/www\.terescode\.com\/favico\.ico" width="100%" class="aligncenter" \/><p>In a stunning.+Again[!]<\/p><span[^>]*>Continue reading at:<\/span><strong><a target="_blank" href="http:\/\/www.terescode.com">Click here<\/a><\/strong>/s' );

		// @exercise
		include( 'admin/templates/wp-post-tpl.php' );
	}

	/**
	 * Test the default template
	 */
	public function test_default_template_outputs_image_description_link_link_text_link_intro_given_blast() {
		// @setup
		$blast = new BcBlast( 'Trump wins big', 'In a stunning upset today, Trump will be the next president and will Make America Great Again!', [ 'url' => 'http://www.terescode.com/favico.ico' ], [], [], 'http://www.terescode.com', 'Click here', 'Read more at...' );

		$this->expectOutputRegex( '/<img src="http:\/\/www\.terescode\.com\/favico\.ico" width="100%" class="aligncenter" \/><p>In a stunning.+Again[!]<\/p><span[^>]*>Read more at...<\/span><strong><a target="_blank" href="http:\/\/www.terescode.com">Click here<\/a><\/strong>/s' );

		// @exercise
		include( 'admin/templates/wp-post-tpl.php' );
	}
}
