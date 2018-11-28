<?php

class FH_Debug_Bar_Doing_It_Wrong_Collector {

	public $_doing_it_wrong = array();
	private $stop = array( '_doing_it_wrong',
						   '_wp_scripts_maybe_doing_it_wrong',
						   '_deprecated_function',
						   '_deprecated_constructor',
						   '_deprecated_file',
						   '_deprecated_argument',
						   '_deprecated_hook' );

	public function __construct() {
		add_action( 'doing_it_wrong_run', array( &$this, 'doing_it_wrong' ), 0, 3 );
		add_filter( 'doing_it_wrong_trigger_error', '__return_false', 0 );
		add_action( 'deprecated_function_run', array( &$this, 'deprecated_function' ), 0, 3 );
		add_filter( 'deprecated_function_trigger_error', '__return_false', 0 );
		add_action( 'deprecated_constructor_run', array( &$this, 'deprecated_constructor' ), 0, 3 );
		add_filter( 'deprecated_constructor_trigger_error', '__return_false', 0 );
		add_action( 'deprecated_file_included', array( &$this, 'deprecated_file_included' ), 0, 4 );
		add_filter( 'deprecated_file_trigger_error', '__return_false', 0 );
		add_action( 'deprecated_argument_run', array( &$this, 'deprecated_argument' ), 0, 3 );
		add_filter( 'deprecated_argument_trigger_error', '__return_false', 0 );
		add_action( 'deprecated_hook_run', array( &$this, 'deprecated_hook' ), 0, 4 );
		add_filter( 'deprecated_hook_trigger_error', '__return_false', 0 );
	}

	public function doing_it_wrong( $function, $message, $version ) {
		if ( WP_DEBUG ) {
			if ( function_exists( '__' ) ) {
				if ( is_null( $version ) ) {
					$version = '';
				} else {
					/* translators: %s: version number */
					$version = sprintf( __( '(This message was added in version %s.)' ), $version );
				}
				/* translators: %s: Codex URL */
				$message .= ' ' . sprintf( __( 'Please see <a href="%s">Debugging in WordPress</a> for more information.' ),
					__( 'https://codex.wordpress.org/Debugging_in_WordPress' )
				);
				/* translators: Developer debugging message. 1: PHP function name, 2: Explanatory message, 3: Version information message */
				$message = sprintf( __( '%1$s was called <strong>incorrectly</strong>. %2$s %3$s' ), $function, $message, $version );
			} else {
				if ( is_null( $version ) ) {
					$version = '';
				} else {
					$version = sprintf( '(This message was added in version %s.)', $version );
				}
				$message .= sprintf( ' Please see <a href="%s">Debugging in WordPress</a> for more information.',
					'https://codex.wordpress.org/Debugging_in_WordPress'
				);
				$message = sprintf( '%1$s was called <strong>incorrectly</strong>. %2$s %3$s', $function, $message, $version );
			}
			$this->collect( $message );
		}
	}

	public function deprecated_function( $function, $replacement = null, $version ) {
		if ( WP_DEBUG ) {
			if ( function_exists( '__' ) ) {
				if ( ! is_null( $replacement ) ) {
					/* translators: 1: PHP function name, 2: version number, 3: alternative function name */
					$message = sprintf( __('%1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.'), $function, $version, $replacement );
				} else {
					/* translators: 1: PHP function name, 2: version number */
					$message = sprintf( __('%1$s is <strong>deprecated</strong> since version %2$s with no alternative available.'), $function, $version );
				}
			} else {
				if ( ! is_null( $replacement ) ) {
					$message = sprintf( '%1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.', $function, $version, $replacement );
				} else {
					$message = sprintf( '%1$s is <strong>deprecated</strong> since version %2$s with no alternative available.', $function, $version );
				}
			}
			$this->collect( $message );
		}
	}

	public function deprecated_constructor( $class, $version, $parent_class = '' ) {
		if ( WP_DEBUG ) {
			if ( function_exists( '__' ) ) {
				if ( ! empty( $parent_class ) ) {
					/* translators: 1: PHP class name, 2: PHP parent class name, 3: version number, 4: __construct() method */
					$message = sprintf( __( 'The called constructor method for %1$s in %2$s is <strong>deprecated</strong> since version %3$s! Use %4$s instead.' ),
						$class, $parent_class, $version, '<pre>__construct()</pre>' );
				} else {
					/* translators: 1: PHP class name, 2: version number, 3: __construct() method */
					$message = sprintf( __( 'The called constructor method for %1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.' ),
						$class, $version, '<pre>__construct()</pre>' );
				}
			} else {
				if ( ! empty( $parent_class ) ) {
					$message = sprintf( 'The called constructor method for %1$s in %2$s is <strong>deprecated</strong> since version %3$s! Use %4$s instead.',
						$class, $parent_class, $version, '<pre>__construct()</pre>' );
				} else {
					$message = sprintf( 'The called constructor method for %1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.',
						$class, $version, '<pre>__construct()</pre>' );
				}
			}
			$this->collect( $message );
		}
	}

