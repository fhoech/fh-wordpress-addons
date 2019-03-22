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
 * to support that are marked up with PHP comments 'Persistent object cache [start|end]'
 * to make updating the file easy.
 */

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

/* Persistent object cache start */
/**
 * Closes the cache.
 *
 * @since 2.0.0
 *
 * @return true Always returns true.
 */
function wp_cache_close() {
	global $wp_object_cache;

	if (isset($wp_object_cache))
		$wp_object_cache->close();

	return true;
}
/* Persistent object cache end */

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
	/* Persistent object cache start */
	global $wp_object_cache;

	return $wp_object_cache->add_non_persistent_groups( $groups );
	/* Persistent object cache end */
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

if ( ! defined( 'FH_OBJECT_CACHE_PATH' ) ) {
	// Using the correct separator eliminates some cache flush errors on Windows
	if ( defined( 'ABSPATH' ) ) 
		define( 'FH_OBJECT_CACHE_PATH', ABSPATH . 'wp-content' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'fh-object-cache' . DIRECTORY_SEPARATOR );
	else
		define( 'FH_OBJECT_CACHE_PATH', dirname( isset( $_SERVER['SCRIPT_FILENAME'] ) ? $_SERVER['SCRIPT_FILENAME'] : __FILE__ ) . DIRECTORY_SEPARATOR );
}

class SHM_Cache {

	public static function format_id( $id, $hex = false ) {
		if ( $hex )  // Formatted like ipcs -m
			$id = '0x' . str_pad( dechex( $id & 0xffffffff ), 8, '0', STR_PAD_LEFT );
		return $id;
	}

}

class SHM_Partitioned_Cache {

	/*
	 * Cache structure:
	 * Header (32 bytes)
	 * Hashes (each hash has a fixed length of hash_bytes)
	 * Offsets to key data and size (each entry has a fixed size of 8 bytes).
	 *                               Offsets are stored at
	 *                               ceil( size / 32 / block_size ) * block_size
	 * Data (variable length, each entry is padded to a multiple of
	 *       block_size bytes).
	 *       Data begins at ceil( size / 32 / block_size ) * block_size * 2
	 * 
	 * HEADER (byte offsets from beginning of file)
	 * Byte offset    Content                       Encoding
	 * ------------------------------------------------------------------------
	 * 0..3           Partition table size          Unsigned Long (Big Endian)
	 *                (number of hashes * hash_bytes)
	 * 4..7           Last data entry offset        Unsigned Long (Big Endian)
	 * 8..11          Last data entry size          Unsigned Long (Big Endian)
	 * 12..15         (Reserved, null)              N/A
	 * 16..31         Hash algorithm name           ASCII
	 * 
	 * OFFSET ENTRY (byte offsets from beginning of entry)
	 * Byte offset    Content                       Encoding
	 * ------------------------------------------------------------------------
	 * 0..3           Data entry offset (from       Unsigned Long (Big Endian)
	 *                beginning of file)
	 * 4..7           Data entry size (padded to    Unsigned Long (Big Endian)
	 *                multiples of block_size)
	 * 
	 * DATA ENTRY (byte offsets from beginning of entry, each entry is padded
	 * to multiples of block_size)
	 * Byte offset    Content                       Encoding
	 * ------------------------------------------------------------------------
	 * 0..3           Last access time (Unix)       Unsigned Long (Big Endian)
	 * 4..7           Expiration time (Unix)        Unsigned Long (Big Endian)
	 * 8..11          Key size                      Unsigned Long (Big Endian)
	 * 12..15         Data size (actual)            Unsigned Long (Big Endian)
	 * 16..n-1        Key                           ASCII
	 * n..m           Data                          PHP serialized data or int/float as string
	 */

	const ADD = 1;
	const REPLACE = 2;
	const INCR = 3;
	const DECR = 4;

	private $use_file_backend;
	private $id;
	private $res = false;
	private $size = 0;
	private $partition = array();
	private $partition_table_offset = 32;
	private $partition_table = null;
	private $partition_size = -1;
	private $block_size = 16;
	private $data_offset = -1;
	private $data_offset_count_offset = -1;
	private $last_key_data_offset = -1;
	private $last_key_data_size = -1;
	private $next = -1;
	private $now;
	private $debug;
	private $time_read = 0;
	private $time_seek = 0;
	private $complex_groups = array( 'options',
									 'mo',
									 'posts',
									 'users',
									 'user_meta',
									 'bp_pages',
									 'bp',
									 'post_meta',
									 'terms',
									 'post_tag_relationships',
									 'category_relationships',
									 'bp_last_activity',
									 'bp_messages',
									 'post_format_relationships',
									 'comment',
									 'comment_meta',
									 'term_meta',
									 'bp_notifications_grouped_notifications',
									 'topic-tag_relationships',
									 'tracpress_ticket_type_relationships',
									 'tracpress_ticket_component_relationships',
									 'tracpress_ticket_milestone_relationships',
									 'tracpress_ticket_priority_relationships',
									 'tracpress_ticket_severity_relationships',
									 'tracpress_ticket_tag_relationships',
									 //'bp_member_member_type',
									 'bp_xprofile_groups',
									 'bp_xprofile',
									 'bp_xprofile_fields',
									 'bp_xprofile_data',
									 'xprofile_field_meta',
									 'calendar' );
	private $complex_groups_noncomplex_keys = array( 'posts:last_changed',
													 'options:can_compress_scripts',
													 'terms:last_changed',
													 'options:uninstall_plugins',
													 'posts:get_page_by_path',
													 'comment:last_changed',
													 'bp_xprofile:fullname_field_id',
													 'options:wp_mail_smtp_debug',
													 'options:blacklist_keys',
													 'options:moderation_keys',
													 'options:flush-opcache-hide-button',
													 'options:flush-opcache-preload',
													 'options:simba_tfa_priv_key_format',
													 'options:auto_core_update_notified',
													 'options:show_comments_cookies_opt_in' );
	private $check_data_types = false;
	// Hash algorythm name => byte count
	private $hash_algos = array( 'crc32' => 4,
								 'crc32b' => 4, 
								 'fnv132' => 4,
								 'fnv1a32' => 4, 
								 'fnv164' => 8,
								 'fnv1a64' => 8 );
	private $hash_algo = 'fnv1a64';
	private $hash_bytes = 8;
	private $_hash_table = array();

