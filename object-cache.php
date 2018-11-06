<?php
/**
 * Object Cache API
 *
 * @link https://codex.wordpress.org/Function_Reference/WP_Cache
 *
 * @package WordPress
 * @subpackage Cache
 * @version $Id$
 * 
 * This file will always be for the most part a verbatim copy of wp-includes/cache.php,
 * with the exception that it has support for file-based object caching. All changes
 * to support that are marked up with PHP comments 'File-based object cache [start|end]'
 * to make updating the file easy.
 */

/* File-based object cache start */
define('CACHE_SERIAL_HEADER', "<?php\n/*");
define('CACHE_SERIAL_FOOTER', "*/\n?".">");
/* File-based object cache end */

/**
 * Adds data to the cache, if the cache key doesn't already exist.
 *
 * @since 2.0.0
 *
 * @global WP_Object_Cache $wp_object_cache
 *
 * @param int|string $key The cache key to use for retrieval later
 * @param mixed $data The data to add to the cache store
 * @param string $group The group to add the cache to
 * @param int $expire When the cache data should be expired
 * @return bool False if cache key and group already exist, true on success
 */
function wp_cache_add( $key, $data, $group = '', $expire = 0 ) {
	global $wp_object_cache;

	return $wp_object_cache->add( $key, $data, $group, (int) $expire );
}

/* File-based object cache start */
/**
 * Closes and persists the cache.
 *
 * @since 2.0.0
 *
 * @return true if cache was successfully persisted, false on failure
 */
function wp_cache_close() {
	global $wp_object_cache;

	if (isset($wp_object_cache))
		return $wp_object_cache->persist();
}
/* File-based object cache end */

/**
 * Decrement numeric cache item's value
 *
 * @since 3.3.0
 *
 * @global WP_Object_Cache $wp_object_cache
 *
 * @param int|string $key The cache key to increment
 * @param int $offset The amount by which to decrement the item's value. Default is 1.
 * @param string $group The group the key is in.
 * @return false|int False on failure, the item's new value on success.
 */
function wp_cache_decr( $key, $offset = 1, $group = '' ) {
	global $wp_object_cache;

	return $wp_object_cache->decr( $key, $offset, $group );
}

/**
 * Removes the cache contents matching key and group.
 *
 * @since 2.0.0
 *
 * @global WP_Object_Cache $wp_object_cache
 *
 * @param int|string $key What the contents in the cache are called
 * @param string $group Where the cache contents are grouped
 * @return bool True on successful removal, false on failure
 */
function wp_cache_delete($key, $group = '') {
	global $wp_object_cache;

	return $wp_object_cache->delete($key, $group);
}

/**
 * Removes all cache items.
 *
 * @since 2.0.0
 *
 * @global WP_Object_Cache $wp_object_cache
 *
 * @return bool False on failure, true on success
 */
function wp_cache_flush() {
	global $wp_object_cache;

	return $wp_object_cache->flush();
}

/**
 * Retrieves the cache contents from the cache by key and group.
 *
 * @since 2.0.0
 *
 * @global WP_Object_Cache $wp_object_cache
 *
 * @param int|string $key What the contents in the cache are called
 * @param string $group Where the cache contents are grouped
 * @param bool $force Whether to force an update of the local cache from the persistent cache (default is false)
 * @param bool &$found Whether key was found in the cache. Disambiguates a return of false, a storable value.
 * @return bool|mixed False on failure to retrieve contents or the cache
 *		              contents on success
 */
function wp_cache_get( $key, $group = '', $force = false, &$found = null ) {
	global $wp_object_cache;

	return $wp_object_cache->get( $key, $group, $force, $found );
}

/**
 * Increment numeric cache item's value
 *
 * @since 3.3.0
 *
 * @global WP_Object_Cache $wp_object_cache
 *
 * @param int|string $key The cache key to increment
 * @param int $offset The amount by which to increment the item's value. Default is 1.
 * @param string $group The group the key is in.
 * @return false|int False on failure, the item's new value on success.
 */
function wp_cache_incr( $key, $offset = 1, $group = '' ) {
	global $wp_object_cache;

	return $wp_object_cache->incr( $key, $offset, $group );
}

/**
 * Sets up Object Cache Global and assigns it.
 *
 * @since 2.0.0
 *
 * @global WP_Object_Cache $wp_object_cache
 */
function wp_cache_init() {
	$GLOBALS['wp_object_cache'] = new WP_Object_Cache();
}

/**
 * Replaces the contents of the cache with new data.
 *
 * @since 2.0.0
 *
 * @global WP_Object_Cache $wp_object_cache
 *
 * @param int|string $key What to call the contents in the cache
 * @param mixed $data The contents to store in the cache
 * @param string $group Where to group the cache contents
 * @param int $expire When to expire the cache contents
 * @return bool False if not exists, true if contents were replaced
 */
function wp_cache_replace( $key, $data, $group = '', $expire = 0 ) {
	global $wp_object_cache;

	return $wp_object_cache->replace( $key, $data, $group, (int) $expire );
}

/**
 * Saves the data to the cache.
 *
 * @since 2.0.0
 *
 * @global WP_Object_Cache $wp_object_cache
 *
 * @param int|string $key What to call the contents in the cache
 * @param mixed $data The contents to store in the cache
 * @param string $group Where to group the cache contents
 * @param int $expire When to expire the cache contents
 * @return bool False on failure, true on success
 */
function wp_cache_set( $key, $data, $group = '', $expire = 0 ) {
	global $wp_object_cache;

	return $wp_object_cache->set( $key, $data, $group, (int) $expire );
}

/**
 * Switch the interal blog id.
 *
 * This changes the blog id used to create keys in blog specific groups.
 *
 * @since 3.5.0
 *
 * @global WP_Object_Cache $wp_object_cache
 *
 * @param int $blog_id Blog ID
 */
function wp_cache_switch_to_blog( $blog_id ) {
	global $wp_object_cache;

	$wp_object_cache->switch_to_blog( $blog_id );
}

/**
 * Adds a group or set of groups to the list of global groups.
 *
 * @since 2.6.0
 *
 * @global WP_Object_Cache $wp_object_cache
 *
 * @param string|array $groups A group or an array of groups to add
 */
function wp_cache_add_global_groups( $groups ) {
	global $wp_object_cache;

	$wp_object_cache->add_global_groups( $groups );
}

/**
 * Adds a group or set of groups to the list of non-persistent groups.
 *
 * @since 2.6.0
 *
 * @param string|array $groups A group or an array of groups to add
 */
function wp_cache_add_non_persistent_groups( $groups ) {
	/* File-based object cache start */
	global $wp_object_cache;

	return $wp_object_cache->add_non_persistent_groups( $groups );
	/* File-based object cache end */
}

/**
 * Reset internal cache keys and structures. If the cache backend uses global
 * blog or site IDs as part of its cache keys, this function instructs the
 * backend to reset those keys and perform any cleanup since blog or site IDs
 * have changed since cache init.
 *
 * This function is deprecated. Use wp_cache_switch_to_blog() instead of this
 * function when preparing the cache for a blog switch. For clearing the cache
 * during unit tests, consider using wp_cache_init(). wp_cache_init() is not
 * recommended outside of unit tests as the performance penality for using it is
 * high.
 *
 * @since 2.6.0
 * @deprecated 3.5.0
 *
 * @global WP_Object_Cache $wp_object_cache
 */
function wp_cache_reset() {
	_deprecated_function( __FUNCTION__, '3.5' );

	global $wp_object_cache;

	$wp_object_cache->reset();
}

define('FH_OBJECT_CACHE_UNIQID', uniqid());

class SHM_Cache {

	private $group = 'default';
	private static $groups = null;
	private static $groups_persist = false;
	private static $groups_id = null;
	private static $groups_shm_id = false;
	private static $groups_size = 0;
	private $id = null;
	private $shm_id = false;
	private $size = 0;
	private static $debug = 0;

	public function __construct( $group = 'default' ) {
		// XXX: Max 255 cache keys

		$this->group = $group;

		SHM_Cache::_init_groups( $group );

		$this->id = SHM_Cache::_get_id( $group );
	}

	public function __destruct() {
		@ $this->close();
		return true;
	}

	private static function _init_groups( $group = 'default' ) {
		if ( SHM_Cache::$groups === null ) {
			// Init groups to (proj_id, mtime) mapping
	
			SHM_Cache::$groups = array( $group => array( 1, 0 ) );

			SHM_Cache::$debug = defined('FH_OBJECT_CACHE_SHM_DEBUG') ? FH_OBJECT_CACHE_SHM_DEBUG : 0;

			// Read existing groups to (proj_id, mtime) mapping
			if ( SHM_Cache::_open_groups() ) {
				$data = SHM_Cache::_read( SHM_Cache::$groups_shm_id, SHM_Cache::$groups_size );

				if ( $data !== false ) {
					$groups = @ unserialize( $data );
					if ( $groups !== false ) SHM_Cache::$groups = $groups;
				}
			}

			//register_shutdown_function( array( 'SHM_Cache', '_persist_groups' ) );
		}
	}

	private static function _get_groups_id() {
		if ( ! SHM_Cache::$groups_id )
			SHM_Cache::$groups_id = ftok( __FILE__, "\0" );
		return SHM_Cache::$groups_id;
	}

	private static function _get_id( $group, $reallocate = false ) {
		if ( ! $reallocate && array_key_exists( $group, SHM_Cache::$groups ) &&
			 is_array( SHM_Cache::$groups[$group] ) )
			$i = SHM_Cache::$groups[$group][0];
		else {
			// Update groups to (proj_id, mtime) mapping

			if ( empty( SHM_Cache::$groups ) ) $i = 1;
			else {
				$proj_ids = array_values( SHM_Cache::$groups );
				sort( $proj_ids );
				$i = array_pop( $proj_ids )[0] + 1;
				//if ( $reallocate && $i > 255 ) {
					//$i = 1;
					//while ( $i < 256 ) {
						//$id = ftok( __FILE__, chr( $i ) );
						//$shm_id = SHM_Cache::_open( $id, 'a' );
						//SHM_Cache::_close( $shm_id );
						//if ( ! $shm_id ) break;
						//$i ++;
					//}
				//}
				if ( $i > 255 ) return;
			}

			SHM_Cache::$groups[$group] = array( $i, time() );

			SHM_Cache::$groups_persist = true;
			//SHM_Cache::_persist_groups();
		}

		if ( ! isset( $id ) ) $id = ftok( __FILE__, chr( $i ) );

		return $id;
	}

