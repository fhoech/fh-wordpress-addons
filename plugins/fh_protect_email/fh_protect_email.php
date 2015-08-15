<?php
/*
Plugin name: FH Protect E-Mail
Plugin URI: https://github.com/fhoech/fh-wordpress-addons/blob/master/plugins/fh_protect_email/fh_protect_email.php
Description: Protect email addresses from spambots.
Version: $Id:$
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

	private $key;

	public function __construct() {
		session_start();
		if (empty($_SESSION['fhpe_key']))
			$this -> key = $_SESSION['fhpe_key'] = uniqid(rand());
		else $this -> key = $_SESSION['fhpe_key'];
		if (function_exists('add_action')) {
			if (!empty($_GET['fhpe'])) {
				add_action( 'init', array( &$this, 'wp_init' ), 1 );
				add_action( 'pre_get_posts', array( &$this, 'wp_pre_get_posts' ) );
			}
			add_action( 'wp', array( &$this, 'wp_enqueue_css_js' ) );
			add_action( 'wp_head', array( &$this, 'wp_meta_key' ) );
			add_filter( 'the_content', array( &$this, 'protect_email' ), 99 );
		}
	}

	private function _protect_email_callback($match) {
		$fakerecipient = $this :: _alpha(sprintf('%x', crc32($match[2] . $match[1])));
		$fakedomain = $this :: _alpha(sprintf('%x', crc32($match[1] . $match[2])));
		return '<span class="fhpe" data-a="' . strrev($match[3]) . '" data-b="' . strrev($match[2]) . '" data-c="' . strrev($match[1]) . '"><span class="fhpe">' . __( 'Email' ) . '</span></span>';
	}

	private function  _protect_mailto_callback($match) {
		return 'href="?fhpe=' . rawurlencode($this :: base64_encrypt(html_entity_decode($match[2], ENT_QUOTES, get_bloginfo('charset')), $this -> key)) . '" rel="nofollow"';
	}

	/**
	 * Convert alphanumeric string to alpha-only string.
	 *
	 * @param string $str String to convert.
	 * @return string $alpha The alpha-only string.
	 */
	private static function _alpha($str) {
		$alpha = [];
		for ($i = 0; $i < strlen($str); $i ++) {
			if (is_numeric($str[$i])) $alpha[] = chr(intval($str[$i]) + 97);
			else $alpha[] = $str[$i];
		}
		return implode('', $alpha);
	}

	public function protect_email($html) {
		$html = preg_replace_callback('/href=([\'"])mailto:(.+?@.+?)\\1/i', array( &$this, '_protect_mailto_callback'), $html);
		$namepattern = '\w+(?:[+-.]\w+)*';
		$topleveldomainpattern = '[A-Za-z]+';
		$html = preg_replace_callback('/(' . $namepattern . ')@(' . $namepattern . ')\.(' . $topleveldomainpattern . ')/', array( &$this, '_protect_email_callback'), $html);
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
		$this :: wp_enqueue( 'fh_protect_email.css' );
		$this :: wp_enqueue( 'fh_protect_email.js', array(), true );
	}

	public function wp_init() {
		if ( !isset($_SESSION['fh_protect_email_check_x']) ||
			 !isset($_POST['product']) ||
			 $_POST['product'] != $_SESSION['fh_protect_email_check_x'] * $_SESSION['fh_protect_email_check_y'] ) {
			// Prepare a math question
			$x = rand(1, 10);
			do { $y = rand(1, 10); } while ($x == $y);
			$_SESSION['fh_protect_email_check_x'] = $x;
			$_SESSION['fh_protect_email_check_y'] = $y;
		}
		if ( isset($_POST['product']) &&
			 $_POST['product'] == $_SESSION['fh_protect_email_check_x'] * $_SESSION['fh_protect_email_check_y'] ) {
			$email = $this :: base64_decrypt($_GET['fhpe'], $this -> key);
			add_action( 'wp_head', array( &$this, 'wp_redirect_mailto' ) );
			remove_filter( 'the_content', array( &$this, 'protect_email' ) );
			new FH_virtual_post(
				array(
					'slug' => 'fh-protect-email',
					'title' => 'E-Mail',
					'type' => 'page',
					'content' => '<p><a href="mailto:' . $this :: wp_mailto_encode($email) . '">' . htmlspecialchars(preg_replace('/(?:\?.*)?$/', '', $email), ENT_QUOTES, get_bloginfo('charset')) . '</a></p>'
				)
			);
		}
		else
			new FH_virtual_post(
				array(
					'slug' => 'fh-protect-email-form',
					'title' => 'E-Mail',
					'type' => 'page',
					'content' => '<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">	<p><label for="product">' . $_SESSION['fh_protect_email_check_x'] . ' * ' . $_SESSION['fh_protect_email_check_y'] . ' = </label><input type="number" size="2" maxlength="2" name="product" id="product" /> <input type="submit" /></p></form>'
				)
			);
	}
	
	public function wp_meta_key() {
		echo '<meta data-fhpe="' . strrev($this -> key) . '" />';
	}
	
	public function wp_pre_get_posts() {
		$this :: wp_email_form();
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
	
	public static function wp_email_form() {
		global $wp;
		if ( isset($_POST['product']) &&
			 $_POST['product'] == $_SESSION['fh_protect_email_check_x'] * $_SESSION['fh_protect_email_check_y'] ) {
			$wp -> request = 'fh-protect-email';
		}
		else {
			$wp -> request = 'fh-protect-email-form';
		}
	}
	
	public static function wp_mailto_encode($email) {
		return str_replace(' ', '%20', htmlspecialchars($email, ENT_QUOTES, get_bloginfo('charset')));
	}

}

$fh_protect_email = new FH_protect_email();

?>
