<?php

if ( ! is_file( __DIR__ . '/wp-config.php' ) ) die( "Place this file next to wp-config.php" );

$time_start = microtime( true );

define( 'SHORTINIT', true );

// Minimum WordPress includes to allow current_user_can() to function

require_once( __DIR__ . '/wp-config.php' );

require_once( ABSPATH . WPINC . '/formatting.php' );
require_once( ABSPATH . WPINC . '/capabilities.php' );
require_once( ABSPATH . WPINC . '/class-wp-roles.php' );
require_once( ABSPATH . WPINC . '/class-wp-role.php' );
require_once( ABSPATH . WPINC . '/class-wp-user.php' );
require_once( ABSPATH . WPINC . '/user.php' );
require_once( ABSPATH . WPINC . '/class-wp-session-tokens.php' );
require_once( ABSPATH . WPINC . '/class-wp-user-meta-session-tokens.php' );
require_once( ABSPATH . WPINC . '/meta.php' );
require_once( ABSPATH . WPINC . '/kses.php' );
require_once( ABSPATH . WPINC . '/rest-api.php' );

require_once( ABSPATH . WPINC . '/pluggable.php' );

wp_plugin_directory_constants();

wp_cookie_constants();

$time_wp_load = microtime( true ) - $time_start;



ini_set('log_errors','Off');
ini_set('display_errors','On');
ini_set('error_reporting', E_ALL | E_STRICT );

/* Stub WordPress replacements for functions from wp-includes/l10n.php which isn't loaded because we set SHORTINIT = true */
function __( $text, $domain = 'default' ) {
	return $text;
}
function _n( $single, $plural, $number, $domain = 'default' ) {
	return $number > 1 ? $plural : $single;
}

/* Similar to WordPress human_time_diff (but not quite the same) */
function fh_human_time_diff( $from, $to = '' ) {
	if ( empty( $to ) ) {
			$to = time();
	}
	$diff = (int) abs( $to - $from );
	if ( $diff < 5 ) return 'now';
	if ( $diff < 60 ) {
			$since = 'less than 1 min';
	} elseif ( $diff < HOUR_IN_SECONDS ) {
			$mins = round( $diff / MINUTE_IN_SECONDS );
			if ( $mins <= 1 )
					$mins = 1;
			/* translators: Time difference between two dates, in minutes (min=minute). 1: Number of minutes */
			$since = sprintf( _n( '%s min', '%s mins', $mins ), $mins );
	} elseif ( $diff < DAY_IN_SECONDS && $diff >= HOUR_IN_SECONDS ) {
			$hours = round( $diff / HOUR_IN_SECONDS );
			if ( $hours <= 1 )
					$hours = 1;
			/* translators: Time difference between two dates, in hours. 1: Number of hours */
			$since = sprintf( _n( '%s hour', '%s hours', $hours ), $hours );
	} elseif ( $diff < WEEK_IN_SECONDS && $diff >= DAY_IN_SECONDS ) {
			$days = round( $diff / DAY_IN_SECONDS );
			if ( $days <= 1 )
					$days = 1;
			/* translators: Time difference between two dates, in days. 1: Number of days */
			$since = sprintf( _n( '%s day', '%s days', $days ), $days );
	} elseif ( $diff < MONTH_IN_SECONDS && $diff >= WEEK_IN_SECONDS ) {
			$weeks = round( $diff / WEEK_IN_SECONDS );
			if ( $weeks <= 1 )
					$weeks = 1;
			/* translators: Time difference between two dates, in weeks. 1: Number of weeks */
			$since = sprintf( _n( '%s week', '%s weeks', $weeks ), $weeks );
	} elseif ( $diff < YEAR_IN_SECONDS && $diff >= MONTH_IN_SECONDS ) {
			$months = round( $diff / MONTH_IN_SECONDS );
			if ( $months <= 1 )
					$months = 1;
			/* translators: Time difference between two dates, in months. 1: Number of months */
			$since = sprintf( _n( '%s month', '%s months', $months ), $months );
	} elseif ( $diff >= YEAR_IN_SECONDS ) {
			$years = round( $diff / YEAR_IN_SECONDS );
			if ( $years <= 1 )
					$years = 1;
			/* translators: Time difference between two dates, in years. 1: Number of years */
			$since = sprintf( _n( '%s year', '%s years', $years ), $years );
	}
	return $since . ' ago';
}