	public function __construct( $size = 16 * 1024 * 1024, $parse = false, $sanity_check = false ) {
		$this->now = time();
		$this->use_file_backend = ( defined('FH_OBJECT_CACHE_SHM_USE_FILE_BACKEND') && FH_OBJECT_CACHE_SHM_USE_FILE_BACKEND ) || ! function_exists( 'shmop_open' );
		$this->debug = defined('FH_OBJECT_CACHE_SHM_DEBUG') ? FH_OBJECT_CACHE_SHM_DEBUG : 0;
		$this->check_data_types = defined('FH_OBJECT_CACHE_SHM_CHECK_DATA_TYPES') && FH_OBJECT_CACHE_SHM_CHECK_DATA_TYPES;
		$this->id = ftok( __FILE__, "\xff" );
		if ( ! $this->res = @ $this->_open( $this->id, 'c', 0600, $size ) ) {
			$error = error_get_last();
			file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't open SHM segment (key " . $this->get_id( true ) . ", size $size): " .
							   $error['message'] . "\n", FILE_APPEND );
		}
		else {
			$this->size = $this->_size( $this->res );
			$this->data_offset_count_offset = (int) ceil( $this->size / 32 / $this->block_size ) * $this->block_size;
			$this->data_offset = $this->data_offset_count_offset * 2;
			// Partition table is in the first 1/16 of total SHM segment size.
			// Data begins directly after that.
			$this->read_partition_table( $parse, $sanity_check );
			// Read existing file into shared memory
			$fn = FH_OBJECT_CACHE_PATH . SHM_Cache::format_id( $this->id, true );
			if ( empty( $this->partition ) && ! $this->use_file_backend && function_exists( 'shmop_open' ) && is_file( $fn ) ) {
				$res = @ fshmop_open( $this->id, 'a', 0600, $size );
				if ( $res === false ) {
					$error = error_get_last();
					file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
									   date( 'Y-m-d H:i:s,v' ) .
									   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't open file $fn, size $size): " .
									   $error['message'] . "\n", FILE_APPEND );
				}
				else {
					// Read chunks of 1024 kbytes and write to SHM
					$offset = 0;
					$chunk_size = 1024 * 1024;
					while ( $offset < $size ) {
						$chunk = fshmop_read( $res, $offset, $chunk_size );
						if ( $chunk === false ) {
							$error = error_get_last();
							file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
											   date( 'Y-m-d H:i:s,v' ) .
											   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't read from file $fn, size $size): " .
											   $error['message'] . "\n", FILE_APPEND );
							break;
						}
						if ( false === $this->_write( $this->res, $chunk, $offset ) ) {
							$error = error_get_last();
							file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
											   date( 'Y-m-d H:i:s,v' ) .
											   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't write to SHM segment (key " . $this->get_id( true ) . ", size $size): " .
											   $error['message'] . "\n", FILE_APPEND );
							break;
						}
						$offset += $chunk_size;
					}
					fshmop_close( $res );
					if ( $offset === $size ) {
						unlink( $fn );
						file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
										   date( 'Y-m-d H:i:s,v' ) .
										   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Wrote contents of $fn to SHM segment (key " . $this->get_id( true ) . ", size $size)\n", FILE_APPEND );
					}
					$this->read_partition_table( $parse, $sanity_check );
				}
			}
		}
	}

	private function _parse( $result ) {
		if ( $result === false ) return false;
		// Quickest way to check if result is a serialized string or not.
		// If it isn't, it's a numeric string, the only other 'data type' we store
		if ( strlen( $result ) < 2 || $result[1] !== ':' ) return strpos( $result, "." ) === false ? intval( $result ) : floatval( $result );
		return unserialize( $result );
	}

	private function _prepare( $data ) {
		if ( is_int( $data ) || is_float( $data ) ) return strval( $data );
		return serialize( $data );
	}

	private function _open( $key, $flags, $mode, $size ) {
		if ( $this->use_file_backend )
			return fshmop_open( $key, $flags, $mode, $size );
		else
			return shmop_open( $key, $flags, $mode, $size );
	}

	private function _close( $res ) {
		if ( $this->use_file_backend )
			fshmop_close( $res );
		else
			shmop_close( $res );
	}

	private function _delete( $res ) {
		if ( $this->use_file_backend )
			return fshmop_delete( $res );
		else
			return shmop_delete( $res );
	}

	private function _read( $res, $start, $count ) {
		if ( $this->use_file_backend )
			return fshmop_read( $res, $start, $count );
		else
			return shmop_read( $res, $start, $count );
	}

	private function _size( $res ) {
		if ( $this->use_file_backend )
			return fshmop_size( $res );
		else
			return shmop_size( $res );
	}

	private function _write( $res, $data, $offset ) {
		if ( $this->use_file_backend )
			return fshmop_write( $res, $data, $offset );
		else
			return shmop_write( $res, $data, $offset );
	}

	public function read_partition_table( $parse = false, $sanity_check = false ) {
		// Actual size of partition table is in first four bytes
		$this->partition = array();
		$partition_size = @ $this->_read( $this->res, 0, 4 );
		if ( $partition_size !== false ) {
			$this->partition_size = unpack( 'N', $partition_size )[1];
			$start_data = @ $this->_read( $this->res, 4, 4 );
			$count_data = @ $this->_read( $this->res, 8, 4 );
			// Get offset and length of last data chunk
			if ( $start_data !== false && $count_data !== false ) {
				// BEGIN read
					$hash_algo = @ $this->_read( $this->res, 16, 16 );
					if ( $hash_algo !== false && isset( $this->hash_algos[ $hash_algo = rtrim( $hash_algo, "\0" ) ] ) ) {
						$this->hash_algo = $hash_algo;
						$this->hash_bytes = $this->hash_algos[ $hash_algo ];
					}
					else {
						// Get rid of data
						$partition_size = 0;
						$start_data = "\0\0\0\0";
						$count_data = "\0\0\0\0";
						// Write out v4 format
						if ( 32 !== @ $this->_write( $this->res, pack( 'N', $partition_size ) . $start_data . $count_data . "\0\0\0\0" . str_pad( $this->hash_algo, 16, "\0" ), 0 ) ) {
							$error = error_get_last();
							file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
											   date( 'Y-m-d H:i:s,v' ) .
											   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't write v4 partition table format to SHM segment (key " . $this->get_id( true ) . "): " .
											   $error['message'] . "\n", FILE_APPEND );
						}
						$this->partition_size = $partition_size;
					}
					$this->partition_table = $this->partition_size ? $this->_read( $this->res, $this->partition_table_offset, $this->partition_size ) : "";
					if ( $this->partition_size ) {
						if ( $parse ) {
							$this->parse_partition_table( $sanity_check );
						}
					}
				// END read
				$start = unpack( 'N', $start_data )[1];
				$count = unpack( 'N', $count_data )[1];
			}
			else {
				$start = $this->data_offset;
				$count = 0;
			}
			// Offset for next data chunk
			if ( ! $start ) $start = $this->data_offset;
			if ( $this->partition_size === 0 ) {
				file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Partition table size is zero (key " . $this->get_id( true ) . ")\n", FILE_APPEND );
			}
			$this->last_key_data_offset = $start;
			$this->last_key_data_size = $count;
			$this->next = (int) ceil( ( $start + $count ) / $this->block_size ) * $this->block_size;
		}
		else {
			$error = error_get_last();
			file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't read partition table from SHM segment (key " . $this->get_id( true ) . "): " .
							   $error['message'] . "\n", FILE_APPEND );
		}
	}

	public function parse_partition_table( $sanity_check = false ) {
		$time_start = microtime( true );
		$partition = array();
		$start = 0;
		// BEGIN parse
			$i = 0;
			$hash_null = str_repeat( "\0", $this->hash_bytes );
			while ( $start < $this->partition_size ) {
				$hash = substr( $this->partition_table, $start, $this->hash_bytes );
				if ( $hash !== $hash_null ) {
					if ( $sanity_check && isset( $partition[ $hash ] ) )
						echo "WARNING - partition table is corrupt! Duplicate entry '" . htmlspecialchars( addcslashes( $hash, "\x00..\x19\x7f..\xff\\" ), ENT_COMPAT, 'UTF-8' ) . "'<br />\n";
					$offset_count = @ $this->_read( $this->res, $this->data_offset_count_offset + $i * 8, 8 );
					if ( $offset_count === false ) {
						$error = error_get_last();
						file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
										   date( 'Y-m-d H:i:s,v' ) .
										   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't read partition table offset and count from SHM segment (key " . $this->get_id( true ) . "): " .
										   $error['message'] . "\n", FILE_APPEND );
						break;
					}
					$offset = unpack( 'N', substr(  $offset_count, 0, 4 ) )[1];
					$count = unpack( 'N', substr(  $offset_count, 4, 4 ) )[1];
					$partition[ $hash ] = array( $start, $offset, $count );
				}
				$i ++;
				$start += $this->hash_bytes;
			}
		// END parse
		$this->partition = $partition;
		$this->time_seek += microtime( true ) - $time_start;
	}

	public function __destruct() {
		if ( $this->res !== false && $this->_close( $this->res ) !== null ) {
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

		if ( ! @ $this->_write( $this->res, str_repeat( "\0", 12 ), 0 ) ) {
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
		$this->last_key_data_offset = $this->data_offset;
		$this->last_key_data_size = 0;
		$this->next = $this->data_offset;

		return true;
	}

	public function flush() {
		return $this->clear();
	}

	public function defrag( $compact = false ) {
		if ( $this->res === false ) return false;

		$this->read_partition_table( true );

		// Create temp SHM segment
		$id = ftok( __FILE__, "\x7e" );
		$res = @ $this->_open( $id, 'n', 0600, $this->size );
		if ( $res === false ) {
			$error = error_get_last();
			file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Defrag failed: Couldn't open temporary SHM segment (key " . SHM_Cache::format_id( $id, true ) . ", size {$this->size}): " .
							   $error['message'] . "\n", FILE_APPEND );
			return false;
		}

		// Read header
		$header = @ $this->_read( $this->res, 12, 20 );
		if ( $header === false ) {
			$error = error_get_last();
			file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Defrag failed: Couldn't read from SHM segment (key " . $this->get_id( true ) . ", offset 12, length 20): " .
							   $error['message'] . "\n", FILE_APPEND );
			$this->_delete( $res );
			$this->_close( $res );
			return false;
		}

		// Write header to temp SHM segment
		if ( 20 !== @ $this->_write( $res, $header, 12 ) ) {
			$error = error_get_last();
			file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Defrag failed: Couldn't write to temporary SHM segment (key " . SHM_Cache::format_id( $id, true ) . ", offset 12, length 20): " .
							   $error['message'] . "\n", FILE_APPEND );
			$this->_delete( $res );
			$this->_close( $res );
			return false;
		}

		$n = 0;
		$data_offset = $this->data_offset;
		$i = 0;
		foreach( $this->partition as $group_key => $entry ) {
			$n ++;

			if ( $entry === false ) continue;
			list( $pos, $offset, $count ) = $entry;
			if ( ! $count ) continue;

			// Read data header
			$data_header = @ $this->_read( $this->res, $offset, 16 );
			if ( $data_header === false ) {
				$error = error_get_last();
				file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Defrag failed: Couldn't read from SHM segment (key " . $this->get_id( true ) . ", offset $offset, length 16): " .
								   $error['message'] . "\n", FILE_APPEND );
				continue;
			}

			// Parse data header
			$expire = unpack( 'N', substr( $data_header, 4, 4 ) )[1];
			if ( ! $expire && defined('FH_OBJECT_CACHE_LIFETIME') && FH_OBJECT_CACHE_LIFETIME ) $expire = unpack( 'N', substr( $data_header, 0, 4 ) )[1] + FH_OBJECT_CACHE_LIFETIME;
			if ( $expire && $expire <= $this->now ) continue;
			$key_size = unpack( 'N', substr( $data_header, 8, 4 ) )[1];
			$data_len = unpack( 'N', substr( $data_header, 12, 4 ) )[1];
			$len = $key_size + $data_len;

			// Read data
			$data = @ $this->_read( $this->res, $offset + 16, $len );
			if ( $data === false ) {
				$error = error_get_last();
				file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Defrag failed: Couldn't read from SHM segment (key " . $this->get_id( true ) . ", offset " . ( $offset + 16 ) . ", length $len): " .
								   $error['message'] . "\n", FILE_APPEND );
				continue;
			}

			$padded_len = $compact ? 16 + (int) ceil( $len / $this->block_size ) * $this->block_size : $count;
			$data_offset_count = pack( 'N', $data_offset ) . pack( 'N', $padded_len );

			// Write to temp SHM segment
			if ( $this->hash_bytes != @ $this->_write( $res, $group_key, 32 + $i * $this->hash_bytes ) ||
				 8 !== @ $this->_write( $res, $data_offset_count, $this->data_offset_count_offset + $i * 8 ) ||
				 16 !== @ $this->_write( $res, $data_header, $data_offset ) ||
				 $len !== @ $this->_write( $res, $data, $data_offset + 16 ) ) {
				$error = error_get_last();
				file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Defrag failed: Couldn't write to temporary SHM segment (key " . SHM_Cache::format_id( $id, true ) . "): " .
								   $error['message'] . "\n", FILE_APPEND );
				continue;
			}

			// Increase offset for variable-length data
			$data_offset += $padded_len;

			$i ++;
		}

		// Write updated header to temp SHM segment
		if ( 12 !== @ $this->_write( $res, pack( 'N', $i * $this->hash_bytes ) . $data_offset_count, 0 ) ) {
			$error = error_get_last();
			file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Defrag failed: Couldn't write to temporary SHM segment (key " . SHM_Cache::format_id( $id, true ) . ", offset 0, length 12): " .
							   $error['message'] . "\n", FILE_APPEND );
			$this->_delete( $res );
			$this->_close( $res );
			return false;
		}

		// Copy defragged SHM segment
		// Read in chunks
		$offset = 0;
		$chunksize = 1024 * 1024;
		while ( $offset < $this->size ) {
			$chunk = $this->_read( $res, $offset, $chunksize );
			if ( $chunk === false ) {
				$error = error_get_last();
				file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Defrag failed: Couldn't read from temporary SHM segment (key " . SHM_Cache::format_id( $id, true ) . "): " .
								   $error['message'] . "\n", FILE_APPEND );
				break;
			}
			if ( false === $this->_write( $this->res, $chunk, $offset ) ) {
				$error = error_get_last();
				file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Defrag failed: Couldn't write to SHM segment (key " . $this->get_id( true ) . "): " .
								   $error['message'] . "\n", FILE_APPEND );
				break;
			}
			$offset += $chunksize;
		}

		$this->_delete( $res );
		$this->_close( $res );

		$this->read_partition_table();

		file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
						   date( 'Y-m-d H:i:s,v' ) .
						   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Defragged SHM segment (key " . $this->get_id( true ) . "), purged " . ( $compact ? "& compacted " : "" ) . ( $n - $i ) . " entries\n", FILE_APPEND );

		return true;
	}

	public function get( $key, $group = 'default', $update_time = true ) {
		if ( defined( 'FH_OBJECT_CACHE_SHM_LOCAL_DEBUG' ) ) echo "GET $group:$key\n";
		if ( $this->res === false ) return false;


		$group_key = $this->_get_group_key( $key, $group );

		$partition_entry = $this->_get_partition_entry( $group_key );
		if ( $partition_entry === false ) return false;
		list( $pos, $start, $count ) = $partition_entry;
		if ( defined( 'FH_OBJECT_CACHE_SHM_LOCAL_DEBUG' ) ) echo "Existing partition entry at " . ( $pos + $this->partition_table_offset ) . ", data at $start, allocated $count\n";
		if ( ! $count || $start + $count > $this->size ) return false;
		$allocated_count = $count;

		$time_start = microtime( true );
		$result = @ $this->_read( $this->res, $start, 16 );
		$this->time_read += microtime( true ) - $time_start;
		if ( $result === false ) {
			$error = error_get_last();
			file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): get() Couldn't read header for '$group:$key' from SHM segment (key " . $this->get_id( true ) . ") at offset $start, length $count (allocated $allocated_count): " .
							   $error['message'] . "\n", FILE_APPEND );
			//$this->delete( $key, $group );
			return false;
		}
		// BEGIN read
			$atime = unpack( 'N', substr( $result, 0, 4 ) )[1];
			$expire = $oexpire = unpack( 'N', substr( $result, 4, 4 ) )[1];
			if ( ! $expire && defined('FH_OBJECT_CACHE_LIFETIME') && FH_OBJECT_CACHE_LIFETIME ) $expire = $atime + FH_OBJECT_CACHE_LIFETIME;
			if ( $expire && $expire <= $this->now ) {
				if ( defined( 'FH_OBJECT_CACHE_SHM_LOG_EXPIRATIONS' ) && FH_OBJECT_CACHE_SHM_LOG_EXPIRATIONS ) 
					file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
									   date( 'Y-m-d H:i:s,v' ) .
									   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Expired " . date( 'Y-m-d H:i:s T', $expire ) . ": '$group:$key' (last modified " .
									   date( 'Y-m-d H:i:s T', $atime ) . ")\n", FILE_APPEND );
				//$this->delete( $key, $group );
				return array( false, $oexpire, $atime );
			}
			$key_data_len = unpack( 'N', substr( $result, 8, 4 ) )[1];
			$time_start = microtime( true );
			$key_data = @ $this->_read( $this->res, $start + 16, $key_data_len );
			$this->time_read += microtime( true ) - $time_start;
			if ( $key_data === false ) {
				$error = error_get_last();
				file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't read key data for '$group:$key' from SHM segment (key " . $this->get_id( true ) . ") at key data offset " . ( $start + 16 ) . ", length $key_data_len: " .
								   $error['message'] . ( $this->debug ? " (header '" . addcslashes( $result, "\x00..\x19\x7f..\xff\\" ) . "')" : "" ) . "\n", FILE_APPEND );
				//$this->delete( $key, $group );
				return false;
			}
			if ( $key_data !== "$group:$key" ) {
				file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
										   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Key data '" . addcslashes( $key_data, "\x00..\x19\x7f..\xff\\" ) . "' from SHM segment (key " . $this->get_id( true ) . ") at key data offset " . ( $start + 16 ) . ", length $key_data_len doesn't match requested '$group:$key'\n", FILE_APPEND );
				//$this->delete( $key, $group );
				return false;
			}
			$count = unpack( 'N', substr( $result, 12, 4 ) )[1];
			$time_start = microtime( true );
			$data = @ $this->_read( $this->res, $start + 16 + $key_data_len, $count );
			$this->time_read += microtime( true ) - $time_start;
			if ( $data === false ) {
				$error = error_get_last();
				file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't read '$group:$key' from SHM segment (key " . $this->get_id( true ) . ") at data offset " . ( $start + 16 + $key_data_len ) . ", length $count (allocated $allocated_count): " .
								   $error['message'] . ( $this->debug ? " (header '" . addcslashes( $result, "\x00..\x19\x7f..\xff\\" ) . "')" : "" ) . "\n", FILE_APPEND );
				//$this->delete( $key, $group );
				return false;
			}
			$result = $data;
			// Update access time
			if ( $update_time ) {
				$atime = $this->now;
				$this->_write( $this->res, pack( 'N', $atime ), $start );
			}
		// END read
		$parsed = @ $this->_parse( $result );
		if ( $parsed === false && $result !== 'b:0;' ) {
			$error = error_get_last();
			file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't parse '$group:$key' from SHM segment (key " . $this->get_id( true ) . ") at offset $start, length $count (allocated $allocated_count): " .
							   $error['message'] . ( $this->debug > 1 ? " (header '" . addcslashes( $result, "\x00..\x19\x7f..\xff\\" ) . "')" : "" ) . "\n", FILE_APPEND );
			//$this->delete( $key, $group );
			return false;
		}

		// BEGIN check/return parsed
			if ( $this->check_data_types &&
				 in_array( $group, $this->complex_groups ) &&
				 ! in_array( $group . ':' . explode( ':', $key )[0], $this->complex_groups_noncomplex_keys ) &&
				 ! ( is_object( $parsed ) || is_array( $parsed ) ) ) {
				file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Not an object or array: '$group:$key'" . ( $this->debug ?  ": '" . addcslashes( $result, "\x00..\x19\x7f..\xff\\" ) . "'" : "" ) . "\n", FILE_APPEND );
				//$this->delete( $key, $group );
				return false;
			}
			return array( $parsed, $oexpire, $atime );
		// END check/return parsed
	}

	public function set( $key, $value, $group = 'default', $expire = 0 ) {
		if ( $this->res === false ) return false;

		$group_key = $this->_get_group_key( $key, $group );

		$atime = time();
		$key_data = $group . ':' . $key;
		$key_data_len = strlen( $key_data );
		$data = $this->_prepare( $value );
		// Data header: atime(4) expire(4) key_data_len(4) data_len(4) key_data data
		$data = pack( 'N', $atime ) . pack( 'N', $expire ) . pack( 'N', $key_data_len ) . pack( 'N', strlen( $data ) ) . $key_data . $data;
		$data_len = strlen( $data );
		$padded_len = (int) ceil( $data_len / $this->block_size ) * $this->block_size;
		if ( defined( 'FH_OBJECT_CACHE_SHM_LOCAL_DEBUG' ) ) {
			ob_start();
			var_dump( $value );
			$repr = ob_get_contents();
			ob_end_clean();
			echo "SET $group:$key = " . htmlspecialchars( $repr, ENT_COMPAT, 'UTF-8' ) . " (atime = $atime, expire = $expire, len = $data_len, padded $padded_len)\n";
		}

		$partition_entry = $this->_get_partition_entry( $group_key );
		if ( $partition_entry !== false ) {
			// Existing partition entry
			list( $pos, $offset, $count ) = $partition_entry;
			if ( ! $count ) {
				$deleted = true;
				if ( defined( 'FH_OBJECT_CACHE_SHM_LOCAL_DEBUG' ) ) echo "Existing partition entry marked as deleted, about to get len from data block\n";
				// Get actual size of deleted entry so we may re-use the same byte range
			}
			$time_start = microtime( true );
			$result = @ $this->_read( $this->res, $offset, 16 );
			$this->time_read += microtime( true ) - $time_start;
			if ( $result === false ) {
				$error = error_get_last();
				file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): " . ( ! $count ? "[Warning]" : "" ) . " set(): Couldn't read header for '$group:$key' from SHM segment (key " . $this->get_id( true ) . ") at offset $offset: " .
								   $error['message'] . "\n", FILE_APPEND );
				if ( $count ) return false;
			}
			// BEGIN read entry header
				$old_key_data_len = unpack( 'N', substr( $result, 8, 4 ) )[1];
				$time_start = microtime( true );
				$old_key_data = @ $this->_read( $this->res, $offset + 16, $old_key_data_len );
				$this->time_read += microtime( true ) - $time_start;
				if ( $old_key_data === false ) {
					$error = error_get_last();
					file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
									   date( 'Y-m-d H:i:s,v' ) .
									   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "):" . ( ! $count ? " [Warning]" : "" ) . " Couldn't read existing key data for '$group:$key' from SHM segment (key " . $this->get_id( true ) . ") at key data offset " . ( $offset + 16 ) . ", length $old_key_data_len: " .
									   $error['message'] . "\n", FILE_APPEND );
					if ( $count ) return false;
				}
				else if ( $old_key_data !== $key_data ) {
					file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
									   date( 'Y-m-d H:i:s,v' ) .
									   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "):" . ( ! $count ? " [Warning]" : "" ) . " Existing key data '" . addcslashes( $old_key_data, "\x00..\x19\x7f..\xff\\" ) . "' from SHM segment (key " . $this->get_id( true ) . ") at key data offset " . ( $offset + 16 ) . ", length $old_key_data_len doesn't match expected '$key_data'\n", FILE_APPEND );
					if ( $count ) return false;
				}
				// Used bytes = header 16 bytes + key bytes + data bytes
				else if ( ! $count ) $count = 16 + $key_data_len + unpack( 'N', substr( $result, 12, 4 ) )[1];
			// END read entry header
			$padded_count = (int) ceil( $count / $this->block_size ) * $this->block_size;
			if ( $offset + $padded_count > $this->size ) {
				// This shouldn't happen, ever
				file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): [Warning] Existing data offset $offset + padded count $padded_count for '$group:$key' from SHM segment (key " . $this->get_id( true ) . ") exceeds SHM segment size {$this->size}\n", FILE_APPEND );
				$padded_count = 0;
			}
			if ( defined( 'FH_OBJECT_CACHE_SHM_LOCAL_DEBUG' ) ) echo "Existing partition entry at " . ( $pos + $this->partition_table_offset ) . ", data at $offset, len $count, padded $padded_count\n";
		}
		else {
			// Create new partition entry
			$pos = $this->partition_size;
			$count = 0;
			$padded_count = 0;
			if ( defined( 'FH_OBJECT_CACHE_SHM_LOCAL_DEBUG' ) ) echo "No existing partition entry. About to create at " . ( $pos + $this->partition_table_offset ) . "\n";
		}

		$padded_len = max( $padded_len, $padded_count );  // Assign enough space for old and new entry to minimize cache trashing

		if ( $padded_len > $padded_count ) {
			// Create new entry
			$offset = $this->next;
			if ( defined( 'FH_OBJECT_CACHE_SHM_LOCAL_DEBUG' ) ) echo "Data padded $padded_len > existing data padded $padded_count, using next free data segment offset $offset\n";
			if ( $offset + $padded_len > $this->size ) {
				file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Can't write '$group:$key' ($data_len bytes, padded $padded_len) to SHM segment (key " . $this->get_id( true ) . ") at offset $offset: Allocated space for data exceeded. Defragging cache.\n", FILE_APPEND );
				$this->defrag();
				$offset = $this->next;
				if ( defined( 'FH_OBJECT_CACHE_SHM_LOCAL_DEBUG' ) ) echo "Data padded $padded_len > existing data padded $padded_count, using next free data segment offset $offset\n";
				if ( $offset + $padded_len > $this->size ) {
					file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
									   date( 'Y-m-d H:i:s,v' ) .
									   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't write '$group:$key' ($data_len bytes, padded $padded_len) to SHM segment (key " . $this->get_id( true ) . ") at offset $offset: Allocated space for data exceeded. Flushing cache.\n", FILE_APPEND );
					$this->flush();
					return false;
				}
				else return $this->set( $key, $value, $group, $expire );
			}
		}

		$partition_size = $this->partition_size;

		if ( $pos == $this->partition_size ) {
			// This is a new partition entry, need to increase partition size
			$partition_size += strlen( $group_key );
			if ( defined( 'FH_OBJECT_CACHE_SHM_LOCAL_DEBUG' ) ) echo "Partition entry at $pos == partition size {$this->partition_size}, about to increase partition size to $partition_size\n";
			if ( $this->partition_table_offset + $partition_size > $this->data_offset_count_offset ) {
				file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't write partition table entry for '$group:$key' to SHM segment (key " . $this->get_id( true ) . "): Allocated space for partition table exceeded. Flushing cache.\n", FILE_APPEND );
				$this->flush();
				return false;
			}
			if ( $partition_size / $this->hash_bytes * 8 > $this->data_offset ) {
				file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't write data offset and count for '$group:$key' to SHM segment (key " . $this->get_id( true ) . "): Allocated space for offset and count table exceeded. Flushing cache.\n", FILE_APPEND );
				$this->flush();
				return false;
			}
		}

		// Write data
		$bytes_written = @ $this->_write( $this->res, $data, $offset );
		if ( $bytes_written === false ) {
			$error = error_get_last();
			file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't write '$group:$key' ($data_len bytes) to SHM segment (key " . $this->get_id( true ) . ") at offset $offset: " .
							   $error['message'] . "\n", FILE_APPEND );
			return false;
		}
		if ( $bytes_written !== $data_len ) {
			file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't write all data for '$group:$key' to SHM segment (key " . $this->get_id( true ) . ") at offset $offset: Wrote $bytes_written of $data_len bytes\n", FILE_APPEND );
			return false;
		}

		if ( $padded_len > $padded_count || isset( $deleted ) ) {
			// Create new partition entry or update existing
			if ( defined( 'FH_OBJECT_CACHE_SHM_LOCAL_DEBUG' ) ) echo "Data len $data_len (padded $padded_len) > existing data len $count (padded $padded_count) or deleted, about to write partition entry at " . ( $this->partition_table_offset + $pos ) . "\n";
			// Write partition entry
			$data_offset_count = pack( 'N', $offset ) . pack( 'N', $padded_len );
			if ( strlen( $group_key ) !==  @ $this->_write( $this->res, $group_key, $this->partition_table_offset + $pos ) ) {
				$error = error_get_last();
				file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't write partition table entry for '$group:$key' to SHM segment (key " . $this->get_id( true ) . "): " .
								   $error['message'] . "\n", FILE_APPEND );
				return false;
			}
			if ( 8 !==  @ $this->_write( $this->res, $data_offset_count, $this->data_offset_count_offset + $pos / $this->hash_bytes * 8 ) ) {
				$error = error_get_last();
				file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't write partition table data offset and count entry for '$group:$key' to SHM segment (key " . $this->get_id( true ) . "): " .
								   $error['message'] . "\n", FILE_APPEND );
				return false;
			}
		}

		if ( $padded_len > $padded_count ) {
			// This is a new entry, need to increase next data segment offset
			if ( defined( 'FH_OBJECT_CACHE_SHM_LOCAL_DEBUG' ) ) echo "Data padded $padded_len > existing data padded $padded_count, increasing next free key data offset by padded $padded_len\n";
			$this->next += $padded_len;
		}

		if ( $padded_len > $padded_count || isset( $deleted ) )
			$this->partition[ $group_key ] = array( $pos, $offset, $padded_len );

		if ( $pos == $this->partition_size ) {
			// This is a new partition entry, need to write updated partition size
			if ( defined( 'FH_OBJECT_CACHE_SHM_LOCAL_DEBUG' ) ) echo "Partition entry at $pos == partition size {$this->partition_size}, about to write increased partition size $partition_size\n";
			$this->partition_size = $partition_size;
			if ( 4 !==  @ $this->_write( $this->res, pack( 'N', $partition_size ), 0 ) ) {
				$error = error_get_last();
				file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): [Warning] Couldn't write increased partition table size for '$group:$key' in SHM segment (key " . $this->get_id( true ) . "): " .
								   $error['message'] . "\n", FILE_APPEND );
			}
		}

		if ( $padded_len > $padded_count ) {
			// This is a new entry, need to write updated last data block offset and size
			if ( defined( 'FH_OBJECT_CACHE_SHM_LOCAL_DEBUG' ) ) echo "About to write last key data offset and count to partition table\n";
			if ( $offset < $this->last_key_data_offset ) {
				file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): [CRITICAL] New last key data offset $offset < old last key data offset {$this->last_key_data_offset} for '$group:$key' in SHM segment (key " . $this->get_id( true ) . ")\n", FILE_APPEND );
			}
			else if ( 8 !== @ $this->_write( $this->res, $data_offset_count, 4 ) ) {
				$error = error_get_last();
				file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): [Warning] Couldn't update partition table last key data offset and size for '$group:$key' in SHM segment (key " . $this->get_id( true ) . "): " .
								   $error['message'] . "\n", FILE_APPEND );
			}
			else {
				$this->last_key_data_offset = $offset;
				$this->last_key_data_size = $padded_len;
			}
		}

		return $bytes_written !== false;
	}

	public function incr( $key, $offset = 1, $group = 'default' ) {
		return $this->_incr_decr( SHM_Partitioned_Cache::INCR, $key, $offset, $group );
	}

	public function decr( $key, $offset = 1, $group = 'default' ) {
		return $this->_incr_decr( SHM_Partitioned_Cache::DECR, $key, $offset, $group );
	}

	private function _incr_decr( $cmd, $key, $offset = 1, $group = 'default' ) {
		if ( $this->res === false ) return false;

		$result = $this->get( $key, $group );
		if ( $result === false ) return false;
		list( $value, $expire, $atime ) = $result;

		if ( ! is_numeric( $value ) )
			$value = 0;

		$offset = (int) $offset;

		if ( $cmd === SHM_Partitioned_Cache::INCR )
			$value += $offset;
		else
			$value -= $offset;

		if ( $value < 0 )
			$value = 0;

		if ( $this->set( $key, $value, $group, $expire ) )
			return $value;

		return false;
	}

	public function add( $key, $value, $group = 'default', $expire = 0 ) {
		return $this->_add_replace( SHM_Partitioned_Cache::ADD, $key, $value, $group, $expire );
	}

	public function replace( $key, $value, $group = 'default', $expire = 0 ) {
		return $this->_add_replace( SHM_Partitioned_Cache::REPLACE, $key, $value, $group, $expire );
	}

	public function _add_replace( $cmd, $key, $value, $group = 'default', $expire = 0 ) {
		if ( $this->res === false ) return false;

		$group_key = $this->_get_group_key( $key, $group );

		$partition_entry = $this->_get_partition_entry( $group_key );
		if ( $cmd === SHM_Partitioned_Cache::ADD ) {
			// Return false if entry already exists
			if ( $partition_entry !== false ) {
				list( $pos, $offset, $count ) = $partition_entry;
				if ( $count ) return false;
			}
		}
		else {
			// Return false if entry doesn't exist
			if ( $partition_entry === false ) return false;
			list( $pos, $offset, $count ) = $partition_entry;
			if ( ! $count ) return false;
		}

		return $this->set( $key, $value, $group, $expire );
	}

	public function delete( $key, $group = 'default', $permanent = false ) {
		if ( $this->res === false ) return false;

		$group_key = $this->_get_group_key( $key, $group );

		$partition_entry = $this->_get_partition_entry( $group_key );
		if ( $partition_entry === false ) return false;
		list( $pos, $offset, $count ) = $partition_entry;

		if ( $permanent ) {
			// Overwrite whole entry with binary zeros to permanently delete
			if ( ! @ $this->_write( $this->res, str_repeat( "\0", $this->hash_bytes ), $this->partition_table_offset + $pos ) ) {
				$error = error_get_last();
				file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't truncate partition table entry for '$group:$key' in SHM segment (key " . $this->get_id( true ) . "): " .
								   $error['message'] . "\n", FILE_APPEND );
				return false;
			}
			$data = str_repeat( "\0", 8 );
			$pos_offset = 0;
		}
		else if ( ! $count ) return false;
		else {
			// Set size to zero in partition table entry to mark as deleted
			$data = "\0\0\0\0";
			$pos_offset = 4;
		}
		if ( ! @ $this->_write( $this->res, $data, $this->data_offset_count_offset + $pos / $this->hash_bytes * 8 + $pos_offset ) ) {
			$error = error_get_last();
			file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't overwrite partition table offset and count entry for '$group:$key' in SHM segment (key " . $this->get_id( true ) . "): " .
							   $error['message'] . "\n", FILE_APPEND );
			return false;
		}

		$this->partition[ $group_key ] = $permanent ? false : array( $pos, $offset, 0 );

		if ( $this->debug > 1 ) {
			file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
							   date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Partitioned_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Deleted '$group:$key'\n", FILE_APPEND );
		}

		return true;
	}

	public function delete_group( $group, $permanent = false ) {
		if ( $this->res === false ) return false;

		$groups = $this->get_groups();

		$result = false;

		foreach ( $groups as $key => $stats ) {
			if ( $key === $group ) {
				$keys = $stats[ 'keys' ];
				if ( $permanent ) $keys = array_merge( $keys, $stats[ 'deleted_keys' ] );
				foreach ( $keys as $key ) {
					$result = $this->delete( $key, $group, $permanent );
				}
			}
		}

		return $result;
	}

	public function __get( $name ) {
		return $this->$name;
	}

	public function get_block_size() {
		return $this->block_size;
	}

	public function get_size() {
		return $this->size;
	}

	public function get_next() {
		return $this->next;
	}

	public function get_groups( $reset = false, $sanity_check = false ) {
		$groups = array();

		if ( $this->res !== false ) {
			if ( $reset ) $this->read_partition_table( true, $sanity_check );
			foreach ( $this->partition as $group_key => $entry ) {
				if ( $entry === false ) continue;
				else list( $pos, $offset, $count ) = $entry;
				$result = @ $this->_read( $this->res, $offset, 16 );
				if ( $result === false ) continue;
				else {
					$atime = unpack( 'N', substr( $result, 0, 4 ) )[1];
					$expire = unpack( 'N', substr( $result, 4, 4 ) )[1];
					$key_size = unpack( 'N', substr( $result, 8, 4 ) )[1];
					if ( $key_size > 256 ) {
						if ( $sanity_check ) echo "WARNING - shared memory is corrupt! &lt;group:key&gt; size $key_size &gt; 256 for hash '" . htmlspecialchars( addcslashes( $group_key, "\x00..\x19\x7f..\xff\\" ), ENT_COMPAT, 'UTF-8' ) . "' (header '" . htmlspecialchars( addcslashes( $result, "\x00..\x19\x7f..\xff\\" ), ENT_COMPAT, 'UTF-8' ) . "')<br />\n";
						continue;
					}
					$size = 16 + $key_size + unpack( 'N', substr( $result, 12, 4 ) )[1];
					$key_data = @ $this->_read( $this->res, $offset + 16, $key_size );
					if ( $key_data === false ) continue;
					list( $group, $key ) = explode( ':', $key_data, 2 );
				}
				if ( ! isset( $groups[ $group ] ) )
					$groups[ $group ] = array( 'entries_count' => 0,
											   'bytes_used' => 0,
											   'keys' => array(),
											   'deleted_keys' => array(),
											   'bytes_allocated' => 0,
											   'entry_max_size' => 0,
											   'entry_max_size_key' => null,
											   'atime' => 0,
											   'expire' => 0 );
				if ( $count ) {
					if ( $sanity_check && $size > $count ) {
						echo "WARNING - shared memory is corrupt! Entry size $size &gt; allocated size $count for $group:$key<br />\n";
					}
					if ( $sanity_check && $size > $this->size ) {
						echo "WARNING - shared memory is corrupt! Entry size $size &gt; total SHM size {$this->size} for $group:$key<br />\n";
					}
					$groups[ $group ][ 'entries_count' ] += 1;
					$groups[ $group ][ 'bytes_used' ] += $size;
					$groups[ $group ][ 'keys' ][] = $key;
					if ( $size > $groups[ $group ][ 'entry_max_size' ] ) {
						$groups[ $group ][ 'entry_max_size' ] = $size;
						$groups[ $group ][ 'entry_max_size_key' ] = $key;
					}
					if ( $atime > $groups[ $group ][ 'atime' ] ) {
						$groups[ $group ][ 'atime' ] = $atime;
					}
					if ( $expire > $groups[ $group ][ 'expire' ] ) {
						$groups[ $group ][ 'expire' ] = $expire;
					}
				}
				else {
					$groups[ $group ][ 'deleted_keys' ][] = $key;
				}
				$groups[ $group ][ 'bytes_allocated' ] += $count;
			}
		}

		return $groups;
	}

	public function get_id( $hex = false ) {
		return SHM_Cache::format_id( $this->id, $hex );
	}

	public function get_shm_id( $hex = false ) {
		return $this->res;
	}

	public function get_partition_size() {
		return $this->partition_size;
	}

	public function get_partition_table() {
		return $this->partition_table;
	}

	public function get_data_offset() {
		return $this->data_offset;
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
		$stats_time = microtime( true );
		echo '<h4>Backend</h4>';
		echo '<table><tbody>';
		if ( $this->use_file_backend )
			echo '<tr><th>Cache File</th><td>' . FH_OBJECT_CACHE_PATH . "</td></tr>\n";
		else
			echo '<tr><th>Shared Memory Key</th><td>' . $this->get_id( true ) . "</td></tr>\n";
		echo '<tr><th>Resource</th><td>' . $this->res . "</td></tr>\n";
		$size = $this->size;
		$used = $this->next - $this->data_offset + $this->partition_size;
		$free = $size - $used;
		echo '<tr><th>Size</th><td>' . $size . ' bytes (' . size_format( $size , 2 ) . ")</td></tr>\n";
		echo '<tr><th>Used</th><td>' . $used . ' bytes (' . size_format( $used , 2 ) . ', ' . number_format( $used / $size * 100, 1 ) . "%)</td></tr>\n";
		echo '<tr><th>Free</th><td>' . $free . ' bytes (' . size_format( $free , 2 ) . ', ' . number_format( $free / $size * 100, 1 ) . "%)</td></tr>\n";
		echo '<tr><th>Partition Table Size</th><td>' . $this->partition_size . ' bytes (' . size_format( $this->partition_size , 2 ) . ")</td></tr>\n";
		echo '<tr><th>Data Offset</th><td>' . $this->data_offset . ' bytes (' . size_format( $this->data_offset , 2 ) . ")</td></tr>\n";
		echo '<tr><th>Data Offset Count Offset</th><td>' . $this->data_offset_count_offset . ' bytes (' . size_format( $this->data_offset_count_offset , 2 ) . ")</td></tr>\n";
		echo '<tr><th>Next Free Data Segment Offset</th><td>' . $this->next . ' bytes (' . size_format( $this->next , 2 ) . ")</td></tr>\n";
		$next_free = $size - $this->next;
		echo '<tr><th>Next Free Data Segment Size</th><td>' . $next_free . ' bytes (' . size_format( $next_free , 2 ) . ")</td></tr>\n";
		// Figure out last added key
		$hash = $this->partition_size > $this->hash_bytes ? @ $this->_read( $this->res, $this->partition_table_offset + $this->partition_size - $this->hash_bytes, $this->hash_bytes ) : false;
		echo '<tr><th>Partition Table Seek Time</th><td>' . number_format( $this->time_seek * 1000, 1 ) . " ms</td></tr>\n";
		echo '<tr><th>Read Time</th><td>' . number_format( $this->time_read * 1000, 1 ) . " ms</td></tr>\n";
		if ( $hash !== false ) $pos = $this->partition_size - $this->hash_bytes;
		if ( ! empty( $pos ) ) {
			echo '<tr><th>Last Added Key Partition Table Entry Offset</th><td>' . ( $this->partition_table_offset + $pos ) . ' bytes (' . size_format( $this->partition_table_offset + $pos , 2 ) . ")</td></tr>\n";
			$last_partition_entry = $this->_get_partition_entry( $hash );
		}
		if ( ! empty( $last_partition_entry ) ) {
			list( $pos, $offset, $size ) = $last_partition_entry;
			$result = @ $this->_read( $this->res, $offset + 8, 4 );
		}
		if ( ! empty( $result ) ) {
			$key_size = unpack( 'N', $result )[1];
			$key_data = @ $this->_read( $this->res, $offset + 16, $key_size );
		}
		$result = false;
		if ( ! empty( $key_data ) ) {
			echo '<tr><th>Last Added Key</th><td>' . addcslashes( $key_data, "\x00..\x19\x7f..\xff\\" ) . "</td></tr>\n";
			echo '<tr><th>Last Added Key Data Offset</th><td>' . $offset . ' bytes (' . size_format( $offset , 2 ) . ")</td></tr>\n";
			echo '<tr><th>Last Added Key Data Size</th><td>' . $size . ' bytes (' . size_format( $size , 2 ) . ")</td></tr>\n";
			$result = @ $this->_read( $this->res, $this->last_key_data_offset + 8, 4 );
		}
		$key_data = false;
		if ( ! empty( $result ) ) {
			$key_size = unpack( 'N', $result )[1];
			$key_data = @ $this->_read( $this->res, $this->last_key_data_offset + 16, $key_size );
		}
		if ( ! empty( $key_data ) ) {
			echo '<tr><th>Last Changed Key</th><td>' . addcslashes( $key_data, "\x00..\x19\x7f..\xff\\" ) . "</td></tr>\n";
			echo '<tr><th>Last Changed Key Data Offset</th><td>' . $this->last_key_data_offset . ' bytes (' . size_format( $this->last_key_data_offset , 2 ) . ")</td></tr>\n";
			echo '<tr><th>Last Changed Key Data Size</th><td>' . $this->last_key_data_size . ' bytes (' . size_format( $this->last_key_data_size , 2 ) . ")</td></tr>\n";
		}
		$result = $key_data = false;
		$last_accessed_partition_entry = end( $this->partition );
		if ( $last_accessed_partition_entry !== false ) {
			list( $pos, $offset, $size ) = $last_accessed_partition_entry;
			echo '<tr><th>Last Accessed Key Partition Table Entry Offset</th><td>' . ( $this->partition_table_offset + $pos ) . ' bytes (' . size_format( $this->partition_table_offset + $pos , 2 ) . ")</td></tr>\n";
			$result = @ $this->_read( $this->res, $offset + 8, 4 );
		}
		if ( ! empty( $result ) ) {
			$key_size = unpack( 'N', $result )[1];
			$key_data = @ $this->_read( $this->res, $offset + 16, $key_size );
		}
		if ( ! empty( $key_data ) ) {
			echo '<tr><th>Last Accessed Key</th><td>' . $key_data . "</td></tr>\n";
			echo '<tr><th>Last Accessed Key Data Offset</th><td>' . $offset . ' bytes (' . size_format( $offset , 2 ) . ")</td></tr>\n";
			echo '<tr><th>Last Accessed Key Data Size</th><td>' . $size . ' bytes (' . size_format( $size , 2 ) . ")</td></tr>\n";
		}
		echo '</table></tbody>';
		echo '<br /><span class="qm-info">Backend Statistics generated in ' . number_format( ( microtime( true ) - $stats_time ) * 1000, 1 ) . ' ms</span>';
	}

	public function _get_group_key( $key, $group = 'default' ) {
		// Concatenate group and key, return the result
		$group_key = "$group:$key";
		if ( ! isset( $this->_hash_table[ $group_key ] ) )
			$this->_hash_table[ $group_key ] = hash( $this->hash_algo, $group_key, true );
		return $this->_hash_table[ $group_key ];
	}

	public function _get_partition_entry( $group_key ) {
		// Return partition entry for group key. May be false if entry does
		// not exist or has been permanently deleted.
		if ( isset( $this->partition[ $group_key ] ) ) return $this->partition[ $group_key ];
		$time_start = microtime( true );
		$pos = strpos( $this->partition_table, $group_key );
		while ( $pos !== false && $pos % $this->hash_bytes !== 0 ) $pos = strpos( $this->partition_table, $group_key, $pos + ( $this->hash_bytes - ( $pos % $this->hash_bytes ) ) );
		if ( $pos === false ) $partition_entry = false;
		else {
			//$time_start = microtime( true );
			$offset_count = @ $this->_read( $this->res, $this->data_offset_count_offset + $pos / $this->hash_bytes * 8, 8 );
			//$this->time_read += microtime( true ) - $time_start;
			if ( $offset_count === false ) {
				$partition_entry = false;
			}
			else {
				$offset = unpack( 'N', substr(  $offset_count, 0, 4 ) )[1];
				$count = unpack( 'N', substr(  $offset_count, 4, 4 ) )[1];
				$partition_entry = array( $pos, $offset, $count );
			}
		}
		$this->partition[ $group_key ] = $partition_entry;
		$this->time_seek += microtime( true ) - $time_start;
		return $partition_entry;
	}

}

