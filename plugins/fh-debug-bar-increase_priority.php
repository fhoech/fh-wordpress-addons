<?php
/*
Plugin Name: Debug Bar Increase Priority
Plugin URI: https://github.com/fhoech/fh-wordpress-addons/blob/master/plugins/fh-debug-bar-increase_priority.php
Description: Increase Debug Bar priority so it loads later and catches more.
Author: Florian Höch
Version: 0.1
Author URI: https://hoech.net/
*/

function fh_debug_bar_increase_priority() {
	// Remove filter that disables Debug Bar
	remove_filter( 'debug_bar_enable', '__return_false' );

	if ( isset( $GLOBALS['debug_bar'] ) ) {
		$bar = $GLOBALS['debug_bar'] ;
		
		// Do manual equivalent of Debug_Bar->init()
		if ( ! $bar->enable_debug_bar() ) {
			return;
		}

		load_plugin_textdomain( 'debug-bar' );

		add_action( 'wp_before_admin_bar_render',   array( $bar, 'wp_before_admin_bar_render' ), 1000000 );
		add_action( 'admin_footer',     array( $bar, 'render' ), 99999 );
		add_action( 'wp_footer',        array( $bar, 'render' ), 99999 );
		add_action( 'wp_head',          array( $bar, 'ensure_ajaxurl' ), 1 );
		add_filter( 'body_class',       array( $bar, 'body_class' ) );
		add_filter( 'admin_body_class', array( $bar, 'body_class' ) );

		$bar->requirements();
		$bar->enqueue();
		$bar->init_panels();
	}
}

add_action( 'admin_bar_init', 'fh_debug_bar_increase_priority', 99999 );

// Disable Debug Bar by returning false
add_filter( 'debug_bar_enable', '__return_false' );