function human_size( $bytes ) {
	if ( $bytes < 1024 * 1024 )
		return round( $bytes / 1024, 2 ) . " KiB";
	return round( $bytes / 1024 / 1024, 2 ) . " MiB";
}



$admin = current_user_can( 'administrator' );

$clear_corrupt = isset( $_REQUEST['clear_corrupt'] );
$clear_all = isset( $_REQUEST['clear_all'] );
$clear = ! empty( $_REQUEST['clear'] ) ? $_REQUEST['clear'] : false;
$defrag = isset( $_REQUEST['defrag'] );
$get = ! empty( $_REQUEST['get'] ) ? $_REQUEST['get'] : false;
$dump = isset( $_REQUEST['dump'] );

if ( ( $clear_corrupt || $clear_all || $clear || $defrag || $get || $dump ) && ! $admin ) {
	http_response_code( 403 );

	if ( isset( $_REQUEST['json'] ) ) {
		header( 'Content-Type: application/json' );
		die();
	}

	$clear_corrupt = false;
	$clear_all = false;
	$clear = false;
	$defrag = false;
	$get = false;
	$dump = false;

	define( 'FORBIDDEN', true );
}

if ( ! $get || ! isset( $_REQUEST['json'] ) ) {

	$hours = floatval( date( 'H' ) ) + floatval( date( 'i' ) ) / 60 + floatval( date( 's' ) ) / 60 / 60;
	$tzoffset = get_option('gmt_offset');
	$hours += $tzoffset;
	$start = 6;
	$end = 22;
	$range = $end - $start;
	$f = ( $hours - $start ) / ( $range / 2 );
	if ( $f > 1 ) $f = 1 - ( $f - 1 );
	if ( $f < 0 ) $f = 0;
	$f = sin( $f * pi() / 2 );

?>
<!DOCTYPE html>
<html data-hours="<?php echo $hours; ?>" data-timezone-offset="<?php echo $tzoffset; ?>">
<head>
	<meta charset="utf-8">
	<title>Object Cache Partitioned Shared Memory Control Center</title>
	<script>

		function update_day_night_cycle( hours ) {

			if ( hours == undefined ) {
				var date = new Date(),
					hours = window.day_night_cycle_hours || date.getHours() + date.getMinutes() / 60 + date.getSeconds() / 60 / 60;
			}

			var start = <?php echo $start; ?>,
				end = <?php echo $end; ?>,
				f = ( hours - start ) / ( ( end - start ) / 2 ),
				sun = document.getElementById( 'sun' );

			if ( f > 1 ) f = 1 - ( f - 1 );
			if ( f < 0 ) f = 0;
			f = Math.sin( f * Math.PI / 2 );

			document.body.style.backgroundPosition = '0 ' + ( 100 * f ) + '%';
			sun.style.top = -( 100 * f ) + ( 150 * ( 1 - f ) ) + '%';
			
		}

		setInterval( update_day_night_cycle, 10000 );

		setInterval( function () {

			var sun = document.getElementById( 'sun' ),
				buttons = document.getElementsByTagName( 'button' ),
				day = sun.offsetTop < document.body.clientHeight;

			var c = day ? 0 : 0xcc;
			document.body.style.color = 'rgb(' + c + ', ' + c + ', ' + c + ')';

			var c = day ? 0 : 255;
			for ( var i = 0; i < buttons.length; i ++ ) {
				buttons[i].style.color = 'rgb(' + c + ', ' + c + ', ' + c + ')';
			}
	
		}, 1000 );

		var three_state = [];

		function toggle_day_night_cycle() {

			if (toggle_day_night_cycle._reset_transition_timeout) {
				clearTimeout(toggle_day_night_cycle._reset_transition_timeout);
				toggle_day_night_cycle._reset_transition_timeout = undefined;
			}

			var date = new Date(),
				hours = date.getHours() + date.getMinutes() / 60,
				start = <?php echo $start; ?>,
				end = <?php echo $end; ?>,
				range = end - start;

			var cls;
			if ( document.body.className == '' ) {
				if ( hours > start * 2 && hours < end - .25 ) cls = 'night';
				else cls = 'day';
				while ( three_state.length ) three_state.pop();
				three_state.push( '' );
				three_state.push( cls == 'day' ? 'night' : 'day' );
				three_state.push( cls );
			}

			cls = three_state.pop();
			three_state.unshift( cls );

			document.body.style.transitionTimingFunction = 'ease, ease';
			document.body.style.transitionDuration = '30s, 1s';
			document.getElementById( 'sun' ).style.transitionTimingFunction = 'ease, ease, ease, ease';
			document.getElementById( 'sun' ).style.transitionDuration = '30s, 30s, 30s, 30s';

			document.body.className = cls;

			document.getElementById( 'toggle' ).innerHTML = 'Day/night mode: ' + ( cls == '' ? 'Auto' : cls[0].toUpperCase() + cls.slice(1) );

			toggle_day_night_cycle._reset_transition_timeout = setTimeout( function () {
				document.body.style.transitionTimingFunction = 'linear, ease';
				document.body.style.transitionDuration = '10s, 1s';
				document.getElementById( 'sun' ).style.transitionTimingFunction = 'linear, linear, linear, linear';
				document.getElementById( 'sun' ).style.transitionDuration = '10s, 10s, 10s, 10s';
			}, 30000 );

		}

		function get( tr ) {
			if ( document.forms[0].elements[0].name ) return;
			var group = tr.getAttribute( 'data-group' );
			location.href = "<?php echo $_SERVER['SCRIPT_NAME']; ?>?get=" + encodeURIComponent( group ) + '&json';
		}

		function submit( a ) {
			var action = a.getAttribute( 'data-action' ),
				value = a.getAttribute( 'data-value' );
			document.forms[0].elements[0].name = action;
			document.forms[0].elements[0].value = value;
			document.forms[0].submit();
			return false;
		}

		function toggle_stats() {
			var main = document.getElementsByTagName( 'main' )[0];
			if ( main.className == 'hidden' ) main.className = '';
			else main.className = 'hidden';
		}

	</script>
	<style>
		html, body {
			height: 100%;
			min-height: 100%;
			overflow: hidden;
		}
		body {
			background: linear-gradient(to top, #fff 0%, #fff 8%, #ffefcf 40%, #c68666 72.5%, #282c30 90%, #080a0c 100%);
			background-position: 0 <?php echo ( 100 * $f ); ?>%;
			background-size: 100% 1600%;
			color: <?php echo $hours < $start + 1 || $hours > $end - 1 ? '#ccc' : '#000'; ?>;
			font-family: monospace;
			margin: 0 auto;
			position: relative;
			transition: background-position linear 10s, color ease 1s;
		}
		body.day {
			background-position: 0 100% !important;
		}
		body.night {
			background-position: 0 0% !important;
		}
		a {
			color: inherit;
			text-decoration: none;
		}
		tbody tr:hover td a:nth-child(2),
		tbody tr:hover td:hover a:hover,
		a:hover {
			color: #06f;
		}
		tbody tr:hover td:hover a {
			color: inherit;
		}
		main {
			bottom: 0;
			left: 0;
			overflow: auto;
			padding: 1em;
			position: absolute;
			right: 0;
			top: 0;
			z-index: 1;
			transition: opacity ease 1s;
		}
		.hidden {
			opacity: 0;
			pointer-events: none;
		}
		#cp {
			bottom: 0;
			position: fixed;
			padding: 0 1em;
			right: 1em;
			z-index: 9999;
		}
		#cp p {
			text-align: right;
		}
		#sun {
			background: #fffe9f;
			border-radius: 160px;
			box-shadow: 0 0 128px #fffe9f;
			height: 320px;
			left: 50%;
			width: 320px;
			margin-left: -160px;
			position: absolute;
			z-index: 0;
			transition: background-color linear 10s, box-shadow linear 10s, color linear 10s, top linear 10s;
			top: <?php echo -( 100 * $f ) + ( 150 * ( 1 - $f ) ); ?>%;
		}
		body.day #sun {
			top: -100% !important;
		}
		body.night #sun {
			top: 150% !important;
		}
		#sun.set {
			background: #f90;
			box-shadow: 0 0 320px 160px #f90;
		}
		table {
			border-collapse: collapse;
			width: 100%;
		}
		tbody tr:nth-child(odd) {
			background-color: rgba(128, 128, 128, .0625);
		}
		tbody tr[data-group]:not(.unallocated):hover {
			background-color: rgba(0, 102, 204, .2);
		}
		tbody tr[data-group][onclick]:hover {
			cursor: pointer;
		}
		th {
			text-align: left;
		}
		td, th {
			padding-right: 1em;
		}
		.unallocated td {
			opacity: .333;
		}
		.error {
			color: #c00;
		}
		.stale {
			opacity: .6;
		}
		button,
		input[type="button"] {
			background: rgba(128, 128, 128, .4);
			border: 0;
			border-radius: 0;
			color: <?php echo $hours < $start + 1 || $hours > $end - 1 ? '#fff' : '#000'; ?>;
			opacity: .796875;
			padding: 8px 16px;
			transition: color ease 1s;
		}
		button[name="clear_all"] {
			background: rgba(255, 0, 0, .4);
		}
		button:hover,
		input[type="button"]:hover {
			opacity: 1;
		}
		button[disabled],
		input[type="button"][disabled] {
			opacity: .25;
		}
		pre {
			padding-bottom: 1em;
		}
	</style>
