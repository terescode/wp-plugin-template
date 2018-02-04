<?php

namespace WordPress\Plugins;

define( 'BC_PLUGIN_ID', 'blastcaster' );
define( 'BC_PLUGIN_DIR', plugin_dir_path( dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . BC_PLUGIN_ID ) );
define( 'BC_PLUGIN_URL', plugin_dir_url( dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . BC_PLUGIN_ID ) );

if ( ! function_exists( __NAMESPACE__ . '\is_wpinc_defined' ) ) {
	function is_wpinc_defined() {
		return defined( 'WPINC' );
	}
}