	private static function _open_groups() {
		$id = SHM_Cache::_get_groups_id();
		$shm_id = SHM_Cache::$groups_shm_id = SHM_Cache::_open( $id, "w", 0, 0, SHM_Cache::$groups_shm_id );
		if ( $shm_id !== false ) SHM_Cache::$groups_size = SHM_Cache::_size( $shm_id );
		return $shm_id !== false;
	}

	public static function _persist_groups() {
		// Write updated groups to (proj_id, mtime) mapping
		if ( ! SHM_Cache::$groups_persist ) return;
		$id = SHM_Cache::_get_groups_id();
		$data = serialize( SHM_Cache::$groups );
		list( $bytes_written, $deleted, $shm_id, $new_id ) = SHM_Cache::_write( SHM_Cache::$groups_shm_id, $data, SHM_Cache::$groups_size, $id );
		if ( $shm_id != SHM_Cache::$groups_shm_id ) {
			file_put_contents( __DIR__ . '/.SHM_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Reallocated SHM " . SHM_Cache::$groups_shm_id . " -> $shm_id (key " . SHM_Cache::get_groups_id( true ) . ") for groups to (proj_id, mtime) mapping: " . SHM_Cache::$groups_size . " -> $bytes_written bytes\n",
							   FILE_APPEND );
			SHM_Cache::$groups_shm_id = $shm_id;
		}
		if ( ! $bytes_written ) {
			if ( $deleted ) SHM_Cache::$groups_size = 0;
			if ( SHM_Cache::$debug ) {
				$error = error_get_last();
				file_put_contents( __DIR__ . '/.SHM_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't persist SHM $shm_id (key " . SHM_Cache::get_groups_id( true ) .
								   ( $deleted !== null ? ", deleted=" . ( $deleted ? 'true' : 'false' ) : '' ) . ") for groups to (proj_id, mtime) mapping: " .
								   $error['message'] . "\n", FILE_APPEND );
			}
			return;
		}
		if ( $bytes_written > SHM_Cache::$groups_size ) {
			//file_put_contents( __DIR__ . '/.SHM_Cache.log',
							   //date( 'Y-m-d H:i:s,v' ) .
							   //" SHM_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Reallocated SHM $shm_id (key " . SHM_Cache::get_groups_id( true ) . ") for groups to (proj_id, mtime) mapping: " . SHM_Cache::$groups_size . " -> $bytes_written bytes\n",
							   //FILE_APPEND );
			SHM_Cache::$groups_size = $bytes_written;
		}
	}

	public static function _open( $id, $flags="w", $mode=0, $size=0, $shm_id = false ) {
		// Open SHM segment. If existing SHM ID is given, will be closed first
		// Return SHM ID
		if ( $id === null ) return false;
		SHM_Cache::_close( $shm_id );
		$shm_id = @ shmop_open( $id, $flags, $mode, $size );
		return $shm_id;
	}

	public static function _close( $shm_id ) {
		// Close SHM segment
		if ( $shm_id !== false ) {
			$result = shmop_close( $shm_id );
			if ( $result !== null ) {
				$error = error_get_last();
				ob_start();
				var_dump( $result );
				$repr = ob_get_contents();
				ob_end_clean();
				file_put_contents( __DIR__ . '/.SHM_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't close SHM $shm_id: " .
								   $error['message'] . " (shmop_close returned " . str_replace( "\n", "", $repr ) . ")\n",
								   FILE_APPEND );
			}
			return $result;
		}
	}

	public static function _size( $shm_id ) {
		if ( $shm_id === false ) return false;
		return shmop_size( $shm_id );
	}

	public static function _read( $shm_id, $size ) {
		// Read and return string from SHM
		// Length of string can be shorter than given size!
		if ( $shm_id === false ) return false;
		$offset = 0;
		if ( $size > 5 ) {
			$header = shmop_read( $shm_id, 0, 5 );
			if ( $header === false ) return false;
			if ( $header[0] == ";" ) {
				// Data size follows in the next 4 bytes (big-endian)
				$size = unpack( 'N', substr( $header, 1, 4 ) )[1];
				$offset = 5;
			}
		}
		$data = shmop_read( $shm_id, $offset, $size );
		if ( $data === false ) return false;
		if ( $offset == 0 ) {
			// Null-terminated
			$len = strpos( $data, "\0" );
			if ( $len !== false ) $data = substr( $data, 0, $len );
		}
		$data = stripcslashes( $data );  // Unescape, see _write()
		return $data;
	}

	public static function _write( $shm_id, $data, $shm_size = 0, $id = null, $group = null ) {
		// Write string to SHM segment. String will be automatically null-terminated.
		// Return array( <bytes written>, <resized>, <SHM ID> )
		// If <resized> is true, segment was successfully resized and SHM ID has changed
		// If <resized> is false, segment resizing failed (<bytes written> will also be false)
		// If <resized> is null, no resizing was necessary (existing SHM segment re-used)
		$data = addcslashes( $data, "\0\\" );  // Because we use null byte as string terminator, need to escape existing null bytes and backslashes
		$size = strlen( $data );
		$data = ";" . pack( 'N', $size ) . $data;  // Prepend packed size
		$shm_size_new = $size + 6;  // 5 bytes header + terminating null
		if ( ( $shm_id === false || $shm_size_new > $shm_size || ! $size ) && $id !== null ) {
			// Delete SHM segment if size is zero or larger than existing segment
			$deleted = SHM_Cache::_delete( $shm_id );
			//if ( $deleted && $group !== null ) $id = SHM_Cache::_get_id( $group, true );
			// If new size is zero, we are done here
			if ( ! $size ) return array( 0, $deleted, $shm_id, $id );
			// Re-create SHM segment with new size
			$shm_id = SHM_Cache::_open( $id, "n", 0600, $shm_size_new );
			if ( $shm_id === false ) return array( false, $deleted, $shm_id, $id );
		}
		else $deleted = null;
		$bytes_written = shmop_write( $shm_id, $data . "\0", 0 );
		return array( $bytes_written, $deleted, $shm_id, $id );
	}

	public static function _delete( $shm_id ) {
		// Delete and close SHM segment
		if ( $shm_id === false ) return false;
		$deleted = shmop_delete( $shm_id );
		if ( ! $deleted ) {
			$error = error_get_last();
			file_put_contents( __DIR__ . '/.SHM_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't delete SHM $shm_id: " .
							   $error['message'] . "\n",
							   FILE_APPEND );
		}
		SHM_Cache::_close( $shm_id );
		return $deleted;
	}

	public function open() {
		$this->shm_id = SHM_Cache::_open( $this->id, "w", 0, 0, $this->shm_id );
		if ( $this->shm_id !== false ) $this->size = SHM_Cache::_size( $this->shm_id );
		return $this->shm_id !== false;
	}

	public function close() {
		SHM_Cache::_close( $this->shm_id );
		$this->shm_id = false;
	}

	public function get() {
		if ( $this->shm_id === false && ! $this->open() ) return false;
		$data = SHM_Cache::_read( $this->shm_id, $this->size );
		return $data;
	}

	public function put( $data ) {
		if ( $this->id === null ) return false;
		if ( $this->shm_id === false ) {
			$this->open();  // Also sets $this->size if the SHM segment exists so we can check whether we need to re-create the segment
		}
		list( $bytes_written, $deleted, $shm_id, $id ) = SHM_Cache::_write( $this->shm_id, $data, $this->size, $this->id, $this->group );
		if ( $id != $this->id ) {
			file_put_contents( __DIR__ . '/.SHM_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Reallocated SHM $this->shm_id -> $shm_id (key " . $this->get_id( true ) . " -> " . SHM_Cache::format_id( $id, true ) . ") for group '$this->group': $this->size -> $bytes_written bytes\n",
							   FILE_APPEND );
			$this->id = $id;
		}
		if ( $shm_id != $this->shm_id ) $this->shm_id = $shm_id;
		if ( ! $bytes_written ) {
			if ( $deleted ) $this->size = 0;
			if ( SHM_Cache::$debug ) {
				$error = error_get_last();
				file_put_contents( __DIR__ . '/.SHM_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't persist SHM $shm_id (key " . $this->get_id( true ) .
								   ( $deleted !== null ? ", deleted=" . ( $deleted ? 'true' : 'false' ) : '' ) . ") for group '$this->group': " .
								   $error['message'] . "\n", FILE_APPEND );
			}
			return false;
		}
		else if ( SHM_Cache::$debug > 1 ) {
			file_put_contents( __DIR__ . '/.SHM_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Persisted SHM $shm_id (key " . $this->get_id( true ) . ") for group '$this->group'\n",
							   FILE_APPEND );
		}
		if ( $bytes_written > $this->size ) {
			file_put_contents( __DIR__ . '/.SHM_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Reallocated SHM $shm_id (key " . $this->get_id( true ) . ") for group '$this->group': $this->size -> $bytes_written bytes\n",
							   FILE_APPEND );
			$this->size = $bytes_written;
		}
		// Update last-modified time
		SHM_Cache::$groups[$this->group][1] = time();
		SHM_Cache::$groups_persist = true;
		//SHM_Cache::_persist_groups();
		return $bytes_written !== false;
	}

	public function clear() {
		if ( $this->shm_id === false && ! $this->open() ) return false;
		$deleted = SHM_Cache::_delete( $this->shm_id );
		if ( $deleted ) {
			$this->shm_id = false;
			$this->size = 0;
		}
		else if ( SHM_Cache::$debug ) {
			file_put_contents( __DIR__ . '/.SHM_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't delete SHM $this->shm_id (key " . $this->get_id( true ) . ") for group '$this->group'\n",
							   FILE_APPEND );
		}
		return $deleted;
	}

	public function mtime() {
		return SHM_Cache::$groups[$this->group][1];
	}

	// Accessor methods to make private properties publicly readable

	public function get_group() {
		return $this->group;
	}

	public static function get_groups() {
		SHM_Cache::_init_groups();
		return SHM_Cache::$groups;
	}

	public static function get_groups_id( $hex = false ) {
		SHM_Cache::_init_groups();
		return SHM_Cache::format_id( SHM_Cache::$groups_id, $hex );
	}

	public static function get_groups_shm_id() {
		SHM_Cache::_init_groups();
		return SHM_Cache::$groups_shm_id;
	}

	public static function get_groups_size() {
		SHM_Cache::_init_groups();
		return SHM_Cache::$groups_size;
	}

	public static function get_groups_persist() {
		return SHM_Cache::$groups_persist;
	}

	public static function format_id( $id, $hex = false ) {
		if ( $hex )  // Formatted like ipcs -m
			$id = '0x' . str_pad( dechex( $id ), 8, '0', STR_PAD_LEFT );
		return $id;
	}

	public function get_id( $hex = false ) {
		return SHM_Cache::format_id( $this->id, $hex );
	}

	public function get_shm_id() {
		return $this->shm_id;
	}

	public function get_size() {
		return $this->size;
	}

}

class SHM_SYSV_Cache {

	private $id;
	private $res = false;
	private $expires = array();
	private $mtime = array();

	public function __construct( $size = 16 * 1024 * 1024 ) {
		$this->id = ftok( __FILE__, "\xff" );
		if ( ! $this->res = @ shm_attach( $this->id, $size, 0600 ) ) {
			$error = error_get_last();
			file_put_contents( __DIR__ . '/.SHM_SYSV_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_SYSV_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't attach SHM segment (key " . $this->id . "): " .
							   $error['message'] . "\n", FILE_APPEND );
		}
	}

	public function __destruct() {
		if ( $this->res !== false ) shm_detach( $this->res );
		return true;
	}

	public function get( $key, $group = 'default' ) {
		if ( $this->res === false ) return false;
		$group_key = $this->_get_group_key( $key, $group );
		$result = shm_get_var( $this->res, $this->_crc32( $group_key ) );
		if ( $result === false || ! is_array( $result ) || count ( $result ) != 3 ) return false;
		list( $data, $expire, $mtime ) = $result;
		$this->expires[ $group_key ] = $expire;
		$this->mtime[ $group_key ] = $mtime;
		return $data;
	}

	public function set( $key, $data, $group = 'default', $expire = 0 ) {
		if ( $this->res === false ) return false;
		$mtime = time();
		$group_key = $this->_get_group_key( $key, $group );
		$result = @ shm_put_var( $this->res, $this->_crc32( $group_key ), array( &$data, $expire, $mtime ) );
		if ( $result === false ) {
			$error = error_get_last();
			file_put_contents( __DIR__ . '/.SHM_SYSV_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_SYSV_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't set '$group_key' in SHM segment (key " . $this->id . "): " .
							   $error['message'] . "\n", FILE_APPEND );
			return false;
		}
		$this->mtime[ $group_key ] = $mtime;
		return $result;
	}

	public function delete( $key, $group = 'default' ) {
		if ( $this->res === false ) return false;
		$group_key = $this->_get_group_key( $key, $group );
		$result = shm_remove_var( $this->res, $this->_crc32( $group_key ) );
		if ( $result === false ) return false;
		$this->mtime[ $group_key ] = time();
		return $result;
	}

	private function _get_group_key( $key, $group = 'default' ) {
		return $group . ':' . $key;
	}

	private function _crc32( $str ) {
		return crc32( $str ) & 0xffffffff;
	}

}

class SHM_Partitioned_Cache {

	private $id;
	private $res = false;
	private $size = 0;
	private $partition = array();
	private $partition_table = null;
	private $partition_size = -1;
	private $block_size = 128;
	private $data_offset = -1;
	private $next = -1;
	private $cache = array();
	private $expires = array();
	private $mtime = array();
	private $now;

	public function __construct( $size = 16 * 1024 * 1024 ) {
		$this->now = time();
		$this->id = ftok( __FILE__, "\xff" );
		if ( ! $this->res = @ shmop_open( $this->id, 'c', 0600, $size ) ) {
			$error = error_get_last();
			file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't open SHM segment (key " . $this->get_id( true ) . ", size $size): " .
							   $error['message'] . "\n", FILE_APPEND );
		}
		else {
			$this->size = shmop_size( $this->res );
			$this->data_offset = (int) ceil( $this->size / 8 / $this->block_size ) * $this->block_size;
			// Partition table is in the first 1/8 of total SHM segment size.
			// Data begins directly after that.
			$this->read_partition_table();
		}
	}

	private function read_partition_table() {
		// Actual size of partition table is in first four bytes
		$result = @ shmop_read( $this->res, 0, 4 );
		if ( $result !== false ) {
			$this->partition_size = unpack( 'N', $result )[1];
			$this->partition_table = $this->partition_size ? shmop_read( $this->res, 4, $this->partition_size ) : '';
			// Get offset and length for last data chunk
			// (always last 8 bytes in partition table regardless of order)
			if ( $this->partition_size >= 8 ) {
				$start = unpack( 'N', substr( $this->partition_table, $this->partition_size - 8, 4 ) )[1];
				$count = unpack( 'N', substr( $this->partition_table, $this->partition_size - 4, 4 ) )[1];
			}
			else {
				$start = 0;
				$count = 0;
			}
			// Offset for next data chunk
			if ( ! $start ) $start = $this->data_offset;
			$this->next = $start + (int) ceil( $count / $this->block_size ) * $this->block_size;
		}
		else {
			$error = error_get_last();
			file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't read partition table from SHM segment (key " . $this->get_id( true ) . "): " .
							   $error['message'] . "\n", FILE_APPEND );
		}
	}

