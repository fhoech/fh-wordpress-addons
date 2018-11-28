<?php

class FH_QM_Output_ObjectCache_Stats extends QM_Output_Html {

	/**
	 * @param QM_Collector $collector
	 */
	public function __construct( QM_Collector $collector ) {
		parent::__construct( $collector );
		add_filter( 'qm/output/menus', array( $this, 'admin_menu' ), 101 );
		add_filter( 'qm/output/menu_class', array( $this, 'admin_class' ) );
	}

	/**
	 * Echoes the Query Manager compatible output
	 * @return void
	 */
	public function output() {
		global $wp_object_cache;
		echo '<div id="' . esc_attr( $this->collector->id() ) . '" class="qm qm-non-tabular qm-panel-show" role="group" aria-labelledby="qm-fh-objectcache-caption" tabindex="-1">';
		echo '<div class="qm-boxed">';
		echo '<h2 id="qm-fh-objectcache-caption" class="qm-screen-reader-text">Cache</h2>';
		echo '<div class="qm-section">';
		echo '<h3>Object Cache</h3>';
		if ( method_exists( $wp_object_cache, 'stats' ) ) {
			$wp_object_cache->stats();
		}
		else echo '<p><span class="qm-info">Object cache information is not available</span></p>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Add Object Cache class
	 *
	 * @param array $classes Array of QM classes
	 *
	 * @return array
	 */
	public function admin_class( array $class ) {
		$class[] = 'qm-fh-objectcache-stats';
		return $class;
	}

	/**
	 * Adds Object Cache stats item to Query Monitor Menu
	 *
	 * @param array $menu Array of QM admin menu items
	 *
	 * @return array
	 */
	public function admin_menu( array $menu ) {
		$menu[] = $this->menu( array(
			'id'    => 'qm-fh-objectcache-stats',
			'href'  => '#qm-fh-objectcache-stats',
			'title' => esc_html__( 'Cache', 'query-monitor' )
		) );
		return $menu;
	}

}
