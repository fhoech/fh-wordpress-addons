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

require( dirname(__FILE__) . '/includes/hyper-cache-utility.php' );

$debug = is_file(dirname(__FILE__) . '/DEBUG');
$delete = HyperCacheUtility :: get($_GET['delete']);
$hcutil = new HyperCacheUtility($debug);
try {
	$hcutil -> process($delete, false);
	$hcutil -> send_headers();
}
catch (Exception $e) {
	echo nl2br(esc_html(strip_tags($e)));
}

?>