	public function deprecated_file_included( $file, $replacement = null, $version, $message = '' ) {
		if ( WP_DEBUG ) {
			$message = empty( $message ) ? '' : ' ' . $message;
			if ( function_exists( '__' ) ) {
				if ( ! is_null( $replacement ) ) {
					/* translators: 1: PHP file name, 2: version number, 3: alternative file name */
					$message = sprintf( __('%1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.'), $file, $version, $replacement ) . $message;
				} else {
					/* translators: 1: PHP file name, 2: version number */
					$message = sprintf( __('%1$s is <strong>deprecated</strong> since version %2$s with no alternative available.'), $file, $version ) . $message;
				}
			} else {
				if ( ! is_null( $replacement ) ) {
					$message = sprintf( '%1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.', $file, $version, $replacement ) . $message;
				} else {
					$message = sprintf( '%1$s is <strong>deprecated</strong> since version %2$s with no alternative available.', $file, $version ) . $message;
				}
			}
			$this->collect( $message );
		}
	}

	public function deprecated_argument( $function, $message = null, $version ) {
		if ( WP_DEBUG ) {
			if ( function_exists( '__' ) ) {
				if ( ! is_null( $message ) ) {
					/* translators: 1: PHP function name, 2: version number, 3: optional message regarding the change */
					$message = sprintf( __('%1$s was called with an argument that is <strong>deprecated</strong> since version %2$s! %3$s'), $function, $version, $message );
				} else {
					/* translators: 1: PHP function name, 2: version number */
					$message = sprintf( __('%1$s was called with an argument that is <strong>deprecated</strong> since version %2$s with no alternative available.'), $function, $version );
				}
			} else {
				if ( ! is_null( $message ) ) {
					$message = sprintf( '%1$s was called with an argument that is <strong>deprecated</strong> since version %2$s! %3$s', $function, $version, $message );
				} else {
					$message = sprintf( '%1$s was called with an argument that is <strong>deprecated</strong> since version %2$s with no alternative available.', $function, $version );
				}
			}
			$this->collect( $message );
		}
	}

	public function deprecated_hook( $hook, $replacement = null, $version, $message = null ) {
		if ( WP_DEBUG ) {
			$message = empty( $message ) ? '' : ' ' . $message;
			if ( ! is_null( $replacement ) ) {
				/* translators: 1: WordPress hook name, 2: version number, 3: alternative hook name */
				$message = sprintf( __( '%1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.' ), $hook, $version, $replacement ) . $message;
			} else {
				/* translators: 1: WordPress hook name, 2: version number */
				$message = sprintf( __( '%1$s is <strong>deprecated</strong> since version %2$s with no alternative available.' ), $hook, $version ) . $message;
			}
			$this->collect( $message );
		}
	}

	private function collect( $message ) {
		$id = uniqid( 'debug-backtrace' );
		$backtrace = $this->get_backtrace();
		$message .= ' Backtrace (most recent call last): <a href="javascript:void(0)" onclick="jQuery(&quot;#' . $id . '&quot;).toggle()">Toggle Details</a><br /><span id="' . $id . '" style="display: none">' . implode( "<br />\n", array_slice( $backtrace, 0, -1 ) ) . "<br />\n</span>" . end( $backtrace );
		$this->_doing_it_wrong[] = $message;
		file_put_contents( ABSPATH . '.wordpress-doing-it-wrong.log', '[' . date( 'Y-m-d H:i:s T' ) . '] ' . strip_tags( $message ) . "\n", FILE_APPEND );
	}

	private function get_backtrace( $include_args = false ) {
		$backtrace = array_reverse( debug_backtrace() );
		$sequence = array();
		foreach ( $backtrace as $i => $call ) {
			if ( in_array( $call['function'], $this->stop ) ) break;
			if ( isset( $call['class'] ) ) {
				$args = '';
			} elseif ( in_array( $call['function'], array( 'do_action', 'apply_filters' ) ) ) {
                $args = "('{$call['args'][0]}')";
            } elseif ( in_array( $call['function'], array( 'include', 'include_once', 'require', 'require_once' ) ) ) {
				$args = "('" . str_replace( array( WP_CONTENT_DIR, ABSPATH ) , '', $call['args'][0] ) . "')";
			} else $args = '';
			$sequence[] = '#' . $i . ' ' . str_replace( ABSPATH, '', $call['file'] ) . '(' . $call['line'] . '): ' .
						  ( isset( $call['class'] ) ? $call['class'] . $call['type'] : '' ) .
						  $call['function'] . $args;
		}
		return $sequence;
	}

}

$fh_doing_it_wrong_collector = new FH_Debug_Bar_Doing_It_Wrong_Collector;
