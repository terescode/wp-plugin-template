<?php

namespace WordPress\Plugins\Plugin;

class Plugin {
	/**
	 * WordPress helper.
	 *
	 * @var \WordPress\Plugins\WordPressOop
	 */
	private $wpOop;
	
	public function __construct(
		WordPressOop $wpOop
	) {
		$this->$wpOop = $wpOop;
	}
	
	public function init() {
		
	}
}