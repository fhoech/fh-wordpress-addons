<?php
/*
Plugin Name: Force Maximize Query Monitor
Plugin URI: https://github.com/fhoech/fh-wordpress-addons/blob/master/plugins/fh_qm_maximize.php
Description: Force maximize the Query Monitor panel when opened and move close button to left. Note the plugin is only active for administrators.
Author: Florian Höch
Version: 0.1
Author URI: https://hoech.net/
*/

function fh_qm_maximize() {
	if ( function_exists( 'current_user_can' ) && current_user_can( 'administrator' ) ) {
		?>
		<style>#query-monitor{height:100% !important;top:32px !important}#query-monitor #qm-title .qm-title-heading{margin-left:32px !important}.qm-button-container-close{position:absolute;top:1px;left:0}#query-monitor #qm-title{cursor:auto !important}</style>
		<?php
	}
}

add_action( 'wp_head', 'fh_qm_maximize', 9999 );
add_action( 'admin_head', 'fh_qm_maximize', 9999 );

?>
