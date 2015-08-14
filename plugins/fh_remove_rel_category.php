<?php
/*
Plugin name: FH Remove rel="category"
Plugin URI: https://github.com/fhoech/fh-wordpress-addons/blob/master/plugins/fh_remove_rel_category.php
Description: Remove rel="category" from category HTML
Version: $Id:$
Author: Florian Höch
Author URI: http://hoech.net
License: GPL3
*/

add_filter( 'the_category', 'fh_remove_rel_category' );

function fh_remove_rel_category( $html ) {
	return str_replace('rel="category ', 'rel="', $html);
}

?>
