<?php

header('Content-Type: application/javascript; charset=UTF-8');

/** Sets up the WordPress Environment. */
require( dirname(__FILE__) . '/../../../../../wp-load.php' );

load_plugin_textdomain( 'hyper-cache-utility', false, dirname( plugin_basename( realpath(dirname(__FILE__) . '/../../hyper-cache-utility.php') ) ) . '/languages/' );

do_action('wp');  // Polylang: load_textdomains

$locale = get_locale();

?>
var hyper_cache_utility = {
	ajax_uri: '<?php echo plugins_url( 'ajax.php' , dirname( dirname(__FILE__) ) ); ?>',
	usNumberFormat: <?php echo strtolower(substr($locale, 0, 2)) == 'en' ? 'true' : 'false';  // All 'en' locales share the 'en_US' decimal point/thousands separator format, see http://lh.2xlibre.net/locales/ ?>,  /* <?php echo $locale; ?> */
	pagerOutput: '<?php _e('{page} / {filteredPages} (Entries {startRow} - {endRow} / {filteredRows})', 'hyper-cache-utility'); ?>',
	resetText: '<?php _e('Reset', 'hyper-cache-utility'); ?>'
};
