<?php

error_reporting(E_ALL | E_STRICT);

require dirname(__FILE__) . '/raintpl/rain.tpl.class.php';

class HyperCacheUtility {

	public $data;
	public $debug;
	public $deleted = 0;
	public $expired = 0;
	public $files = array();
	public $hc_invalidation_archives_time;
	public $hc_invalidation_global_time;
	public $hc_cache_path;
	public $hc_timeout;
	public $last_deleted_filename = '';
	protected $special = array('_archives', '_global');
	public $status301 = 0;
	public $status404 = 0;
	public $time;
	public $uris;

	public function __construct($debug=false) {
		// Configure Rain TPL
		raintpl::configure('base_url', null);
		raintpl::configure('tpl_dir', dirname(__FILE__) . '/../tpl/');
		raintpl::configure('cache_dir', dirname(__FILE__) . '/../../../cache/raintpl/');

		$this -> init($debug);
	}

	/**
	 * Delete a cache file with given filename.
	 * 
	 * This method sets $this -> last_deleted_filename to filename and also
	 * increments $this -> deleted on success.
	 *
	 * @param string $filename Filename of the file to delete.
	 * @return bool True on successful removal, false on failure.
	 */
	public function delete($filename) {
		if (!$this :: is_valid_hash($filename) && !$this -> is_special($filename)) throw new Exception('Invalid hash: ' . $filename);
		if ($this -> debug) $deleted = true;  // Pretend
		else $deleted = @unlink($this -> hc_cache_path . $filename . '.dat');
		if ($deleted) {
			$this -> last_deleted_filename = $filename;
			$this -> deleted ++;
			return true;
		}
		return false;
	}

	/**
	 * Truncate a string to a given maximum length.
	 * 
	 * Append an ellipsis character if the string has been truncated.
	 *
	 * @param string $str String to truncate.
	 * @param int $maxlen Optional. Character limit. Default is 80.
	 * @return string Truncated string.
	 */
	public static function ellipsis($str, $maxlen=80) {
		if (strlen($str) > $maxlen) return substr($str, 0, $maxlen) . '…';
		return $str;
	}

	/**
	 * Get value if set, optional default otherwise.
	 *
	 * @param mixed $what Value to check.
	 * @param mixed $default Optional. Value to return if $what is not set. Default NULL.
	 * @return mixed Same as $what if set else $default.
	 */
	public static function get(&$what, $default=NULL) {
		return isset($what) ? $what : $default;
	}
	
	public static function get_charset(&$data) {
		if (isset($data['mime'])) return preg_replace('/^.*;\s*charset=/', '', $data['mime']);
	}
	
	public static function get_content_type(&$data) {
		if (isset($data['mime'])) return preg_replace('/;.*$/', '', $data['mime']);
	}
	
	public function get_data($file) {
		$data = @unserialize(file_get_contents($file));
		$data['file'] = $file;
		$data['filename'] = pathinfo($file, PATHINFO_FILENAME);
		$data['filetime'] = @filemtime($file);
		$data['is_expired'] = $this -> is_expired($data);
		if (!isset($data['status']) && !$this -> is_special($data['filename'])) $data['status'] = !isset($data['location']) ? 200 : 301;
		return $data;
	}
	
	/**
	 * Get translated status text from HTTP status code.
	 *
	 * @param object $data Cache file object.
	 * @return string Translated status text.
	 */
	public static function get_status_text(&$data) {
		if (isset($data['status'])) {
			$status_text = sprintf(__('HTTP Status Code %u %s', 'hyper-cache-utility'), $data['status'], get_status_header_desc($data['status']));
			if ($data['is_expired']) $status_text .= ' ' . __('(Expired)', 'hyper-cache-utility');
			return $status_text;
		}
	}

