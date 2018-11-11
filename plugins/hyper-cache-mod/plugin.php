<?php
/*
Plugin Name: Hyper Cache 2.9.1.6 Mod
Plugin URI: https://github.com/fhoech/fh-wordpress-addons/blob/master/plugins/hyper-cache-mod
Description: Hyper Cache is a cache system for WordPress to improve it's perfomances and save resources. <a href="http://www.satollo.net/plugins/hyper-cache" target="_blank">Hyper Cache official page</a>. To manually upgrade remember the sequence: deactivate, update, reactivate.
Version: 2.9.1.6-Mod-$Id$
Text Domain: hyper-cache-mod
Author: Stefano Lissa, Florian Höch (Mod)
Author URI: http://www.satollo.net
Disclaimer: Use at your own risk. No warranty expressed or implied is provided.

Copyright 2011  Satollo  (email : info@satollo.net)
 
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

$hyper_invalidated = false;
$hyper_invalidated_post_id = null;


// On activation, we try to create files and directories. If something goes wrong
// (eg. for wrong permission on file system) the options page will give a
// warning.
register_activation_hook(__FILE__, 'hyper_activate');
function hyper_activate()
{
    $options = get_option('hyper');

    wp_clear_scheduled_hook('hyper_clean');

    if (!is_array($options)) {
        $options = array();
        $options['comment'] = 1;
        $options['archive'] = 1;
        $options['timeout'] = 1440;
        $options['browsercache_timeout'] = 720;
        $options['browsercache_loggedin_timeout'] = 0;
        $options['redirects'] = 1;
        $options['notfound'] = 1;
        $options['clean_interval'] = 60;
        $options['gzip'] = 1;
        $options['store_compressed'] = 1;
        $options['expire_type'] = 'post';
        update_option('hyper', $options);
    }

    $buffer = hyper_generate_config($options);
    $file = @fopen(WP_CONTENT_DIR . '/advanced-cache.php', 'wb');
    @fwrite($file, $buffer);
    @fclose($file);

    wp_mkdir_p(WP_CONTENT_DIR . '/cache/hyper-cache-mod');

    wp_schedule_event(time()+300, 'hourly', 'hyper_clean');
}

add_action('hyper_clean', 'hyper_clean');
function hyper_clean($path=null, &$invalidation_time=null, &$time=null, &$timeout=null)
{
    if ($path == null) {
        // Latest global invalidation (may be false)
        $invalidation_time = @filemtime(WP_CONTENT_DIR . '/cache/hyper-cache-mod/_global.dat');

        hyper_log('start cleaning');

        $options = get_option('hyper');

        $timeout = $options['timeout']*60;
        if ($timeout == 0) return;

        $path = WP_CONTENT_DIR . '/cache/hyper-cache-mod/';
        $time = time();

        $stack = 0;
    }

    $files = glob($path . '*', GLOB_MARK);
    foreach ($files as &$file) {
        if (substr($file, -1) == '/')
            hyper_clean($file, $invalidation_time, $time, $timeout);
        else {
            hyper_log('checking ' . $file . ' for cleaning');
            $t = @filemtime($file);
            hyper_log('file time ' . $t);
            if ($time - $t > $timeout || ($invalidation_time && $t < $invalidation_time)) {
                @unlink($file);
                hyper_log('cleaned ' . $file);
            }
        }
    }
    @rmdir($path);

    if (isset($stack)) hyper_log('end cleaning');
}

register_deactivation_hook(__FILE__, 'hyper_deactivate');
function hyper_deactivate()
{
    wp_clear_scheduled_hook('hyper_clean');

    // burn the file without delete it so one can rewrite it
    $file = @fopen(WP_CONTENT_DIR . '/advanced-cache.php', 'wb');
    if ($file)
    {
        @fwrite($file, '');
        @fclose($file);
    }
}

add_action('admin_menu', 'hyper_admin_menu');
function hyper_admin_menu()
{
    add_options_page('Hyper Cache 2.9.1.6 Mod', 'Hyper Cache 2.9.1.6 Mod', 'manage_options', 'hyper-cache-mod/options.php');
}

// Completely invalidate the cache. The hyper-cache-mod directory is renamed
// with a random name and re-created to be immediately available to the cache
// system. Then the renamed directory is removed.
// If the cache has been already invalidated, the function doesn't anything.
function hyper_cache_invalidate()
{
    global $hyper_invalidated;

    hyper_log("hyper_cache_invalidate> Called");

    if ($hyper_invalidated)
    {
        hyper_log("hyper_cache_invalidate> Cache already invalidated");
        return;
    }

    if (!@touch(WP_CONTENT_DIR . '/cache/hyper-cache-mod/_global.dat'))
    {
        hyper_log("hyper_cache_invalidate> Unable to touch cache/_global.dat");
    }
    else
    {
        hyper_log("hyper_cache_invalidate> Touched cache/_global.dat");
    }
    @unlink(WP_CONTENT_DIR . '/cache/hyper-cache-mod/_archives.dat');
    $hyper_invalidated = true;

}

/**
 * Invalidates a single post and eventually the home and archives if
 * required.
 */