	public function __destruct() {
		if ( $this->res !== false && shmop_close( $this->res ) !== null ) {
			$error = error_get_last();
			file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't close SHM segment (key " . $this->get_id( true ) . "): " .
							   $error['message'] . "\n", FILE_APPEND );
		}
		return true;
	}

	public function clear() {
		if ( $this->res === false ) return false;

		if ( ! @ shmop_write( $this->res, "\0\0\0\0", 0 ) ) {
			$error = error_get_last();
			file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't set partition table size to zero in SHM segment (key " . $this->get_id( true ) . "): " .
							   $error['message'] . "\n", FILE_APPEND );
			return false;
		}

		$this->partition = array();
		$this->partition_table = '';
		$this->partition_size = 0;
		$this->next = $this->data_offset;
		$this->cache = array();
		$this->expires = array();
		$this->mtime = array();

		return true;
	}

	public function flush() {
		return $this->clear();
	}

	public function defrag() {
		if ( $this->res === false ) return false;

		$cache = $this->cache;
		//$expires = $this->expires;

		$this->clear();

		foreach ( $cache as $group => $entries ) {
			foreach ( $entries as $key => $data ) {
				list( $value, $expire, $mtime ) = $data;
				if ( ! $this->set( $key, $value, $group, $expire ) )
					return false;
			}
		}

		return true;
	}

	public function get( $key, $group = 'default' ) {
		if ( $this->res === false ) return false;

		//if ( isset( $this->cache[ $group ][ $key ] ) ) return $this->cache[ $group ][ $key ];

		//$this->cache[ $group ][ $key ] = false;

		$group_key = $this->_get_group_key( $key, $group );

		$partition_entry = $this->_get_partition_entry( $group_key );
		if ( $partition_entry === false ) return false;
		list( $pos, $start, $count ) = $partition_entry;
		if ( ! $count || $start + $count > $this->size ) return false;

		$result = @ shmop_read( $this->res, $start, $count );
		if ( $result === false ) {
			$error = error_get_last();
			file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't read '$group_key' from SHM segment (key " . $this->get_id( true ) . ") at offset $start: " .
							   $error['message'] . ". Deleting.\n", FILE_APPEND );
			$this->delete( $key, $group );
			return false;
		}
		$result = unserialize( $result );
		if ( $result === false || ! is_array( $result ) || count ( $result ) != 3 ) return false;

		list( $value, $expire, $mtime ) = $result;

		//$this->cache[ $group ][ $key ] = $result;
		//$this->expires[ $group ][ $key ] = $expire;
		//if ( ! isset( $this->mtime[ $group ] ) ||
		//	 $mtime > $this->mtime[ $group ] )
		//	$this->mtime[ $group ] = $mtime;

		return $result;
	}

	public function set( $key, $value, $group = 'default', $expire = 0 ) {
		if ( $this->res === false ) return false;

		$group_key = $this->_get_group_key( $key, $group );

		$mtime = time();
		$data = serialize( array( &$value, $this->now + $expire, $mtime ) );
		$data_len = strlen( $data );
		if ( is_array( $value ) || is_object( $value ) )
			$padded_len = (int) ceil( $data_len / $this->block_size ) * $this->block_size;
		else
			$padded_len = $data_len;

		$partition_entry = $this->_get_partition_entry( $group_key );
		if ( $partition_entry !== false ) {
			// Update existing partition entry
			list( $pos, $offset, $count ) = $partition_entry;
			if ( $offset + $count > $this->size ) $count = 0;
		}
		else {
			// Create new partition entry
			$pos = $this->partition_size;
			$count = 0;
		}

		if ( $padded_len > $count ) {
			// Create new partition entry or update existing
			$offset = $this->next;
			if ( $offset + $padded_len > $this->size ) {
				file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't write '$group_key' ($data_len bytes, padded $padded_len) to SHM segment (key " . $this->get_id( true ) . ") at offset $offset: Allocated space for data exceeded. Flushing cache.\n", FILE_APPEND );
				$this->flush();
				return false;
			}
			$data_offset_count = pack( 'N', $offset ) . pack( 'N', $data_len );
			$partition_size = $this->partition_size;
			if ( $pos == $this->partition_size ) {
				// This is a new partition entry, need to increase partition size
				$partition_size += strlen( $group_key ) + 16;
				if ( $partition_size >= $this->data_offset ) {
					file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
									   date( 'Y-m-d H:i:s,v' ) .
									   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't write partition table entry for '$group_key' to SHM segment (key " . $this->get_id( true ) . "): Allocated space for partition table exceeded. Flushing cache.\n", FILE_APPEND );
					$this->flush();
					return false;
				}
			}
			$this->next += $padded_len;
			if ( ! @ shmop_write( $this->res, $group_key . $data_offset_count, 4 + $pos ) ||
				 ! @ shmop_write( $this->res, $data_offset_count, 4 + $partition_size - 8 ) ) {
				$error = error_get_last();
				file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't write partition table entry for '$group_key' to SHM segment (key " . $this->get_id( true ) . "): " .
								   $error['message'] . "\n", FILE_APPEND );
				return false;
			}
			$this->partition[ $group_key ] = array( $pos, $offset, $data_len );
			if ( $pos == $this->partition_size ) {
				// This is a new partition entry, need to update partition size
				$this->partition_size = $partition_size;
				if ( ! @ shmop_write( $this->res, pack( 'N', $partition_size ), 0 ) ) {
					$error = error_get_last();
					file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
									   date( 'Y-m-d H:i:s,v' ) .
									   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't increase partition table size for '$group_key' in SHM segment (key " . $this->get_id( true ) . "): " .
									   $error['message'] . "\n", FILE_APPEND );
					return false;
				}
			}
		}

		$bytes_written = @ shmop_write( $this->res, $data, $offset );
		if ( $bytes_written === false ) {
			$error = error_get_last();
			file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't write '$group_key' ($data_len bytes) to SHM segment (key " . $this->get_id( true ) . ") at offset $offset: " .
							   $error['message'] . "\n", FILE_APPEND );
			return false;
		}

		//$this->mtime[ $group ] = $mtime;

		return $bytes_written !== false;
	}

	public function delete( $key, $group = 'default' ) {
		if ( $this->res === false ) return false;

		$group_key = $this->_get_group_key( $key, $group );

		$partition_entry = $this->_get_partition_entry( $group_key );
		if ( $partition_entry === false ) return false;
		list( $pos, $offset, $count ) = $partition_entry;

		// Set size to zero in partition table entry to mark as deleted
		if ( ! @ shmop_write( $this->res, "\0\0\0\0", 4 + $pos + strlen( $group_key ) + 4 ) ) {
			$error = error_get_last();
			file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't truncate partition table entry for '$group_key' in SHM segment (key " . $this->get_id( true ) . "): " .
							   $error['message'] . "\n", FILE_APPEND );
			return false;
		}

		$this->partition[ $group_key ] = false;
		//unset( $this->cache[ $group ][ $key ] );
		//unset( $this->expires[ $group ][ $key ] );
		//$this->mtime[ $group ] = time();

		return true;
	}

	public function get_size() {
		return $this->size;
	}

	public function get_groups() {
		$groups = array();

		if ( $this->res !== false ) {
			
		}

		return $groups;
	}

	public function get_id( $hex = false ) {
		return SHM_Cache::format_id( $this->id, $hex );
	}

	public function get_shm_id( $hex = false ) {
		return $this->res;
	}

	public function has_key( $key, $group = 'default', $fetch = true ) {
		if ( $this->res === false ) return false;

		$group_key = $this->_get_group_key( $key, $group );

		if ( ! $fetch ) return ! empty( $this->partition[ $group_key ] );

		$partition_entry = $this->_get_partition_entry( $group_key );
		if ( $partition_entry === false ) return false;
		list( $pos, $offset, $count ) = $partition_entry;

		return $count && $offset + $count <= $this->size;
	}

	public function stats() {
		echo "SHM_Partitioned_Cache stats\n";
		echo "===========================\n";
		echo "SHM key: " . $this->get_id( true ) . "\n";
		echo "Resource: " . $this->res . "\n";
		echo "Size: " . $this->size . " bytes (" . round( $this->size / 1024, 2 ) . " KiB)\n";
		echo "\nPartition table\n";
		echo "---------------\n";
		echo "Partition table size: " . $this->partition_size . " bytes (" . round( $this->partition_size / 1024, 2 ) . " KiB)\n";
		echo "Key data offset: " . $this->data_offset . " bytes (" . round( $this->data_offset / 1024, 2 ) . " KiB)\n";
		echo "Next free key data segment offset: " . $this->next . " bytes (" . round( $this->next / 1024, 2 ) . " KiB)\n";
		$time_start = microtime( true );
		$this->read_partition_table();
		echo "Time to re-read partition table for stats: " . round( microtime( true ) - $time_start, 3 ) . "s\n";
		$pos = strrpos( $this->partition_table, ':' );
		if ( $pos !== false ) {
			echo "Last added key partition table entry offset: " . $pos . " bytes\n";
			$key_len = $this->partition_size - 16 - $pos - 1;
			echo "Last added key: " . addcslashes( substr( $this->partition_table, $pos + 1, $key_len ), "\x00..\x19\x7e..\xff" ) . "\n";
			echo "Last added key data offset: " . unpack( 'N', substr( $this->partition_table, $pos + 1 + $key_len, 4 ) )[1] . " bytes\n";
			echo "Last added key data size: " . unpack( 'N', substr( $this->partition_table, $pos + 1 + $key_len + 4, 4 ) )[1] . " bytes\n";
			echo "Last changed key data offset: " . unpack( 'N', substr( $this->partition_table, $pos + 1 + $key_len + 8, 4 ) )[1] . " bytes\n";
			echo "Last changed key data size: " . unpack( 'N', substr( $this->partition_table, $pos + 1 + $key_len + 12, 4 ) )[1] . " bytes\n";
		}
		$last_added_partition_entry = end( $this->partition );
		if ( $last_added_partition_entry !== false ) {
			echo "\nAccessed partition entries for this request\n";
			echo "-------------------------------------------\n";
			list( $pos, $start, $count ) = $last_added_partition_entry;
			echo "Last added key partition table entry offset: " . $pos . " bytes\n";
			echo "Last added key: " . key( $this->partition ) . "\n";
		}
	}

	private function _get_group_key( $key, $group = 'default' ) {
		// Concatenate group and key, return the result
		return $group . ':' . $key;
	}

	private function _get_partition_entry( $group_key ) {
		// Return partition entry for group key. May be false if entry does
		// not exist or has been deleted.
		if ( isset( $this->partition[ $group_key ] ) ) return $this->partition[ $group_key ];
		$pos = strpos( $this->partition_table, $group_key );
		if ( $pos === false ) $partition_entry = false;
		else {
			// <group_key (variable length)><start (4 bytes)><count (4 bytes)>
			$offset = $pos + strlen( $group_key );
			$start = unpack( 'N', substr( $this->partition_table, $offset, 4 ) )[1];
			$count = unpack( 'N', substr( $this->partition_table, $offset + 4, 4 ) )[1];
			$partition_entry = array( $pos, $start, $count );
		}
		$this->partition[ $group_key ] = $partition_entry;
		return $partition_entry;
	}

}