	/**
	 * Sets up Hyper Cache settings related properties.
	 *
	 * @param mixed $debug Optional. Print debug messages if true. Default NULL.
	 */
	public function init($debug=NULL) {
		if ($debug !== NULL) $this -> debug = $debug;

		// Get Hyper Cache configuration from advanced-cache.php
		$advanced_cache = WP_CONTENT_DIR . '/advanced-cache.php';
		if (is_file($advanced_cache)) {
			if ($this -> debug) $this -> log( 'advanced-cache.php: ' . $advanced_cache );
			$contents = file_get_contents($advanced_cache);
			if (preg_match('/\$hyper_cache_path\s*=\s*(["\'])(.+?)\1/', $contents, $match)) {
				$hyper_cache_path = $match[2];
				if ($this -> debug) $this -> log( '$hyper_cache_path from advanced-cache.php: ' . $hyper_cache_path );
			}
			if (preg_match('/\$hyper_cache_timeout\s*=\s*(\d+(?:.\d+)?)/', $contents, $match)) {
				$hyper_cache_timeout = floatval($match[1]);
				if ($this -> debug) $this -> log( '$hyper_cache_timeout from advanced-cache.php: ' . $hyper_cache_timeout );
			}
		};

		// Fallback values if Hyper Cache configuration is not available
		if (!isset($hyper_cache_path)) {
			$hyper_cache_path = WP_CONTENT_DIR . '/cache/hyper-cache/';
			if ($this -> debug) $this -> log( '$hyper_cache_path fallback: ' . $hyper_cache_path );
		}
		$this -> hc_cache_path = $hyper_cache_path;
		if (!isset($hyper_cache_timeout)) {
			$hyper_cache_timeout = 2000000000;
			if ($this -> debug) $this -> log( '$hyper_cache_timeout fallback: ' . $hyper_cache_timeout );
		}
		$this -> hc_timeout = $hyper_cache_timeout;
	}

	/**
	 * Check if cache object is expired.
	 *
	 * @param object $data Cache object to check.
	 * @return bool True if object is expired, false otherwise.
	 */
	public function is_expired(&$data) {
		$file_age = $this -> time - $data['filetime'];
		return !$this -> is_special($data['filename']) && ($file_age > $this -> hc_timeout ||
			($this -> hc_invalidation_global_time && $data['filetime'] < $this -> hc_invalidation_global_time) ||
			(isset($data['type']) && ($data['type'] == 'blog' || $data['type'] == 'home' || $data['type'] == 'archive' || $data['type'] == 'feed' || $data['type'] == 'search') &&
			 $this -> hc_invalidation_archives_time && $data['filetime'] < $this -> hc_invalidation_archives_time));
	}

	/**
	 * Check if filename is a 'special' file.
	 *
	 * @param string $filename Filename (without extension) to check.
	 * @return bool True if file is special, false otherwise.
	 */
	public function is_special($filename) {
		return in_array($filename, $this -> special);
	}

	/**
	 * Check if hash is a valid md5 sum hex string.
	 *
	 * @param string $hash Hash to check.
	 * @return int 1 if hash is valid, 0 if invalid.
	 */
	public static function is_valid_hash($hash) {
		return preg_match('/^[0-9a-f_]{32}$/i', $hash);
	}

	/**
	 * Log a message to the logfile in the script directory.
	 * 
	 * This requires that the script directory is writable.
	 *
	 * @param string $message The log message.
	 */
	public static function log($message) {
		$logfile = dirname(__FILE__) . '/log.txt';
		$log = @file_get_contents($logfile);
		$log .= $message . "\n";
		file_put_contents($logfile, $log);
	}

