<?php
/*
Plugin name: FH Add Slug Body Class
Plugin URI: https://github.com/fhoech/fh-wordpress-addons/blob/master/plugins/fh_add_slug_body_class.php
Description: Add slug to body HTML 'class' attribute
Version: $Id:$
Author: Florian Höch
Author URI: http://hoech.net
License: GPL3
*/

function fh_add_slug_body_class($classes) {
	global $post;
	if (is_singular() && isset($post)) {
		if (is_page()) {
			if ($post -> post_parent) {
				$parent_post = get_post($post -> post_parent);
				$classes[] = 'parent-' . $parent_post -> post_type . '-' . $parent_post -> post_name;
			}
		}
		$classes[] = $post -> post_type . '-' . $post -> post_name;
	}
	return $classes;
}

add_filter( 'body_class', 'fh_add_slug_body_class' );

?>
