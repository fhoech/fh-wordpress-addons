<?php
/*
Plugin name: FH Custom DIV
Plugin URI: https://github.com/fhoech/fh-wordpress-addons/blob/master/plugins/fh_custom_div.php
Description: Use shortcodes in the form [div class="css classes"] ... [/div] and have them automatically replaced by &lt;div&gt; ... &lt;/div&gt;
Version: $Id:$
Author: Florian Höch
Author URI: http://hoech.net
License: GPL3
*/

function fh_custom_div($atts, $content=null) {
	extract(shortcode_atts(array(
		'class' => 'custom'
	), $atts));
	return '<div class="' . $class . '">' . do_shortcode($content) . '</div>';
}
add_shortcode('div', 'fh_custom_div');

?>
