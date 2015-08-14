<?php
/*
Plugin name: FH 16 Column Grid
Plugin URI: https://github.com/fhoech/fh-wordpress-addons/blob/master/plugins/fh_16_column_grid.php
Description: 16 column grid via shortcodes (see plugin source)
Version: $Id:$
Author: Florian Höch
Author URI: http://hoech.net
License: GPL3
*/

// Row (just a container, can be used to e.g. clear floats)
function row($atts, $content=null) {
   return '<div class="row">' . do_shortcode($content) . '</div>';
}
add_shortcode('row', 'row');

// Column: Two Thirds (16 Column Layout)
function column_two_thirds_first($atts, $content=null) {
   return '<div class="two-thirds column alpha">' . do_shortcode($content) . '</div>';
}
function column_two_thirds($atts, $content=null) {
   return '<div class="two-thirds column">' . do_shortcode($content) . '</div>';
}
function column_two_thirds_last($atts, $content=null) {
   return '<div class="two-thirds column omega">' . do_shortcode($content) . '</div>';
}
add_shortcode('two_thirds_first', 'column_two_thirds_first');
add_shortcode('two_thirds', 'column_two_thirds');
add_shortcode('two_thirds_last', 'column_two_thirds_last');

// Column: One Third (16 Column Layout)
function column_one_third_first($atts, $content=null) {
   return '<div class="one-third column alpha">' . do_shortcode($content) . '</div>';
}
function column_one_third($atts, $content=null) {
   return '<div class="one-third column">' . do_shortcode($content) . '</div>';
}
function column_one_third_last($atts, $content=null) {
   return '<div class="one-third column omega">' . do_shortcode($content) . '</div>';
}
add_shortcode('one_third_first', 'column_one_third_first');
add_shortcode('one_third', 'column_one_third');
add_shortcode('one_third_last', 'column_one_third_last');

// Column: One Column (16 Column Layout)
function column_one_column_first($atts, $content=null) {
   return '<div class="one column alpha">' . do_shortcode($content) . '</div>';
}
function column_one_column($atts, $content=null) {
   return '<div class="one column">' . do_shortcode($content) . '</div>';
}
function column_one_column_last($atts, $content=null) {
   return '<div class="one column omega">' . do_shortcode($content) . '</div>';
}
add_shortcode('one_column_first', 'column_one_column_first');
add_shortcode('one_column', 'column_one_column');
add_shortcode('one_column_last', 'column_one_column_last');

// Column: Two Columns (16 Column Layout)
function column_two_columns_first($atts, $content=null) {
   return '<div class="two columns alpha">' . do_shortcode($content) . '</div>';
}
function column_two_columns($atts, $content=null) {
   return '<div class="two columns">' . do_shortcode($content) . '</div>';
}
function column_two_columns_last($atts, $content=null) {
   return '<div class="two columns omega">' . do_shortcode($content) . '</div>';
}
add_shortcode('two_columns_first', 'column_two_columns_first');
add_shortcode('two_columns', 'column_two_columns');
add_shortcode('two_columns_last', 'column_two_columns_last');

// Column: Three Columns (16 Column Layout)
function column_three_columns_first($atts, $content=null) {
   return '<div class="three columns alpha">' . do_shortcode($content) . '</div>';
}
function column_three_columns($atts, $content=null) {
   return '<div class="three columns">' . do_shortcode($content) . '</div>';
}
function column_three_columns_last($atts, $content=null) {
   return '<div class="three columns omega">' . do_shortcode($content) . '</div>';
}
add_shortcode('three_columns_first', 'column_three_columns_first');
add_shortcode('three_columns', 'column_three_columns');
add_shortcode('three_columns_last', 'column_three_columns_last');

// Column: Four Columns (16 Column Layout)
function column_four_columns_first($atts, $content=null) {
   return '<div class="four columns alpha">' . do_shortcode($content) . '</div>';
}
function column_four_columns($atts, $content=null) {
   return '<div class="four columns">' . do_shortcode($content) . '</div>';
}
function column_four_columns_last($atts, $content=null) {
   return '<div class="four columns omega">' . do_shortcode($content) . '</div>';
}
add_shortcode('four_columns_first', 'column_four_columns_first');
add_shortcode('four_columns', 'column_four_columns');
add_shortcode('four_columns_last', 'column_four_columns_last');

