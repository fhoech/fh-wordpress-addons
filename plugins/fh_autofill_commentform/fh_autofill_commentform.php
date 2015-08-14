<?php
/*
Plugin name: FH Autofill Comment Form
Plugin URI: https://github.com/fhoech/fh-wordpress-addons/blob/master/plugins/fh_autofill_commentform/fh_autofill_commentform.php
Description: Wait... doesn't WordPress do this automatically? Well yes, <em>unless</em> you use a caching plugin and have enabled caching for commenters, in which case caching plugins disable WordPress' own autofill feature to prevent caching of user information (for example the email address). This plugin works around that limitation by filling the comment form with JavaScript. For that purpose, a small script (including &lt;script&gt; tags only 513 bytes) is inserted into the footer of posts and pages if they are open for comments and if the user is not logged in.
Version: $Id:$
Author: Florian Höch
Author URI: http://hoech.net
License: GPL3
*/


if (!function_exists('add_action')) exit;

add_action( 'wp_footer', 'fh_autofill_commentform' );

function fh_autofill_commentform() {
	if ( is_singular() && comments_open() && !is_user_logged_in() ) {
		@include ( dirname( __FILE__ ) . '/fh_autofill_commentform.min.js.php' );
	}
}

?>