if ( ! function_exists( 'ftok' ) ) {  // Windows

	function ftok($pathname, $proj_id) {
		$st = @stat($pathname);
		if (!$st) {
			return -1;
		}

		$key = sprintf("%u", (($st['ino'] & 0xffff) | (($st['dev'] & 0xff) << 16) | (($proj_id & 0xff) << 24)));
		return $key;
	}

}

/**
 * WordPress Object Cache
 *
 * The WordPress Object Cache is used to save on trips to the database. The
 * Object Cache stores all of the cache data to memory and makes the cache
 * contents available by using a key, which is used to name and later retrieve
 * the cache contents.
 *
 * The Object Cache can be replaced by other caching mechanisms by placing files
 * in the wp-content folder which is looked at in wp-settings. If that file
 * exists, then this file will not be included.
 *
 * @package WordPress
 * @subpackage Cache
 * @since 2.0.0
 */
class WP_Object_Cache {

	/* File-based object cache start */
	private $cache_dir;
	private $flock_filename = '.lock';
	private $mutex;
	private $deleted = array();
	private $dirty_groups = array();
	private $non_persistent_groups = array('bp_notifications' => true,
										   'bp_messages' => true,
										   'bp_notifications_grouped_notifications' => true,
										   'bp_notifications_unread_count' => true,
										   'bp_messages_threads' => true,
										   'bp_messages_unread_count' => true,
										   'notification_meta' => true,
										   'message_meta' => true);
	private $expires = array();
	private $expirations = 0;
	private $expirations_groups = array();
	private $mtime = array();
	private $ajax;
	private $cron;
	private $skip;
	private $file_cache_reads = 0;
	private $file_cache_misses = 0;
	private $file_cache_expirations = 0;
	private $file_cache_hits = 0;
	private $cache_hits_groups = array();
	private $file_cache_hits_groups = array();
	private $cache_misses_groups = array();
	private $file_cache_groups = array();
	private $file_cache_errors_groups = array();
	private $file_cache_persist_errors_groups = array();
	private $actual_persists = 0;
	private $persists = 0;
	private $cache_deletions = 0;
	private $cache_deletions_groups = array();
	private $resets = 0;
	private $flushes = 0;
	private $debug;
	private $time_disk_read = 0;
	private $time_disk_write = 0;
	private $time_read = 0;
	private $time_shm_read = 0;
	private $time_total = 0;
	private $now;
	private $expiration_time = 0;
	private $shm_enable = false;
	private $shm = array();
	public $cache_writes = 0;
	/* File-based object cache end */

	/**
	 * Holds the cached objects
	 *
	 * @var array
	 * @access private
	 * @since 2.0.0
	 */
	private $cache = array();

	/**
	 * The amount of times the cache data was already stored in the cache.
	 *
	 * @since 2.5.0
	 * @access public
	 * @var int
	 */
	public $cache_hits = 0;

	/**
	 * Amount of times the cache did not have the request in cache
	 *
	 * @var int
	 * @access public
	 * @since 2.0.0
	 */
	public $cache_misses = 0;

	/**
	 * List of global groups
	 *
	 * @var array
	 * @access protected
	 * @since 3.0.0
	 */
	protected $global_groups = array();

