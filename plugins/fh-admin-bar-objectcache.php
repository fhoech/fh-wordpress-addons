<?php 
/*
Plugin Name: Admin Bar Object Cache Statistics
Plugin URI: https://github.com/fhoech/fh-wordpress-addons/blob/master/plugins/fh-admin-bar-objectcache.php
Description: Show Object Cache information in the WP Admin Bar.
Author: Florian Höch
Version: 0.1
Author URI: https://hoech.net/
*/

function fh_admin_bar_objectcache_add(){
	if ( !is_super_admin() )
		return;

	global $wp_admin_bar, $wp_object_cache;

	$wp_admin_bar->add_menu( array(
		'id' => 'admin_bar_objectcache',
		'title' => 'Cache …% (… hits, … misses)',
		'href' => 'javascript:void(0)',
		'meta' => array(
			'onclick' => 'window.wpDebugBar&&((!wpDebugBar.isVisible()||jQuery("#debug-menu-target-Debug_Bar_Object_Cache").is(":visible"))&&wpDebugBar.toggle.visibility(),wpDebugBar.isVisible()&&jQuery("#debug-menu-link-Debug_Bar_Object_Cache").click());return false'
		)
	));
	
	if ( is_object( $wp_object_cache ) &&
		 property_exists( $wp_object_cache, 'backend' ) &&
		 property_exists( $wp_object_cache->backend, 'next' ) &&
		 property_exists( $wp_object_cache->backend, 'size' ) &&
		 property_exists( $wp_object_cache->backend, 'partition_size' ) &&
		 property_exists( $wp_object_cache->backend, 'data_offset' ) ) {
		$wp_admin_bar->add_menu( array(
			'parent' => 'admin_bar_objectcache',
			'id' => 'admin_bar_objectcache_utilization',
			'title' => 'Utilization: …%'
		));
	}
}

add_action( 'wp_before_admin_bar_render', 'fh_admin_bar_objectcache_add' );

function fh_admin_bar_objectcache_render(){
	if ( !is_super_admin() )
		return;

	global $wp_object_cache;

	if ( is_object( $wp_object_cache ) &&
		 property_exists( $wp_object_cache, 'cache_hits' ) &&
		 property_exists( $wp_object_cache, 'cache_misses' ) ) {
		if ( property_exists( $wp_object_cache, 'persistent_cache_reads_hits' ) &&
			 property_exists( $wp_object_cache, 'time_total' ) ) {
			$total = (int) $wp_object_cache->cache_misses + (int) $wp_object_cache->persistent_cache_reads_hits;
			$hit_rate = $total ? 100 / $total * (int) $wp_object_cache->persistent_cache_reads_hits : 0;
			$time = $wp_object_cache->time_total;
			$title = sprintf(
				__( 'Cache hit rate %s%% (%s hits, %s misses) %ss', 'fh-admin-bar-objectcache' ),
				function_exists( 'number_format_i18n' ) ? number_format_i18n( $hit_rate, 1 ) : number_format( $hit_rate, 1 ),
				$wp_object_cache->persistent_cache_reads_hits, $wp_object_cache->cache_misses,
				function_exists( 'number_format_i18n' ) ? number_format_i18n( $time, 3 ) : number_format( $time, 3 )
			);
		}
		else {
			$total = (int) $wp_object_cache->cache_misses + (int) $wp_object_cache->cache_hits;
			$hit_rate = $total ? 100 / $total * (int) $wp_object_cache->cache_hits : 0;
			$title = sprintf(
				__( 'Cache hit rate %s%% (%s hits, %s misses)', 'fh-admin-bar-objectcache' ),
				function_exists( 'number_format_i18n' ) ? number_format_i18n( $hit_rate, 1 ) : number_format( $hit_rate, 1 ),
				$wp_object_cache->cache_hits, $wp_object_cache->cache_misses
			);
		}
		if ( property_exists( $wp_object_cache, 'backend' ) &&
			 property_exists( $wp_object_cache->backend, 'next' ) &&
			 property_exists( $wp_object_cache->backend, 'size' ) &&
			 property_exists( $wp_object_cache->backend, 'partition_size' ) &&
			 property_exists( $wp_object_cache->backend, 'data_offset' ) ) {
			$size = $wp_object_cache->backend->size;
			$used = $wp_object_cache->backend->next - $wp_object_cache->backend->data_offset + $wp_object_cache->backend->partition_size;
			$perc = 100 / $size * $used;
			$utilization = sprintf( __( 'Cache usage: %s%% (%s/%s)', 'fh-admin-bar-objectcache' ),
									function_exists( 'number_format_i18n' ) ? number_format_i18n( $perc, 1 ) : number_format( $perc, 3 ),
									size_format( $used, 2 ), size_format( $size, 2 ) );
		}
		else $utilization = '';
	}
	else $title = __( 'Cache N/A' );
	?>
	<script>
		if (jQuery)
			(function ($) {
				$(function () {
					$('#wp-admin-bar-admin_bar_objectcache > .ab-item').html('<?php echo $title; ?>');
					$('#wp-admin-bar-admin_bar_objectcache_utilization > .ab-item').html('<?php echo $utilization; ?>');
				});
			})(jQuery);
	</script>
	<?php
}

add_action( 'admin_footer', 'fh_admin_bar_objectcache_render', 99999 );
add_action( 'wp_footer', 'fh_admin_bar_objectcache_render', 99999 );
