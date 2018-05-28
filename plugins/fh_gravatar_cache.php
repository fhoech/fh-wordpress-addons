<?php
/*
Plugin Name: FH Gravatar Cache
Plugin URI: https://github.com/fhoech/fh-wordpress-addons/blob/master/plugins/fh_gravatar_cache
Version: $Id$
Description: Cache gravatars for a week (overridable by defining FH_GRAVATAR_CACHE_LIFETIME). Unlike other gravatar cache plugins, this one respects the requested avatar size and serves the correct file type. Works with BuddyPress and bbPress. Uses WP_Cron to fetch gravatars, and serves the same file for all users with default gravatar to keep the number of HTTP requests to a minimum.
Author: Florian Höch
Author URI: http://hoech.net
License: GPL3
*/

class FH_Gravatar_Cache {

	private $cache_dir;
	private $default_md5 = 'd41d8cd98f00b204e9800998ecf8427e';  // md5( '' )
	private $expiration_time;
	private $flock_filename = '.lock';
	private $mutex;
	private $now;
	
	public function __construct() {
		$this->expiration_time = defined('FH_GRAVATAR_CACHE_LIFETIME') ? FH_GRAVATAR_CACHE_LIFETIME : 60 * 60 * 24 * 7;  // One week default
		if (defined('FH_GRAVATAR_CACHE_PATH'))
			$this->cache_dir = FH_GRAVATAR_CACHE_PATH;
		else
			// Using the correct separator eliminates some cache flush errors on Windows
			$this->cache_dir = ABSPATH.'wp-content'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'fh-gravatar-cache'.DIRECTORY_SEPARATOR;

		$this->now = time();

		add_action( 'fh_gravatar_cache_update_cron', array( &$this, 'fetch_gravatar'), 10, 4 );
		add_action( 'fh_gravatar_cache_clean_cron', array( &$this, 'clean_cache'), 10, 0 );
		add_filter( 'get_avatar', array( &$this, 'get_avatar'), 10, 5 );
		add_filter( 'bp_core_fetch_avatar', array( &$this, 'bp_core_fetch_avatar'), 10, 9 );
		add_filter( 'bp_core_fetch_avatar_url', array( &$this, 'bp_core_fetch_avatar_url'), 10, 2 );

		register_activation_hook( __FILE__, array( &$this, 'activate') );
		register_deactivation_hook( __FILE__, array( &$this, 'deactivate') );
	}

	public function activate () {
		if ( ! wp_next_scheduled ( 'fh_gravatar_cache_clean_cron' ) )
			wp_schedule_event( time(), 'daily', 'fh_gravatar_cache_clean_cron' );
	}

	public function deactivate () {
		wp_clear_scheduled_hook( 'fh_gravatar_cache_clean_cron' );
	}

	public function clean_cache () {
		if ( ! is_dir($this->cache_dir) ) return;  // Nothing to do
		
		$cache_dir = new DirectoryIterator( $this->cache_dir );
		foreach ( $cache_dir as $fileinfo ) {
			$cache_filename = $fileinfo->getFilename();
			if ( $fileinfo->isDot() ||
				 $cache_filename == '.htaccess' ||
				 $cache_filename == 'index.html' ||
				 $cache_filename == $this->flock_filename ||
				 substr( $cache_filename, 0, 32 ) == $this->default_md5 ) continue;
			$stat = stat( $this->cache_dir . $cache_filename );
			if ( $stat['mtime'] + $this->expiration_time < $this->now )
				unlink( $this->cache_dir . $cache_filename );
		}
	}

	public function bp_core_fetch_avatar( $avatar, $params, $item_id, $avatar_dir, $html_css_id, $html_width, $html_height, $avatar_folder_url, $avatar_folder_dir ) {
		if ( empty( $params['html'] ) )
			$avatar_url = $avatar;
		elseif ( preg_match( '~src="([^"]+)"~', $avatar, $match ) )
			$avatar_url = $match[1];
		// FIXME: We default to mystery man irrespective of BuddyPress setting
		$avatar = str_replace( $avatar_url, $this->bp_core_fetch_avatar_url( $avatar_url, $params ), $avatar );
		return $avatar;
	}

	public function bp_core_fetch_avatar_url( $avatar_url, $params ) {
		// FIXME: We default to mystery man irrespective of BuddyPress setting
		return $this->get_avatar( $avatar_url, $params['email'], $params['width'], 'mm', '' );
	}