	/**
	 * The blog prefix to prepend to keys in non-global groups.
	 *
	 * @var int
	 * @access private
	 * @since 3.5.0
	 */
	private $blog_prefix;

	/**
	 * Holds the value of `is_multisite()`
	 *
	 * @var bool
	 * @access private
	 * @since 3.5.0
	 */
	private $multisite;

	/**
	 * Make private properties readable for backwards compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $name Property to get.
	 * @return mixed Property.
	 */
	public function __get( $name ) {
		return $this->$name;
	}

	/**
	 * Make private properties settable for backwards compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $name  Property to set.
	 * @param mixed  $value Property value.
	 * @return mixed Newly-set property.
	 */
	public function __set( $name, $value ) {
		return $this->$name = $value;
	}

	/**
	 * Make private properties checkable for backwards compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $name Property to check if set.
	 * @return bool Whether the property is set.
	 */
	public function __isset( $name ) {
		return isset( $this->$name );
	}

	/**
	 * Make private properties un-settable for backwards compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $name Property to unset.
	 */
	public function __unset( $name ) {
		unset( $this->$name );
	}

	/**
	 * Adds data to the cache if it doesn't already exist.
	 *
	 * @uses WP_Object_Cache::_exists Checks to see if the cache already has data.
	 * @uses WP_Object_Cache::set Sets the data after the checking the cache
	 *		contents existence.
	 *
	 * @since 2.0.0
	 *
	 * @param int|string $key What to call the contents in the cache
	 * @param mixed $data The contents to store in the cache
	 * @param string $group Where to group the cache contents
	 * @param int $expire When to expire the cache contents
	 * @return bool False if cache key and group already exist, true on success
	 */
	public function add( $key, $data, $group = 'default', $expire = 0 ) {
		if ( wp_suspend_cache_addition() )
			return false;

		if ( empty( $group ) )
			$group = 'default';

		$id = $key;
		if ( $this->multisite && ! isset( $this->global_groups[ $group ] ) )
			$id = $this->blog_prefix . $key;

		if ( $this->_exists( $id, $group ) )
			return false;

		return $this->set( $key, $data, $group, (int) $expire );
	}

	/**
	 * Sets the list of global groups.
	 *
	 * @since 3.0.0
	 *
	 * @param array $groups List of groups that are global.
	 */
	public function add_global_groups( $groups ) {
		$groups = (array) $groups;

		$groups = array_fill_keys( $groups, true );
		$this->global_groups = array_merge( $this->global_groups, $groups );
	}

	/**
	 * Decrement numeric cache item's value
	 *
	 * @since 3.3.0
	 *
	 * @param int|string $key The cache key to increment
	 * @param int $offset The amount by which to decrement the item's value. Default is 1.
	 * @param string $group The group the key is in.
	 * @return false|int False on failure, the item's new value on success.
	 */
	public function decr( $key, $offset = 1, $group = 'default' ) {
		if ( empty( $group ) )
			$group = 'default';

		if ( $this->multisite && ! isset( $this->global_groups[ $group ] ) )
			$key = $this->blog_prefix . $key;

		if ( ! $this->_exists( $key, $group ) )
			return false;

		if ( ! is_numeric( $this->cache[ $group ][ $key ] ) )
			$this->cache[ $group ][ $key ] = 0;

		$offset = (int) $offset;

		$this->cache[ $group ][ $key ] -= $offset;

		if ( $this->cache[ $group ][ $key ] < 0 )
			$this->cache[ $group ][ $key ] = 0;

		/* File-based object cache start */
        if ($this->debug) $time_start = microtime(true);
        if ($this->shm_enable === 2) {
			if ($this->debug) $time_write_start = microtime(true);
			$this->shm->set( $key, $this->cache[ $group ][ $key ], $group );
			$this->time_disk_write += microtime(true) - $time_write_start;
		}
		$this->dirty_groups[$group] = true;
        $this->file_cache_groups[$group][$key] = false;
		$this->mtime[$group] = time();
		$this->_check_persist($key, $group);
		if ($this->debug) $this->time_total += microtime(true) - $time_start;
		/* File-based object cache end */

		return $this->cache[ $group ][ $key ];
	}

	/**
	 * Remove the contents of the cache key in the group
	 *
	 * If the cache key does not exist in the group, then nothing will happen.
	 *
	 * @since 2.0.0
	 *
	 * @param int|string $key What the contents in the cache are called
	 * @param string $group Where the cache contents are grouped
	 * @param bool $deprecated Deprecated.
	 *
	 * @return bool False if the contents weren't deleted and true on success
	 */
	public function delete( $key, $group = 'default', $deprecated = false ) {
		if ( empty( $group ) )
			$group = 'default';

		if ( $this->multisite && ! isset( $this->global_groups[ $group ] ) )
			$key = $this->blog_prefix . $key;

		if ( ! $this->_exists( $key, $group ) )
			return false;

		/* File-based object cache start */
        if ($this->debug) $time_start = microtime(true);
		if (!isset($this->dirty_groups[$group]))
			$this->dirty_groups[$group] = true;
        if ($this->debug) $this->time_total += microtime(true) - $time_start;
		/* File-based object cache end */
		unset( $this->cache[$group][$key] );
		/* File-based object cache start */
        if ($this->debug) $time_start = microtime(true);
        if ($this->shm_enable === 2) {
			if ($this->debug) $time_write_start = microtime(true);
			$this->shm->delete( $key, $group );
			$this->time_disk_write += microtime(true) - $time_write_start;
		}
        $this->deleted[$group][$key] = true;
		unset( $this->expires[$group][$key] );
        $this->file_cache_groups[$group][$key] = false;
		$this->mtime[$group] = time();
		$this->_check_persist($key, $group);
		$this->cache_deletions += 1;
		if ($this->debug) {
			if (!isset($this->cache_deletions_groups[$group]))
				$this->cache_deletions_groups[$group] = 1;
			else
				$this->cache_deletions_groups[$group] += 1;
			$this->time_total += microtime(true) - $time_start;
		}
		/* File-based object cache end */
		return true;
	}

	/**
	 * Clears the object cache of all data
	 *
	 * @since 2.0.0
	 *
	 * @return true Always returns true
	 */
	public function flush() {
		/* File-based object cache start */
        if ($this->debug) $time_start = microtime(true);

		if ( ! $this->acquire_lock() ) {
			if ($this->debug) $this->time_total += microtime(true) - $time_start;
			return false;
		}

		if ($this->shm_enable === 2) $this->shm->clear();
		else foreach ($this->shm as $group => $shm) $shm->clear();

		$dh = @ opendir($this->cache_dir);
		if (!$dh) {
			if ($this->debug) $this->time_total += microtime(true) - $time_start;
			return false;
		}

		while (($file = @ readdir($dh)) !== false) {
			if (substr($file, -4) == '.php' && @ is_file($this->cache_dir . $file))
				@ unlink($this->cache_dir . $file);
		}

		$this->release_lock();
		$this->deleted = array();
		$this->dirty_groups = array();
		$this->flushes += 1;
		if ($this->debug) $this->time_total += microtime(true) - $time_start;
		/* File-based object cache end */

		$this->cache = array();

		return true;
	}

	/**
	 * Retrieves the cache contents, if it exists
	 *
	 * The contents will be first attempted to be retrieved by searching by the
	 * key in the cache group. If the cache is hit (success) then the contents
	 * are returned.
	 *
	 * On failure, the number of cache misses will be incremented.
	 *
	 * @since 2.0.0
	 *
	 * @param int|string $key What the contents in the cache are called
	 * @param string $group Where the cache contents are grouped
	 * @param string $force Whether to force a refetch from the persistent cache rather than relying on the local cache (default is false)
	 * @return false|mixed False on failure to retrieve contents or the cache
	 *		               contents on success
	 */
	public function get( $key, $group = 'default', $force = false, &$found = null ) {
		if ( empty( $group ) )
			$group = 'default';

		/* File-based object cache start */
		$id = $key;
		/* File-based object cache end */
		if ( $this->multisite && ! isset( $this->global_groups[ $group ] ) )
			$key = $this->blog_prefix . $key;

		/* File-based object cache start */
        if ($this->debug) $time_start = microtime(true);
        $is_persistent_group = !isset($this->non_persistent_groups[$group]);
		if ($force && $is_persistent_group) {
			$this->_log("FORCE REFETCH FROM PERSISTENT CACHE FOR $group.$key");
			if ($this->_exists( $key, $group ))
				$this->_log("BEFORE REFETCH: $group.$key = " . json_encode($this->cache[$group][$key], JSON_PRETTY_PRINT));
		}
		if ($is_persistent_group &&
			($force ||
			 (!$this->skip &&
			  ($this->shm_enable === 2 ?
			   !isset($this->file_cache_groups[$group][$key]) :
			   !isset($this->file_cache_groups[$group]))))) {
			$this->file_cache_reads += 1;
			if ($this->shm_enable === 2) {
				if ($this->debug) $time_shm_read_start = microtime(true);
				$result = $this->shm->get($key, $group);
				if ($this->debug) {
					$this->time_shm_read += microtime(true) - $time_shm_read_start;
					$this->time_read = $this->time_shm_read;
				}
				if ($result !== false) {
					list($value, $expire, $mtime) = $result;
					if ($expire > $this->now) {
						$this->cache[$group][$key] = $value;
						$this->expires[$group][$key] = $expire;
						$this->file_cache_groups[$group][$key] = true;
						if (!isset($this->mtime[$group]) ||
							$mtime > $this->mtime[$group]) $this->mtime[$group] = $mtime;
					}
					else {
						$this->file_cache_groups[$group][$key] = false;
						$this->file_cache_expirations += 1;
						$this->expirations += 1;
					}
				}
				else {
					$this->file_cache_groups[$group][$key] = false;
					$this->file_cache_misses += 1;
				}
			}
			else if ($this->_group_exists($group)) {
				if ($this->debug) $time_read_start = microtime(true);
				$this->file_cache_groups[$group] = unserialize($this->_get_group($group));
				if ($this->debug) $this->time_read += microtime(true) - $time_read_start;
				if ($force)
					$this->_log("PERSISTENT CACHE: $group.$key = " . json_encode($this->file_cache_groups[$group][$key], JSON_PRETTY_PRINT));
				if (false === $this->file_cache_groups[$group]) {
					$this->file_cache_errors_groups[$group] = true;
					$this->file_cache_groups[$group] = array();
					$this->file_cache_misses += 1;
				}
				else {
					if (!$force && isset($this->deleted[$group])) foreach ($this->deleted[$group] as $deleted => $value) unset($this->file_cache_groups[$group][$deleted]);
					if (isset($this->cache[$group])) {
						$this->cache[$group] = array_replace($this->file_cache_groups[$group], $this->cache[$group]);
						if ($force) $this->cache[$group][$key] = $this->file_cache_groups[$group][$key];
					}
					else $this->cache[$group] = $this->file_cache_groups[$group];
					$this->file_cache_groups[$group] = array_fill_keys(array_keys($this->file_cache_groups[$group]), true);
					$this->mtime[$group] = $this->_mtime($group);
					if ($group == 'options' && $id == 'alloptions')
						$this->_log("GET $group.$key\n");
				}
			}
			else {
				$this->file_cache_groups[$group] = array();
				$this->file_cache_misses += 1;
			}
		}
		if ($this->debug) $this->time_total += microtime(true) - $time_start;
		/* File-based object cache end */

		if ( $this->_exists( $key, $group )/* File-based object cache start */ && ! $this->_expire( $key, $group ) /* File-based object cache end */) {
			$found = true;
			$this->cache_hits += 1;
			/* File-based object cache start */
			if ($force && $is_persistent_group)
				$this->_log("AFTER REFETCH: $group.$key = " . json_encode($this->cache[$group][$key], JSON_PRETTY_PRINT));
			if ($this->debug) {
				$time_start = microtime(true);
				if (!isset($this->cache_hits_groups[$group]))
					$this->cache_hits_groups[$group] = 1;
				else
					$this->cache_hits_groups[$group] += 1;
				if (!empty($this->file_cache_groups[$group][$key])) {
					$this->file_cache_hits += 1;
					if (!isset($this->file_cache_hits_groups[$group]))
						$this->file_cache_hits_groups[$group] = 1;
					else
						$this->file_cache_hits_groups[$group] += 1;
				}
				$this->time_total += microtime(true) - $time_start;
			}
			/* File-based object cache end */
			if ( is_object($this->cache[$group][$key]) )
				return clone $this->cache[$group][$key];
			else
				return $this->cache[$group][$key];
		}

		/* File-based object cache start */
        if ($this->debug) {
			$time_start = microtime(true);
			if (!isset($this->cache_misses_groups[$group]))
				$this->cache_misses_groups[$group] = 1;
			else
				$this->cache_misses_groups[$group] += 1;
			$this->time_total += microtime(true) - $time_start;
		}
		/* File-based object cache end */

		$found = false;
		$this->cache_misses += 1;
		return false;
	}

