<?php
/*
Plugin name: FH Protect E-Mail
Plugin URI: https://github.com/fhoech/fh-wordpress-addons/blob/master/plugins/fhpe/
Description: Protect email addresses from spambots.
Version: $Id$
Author: Florian Höch
Author URI: http://hoech.net
License: GPL3
*/

if (!function_exists('__')) {
	function __( $text, $domain='default' ) {
		return _( $text );
	}
}

if (!class_exists('FH_virtual_post')) {

	class FH_virtual_post {

		private $slug = NULL;
		private $title = NULL;
		private $content = NULL;
		private $author = NULL;
		private $date = NULL;
		private $type = NULL;

		public function __construct($args) {
			if (!isset($args['slug']))
				throw new Exception('No slug given for virtual post');

			$this -> slug = $args['slug'];
			$this -> title = isset($args['title']) ? $args['title'] : '';
			$this -> content = isset($args['content']) ? $args['content'] : '';
			$this -> author = isset($args['author']) ? $args['author'] : 1;
			$this -> date = isset($args['date']) ? $args['date'] : current_time('mysql');
			$this -> dategmt = isset($args['date']) ? $args['date'] : current_time('mysql', 1);
			$this -> type = isset($args['type']) ? $args['type'] : 'post';

			add_filter( 'the_posts', array(&$this, 'virtual_post') );
			add_action( 'send_headers', 'nocache_headers' );
		}

		public function virtual_post($posts) {
			global $wp, $wp_query;

			if (strcasecmp($wp -> request, $this -> slug) == 0 || $wp -> query_vars['page_id'] == $this -> slug) {
				// create a fake post intance
				$post = new stdClass;
				// fill properties of $post with everything a post in the database would have
				$post -> ID = PHP_INT_MAX;                          // use an illegal value for post ID
				$post -> post_author = $this -> author;       // post author id
				$post -> post_date = $this -> date;           // date of post
				$post -> post_date_gmt = $this -> dategmt;
				$post -> post_content = $this -> content;
				$post -> post_title = $this -> title;
				$post -> post_excerpt = '';
				$post -> post_status = 'publish';
				$post -> comment_status = 'closed';        // mark as closed for comments, since post doesn't exist
				$post -> ping_status = 'closed';           // mark as closed for pings, since post doesn't exist
				$post -> post_password = '';               // no password
				$post -> post_name = $this -> slug;
				$post -> to_ping = '';
				$post -> pinged = '';
				$post -> modified = $post -> post_date;
				$post -> modified_gmt = $post -> post_date_gmt;
				$post -> post_content_filtered = '';
				$post -> post_parent = 0;
				$post -> guid = get_home_url('/' . $this -> slug);
				$post -> menu_order = 0;
				$post -> post_type = $this -> type;
				$post -> post_mime_type = '';
				$post -> comment_count = 0;

				$posts = array($post);

				// reset wp_query properties to simulate a found post
				$wp_query -> is_page = $this -> type == 'page';
				$wp_query -> is_singular = true;
				$wp_query -> is_home = false;
				$wp_query -> is_archive = false;
				$wp_query -> is_category = false;
				unset($wp_query -> query['error']);
				$wp_query -> query_vars['error'] = '';
				$wp_query -> is_404 = false;
			}
			return $posts;
		}
	}

}

class FH_protect_email {

	private $uri;
	private $key;
	private $charset;
	private $ip;
	private $ua;
	private $ts;

	public function __construct() {
		$uri = $_SERVER['REQUEST_URI'];
		$query_string_pos = strpos($uri, '?');
		if ($query_string_pos !== false) $uri = substr($uri, 0, $query_string_pos);
		$this -> uri = $_SERVER['HTTP_HOST'] . $uri;
		$this -> key = sprintf('%x', crc32($this -> uri));
		$this -> charset = get_bloginfo('charset');
		if (!empty($_GET['fhpe'])) {
			$this -> ip = $this :: get_ip();
			$this -> ua = $_SERVER['HTTP_USER_AGENT'];
			$this -> ts = date('Y-m-d H:') . strval(floor(intval(date('i')) / 5) * 5);
			// Prepare a math question
			$this -> check_x = $this -> reduce($this -> key . $this -> ip . $this -> ts);
			$this -> check_y = $this -> reduce($this -> key . $this -> ua . $this -> ts);
			add_action( 'init', array( &$this, 'wp_init' ), 1 );
			add_action( 'pre_get_posts', array( &$this, 'wp_pre_get_posts' ) );
		}
		else {
			add_action( 'wp', array( &$this, 'wp_enqueue_css_js' ) );
			add_action( 'wp_footer', array( &$this, 'wp_footer' ) );
			add_action( 'wp_head', array( &$this, 'buffer_start' ) );
		}
		add_filter( 'wpseo_metadesc', array(&$this, 'protect_meta'), 10, 1 );
		add_filter( 'wpseo_opengraph_desc', array(&$this, 'protect_meta'), 10, 1 );
		add_filter( 'wpseo_twitter_description', array(&$this, 'protect_meta'), 10, 1 );
	}

