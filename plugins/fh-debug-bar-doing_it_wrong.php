<?php
/*
Plugin Name: Debug Bar 'Doing It Wrong'
Plugin URI: https://github.com/fhoech/fh-wordpress-addons/blob/master/plugins/fh-debug-bar-doing_it_wrong.php
Description: Collects all 'Doing It Wrong' messages in Debug Bar.
Author: Florian Höch
Version: 0.1
Author URI: https://hoech.net/
*/

function fh_add_debug_bar_doing_it_wrong_panel( $panels ) {
	if ( ! class_exists( 'FH_Debug_Bar_Doing_It_Wrong' ) &&
		 class_exists( 'Debug_Bar_Panel' ) ) {

		class FH_Debug_Bar_Doing_It_Wrong extends Debug_Bar_Panel {

			/**
			 * Give the panel a title.
			 *
			 * @return void
			 */
			public function init() {
				$this->title( __( 'Doing It Wrong', 'debug-bar' ) );
				if ( defined( 'WP_DEBUGBAR_DOING_IT_WRONG_TEST' ) ) $this->doing_it_wrong_test();
			}

			/**
			 * Show the menu item in Debug Bar.
			 *
			 * @return  void
			 */
			public function prerender() {
				global $fh_doing_it_wrong_collector;
				$this->set_visible( isset( $fh_doing_it_wrong_collector ) &&
									count( $fh_doing_it_wrong_collector->_doing_it_wrong ) );
			}

			/**
			 * Show the contents of the page.

			 * @return  void
			 */
			public function render() {
				global $fh_doing_it_wrong_collector;
				if ( isset( $fh_doing_it_wrong_collector ) ) {
					echo '<ol>';
					foreach ( $fh_doing_it_wrong_collector->_doing_it_wrong as $message )
						echo '<li>' . $message . '</li>';
					echo '</ol>';
				}
			}

			private function doing_it_wrong_test() {
				_doing_it_wrong( '<strong>Test:</strong> You do <strong>not</strong> need to worry that this function actually', 'This is just a test so you know the “Doing It Wrong” Debug Bar add-on is working.', '0.1' );
			}

		}

		$panels[] = new FH_Debug_Bar_Doing_It_Wrong();
	}
	return $panels;
}

add_filter( 'debug_bar_panels', 'fh_add_debug_bar_doing_it_wrong_panel', 0 );
