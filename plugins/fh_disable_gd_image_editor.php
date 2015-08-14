<?php
/*
Plugin Name: FH Disable GD Image Editor
Plugin URI: https://github.com/fhoech/fh-wordpress-addons/blob/master/plugins/fh_disable_gd_image_editor.php
Description: Disable WordPress' GD Image Editor. GD is a resource hog, so this can help against out of memory errors on limited webhosts. This plugin should only be used if an alternative image editor is available (e.g. ImageMagick).
Version: $Id:$
Author: Florian Höch
Author URI: http://hoech.net
License: GPL3
*/

add_filter('wp_image_editors', 'fh_disable_gd_image_editor', 0);

function fh_disable_gd_image_editor($image_editors) {
	$filtered_editors = array();
	foreach ($image_editors as $editor) if ($editor != 'WP_Image_Editor_GD') $filtered_editors[] = $editor;
	return $filtered_editors;
}

?>