<?php

namespace WordPress\Plugins\Plugin;

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . WPINC . '/class-wp-error.php';

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods, PHPMD.TooManyMethods)
 */
class WordPressOop {

	function add_action( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
		return add_action( $tag, $function_to_add, $priority, $accepted_args );
	}

	function register_activation_hook( $file, $callable ) {
		register_activation_hook( $file, $callable );
	}

	function register_deactivation_hook( $file, $callable ) {
		register_deactivation_hook( $file, $callable );
	}

	/**
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	function load_plugin_textdomain( $domain, $deprecated = false, $plugin_rel_path = false ) {
		return load_plugin_textdomain( $domain, $deprecated, $plugin_rel_path );
	}

	/**
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	function wp_enqueue_script( $handle, $src = false, $deps = array(), $ver = false, $in_footer = false ) {
		wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
	}

	/**
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	function wp_enqueue_style( $handle, $src = false, $deps = array(), $ver = false, $media = 'all' ) {
		wp_enqueue_style( $handle, $src, $deps, $ver, $media );
	}

	function esc_attr( $text ) {
		return esc_attr( $text );
	}

	function esc_html( $text ) {
		return esc_html( $text );
	}

	function esc_url( $url, $protocols = null, $_context = 'display' ) {
		return esc_url( $url, $protocols, $_context );
	}

	function esc_url_raw( $url, $protocols = null ) {
		return esc_url_raw( $url, $protocols );
	}

	function do_action( $tag, $arg = '' ) {
		$count = func_num_args();
		if ( 1 === $count ) {
			do_action( $tag );
			return;
		} elseif ( 2 === $count ) {
			do_action( $tag, $arg );
			return;
		}

		call_user_func_array( 'do_action', func_get_args() );
	}

	function apply_filters( $tag, $value ) {
		$count = func_num_args();
		if ( 2 === $count ) {
			return apply_filters( $tag, $value );
		}
		return call_user_func_array( 'apply_filters', func_get_args() );
	}

	function add_posts_page( $page_title, $menu_title, $capability, $menu_slug, $function = '' ) {
		return add_posts_page( $page_title, $menu_title, $capability, $menu_slug, $function );
	}

	/**
	 * @SuppressWarnings(PHPMD.ShortVariable) as this is a WP name we can't change, not ours
	 */
	function add_meta_box( $id, $title, $callback, $screen = null, $context = 'advanced', $priority = 'default', $callback_args = null ) {
		add_meta_box( $id, $title, $callback, $screen, $context, $priority, $callback_args );
	}

	function current_user_can( $capability, $objid = null ) {
		if ( null === $objid ) {
			return current_user_can( $capability );
		}
		return current_user_can( $capability, $objid );
	}

	function admin_url( $path = '', $scheme = 'admin' ) {
		return admin_url( $path, $scheme );
	}

	/**
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	function wp_nonce_field( $action = -1, $name = '_wpnonce', $referer = true, $echo = true ) {
		return wp_nonce_field( $action, $name, $referer, $echo );
	}

	function wp_create_nonce( $action = -1 ) {
		return wp_create_nonce( $action );
	}

	function do_meta_boxes( $screen, $context, $object ) {
		return do_meta_boxes( $screen, $context, $object );
	}

	/**
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	function submit_button( $text = null, $type = 'primary', $name = 'submit', $wrap = true, $other_attributes = null ) {
		return submit_button( $text, $type, $name, $wrap, $other_attributes );
	}

	/**
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	function wp_insert_post( $postarr, $wp_error = false ) {
		return wp_insert_post( $postarr, $wp_error );
	}

	function sanitize_text_field( $str ) {
		return sanitize_text_field( $str );
	}

	function status_header( $code, $description = '' ) {
		status_header( $code, $description );
	}

	function wp_safe_redirect( $location, $status = 302 ) {
		wp_safe_redirect( $location, $status );
	}

	function wp_verify_nonce( $nonce, $action = -1 ) {
		return wp_verify_nonce( $nonce, $action );
	}

	function download_url( $url, $timeout = 300 ) {
		return download_url( $url, $timeout );
	}

	function is_wp_error( $thing ) {
		return is_wp_error( $thing );
	}

	/**
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	function wp_handle_sideload( $file, $overrides = false, $time = null ) {
		return wp_handle_sideload( $file, $overrides, $time );
	}

	/**
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	function wp_handle_upload( $file, $overrides = false, $time = null ) {
		return wp_handle_upload( $file, $overrides, $time );
	}

	/**
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	function wp_upload_dir( $time = null, $create_dir = true, $refresh_cache = false ) {
		return wp_upload_dir( $time, $create_dir, $refresh_cache );
	}

	/**
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	function wp_insert_attachment( $args, $file = false, $parent = 0, $wp_error = false ) {
		return wp_insert_attachment( $args, $file, $parent, $wp_error );
	}

	function wp_generate_attachment_metadata( $attachment_id, $file ) {
		return wp_generate_attachment_metadata( $attachment_id, $file );
	}

	function wp_update_attachment_metadata( $post_id, $data ) {
		return wp_update_attachment_metadata( $post_id, $data );
	}

	function set_post_thumbnail( $post, $thumbnail_id ) {
		return set_post_thumbnail( $post, $thumbnail_id );
	}

	function get_categories( $args = '' ) {
		return get_categories( $args );
	}

	function get_tags( $args = '' ) {
		return get_tags( $args );
	}

	function absint( $maybeint ) {
		return absint( $maybeint );
	}

	function wp_parse_url( $url, $component = -1 ) {
		return wp_parse_url( $url, $component );
	}

	function wp_check_filetype_and_ext( $file, $filename, $mimes = null ) {
		return wp_check_filetype_and_ext( $file, $filename, $mimes );
	}

	/**
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	function get_permalink( $post, $leavename = false ) {
		return get_permalink( $post, $leavename );
	}
}
