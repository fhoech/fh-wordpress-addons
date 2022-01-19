<?php

global $hyper_cache_stop;

$hyper_cache_stop = false;

if (!isset($hyper_cache_etag)) $hyper_cache_etag = false;
if (!isset($hyper_cache_browsercache_loggedin_timeout)) $hyper_cache_browsercache_loggedin_timeout = 0;

$hyper_uri = $_SERVER['REQUEST_URI'];
$hyper_wp = strpos($hyper_uri, '/wp-') !== false;

header('X-HyperCache-Version: 2.9.1.6-Mod-$Id:$');

// If no-cache header support is enabled and the browser explicitly requests a fresh page, do not cache
if ($hyper_cache_nocache &&
    ((!empty($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] == 'no-cache') ||
     (!empty($_SERVER['HTTP_PRAGMA']) && $_SERVER['HTTP_PRAGMA'] == 'no-cache'))) return hyper_cache_exit(false, 'Cache-Control=no-cache');

// Do not cache post request (comments, plugins and so on)
if ($_SERVER["REQUEST_METHOD"] == 'POST') return hyper_cache_exit(false, 'Request-Method=POST');

$hyper_qs = strpos($hyper_uri, '?');

// Do not cache WP pages, even if those calls typically don't go throught this script
if ($hyper_wp) return hyper_cache_exit(false, 'Request-URI*=/wp-');

if ($hyper_qs !== false) {
    if ($hyper_cache_strip_qs) $hyper_uri = substr($hyper_uri, 0, $hyper_qs);
    else if (!$hyper_cache_cache_qs) return hyper_cache_exit(false, 'Query-String');
}

// Try to avoid enabling the cache if sessions are managed with request parameters and a session is active
if (defined('SID') && SID != '') return hyper_cache_exit(true, 'SID');

if (strpos($hyper_uri, 'robots.txt') !== false || strpos($hyper_uri, 'sitemap.xml') !== false) return hyper_cache_exit(true, 'Request-URI*=robots.txt|sitemap.xml');

// Checks for rejected url
if ($hyper_cache_reject !== false) {
    foreach($hyper_cache_reject as $uri) {
        if (substr($uri, 0, 1) == '"') {
            if ($uri == '"' . $hyper_uri . '"') return hyper_cache_exit(true, 'Request-URI=' . $uri . '');
        }
        if (substr($hyper_uri, 0, strlen($uri)) == $uri) return hyper_cache_exit(true, 'Request-URI^="' . $uri . '"');
        if ($uri[0] == '*' && strpos($hyper_uri, substr($uri, 1)) !== false) return hyper_cache_exit(true, 'Request-URI*="' . $uri . '"');
    }
}

if ($hyper_cache_reject_agents !== false) {
    $hyper_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    foreach ($hyper_cache_reject_agents as $hyper_a) {
        if (strpos($hyper_agent, $hyper_a) !== false) return hyper_cache_exit(true, 'User-Agent*="' . $hyper_a . '"');
    }
}

// Do nested cycles in this order, usually no cookies are specified
if ($hyper_cache_reject_cookies !== false) {
    foreach ($hyper_cache_reject_cookies as $hyper_c) {
        $hyper_c = explode('=', $hyper_c);
        foreach ($_COOKIE as $n=>$v) {
            if (substr($n, 0, strlen($hyper_c[0])) == $hyper_c[0] && (isset($hyper_c[1]) ? $v == $hyper_c[1] : true)) return hyper_cache_exit(true, 'Cookie*=^"' . implode('=', $hyper_c) . '"');
        }
    }
}

// Do not use or cache pages when a wordpress user is logged on

foreach ($_COOKIE as $n=>$v) {
// If it's required to bypass the cache when the visitor is a commenter, stop.
    if ($hyper_cache_comment && substr($n, 0, 15) == 'comment_author_') return hyper_cache_exit(true, 'Cookie*=^comment_author_');

    // Skip cache if user is logged in
    // wp 2.5 and wp 2.3 have different cookie prefix, skip cache if a post password cookie is present, also
    if (substr($n, 0, 14) == 'wordpressuser_' || substr($n, 0, 20) == 'wordpress_logged_in_' || substr($n, 0, 12) == 'wp-postpass_') {
        return hyper_cache_exit(true, 'Cookie*=^"' . $n . '"');
    }
}

// Multisite
if (function_exists('is_multisite') && is_multisite() && strpos($hyper_uri, '/files/') !== false) return hyper_cache_exit(true, 'is_multisite, Request-URI*=/files/');

