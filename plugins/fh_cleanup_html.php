<?php
/*
Plugin name: FH Cleanup HTML
*/

function fh_cleanup_html($html){
	// Set table border to "" and remove cellspacing/cellpadding
	$html = preg_replace('/(<table\s+[^>]*?border=)[^>\s]+/i', '\1""', $html);
	$html = preg_replace('/(<table\s+[^>]*?)cellspacing=[^>\s]+/i', '\1', $html);
	$html = preg_replace('/(<table\s+[^>]*?)cellpadding=[^>\s]+/i', '\1', $html);
	// Remove table width, height, style, (v)align
	$html = preg_replace('/(<(?:table|td|th)\s+[^>]*?)width=[^>\s]+/i', '\1', $html);
	$html = preg_replace('/(<(?:table|td|th)\s+[^>]*?)height=[^>\s]+/i', '\1', $html);
	$html = preg_replace('/(<(?:table|td|th)\s+[^>]*?)style="[^"]+"/i', '\1', $html);
	$html = preg_replace('/(<(?:tr|td|th)\s+[^>]*?)v?align=[^>\s]+/i', '\1', $html);
	// Fix common bad nesting
	$html = preg_replace('/<(b|em|i|span|strong)(?:\s+[^>]*|\/)?>\s*(\[(\w+)[^\]]*\](?:.*?\[\/\3\])?|<div[^>]*>.*?<\/div>)\s*(?:<\/\1>)?/is', '\2', $html);
	$html = preg_replace('/(<p[^>]*>)\s*<\/?(?:b|em|i|span|strong)(?:\s+[^>]*|\/)?>\s*<\/p>/i', '\1</p>', $html);
	$html = preg_replace('/<p[^>]*>\s*(\[\/?\w+[^\]]*\]|<\/?div[^>]*>)/i', '\1', $html);
	$html = preg_replace('/(\[\/?\w+[^\]]*\]|<\/?div[^>]*>)\s*<\/p>/i', '\1', $html);
	$html = preg_replace('/(<\/div[^>]*>)\s*<br(?:\s+\/)?>/i', '\1', $html);
	return $html;
}
 
add_filter('the_content', 'fh_cleanup_html', 9999);

?>