function hyper_cache_invalidate_post($post_id)
{
    global $hyper_invalidated_post_id;

    hyper_log("hyper_cache_invalidate_post(" . $post_id . ")> Called");

    if ($hyper_invalidated_post_id == $post_id)
    {
        hyper_log("hyper_cache_invalidate_post(" . $post_id . ")> Post was already invalidated");
        return;
    }

    $options = get_option('hyper');

    if ($options['expire_type'] == 'none')
    {
        hyper_log("hyper_cache_invalidate_post(" . $post_id . ")> Invalidation disabled");
        return;
    }

    if ($options['expire_type'] == 'post')
    {
        $post = get_post($post_id);

        $link = get_permalink($post_id);
        hyper_log('Permalink to invalidate ' . $link);

        $parts = parse_url($link);
        $hyper_cache_name = $parts['host'] . hyper_cache_sanitize_uri($parts['path']);
        if (substr($hyper_cache_name, -1) == '/') $hyper_cache_name .= 'index';
        if (!empty($parts['query']) && !isset($options['strip_qs']) && isset($options['cache_qs'])) {
            parse_str($parts['query'], $hyper_query);
            ksort($hyper_query);
            if (substr($hyper_cache_name, -1) != '/') $hyper_cache_name .= '/';
            $hyper_cache_name .= http_build_query($hyper_query, '', '/', PHP_QUERY_RFC3986);
        }
        hyper_log('File basename to invalidate ' . $hyper_cache_name);

        $path = WP_CONTENT_DIR . '/cache/hyper-cache-mod/' . dirname($hyper_cache_name);
        if (is_dir($path)) $handle = @opendir($path);
        if (!empty($handle))
        {
            while ($f = readdir($handle))
            {
                if (substr($f, -4) == '.dat')
                {
                    if (unlink($path . '/' . $f)) {
                        hyper_log('Deleted ' . $path . '/' . $f);
                    }
                    else {
                        hyper_log('Unable to delete ' . $path . '/' . $f);
                    }
                }
            }
            closedir($handle);
        }

        $hyper_invalidated_post_id = $post_id;

        hyper_log("hyper_cache_invalidate_post(" . $post_id . ")> Post invalidated");

        if ($options['archive'])
        {

            hyper_log("hyper_cache_invalidate_post(" . $post_id . ")> Archive invalidation required");

            if (!@touch(WP_CONTENT_DIR . '/cache/hyper-cache-mod/_archives.dat'))
            {
                hyper_log("hyper_cache_invalidate_post(" . $post_id . ")> Unable to touch cache/_archives.dat");
            }
            else
            {
                hyper_log("hyper_cache_invalidate_post(" . $post_id . ")> Touched cache/_archives.dat");
            }
        }
        return;
    }

    if ($options['expire_type'] == 'all')
    {
        hyper_log("hyper_cache_invalidate_post(" . $post_id . ")> Full invalidation");
        hyper_cache_invalidate();
        return;
    }
}


// Completely remove a directory and it's content.
function hyper_delete_path($path=null)
{
    if ($path == null) return;
    $files = glob($path . '*', GLOB_MARK);
    foreach ($files as &$file) {
        if (substr($file, -1) == '/')
            hyper_delete_path($file);
        else {
            @unlink($file);
        }
    }
}

