<?php

error_reporting(E_ALL | E_STRICT);

global $hyper_cache_stop;

$hyper_cache_stop = true;

define('XHR', !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

if (!function_exists('hyper_cache_utility_management_page')) exit;

if (!function_exists('hyper_cache_gzdecode')) {
	function hyper_cache_gzdecode ($data) {

		$flags = ord(substr($data, 3, 1));
		$headerlen = 10;
		$extralen = 0;

		$filenamelen = 0;
		if ($flags & 4) {
			$extralen = unpack('v' ,substr($data, 10, 2));

			$extralen = $extralen[1];
			$headerlen += 2 + $extralen;
		}
		if ($flags & 8) // Filename

			$headerlen = strpos($data, chr(0), $headerlen) + 1;
		if ($flags & 16) // Comment

			$headerlen = strpos($data, chr(0), $headerlen) + 1;
		if ($flags & 2) // CRC at end of file

			$headerlen += 2;
		$unpacked = gzinflate(substr($data, $headerlen));
		return $unpacked;
	}
}

function get(&$what) {
	if (!empty($what)) return $what;
}

function ellipsis($str, $maxlen) {
	if (strlen($str) > $maxlen) return substr($str, 0, $maxlen) . '…';
	return $str;
}

if (!XHR) {

?>
	<!--[if gte IE 9 ]><section class="gte-ie9" id="hyper-cache-utility"><![endif]-->
	<!--[if lt IE 9]><!--><section id="hyper-cache-utility"><!--<![endif]-->
		<header>
			<h1><?php _e('Hyper Cache Utility', 'hyper-cache-utility'); ?></h1>
		</header>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $_SERVER['QUERY_STRING']; ?>" method="post">
<?php ob_start(); ?>
			<table>
				<thead>
					<tr>
						<th class="status"><div title="<?php _e('Sort by HTTP Status Code', 'hyper-cache-utility'); ?>"><span><?php _e('Status', 'hyper-cache-utility'); ?></span></div></th>
						<th class="uri cache-filename"><div title="<?php _e('Sort by URI', 'hyper-cache-utility'); ?>"><span><?php _e('URI &amp; Cache Filename', 'hyper-cache-utility'); ?></span></div></th>
						<th class="cache-filedate"><div title="<?php _e('Sort by Date', 'hyper-cache-utility'); ?>"><span><?php _e('Date', 'hyper-cache-utility'); ?></span></div></th>
						<th class="cache-filesize"><div title="<?php _e('Sort by Size', 'hyper-cache-utility'); ?>"><span><?php _e('Size', 'hyper-cache-utility'); ?></span></div></th>
						<th class="type"><div title="<?php _e('Sort by Type', 'hyper-cache-utility'); ?>"><span><?php _e('Type', 'hyper-cache-utility'); ?></span></div></th>
						<th class="content-type"><div title="<?php _e('Sort by Content-Type', 'hyper-cache-utility'); ?>"><span><?php _e('Content-Type', 'hyper-cache-utility'); ?></span></div></th>
						<th class="compression"><div title="<?php _e('Sort by Compression', 'hyper-cache-utility'); ?>"><span><?php _e('Compression', 'hyper-cache-utility'); ?></span></div></th>
						<th class="user-agent"><div title="<?php _e('Sort by HTTP User Agent', 'hyper-cache-utility'); ?>"><span><?php _e('User Agent', 'hyper-cache-utility'); ?></span></div></th>
						<th class="options"></th>
					</tr>
				</thead>
				<tbody>
<?php

}

$advanced_cache = WP_CONTENT_DIR . '/advanced-cache.php';
if (is_file($advanced_cache)) {
	$contents = file_get_contents($advanced_cache);
	if (preg_match('/\$hyper_cache_path\s*=\s*(["\'])(.+?)\1/', $contents, $match)) {
		$hyper_cache_path = $match[2];
	}
	if (preg_match('/\$hyper_cache_timeout\s*=\s*(\d+(?:.\d+)?)/', $contents, $match)) {
		$hyper_cache_timeout = floatval($match[1]);
	}
};

if (!isset($hyper_cache_path)) $hyper_cache_path = WP_CONTENT_DIR . '/cache/hyper-cache/';
if (!isset($hyper_cache_timeout)) $hyper_cache_timeout = 2000000000;

$files = glob($hyper_cache_path . '*.dat');
if ($files === false) $files = array();
$last_deleted_hash = '';
$deleted = 0;
$special = array('_archives', '_global');
$expired = 0;
$status301 = 0;
$status404 = 0;
$uris = array();
$hc_invalidation_global_time = !empty($_POST['delete']) && $_POST['delete'] == '_global' ? 0 : @filemtime($hyper_cache_path . '_global.dat');
$hc_invalidation_archives_time =  !empty($_POST['delete']) && $_POST['delete'] == '_archives' ? 0 : @filemtime($hyper_cache_path . '_archives.dat');
$time = time();

foreach ($files as $f) {
	$filename = pathinfo($f, PATHINFO_FILENAME);
	$data = @unserialize(file_get_contents($f));
	$hc_file_time = filemtime($f);
	$hc_file_age = $time - $hc_file_time;
	$is_expired = $hc_file_age > $hyper_cache_timeout ||
		($hc_invalidation_global_time && $hc_file_time < $hc_invalidation_global_time) ||
		(isset($data['type']) && ($data['type'] == 'blog' || $data['type'] == 'home' || $data['type'] == 'archive' || $data['type'] == 'feed') &&
		 $hc_invalidation_archives_time && $hc_file_time < $hc_invalidation_archives_time);
	if (!empty($_POST['delete']) &&
		($_POST['delete'] == 'all' ||
		 $_POST['delete'] == $filename ||
		 ($_POST['delete'] == 404 && get($data['status']) == 404) ||
		 ($_POST['delete'] == 'expired' && $is_expired && !in_array($filename, $special)))) {
		unlink($f);
		$last_deleted_hash = $filename;
		$deleted ++;
	}
	else {
		if (!isset($data['status']) && !in_array($filename, $special)) $data['status'] = empty($data['location']) ? 200 : 301;
		if (get($data['status']) == 301) $status301 ++;
		else if (get($data['status']) == 404) $status404 ++;
		if (isset($data['type'])) $data['type'] = str_replace('single', 'post', $data['type']);
		$rowclasses = array();
		$rowclasses[] = 'status-' . (isset($data['status']) ? $data['status'] : 'not-applicable');
		$rowclasses[] = 'type-' . (isset($data['type']) ? $data['type'] : 'not-applicable');
		$rowclasses[] = 'mime-type-' . (isset($data['mime']) ? str_replace('/', '-', preg_replace('/;.*$/', '', $data['mime'])) : 'not-applicable');
		$rowclasses[] = 'compression-' . (isset($data['gz']) ? 'gz' : (isset($data['html']) ? 'none' : 'not-applicable'));
		if ($is_expired && !in_array($filename, $special)) {
			$expired ++;
			$rowclasses[] = 'expired';
		}

		if (XHR) continue;
?>
					<tr class="<?php echo implode(' ', $rowclasses); ?>" id="hash-<?php echo $filename; ?>">
						<td class="status<?php if (!isset($data['status'])) echo ' not-applicable'; ?>"><?php echo isset($data['status']) ? '<abbr title="' . sprintf(__('HTTP Status Code %u %s', 'hyper-cache-utility'), $data['status'], get_status_header_desc($data['status'])) . ($is_expired ? ' ' . __('(Expired)', 'hyper-cache-utility') : '') . '"><span>' . $data['status'] . '</span></abbr>' : '<!-- N/A -->'; ?></td>
						<td class="uri cache-filename">
<?php if (!empty($data['uri'])) { ?>
							<a href="//<?php echo htmlspecialchars(get($data['host']) . $data['uri'], ENT_COMPAT, 'UTF-8'); ?>" title="<?php echo htmlspecialchars(get($data['host']) . $data['uri'], ENT_COMPAT, 'UTF-8'); ?>"><span><?php echo htmlspecialchars(get($data['host']) . $data['uri'], ENT_COMPAT, 'UTF-8'); ?></span></a>
<?php } ?>
<?php if (!empty($data['location'])) { ?>
							→ <a href="<?php echo htmlspecialchars($data['location'], ENT_COMPAT, 'UTF-8'); ?>" title="<?php echo htmlspecialchars(preg_replace('/^\w+:\/\//', '', $data['location']), ENT_COMPAT, 'UTF-8'); ?>"><?php echo htmlspecialchars(preg_replace('/^\w+:\/\//', '', $data['location']), ENT_COMPAT, 'UTF-8'); ?></a>
<?php } ?>
<?php if (!empty($data['uri']) || !empty($data['location'])) echo '<br />'; ?>

							<?php echo basename($f); ?>

						</td>
						<td class="cache-filedate"><time datetime="<?php echo date('c', $hc_file_time); ?>"><?php echo strftime('%Y-%m-%d %H:%M:%S', $hc_file_time); ?></time></td>
						<td class="cache-filesize"><?php echo number_format_i18n(filesize($f) / 1024, 2); ?> KiB</td>
						<td class="type<?php if (!isset($data['type'])) echo ' not-applicable'; ?>"><?php echo isset($data['type']) ? __(ucfirst($data['type']), 'hyper-cache-utility') : '<!-- N/A -->'; ?></td>
						<td class="mime-type<?php if (!isset($data['mime'])) echo ' not-applicable'; ?>">
							<?php echo isset($data['mime']) ? preg_replace('/;.*$/', '', $data['mime']) . ' ' . preg_replace('/^.*;\s*charset=/', '', $data['mime']) : '<!-- N/A -->'; ?></td>
						<td class="compression<?php if (!isset($data['gz']) && !isset($data['html'])) echo ' not-applicable'; ?>"><?php echo isset($data['gz']) ? __('GZIP', 'hyper-cache-utility') : (isset($data['html']) ? __('None') : '<!-- N/A -->'); ?></td>
						<td class="user-agent<?php if (!isset($data['user_agent'])) echo ' not-applicable'; ?>"><?php echo isset($data['user_agent']) ? preg_replace('/(\w+:\/\/[\w$%&\/=?@+~#.:-_]+)/', '<a href="$1" target="_blank">$1</a>', htmlspecialchars(get($data['user_agent']), ENT_COMPAT, 'UTF-8')) : '<!-- N/A -->'; ?></td>
						<td class="options"><button type="submit" name="delete" value="<?php echo $filename; ?>"><?php _e('Delete', 'hyper-cache-utility'); ?></button></td>
					</tr>
<?php

		if (isset($data['uri'])) {
			if (empty($uris[$data['uri']])) $uris[$data['uri']] = array();
			$uris[$data['uri']][basename($f)] = $data;
		}
	}
}

if (!XHR) {

?>
				</tbody>
			</table>
<?php

	$table = ob_get_contents();
	ob_end_clean();

}

header('X-HyperCache-Count: ' . (count($files) - $deleted));
header('X-HyperCache-Expired-Count: ' . $expired);
header('X-HyperCache-Status-301-Count: ' . $status301);
header('X-HyperCache-Status-404-Count: ' . $status404);
	
if ($deleted > 0) {
	header('X-HyperCache-Deleted: ' . ($deleted == count($files) ? 'all' : ($deleted > 1 ? (get($_POST['delete']) == 'expired' ? 'expired' : 'status=404') : 'hash=' . $last_deleted_hash)));
	if (!XHR) {

?>
			<p><?php printf(_n('One file deleted.', '%u files deleted.', $deleted, 'hyper-cache-utility'), $deleted); ?></p>
<?php

	}
}

if (!XHR) {

?>
			<p class="info"><?php echo __('Files in cache (valid and expired)', 'hyper-cache-utility') . ': <span class="count">' . (count($files) - $deleted) . '</span><br />(' . sprintf(__('Expired: <span class="expired-count">%u</span>, Not Found: <span class="status-404-count">%u</span>, Moved Permanently: <span class="status-301-count">%u</span>', 'hyper-cache-utility'), $expired, $status404, $status301) . ')'; ?></p>

			<p class="options">
<?php

	if ($expired > 0) {

?>
				<button class="delete-expired" type="submit" name="delete" value="expired"><?php _e('Delete expired', 'hyper-cache-utility'); ?></button>
<?php

	}

	if ($status404 > 0) {

?>
				<button class="delete-status-404" type="submit" name="delete" value="404"><?php _e('Delete all with status 404', 'hyper-cache-utility'); ?></button>
<?php

	}

	if (count($files) - $deleted > 0) {

?>
				<button class="delete-all" type="submit" name="delete" value="all"><?php _e('Delete all', 'hyper-cache-utility'); ?></button>
<?php

	}

?>
			</p>
<?php

			if (count($files) - $deleted > 0) echo $table;

	foreach ($uris as $uri => $a) {
		if (count($a) > 1) {
			$keys = array_keys($a);
			$data1 = array_shift($a);
			$html1 = explode("\n", hyper_cache_gzdecode($data1['gz']));
			$data2 = array_pop($a);
			$html2 = explode("\n", hyper_cache_gzdecode($data2['gz']));
			echo '		<div>--- ' . $keys[0] . ': ' . get($data1['host']) . $uri . "</div>\n";
			echo '		<div>+++ ' . $keys[1] . ': ' . get($data2['host']) . $uri . "</div>\n";
			foreach ($html1 as $lineno => $line) {
				if ($html2[$lineno] != $line) {
					echo '		<div>- ' . htmlspecialchars($line, ENT_COMPAT, 'UTF-8') . "</div>\n";
					echo '		<div>+ ' . htmlspecialchars($html2[$lineno], ENT_COMPAT, 'UTF-8') . "</div>\n";
				}
			}
		}
	}

?>
		</form>
		<script>
			var hyper_cache_timeout = parseFloat('<?php echo $hyper_cache_timeout; ?>'),
				hc_invalidation_global_time = parseInt('<?php echo $hc_invalidation_global_time ?>'),
				hc_invalidation_archives_time = parseInt('<?php echo $hc_invalidation_archives_time ?>'),
				time = <?php echo $time; ?>;
		</script>
	</section>
<?php

}

?>