// Column: Five Columns (16 Column Layout)
function column_five_columns_first($atts, $content=null) {
   return '<div class="five columns alpha">' . do_shortcode($content) . '</div>';
}
function column_five_columns($atts, $content=null) {
   return '<div class="five columns">' . do_shortcode($content) . '</div>';
}
function column_five_columns_last($atts, $content=null) {
   return '<div class="five columns omega">' . do_shortcode($content) . '</div>';
}
add_shortcode('five_columns_first', 'column_five_columns_first');
add_shortcode('five_columns', 'column_five_columns');
add_shortcode('five_columns_last', 'column_five_columns_last');

// Column: Six Columns (16 Column Layout)
function column_six_columns_first($atts, $content=null) {
   return '<div class="six columns alpha">' . do_shortcode($content) . '</div>';
}
function column_six_columns($atts, $content=null) {
   return '<div class="six columns">' . do_shortcode($content) . '</div>';
}
function column_six_columns_last($atts, $content=null) {
   return '<div class="six columns omega">' . do_shortcode($content) . '</div>';
}
add_shortcode('six_columns_first', 'column_six_columns_first');
add_shortcode('six_columns', 'column_six_columns');
add_shortcode('six_columns_last', 'column_six_columns_last');

// Column: Seven Columns (16 Column Layout)
function column_seven_columns_first($atts, $content=null) {
   return '<div class="seven columns alpha">' . do_shortcode($content) . '</div>';
}
function column_seven_columns($atts, $content=null) {
   return '<div class="seven columns">' . do_shortcode($content) . '</div>';
}
function column_seven_columns_last($atts, $content=null) {
   return '<div class="seven columns omega">' . do_shortcode($content) . '</div>';
}
add_shortcode('seven_columns_first', 'column_seven_columns_first');
add_shortcode('seven_columns', 'column_seven_columns');
add_shortcode('seven_columns_last', 'column_seven_columns_last');

// Column: Eight Columns (16 Column Layout)
function column_eight_columns_first($atts, $content=null) {
   return '<div class="eight columns one-half column alpha">' . do_shortcode($content) . '</div>';
}
function column_eight_columns($atts, $content=null) {
   return '<div class="eight columns one-half column">' . do_shortcode($content) . '</div>';
}
function column_eight_columns_last($atts, $content=null) {
   return '<div class="eight columns one-half column omega">' . do_shortcode($content) . '</div>';
}
add_shortcode('eight_columns_first', 'column_eight_columns_first');
add_shortcode('eight_columns', 'column_eight_columns');
add_shortcode('eight_columns_last', 'column_eight_columns_last');
add_shortcode('one_half_first', 'column_eight_columns_first');
add_shortcode('one_half', 'column_eight_columns');
add_shortcode('one_half_last', 'column_eight_columns_last');

// Column: Nine Columns (16 Column Layout)
function column_nine_columns_first($atts, $content=null) {
   return '<div class="nine columns alpha">' . do_shortcode($content) . '</div>';
}
function column_nine_columns($atts, $content=null) {
   return '<div class="nine columns">' . do_shortcode($content) . '</div>';
}
function column_nine_columns_last($atts, $content=null) {
   return '<div class="nine columns omega">' . do_shortcode($content) . '</div>';
}
add_shortcode('nine_columns_first', 'column_nine_columns_first');
add_shortcode('nine_columns', 'column_nine_columns');
add_shortcode('nine_columns_last', 'column_nine_columns_last');

// Column: Ten Columns (16 Column Layout)
function column_ten_columns_first($atts, $content=null) {
   return '<div class="ten columns alpha">' . do_shortcode($content) . '</div>';
}
function column_ten_columns($atts, $content=null) {
   return '<div class="ten columns">' . do_shortcode($content) . '</div>';
}
function column_ten_columns_last($atts, $content=null) {
   return '<div class="ten columns omega">' . do_shortcode($content) . '</div>';
}
add_shortcode('ten_columns_first', 'column_ten_columns_first');
add_shortcode('ten_columns', 'column_ten_columns');
add_shortcode('ten_columns_last', 'column_ten_columns_last');

// Column: Eleven Columns (16 Column Layout)
function column_eleven_columns_first($atts, $content=null) {
   return '<div class="eleven columns alpha">' . do_shortcode($content) . '</div>';
}
function column_eleven_columns($atts, $content=null) {
   return '<div class="eleven columns">' . do_shortcode($content) . '</div>';
}
function column_eleven_columns_last($atts, $content=null) {
   return '<div class="eleven columns omega">' . do_shortcode($content) . '</div>';
}
add_shortcode('eleven_columns_first', 'column_eleven_columns_first');
add_shortcode('eleven_columns', 'column_eleven_columns');
add_shortcode('eleven_columns_last', 'column_eleven_columns_last');

