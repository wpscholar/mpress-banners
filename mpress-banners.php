<?php

/**
 * Plugin Name: mPress Banners
 * Description: Easily manage pop-up banners on your site.
 * Author: Micah Wood
 * Author URI: https://wpscholar.com
 * Version: 1.0
 * License: GPL3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Copyright 2013-2016 by Micah Wood - All rights reserved.
 */

if ( ! defined( 'MPRESS_BANNERS_VERSION' ) ) {
	define( 'MPRESS_BANNERS_VERSION', '1.0' );
}

if ( version_compare( PHP_VERSION, '5.2', '<' ) ) {
	if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		deactivate_plugins( __FILE__ );
		wp_die( printf( __(
			'%s requires PHP version 5.2 or later. You are currently running version %s.
			This plugin has now disabled itself.
			Please contact your web host regarding upgrading your PHP version.', 'mpress-banners'
		), __( 'mPress Banners', 'mpress-banners' ), PHP_VERSION ) );
	}
}

spl_autoload_register( 'mpress_banner_autoload' );

function mpress_banner_autoload( $class ) {
	$class = preg_replace( '/_/', '-', strtolower( $class ) );
	$file = dirname( __FILE__ ) . '/classes/' . $class . '.class.php';
	if ( file_exists( $file ) ) {
		include( $file );
	}
}

register_activation_hook( __FILE__, array( 'mPress_Banners_Init', 'register_post_types' ) );
add_action( 'plugins_loaded', array( 'mPress_Banners_Init', 'get_instance' ) );

add_filter( 'widget_text', 'do_shortcode' );