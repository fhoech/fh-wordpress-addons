<?php

class FH_QM_Collector_ObjectCache_Stats extends QM_Collector {

	public $id = 'fh-objectcache-stats';

	/**
	 * @return string
	 */
	public function name() {
		return esc_html__( 'Object Cache', 'query-monitor' );
	}

	/**
	 * @return void
	 */
	public function process() {
	}

}
