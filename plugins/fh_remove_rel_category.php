<?php
/*
Plugin name: FH Remove rel="category"
*/

add_filter( 'the_category', 'fh_remove_rel_category' );

function fh_remove_rel_category( $html ) {
	return str_replace('rel="category ', 'rel="', $html);
}

?>