	/**
	 * Process cache files.
	 * 
	 * $delete can be false, 'all', 'expired', '404' or a filename.
	 *
	 * @param string $delete Optional. What to delete.
	 * @param bool $prepare_output Optional. Prepare HTML output if true. Default true.
	 */
	public function process($delete=false, $prepare_output=true) {
		$is_valid_filename = $this :: is_valid_hash($delete) || $this -> is_special($delete);
		if ( !$prepare_output && $is_valid_filename ) {
			// If we only delete a single file without outputting anything,
			// skip processing of all other files
			$this -> delete($delete);
		}
		else if ( !$is_valid_filename && !in_array( $delete, array(false, 'all', 'expired', 404) ) ) {
			throw new Exception('Invalid value for \'delete\' parameter: ' . $delete);
		}
		else {
			$this -> data = array();

			$files = glob($this -> hc_cache_path . '*.dat');
			if ($files !== false) $this -> files = $files;

			$this -> hc_invalidation_archives_time =  $delete == '_archives' ? 0 : @filemtime($this -> hc_cache_path . '_archives.dat');
			$this -> hc_invalidation_global_time = $delete == '_global' ? 0 : @filemtime($this -> hc_cache_path . '_global.dat');

			$this -> time = time();

			$this -> uris = array();

			foreach ($this -> files as $file) {
				$filename = pathinfo($file, PATHINFO_FILENAME);
				$should_delete = $delete == 'all' || $delete == $filename;
				if (!$should_delete) {
					$data = $this -> get_data($file);
					$should_delete = (($delete == 404 && $this :: get($data['status']) == 404) ||
									  ($delete == 'expired' && $data['is_expired']));
				}
				if ($should_delete) {
					$this -> delete($filename);
				}
				else {
					if ($data['is_expired']) $this -> expired ++;
					if ($this :: get($data['status']) == 301) $this -> status301 ++;
					else if ($this :: get($data['status']) == 404) $this -> status404 ++;
					if ($prepare_output) {
						$this -> update_meta($data);
						$this -> data[] = $data;
						if (isset($data['uri'])) {
							if (empty($this -> uris[$data['uri']])) $this -> uris[$data['uri']] = array();
							$this -> uris[$data['uri']][] = $data['filename'];
						}
					}
				}
			}
		}
	}

