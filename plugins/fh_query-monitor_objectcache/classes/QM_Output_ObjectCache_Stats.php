<?php

class FH_QM_Output_ObjectCache_Stats extends QM_Output_Html {

	/**
	 * @param QM_Collector $collector
	 */
	public function __construct( QM_Collector $collector ) {
		parent::__construct( $collector );
		add_filter( 'qm/output/menus', array( $this, 'admin_menu' ), 101 );
		add_filter( 'qm/output/title', array( $this, 'admin_title' ), 101 );
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
		if ( function_exists('opcache_get_configuration') &&
			   function_exists('opcache_get_status') ) {
			echo '<div class="qm-boxed">';
			foreach ( array( 'Configuration' => opcache_get_configuration(),
							 'Status' => opcache_get_status( false ) ) as $title => $entries ) {
				echo '<div class="qm-section">';
				echo "<h3>OPcache $title</h3>";
				echo '<table><tbody>';
				foreach ( $entries as $key_1 => $val_1 ) {
					if ( is_scalar( $val_1 ) )
						echo "<tr><th>$key_1</th><td>" . trim( var_export( $val_1, true ), "'" ) . '</td></tr>';
					else {
						if ( $key_1 !== 'directives' ) {
							switch ( $key_1 ) {
								case 'memory_usage':
									$key_title = 'Memory Usage';
									break;
								case 'interned_strings_usage':
									$key_title = 'Interned Strings Usage';
									break;
								case 'opcache_statistics':
									$key_title = 'Statistics';
									break;
								default:
									$key_title = ucfirst( $key_1 );
							}
							echo '</table></tbody>';
							echo "<h4>$key_title</h4>";
							echo '<table><tbody>';
						}
						foreach ( $val_1 as $key_2 => $val_2 ) {
							echo "<tr><th>$key_2</th><td>" . trim( var_export( $val_2, true ), "'" ) . '</td></tr>';
						}
					}
				}
				echo '</table></tbody>';
				echo '</div>';
			}
			echo '</div>';
		}
		echo '</div>';
	}

	/**
	 * Adds QM Object Cache stats to admin panel
	 *
	 * @param array $title Array of QM admin panel titles
	 *
	 * @return array
	 */
	public function admin_title( array $title ) {
		global $wp_object_cache;
		if ( is_object( $wp_object_cache ) &&
			 property_exists( $wp_object_cache, 'cache_hits' ) &&
			 property_exists( $wp_object_cache, 'cache_misses' ) ) {
			$total = (int) $wp_object_cache->cache_misses + (int) $wp_object_cache->cache_hits;
			$title[] = sprintf(
				esc_html__( 'Object Cache %s%% hit rate', 'query-monitor' ),
				number_format( 100 / $total * (int) $wp_object_cache->cache_hits, 1 )
			);
		}
		return $title;
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