if ( ! function_exists( 'ftok' ) ) {  // Windows

	function ftok( $pathname, $proj_id ) {

		// https://github.com/php/php-src/blob/master/ext/standard/ftok.c

		if ( strlen( $pathname ) === 0 ) {
			trigger_error( "Pathname is invalid", E_USER_WARNING );
			return -1;
		}

		if ( strlen( $proj_id ) !== 1 ){
			trigger_error( "Project identifier is invalid", E_USER_WARNING );
			return -1;
		}

		$st = @ stat( $pathname );
		if ( ! $st ) {
			return -1;
		}

		$key = sprintf( "%u", ( ( $st['ino'] & 0xffff ) | ( ( $st['dev'] & 0xff ) << 16 ) | ( ( ord( $proj_id[0] ) & 0xff ) << 24 ) ) );
		return $key;
	}

}

function fshmop_open( $key, $flags, $mode, $size ) {
	if ( $flags === 'a' ) $fopen_mode = 'r';
	else if ( $flags === 'c' ) $fopen_mode = 'c+';
	else if ( $flags === 'n' ) $fopen_mode = 'x+';
	else if ( $flags === 'w' ) $fopen_mode = 'r+';
	else {
		trigger_error( "fshmop_open(): invalid access mode '$flags'", E_USER_WARNING );
		return false;
	}
	$fn = FH_OBJECT_CACHE_PATH . SHM_Cache::format_id( $key, true );
	if ( $size && $flags !== 'n' ) {
		$st = @ stat( $fn );
		if ( $st === false ) {
			if ( $flags !== 'c' ) {
				trigger_error( "fshmop_open(): unable to open '$fn': No such file or directory", E_USER_WARNING );
				return false;
			}
		}
		else {
			if ( $st[ 'size' ] !== $size ) {
				trigger_error( "fshmop_open(): unable to open '$fn': Invalid size argument for access mode '$flags'", E_USER_WARNING );
				return false;
			}
		}
	}
	else if ( ! $size && ( $flags === 'c' || $flags === 'n' ) ) {
		trigger_error( "fshmop_open(): File size must be greater than zero", E_USER_WARNING );
		return false;
	}
	$res = fopen( $fn, $fopen_mode );
	if ( $res === false ) return false;
	if ( $flags !== 'n' && isset( $st ) && $st !== false ) return $res;
	if ( ! chmod( $fn, $mode ) ) return false;
	if ( ! ftruncate( $res, $size ) ) return false;
	return $res;
}