	/* File-based object cache start */

	/**
	 * Retrieves the group contents, if it exists
	 *
	 * @param string $group Where the cache contents are grouped
	 * @return mixed Group contents on success
	 */
	private function _get_group( $group = 'default' ) {
		if ( $this->shm_enable ) {
			if ($this->debug) $time_shm_read_start = microtime(true);
			if ( ! isset( $this->shm[$group] ) ) $this->shm[$group] = new SHM_Cache( $group );
			$data = $this->shm[$group]->get();
			if ($this->debug) $this->time_shm_read += microtime(true) - $time_shm_read_start;
			if ( $data !== false ) return $data;
		}
		if ($this->debug) $time_disk_read_start = microtime(true);
		$cache_file = $this->cache_dir.$group.'.php';
		$data = @ file_get_contents($cache_file);
		if ($this->debug) $this->time_disk_read += microtime(true) - $time_disk_read_start;
		return substr($data, strlen(CACHE_SERIAL_HEADER), -strlen(CACHE_SERIAL_FOOTER));
	}

	private function _mtime( $group = 'default' ) {
		if ( $this->shm_enable ) {
			if ( ! isset( $this->shm[$group] ) ) $this->shm[$group] = new SHM_Cache( $group );
			return $this->shm[$group]->mtime();
		}
		$cache_file = $this->cache_dir.$group.'.php';
		return filemtime($cache_file);
	}

	/* File-based object cache end */

	/**
	 * Increment numeric cache item's value
	 *
	 * @since 3.3.0
	 *
	 * @param int|string $key The cache key to increment
	 * @param int $offset The amount by which to increment the item's value. Default is 1.
	 * @param string $group The group the key is in.
	 * @return false|int False on failure, the item's new value on success.
	 */
	public function incr( $key, $offset = 1, $group = 'default' ) {
		if ( empty( $group ) )
			$group = 'default';

		if ( $this->multisite && ! isset( $this->global_groups[ $group ] ) )
			$key = $this->blog_prefix . $key;

		if ( ! $this->_exists( $key, $group ) )
			return false;

		if ( ! is_numeric( $this->cache[ $group ][ $key ] ) )
			$this->cache[ $group ][ $key ] = 0;

		$offset = (int) $offset;

		$this->cache[ $group ][ $key ] += $offset;

		if ( $this->cache[ $group ][ $key ] < 0 )
			$this->cache[ $group ][ $key ] = 0;

		/* File-based object cache start */
        if ($this->debug) $time_start = microtime(true);
        if ($this->shm_enable === 2) {
			if ($this->debug) $time_write_start = microtime(true);
			$this->shm->set( $key, $this->cache[ $group ][ $key ], $group );
			$this->time_disk_write += microtime(true) - $time_write_start;
		}
		$this->dirty_groups[$group] = true;
        $this->file_cache_groups[$group][$key] = false;
		$this->mtime[$group] = time();
		$this->_check_persist($key, $group);
		if ($this->debug) $this->time_total += microtime(true) - $time_start;
		/* File-based object cache end */

		return $this->cache[ $group ][ $key ];
	}

	/**
	 * Replace the contents in the cache, if contents already exist
	 *
	 * @since 2.0.0
	 * @see WP_Object_Cache::set()
	 *
	 * @param int|string $key What to call the contents in the cache
	 * @param mixed $data The contents to store in the cache
	 * @param string $group Where to group the cache contents
	 * @param int $expire When to expire the cache contents
	 * @return bool False if not exists, true if contents were replaced
	 */
	public function replace( $key, $data, $group = 'default', $expire = 0 ) {
		if ( empty( $group ) )
			$group = 'default';

		$id = $key;
		if ( $this->multisite && ! isset( $this->global_groups[ $group ] ) )
			$id = $this->blog_prefix . $key;

		if ( ! $this->_exists( $id, $group ) )
			return false;

		return $this->set( $key, $data, $group, (int) $expire );
	}

	/**
	 * Reset keys
	 *
	 * @since 3.0.0
	 * @deprecated 3.5.0
	 */
	public function reset() {
		_deprecated_function( __FUNCTION__, '3.5', 'switch_to_blog()' );

		// Clear out non-global caches since the blog ID has changed.
		foreach ( array_keys( $this->cache ) as $group ) {
			if ( ! isset( $this->global_groups[ $group ] ) ) {
				unset( $this->cache[ $group ] );

				/* File-based object cache start */
				if ($this->debug) $time_start = microtime(true);
				$this->dirty_groups[$group] = true;
				unset( $this->deleted[$group] );
				unset( $this->expires[$group] );
				unset($this->file_cache_groups[$group]);
				if ($this->debug) $this->time_total += microtime(true) - $time_start;
				/* File-based object cache end */
			}
		}

		/* File-based object cache start */
		$this->resets += 1;
		/* File-based object cache end */
	}

	/**
	 * Sets the data contents into the cache
	 *
	 * The cache contents is grouped by the $group parameter followed by the
	 * $key. This allows for duplicate ids in unique groups. Therefore, naming of
	 * the group should be used with care and should follow normal function
	 * naming guidelines outside of core WordPress usage.
	 *
	 * @since 2.0.0
	 *
	 * @param int|string $key What to call the contents in the cache
	 * @param mixed $data The contents to store in the cache
	 * @param string $group Where to group the cache contents
	 * @param int $expire Expiration time in seconds
	 * @return true Always returns true
	 */
	public function set( $key, $data, $group = 'default', $expire = 0 ) {
		if ( empty( $group ) )
			$group = 'default';

		if ( $this->multisite && ! isset( $this->global_groups[ $group ] ) )
			$key = $this->blog_prefix . $key;

		if ( is_object( $data ) )
			$data = clone $data;

		/* File-based object cache start */
		if ($this->debug) $time_start = microtime(true);
		if (!isset($this->dirty_groups[$group])) {
			$exists = $this->_exists($key, $group);
			$is_complex = $exists && ( is_object( $this->cache[$group][$key] ) || is_array( $this->cache[$group][$key] ) );
			if (!$exists ||
				(!$is_complex && $this->cache[$group][$key] != $data) ||
				($is_complex && serialize($this->cache[$group][$key]) != serialize($data))) {
					$this->dirty_groups[$group] = true;
					$this->mtime[$group] = time();
				}
		}
        if ($this->debug) $this->time_total += microtime(true) - $time_start;
		/* File-based object cache end */
		$this->cache[$group][$key] = $data;
		/* File-based object cache start */
		if ($this->debug) $time_start = microtime(true);
		$this->cache_writes ++;
		if (!$expire) $expire = $this->expiration_time;
        if ($this->shm_enable === 2) {
			if ($this->debug) $time_write_start = microtime(true);
			$this->shm->set($key, $data, $group, $expire);
			$this->time_disk_write += microtime(true) - $time_write_start;
		}
		if ($expire) $this->expires[$group][$key] = $this->now + $expire;
		unset($this->deleted[$group][$key]);
        $this->file_cache_groups[$group][$key] = false;
		$this->_check_persist($key, $group);
		if ($this->debug) $this->time_total += microtime(true) - $time_start;
		/* File-based object cache end */
		return true;
	}

