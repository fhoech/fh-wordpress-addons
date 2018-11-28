<?php
/*
Plugin Name: Debug Bar Remove WP Query Panel
Plugin URI: https://github.com/fhoech/fh-wordpress-addons/blob/master/plugins/fh-debug-bar-remove_wp_query_panel.php
Description: Remove Debug Bar WP Query Panel.
Author: Florian Höch
Version: 0.1
Author URI: https://hoech.net/
*/

function fh_debug_bar_remove_wp_query_panel( $panels_in ) {
	$panels_out = array();
	foreach ( $panels_in as $panel ) if ( ! ( $panel instanceof Debug_Bar_WP_Query ) ) $panels_out[] = $panel;
	return $panels_out;
}

add_filter( 'debug_bar_panels', 'fh_debug_bar_remove_wp_query_panel', 10, 1 );