function fshmop_close( $res ) {
	@ fclose( $res );
}

function fshmop_delete( $res ) {
	$meta = stream_get_meta_data( $res );
	fclose( $res );
	return unlink( $meta[ 'uri' ] );
}

function fshmop_read( $res, $start, $count ) {
	if ( fseek( $res, $start ) === 0 )
		return fread( $res, $count );
	else
		return false;
}

function fshmop_size( $res ) {
	return fstat( $res )[ 'size' ];
}

function fshmop_write( $res, $data, $offset ) {
	if ( fseek( $res, $offset ) === 0 )
		return fwrite( $res, $data, strlen( $data ) );
	else
		return false;
}

if ( ! function_exists( 'shmop_open' ) && defined( 'FH_OBJECT_CACHE_SHM_USE_FILE_BACKEND' ) && FH_OBJECT_CACHE_SHM_USE_FILE_BACKEND ) {

	function shmop_open( $key, $flags, $mode, $size ) {
		return fshmop_open( $key, $flags, $mode, $size );
	}

	function shmop_close( $res ) {
		fshmop_close( $res );
	}

	function shmop_delete( $res ) {
		return fshmop_delete( $res );
	}

	function shmop_read( $res, $start, $count ) {
		return fshmop_read( $res, $start, $count );
	}

	function shmop_size( $res ) {
		return fshmop_size( $res );
	}

	function shmop_write( $res, $data, $offset ) {
		return fshmop_write( $res, $data, $offset );
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

	/* Persistent object cache start */
	private $cache_dir;
	private $flock_filename = '.lock';
	private $lock_mode = 0;
	private $mutex = null;
	private $deleted = array();
	private $dirty_groups = array();
	private $non_persistent_groups = array();
	private $expires = array();
	private $expirations = 0;
	private $expirations_groups = array();
	private $persistent_cache_reads = 0;
	private $persistent_cache_reads_hits = 0;
	private $persistent_cache_reads_hits_groups = array();
	private $persistent_cache_misses = 0;
	private $persistent_cache_hits = 0;
	private $cache_hits_groups = array();
	private $persistent_cache_hits_groups = array();
	private $cache_misses_groups = array();
	private $persistent_cache_groups = array();
	private $persistent_cache_errors_groups = array();
	private $persistent_cache_persist_errors_groups = array();
	private $actual_persists = 0;
	private $persists = 0;
	private $cache_deletions = 0;
	private $cache_deletions_groups = array();
	private $resets = 0;
	private $flushes = 0;
	private $debug;
	private $time_persistent_cache_write = 0;
	private $time_read = 0;
	private $time_total = 0;
	private $now;
	private $expiration_time = 0;
	private $backend;
	public $cache_writes = 0;
	private $closed = false;
	private $time_lock = 0;
	private $time_lock_ex_start = 0;
	private $time_lock_ex = 0;
	private $use_persistent_cache = true;
	/* Persistent object cache end */

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

		/* Persistent object cache start */
		return $this->_add_set_replace( 'add', $key, $data, $group, $expire );
		/* Persistent object cache end */
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
		/* Persistent object cache start */
		return $this->_incr_decr( 'decr', $key, $offset, $group );
		/* Persistent object cache end */
	}

	private function _incr_decr( $cmd, $key, $offset = 1, $group = 'default' ) {
		if ( empty( $group ) )
			$group = 'default';

		if ( $this->multisite && ! isset( $this->global_groups[ $group ] ) )
			$key = $this->blog_prefix . $key;

		/* Persistent object cache start */
		$offset = (int) $offset;

		$value = $this->_backend_cmd( $cmd, $key, $offset, $group );
		if ( $value === false ) {
			/* Persistent object cache end */
			if ( ! $this->_exists( $key, $group ) )
				return false;

			$value = $this->cache[ $group ][ $key ];

			if ( ! is_numeric( $value ) )
				$value = 0;

			if ( $cmd === 'incr' )
				$value += $offset;
			else
				$value -= $offset;

			if ( $value < 0 )
				$value = 0;
			/* Persistent object cache start */
		}
		/* Persistent object cache end */
		$this->cache[ $group ][ $key ] = $value;
		/* Persistent object cache start */
		if ($this->debug) $time_start = microtime(true);
		$this->cache_writes ++;
		$this->dirty_groups[$group][$key] = true;
        if ( isset( $this->persistent_cache_groups[$group][$key] ) )
			$this->persistent_cache_groups[$group][$key] = false;
		if ($this->debug) $this->time_total += microtime(true) - $time_start;
		/* Persistent object cache end */
		return $value;
	}

	private function _backend_cmd( $cmd, $key, $data, $group = 'default', $expire = 0 ) {
		// Add, set, replace, incr, decr or delete
		if ( ! isset( $this->non_persistent_groups[ $group ] ) ) {
			if ( $this->lock_mode !== LOCK_EX ) {
				if ($this->debug) $time_start = microtime(true);
				if ( $this->acquire_lock() )
					$this->backend->read_partition_table();
				if ($this->debug) $this->time_total += microtime(true) - $time_start;
			}
			if ( $this->lock_mode !== 0 ) {
				if ($this->debug) $time_start = microtime(true);
				switch ( $cmd ) {
					case 'incr' :
					case 'decr' :
						$result = $this->backend->$cmd( $key, $data, $group );
						break;
					case 'delete' :
						$result = $this->backend->delete( $key, $group );
						break;
					default :
						// Add, set or replace
						$result = $this->backend->$cmd( $key, $data, $group, $expire );
				}
				if ($this->debug) $this->time_persistent_cache_write += microtime(true) - $time_start;
				$this->acquire_lock( LOCK_SH );
				if ($this->debug) $this->time_total += microtime(true) - $time_start;
				return $result;
			}
		}
		return false;
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

		/* Persistent object cache start */
		$result = $this->_backend_cmd( 'delete', $key, null, $group );
		if ( $result === false ) {
			/* Persistent object cache end */

			if ( ! $this->_exists( $key, $group ) )
				return false;

			/* Persistent object cache start */
		}
		/* Persistent object cache end */

		unset( $this->cache[$group][$key] );

		/* Persistent object cache start */
        if ($this->debug) $time_start = microtime(true);
		$this->dirty_groups[$group][$key] = false;
        $this->deleted[$group][$key] = true;
		unset( $this->expires[$group][$key] );
        if ( isset( $this->persistent_cache_groups[$group][$key] ) )
			$this->persistent_cache_groups[$group][$key] = false;
		$this->cache_deletions += 1;
		if ($this->debug) {
			if (!isset($this->cache_deletions_groups[$group]))
				$this->cache_deletions_groups[$group] = 1;
			else
				$this->cache_deletions_groups[$group] += 1;
			$this->time_total += microtime(true) - $time_start;
			if ($this->debug > 1) {
				file_put_contents( __DIR__ . '/.SHM_Partitioned_Cache.log',
								   date( 'Y-m-d H:i:s,v' ) .
								   " WP_Object_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Deleted '$group:$key'\n", FILE_APPEND );
			}
		}
		/* Persistent object cache end */

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
		/* Persistent object cache start */
        if ($this->debug) $time_start = microtime(true);

		if ( ! $this->acquire_lock() ) {
			if ($this->debug) $this->time_total += microtime(true) - $time_start;
			return false;
		}

		$this->backend->flush();

		$this->acquire_lock( LOCK_SH );
		$this->deleted = array();
		$this->dirty_groups = array();
		$this->flushes += 1;
		if ($this->debug) $this->time_total += microtime(true) - $time_start;
		/* Persistent object cache end */

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

		/* Persistent object cache start */
		$id = $key;
		/* Persistent object cache end */
		if ( $this->multisite && ! isset( $this->global_groups[ $group ] ) )
			$key = $this->blog_prefix . $key;

		/* Persistent object cache start */
        if ($this->debug) $time_start = microtime(true);
        $use_persistent_cache = $this->use_persistent_cache || $force;
        $is_persistent_group = !isset($this->non_persistent_groups[$group]);
		if ($force && $is_persistent_group) {
			$this->_log("FORCE REFETCH FROM PERSISTENT CACHE FOR $group.$key");
			if ($this->_exists( $key, $group ))
				$this->_log("BEFORE REFETCH: $group.$key = " . json_encode($this->cache[$group][$key], JSON_PRETTY_PRINT));
		}
		if ($use_persistent_cache && $is_persistent_group &&
			($force || !isset($this->cache[$group][$key]))) {
			// If we already hold exclusive lock, no need to (re-)acquire shared lock
			if ( $this->lock_mode !== LOCK_EX &&
				 ! $this->acquire_lock( LOCK_SH | LOCK_NB, $wouldblock ) ) {
				// Cache in use by another process.
				// Acquire blocking shared read lock and re-read partition table
				if ( $wouldblock && $this->acquire_lock( LOCK_SH ) )
					$this->backend->read_partition_table();
			}
			if ( $this->lock_mode !== 0 ) {
				$this->persistent_cache_reads += 1;
				if ($this->debug) $time_read_start = microtime(true);
				$result = $this->backend->get($key, $group);
				if ($this->debug) {
					$this->time_read += microtime(true) - $time_read_start;
				}
				if ($result !== false) {
					list($value, $expire, $atime) = $result;
					if (!$expire || $expire > $this->now) {
						$this->cache[$group][$key] = $value;
						$this->expires[$group][$key] = $expire;
						$this->persistent_cache_groups[$group][$key] = true;
						$this->persistent_cache_reads_hits += 1;
						if ($this->debug) {
							if ( ! isset($this->persistent_cache_reads_hits_groups[$group]) )
								$this->persistent_cache_reads_hits_groups[$group] = 1;
							else
								$this->persistent_cache_reads_hits_groups[$group] += 1;
						}
					}
					else {
						$this->persistent_cache_groups[$group][$key] = false;
						$this->expirations += 1;
						if ($this->debug) {
							if ( ! isset($this->expirations_groups[$group]) )
								$this->expirations_groups[$group] = 1;
							else
								$this->expirations_groups[$group] += 1;
						}
					}
				}
				else {
					$this->persistent_cache_groups[$group][$key] = false;
					$this->persistent_cache_misses += 1;
				}
			}
		}
		if ($this->debug) $this->time_total += microtime(true) - $time_start;
		/* Persistent object cache end */

		if ( $this->_exists( $key, $group ) ) {
			$found = true;
			$this->cache_hits += 1;
			/* Persistent object cache start */
			if ($force && $is_persistent_group)
				$this->_log("AFTER REFETCH: $group.$key = " . json_encode($this->cache[$group][$key], JSON_PRETTY_PRINT));
			if ($this->debug) {
				$time_start = microtime(true);
				if (!isset($this->cache_hits_groups[$group]))
					$this->cache_hits_groups[$group] = 1;
				else
					$this->cache_hits_groups[$group] += 1;
				if (!empty($this->persistent_cache_groups[$group][$key])) {
					$this->persistent_cache_hits += 1;
					if (!isset($this->persistent_cache_hits_groups[$group]))
						$this->persistent_cache_hits_groups[$group] = 1;
					else
						$this->persistent_cache_hits_groups[$group] += 1;
				}
				$this->time_total += microtime(true) - $time_start;
			}
			/* Persistent object cache end */
			if ( is_object($this->cache[$group][$key]) )
				return clone $this->cache[$group][$key];
			else
				return $this->cache[$group][$key];
		}

		/* Persistent object cache start */
        if ($this->debug) {
			$time_start = microtime(true);
			if (!isset($this->cache_misses_groups[$group]))
				$this->cache_misses_groups[$group] = 1;
			else
				$this->cache_misses_groups[$group] += 1;
			$this->time_total += microtime(true) - $time_start;
		}
		/* Persistent object cache end */

		$found = false;
		$this->cache_misses += 1;
		return false;
	}

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
		/* Persistent object cache start */
		return $this->_incr_decr( 'incr', $key, $offset, $group );
		/* Persistent object cache end */
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
		/* Persistent object cache start */
		return $this->_add_set_replace( 'replace', $key, $data, $group, $expire );
		/* Persistent object cache end */
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

				/* Persistent object cache start */
				if ($this->debug) $time_start = microtime(true);
				$this->dirty_groups[$group] = array();
				unset( $this->deleted[$group] );
				unset( $this->expires[$group] );
				unset($this->persistent_cache_groups[$group]);
				if ($this->debug) $this->time_total += microtime(true) - $time_start;
				/* Persistent object cache end */
			}
		}

		/* Persistent object cache start */
		$this->resets += 1;
		/* Persistent object cache end */
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
		/* Persistent object cache start */
		return $this->_add_set_replace( 'set', $key, $data, $group, $expire );
		/* Persistent object cache end */
	}

	private function _add_set_replace( $cmd, $key, $data, $group = 'default', $expire = 0 ) {
		if ( empty( $group ) )
			$group = 'default';

		if ( $this->multisite && ! isset( $this->global_groups[ $group ] ) )
			$key = $this->blog_prefix . $key;

		/* Persistent object cache start */
		$expire = (int) $expire;
		if ( $expire ) $expire = $this->now + $expire;
		$result = $this->_backend_cmd( $cmd, $key, $data, $group, $expire );
		if ( ! $result ) {
			/* Persistent object cache end */
			if ( ( $cmd === 'add' && $this->_exists( $key, $group ) ) ||
				 ( $cmd === 'replace' && ! $this->_exists( $key, $group ) ) )
				return false;

			$result = true;
			/* Persistent object cache start */
		}
		/* Persistent object cache end */
		if ( is_object( $data ) )
			$data = clone $data;

		$this->cache[$group][$key] = $data;
		/* Persistent object cache start */
		if ($this->debug) $time_start = microtime(true);
		$this->cache_writes ++;
		$this->dirty_groups[$group][$key] = true;
		if ($expire) $this->expires[$group][$key] = $expire;
		unset($this->deleted[$group][$key]);
        if ( isset( $this->persistent_cache_groups[$group][$key] ) )
			$this->persistent_cache_groups[$group][$key] = false;
		if ($this->debug) $this->time_total += microtime(true) - $time_start;
		/* Persistent object cache end */
		return $result;
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
		$stats_time = microtime( true );
		/* Persistent object cache start */
		if ( ! $this->debug ) {
			echo '<p>Define FH_OBJECT_CACHE_DEBUG for additional stats</p>';
		}
		echo '<table><tbody>';
		echo "<tr><th>Cache Hits</th><td>{$this->cache_hits}";
		if ( $this->debug ) echo " ({$this->persistent_cache_hits} from persistent cache)";
		echo '</td></tr>';
		echo "<tr><th>Cache Misses</th><td>{$this->cache_misses}</td></tr>";
		$total = $this->cache_misses + $this->cache_hits;
		echo '<tr><th>Hit Rate</th><td>' . number_format( 100 / $total * $this->cache_hits, 1 ) . '%</td></tr>';
		echo "<tr><th>Persistent Cache Reads</th><td>{$this->persistent_cache_reads}</td></tr>";
		echo "<tr><th>Persistent Cache Hits</th><td>{$this->persistent_cache_reads_hits}</td></tr>";
		$total = $this->cache_misses + $this->persistent_cache_reads_hits;
		echo '<tr><th>Persistent Cache Hit Rate</th><td>' . ( $total ? number_format( 100 / $total * $this->persistent_cache_reads_hits, 1 ) . '%' : ( $this->debug ? '0%' : 'Unknown' ) ) . '</td></tr>';
		if ( $this->debug ) {
			echo '<tr><th>Persistent Cache Seek Time</th><td>' . number_format( $this->backend->time_seek * 1000, 1 ) . ' ms</td></tr>';
			echo '<tr><th>Persistent Cache Read Time</th><td>' . number_format( $this->backend->time_read * 1000, 1 ) . ' ms</td></tr>';
			echo '<tr><th>Persistent Cache Processing Time</th><td>' . number_format( ( $this->time_read - $this->backend->time_read - $this->backend->time_seek ) * 1000, 1) . ' ms</td></tr>';
		}
		$hours = floor( $this->expiration_time / 60 / 60 );
		$minutes = floor( ( $this->expiration_time - $hours * 60 * 60 ) / 60 );
		$seconds = $this->expiration_time - $hours * 60 * 60 - $minutes * 60;
		echo '<tr><th>Persistent Cache Entry Default Lifetime</th><td>' . ( $this->expiration_time ? "{$hours}h {$minutes}m {$seconds}s" : "Indefinite" ) . "</td></tr>";
		echo "<tr><th>Persistent Cache Entry Expirations</th><td>{$this->expirations}</td></tr>";
		echo "<tr><th>Cache Entry Writes (w/o deletions)</th><td>{$this->cache_writes}</td></tr>";
		echo "<tr><th>Cache Entry Deletions</th><td>{$this->cache_deletions}</td></tr>";
		echo "<tr><th>Cache Persists</th><td>{$this->actual_persists}</td></tr>";
		echo "<tr><th>Cache Flushes</th><td>{$this->flushes}</td></tr>";
		echo "<tr><th>Cache Resets (deprecated)</th><td>{$this->resets}";
		if ( $this->debug ) {
			echo '<tr><th>Persistent Cache Write Time</th><td>' . number_format( $this->time_persistent_cache_write * 1000, 1 ) . ' ms</td></tr>';
			echo '<tr><th>Persistent Cache Lock Acquisition Time</th><td>' . number_format( $this->time_lock * 1000, 1 ) . ' ms</td></tr>';
			echo '<tr><th>Persistent Cache Exclusive Lock Time</th><td>' . number_format( $this->time_lock_ex * 1000, 1 ) . ' ms</td></tr>';
			echo '<tr><th>Cache Total Time</th><td>' . number_format( $this->time_total * 1000, 1 ) . ' ms</td></tr>';
		}
		$total_entries = 0;
		$total_size = 0;
		$table_rows = array();
		foreach ($this->cache as $group => $cache) {
			$cache_hits_groups = isset($this->cache_hits_groups[$group]) ? $this->cache_hits_groups[$group] : ( $this->debug ? 0 : 'Unknown' );
			$persistent_cache_hits_groups = isset($this->persistent_cache_hits_groups[$group]) ? $this->persistent_cache_hits_groups[$group] : ( $this->debug ? 0 : 'Unknown' );
			$cache_misses_groups = isset($this->cache_misses_groups[$group]) ? $this->cache_misses_groups[$group] : ( $this->debug ? 0 : 'Unknown' );
			unset($this->cache_misses_groups[$group]);
			$reads = $cache_hits_groups + $cache_misses_groups;
			$hit_rate = $reads ? number_format( ( 100 / $reads ) * $cache_hits_groups, 1 ) . '%' : ( $this->debug ? '0%' : 'Unknown' );
			$updated = isset($this->dirty_groups[$group]) ? 'Yes' : 'No';
			$persist = isset($this->non_persistent_groups[$group]) ? 'No' : 'Yes';
			$global = isset($this->global_groups[$group]) ? 'Yes' : 'No';
			$entries = count($cache);
			$total_entries += $entries;
			$persistent_cache_reads_hits = isset($this->persistent_cache_reads_hits_groups[$group]) ? $this->persistent_cache_reads_hits_groups[$group] : ( $this->debug ? 0 : 'Unknown' );
			$persistent_cache_reads = $persistent_cache_reads_hits + $cache_misses_groups;
			$persistent_cache_reads_hit_rate = $persistent_cache_reads ? number_format( ( 100 / $persistent_cache_reads ) * $persistent_cache_reads_hits, 1 ) . '%' : ( $this->debug ? '0%' : 'Unknown' );
			$expired = isset($this->expirations_groups[$group]) ? $this->expirations_groups[$group] : ( $this->debug ? 0 : 'Unknown' );
			$deleted = isset($this->cache_deletions_groups[$group]) ? $this->cache_deletions_groups[$group] : ( $this->debug ? 0 : 'Unknown' );
			unset($this->cache_deletions_groups[$group]);
			$size = strlen( serialize( $cache ) ) / 1024;
			$total_size += $size;
			$table_rows[] = '<tr' . ($persist === 'No' ? ' style="opacity: .5"' : '') . '><td' . ($global === 'Yes' ? ' style="font-style: oblique !important"' : '') . ">$group</td><td>$cache_hits_groups</td><td>$persistent_cache_hits_groups</td><td>$cache_misses_groups</td><td>$hit_rate</td><td>" . ( isset($this->persistent_cache_groups[$group]) ? count($this->persistent_cache_groups[$group]) : ( $this->debug ? 0 : 'Unknown' ) ) . "</td><td>$persistent_cache_reads_hits</td><td>$persistent_cache_reads_hit_rate</td><td>$updated</td><td>$persist</td><td>$global</td><td>$expired</td><td>$deleted</td><td>$entries</td><td>" . number_format( $size, 2 ) . '</td></tr>';
		}
		echo "<tr><th>Accessed Cache Entries</th><td>$total_entries</td></tr>";
		echo '<tr><th>Accessed Cache Entries Combined Size</th><td>' . number_format( $total_size, 2 ) . ' KB</td></tr>';
		echo '<tr><th>Global Groups</th><td><span style="font-style: oblique !important">' . implode(', ', array_keys($this->global_groups)) . '</span></td></tr>';
		echo '<tr><th>Non-Persistent Groups</th><td><span style="opacity: .5">' . implode(', ', array_keys($this->non_persistent_groups)) . '</span></td></tr>';
		if (!empty($this->persistent_cache_errors_groups)) echo '<tr><th>File Cache Read Errors</th><td>' . implode(', ', array_keys($this->persistent_cache_errors_groups)) . '</td></tr>';
		if (!empty($this->persistent_cache_persist_errors_groups)) echo '<tr><th>File Cache Write Errors</th><td>' . implode(', ', array_keys($this->persistent_cache_persist_errors_groups)) . '</td></tr>';
		echo '</tbody></table>';
		echo '<h4>Cache Hits</h4>';
		echo '<table><thead><tr><th>Group</th><th>Hits</th><th>Hits From Persistent Cache</th><th>Misses</th><th>Hit Rate</th><th>Persistent Cache Reads</th><th>Persistent Cache Hits</th><th>Persistent Cache Hit Rate</th><th>Changed</th><th>Persistent</th><th>Global</th><th>Expirations</th><th>Deletions</th><th>Entries</th><th>Size (KB)</th></tr></thead><tbody>';
		echo implode( "\n", $table_rows );
		echo '</tbody></table>';
		foreach ( array( 'Misses' => $this->cache_misses_groups,
						 'Expirations' => $this->expirations_groups,
						 'Deletions' => $this->cache_deletions_groups ) as $what => $groups ) {
			if ( ! empty( $groups ) ) {
				echo "<h4>Cache $what</h4>";
				echo "<table><thead><tr><th>Group</th><th>Hits</th><th>Hits From Persistent Cache</th><th>Misses</th><th>Persistent Cache Reads</th><th>Changed</th><th>Persistent</th><th>Global</th><th>Expirations</th><th>Deletions</th></tr></thead><tbody>";
				foreach ($groups as $group => $count) {
					$cache_hits_groups = isset($this->cache_hits_groups[$group]) ? $this->cache_hits_groups[$group] : ( $this->debug ? 0 : 'Unknown' );
					$persistent_cache_hits_groups = isset($this->persistent_cache_hits_groups[$group]) ? $this->persistent_cache_hits_groups[$group] : ( $this->debug ? 0 : 'Unknown' );
					$updated = isset($this->dirty_groups[$group]) ? 'Yes' : 'No';
					$persist = isset($this->non_persistent_groups[$group]) ? 'No' : 'Yes';
					$global = isset($this->global_groups[$group]) ? 'Yes' : 'No';
					$persistent_cache_reads = isset($this->persistent_cache_groups[$group]) ? count($this->persistent_cache_groups[$group]) : 0;
					$cache_misses_groups = isset($this->cache_misses_groups[$group]) ? $this->cache_misses_groups[$group] : ( $this->debug ? 0 : 'Unknown' );
					$expired = isset($this->expirations_groups[$group]) ? $this->expirations_groups[$group] : ( $this->debug ? 0 : 'Unknown' );
					unset($this->expirations_groups[$group]);
					$deleted = isset($this->cache_deletions_groups[$group]) ? $this->cache_deletions_groups[$group] : ( $this->debug ? 0 : 'Unknown' );
					echo '<tr' . (isset($this->non_persistent_groups[$group]) ? ' style="opacity: .5"' : '') . '><td' . (isset($this->global_groups[$group]) ? ' style="font-style: oblique !important"' : '') . ">$group</td><td>$cache_hits_groups</td><td>$persistent_cache_hits_groups</td><td>$cache_misses_groups</td><td>$persistent_cache_reads</td><td>$updated</td><td>$persist</td><td>$global</td><td>$expired</td><td>$deleted</td></tr>";
				}
				echo '</tbody></table>';
			}
		}
		echo '<br /><span class="qm-info">Object Cache Statistics generated in ' . number_format( ( microtime( true ) - $stats_time ) * 1000, 1 ) . ' ms</span>';
		if ( method_exists( $this->backend, 'stats' ) ) {
			$this->backend->stats();
		}
		/* Persistent object cache end */
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
		/* Persistent object cache start */
		if ($cache === null) $cache = &$this->cache;
		/* Persistent object cache end */
		return isset( $cache[ $group ] ) && ( isset( $cache[ $group ][ $key ] ) || array_key_exists( $key, $cache[ $group ] ) );
	}

	/**
	 * Sets up object properties; PHP 5 style constructor
	 *
	 * @since 2.0.8
	 */
	public function __construct() {
		$this->multisite = is_multisite();
		$this->blog_prefix =  $this->multisite ? get_current_blog_id() . ':' : '';

		/* Persistent object cache start */
		$this->debug = defined('FH_OBJECT_CACHE_DEBUG') ? FH_OBJECT_CACHE_DEBUG : 0;
		$this->expiration_time = defined('FH_OBJECT_CACHE_LIFETIME') ? FH_OBJECT_CACHE_LIFETIME : 0;
        if ($this->debug) $time_start = microtime(true);
		if (defined('FH_OBJECT_CACHE_PATH'))
			$this->cache_dir = FH_OBJECT_CACHE_PATH;
		else
			// Using the correct separator eliminates some cache flush errors on Windows
			$this->cache_dir = ABSPATH.'wp-content'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'fh-object-cache'.DIRECTORY_SEPARATOR;

		$this->now = time();

		$this->_log($_SERVER['REQUEST_URI']);

		$this->use_persistent_cache = $this->acquire_lock( LOCK_SH );

		$this->backend = new SHM_Partitioned_Cache( defined( 'FH_OBJECT_CACHE_SHM_SIZE' ) ? FH_OBJECT_CACHE_SHM_SIZE : 16 * 1024 * 1024 );
		add_action( 'shm_object_cache_defrag', array( &$this, 'defrag'), 10, 0 );

		$this->lock_mode = 0;

		add_action( 'edit_post', array( &$this, 'flush_taxonomies' ), 10, 1 );

		if ($this->debug) $this->time_total += microtime(true) - $time_start;
		/* Persistent object cache end */

		/**
		 * @todo This should be moved to the PHP4 style constructor, PHP5
		 * already calls __destruct()
		 */
		//register_shutdown_function( array( $this, '__destruct' ) );
	}

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
		/* Persistent object cache start */
		$this->close();
		/* Persistent object cache end */

		return true;
	}

	/* Persistent object cache start */
	public function close() {
		if ( ! $this->closed ) {
			$this->persist();
			$this->closed = true;
			if ( function_exists( 'wp_next_scheduled' ) && ! wp_next_scheduled ( 'shm_object_cache_defrag' ) )
				wp_schedule_event( time(), 'hourly', 'shm_object_cache_defrag' );
		}

		$this->release_lock();

		return true;
	}
	/* Persistent object cache end */

	/* Persistent object cache start */
	public function flush_taxonomies( $post_id ) {
		$taxonomies = get_post_taxonomies( $post_id );
		foreach ( $taxonomies as $taxonomy ) {
			$group = $taxonomy . '_relationships';
			if ( $this->get( $post_id, $group ) !== false && $this->delete( $post_id, $group ) )
				$this->_log('Flushed taxonomy ' . $taxonomy . ' for post ' . $post_id);
		}
	}
	public function flush_bp_notifications() {
		$this->delete( get_current_user_id(), 'bp_notifications_grouped_notifications' );
		$this->_log( "Deleted bp_notifications_grouped_notifications:" . get_current_user_id(), 1 );
	}

	public function defrag() {
		/* Persistent object cache start */
        if ($this->debug) $time_start = microtime(true);

		if ( $this->acquire_lock() ) {
			$this->backend->defrag();
			$this->acquire_lock( LOCK_SH );
		}

		if ($this->debug) $this->time_total += microtime(true) - $time_start;
		/* Persistent object cache end */
	}

	public function persist($groups=null) {
		// XXX: TBD
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

	private function _log( $msg, $loglevel=2 ) {
		if ($this->debug >= $loglevel) {
			$time = microtime(true);
			$secs = floor($time);
			$ms = sprintf("%03d", ($time - $secs) * 1000);
			$log = strftime("%Y-%m-%d %H:%M:%S") . ",$ms $msg\n";
			@file_put_contents($this->cache_dir . '.object-cache.log', $log, FILE_APPEND);
		}
	}

	public function add_non_persistent_groups ( $groups ) {
		$groups = (array) $groups;

		$groups = array_fill_keys( $groups, true );
		$this->non_persistent_groups = array_merge( $this->non_persistent_groups, $groups );
	}

	function acquire_lock( $operation = LOCK_EX, &$wouldblock = null ) {
		$time_start = microtime( true );
		if ( null === $this->mutex ) $this->mutex = @fopen($this->cache_dir.$this->flock_filename, 'c');
		$lock_mode = $this->lock_mode;
		$this->lock_mode = 0;
		if ( false === $this->mutex ) {
			$this->time_lock += microtime( true ) - $time_start;
			$this->_log( "Couldn't acquire " . ( $operation === LOCK_EX ? "exclusive" : "shared" ) . " lock", 1 );
			return false;
		}
		else {
			$locked = flock($this->mutex, $operation, $wouldblock);
			if ( $operation === LOCK_EX ) {
				if ( $lock_mode !== LOCK_EX ) $this->time_lock_ex_start = microtime( true );
			}
			else if ( $lock_mode === LOCK_EX ) $this->time_lock_ex += microtime( true ) - $this->time_lock_ex_start;
			if ( $locked ) $this->lock_mode = $operation;
			$this->time_lock += microtime( true ) - $time_start;
			return $locked;
		}
	}

	function release_lock() {
		if ( $this->mutex ) {
			$time_start = microtime( true );
			flock($this->mutex, LOCK_UN);
			if ( $this->lock_mode === LOCK_EX ) $this->time_lock_ex += microtime( true ) - $this->time_lock_ex_start;
			fclose($this->mutex);
			$this->mutex = null;
			$this->lock_mode = 0;
			$this->time_lock += microtime( true ) - $time_start;
		}
	}
	/* Persistent object cache end */
}

function fh_get_callee() {
	$backtrace = debug_backtrace();
	$sequence = array();
	while ( $next = next( $backtrace ) ) {
		$sequence[] = ( isset( $next['class'] ) ? $next['class'] . $next['type'] : '' ) . $next['function'];
	}
	return implode( ' -> ', array_reverse( $sequence ) );
}