	/**
	 * Output user interface for managing cache files.
	 */
	public function output() {
		// Initialize Rain TPL
		$tpl = new RainTPL;
		$tpl -> assign( 'title', __('Hyper Cache Utility', 'hyper-cache-utility') );
		$tpl -> assign( 'gzip', __('GZIP', 'hyper-cache-utility') );
		$tpl -> assign( 'html', __('HTML', 'hyper-cache-utility') );
		$tpl -> assign( 'delete', __('Delete', 'hyper-cache-utility') );
		$tpl -> assign( 'view', __('View', 'hyper-cache-utility') );
		$tpl -> assign( 'deleted_info', sprintf(_n('One file deleted.', '%u files deleted.', $this -> deleted, 'hyper-cache-utility'), $this -> deleted) );
		$tpl -> assign( 'files_info', __('Files in cache (valid and expired)', 'hyper-cache-utility') );
		$tpl -> assign( 'files_detail_info', sprintf(__('Expired: <span class="expired-count">%u</span>, Not Found: <span class="status-404-count">%u</span>, Moved Permanently: <span class="status-301-count">%u</span>', 'hyper-cache-utility'), $this -> expired, $this -> status404, $this -> status301) );
		$tpl -> assign( 'delete_expired', __('Delete expired', 'hyper-cache-utility') );
		$tpl -> assign( 'delete_404', __('Delete all with status 404', 'hyper-cache-utility') );
		$tpl -> assign( 'delete_all', __('Delete all', 'hyper-cache-utility') );
		$tpl -> assign( 'sort_by_status', __('Sort by HTTP Status Code', 'hyper-cache-utility') );
		$tpl -> assign( 'status', __('Status', 'hyper-cache-utility') );
		$tpl -> assign( 'sort_by_uri', __('Sort by URI', 'hyper-cache-utility') );
		$tpl -> assign( 'uri_filename', __('URI &amp; Cache Filename', 'hyper-cache-utility') );
		$tpl -> assign( 'sort_by_date', __('Sort by Date', 'hyper-cache-utility') );
		$tpl -> assign( 'date', __('Date', 'hyper-cache-utility') );
		$tpl -> assign( 'sort_by_size', __('Sort by Size', 'hyper-cache-utility') );
		$tpl -> assign( 'size', __('Size', 'hyper-cache-utility') );
		$tpl -> assign( 'sort_by_type', __('Sort by Type', 'hyper-cache-utility') );
		$tpl -> assign( 'type', __('Type', 'hyper-cache-utility') );
		$tpl -> assign( 'sort_by_content_type', __('Sort by Content-Type', 'hyper-cache-utility') );
		$tpl -> assign( 'content_type', __('Content-Type', 'hyper-cache-utility') );
		$tpl -> assign( 'sort_by_data_format', __('Sort by Data Format', 'hyper-cache-utility') );
		$tpl -> assign( 'data_format', __('Data', 'hyper-cache-utility') );
		$tpl -> assign( 'sort_by_user_agent', __('Sort by HTTP User Agent', 'hyper-cache-utility') );
		$tpl -> assign( 'user_agent', __('User Agent', 'hyper-cache-utility') );
		$tpl -> assign( 'page', __('Page', 'hyper-cache-utility') );
		$tpl -> assign( 'first', __('First', 'hyper-cache-utility') );
		$tpl -> assign( 'prev', __('Previous', 'hyper-cache-utility') );
		$tpl -> assign( 'next', __('Next', 'hyper-cache-utility') );
		$tpl -> assign( 'last', __('Last', 'hyper-cache-utility') );
		$tpl -> assign( 'entries_per_page', __('Entries per page:', 'hyper-cache-utility') );
		$tpl -> assign( 'page_uri', 'tools.php?page=hyper-cache-utility/hyper-cache-utility.php' );
		$tpl -> assign( 'deleted', $this -> deleted );
		$tpl -> assign( 'expired', $this -> expired );
		$tpl -> assign( 'status301', $this -> status301 );
		$tpl -> assign( 'status404', $this -> status404 );
		$tpl -> assign( 'hc_timeout', $this -> hc_timeout );
		$tpl -> assign( 'hc_invalidation_global_time', $this -> hc_invalidation_global_time );
		$tpl -> assign( 'hc_invalidation_archives_time', $this -> hc_invalidation_archives_time );
		$tpl -> assign( 'time', $this -> time );
		$tpl -> assign( 'files', $this -> files );
		$tpl -> assign( 'data', $this -> data );

		$html = $tpl -> draw( 'main', $return_string=true );
		
		// Check if User-Agent is set by Hyper Cache. Requires a custom version of Hyper Cache.
		$has_ua = preg_match('/<td class="user-agent">/', $html);
		if (!$has_ua) {
			// Strip the empty UA column
			$html = preg_replace('/<th class="user-agent">.*?<\/th>/', '', $html);
			$html = preg_replace('/<td class="user-agent not-applicable">\s*<\/td>/', '', $html);
		}
		
		echo $html;
		
		foreach ($this -> uris as $uri => $filenames) {
			if (count($filenames) > 1) {
				$data1 = @unserialize(file_get_contents($this -> hc_cache_path . $filenames[0] . '.dat'));
				$html1 = explode("\n", $this :: get($data1['html']) || hyper_cache_gzdecode($data1['gz']));
				$data2 = @unserialize(file_get_contents($this -> hc_cache_path . $filenames[1] . '.dat'));
				$html2 = explode("\n", $this :: get($data2['html']) || hyper_cache_gzdecode($data2['gz']));
				echo '<div>--- ' . $filenames[0] . ': ' . $this :: get($data1['host'], '') . $uri . "</div>\n";
				echo '<div>+++ ' . $filenames[1] . ': ' . $this :: get($data2['host'], '') . $uri . "</div>\n";
				foreach ($html1 as $lineno => $line) {
					if ($html2[$lineno] != $line) {
						echo '<div>- ' . esc_html($line) . "</div>\n";
						echo '<div>+ ' . esc_html($html2[$lineno]) . "</div>\n";
					}
				}
			}
		}
	}

