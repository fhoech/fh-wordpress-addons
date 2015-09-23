<?php
/**
 * Object Cache API
 *
 * @link https://codex.wordpress.org/Function_Reference/WP_Cache
 *
 * @package WordPress
 * @subpackage Cache
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
	private $non_persistent_groups = array('bp_notifications' => true);
	private $expires = array();
	private $expirations = 0;
	private $expirations_groups = array();
	private $mtime = array();
	private $ajax;
	private $cron;
	private $skip;
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
	private $time_total = 0;
	private $now;
	private $expiration_time = 900;
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
	 * @access private
	 * @var int
	 */
	private $cache_hits = 0;

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
		$this->dirty_groups[$group] = true;
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

		unset( $this->cache[$group][$key] );
		/* File-based object cache start */
        if ($this->debug) $time_start = microtime(true);
        $this->deleted[$group][$key] = true;
		$this->dirty_groups[$group] = true;
		unset( $this->expires[$group][$key] );
		if ($this->debug) {
			$this->cache_deletions += 1;
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

		$id = $key;
		if ( $this->multisite && ! isset( $this->global_groups[ $group ] ) )
			$key = $this->blog_prefix . $key;

		/* File-based object cache start */
        if ($this->debug) $time_start = microtime(true);
		//if ($force) {
			//$log = @file_get_contents($this->cache_dir . 'object-cache.log');
			//$log .= "FORCE REFETCH FROM PERSISTENT CACHE\n";
			//@file_put_contents($this->cache_dir . 'object-cache.log', $log);
		//}
		if ($force || (!$this->skip && !isset($this->file_cache_groups[$group]))) {
			$cache_file = $this->cache_dir.$group.'.php';
			if (file_exists($cache_file)) {
				if ($this->debug) $time_disk_read_start = microtime(true);
				$this->file_cache_groups[$group] = unserialize(substr(@ file_get_contents($cache_file), strlen(CACHE_SERIAL_HEADER), -strlen(CACHE_SERIAL_FOOTER)));
				if ($this->debug) $this->time_disk_read += microtime(true) - $time_disk_read_start;
				if (false === $this->file_cache_groups[$group]) {
					$this->file_cache_errors_groups[$group] = true;
					$this->file_cache_groups[$group] = array();
				}
				else {
					if (!$force && isset($this->deleted[$group])) foreach ($this->deleted[$group] as $deleted => $value) unset($this->file_cache_groups[$group][$deleted]);
					if (isset($this->cache[$group])) {
						$this->cache[$group] = array_replace($this->file_cache_groups[$group], $this->cache[$group]);
						if ($force) $this->cache[$group][$key] = $this->file_cache_groups[$group][$key];
					}
					else $this->cache[$group] = $this->file_cache_groups[$group];
					$this->file_cache_groups[$group] = array_fill_keys(array_keys($this->file_cache_groups[$group]), true);
					$this->mtime[$group] = filemtime($cache_file);
					//if ($group == 'options' && $id == 'alloptions') {
						//$log = @file_get_contents($this->cache_dir . 'object-cache.log');
						//$log .= "GET $group.$id\n";
						//@file_put_contents($this->cache_dir . 'object-cache.log', $log);
					//}
				}
			}
			else $this->file_cache_groups[$group] = array();
		}
		if ($this->debug) $this->time_total += microtime(true) - $time_start;
		/* File-based object cache end */

		if ( $this->_exists( $key, $group ) && ! $this->_expire( $key, $group ) ) {
			$found = true;
			$this->cache_hits += 1;
			/* File-based object cache start */
			if ($this->debug) {
				$time_start = microtime(true);
				if (!isset($this->cache_hits_groups[$group]))
					$this->cache_hits_groups[$group] = 1;
				else
					$this->cache_hits_groups[$group] += 1;
				if ($this->_exists( $key, $group, $this->file_cache_groups )) {
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
		$this->dirty_groups[$group] = true;
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

		$this->resets += 1;
	}

	/**
	 * Sets the data contents into the cache
	 *
	 * The cache contents is grouped by the $group parameter followed by the
	 * $key. This allows for duplicate ids in unique groups. Therefore, naming of
	 * the group should be used with care and should follow normal function
	 * naming guidelines outside of core WordPress usage.
	 *
	 * The $expire parameter is not used, because the cache will automatically
	 * expire for each time a page is accessed and PHP finishes. The method is
	 * more for cache plugins which use files.
	 *
	 * @since 2.0.0
	 *
	 * @param int|string $key What to call the contents in the cache
	 * @param mixed $data The contents to store in the cache
	 * @param string $group Where to group the cache contents
	 * @param int $expire Not Used
	 * @return true Always returns true
	 */
	public function set( $key, $data, $group = 'default', $expire = 0 ) {
		if ( empty( $group ) )
			$group = 'default';

		//if ($group == 'options' && $key == 'alloptions') {
			//$log = @file_get_contents($this->cache_dir . 'object-cache.log');
			//if (isset($this->cache[$group][$key]['cron'])) $log .= "WHERE $group.$key.cron =\n" . json_encode(unserialize($this->cache[$group][$key]['cron']), JSON_PRETTY_PRINT) . "\n";
			//$log .= "SET $group.$key.cron =\n" . json_encode(unserialize($data['cron']), JSON_PRETTY_PRINT) . "\n";
			//@file_put_contents($this->cache_dir . 'object-cache.log', $log);
		//}

		if ( $this->multisite && ! isset( $this->global_groups[ $group ] ) )
			$key = $this->blog_prefix . $key;

		if ( is_object( $data ) )
			$data = clone $data;

		/* File-based object cache start */
        if ($this->debug) $time_start = microtime(true);
		if (!isset($this->dirty_groups[$group]) &&
			(!$this->_exists($key, $group) ||
			 $this->cache[$group][$key] != $data))
			$this->dirty_groups[$group] = true;
		//if (!array_key_exists($group, $this->expires))
			//$this->expires[$group] = array();
		if ($expire) $this->expires[$group][$key] = $expire;
		unset($this->deleted[$group][$key]);
        unset($this->file_cache_groups[$group][$key]);
		if ($this->debug) $this->time_total += microtime(true) - $time_start;
		/* File-based object cache end */

		$this->cache[$group][$key] = $data;
		/* File-based object cache start */
		if ($group == 'transient' && $key == 'doing_cron') {
			// We need to persist the value right away because spawn_cron will
			// issue a POST while the cache is still alive, so the destructor
			// won't cause an automatic persist, and wp-cron.php spawned via the
			// POST will try to read the value from the persistent cache.
			$this->persist(array('transient'));
		}
		else if ($group == 'options' && isset($_POST['action']) &&
				 strpos($_POST['action'], 'save-') === 0) {
			$this->persist(array('options'));
		}
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
		echo "<p>";
		echo "<strong>Cache Hits:</strong> {$this->cache_hits} ({$this->file_cache_hits} from disk)";
		echo "</p>";
		echo '<table border="1" style="border-collapse: collapse"><tr><th style="padding: .1em .3em">Group</th><th style="padding: .1em .3em">Hits</th><th style="padding: .1em .3em">From Disk</th><th style="padding: .1em .3em">Freshness</th><th style="padding: .1em .3em">Persist</th><th style="padding: .1em .3em">Global</th><th style="padding: .1em .3em">Entries</th><th style="padding: .1em .3em">Expired</th><th style="padding: .1em .3em">Deleted</th><th style="padding: .1em .3em">Size (KiB)</th></tr>';
		$total_entries = 0;
		$total_size = 0;
		foreach ($this->cache as $group => $cache) {
			$cache_hits_groups = isset($this->cache_hits_groups[$group]) ? $this->cache_hits_groups[$group] : 0;
			$file_cache_hits_groups = isset($this->file_cache_hits_groups[$group]) ? $this->file_cache_hits_groups[$group] : 0;
			$updated = isset($this->dirty_groups[$group]) ? 'Now' : (isset($this->mtime[$group]) ? human_time_diff( $this->mtime[$group] ) : 'Unknown');
			$persist = isset($this->non_persistent_groups[$group]) ? 'No' : 'Yes';
			$global = isset($this->global_groups[$group]) ? 'Yes' : 'No';
			$entries = count($cache);
			$total_entries += $entries;
			$expired = isset($this->expirations_groups[$group]) ? $this->expirations_groups[$group] : 0;
			$deleted = isset($this->cache_deletions_groups[$group]) ? $this->cache_deletions_groups[$group] : 0;
			$size = strlen( serialize( $cache ) ) / 1024;
			$total_size += $size;
			echo "<tr><td style='padding: .1em .3em'>$group</td><td style='padding: .1em .3em'>$cache_hits_groups</td></td><td style='padding: .1em .3em'>$file_cache_hits_groups</td><td style='padding: .1em .3em'>$updated</td><td style='padding: .1em .3em'>$persist</td><td style='padding: .1em .3em'>$global</td><td style='padding: .1em .3em'>$entries</td><td style='padding: .1em .3em'>$expired</td><td style='padding: .1em .3em'>$deleted</td><td style='padding: .1em .3em'>" . number_format( $size, 2 ) . "</td></tr>";
		}
		echo '</table>';
		echo "<p>";
		echo "<strong>Cache Entries:</strong> $total_entries ({$this->expirations} expired, {$this->cache_deletions} deleted)<br />";
		$overhead = strlen(str_repeat(CACHE_SERIAL_HEADER . CACHE_SERIAL_FOOTER, count($this->cache))) / 1024;
		echo "<strong>Cache Size:</strong> " . number_format( $total_size, 2 ) . " KiB (" . number_format( $total_size + $overhead, 2 ) . " KiB on disk)<br />";
		echo "<strong>File Cache Overall Performance:</strong> " . number_format( $this->time_total, 3) . "s<br />";
		echo "<strong>File Cache Disk Read Performance:</strong> " . number_format( $this->time_disk_read, 3) . "s<br />";
		echo "<strong>File Cache Disk Write Performance:</strong> " . number_format( $this->time_disk_write, 3) . "s";
		echo "</p>";
		echo "<p>";
		echo "<strong>Cache Misses:</strong> {$this->cache_misses}<br />";
		echo "</p>";
		echo '<table border="1" style="border-collapse: collapse"><tr><th style="padding: .1em .3em">Group</th><th style="padding: .1em .3em">Misses</th>';
		foreach ($this->cache_misses_groups as $group => $count) {
			echo "<tr><td style='padding: .1em .3em'>$group</td><td style='padding: .1em .3em'>$count</td></tr>";
		}
		echo '</table>';
		echo "<p>";
		echo "<strong>Cache Persists:</strong> {$this->actual_persists} ({$this->persists} calls)<br />";
		echo "<strong>Cache Flushes:</strong> {$this->flushes}<br />";
		echo "<strong>Cache Resets (deprecated):</strong> {$this->resets}";
		echo "</p>";
		echo "<p>";
		echo "<strong>Global Groups:</strong> " . implode(', ', array_keys($this->global_groups)) . "<br />";
		echo "<strong>Non-Persistent Groups:</strong> " . implode(', ', array_keys($this->non_persistent_groups)) . "<br />";
		if (!empty($this->file_cache_errors_groups)) echo "<strong>File Cache Read Errors:</strong> " . implode(', ', array_keys($this->file_cache_errors_groups));
		if (!empty($this->file_cache_persist_errors_groups)) echo "<strong>File Cache Write Errors:</strong> " . implode(', ', array_keys($this->file_cache_persist_errors_groups));
		echo "</p>";
		/* File-based object cache end */
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
		if ($cache === null) $cache = &$this->cache;
		return isset( $cache[ $group ] ) && ( isset( $cache[ $group ][ $key ] ) || array_key_exists( $key, $cache[ $group ] ) );
	}

	/**
	 * Sets up object properties; PHP 5 style constructor
	 *
	 * @since 2.0.8
	 *
     * @global int $blog_id
	 */
	public function __construct() {
		global $blog_id;

		$this->multisite = is_multisite();
		$this->blog_prefix =  $this->multisite ? $blog_id . ':' : '';

		/* File-based object cache start */
		$this->debug = defined('FH_OBJECT_CACHE_DEBUG') && FH_OBJECT_CACHE_DEBUG;
        if ($this->debug) $time_start = microtime(true);
		if (defined('FH_OBJECT_CACHE_PATH'))
			$this->cache_dir = FH_OBJECT_CACHE_PATH;
		else
			// Using the correct separator eliminates some cache flush errors on Windows
			$this->cache_dir = ABSPATH.'wp-content'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'fh-object-cache'.DIRECTORY_SEPARATOR;

		$this->ajax = defined('DOING_AJAX') && DOING_AJAX;
		$this->cron = defined('DOING_CRON') && DOING_CRON;
		// Skip reading from persistent cache if POST, but not if AJAX or CRON
		$this->skip = false; //$_SERVER['REQUEST_METHOD'] == 'POST' && !($this->ajax || $this->cron);
		$this->now = time();

		//$log = @file_get_contents($this->cache_dir . 'object-cache.log');
		//$log .= strftime('%Y-%m-%d %H:%M:%S') . ' ' . $_SERVER['REQUEST_URI'] . "\n";
		//if ($this->ajax) $log .= "DOING_AJAX\n";
		//if ($this->cron) $log .= "DOING_CRON\n";
		//@file_put_contents($this->cache_dir . 'object-cache.log', $log);

		if (is_file($this->cache_dir . '.expires.php')) {
			$this->expires = unserialize(substr(@ file_get_contents($this->cache_dir . '.expires.php'), strlen(CACHE_SERIAL_HEADER), -strlen(CACHE_SERIAL_FOOTER)));
			if ($this->expires === false) $this->expires = array();
		}
		if ($this->debug) $this->time_total += microtime(true) - $time_start;
		/* File-based object cache end */

		/**
		 * @todo This should be moved to the PHP4 style constructor, PHP5
		 * already calls __destruct()
		 */
		register_shutdown_function( array( $this, '__destruct' ) );
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
		/* File-based object cache start */
		$this->persist();
		/* File-based object cache end */

		return true;
	}

	/* File-based object cache start */
	public function persist($groups=null) {
        if ($this->debug) $time_start = microtime(true);
        $this->persists += 1;
		if (!empty($this->dirty_groups)) {
			$this->actual_persists += 1;
			$stat = stat(ABSPATH.'wp-content');
			$dir_perms = $stat['mode'] & 0007777; // Get the permission bits.
			$file_perms = $dir_perms & 0000666; // Remove execute bits for files.

			// Make the base cache dir.
			if (!file_exists($this->cache_dir)) {
				if (! @ mkdir($this->cache_dir)) {
					if ($this->debug) $this->time_total += microtime(true) - $time_start;
					return false;
				}
				@ chmod($this->cache_dir, $dir_perms);
			}

			if (!file_exists($this->cache_dir.".htaccess")) {
				@ touch($this->cache_dir."index.html");
				@ chmod($this->cache_dir."index.html", $file_perms);
				file_put_contents($this->cache_dir.'.htaccess', 'Deny from all');
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
					if (file_put_contents($this->cache_dir.$group.'.php',
										  CACHE_SERIAL_HEADER . serialize($this->cache[$group]) . CACHE_SERIAL_FOOTER) !== false) {
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

			if ($persisted) file_put_contents($this->cache_dir.'.expires.php',
											  CACHE_SERIAL_HEADER . serialize($this->expires) . CACHE_SERIAL_FOOTER);
			if ($this->debug) $this->time_disk_write += microtime(true) - $time_disk_write_start;

			$this->release_lock();
		}
		if ($this->debug) $this->time_total += microtime(true) - $time_start;

		if ($errors)
			return false;

		return true;
	}

	private function _expire ( $key, $group ) {
        if ($this->debug) $time_start = microtime(true);
		$expiration_time = empty( $this->expires[$group][$key] ) ? 0 : $this->expires[$group][$key];
		if ( $expiration_time && isset( $this->mtime[$group] ) && $this->mtime[$group] + $expiration_time <= $this->now ) {
			unset( $this->cache[$group][$key] );
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
