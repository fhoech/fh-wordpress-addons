<?php

// We're being called directly, try to load WordPress
function hyper_cache_utility_get_wp_load_path() {
	$wp_root_dir = realpath( dirname(__FILE__) . '/../../..' );
	$wp_load = $wp_root_dir . '/wp-load.php';
	if (!is_file($wp_load)) die('Fatal error: Could not load WordPress: File does not exist ' . $wp_load);
	return $wp_load;
}

require( hyper_cache_utility_get_wp_load_path() );

if (!current_user_can( 'manage_options' )) die('You are not allowed to view this page.');

do_action('wp');  // Polylang: load_textdomains

load_plugin_textdomain( 'hyper-cache-utility', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

require( dirname(__FILE__) . '/includes/hyper-cache-utility.php' );

$debug = is_file(dirname(__FILE__) . '/DEBUG');
$delete = HyperCacheUtility :: get($_GET['delete']);
$view = HyperCacheUtility :: get($_GET['view']);
$hcutil = new HyperCacheUtility($debug);
try {
	$hcutil -> process($delete, !$delete && !$view);
	if ($view) $hcutil -> view($view);
	else if (!$delete) $hcutil -> output();
	else $hcutil -> send_headers($delete);
}
catch (Exception $e) {
	echo nl2br(esc_html(strip_tags($e)));
}

?>