</head>
<body>
<main>
<?php

}

if ( defined( 'FORBIDDEN' ) ) {
	echo "<p class='error'>You do not have permission to perform this function.</p>";
}
else if ( ! function_exists('shmop_open') ) {
	echo "<p>shmop support disabled</p>\n";
}
else if ( $get ) {

	$group = stripcslashes( $get );

	$shm_cache = new SHM_Partitioned_Cache( defined( 'FH_OBJECT_CACHE_SHM_SIZE' ) ? FH_OBJECT_CACHE_SHM_SIZE : 16 * 1024 * 1024, true );
	$groups = $shm_cache->get_groups();

	if ( $group === '.groups' ) {
		if ( isset( $_REQUEST['json'] ) ) {
			header( 'Content-Type: application/json' );
			echo json_encode( $groups );
			die();
		}
		else {
			echo "<pre>";
			echo htmlspecialchars( var_export( $groups, true ), ENT_COMPAT, 'UTF-8' );
			echo "</pre>";
		}
	}
	else if ( isset( $groups[$group] ) ) {
		$entries = array();
		$result = true;
		foreach ( $groups[$group]['keys'] as $key ) {
			$result = $shm_cache->get( $key, $group, false );
			if ( $result !== false ) {
				list( $value, $expire, $atime ) = $result;
				$entries[$key] = array( 'atime' => date( 'Y-m-d H:i:s T', $atime ),
										'expire' => $expire ? date( 'Y-m-d H:i:s T', $expire ) : 0,
										'expired' => $expire && $expire < time(),
										'data' => $value );
			}
		}
		if ( $result !== false || count( $entries ) ) {
			if ( is_array( $entries ) ) {
				if ( isset( $_REQUEST['json'] ) ) {
					header( 'Content-Type: application/json' );
					echo json_encode( $entries );
					die();
				}
				else {
					echo "<pre>";
					echo htmlspecialchars( var_export( $entries, true ), ENT_COMPAT, 'UTF-8' );
					echo "</pre>";
				}
			}
			else if ( $entries !== false ) {
				if ( isset( $_REQUEST['json'] ) ) {
					http_response_code( 500 );
					header( 'Content-Type: application/json' );
					die();
				}
				else echo "<p class='error'>Unexpected data type for group '$group': " . gettype( $data ) . "</p>\n";
			}
			else {
				if ( isset( $_REQUEST['json'] ) ) {
					http_response_code( 500 );
					header( 'Content-Type: application/json' );
					die();
				}
				else echo "<p class='error'>Unserializing failed for group '$group'.</p>\n";
				//var_dump( $data );
			}
		}
		else if ( isset( $_REQUEST['json'] ) ) {
			http_response_code( 500 );
			header( 'Content-Type: application/json' );
			die();
		}
		else echo "<p class='error'>ERROR reading shared memory for group '$group'.</p>\n";
	}
	else if ( isset( $_REQUEST['json'] ) ) {
		http_response_code( 404 );
		header( 'Content-Type: application/json' );
		die();
	}
	else echo "<p class='error'>Group '$group' does not exist.</p>\n";

}
else {

	$non_persistent_groups = array();

	if ( $defrag || $clear_all || $clear ) $GLOBALS['wp_object_cache']->acquire_lock();

	echo "<p>";

	// Init cache
	echo "Initializing cache...";
	$time_init_start = microtime( true );
	$shm_cache = new SHM_Partitioned_Cache( defined( 'FH_OBJECT_CACHE_SHM_SIZE' ) ? FH_OBJECT_CACHE_SHM_SIZE : 16 * 1024 * 1024 );
	printf( "done (%.1f ms)<br>\n", number_format( ( microtime( true ) - $time_init_start ) * 1000, 1 ) );
	if ( $shm_cache->use_file_backend )
		echo "Cache file: " . FH_OBJECT_CACHE_PATH . "<br>\n";
	else
		echo "SHM key: " . $shm_cache->get_id( true ) . "<br>\n";
	$shm_id = $shm_cache->get_shm_id();
	echo print_r( $shm_id, true ) . "<br>\n";

	if ( $clear_all && $shm_cache->clear() ) echo "Cleared cache<br>\n";
	if ( $defrag && $shm_cache->defrag() ) echo "Defragged cache<br>\n";

	echo "Hashtable size: {$shm_cache->partition_size} bytes (" . human_size( $shm_cache->partition_size ) . ")<br>\n";

	echo "Parsing partition table...";
	$time_parse_start = microtime( true );
	$shm_cache->parse_partition_table( true );
	printf( "done (%.1f ms)<br>\n", number_format( ( microtime( true ) - $time_parse_start ) * 1000, 1 ) );
	echo "Partition entries: " . count( $shm_cache->partition ) . "<br>\n";

	echo "Enumerating groups...";
	$time_groups_get_start = microtime( true );
	$groups = $shm_cache->get_groups( false, true );
	$time_groups_get = microtime( true ) - $time_groups_get_start;
	printf( "done (%.1f ms)<br>\n", number_format( $time_groups_get * 1000, 1 ) );

	if ( $dump ) {
		echo "Dumping cache contents to .object_cache_shm_dump.bin<br>\n";
		$GLOBALS['wp_object_cache']->acquire_lock( LOCK_SH );
		file_put_contents( __DIR__ . '/.object_cache_shm_dump.bin', shmop_read( $shm_cache->res, 0, $shm_cache->size ) );
	}

	echo "</p>";
?>
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
<input type="hidden">
<table>
<thead>
<tr><th>#</th><th>Group</th><th>Entries</th><th>Bytes used</th><th></th><th>Bytes allocated</th><th></th><th>Largest entry size</th><th>% used</th><th>Last accessed</th><th><?php if ( $admin ) { ?>Admin<?php } ?></th></tr>
</thead>
<tbody>
<?php

	$persistent_groups = array();
	$partition_table_entries = 0;
	foreach ( $groups as $group => $stats ) {
		if ( ! isset( $non_persistent_groups[$group] ) ) $persistent_groups[$group] = true;
		$partition_table_entries += count( $stats[ 'keys' ] ) + count( $stats[ 'deleted_keys' ] );
	}

	$groups_bytes = $shm_cache->get_partition_size();
	$groups_bytes += $groups_bytes / $shm_cache->hash_bytes * 8;
	$groups_bytes_allocated = $shm_cache->get_data_offset();
	$used = $groups_bytes_allocated ? $groups_bytes / $groups_bytes_allocated : 0;
	$r = 102 * ( 2 - ( 1 - $used ) );
	$g = min( 153 * ( .5 + ( 1 - $used ) ), 204 );

	echo "<tr data-group='.groups'" . ( $admin ? " onclick='get( this )'" : "" ) . "><td>0</td><td>&lt;Partition table&gt;</td><td>$partition_table_entries</td><td>$groups_bytes</td><td>" . human_size( $groups_bytes ) . "</td><td>$groups_bytes_allocated</td><td>" . human_size( $groups_bytes_allocated ) . "</td><td>N/A</td><td style='color: rgb($r, $g, 0);'>" . round( $used * 100, 2 ) . "%</td><td>N/A</td>";
	echo "<td>" . ( $admin ? "<a href='" . $_SERVER['SCRIPT_NAME'] . "?get=.groups' title='Dump cache contents as PHP'>PHP</a> <a href='" . $_SERVER['SCRIPT_NAME'] . "?get=.groups&amp;json' title='Dump cache contents as JSON'>JSON</a>" : "" ) . "</td>";
	echo "</tr>";

	$bytes_sum = 0;
	$bytes_allocated_sum = 0;
	$n = 1;
	$corrupt = 0;
	$total_entries_count = 0;
	$keys = array();
	foreach ( $groups as $group => $stats ) {
		if ( $clear === $group ) {
			$exists = ! $shm_cache->delete_group( $group, ! $stats[ 'bytes_allocated' ] );
			if ( ! $stats[ 'bytes_allocated' ] ) continue;
		}
		else $exists = $stats[ 'bytes_allocated' ] > 0;
		$atime = $stats[ 'atime' ];
		echo "<tr data-group='$group'" . ( ! $exists ? " class='unallocated'" : ( $admin ? " onclick='get( this )'" : "" ) ) . ( $stats[ 'expire' ] && $stats[ 'expire' ] <= time() ? " class='stale'" : "" ) . ">";
		echo "<td>$n</td><td>$group</td>";
		if ( $exists ) {
			if ( $stats !== false ) {
				$bytes = $stats[ 'bytes_used' ];
				$bytes_sum += $bytes;
				echo "<td>";
				$entry_max_size_key = $stats[ 'entry_max_size_key' ];
				$entry_max_size = $stats[ 'entry_max_size' ];
				$count = $stats[ 'entries_count' ];
				$total_entries_count += $count;
				echo $count;
				echo "</td>";
				$bytes_allocated = $stats[ 'bytes_allocated' ];
				echo "<td>" . $bytes . "</td><td>" . human_size( $bytes ) . "</td><td>$bytes_allocated</td><td>" . human_size( $bytes_allocated ) . "</td><td>" . human_size( $entry_max_size ) . "</td>";
				$bytes_allocated_sum +=  $bytes_allocated;
				$used = $bytes ? $bytes / $bytes_allocated : 0;
				$r = 102 * ( 2 - $used );
				$g = min( 144 * ( .5 + $used ), 204 );
				echo "<td style='color: rgb($r, $g, 0);'>" . round( $used * 100, 2 ) . "%</td>";
				//if ( in_array( $group, array( 'themes', 'post_format_relationships', 'bp_member_member_type' ) ) ) var_dump( $data );
				echo "<td>" . date( 'Y-m-d H:i:s', $atime ) . ", " . fh_human_time_diff( $atime ) . "</td>";
				echo "<td>" . ( $admin ? "<a href='" . $_SERVER['SCRIPT_NAME'] . "?get=" . rawurlencode( $group ) . "' title='Dump cache contents as PHP'>PHP</a> <a href='" . $_SERVER['SCRIPT_NAME'] . "?get=" . rawurlencode( $group ) . "&amp;json' title='Dump cache contents as JSON'>JSON</a> <a href='" . $_SERVER['SCRIPT_NAME'] . "?clear=" . rawurlencode( $group ) . "' title='Clear cache contents' onclick='return confirm( &quot;Are you sure you want to clear cache data for group $group?&quot; ) && submit( this )' data-action='clear' data-value='$group'>Clear</a>" : "" ) . "</td>";
			}
			else {
				if ( $clear_corrupt ) {
					//$shm_cache->delete_group( $group );
					echo "<td colspan='10' class='error'>Discarded</td>";
				}
				else echo "<td colspan='10' class='error'>ERROR reading shared memory</td>";
				$corrupt ++;
			}
		}
		else {
			echo "<td colspan='8'>Not allocated</td>";
			echo "<td>" . ( $admin ? "<a href='" . $_SERVER['SCRIPT_NAME'] . "?clear=" . rawurlencode( $group ) . "' title='Permanently delete entry' onclick='return confirm( &quot;Are you sure you want to permanently delete $group?&quot; ) && submit( this )' data-action='clear' data-value='$group'>Delete</a>" : "" ) . "</td>";
		}
		echo "</tr>\n";
		$n ++;
	}

	if ( $defrag || $clear_all || $clear ) $GLOBALS['wp_object_cache']->release_lock();

?>
</tbody>
</table>
</form>
<?php

	$size = $shm_cache->size;
	$used_bytes = $shm_cache->next - $shm_cache->data_offset + $shm_cache->partition_size;

	$free_bytes = $size - $used_bytes;

	$free = $size ? $free_bytes / $size : 1;
	$r = 102 * ( 2 - min( $free * 2, 1 ) );
	$g = min( 153 * ( .5 + min( $free * 2, 1 ) ), 204 );

	echo "<p>Cache size " . human_size( $shm_cache->get_size() ) . ", effective " . human_size( $shm_cache->get_size() - $shm_cache->get_data_offset() ) . ", " . human_size( $free_bytes ) . " free (<span style='color: rgb($r, $g, 0);'>" . round( $free * 100, 2 ) . "%</span>), ";

	$wasted_bytes = $shm_cache->get_next() - $shm_cache->get_data_offset() - $bytes_sum;

	$wasted = $size ? $wasted_bytes / ( $shm_cache->get_size() - $shm_cache->get_data_offset() ) : 0;
	$r = 102 * ( 2 - ( 1 - $wasted ) );
	$g = min( 153 * ( .5 + ( 1 - $wasted ) ), 204 );

	echo  human_size( $wasted_bytes ) . " wasted (<span style='color: rgb($r, $g, 0);'>" . round( $wasted * 100, 2 ) . "%</span>)</p>";

	$effectiveused = $bytes_allocated_sum ? $bytes_sum / $bytes_allocated_sum : 0;
	$r = 102 * ( 2 - $effectiveused );
	$g = min( 153 * ( .5 + $effectiveused ), 204 );
	
	echo "<p>" . $total_entries_count . " entries using " . human_size( $bytes_sum ) . " (<span style='color: rgb($r, $g, 0);'>" . round( $effectiveused * 100, 2 ) . "%</span>) of " . human_size( $bytes_allocated_sum ) . " allocated</p>\n";

	printf( "<p>WordPress loaded in %.1f ms, page generated in %.1f ms (peak memory used %s, allocated %s)</p>\n", round( $time_wp_load * 1000, 1 ), round( ( microtime( true ) - $time_start ) * 1000, 1 ), human_size( memory_get_peak_usage() ), human_size( memory_get_peak_usage( true ) ) );

}

?>
</main>
<form id="cp" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
<p>
<?php
	if ( function_exists( 'shmop_open' ) && $admin && ! $get ) {
?>
	<button type="submit" name="dump">Dump SHM to file</button>
	<button type="submit" name="defrag">Defrag</button>
	<button type="submit" name="clear_corrupt"<?php if ( ! $corrupt )?> disabled<?php ; ?>>Clear corrupt</button>
	<button type="submit" name="clear_all" onclick="return confirm( 'Are you sure you want to clear all cache data?' )">Clear all</button>
<?php
	}
?>
	<button type="button" id="toggle" onclick="toggle_day_night_cycle()">Day/night mode: Auto</button>
	<button type="button" onclick="toggle_stats()">Toggle stats</button>
</p>
</form>
<div id="sun"></div>
</body>
</html>
