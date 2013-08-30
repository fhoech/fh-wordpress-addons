<?php
/*
Plugin name: FH Add Timestamp to Filename for Stylesheets/Scripts
*/

add_filter( 'script_loader_src', 'fh_add_timestamp', 999 );
add_filter( 'style_loader_src', 'fh_add_timestamp', 999 );

function fh_add_timestamp($src) {
	$path = str_replace(preg_replace('/\/?$/', '/', get_option('siteurl')), ABSPATH, preg_replace('/\?.*$/', '', $src));
	if (is_file($path))
		return preg_replace('/(\.\w+)$/', '.' . filemtime($path) . '$1', $src);
	return $src;
}

?>