	/**
	 * Echoes the stats of the caching.
	 *
	 * Gives the cache hits, and cache misses. Also prints every cached group,
	 * key and the data.
	 *
	 * @since 2.0.0
	 */
	public function stats() {
		/* File-based object cache start */
		if ( ! $this->debug ) {
			echo "<p>Define FH_OBJECT_CACHE_DEBUG for additional stats</p>";
		}
		echo "<p>";
		$hours = floor( $this->expiration_time / 60 / 60 );
		$minutes = floor( ( $this->expiration_time - $hours * 60 * 60 ) / 60 );
		$seconds = $this->expiration_time - $hours * 60 * 60 - $minutes * 60;
		echo "<strong>Cache Lifetime (if unspecified for entry):</strong> $hours hours $minutes minutes $seconds soconds<br />";
		echo "<strong>Persistent Cache Reads:</strong> {$this->file_cache_reads} ({$this->file_cache_misses} misses, {$this->file_cache_expirations} expired)<br />";
		echo "<strong>Cache Hits:</strong> {$this->cache_hits}";
		if ( $this->debug ) echo " ({$this->file_cache_hits} from persistent cache)";
		echo "</p>";
		echo '<table border="1" style="border-collapse: collapse"><tr><th style="padding: .1em .3em">Group</th><th style="padding: .1em .3em">Hits</th><th style="padding: .1em .3em">From Persistent Cache</th><th style="padding: .1em .3em">Misses</th><th style="padding: .1em .3em">SHM</th><th style="padding: .1em .3em">Freshness</th><th style="padding: .1em .3em">Persist</th><th style="padding: .1em .3em">Global</th><th style="padding: .1em .3em">Entries</th><th style="padding: .1em .3em">Expired</th><th style="padding: .1em .3em">Deleted</th><th style="padding: .1em .3em">Size (KiB)</th></tr>';
		$total_entries = 0;
		$total_size = 0;
		foreach ($this->cache as $group => $cache) {
			$cache_hits_groups = isset($this->cache_hits_groups[$group]) ? $this->cache_hits_groups[$group] : ( $this->debug ? 0 : 'N/A' );
			$file_cache_hits_groups = isset($this->file_cache_hits_groups[$group]) ? $this->file_cache_hits_groups[$group] : ( $this->debug ? 0 : 'N/A' );
			$cache_misses_groups = isset($this->cache_misses_groups[$group]) ? $this->cache_misses_groups[$group] : ( $this->debug ? 0 : 'N/A' );
			$shm = ($this->shm_enable === 2 ? !empty($this->file_cache_groups[$group]) : isset($this->shm[$group])) ? 'Yes' : 'No';
			$updated = isset($this->dirty_groups[$group]) ? 'Now' : (isset($this->mtime[$group]) ? human_time_diff( $this->mtime[$group] ) : 'Unknown');
			$persist = isset($this->non_persistent_groups[$group]) ? 'No' : 'Yes';
			$global = isset($this->global_groups[$group]) ? 'Yes' : 'No';
			$entries = count($cache);
			$total_entries += $entries;
			$expired = isset($this->expirations_groups[$group]) ? $this->expirations_groups[$group] : ( $this->debug ? 0 : 'N/A' );
			$deleted = isset($this->cache_deletions_groups[$group]) ? $this->cache_deletions_groups[$group] : ( $this->debug ? 0 : 'N/A' );
			$size = strlen( serialize( $cache ) ) / 1024;
			$total_size += $size;
			echo "<tr style='" . ($persist === "No" ? "opacity: .5;" : "") . "'><td style='" . ($global === "Yes" ? "font-style: oblique;" : "") . "padding: .1em .3em'>$group</td><td style='padding: .1em .3em'>$cache_hits_groups</td><td style='padding: .1em .3em'>$file_cache_hits_groups</td><td style='padding: .1em .3em'>$cache_misses_groups</td><td style='padding: .1em .3em'>$shm</td><td style='padding: .1em .3em'>$updated</td><td style='padding: .1em .3em'>$persist</td><td style='padding: .1em .3em'>$global</td><td style='padding: .1em .3em'>$entries</td><td style='padding: .1em .3em'>$expired</td><td style='padding: .1em .3em'>$deleted</td><td style='padding: .1em .3em'>" . number_format( $size, 2 ) . "</td></tr>";
		}
		echo '</table>';
		echo "<p>";
		echo "<strong>Cache Writes:</strong> {$this->cache_writes}<br />";
		echo "<strong>Expired Cache Entries:</strong> {$this->expirations}<br />";
		echo "<strong>Deleted Cache Entries:</strong> {$this->cache_deletions}<br />";
		echo "<strong>Remaining Cache Entries:</strong> $total_entries<br />";
		$overhead = strlen(str_repeat(CACHE_SERIAL_HEADER . CACHE_SERIAL_FOOTER, count($this->cache))) / 1024;
		echo "<strong>Cache Size:</strong> " . number_format( $total_size, 2 ) . " KiB (" . number_format( $total_size + $overhead, 2 ) . " KiB with overhead)<br />";
		if ( $this->debug ) {
			echo "<strong>Persistent Cache Overall Performance:</strong> " . number_format( $this->time_total, 3) . "s<br />";
			echo "<strong>Persistent Cache Disk Read Performance:</strong> " . number_format( $this->time_disk_read, 3) . "s<br />";
			echo "<strong>Persistent Cache SHM Read Performance:</strong> " . number_format( $this->time_shm_read, 3) . "s<br />";
			echo "<strong>Persistent Cache Total Read &amp; Parse Performance:</strong> " . number_format( $this->time_read, 3) . "s<br />";
			echo "<strong>Persistent Cache Write Performance:</strong> " . number_format( $this->time_disk_write, 3) . "s";
		}
		echo "</p>";
		echo "<p>";
		echo "<strong>Cache Misses:</strong> {$this->cache_misses}<br />";
		echo "</p>";
		if ( ! empty( $this->cache_misses_groups ) ) {
			echo '<table border="1" style="border-collapse: collapse"><tr><th style="padding: .1em .3em">Group</th><th style="padding: .1em .3em">Misses</th></tr>';
			foreach ($this->cache_misses_groups as $group => $count) {
				echo "<tr style='" . (isset($this->non_persistent_groups[$group]) ? "opacity: .5;" : "") . "'><td style='" . (isset($this->global_groups[$group]) ? "font-style: oblique;" : "") . "padding: .1em .3em'>$group</td><td style='padding: .1em .3em'>$count</td></tr>";
			}
			echo '</table>';
		}
		echo "<p>";
		echo "<strong>Cache Persists:</strong> {$this->actual_persists} ({$this->persists} calls)<br />";
		echo "<strong>Cache Flushes:</strong> {$this->flushes}<br />";
		echo "<strong>Cache Resets (deprecated):</strong> {$this->resets}";
		echo "</p>";
		echo "<p>";
		echo "<strong>Global Groups:</strong> <span style='font-style: oblique'>" . implode(', ', array_keys($this->global_groups)) . "</span><br />";
		echo "<strong>Non-Persistent Groups:</strong> <span style='opacity: .5'>" . implode(', ', array_keys($this->non_persistent_groups)) . "</span><br />";
		if (!empty($this->file_cache_errors_groups)) echo "<strong>File Cache Read Errors:</strong> " . implode(', ', array_keys($this->file_cache_errors_groups));
		if (!empty($this->file_cache_persist_errors_groups)) echo "<strong>File Cache Write Errors:</strong> " . implode(', ', array_keys($this->file_cache_persist_errors_groups));
		echo "</p>";
		if ($this->shm_enable === 2) {
			echo "<pre>";
			$this->shm->stats();
			echo "</pre>";
		}
		/* File-based object cache end */

		/* OPcache */
		if ( defined( 'FH_OBJECT_CACHE_OPCACHE_DEBUG' ) ) {
			echo "<pre>OPcache\n";
			echo "=======\n";
			if (function_exists('opcache_get_configuration')) {
				$conf = opcache_get_configuration();

				ob_start();
				var_dump($conf);
				$contents = preg_replace("/=>\s+/", "=>", ob_get_contents());
				ob_end_clean();
				echo "OPcache configuration: " . $contents . "\n";
			}
			if (function_exists('opcache_get_status')) {
				$status = opcache_get_status(false);

				ob_start();
				var_dump($status);
				$contents = preg_replace("/=>\s+/", "=>", ob_get_contents());
				ob_end_clean();
				echo "OPcache status: " . $contents . "\n";
			}
			echo "</pre>\n";
		}
	}

	/**
	 * Switch the interal blog id.
	 *
	 * This changes the blog id used to create keys in blog specific groups.
	 *
	 * @since 3.5.0
	 *
	 * @param int $blog_id Blog ID
	 */
	public function switch_to_blog( $blog_id ) {
		$blog_id = (int) $blog_id;
		$this->blog_prefix = $this->multisite ? $blog_id . ':' : '';
	}

	/**
	 * Utility function to determine whether a key exists in the cache.
	 *
	 * @since 3.4.0
	 *
	 * @access protected
	 * @param string $key
	 * @param string $group
	 * @return bool
	 */
	protected function _exists( $key, $group, $cache = null ) {
		/* File-based object cache start */
		if ($cache === null) $cache = &$this->cache;
		/* File-based object cache end */
		return isset( $cache[ $group ] ) && ( isset( $cache[ $group ][ $key ] ) || array_key_exists( $key, $cache[ $group ] ) );
	}

	/* File-based object cache start */

	/**
	 * Utility function to determine whether a group exists in the underlying cache.
	 *
	 * @access protected
	 * @param string $group
	 * @return bool
	 */
	private function _group_exists( $group ) {
		if ( $this->shm_enable ) {
			if ( ! isset( $this->shm[$group] ) ) $this->shm[$group] = new SHM_Cache( $group );
			if ( $this->shm[$group]->open() !== false ) return true;
		}
		$cache_file = $this->cache_dir.$group.'.php';
		return is_file($cache_file);
	}

	/* File-based object cache end */

