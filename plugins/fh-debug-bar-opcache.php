<?php
/*
Plugin Name: Debug Bar OPcache
Plugin URI: https://github.com/fhoech/fh-wordpress-addons/blob/master/plugins/fh-debug-bar-opcache.php
Description: Show OPcache information in Debug Bar.
Author: Florian Höch
Version: 0.1
Author URI: https://hoech.net/
*/

function fh_add_debug_bar_opcache_panel( $panels ) {
	if ( ! class_exists( 'FH_Debug_Bar_OPcache' ) &&
		 class_exists( 'Debug_Bar_Panel' ) ) {

		class FH_Debug_Bar_OPcache extends Debug_Bar_Panel {

			/**
			 * Give the panel a title.
			 *
			 * @return void
			 */
			public function init() {
				$this->title( __( 'OPcache', 'debug-bar' ) );
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
				if ( function_exists('opcache_get_configuration') &&
					   function_exists('opcache_get_status') ) {
					echo '<div class="qm-boxed">';
					foreach ( array( 'Configuration' => opcache_get_configuration(),
									 'Status' => opcache_get_status( false ) ) as $title => $entries ) {
						echo '<div class="qm-section">';
						echo "<h3>OPcache $title</h3>";
						echo '<table><tbody>';
						foreach ( $entries as $key_1 => $val_1 ) {
							if ( is_scalar( $val_1 ) )
								echo '<tr><th>' . esc_html( $key_1 ) . '</th><td>' . esc_html( trim( var_export( $val_1, true ), "'" ) ) . '</td></tr>';
							else {
								if ( $key_1 !== 'directives' ) {
									switch ( $key_1 ) {
										case 'memory_usage':
											$key_title = 'Memory Usage';
											break;
										case 'interned_strings_usage':
											$key_title = 'Interned Strings Usage';
											break;
										case 'opcache_statistics':
											$key_title = 'Statistics';
											break;
										default:
											$key_title = ucfirst( $key_1 );
									}
									echo '</table></tbody>';
									echo '<h4>' . esc_html( $key_title ) . '</h4>';
									echo '<table><tbody>';
								}
								foreach ( $val_1 as $key_2 => $val_2 ) {
									echo '<tr><th>' . esc_html( $key_2 ) . '</th><td>' . esc_html( trim( var_export( $val_2, true ), "'" ) ) . '</td></tr>';
								}
							}
						}
						echo '</table></tbody>';
						echo '</div>';
					}
					echo '</div>';
				}
				else
					echo '<p><span class="qm-info">OPcache information is not available</span></p>';
			}

		}

		$panels[] = new FH_Debug_Bar_OPcache();
	}
	return $panels;
}

add_filter( 'debug_bar_panels', 'fh_add_debug_bar_opcache_panel' );
