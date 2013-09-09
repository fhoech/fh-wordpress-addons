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

function hyper_cache_utility_enqueue_scripts_styles() {
	wp_enqueue_style( 'hyper-cache-utility-fonts', 'http://fonts.googleapis.com/css?family=Raleway:300,500,700' );
	wp_enqueue_style( 'hyper-cache-utility-iconfonts', 'http://weloveiconfonts.com/api/?family=fontawesome' );
	wp_enqueue_style( 'hyper-cache-utility-styles', plugins_url( 'includes/css/hyper-cache-utility.css' , __FILE__ ), array('hyper-cache-utility-fonts', 'hyper-cache-utility-iconfonts') );
	wp_enqueue_style( 'hyper-cache-utility-styles-dynamic', plugins_url( 'includes/css/hyper-cache-utility.css.php' , __FILE__ ), array('hyper-cache-utility-styles') );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'prefixfree', '//cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js', array(), false, false );
	wp_enqueue_script( 'jquery.form', '//cdnjs.cloudflare.com/ajax/libs/jquery.form/3.32/jquery.form.min.js', array('jquery'), false, true );
	wp_enqueue_script( 'jquery.tablesorter', '//cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.10.8/jquery.tablesorter.min.js', array('jquery'), false, true );
	wp_enqueue_script( 'jquery.tablesorter.widgets', '//cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.10.8/jquery.tablesorter.widgets.min.js', array('jquery.tablesorter'), false, true );
	wp_enqueue_script( 'hyper-cache-utility-js', plugins_url( 'includes/js/hyper-cache-utility.js' , __FILE__ ), array('jquery.form', 'jquery.tablesorter'), false, true );
}

function hyper_cache_utility_management_page() {
    load_plugin_textdomain( 'hyper-cache-utility', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	include( dirname( __FILE__ ) . '/includes/hyper-cache-utility.php' );
}

?>
