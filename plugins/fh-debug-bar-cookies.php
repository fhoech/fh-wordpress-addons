<?php
/*
Plugin Name: Debug Bar Cookies
Plugin URI: https://github.com/fhoech/fh-wordpress-addons/blob/master/plugins/fh-debug-bar-cookies.php
Description: Show HTTP cookie information in Debug Bar.
Author: Florian Höch
Version: 0.1
Author URI: https://hoech.net/
*/

function fh_add_debug_bar_cookies_panel( $panels ) {
	if ( ! class_exists( 'FH_Debug_Bar_Cookies' ) &&
		 class_exists( 'Debug_Bar_Panel' ) ) {

		class FH_Debug_Bar_Cookies extends Debug_Bar_Panel {

			/**
			 * Give the panel a title.
			 *
			 * @return void
			 */
			public function init() {
				$this->title( __( 'Cookies', 'debug-bar' ) );
			}

			/**
			 * Show the menu item in Debug Bar.
			 *
			 * @return  void
			 */
			public function prerender() {
				$this->set_visible( true );
			}

			/**
			 * Show the contents of the page.

			 * @return  void
			 */
			public function render() {
				echo '<table><thead><tr><th>Name</th><th>Value</th></tr></thead><tbody>';
				foreach ( $_COOKIE as $name => $contents ) {
					echo '<tr><td>' . esc_html( $name ) . '</td><td>' . esc_html( $contents ) . "</td></tr>\n";
				}
				echo '</tbody></table>';
			}

		}

		$panels[] = new FH_Debug_Bar_Cookies();
	}
	return $panels;
}

add_filter( 'debug_bar_panels', 'fh_add_debug_bar_cookies_panel' );