// Counts the number of file in to the hyper cache directory to give an idea of
// the number of pages cached.
function hyper_count($path=null)
{
    $count = 0;
    //if (!is_dir(ABSPATH . 'wp-content/hyper-cache-mod')) return 0;
    if ($path == null) $path = WP_CONTENT_DIR . '/cache/hyper-cache-mod/';
    $files = glob($path . '*', GLOB_MARK);
    foreach ($files as &$file) {
        if (substr($file, -1) == '/')
            $count += hyper_count($file);
        else {
            $count++;
        }
    }
    return $count;
}

add_action('switch_theme', 'hyper_cache_invalidate', 0);

add_action('edit_post', 'hyper_cache_invalidate_post', 0);
add_action('publish_post', 'hyper_cache_invalidate_post', 0);
add_action('delete_post', 'hyper_cache_invalidate_post', 0);

// bbPress
add_action('bbp_new_reply', 'hyper_cache_invalidate_bbp_reply', 0);
add_action('bbp_new_topic', 'hyper_cache_invalidate_bbp_topic', 0);

function hyper_cache_invalidate_bbp_reply($reply_id) {
    global $hyper_invalidated_post_id;

    hyper_log("hyper_cache_invalidate_bbp_reply(" . $post_id . ")> Called");

    if ($hyper_invalidated_post_id == $post_id)
    {
        hyper_log("hyper_cache_invalidate_bbp_reply(" . $post_id . ")> Post was already invalidated");
        return;
    }

    $options = get_option('hyper');

    if ($options['expire_type'] == 'none')
    {
        hyper_log("hyper_cache_invalidate_bbp_reply(" . $post_id . ")> Invalidation disabled");
        return;
    }

    //$topic_id = bbp_get_reply_topic_id($reply_id);
    //$topic_url = bbp_get_topic_permalink($topic_id);
    //$dir = $this->get_folder() . '/' . substr($topic_url, strpos($topic_url, '://') + 3) . '/';
    //$this->remove_dir($dir);

    //$forum_id = bbp_get_reply_forum_id($reply_id);
    //$forum_url = bbp_get_forum_permalink($forum_id);
    //$dir = $this->get_folder() . '/' . substr($topic_url, strpos($forum_url, '://') + 3) . '/';
    //$this->remove_dir($dir);
}

function hyper_cache_invalidate_bbp_topic($topic_id) {
    global $hyper_invalidated_post_id;

    hyper_log("hyper_cache_invalidate_bbp_topic(" . $post_id . ")> Called");

    if ($hyper_invalidated_post_id == $post_id)
    {
        hyper_log("hyper_cache_invalidate_bbp_topic(" . $post_id . ")> Post was already invalidated");
        return;
    }

    $options = get_option('hyper');

    if ($options['expire_type'] == 'none')
    {
        hyper_log("hyper_cache_invalidate_bbp_topic(" . $post_id . ")> Invalidation disabled");
        return;
    }

    //$topic_url = bbp_get_topic_permalink($topic_id);
    //$dir = $this->get_folder() . '/' . substr($topic_url, strpos($topic_url, '://') + 3) . '/';
    //$this->remove_dir($dir);

    //$forum_id = bbp_get_topic_forum_id($topic_id);
    //$forum_url = bbp_get_forum_permalink($forum_id);
    //$dir = $this->get_folder() . '/' . substr($topic_url, strpos($forum_url, '://') + 3) . '/';
    //$this->remove_dir($dir);
}


// Capture and register if a redirect is sent back from WP, so the cache
// can cache (or ignore) it. Redirects were source of problems for blogs
// with more than one host name (eg. domain.com and www.domain.com) comined
// with the use of Hyper Cache.
add_filter('redirect_canonical', 'hyper_redirect_canonical', 10, 2);
$hyper_redirect = null;
function hyper_redirect_canonical($redirect_url, $requested_url)
{
    global $hyper_redirect;

    $hyper_redirect = $redirect_url;

    return $redirect_url;
}

function hyper_log($text)
{
//    $file = fopen(ABSPATH . '/log.txt', 'a');
//    fwrite($file, $text . "\n");
//    fclose($file);
}