	private function _protect_email_callback($match) {
		return '<span class="fhpe" data-a="' . strrev($match[3]) . '" data-b="' . strrev($match[2]) . '" data-c="' . strrev($match[1]) . '"><span class="fhpe">' . __( 'Email' ) . '</span></span>';
	}

	private function  _protect_mailto_callback($match) {
		return 'href="?fhpe=' . rawurlencode($this :: base64_encrypt(html_entity_decode(rawurldecode($match[2]), ENT_QUOTES, $this -> charset), $this -> key)) . '" rel="nofollow"';
	}

	public static function get_ip() {
		if (getenv('HTTP_CLIENT_IP'))
			$forwarded_elements = getenv('HTTP_CLIENT_IP');
		elseif (getenv('HTTP_X_FORWARDED_FOR'))
			$forwarded_elements = getenv('HTTP_X_FORWARDED_FOR');
		elseif (getenv('HTTP_X_FORWARDED'))
			$forwarded_elements = getenv('HTTP_X_FORWARDED');
		elseif (getenv('HTTP_FORWARDED_FOR'))
			$forwarded_elements = getenv('HTTP_FORWARDED_FOR');
		elseif (getenv('HTTP_FORWARDED'))
			$forwarded_elements = getenv('HTTP_FORWARDED');

		if (!empty($forwarded_elements)) {
			$forwarded_elements = explode(',', $forwarded_elements);
			foreach ($forwarded_elements as $forwarded_element) {
				$forwarded_pairs = explode(';', $forwarded_element);
				foreach ($forwarded_pairs as $forwarded_pair) {
					$forwarded_pair = explode('=', $forwarded_pair, 2);
					$node = trim(array_pop($forwarded_pair), " \t\n\r\0\x0B\"");
					if (substr($node, 0, 1) == '[') {
						// IPv6 with or without port
						$ip = substr($node, 1, strpos($node, ']') - 1);
					}
					else {
						// IPv4 with or without port, IPv6 without port
						$ip = $node;
						$node = explode(':', $node);
						if (substr_count($node[0], '.') == 3) $ip = $node[0]; // IPv4
					}
					if (!empty($ip) && $ip != 'unknown') break 2;
				}
			}
		}
		if (empty($ip) || $ip == 'unknown')
			$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
		return $ip;
	}

	public function buffer_start() {
		ob_start(array( &$this, 'protect_email' ));
		echo '<!-- fhpe start -->';
	}

	/**
	 * Reduce string to single digit unsigned number.
	 *
	 * @param string $str String to convert.
	 * @return int $reduced The single digit number.
	 */
	public static function reduce($str) {
		$hash = strval(abs(crc32($str)));
		return round(sqrt(intval($hash[0] . $hash[strlen($hash) - 1])));
	}

	public static function str_replace_first($search, $replace, $subject) {
		$pos = strpos($subject, $search);
		if ($pos !== false) {
			$subject = substr_replace($subject, $replace, $pos, strlen($search));
		}
		return $subject;
	}

	public function protect_email($html) {
		$html = preg_replace_callback('/href=([\'"])mailto:(.+?@.+?)\\1/i', array( &$this, '_protect_mailto_callback'), $html);
		$namepattern = '\w+(?:[+-.]\w+)*';
		$topleveldomainpattern = '[A-Za-z]+';
		if (preg_match_all('/^.*?<body[^>]*>|<\/body>.*?$|<!--.*?-->|<script[^>]*>.*?<\/script>|<select[^>]*>.*?<\/select>|<style[^>]*>.*?<\/style>|<textarea[^>]*>.*?<\/textarea>|<[^>]+>/is', $html, $matches)) {
			// Protect HTML tags and everything before <body> as well as after </body>
			foreach ($matches[0] as $index => $match) {
				$html = $this :: str_replace_first($match, "\0$index\0", $html);
			}
		}
		$html = preg_replace_callback('/(' . $namepattern . ')@(' . $namepattern . ')\.(' . $topleveldomainpattern . ')/', array( &$this, '_protect_email_callback'), $html);
		if (!empty($matches)) {
			// Restore HTML tags
			foreach ($matches[0] as $index => $match) {
				$html = $this :: str_replace_first("\0$index\0", $match, $html);
			}
		}
		$html .= '<!-- fhpe end -->';
		return $html;
	}
	
