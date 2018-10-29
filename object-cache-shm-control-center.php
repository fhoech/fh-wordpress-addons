<?php

if ( ! is_file( __DIR__ . '/wp-config.php' ) ) die( "Place this file next to wp-config.php" );

$time_start = microtime( true );

define( 'SHORTINIT', true );

// Minimum WordPress includes to allow current_user_can() to function

require_once( __DIR__ . '/wp-config.php' );

require( ABSPATH . WPINC . '/formatting.php' );
require( ABSPATH . WPINC . '/capabilities.php' );
require( ABSPATH . WPINC . '/class-wp-roles.php' );
require( ABSPATH . WPINC . '/class-wp-role.php' );
require( ABSPATH . WPINC . '/class-wp-user.php' );
require( ABSPATH . WPINC . '/user.php' );
require( ABSPATH . WPINC . '/session.php' );
require( ABSPATH . WPINC . '/meta.php' );
require( ABSPATH . WPINC . '/kses.php' );
require( ABSPATH . WPINC . '/rest-api.php' );

require( ABSPATH . WPINC . '/pluggable.php' );

wp_plugin_directory_constants();

wp_cookie_constants();

$time_wp_load = microtime( true ) - $time_start;



/* Stub WordPress replacement for function from wp-includes/l10n.php */
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



$time_start = microtime( true );

$admin = current_user_can( 'administrator' );

$update_groups = isset( $_REQUEST['update_groups'] );
$clear_corrupt = isset( $_REQUEST['clear_corrupt'] );
$clear_all = isset( $_REQUEST['clear_all'] );
$trim = isset( $_REQUEST['trim'] );
$dump = ! empty( $_REQUEST['get'] );