	/**
	 * Sets up object properties; PHP 5 style constructor
	 *
	 * @since 2.0.8
	 */
	public function __construct() {
		$this->multisite = is_multisite();
		$this->blog_prefix =  $this->multisite ? get_current_blog_id() . ':' : '';

		/* File-based object cache start */
		$this->debug = defined('FH_OBJECT_CACHE_DEBUG') ? FH_OBJECT_CACHE_DEBUG : 0;
		$this->expiration_time = defined('FH_OBJECT_CACHE_LIFETIME') ? FH_OBJECT_CACHE_LIFETIME : 60 * 3;
        if ($this->debug) $time_start = microtime(true);
		if (defined('FH_OBJECT_CACHE_PATH'))
			$this->cache_dir = FH_OBJECT_CACHE_PATH;
		else
			// Using the correct separator eliminates some cache flush errors on Windows
			$this->cache_dir = ABSPATH.'wp-content'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'fh-object-cache'.DIRECTORY_SEPARATOR;
		if (defined('FH_OBJECT_CACHE_SHM') && function_exists( 'shmop_open' ))
			$this->shm_enable = FH_OBJECT_CACHE_SHM;

		$this->ajax = defined('DOING_AJAX') && DOING_AJAX;
		$this->cron = defined('DOING_CRON') && DOING_CRON;
		// Skip reading from persistent cache if POST, but not if AJAX or CRON
		$this->skip = false;  // (!empty($_SERVER['QUERY_STRING']) || $_SERVER['REQUEST_METHOD'] == 'POST') && !($this->ajax || $this->cron);
		$this->now = time();

		$this->_log($_SERVER['REQUEST_URI']);
		if ($this->ajax) $this->_log("DOING_AJAX");
		if ($this->cron) $this->_log("DOING_CRON");

		if ( $this->shm_enable === 2 ) {
			$this->shm = new SHM_Partitioned_Cache( defined( 'FH_OBJECT_CACHE_SHM_SIZE' ) ? FH_OBJECT_CACHE_SHM_SIZE : 16 * 1024 * 1024 );
			$this->non_persistent_groups = array();
		}
		else {
			$this->_set_expires();

			add_action( 'edit_post', array( &$this, 'flush_taxonomies' ), 10, 1 );
		}

		if ($this->debug) $this->time_total += microtime(true) - $time_start;
		/* File-based object cache end */

		/**
		 * @todo This should be moved to the PHP4 style constructor, PHP5
		 * already calls __destruct()
		 */
		register_shutdown_function( array( $this, '__destruct' ) );
	}

	/* File-based object cache start */

	protected function _set_expires() {
		if ($this->_group_exists('.expires')) {
			$this->expires = unserialize($this->_get_group('.expires'));
			if ($this->expires === false) $this->expires = array();
		}
	}

	/* File-based object cache end */

	/**
	 * Will save the object cache before object is completely destroyed.
	 *
	 * Called upon object destruction, which should be when PHP ends.
	 *
	 * @since  2.0.8
	 *
	 * @return true True value. Won't be used by PHP
	 */
	public function __destruct() {
		/* File-based object cache start */
		$this->persist();
		/* File-based object cache end */

		return true;
	}

	/* File-based object cache start */
	public function flush_taxonomies( $post_id ) {
		$taxonomies = get_post_taxonomies( $post_id );
		foreach ( $taxonomies as $taxonomy ) {
			$group = $taxonomy . '_relationships';
			if ( $this->get( $post_id, $group ) !== false && $this->delete( $post_id, $group ) )
				$this->_log('Flushed taxonomy ' . $taxonomy . ' for post ' . $post_id);
		}
	}

	public function persist($groups=null) {
		if ($this->shm_enable === 2) {
			$this->dirty_groups = array();
			return true;
		}

        if ($this->debug) $time_start = microtime(true);
        $this->persists += 1;

		// Remove expired entries
		foreach ($this->cache as $group => $keys) {
			foreach ($keys as $key => $value) {
				$this->_expire( $key, $group );
			}
		}

		if (!empty($this->dirty_groups)) {
			$this->actual_persists += 1;

			if ( ! $this->_create_cache() ) {
				if ($this->debug) $this->time_total += microtime(true) - $time_start;
				return false;
			}

			if ( ! $this->acquire_lock() ) {
				if ($this->debug) $this->time_total += microtime(true) - $time_start;
				return false;
			}

			if ($this->debug) $time_disk_write_start = microtime(true);
			$errors = 0;
			$persisted = false;
			foreach ($this->dirty_groups as $group => $dirty) {
				if (!isset($this->non_persistent_groups[$group]) &&
					isset($this->cache[$group]) &&
					($groups == null || in_array($group, $groups))) {
					if ($this->_persist_group($group, serialize($this->cache[$group])) !== false) {
						$this->mtime[$group] = time();
						$persisted = true;
						unset($this->dirty_groups[$group]);
					}
					else {
						$errors += 1;
						$this->file_cache_persist_errors_groups[$group] = true;
					}
				}
			}

			if ($persisted) {
				foreach ($this->expires as $group => $keys) {
					if (isset($this->cache[$group])) {
						foreach ($keys as $key => $value) {
							if (!isset($this->cache[$group][$key])) unset($this->expires[$group][$key]);
						}
					}
				}
				$this->_persist_group('.expires', serialize($this->expires));
			}
			if ($this->debug) $this->time_disk_write += microtime(true) - $time_disk_write_start;

			if ( $this->shm_enable ) SHM_Cache::_persist_groups();

			$this->release_lock();
		}
		if ($this->debug) $this->time_total += microtime(true) - $time_start;

		if (!empty($errors))
			return false;

		return true;
	}

	private function _create_cache() {
		// Create cache dir if it doesn't exist
		if (!file_exists($this->cache_dir.".htaccess")) {
			$stat = stat(ABSPATH.'wp-content');
			$dir_perms = $stat['mode'] & 0007777; // Get the permission bits.
			$file_perms = $dir_perms & 0000666; // Remove execute bits for files.

			// Make the base cache dir.
			if (!file_exists($this->cache_dir)) {
				if (! @ mkdir($this->cache_dir)) {
					return false;
				}
				@ chmod($this->cache_dir, $dir_perms);
			}

			@ touch($this->cache_dir."index.html");
			@ chmod($this->cache_dir."index.html", $file_perms);
			file_put_contents($this->cache_dir.'.htaccess', 'Deny from all');
		}
		return true;
	}

	private function _persist_group( $group, $data ) {
		if ( $this->shm_enable ) {
			if ( ! isset( $this->shm[$group] ) ) $this->shm[$group] = new SHM_Cache( $group );
			if ( $this->shm[$group]->put( $data ) ) return true;
		}
		return file_put_contents($this->cache_dir.$group.'.php',
										  CACHE_SERIAL_HEADER . $data . CACHE_SERIAL_FOOTER);
	}

	private function _check_persist( $key, $group ) {
		if ($this->shm_enable === 2) return;
		if ($group == 'options' && $key == 'alloptions') {
			if (isset($this->cache[$group][$key]['cron'])) $this->_log("$group.$key.cron = " . json_encode(unserialize($this->cache[$group][$key]['cron']), JSON_PRETTY_PRINT), 3);
			$this->_log("SET $group.$key.cron = " . json_encode(unserialize($this->cache[$group][$key]['cron']), JSON_PRETTY_PRINT), 3);
		}
		if ($group == 'transient' && $key == 'doing_cron' && !$this->ajax) {
			// We need to persist the value right away because spawn_cron will
			// issue a POST while the cache is still alive, so the destructor
			// won't cause an automatic persist, and wp-cron.php spawned via the
			// POST will try to read the value from the persistent cache.
			if (isset($this->cache[$group][$key])) $this->_log("$group.$key = " . json_encode(/*unserialize(*/$this->cache[$group][$key]/*)*/, JSON_PRETTY_PRINT));
			//$this->_log("SET $group.$key = " . json_encode(unserialize($this->cache[$group][$key]), JSON_PRETTY_PRINT));
			$result = $this->persist(array('transient'));
		}
		else if ($group == 'options' && isset($_POST['action']) &&
				 strpos($_POST['action'], 'save-') === 0) {
			$result = $this->persist(array('options'));
		}
		if (isset($result))
			$this->_log("PERSISTED $group " . json_encode($result, JSON_PRETTY_PRINT) . " ({$this->actual_persists})");
	}

	private function _expire ( $key, $group ) {
        if ($this->debug) $time_start = microtime(true);
		$expiration_time = !isset( $this->expires[$group][$key] ) ? 1 : $this->expires[$group][$key];
		if ( $expiration_time && $expiration_time <= $this->now ) {
			unset( $this->cache[$group][$key] );
			$this->mtime[$group] = time();
			unset( $this->expires[$group][$key] );
			$this->dirty_groups[$group] = true;
			$this->expirations += 1;
			if ($this->debug) {
				if ( ! isset($this->expirations_groups[$group]) )
					$this->expirations_groups[$group] = 1;
				else
					$this->expirations_groups[$group] += 1;
			}
			$return = true;
		}
		else $return = false;
		if ($this->debug) $this->time_total += microtime(true) - $time_start;
		return $return;
	}

	private function _log( $msg, $loglevel=2 ) {
		if ($this->debug >= $loglevel) {
			$time = microtime(true);
			$secs = floor($time);
			$ms = sprintf("%03d", ($time - $secs) * 1000);
			$log = strftime("%Y-%m-%d %H:%M:%S") . ",$ms $msg\n";
			@file_put_contents($this->cache_dir . 'object-cache.log', $log, FILE_APPEND);
		}
	}

	public function add_non_persistent_groups ( $groups ) {
		$groups = (array) $groups;

		$groups = array_fill_keys( $groups, true );
		$this->non_persistent_groups = array_merge( $this->non_persistent_groups, $groups );
	}

	function acquire_lock() {
		// Acquire a write lock.
		$this->mutex = @fopen($this->cache_dir.$this->flock_filename, 'w');
		if ( false == $this->mutex)
			return false;
		else {
			flock($this->mutex, LOCK_EX);
			return true;
		}
	}

	function release_lock() {
		// Release write lock.
		flock($this->mutex, LOCK_UN);
		fclose($this->mutex);
	}
	/* File-based object cache end */
}