if ($hyper_qs !== false && !$hyper_cache_strip_qs) $hyper_uri = substr($hyper_uri, 0, $hyper_qs);
// The name of the file with html and other data
// Prefix host
$hyper_cache_name = strtolower($_SERVER['HTTP_HOST']) . hyper_cache_sanitize_uri($hyper_uri);
if (substr($hyper_cache_name, -1) == '/') $hyper_cache_name .= 'index';
if (!empty($_SERVER['QUERY_STRING']) && !$hyper_cache_strip_qs) {
    parse_str($_SERVER['QUERY_STRING'], $hyper_query);
    ksort($hyper_query);
    if (substr($hyper_cache_name, -1) != '/') $hyper_cache_name .= '/';
    $hyper_cache_name .= http_build_query($hyper_query, '', '/', PHP_QUERY_RFC3986);
}
$hyper_cache_name .= hyper_mobile_type() . '.dat';
$hc_file = $hyper_cache_path . $hyper_cache_name;

if (!file_exists($hc_file)) {
    hyper_cache_start(false);
    return;
}

$hc_file_time = @filemtime($hc_file);
$hc_file_age = time() - $hc_file_time;

if ($hc_file_age > $hyper_cache_timeout) {
    hyper_cache_start();
    return;
}

$hc_invalidation_time = @filemtime($hyper_cache_path . '_global.dat');
if ($hc_invalidation_time && $hc_file_time < $hc_invalidation_time) {
    hyper_cache_start();
    return;
}

if (array_key_exists("HTTP_IF_MODIFIED_SINCE", $_SERVER)) {
    $if_modified_since = strtotime(preg_replace('/;.*$/', '', $_SERVER["HTTP_IF_MODIFIED_SINCE"]));
    if ($if_modified_since >= $hc_file_time) {
        hyper_cache_status_header(304);
        hyper_cache_headers(0, false);
        header('X-HyperCache: 304 Not Modified');
        flush();
        die();
    }
}

// Load it and check is it's still valid
$hyper_data = @unserialize(file_get_contents($hc_file));

if (!$hyper_data) {
    hyper_cache_start();
    return;
}

if ($hyper_data['type'] == 'blog' || $hyper_data['type'] == 'home' || $hyper_data['type'] == 'archive' || $hyper_data['type'] == 'feed' || $hyper_data['type'] == 'search') {

    $hc_invalidation_archive_file =  @filemtime($hyper_cache_path . '_archives.dat');
    if ($hc_invalidation_archive_file && $hc_file_time < $hc_invalidation_archive_file) {
        hyper_cache_start();
        return;
    }
}

// Valid cache file check ends here

if (!empty($hyper_data['location'])) {
    hyper_cache_status_header(301);
    header('Location: ' . $hyper_data['location']);
    header('X-HyperCache: 301 Moved Permanently');
    flush();
    die();
}

header('X-HyperCache-File: ' . $hyper_cache_name);

// It's time to serve the cached page

hyper_cache_headers($hc_file_time, !empty($hyper_data['hash']) ? $hyper_data['hash'] : false, !empty($hyper_data['headers']) ? $hyper_data['headers'] : NULL);
header('X-HyperCache: 200 OK');

header('Content-Type: ' . $hyper_data['mime']);
if (isset($hyper_data['status'])) hyper_cache_status_header($hyper_data['status']);

// Send the cached html
if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false &&
    (($hyper_cache_gzip && !empty($hyper_data['gz'])) || ($hyper_cache_gzip_on_the_fly && function_exists('gzencode')))) {
    header('Content-Encoding: gzip');
    if (!empty($hyper_data['gz'])) {
        echo $hyper_data['gz'];
    }
    else {
        echo gzencode($hyper_data['html']);
    }
}
else {
// No compression accepted, check if we have the plain html or
// decompress the compressed one.
    if (!empty($hyper_data['html'])) {
    //header('Content-Length: ' . strlen($hyper_data['html']));
        echo $hyper_data['html'];
    }
    else if (function_exists('gzinflate')) {
        $buffer = hyper_cache_gzdecode($hyper_data['gz']);
        if ($buffer === false) echo 'Error retrieving the content';
        else echo $buffer;
    }
    else {
        // Cannot decode compressed data, serve fresh page
        header('X-HyperCache: 501 Not Implemented');
        return false;
    }
}
flush();
die();


function hyper_cache_start($delete=true) {
    global $hc_file;

    if ($delete) @unlink($hc_file);
    foreach ($_COOKIE as $n=>$v ) {
        if (substr($n, 0, 14) == 'comment_author') {
            unset($_COOKIE[$n]);
        }
    }
    ob_start('hyper_cache_callback');
}

