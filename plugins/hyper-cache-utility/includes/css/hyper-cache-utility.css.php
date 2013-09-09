<?php

header('Content-Type: text/css; charset=UTF-8');

/** Sets up the WordPress Environment. */
require( dirname(__FILE__) . '/../../../../../wp-load.php' );

load_plugin_textdomain( 'hyper-cache-utility', false, dirname( plugin_basename( realpath(dirname(__FILE__) . '/../../hyper-cache-utility.php') ) ) . '/languages/' );

do_action('wp');  // Polylang: load_textdomains

?>
#hyper-cache-utility .not-applicable:before{content:'<?php _e('N/A', 'hyper-cache-utility'); ?>';font-family:Raleway,sans-serif;}
