<?php
/*
Plugin Name: Dark Mode (Stub)
Plugin URI: https://github.com/fhoech/fh-wordpress-addons/blob/master/plugins/darkmode_stub.php
Description: Stub for enabling dark mode in Query Monitor between 22 pm and 6 am
Author: Florian Höch
Version: 0.1
Author URI: https://hoech.net/
*/

if ( ! class_exists( 'Dark_Mode' ) && ( intval( date( 'H' ) ) >= 22 || intval( date( 'H' ) ) <= 6 ) ) {

	class Dark_Mode {

		public static function is_using_dark_mode( $user_id = 0 ) {
			return true;
		}

	}

}

?>