// From here Wordpress starts to process the request

// Called whenever the page generation is ended
function hyper_cache_callback($buffer) {
    global $hyper_cache_notfound, $hyper_cache_stop, $hyper_cache_charset, $hyper_cache_feed, $hyper_cache_home, $hyper_cache_redirects, $hyper_redirect, $hc_file, $hyper_cache_name, $hyper_cache_gzip, $hyper_cache_gzip_on_the_fly;

    header('X-HyperCache: 202 Accepted');

    if (!function_exists('is_home')) {
        header('X-HyperCache-Callback-Exit-Reason: <?php !function_exists(\'is_home\'); ?>');
        return $buffer;
    }
    if (!function_exists('is_front_page')) {
        header('X-HyperCache-Callback-Exit-Reason: <?php !function_exists(\'is_front_page\'), ?>');
        return $buffer;
    }
    
    if (function_exists('apply_filters')) $buffer = apply_filters('hyper_cache_buffer', $buffer);

    if ($hyper_cache_stop) {
        header('X-HyperCache-Callback-Exit-Reason: Stop=' . $hyper_cache_stop);
        return $buffer;
    }

    if (!$hyper_cache_notfound && is_404()) {
        header('X-HyperCache-Callback-Exit-Reason: 404 Not Found');
        return $buffer;
    }

    // WP is sending a redirect
    if ($hyper_redirect) {
        if ($hyper_cache_redirects) {
            $data['location'] = $hyper_redirect;
            hyper_cache_write($data);
        }
        header('X-HyperCache-Callback-Exit-Reason: Redirect');
        return $buffer;
    }

    $buffer = trim($buffer);

    // Can be a trackback or other things without a body. We do not cache them, WP needs to get those calls.
    if (strlen($buffer) == 0) {
        header('X-HyperCache-Callback-Exit-Reason: No content');
        return '';
    }

    if (strpos($buffer, '</body>') === false && !is_feed()) {
        header('X-HyperCache-Callback-Exit-Reason: <?php strpos($buffer, \'</body>\') === false && !is_feed(); ?>');
        return $buffer;
    }

    if ((is_home() || is_front_page()) && $hyper_cache_home) {
        header('X-HyperCache-Callback-Exit-Reason: <?php is_home() || is_front_page(); ?>');
        return $buffer;
    }

    if (is_feed() && !$hyper_cache_feed) {
        header('X-HyperCache-Callback-Exit-Reason: <?php is_feed(); ?>');
        return $buffer;
    }

    if (is_feed()) $data['type'] = 'feed';
    else if (!is_front_page() && is_home()) $data['type'] = 'blog';
    else if (is_front_page()) $data['type'] = 'home';
        else if (is_archive()) $data['type'] = 'archive';
            else if (is_single()) $data['type'] = 'single';
                else if (is_attachment()) $data['type'] = 'attachment';
                    else if (is_page()) $data['type'] = 'page';
                        else if (is_404()) $data['type'] = '404';
                            else if (is_search()) $data['type'] = 'search';
                                else $data['type'] = is_singular() ? get_post_type() : 'archive';

    if (!$hyper_cache_charset) $hyper_cache_charset = 'UTF-8';

    if (is_feed()) {
        $data['mime'] = 'text/xml;charset=' . $hyper_cache_charset;
    }
    else {
        $data['mime'] = 'text/html;charset=' . $hyper_cache_charset;
    }

    $buffer .= '<!-- hyper cache: ' . $hyper_cache_name . ' ' . date('y-m-d h:i:s') .' -->';

    $data['html'] = $buffer;

    $response_code = http_response_code();
    if ($response_code >= 400) $data['status'] = $response_code;

    hyper_cache_write($data);

    hyper_cache_headers(@filemtime($hc_file), !empty($data['hash']) ? $data['hash'] : false);
    
    if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false &&
        (($hyper_cache_gzip && !empty($data['gz'])) || ($hyper_cache_gzip_on_the_fly && !empty($data['html']) && function_exists('gzencode')))) {
        header('Content-Encoding: gzip');
        if (empty($data['gz'])) {
            $data['gz'] = gzencode($data['html']);
        }
        return $data['gz'];
    }

    return $buffer;
}