	public function get_avatar ( $avatar, $id_or_email, $size, $default, $alt ) {

		if ( strpos( $avatar, '//www.gravatar.com/' ) === false )
			return $avatar;  // Not a gravatar

		// Get user email
		$user = false;
		if ( is_numeric( $id_or_email ) ) {
			$id = (int) $id_or_email;
			$user = get_user_by( 'id' , $id );
		}
		elseif ( is_object( $id_or_email ) ) {
			if ( empty ( $id_or_email->user_email ) &&
				 ! empty( $id_or_email->user_id ) ) {
				$id = (int) $id_or_email->user_id;
				$user = get_user_by( 'id' , $id );
			}
			else $user = $id_or_email;
		}
		else $email = $id_or_email;
		if ( $user && ! empty( $user->user_email ) ) $email = $user->user_email;
		if ( ! isset( $email ) ) $email = '';

		if ( preg_match( '~&amp;r=(g|pg|r|x)~', $avatar, $match) )
			$rating = $match[1];
		else
			$rating = 'g';

		$md5 = md5( strtolower( trim( $email ) ) );
		
		// Check if avatar exists in cache and is not expired
		$file_types = array( 'default', 'jpg', 'png', 'gif' );
		$is_cached = false;
		foreach ( $file_types as $file_type ) {
			$cache_file = $this->cache_dir . $md5 . '-' . $size . '-' . $rating . '-' . rawurlencode( $default ) . '.' . $file_type;
			if ( is_file( $cache_file ) ) {
				$stat = stat( $cache_file );
				if ($stat['mtime'] + $this->expiration_time > $this->now)
					$is_cached = true;
				break;
			}
		}

		// ---------------------------------------------------------------------

		if ( ! $is_cached ) {
			// Schedule fetching of gravatar

			$args = array( $md5, $size, $rating, $default );
			wp_clear_scheduled_hook( 'fh_gravatar_cache_update_cron', $args );
			wp_schedule_single_event( time(), 'fh_gravatar_cache_update_cron', $args );

			return $avatar;
		}

		if ( $file_type == 'default' ) {
			if ( $default == 'blank' ) $file_type = 'png';
			else $file_type = 'jpg';
			$cache_file = $this->cache_dir . $this->default_md5 . '-' . $size . '-' . $rating . '-' . rawurlencode( $default ) . '.' . $file_type;
		}
		$url = substr( $cache_file, strlen( ABSPATH ) );
		if ( strpos( $url, 'wp-content' . DIRECTORY_SEPARATOR ) === 0 ) $url = content_url( substr( $url, strlen( 'wp-content' . DIRECTORY_SEPARATOR ) ) );
		else $url = site_url( $url );

		return preg_replace( '~(?:https?:)?//(?:www|secure)\.gravatar\.com/[^"\']+~',
							 esc_attr( $url ), $avatar );
	}

	public function fetch_gravatar( $md5, $size, $rating, $default ) {
		// Fetch gravatar. Map all gravatars that return 404 to default gravatar.

		$stat = stat(ABSPATH.'wp-content');
		$dir_perms = $stat['mode'] & 0007777;  // Get the permission bits.
		$file_perms = $dir_perms & 0000666;  // Remove execute bits for files.

		// Make the base cache dir.
		if (!is_dir($this->cache_dir)) {
			if (! @ mkdir($this->cache_dir))
				return $avatar;
			@ chmod($this->cache_dir, $dir_perms);
		}

		if (!file_exists($this->cache_dir.".htaccess")) {
			@ touch($this->cache_dir."index.html");
			@ chmod($this->cache_dir."index.html", $file_perms);
			file_put_contents($this->cache_dir.'.htaccess',
							  "<IfModule mod_expires.c>\n" .
							  "ExpiresActive on\n" .
							  'ExpiresByType image/jpeg "access plus ' . $this->expiration_time . ' seconds"' . "\n" .
							  'ExpiresByType image/png "access plus ' . $this->expiration_time . ' seconds"' . "\n" .
							  'ExpiresByType image/gif "access plus ' . $this->expiration_time . ' seconds"' . "\n" .
							  "</IfModule>\n");
		}

		if ( ! $this->acquire_lock() )
			return;

		if ( empty( $default ) || $default == 'blank' || $default == 'mm' ) $fallback = '404';
		else $fallback = $default;

		$data = $this->curl_get_contents( 'http://www.gravatar.com/avatar/' . $md5 . '?s=' . $size . '&r=' . $rating . '&d=' . $fallback );
		if ( $fallback == '404' && ! empty( $data ) && substr( $data, 0, 3 ) == '404' ) {
			file_put_contents( $this->cache_dir . $md5 . '-' . $size . '-' . $rating . '-' . rawurlencode( $default ) . '.default', '' );
			// Map to default gravatar
			$md5 = $this->default_md5;
			// Check if default gravatar cache file already exists
			if ( $default == 'blank' ) $file_type = 'png';
			else $file_type = 'jpg';
			if ( is_file( $this->cache_dir . $md5 . '-' . $size . '-' . $rating . '-' . rawurlencode( $default ) . '.' . $file_type ) )
				unset( $data );
			else $data = $this->curl_get_contents( 'http://www.gravatar.com/avatar/' . $md5 . '?s=' . $size . '&r=' . $rating . '&d=' . rawurlencode( $default ) );
		}
		if ( ! empty( $data ) ) {
			$header = substr( $data, 0, 4);
			switch ( $header ) {
				case "\x89PNG":
					$file_type = 'png';
					break;
				case 'GIF8':
					$file_type = 'gif';
					break;
				default:
					$file_type = 'jpg';
			}
			$cache_file = $this->cache_dir . $md5 . '-' . $size . '-' . $rating . '-' . rawurlencode( $default ) . '.' . $file_type;
			file_put_contents( $cache_file, $data );
		}

		$this->release_lock();
	}

	private function curl_get_contents( $uri, $timeout=5 ) {
		$ch = curl_init( $uri );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
		$contents = @curl_exec( $ch );
		curl_close( $ch );
		return $contents;
	}

	private function acquire_lock() {
		// Acquire a write lock
		$this->mutex = @fopen($this->cache_dir.$this->flock_filename, 'w');
		if ( false == $this->mutex)
			return false;
		else {
			flock($this->mutex, LOCK_EX);
			return true;
		}
	}

	private function release_lock() {
		// Release write lock
		flock($this->mutex, LOCK_UN);
		fclose($this->mutex);
	}

}

$FH_Gravatar_Cache = new FH_Gravatar_Cache;

?>