if ( ( $update_groups || $clear_corrupt || $clear_all || $trim || $dump ) && ! $admin ) {
	http_response_code( 403 );

	$update_groups = false;
	$clear_corrupt = false;
	$clear_all = false;
	$trim = false;
	$dump = false;

	define( 'FORBIDDEN', true );
}

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
	<title>Object Cache Shared Memory Control Center</title>
	<script>

		function update_day_night_cycle( hours ) {

			if ( hours == undefined ) {
				var date = new Date(),
					hours = window.day_night_cycle_hours || date.getHours() + date.getMinutes() / 60 + date.getSeconds() / 60 / 60;
			}

			var start = <?php echo $start; ?>,
				end = <?php echo $end; ?>,
				f = ( hours - start ) / ( ( end - start ) / 2 );

			if ( f > 1 ) f = 1 - ( f - 1 );
			if ( f < 0 ) f = 0;
			f = Math.sin( f * Math.PI / 2 );

			document.body.style.backgroundPosition = '0 ' + ( 100 * f ) + '%';
			var c = hours < start + .25 || hours > end - .75 ? 0xcc : 0;
			document.body.style.color = 'rgb(' + c + ', ' + c + ', ' + c + ')';
			document.getElementById( 'sun' ).style.top = -( 100 * f ) + ( 150 * ( 1 - f ) ) + '%';

			var buttons = document.getElementsByTagName( 'button' ),
				c = hours < start + .25 || hours > end - .75 ? 255 : 0;
			for ( var i = 0; i < buttons.length; i ++ ) {
				buttons[i].style.color = 'rgb(' + c + ', ' + c + ', ' + c + ')';
			}
			
		}

		setInterval( update_day_night_cycle, 10000 );

		var three_state = [];

		function toggle_day_night_cycle() {

			var date = new Date(),
				hours = date.getHours() + date.getMinutes() / 60,
				start = <?php echo $start; ?>,
				end = <?php echo $end; ?>,
				range = end - start;

			if ( ! window.day_night_cycle_hours ) window.day_night_cycle_hours = hours;

			var cls;
			if ( document.body.className == '' ) {
				if ( hours > start + range / 4 && hours < end - range / 4) cls = 'night';
				else cls = 'day';
				while ( three_state.length ) three_state.pop();
				three_state.push( '' );
				three_state.push( cls == 'day' ? 'night' : 'day' );
				three_state.push( cls );
			}

			cls = three_state.pop();
			three_state.unshift( cls );

			document.body.style.transitionTimingFunction = 'ease, ease';
			document.body.style.transitionDuration = '3s, 1s';
			document.body.style.transitionDelay = '0s, ' + ( cls == 'day' ? '0s' : '1s' );
			document.getElementById( 'sun' ).style.transitionTimingFunction = 'ease, ease, ease, ease';
			document.getElementById( 'sun' ).style.transitionDuration = '3s, 3s, 3s, 3s';

			var buttons = document.getElementsByTagName( 'button' );
			for ( var i = 0; i < buttons.length; i ++ )
				buttons[i].style.transitionDelay = cls == 'day' ? '0s' : '1s';

			document.body.className = cls;

			document.getElementById( 'toggle' ).innerHTML = 'Day/night mode: ' + ( cls == '' ? 'Auto' : cls[0].toUpperCase() + cls.slice(1) );

			setTimeout( function () {
				document.body.style.transitionTimingFunction = 'linear, ease';
				document.body.style.transitionDuration = '10s, 1s';
				document.body.style.transitionDelay = '0s, 0s';
				document.getElementById( 'sun' ).style.transitionTimingFunction = 'linear, linear, linear, linear';
				document.getElementById( 'sun' ).style.transitionDuration = '10s, 10s, 10s, 10s';
			}, 3000 );

		}

		function get( tr ) {
			var group = tr.getAttribute( 'data-group' );
			location.href = "<?php echo $_SERVER['REQUEST_URI']; ?>?get=" + group;
		}

	</script>
	<style>
		html, body {
			height: 100%;
			min-height: 100%;
			overflow: hidden;
		}
		body {
			background: linear-gradient(to top, #fff 0%, #fff 8%, #ffefcf 40%, #c68666 70%, #282c30 90%, #080a0c 100%);
			background-position: 0 <?php echo ( 100 * $f ); ?>%;
			background-size: 100% 1600%;
			color: <?php echo $hours < $start + .25 || $hours > $end - .75 ? '#ccc' : '#000'; ?>;
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
		body.day {
			color: #000 !important;
		}
		body.night {
			color: #ccc !important;
		}
		a {
			color: inherit;
			text-decoration: none;
		}
		tbody tr:hover a,
		a:hover {
			color: #06f;
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
		}
		form {
			background: inherit;
			bottom: 0;
			position: fixed;
			padding: 0 1em;
			right: 1em;
		}
		form p {
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
			/* background: #f90;
			box-shadow: 0 0 320px 160px #f90; */
			top: 150% !important;
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
			color: <?php echo $hours < $start + .25 || $hours > $end - .75 ? '#fff' : '#000'; ?>;
			opacity: .796875;
			padding: 8px 16px;
			transition: color ease 1s;
		}
		body.day button,
		body.day input[type="button"] {
			color: #000 !important;
		}
		body.night button,
		body.night input[type="button"] {
			color: #fff !important;
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
<body data-class="<?php echo intval( date( 'H' ) ) > $start && intval( date( 'H' ) ) < $end ? 'day' : 'night'; ?>">
<main>
<?php

if ( defined( 'FORBIDDEN' ) ) {
	echo "<p class='error'>You do not have permission to perform this function.</p>";
}
else if ( $dump ) {

	$group = $_REQUEST['get'];

	$shm_cache = new SHM_Cache;
	
	$groups = SHM_Cache::get_groups();

	if ( isset( $groups[$group] ) ) {
		$shm_cache = new SHM_Cache( $group );
		$data = $shm_cache->get();
		if ( $data !== false ) {
			$entries = unserialize( $data );
			if ( is_array( $entries ) ) {
				echo "<pre>";
				print_r( $entries );
				echo "</pre>";
			}
			else if ( $entries !== false ) echo "<p class='error'>Unexpected data type for group '$group': " . gettype( $data ) . "</p>\n";
			else {
				echo "<p class='error'>Unserializing failed for group '$group'.</p>\n";
				//var_dump( $data );
			}
		}
		else echo "<p class='error'>ERROR reading shared memory for group '$group'.</p>\n";
	}
	else echo "<p class='error'>Group '$group' does not exist.</p>\n";

}
else if ( function_exists('shmop_open') ) {
?>
<table>
<thead>
<tr><th>#</th><th>Group</th><th>Project ID</th><th>SHM key</th><th>Resource ID</th><th>Entries</th><th>.expires entries</th><th>Bytes used</th><th></th><th>Bytes allocated</th><th></th><th>% used</th><th>Last modified</th><th></th></tr>
</thead>
<tbody>
<?php

	$non_persistent_groups = array('bp_notifications' => true,
										   'bp_messages' => true,
										   'bp_notifications_grouped_notifications' => true,
										   'bp_notifications_unread_count' => true,
										   'bp_messages_threads' => true,
										   'bp_messages_unread_count' => true,
										   'notification_meta' => true,
										   'message_meta' => true);

	// Init cache
	$shm_cache = new SHM_Cache;
	
	$groups = SHM_Cache::get_groups();

	$persistent_groups = array();
	foreach ( $groups as $group => $proj_id_mtime ) {
		if ( ! isset( $non_persistent_groups[$group] ) ) $persistent_groups[$group] = $proj_id_mtime;
	}

	if ( $update_groups || $trim ) {
		// Update SHM groups
		$id = SHM_Cache::get_groups_id();
		$data = serialize( $persistent_groups );
		$shm_id = SHM_Cache::get_groups_shm_id();
		$shm_size = SHM_Cache::get_groups_size();
		if ( $trim ) {
			if ( SHM_Cache::_delete( $shm_id ) ) {
				$shm_id = false;
			}
		}
		list( $bytes_written, $deleted, $shm_id ) = SHM_Cache::_write( $shm_id, $data, $shm_size, $id );
		if ( $shm_id === false || ! $bytes_written ) {
			echo "<p class='error'>" . date( 'Y-m-d H:i:s,v' ) .
								   " SHM_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Couldn't persist SHM $shm_id (key " . SHM_Cache::get_groups_id( true ) .
								   ( $deleted !== null ? ", deleted=" . ( $deleted ? 'true' : 'false' ) : '' ) . ") for groups to (proj_id, mtime) mapping: " .
								   $error['message'] . "</p>\n";
		}
		else if ( $bytes_written > $shm_size ) {
			echo "<p>" . date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Reallocated SHM $shm_id (key " . SHM_Cache::get_groups_id( true ) . ") for groups to (proj_id, mtime) mapping: " . $shm_size . " -> $bytes_written bytes</p>\n";
		}
		else {
			echo "<p>" . date( 'Y-m-d H:i:s,v' ) .
							   " SHM_Cache (" . FH_OBJECT_CACHE_UNIQID . "): Persisted SHM $shm_id (key " . SHM_Cache::get_groups_id( true ) . ") for groups to (proj_id, mtime) mapping: " . $shm_size . " -> $bytes_written bytes</p>\n";
		}
		$groups = $persistent_groups;
	}

	$groups_bytes = strlen( addcslashes( serialize( $groups ), "\0\\" ) ) + 6;  // 5 bytes header + terminating null
	$groups_bytes_allocated = SHM_Cache::get_groups_size();
	$used = $groups_bytes_allocated ? $groups_bytes / $groups_bytes_allocated : 0;
	$r = 102 * ( 2 - $used );
	$g = min( 153 * ( .5 + $used ), 204 );

	echo "<tr><td>0</td><td>Groups to (proj_id, mtime) mapping</td><td>0</td><td>" . SHM_Cache::get_groups_id( true ) . "</td><td>" . SHM_Cache::get_groups_shm_id() . "</td><td>" . count( $groups ) . "</td><td>N/A</td><td>$groups_bytes</td><td>" . human_size( $groups_bytes ) . "</td><td>$groups_bytes_allocated</td><td>" . human_size( $groups_bytes_allocated ) . "</td><td style='color: rgb($r, $g, 0);'>" . round( $used * 100, 2 ) . "%</td><td>N/A</td><td></td></tr>";

	$bytes_sum = $groups_bytes;
	$bytes_allocated_sum = $groups_bytes_allocated;
	$n = 1;
	$expires = array();
	$corrupt = 0;
	foreach ( $groups as $group => $proj_id_mtime ) {
		$shm_cache = new SHM_Cache( $group );
		$exists = $shm_cache->open();
		echo "<tr data-group='$group'" . ( ! $exists ? " class='unallocated'" : ( $admin ? " onclick='get( this )'" : "" ) ) . ( time() - $groups[$group][1] > HOUR_IN_SECONDS ? " class='stale'" : "" ) . ">";
		echo "<td>$n</td><td>$group</td><td>" . $groups[$group][0] . "</td><td>" . ( $exists ? $shm_cache->get_id( true ) : "Not allocated" ) . "</td>";
		$expires_count = isset( $expires[$group] ) ? count( $expires[$group] ) : 0;
		if ( $exists ) {
			echo "<td>" . $shm_cache->get_shm_id() . "</td>";
			$data = $shm_cache->get();
			if ( $trim || $clear_all || isset( $non_persistent_groups[$group] ) ) $shm_cache->clear();
			if ( $data !== false ) {
				if ( $trim ) $shm_cache->put( $data );
				$bytes = strlen( addcslashes( $data, "\0\\" ) ) + 6;  // 5 bytes header + terminating null
				$bytes_sum += $bytes;
				$entries = unserialize( $data );
				echo "<td>";
				if ( is_array( $entries ) ) {
					$count = count( $entries );
					echo $count;
					if ( $group == ".expires" ) $expires = $entries;
				}
				else if ( $entries !== false ) echo "Unexpected data type: " . gettype( $data );
				else {
					echo "Unserializing failed";
					//var_dump( $data );
				}
				echo "</td>";
				echo "<td" . ( $group != ".expires" && $expires_count != $count ? " class='error'" : "" ) . ">" . ( $group != ".expires" ? $expires_count : "N/A" ) . "</td>";
				echo "<td>" . $bytes . "</td><td>" . human_size( $bytes ) . "</td><td>" . $shm_cache->get_size() . "</td><td>" . human_size( $shm_cache->get_size() ) . "</td>";
				$bytes_allocated_sum +=  $shm_cache->get_size();
				$used = $bytes / $shm_cache->get_size();
				$r = 102 * ( 2 - $used );
				$g = min( 144 * ( .5 + $used ), 204 );
				echo "<td style='color: rgb($r, $g, 0);'>" . round( $used * 100, 2 ) . "%</td>";
				//if ( in_array( $group, array( 'themes', 'post_format_relationships', 'bp_member_member_type' ) ) ) var_dump( $data );
				echo "<td>" . date( 'Y-m-d H:i:s', $groups[$group][1] ) . ", " . fh_human_time_diff( $groups[$group][1] ) . "</td>";
				echo "<td>" . ( $admin ? "<a href='" . $_SERVER['REQUEST_URI'] . "?get=$group' title='Dump cache contents'>⯈</a>" : "" ) . "</td>";
			}
			else {
				if ( $clear_corrupt ) {
					$shm_cache->clear();
					echo "<td colspan='9' class='error'>Discarded</td>";
				}
				else echo "<td colspan='9' class='error'>ERROR reading shared memory</td>";
				$corrupt ++;
			}
		}
		else {
			echo "<td colspan='2'></td>";
			echo "<td>" . ( $group != ".expires" ? $expires_count : "N/A" ) . "</td>";
			echo "<td colspan='7'></td>";
		}
		echo "</tr>\n";
		$n ++;
	}
	//echo "<pre>";
	//print_r($expires);
	//echo "</pre>";
?>
</tbody>
</table>
<?php

	$used = $bytes_allocated_sum ? $bytes_sum / $bytes_allocated_sum : 0;
	$r = 102 * ( 2 - $used );
	$g = min( 153 * ( .5 + $used ), 204 );
	
	echo "<p>" . human_size( $bytes_sum ) . " used of " . human_size( $bytes_allocated_sum ) . " allocated (<span style='color: rgb($r, $g, 0);'>" . round( $used * 100, 2 ) . "%</span>)</p>\n";

	echo "<p>Groups to (proj_id, mtime) mapping modified? " . ( SHM_Cache::get_groups_persist() ? "Yes" : "No" ) . "</p>\n";

	printf( "<p>WordPress loaded in %.3f seconds, page generated in %.3f seconds</p>\n", round( $time_wp_load, 3 ), round( microtime( true ) - $time_start, 3 ) );

}
else echo "<p>shmop support disabled</p>\n";

?>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
<p>
<?php
	if ( function_exists( 'shmop_open' ) && $admin && ! $dump ) {
?>
	<button type="submit" name="update_groups">Update groups to (proj_id, mtime) mapping</button>
	<button type="submit" name="trim">Trim</button>
	<button type="submit" name="clear_corrupt"<?php if ( ! $corrupt )?> disabled<?php ; ?>>Clear corrupt</button>
	<button type="submit" name="clear_all" onclick="return confirm( 'Are you sure you want to clear all cache data?' )">Clear all</button>
<?php
	}
?>
	<button type="button" id="toggle" onclick="toggle_day_night_cycle()">Day/night mode: Auto</button>
</p>
</form>
</main>
<div id="sun"></div>
</body>
</html>