	public static function base64_decrypt($string, $key) {
		$result = '';
		$string = base64_decode($string);
		for ($i = 0; $i < strlen($string); $i ++) {
			$result .= chr(ord($string[$i]) - ord($key[$i % strlen($key)]));
		}
		return $result;
	}

	public static function base64_encrypt($string, $key) {
		$result = '';
		for ($i = 0; $i < strlen($string); $i ++) {
			$result .= chr(ord($string[$i]) + ord($key[$i % strlen($key)]));
		}
		return base64_encode($result);
	}

	public function wp_enqueue_css_js() {
		$this :: wp_enqueue( 'fhpe.css' );
		$this :: wp_enqueue( 'fhpe.js', array('jquery'), true );
	}

	public function wp_init() {
		if ( isset($_POST['product']) &&
			 $_POST['product'] == $this -> check_x * $this -> check_y ) {
			$email = $this :: base64_decrypt($_GET['fhpe'], $this -> key);
			add_action( 'wp_head', array( &$this, 'wp_redirect_mailto' ) );
			new FH_virtual_post(
				array(
					'slug' => 'fh-protect-email',
					'title' => __( 'Email' ),
					'type' => 'page',
					'content' => '<p><a href="mailto:' . $this :: wp_mailto_encode($email) . '">' . htmlspecialchars(preg_replace('/(?:\?.*)?$/', '', $email), ENT_QUOTES, $this -> charset) . '</a></p>'
				)
			);
		}
		else
			new FH_virtual_post(
				array(
					'slug' => 'fh-protect-email-form',
					'title' => __( 'Email' ),
					'type' => 'page',
					'content' => '<form action="' . $_SERVER['REQUEST_URI'] . '" method="post"><fieldset><legend>' . __( 'Anti-Spam' ) . '</legend><p><label for="product">' . $this -> check_x . ' × ' . $this -> check_y . ' = </label><input type="number" size="3" maxlength="3" name="product" id="product" /> <input type="submit" /></p></fieldset></form>'
				)
			);
	}
	
	public function wp_footer() {
		echo '<script data-fhpe="' . strrev($this -> key) . '"></script>';
	}
	
	public function wp_pre_get_posts() {
		$this -> wp_email_form();
	}
	
	public function wp_redirect_mailto() {
		$email = $this :: base64_decrypt($_GET['fhpe'], $this -> key);
		echo '<meta http-equiv="refresh" content="0; url=mailto:' . $this :: wp_mailto_encode($email) . '" />';
	}

	public static function wp_enqueue( $relative_src, $deps=array(), $in_footer=false ) {
		// By default add file modification time as version parameter
		$ver = filemtime(dirname(__FILE__) . '/' . $relative_src);
		// Get URL of src
		$src = plugins_url( $relative_src , __FILE__ );
		// Enqueue
		$pathinfo = pathinfo( $src );
		$handle = $pathinfo['filename'];
		if ( $pathinfo['extension'] == 'js' ) wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
		else wp_enqueue_style( $handle, $src, $deps, $ver );
	}
	
	public function wp_email_form() {
		global $wp;
		if ( isset($_POST['product']) &&
			 $_POST['product'] == $this -> check_x * $this -> check_y ) {
			$wp -> request = 'fh-protect-email';
		}
		else {
			$wp -> request = 'fh-protect-email-form';
		}
	}
	
	public static function wp_mailto_encode($email) {
		return rawurlencode($email);
	}

	public static function protect_meta( $meta ) {
		$namepattern = '\w+(?:[+-.]\w+)*';
		$topleveldomainpattern = '[A-Za-z]+';
		// Remove part before @ and TLD of email address
		$meta = preg_replace( '/(' . $namepattern . ')@(' . $namepattern . ')\.(' . $topleveldomainpattern . ')/', '@\\2', $meta );
		return $meta;
	}

}

if ( ! is_admin() ) $fh_protect_email = new FH_protect_email();

?>