function hyper_cache_write(&$data) {
    global $hc_file, $hyper_cache_store_compressed, $hyper_cache_store_uncompressed, $hyper_cache_name, $hyper_cache_lastmodified, $hyper_cache_etag;

    $data['host'] = $_SERVER['HTTP_HOST'];
    $data['uri'] = $_SERVER['REQUEST_URI'];
    $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

    // Support HTTP2 server push
    $headers = headers_list();
    $data['headers'] = array();
    foreach ($headers as $header) {
        if (strpos($header, 'Link:') === 0) $data['headers'][] = $header;
    }

    // Look if we need the compressed version
    if ($hyper_cache_store_compressed && !empty($data['html']) && function_exists('gzencode')) {
        $data['gz'] = gzencode($data['html']);
        if ($data['gz'] && !$hyper_cache_store_uncompressed) unset($data['html']);
        if ($hyper_cache_lastmodified && $hyper_cache_etag) $data['hash'] = crc32($data['gz']);
    }
    else if ($hyper_cache_lastmodified && $hyper_cache_etag && !empty($data['html'])) $data['hash'] = crc32($data['html']);
    $hc_dir = dirname($hc_file);
    if (!is_dir($hc_dir)) wp_mkdir_p($hc_dir);
    $file = fopen($hc_file, 'w');
    if ($file !== FALSE) {
        fwrite($file, serialize($data));
        fclose($file);

        header('X-HyperCache: 201 Created');
        header('X-HyperCache-File: ' . $hyper_cache_name);
    }
    else {
        header('X-HyperCache: 500 Could not create cache file');
        header('X-HyperCache-File: ' . $hyper_cache_name);
    }
}

function hyper_mobile_type() {
    global $hyper_cache_mobile, $hyper_cache_mobile_agents, $hyper_cache_plugin_mobile_pack;

    if ($hyper_cache_plugin_mobile_pack) {
        @include_once ABSPATH . 'wp-content/plugins/wordpress-mobile-pack/plugins/wpmp_switcher/lite_detection.php';
        if (function_exists('lite_detection')) {
            $is_mobile = lite_detection();
            if (!$is_mobile) return '';
            include_once ABSPATH . 'wp-content/plugins/wordpress-mobile-pack/themes/mobile_pack_base/group_detection.php';
            if (function_exists('group_detection')) {
                return '-mobile-' . group_detection();
            }
            else return '-mobile';
        }
    }

    if (!isset($hyper_cache_mobile) || $hyper_cache_mobile_agents === false || !isset($_SERVER['HTTP_USER_AGENT'])) return '';

    $hyper_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if (!empty($hyper_cache_mobile_agents)) {
        foreach ($hyper_cache_mobile_agents as $hyper_a) {
            if (strpos($hyper_agent, $hyper_a) !== false) {
                if (strpos($hyper_agent, 'iphone') || strpos($hyper_agent, 'ipod')) {
                    return '-iphone';
                }
                else {
                    return '-pda';
                }
            }
        }
    }
    return '';
}

function hyper_cache_sanitize_uri($uri) {
    $uri = preg_replace('/[^a-zA-Z0-9\/\-_!$%&()=+~\';,.]+/', '_', $uri);
    $uri = preg_replace('/\/\/+/', '/', $uri);
    $uri = preg_replace('/\.\.+/', '.', $uri);
    if (empty($uri) || $uri[0] != '/') {
        $uri = '/' . $uri;
    }
    return $uri;
}

function hyper_cache_headers($hc_file_time, $hash=false, $headers=NULL) {
    global $hyper_cache_lastmodified;
    $browsercache_timeout = hyper_cache_browsercache_timeout();
    // Always send Vary
    header('Vary: Accept-Encoding, Cookie');

    // Send custom headers
    if (!empty($headers)) {
        foreach ($headers as $header) header($header, false);
    }

    if (!$browsercache_timeout) {
        // Browser caching NOT enabled (default) or timeout = 0
        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
        header('X-HyperCache-Cache-Control: no-cache, must-revalidate, max-age=0');
    }
    else {
        $loggedin = hyper_cache_loggedin();
        $cache_control = 'Cache-Control: ' . ($loggedin ? 'private, ' : '') . 'max-age=' . $browsercache_timeout;
        header($cache_control);
        if ($loggedin) header('Pragma: private');
        header('Expires: ' . gmdate("D, d M Y H:i:s", time() + $browsercache_timeout) . " GMT");
        header('X-HyperCache-' . $cache_control);
        if ($hash && hyper_cache_etag($hash) == 304) {
            flush();
            die();
        }
        // True if user ask to NOT send Last-Modified
        if (!$hash && $hc_file_time && !$hyper_cache_lastmodified) {
            header('Last-Modified: ' . gmdate("D, d M Y H:i:s", $hc_file_time). " GMT");
        }
    }
}

