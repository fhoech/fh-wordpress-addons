<?php
/*
Plugin name: FH Cleanup HTML
Plugin URI: https://github.com/fhoech/fh-wordpress-addons/blob/master/plugins/fh_cleanup_html.php
Description: Remove a bit of cruft from post HTML.
Version: $Id$
Author: Florian Höch
Author URI: http://hoech.net
License: GPL3
*/

function str_replace_first($search, $replace, $subject) {
	$pos = strpos($subject, $search);
	if ($pos !== false) {
		$subject = substr_replace($subject, $replace, $pos, strlen($search));
	}
	return $subject;
}

function fh_cleanup_html($html){
	// Set table border to "" and remove cellspacing/cellpadding
	$html = preg_replace('/(<table\s+[^>]*?border=)[^>\s]+/i', '\1""', $html);
	$html = preg_replace('/(<table\s+[^>]*?)cellspacing=[^>\s]+/i', '\1', $html);
	$html = preg_replace('/(<table\s+[^>]*?)cellpadding=[^>\s]+/i', '\1', $html);
	// Remove table width, height, (v)align
	$html = preg_replace('/(<(?:table|td|th)\s+[^>]*?)width=[^>\s]+/i', '\1', $html);
	$html = preg_replace('/(<(?:table|td|th)\s+[^>]*?)height=[^>\s]+/i', '\1', $html);
	$html = preg_replace('/(<(?:tr|td|th)\s+[^>]*?)v?align=[^>\s]+/i', '\1', $html);
	// Fix common bad nesting
	$html = preg_replace('/<(b|em|i|span|strong)(?:\s+[^>]*|\/)?>\s*(\[(\w+)[^\]]*\](?:.*?\[\/\3\])?|<div[^>]*>.*?<\/div>)\s*(?:<\/\1>)?/is', '\2', $html);
	$html = preg_replace('/(<p[^>]*>)\s*<\/?(?:b|em|i|span|strong)(?:\s+[^>]*|\/)?>\s*<\/p>/i', '\1</p>', $html);
	$html = preg_replace('/<p[^>]*>\s*(\[\/?\w+[^\]]*\]|<\/?(?:div|ol|ul)[^>]*>)/i', '\1', $html);
	$html = preg_replace('/(\[\/?\w+[^\]]*\]|<\/?(?:div|ol|ul)[^>]*>)\s*<\/p>/i', '\1', $html);
	$html = preg_replace('/<p>\s*(<\/div>)/i', '\1', $html);
	$html = preg_replace('/(<\/div[^>]*>)\s*<br(?:\s+\/)?>/i', '\1', $html);
	// Remove empty tags
	$count = 1;
	while ($count) $html = preg_replace('~<(\w+)(?:\s+[0-9a-z\-_:]+=""|\s)*></\1>~i', '', $html, -1, $count);
	// Remove MS Office cruft
	$html = preg_replace('/<!--\[if\s+(?:\S+\s+)?mso.*?-->/s', '', $html);
	// Remove empty paragraphs
	$html = str_replace('<p>&nbsp;</p>', '', $html);

	// Remove accidental entity-encoded HTML
	if ( preg_match_all( '~<pre[^>]*>.*?</pre>|<code[^>]*>.*?</code>|<[^>]+>|&(?:#\d+|nbsp|quot);~is', $html, $matches ) ) {
		// Protect HTML tags and entities
		foreach ( $matches[0] as $index => $match ) {
			$html = str_replace_first( $match, "\0$index\0", $html );
		}
	}
	$html = str_replace( '&lt;', '<', $html );
	$html = str_replace( '&gt;', '>', $html );
	$html = preg_replace( '~</?(div|p)[^>]*>~' , '', $html );
	$html = str_replace( '<', '&lt;', $html );
	$html = str_replace( '>', '&gt;', $html );
	if ( !empty( $matches ) ) {
		// Restore HTML tags
		foreach ( $matches[0] as $index => $match ) {
			$html = str_replace_first( "\0$index\0", $match, $html );
		}
	}

	return $html /*. (current_filter() == 'the_content' ? '<!-- fh_cleanup_html -->' : '')*/;
}

function fh_cleanup_comment($text) {
	$text = preg_replace('~\n&nbsp;(\n|$)~', "\n", $text);
	$text = fh_cleanup_html($text);
	return $text;
}
 
add_filter('the_content', 'fh_cleanup_html');
add_filter('content_save_pre', 'fh_cleanup_html');
add_filter('comment_text', 'fh_cleanup_comment');
add_filter('bbp_get_topic_content', 'fh_cleanup_comment');
add_filter('bbp_get_reply_content', 'fh_cleanup_comment');

?>
