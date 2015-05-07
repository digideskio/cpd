<?php

/**
 * CPD Legacy Upgrade
 *
 * Upgrades the legacy CPD system to use the functionality in this plugin.
 * 
 * @since             2.0.0
 * @package           CPD
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Upgrade_Legacy' ) ) {

/**
 * CPD_Upgrade_Legacy
 *
 * The container for all legacy upgrade functions
 *
 * @since             	2.0.0
 */
class CPD_Upgrade_Legacy {

	private static $instance = null;

    /**
	 * Creates or returns an instance of this class.
	 */
	public static function get_instance() {
		/**
		 * If an instance hasn't been created and set to $instance create an instance  
		 * and set it to $instance.
		 */
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Load the dependencies
	 *
	 */
	private function __construct() {

		$this->load_dependencies();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * This dependancy loader lets you group dependancies into logical load order
	 * groupings. This allows easy reading of what loads in what order.
	 *
	 * It also allows easier and documented reading of what each dependancie is.
	 *
	 * @access   private
	 */
	private function load_dependencies() {

		// Order of dependancy load
		$dependencies 				= 	array( 
			'vendor', 						// Any third party plugins or libraries
			'includes',						// Functions common to admin and public
			'admin', 						// Admin functions
			'public',						// Public functions
		);

		// Load dependancies
		foreach( $dependencies as $order => $dependancy ) {
			if( is_array( $dependancy ) ) {
				foreach( $dependancy as $path ) {
					require_once $this->plugin_path  . $order . '/' . $path . '.php';
				}
			}
		}
	}

	/**
	 * Upgrade Relationships
	 *
	 * Upgrades the old CPD database relationship table to use membership information 
	 * instead.
	 */
	public function upgrade_relationships() {
		
		global $wpdb;

		$relationship_table = $wpdb->base_prefix . 'cpd_relationships';
		
		// If table exists (old system has been installed)
		if( $wpdb->get_var("SHOW TABLES LIKE '$relationship_table'") == $relationship_table && get_site_option( 'cpd_upgraded_from_cpd_journals', FALSE ) === FALSE ) {
			$results 	= 	$wpdb->get_results( 
									"SELECT * FROM {$relationship_table}"
							);
			if( is_array( $results ) && !empty( $results ) ) {
				foreach( $results as $row ) {
					
					// Add participant to supervisor
					$participants 			= 	get_user_meta( $row->supervisor_id, 'cpd_related_participants', TRUE );
					if( !is_array( $participants ) ) {
						$participants = array();
					}
					if( !in_array( $row->participant_id, $participants ) ) {
						$participants[] = $row->participant_id;
					}
					update_user_meta( $row->supervisor_id, 'cpd_related_participants', $participants );

					// Add supervisor to participant
					$supervisors 			= 	get_user_meta( $row->participant_id, 'cpd_related_supervisors', TRUE );
					if( !is_array( $supervisors ) ) {
						$supervisors = array();
					}
					if( !in_array( $row->supervisor_id, $supervisors ) ) {
						$supervisors[] = $row->supervisor_id;
					}
					update_user_meta( $row->participant_id, 'cpd_related_participants', $supervisors );
				}
			}

			add_site_option( 'cpd_upgraded_from_cpd_journals', TRUE );
		}
	}
}
}