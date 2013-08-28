<?php
/*
Plugin name: FH Add Slug Body Class
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
