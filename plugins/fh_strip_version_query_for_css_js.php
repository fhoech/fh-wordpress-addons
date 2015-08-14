<?php
/*
Plugin name: FH Strip Version Query String for Stylesheets/Scripts
Plugin URI: https://github.com/fhoech/fh-wordpress-addons/blob/master/plugins/fh_strip_version_query_for_css_js.php
Description: Strip version query string for stylesheets and scripts
Version: $Id:$
Author: Florian Höch
Author URI: http://hoech.net
License: GPL3
*/

add_filter( 'script_loader_src', 'fh_strip_query_version' );
add_filter( 'style_loader_src', 'fh_strip_query_version' );

function fh_strip_query_version($src) {
    return preg_replace('/[?&]ver=.+$/', '', $src);
}

?>