function hyper_cache_browsercache_timeout() {
    global $hyper_cache_browsercache, $hyper_cache_browsercache_timeout, $hyper_cache_browsercache_loggedin_timeout;
    if ($hyper_cache_browsercache)
        return hyper_cache_loggedin() ? $hyper_cache_browsercache_loggedin_timeout : $hyper_cache_browsercache_timeout;
    else return false;
}

function hyper_cache_loggedin() {
    return function_exists('is_user_logged_in') && is_user_logged_in();
}

function hyper_cache_get_status_header_desc( $code ) {  // ALMOST verbatim copy of get_status_header_desc from wp-includes/functions.php
    global $wp_header_to_desc;
 
    $code = abs( intval( $code ) );  // Original function uses WP's functionally equivalent absint()
 
    if ( ! isset( $wp_header_to_desc ) ) {
        $wp_header_to_desc = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            103 => 'Early Hints',
 
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            226 => 'IM Used',
 
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => 'Reserved',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',
 
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            421 => 'Misdirected Request',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            426 => 'Upgrade Required',
            428 => 'Precondition Required',
            429 => 'Too Many Requests',
            431 => 'Request Header Fields Too Large',
            451 => 'Unavailable For Legal Reasons',
 
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            510 => 'Not Extended',
            511 => 'Network Authentication Required',
        );
    }
 
    if ( isset( $wp_header_to_desc[ $code ] ) ) {
        return $wp_header_to_desc[ $code ];
    } else {
        return '';
    }
}

function hyper_cache_status_header( $code, $description = '' ) {  // ALMOST verbatim copy of status_header from wp-includes/functions.php
    if ( ! $description ) {
        $description = hyper_cache_get_status_header_desc( $code );
    }
 
    if ( empty( $description ) ) {
        return;
    }
 
    $protocol      = $_SERVER['SERVER_PROTOCOL'];  // Original status_header uses wp_get_server_protocol
    $status_header = "$protocol $code $description";
    if ( function_exists( 'apply_filters' ) ) {
 
        /**
         * Filters an HTTP status header.
         *
         * @since 2.2.0
         *
         * @param string $status_header HTTP status header.
         * @param int    $code          HTTP status code.
         * @param string $description   Description for the status code.
         * @param string $protocol      Server protocol.
         */
        $status_header = apply_filters( 'status_header', $status_header, $code, $description, $protocol );
    }
 
    if ( ! headers_sent() ) {
        header( $status_header, true, $code );
    }
}

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

function hyper_cache_etag($hash) {
    $etag = '"hypercache-' . sprintf('%x', $hash) . '"';
    header('ETag: ' . $etag);
    if (isset($_SERVER['HTTP_IF_NONE_MATCH']) &&
        ($_SERVER['HTTP_IF_NONE_MATCH'] == "*" ||
         strpos(stripslashes($_SERVER['HTTP_IF_NONE_MATCH']), $etag) !== false)) {
      hyper_cache_status_header(304);
      header('X-HyperCache: 304 Not Modified');
      return 304;
    }
    return 200;
}

function hyper_cache_output($buffer) {
    global $hyper_cache_gzip_on_the_fly, $hyper_cache_etag;
    hyper_cache_headers(0, false);
    if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false &&
        $hyper_cache_gzip_on_the_fly && !empty($buffer) && function_exists('gzencode')) {
        $buffer = gzencode($buffer);
        if ($hyper_cache_etag && hyper_cache_etag(crc32($buffer)) == 304) return '';
        header('Content-Encoding: gzip');
    }
    else if ($hyper_cache_etag && hyper_cache_etag(crc32($buffer)) == 304) return '';
    return $buffer;
}

function hyper_cache_exit($allow_browsercache=true, $reason='Unspecified') {
    global $hyper_cache_gzip_on_the_fly, $hyper_wp;
    header('X-HyperCache-Bypass-Reason: ' . $reason);

    if ($allow_browsercache && hyper_cache_browsercache_timeout()) {
        header('X-HyperCache-OB: hyper_cache_output');
        ob_start('hyper_cache_output');
    }
    else if ($hyper_cache_gzip_on_the_fly &&
             (!$hyper_wp || (strpos($_SERVER['REQUEST_URI'], '/wp-login.php') === false &&
                             strpos($_SERVER['REQUEST_URI'], '/wp-admin/up') === false &&
                             strpos($_SERVER['REQUEST_URI'], '/wp-admin/admin.php?import=') === false &&
                             $_SERVER["REQUEST_METHOD"] != 'POST')) &&
             (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], '/wp-admin/customize.php?') === false)) {
        header('X-HyperCache-OB: ob_gzhandler');
        ob_start('ob_gzhandler');
    }
    return false;
}