// Column: Twelve Columns (16 Column Layout)
function column_twelve_columns_first($atts, $content=null) {
   return '<div class="twelve columns alpha">' . do_shortcode($content) . '</div>';
}
function column_twelve_columns($atts, $content=null) {
   return '<div class="twelve columns">' . do_shortcode($content) . '</div>';
}
function column_twelve_columns_last($atts, $content=null) {
   return '<div class="twelve columns omega">' . do_shortcode($content) . '</div>';
}
add_shortcode('twelve_columns_first', 'column_twelve_columns_first');
add_shortcode('twelve_columns', 'column_twelve_columns');
add_shortcode('twelve_columns_last', 'column_twelve_columns_last');

// Column: Thirteen Columns (16 Column Layout)
function column_thirteen_columns_first($atts, $content=null) {
   return '<div class="thirteen columns alpha">' . do_shortcode($content) . '</div>';
}
function column_thirteen_columns($atts, $content=null) {
   return '<div class="thirteen columns">' . do_shortcode($content) . '</div>';
}
function column_thirteen_columns_last($atts, $content=null) {
   return '<div class="thirteen columns omega">' . do_shortcode($content) . '</div>';
}
add_shortcode('thirteen_columns_first', 'column_thirteen_columns_first');
add_shortcode('thirteen_columns', 'column_thirteen_columns');
add_shortcode('thirteen_columns_last', 'column_thirteen_columns_last');

// Column: Fourteen Columns (16 Column Layout)
function column_fourteen_columns_first($atts, $content=null) {
   return '<div class="fourteen columns alpha">' . do_shortcode($content) . '</div>';
}
function column_fourteen_columns($atts, $content=null) {
   return '<div class="fourteen columns">' . do_shortcode($content) . '</div>';
}
function column_fourteen_columns_last($atts, $content=null) {
   return '<div class="fourteen columns omega">' . do_shortcode($content) . '</div>';
}
add_shortcode('fourteen_columns_first', 'column_fourteen_columns_first');
add_shortcode('fourteen_columns', 'column_fourteen_columns');
add_shortcode('fourteen_columns_last', 'column_fourteen_columns_last');

// Column: Fifteen Columns (16 Column Layout)
function column_fifteen_columns_first($atts, $content=null) {
   return '<div class="fifteen columns alpha">' . do_shortcode($content) . '</div>';
}
function column_fifteen_columns($atts, $content=null) {
   return '<div class="fifteen columns">' . do_shortcode($content) . '</div>';
}
function column_fifteen_columns_last($atts, $content=null) {
   return '<div class="fifteen columns omega">' . do_shortcode($content) . '</div>';
}
add_shortcode('fifteen_columns_first', 'column_fifteen_columns_first');
add_shortcode('fifteen_columns', 'column_fifteen_columns');
add_shortcode('fifteen_columns_last', 'column_fifteen_columns_last');

// Column: Flexible (16 Column Layout)
function column($atts, $content=null) {
	$first = false;
	$last = false;
	foreach ($atts as $att => $value) {
		if ($att == '' && $value == 'first') $first = true;
		if ($att == '' && $value == 'last') $last = true;
	}
	extract(shortcode_atts(array(
		'span' => 1,
		'push' => 0
	), $atts));
	$class = '';
	$columns = array(
		1 => 'one column',
		2 => 'two columns',
		3 => 'three columns',
		4 => 'four columns',
		5 => 'five columns',
		6 => 'six columns',
		7 => 'seven columns',
		8 => 'eight columns',
		9 => 'nine columns',
		10 => 'ten columns',
		11 => 'eleven columns',
		12 => 'twelve columns',
		13 => 'thirteen columns',
		14 => 'fourteen columns',
		15 => 'fifteen columns'
	);
	$push_by = array(
		1 => 'push_one',
		2 => 'push_two',
		3 => 'push_three',
		4 => 'push_four',
		5 => 'push_five',
		6 => 'push_six',
		7 => 'push_seven',
		8 => 'push_eight',
		9 => 'push_nine',
		10 => 'push_ten',
		11 => 'push_eleven',
		12 => 'push_twelve',
		13 => 'push_thirteen',
		14 => 'push_fourteen',
		15 => 'push_fifteen'
	);
	if ($push > 0) $class .= $push_by[$push] . ' ';
	$class .= $columns[$span];
	if ($first) $class .= ' alpha';
	if ($last) $class .= ' omega';
	return '<div class="' . $class . '">' . do_shortcode($content) . '</div>';
}
add_shortcode('column', 'column');

?>