function hyper_generate_config(&$options)
{
    $buffer = '';

    $timeout = $options['timeout']*60;
    if ($timeout == 0) $timeout = 2000000000;

    $browsercache_timeout = $options['browsercache_timeout']*60;
    $browsercache_loggedin_timeout = $options['browsercache_loggedin_timeout']*60;

    $buffer = "<?php\n";
    $buffer .= '$hyper_cache_path = \'' . WP_CONTENT_DIR . '/cache/hyper-cache-mod/\'' . ";\n";
    $buffer .= '$hyper_cache_charset = "' . get_option('blog_charset') . '"' . ";\n";
    // Collect statistics
    //$buffer .= '$hyper_cache_stats = ' . (isset($options['stats'])?'true':'false') . ";\n";
    // Do not cache for commenters
    $buffer .= '$hyper_cache_comment = ' . (isset($options['comment'])?'true':'false') . ";\n";
    // Ivalidate archives on post invalidation
    $buffer .= '$hyper_cache_archive = ' . ($options['archive']?'true':'false') . ";\n";
    // Single page timeout
    $buffer .= '$hyper_cache_timeout = ' . ($timeout) . ";\n";
    // Cache redirects?
    $buffer .= '$hyper_cache_redirects = ' . (isset($options['redirects'])?'true':'false') . ";\n";
    // Cache page not found?
    $buffer .= '$hyper_cache_notfound = ' . (isset($options['notfound'])?'true':'false') . ";\n";
    // Separate caching for mobile agents?
    $buffer .= '$hyper_cache_mobile = ' . (isset($options['mobile'])?'true':'false') . ";\n";
    // WordPress mobile pack integration?
    $buffer .= '$hyper_cache_plugin_mobile_pack = ' . (isset($options['plugin_mobile_pack'])?'true':'false') . ";\n";
    // Cache the feeds?
    $buffer .= '$hyper_cache_feed = ' . (isset($options['feed'])?'true':'false') . ";\n";
    // Cache GET request with parameters?
    $buffer .= '$hyper_cache_cache_qs = ' . (isset($options['cache_qs'])?'true':'false') . ";\n";
    // Strip query string?
    $buffer .= '$hyper_cache_strip_qs = ' . (isset($options['strip_qs'])?'true':'false') . ";\n";
    // DO NOT cache the home?
    $buffer .= '$hyper_cache_home = ' . (isset($options['home'])?'true':'false') . ";\n";
    // Disable last modified header
    $buffer .= '$hyper_cache_lastmodified = ' . (isset($options['lastmodified'])?'true':'false') . ";\n";
    // Allow browser caching?
    $buffer .= '$hyper_cache_browsercache = ' . (isset($options['browsercache'])?'true':'false') . ";\n";
    $buffer .= '$hyper_cache_etag = ' . (isset($options['etag'])?'true':'false') . ";\n";
    $buffer .= '$hyper_cache_browsercache_timeout = ' . ($browsercache_timeout) . ";\n";
    $buffer .= '$hyper_cache_browsercache_loggedin_timeout = ' . ($browsercache_loggedin_timeout) . ";\n";
    // Do not use cache if browser sends no-cache header?
    $buffer .= '$hyper_cache_nocache = ' . (isset($options['nocache'])?'true':'false') . ";\n";

    if ($options['gzip']) $options['store_compressed'] = 1;

    $buffer .= '$hyper_cache_gzip = ' . (isset($options['gzip'])?'true':'false') . ";\n";
    $buffer .= '$hyper_cache_gzip_on_the_fly = ' . (isset($options['gzip_on_the_fly'])?'true':'false') . ";\n";
    $buffer .= '$hyper_cache_store_compressed = ' . (isset($options['store_compressed'])?'true':'false') . ";\n";
    $buffer .= '$hyper_cache_store_uncompressed = ' . (isset($options['store_uncompressed'])?'true':'false') . ";\n";

    //$buffer .= '$hyper_cache_clean_interval = ' . ($options['clean_interval']*60) . ";\n";

    if (isset($options['reject']) && trim($options['reject']) != '')
    {
        $options['reject'] = str_replace(' ', "\n", $options['reject']);
        $options['reject'] = str_replace("\r", "\n", $options['reject']);
        $buffer .= '$hyper_cache_reject = array(';
        $reject = explode("\n", $options['reject']);
        $options['reject'] = '';
        foreach ($reject as $uri)
        {
            $uri = trim($uri);
            if ($uri == '') continue;
            $buffer .= "\"" . addslashes(trim($uri)) . "\",";
            $options['reject'] .= $uri . "\n";
        }
        $buffer = rtrim($buffer, ',');
        $buffer .= ");\n";
    }
    else {
        $buffer .= '$hyper_cache_reject = false;' . "\n";
    }

    if (isset($options['reject_agents']) && trim($options['reject_agents']) != '')
    {
        $options['reject_agents'] = str_replace(' ', "\n", $options['reject_agents']);
        $options['reject_agents'] = str_replace("\r", "\n", $options['reject_agents']);
        $buffer .= '$hyper_cache_reject_agents = array(';
        $reject_agents = explode("\n", $options['reject_agents']);
        $options['reject_agents'] = '';
        foreach ($reject_agents as $uri)
        {
            $uri = trim($uri);
            if ($uri == '') continue;
            $buffer .= "\"" . addslashes(strtolower(trim($uri))) . "\",";
            $options['reject_agents'] .= $uri . "\n";
        }
        $buffer = rtrim($buffer, ',');
        $buffer .= ");\n";
    }
    else {
        $buffer .= '$hyper_cache_reject_agents = false;' . "\n";
    }

    if (isset($options['reject_cookies']) && trim($options['reject_cookies']) != '')
    {
        $options['reject_cookies'] = str_replace(' ', "\n", $options['reject_cookies']);
        $options['reject_cookies'] = str_replace("\r", "\n", $options['reject_cookies']);
        $buffer .= '$hyper_cache_reject_cookies = array(';
        $reject_cookies = explode("\n", $options['reject_cookies']);
        $options['reject_cookies'] = '';
        foreach ($reject_cookies as $c)
        {
            $c = trim($c);
            if ($c == '') continue;
            $buffer .= "\"" . addslashes(strtolower(trim($c))) . "\",";
            $options['reject_cookies'] .= $c . "\n";
        }
        $buffer = rtrim($buffer, ',');
        $buffer .= ");\n";
    }
    else {
        $buffer .= '$hyper_cache_reject_cookies = false;' . "\n";
    }

    if (isset($options['mobile']))
    {
        if (!isset($options['mobile_agents']) || trim($options['mobile_agents']) == '')
        {
            $options['mobile_agents'] = "elaine/3.0\niphone\nipod\npalm\neudoraweb\nblazer\navantgo\nwindows ce\ncellphone\nsmall\nmmef20\ndanger\nhiptop\nproxinet\nnewt\npalmos\nnetfront\nsharp-tq-gx10\nsonyericsson\nsymbianos\nup.browser\nup.link\nts21i-10\nmot-v\nportalmmm\ndocomo\nopera mini\npalm\nhandspring\nnokia\nkyocera\nsamsung\nmotorola\nmot\nsmartphone\nblackberry\nwap\nplaystation portable\nlg\nmmp\nopwv\nsymbian\nepoc";
        }

        if (trim($options['mobile_agents']) != '')
        {
            $options['mobile_agents'] = str_replace(',', "\n", $options['mobile_agents']);
            $options['mobile_agents'] = str_replace("\r", "\n", $options['mobile_agents']);
            $buffer .= '$hyper_cache_mobile_agents = array(';
            $mobile_agents = explode("\n", $options['mobile_agents']);
            $options['mobile_agents'] = '';
            foreach ($mobile_agents as $uri)
            {
                $uri = trim($uri);
                if ($uri == '') continue;
                $buffer .= "\"" . addslashes(strtolower(trim($uri))) . "\",";
                $options['mobile_agents'] .= $uri . "\n";
            }
            $buffer = rtrim($buffer, ',');
            $buffer .= ");\n";
        }
        else
        {
            $buffer .= '$hyper_cache_mobile_agents = false;' . "\n";
        }
    }
    
    $buffer .= "include(WP_CONTENT_DIR . '/plugins/hyper-cache-mod/cache.php');\n";
    $buffer .= '?>';

    return $buffer;
}

if (!function_exists('hyper_cache_sanitize_uri')) {
    function hyper_cache_sanitize_uri($uri) {
        $uri = preg_replace('/[^a-zA-Z0-9\/\-_!$%&()=+~\';,.]+/', '_', $uri);
        $uri = preg_replace('/\/\/+/', '/', $uri);
        $uri = preg_replace('/\.\.+/', '.', $uri);
        if (empty($uri) || $uri[0] != '/') {
            $uri = '/' . $uri;
        }
        return $uri;
    }
}

?>