	/**
	 * Send headers informing about number of deleted/expired/301/404 files.
	 */
	public function send_headers() {
		header('X-HyperCache-Count: ' . (count($this -> files) - $this -> deleted));
		if ($this -> deleted > 0) header('X-HyperCache-Deleted: ' . ($this -> deleted == count($this -> files) ? 'all' : ($this -> deleted > 1 ? ($delete == 'expired' ? 'expired' : 'status=404') : 'hash=' . $this -> last_deleted_filename)));
		header('X-HyperCache-Expired-Count: ' . $this -> expired);
		header('X-HyperCache-Status-301-Count: ' . $this -> status301);
		header('X-HyperCache-Status-404-Count: ' . $this -> status404);
	}

	public function update_meta(&$data) {
		if (isset($data['type'])) $data['type'] = str_replace('single', 'post', $data['type']);
		$tags = array();
		$tags[] = 'status-' . (isset($data['status']) ? $data['status'] : 'not-applicable');
		$tags[] = 'type-' . (isset($data['type']) ? $data['type'] : 'not-applicable');
		$tags[] = 'mime-type-' . (isset($data['mime']) ? str_replace('/', '-', preg_replace('/;.*$/', '', $data['mime'])) : 'not-applicable');
		if (!isset($data['gz']) && !isset($data['html'])) $tags[] = 'data-not-applicable';
		else {
			if (isset($data['gz'])) $tags[] = 'data-gz';
			if (isset($data['html'])) $tags[] = 'data-html';
		}
		if (!$this -> is_special($data['filename']) && $data['is_expired']) $tags[] = 'expired';
		$data['tags'] = $tags;
		$data['status_text'] = $this :: get_status_text($data);
		if (isset($data['host'])) $data['uri'] = '//' . $data['host'] . $data['uri'];
		if (isset($data['location']))
			$data['location_text'] = preg_replace('/^\w+:\/\//', '', $data['location']);
		$data['basename'] = $data['filename'] . '.dat';
		if (isset($data['type'])) $data['type_text'] = __(ucfirst($data['type']), 'hyper-cache-utility');
		$data['content_type'] = $this :: get_content_type($data);
		$data['charset'] = $this :: get_charset($data);
		if (isset($data['user_agent'])) $data['user_agent_html'] = preg_replace('/(\w+:\/\/[\w$%&\/=?@+~#.:-_]+)/', '<a href="$1" target="_blank">$1</a>', esc_html($data['user_agent']));
	}

	/**
	 * View a cache file.
	 *
	 * @param string $filename Filename.
	 */
	public function view($filename) {
		if (!$this :: is_valid_hash($filename) && !$this -> is_special($filename)) throw new Exception('Invalid hash: ' . $filename);
		$file = $this -> hc_cache_path . $filename . '.dat';
		if (!is_file($file)) throw new Exception('Not a file: ' . $filename . '.dat');

		$data = $this -> get_data($file);

		if (!isset($data['html']) && isset($data['gz'])) {
			if (function_exists('gzinflate')) {
				$data['html'] = hyper_cache_gzdecode($data['gz']);
				if ($data['html'] === false) throw new Exception('Error decoding the content of ' . $filename . '.dat');
			}
			else {
				// Cannot decode compressed data
				throw new Exception('GZIP encoded content detected, but no decoding function available. Unable to decode ' . $filename . '.dat');
			}
		}

		$this -> update_meta($data);

		// Initialize Rain TPL
		$tpl = new RainTPL;
		$tpl -> assign( 'title', __('Hyper Cache Utility', 'hyper-cache-utility') );
		$tpl -> assign( 'go_back', __('Go back') );
		$tpl -> assign( 'page_uri', 'tools.php?page=hyper-cache-utility/hyper-cache-utility.php' );
		$tpl -> assign( 'data', $data );

		$tpl -> draw( 'view' );
	}
}

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

?>
