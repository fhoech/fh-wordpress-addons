<?php
/*
Plugin Name: Hyper Cache Utility
Plugin URI: http://hoech.net
Description: Hyper Cache Utility is an add-on for the Hyper Cache plugin. It allows viewing of the cache files metadata and clearing of cache files.
Version: 0.1
Text Domain: hyper-cache-utility
Author: Florian Höch
Author URI: http://hoech.net
Disclaimer: Use at your own risk. No warranty expressed or implied is provided.

Copyright (c) 2013 Florian Höch
 
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/


if (!function_exists('add_action')) exit;

add_action('admin_menu', 'hyper_cache_utility_admin_menu');

function hyper_cache_utility_admin_menu() {
	$hook = add_management_page( 'Hyper Cache Utility', 'Hyper Cache Utility', 'manage_options', __FILE__, 'hyper_cache_utility_management_page' );
	add_action( 'load-' . $hook, 'hyper_cache_utility_enqueue_scripts_styles' );  // only load the scripts and stylesheets by hook, if this admin page will be shown
}

function hyper_cache_utility_enqueue( $what, $handle, $relative_src, $deps=array(), $in_footer=false ) {
	// By default add file modification time as version parameter
	$ver = filemtime(dirname(__FILE__) . '/' . $relative_src);
	// Get URL of src
	$src = plugins_url( $relative_src , __FILE__ );
	// Enqueue
	if ( $what == 'style' ) wp_enqueue_style( $handle, $src, $deps, $ver );
	else wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
}

function hyper_cache_utility_enqueue_scripts_styles() {
	wp_enqueue_style( 'hyper-cache-utility-fonts', '//fonts.googleapis.com/css?family=Raleway:300,500,700', array(), null );
	wp_enqueue_style( 'hyper-cache-utility-iconfonts', '//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css', array(), null );
	hyper_cache_utility_enqueue( 'style', 'hyper-cache-utility-styles', 'includes/css/hyper-cache-utility.css', array('hyper-cache-utility-fonts', 'hyper-cache-utility-iconfonts') );
	hyper_cache_utility_enqueue( 'style', 'hyper-cache-utility-styles-dynamic', 'includes/css/hyper-cache-utility.css.php', array('hyper-cache-utility-styles') );
	hyper_cache_utility_enqueue( 'style', 'pism', 'includes/css/prism.css' );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'prefixfree', '//cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js', array(), null, false );
	wp_enqueue_script( 'jquery.tablesorter', '//cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.10.8/jquery.tablesorter.min.js', array('jquery'), null, true );
	hyper_cache_utility_enqueue( 'script', 'jquery.tablesorter.pager', 'includes/js/jquery.tablesorter.pager.min.js', array('jquery.tablesorter'), true );
	wp_enqueue_script( 'jquery.tablesorter.widgets', '//cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.10.8/jquery.tablesorter.widgets.min.js', array('jquery.tablesorter'), null, true );
	hyper_cache_utility_enqueue( 'script', 'hyper-cache-utility-config', 'includes/js/hyper-cache-utility-config.js.php', array(), true );
	wp_enqueue_script( 'jquery.history', '//cdnjs.cloudflare.com/ajax/libs/history.js/1.8/bundled/html4+html5/jquery.history.min.js', array('jquery'), null, false );
	hyper_cache_utility_enqueue( 'script', 'prism', 'includes/js/prism.js', array(), true );
	hyper_cache_utility_enqueue( 'script', 'hyper-cache-utility', 'includes/js/hyper-cache-utility.js', array('jquery.tablesorter.pager', 'jquery.tablesorter.widgets', 'hyper-cache-utility-config', 'jquery.history', 'prism'), true );
}

function hyper_cache_utility_management_page() {
    load_plugin_textdomain( 'hyper-cache-utility', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	require( dirname( __FILE__ ) . '/includes/hyper-cache-utility.php' );
	$debug = is_file(dirname(__FILE__) . '/DEBUG');
	$delete = HyperCacheUtility :: get($_GET['delete']);
	$view = HyperCacheUtility :: get($_GET['view']);
	$hcutil = new HyperCacheUtility($debug);
	try {
		$hcutil -> process($delete, !$view);
		if ($view) $hcutil -> view($view);
		else $hcutil -> output();
	}
	catch (Exception $e) {
		echo nl2br(esc_html(strip_tags($e)));
	}
}

?>
