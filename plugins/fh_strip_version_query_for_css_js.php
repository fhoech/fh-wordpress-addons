<?php
/*
Plugin name: FH Strip Version Query String for Stylesheets/Scripts
*/

add_filter( 'script_loader_src', 'fh_strip_query_version' );
add_filter( 'style_loader_src', 'fh_strip_query_version' );

function fh_strip_query_version($src) {
    return preg_replace('/[?&]ver=.+$/', '', $src);
}

?>