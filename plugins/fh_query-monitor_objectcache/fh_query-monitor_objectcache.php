<?php
/**
 * Plugin Name: Query Monitor: Object Cache Stats
 * Description: Shows Object Cache stats in Query Monitor
 * Version: $Id$
 * Plugin URI: https://github.com/fhoech/fh-wordpress-addons/plugins/blob/master/fh_query-monitor_objectcache.php
 * Author: Florian Höch
 * License: GPL3
 */

class FH_QM_ObjectCache_Stats {

	public function __construct() {
		if ( class_exists( 'QM_Collectors' ) ) {
			$this->register_collector();
			add_filter( 'qm/outputter/html', array( $this, 'register_output' ), 101, 1 );
		}
	}

	/*
	 * Register collector
	 *
	 * @return void
	 */
	private function register_collector() {
		require_once( 'classes/QM_Collector_ObjectCache_Stats.php' );
		QM_Collectors::add( new FH_QM_Collector_ObjectCache_Stats() );
	}

	/*
	 * Register output
	 *
	 * @param array $output
	 *
	 * @return array
	 */
	public function register_output( $output ) {
		require_once( 'classes/QM_Output_ObjectCache_Stats.php' );
		if ( $collector = QM_Collectors::get( 'fh-objectcache-stats' ) ) {
			$output['fh-objectcache-stats'] = new FH_QM_Output_ObjectCache_Stats( $collector );
		}
		return $output;
	}

}

add_action( 'plugins_loaded', function () {
	new FH_QM_ObjectCache_Stats();
}, 10, 0 );
