<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Columns' ) ) {

/**
 * Columns
 *
 * Admin column rendering
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Columns {

	private static $instance = null;
	private $text_domain;

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
	 * Initialize the class and set its properties.
	 */
	public function __construct() {
		
	}

	/**
	 * Set the text domain
	 *
	 * @param      string    $text_domain       The text domain of the plugin.
	 */
	public function set_text_domain( $text_domain ) { 
		$this->text_domain = $text_domain;
	}

	/**
	 * Hide columns
	 *
	 * @param  string $user_login User login name
	 * @param  object $user       User Object
	 *
	 * @hook 	filter_cpd_hide_columns 	Filter columns that get hidden when a user logs in
	 */
	public function hide_columns( $user_login, $user ) {

		$screens	 = 	get_post_types();

		$mkdo_columns = apply_filters(
			'filter_cpd_hide_columns',
			array(
				'comments',
				'tags',
				'wpseo-score',
				'wpseo-title',
				'wpseo-metadesc',
				'wpseo-focuskw',
				'google_last30',
				'twitter_shares',
				'linkedin_shares',
				'facebook_likes',
				'facebook_shares',
				'total_shares',
				'decay_views',
				'decay_shares',
				'seotitle',
				'seodesc',
				'seokeywords',
			)
		);

		foreach( $screens as $screen ) {
		
			$hidden_columns 	= get_user_option( 'manageedit-' . $screen . 'columnshidden',  $user->ID );

			foreach( $mkdo_columns as $column ) 
			{
				if( !in_array( $column, (array) $hidden_columns ) ){

					$hidden_columns[] 		= $column;
				} 
			}

			$hidden_columns[] = array();

			update_user_meta( $user->ID, 'manageedit-' . $screen . 'columnshidden', $hidden_columns );
		}
	}

	/**
	 * Remove column filters
	 */
	public function remove_column_filters() {
		
		global $wpseo_metabox;

		if ( $wpseo_metabox ) {
			remove_action( 'restrict_manage_posts', array( $wpseo_metabox, 'posts_filter_dropdown' ) );
		}
	}

	/**
	 * Add the CPD Role Column
	 * 
	 * @param  array $columns      	Any array of columns
	 * 
	 * @return array $columns      	Any array of columns
	 */
	public function add_column_cpd_role( $columns ) {
		$columns['cpd_role'] = __('CPD Role', $this->text_domain);
		return $columns;
	}

	/**
	 * Manage the CPD Role Column
	 * 
	 * @param  string $value       	The column value
	 * @param  string $column_name 	The Column Name
	 * @param  int $id          	User ID
	 * 
	 * @return string cpd_role of user
	 */
	public function manage_column_cpd_role( $value, $column_name, $id ) {
		if( 'cpd_role' == $column_name ) {
			return get_user_meta( $id, 'cpd_role', TRUE );
		}
	}

	/**
	 * Sort the CPD Role Column
	 * 
	 * @param  array $columns      	Any array of columns
	 * 
	 * @return array $columns      	Any array of columns
	 */
	public function sort_column_cpd_role( $columns )
	{
		$columns['cpd_role'] = 'cpd_role'; 
		return $columns; 
	}

	/**
	 * Sort the CPD Role Column
	 * 
	 * @param  array $views      	Any array of views
	 * 
	 * @return array $views      	Any array of views
	 */
	public function view_count_cpd_role( $views ) {

		global $wpdb;

		$class 					=	'';
		
		/** 
		 * HACK ALERT
		 *
		 * Because the 'filter_column_cpd_role' method filters the same loop that 
		 * gets the supervisors and participants, the count is shown wrong.
		 *
		 * A way around this is to manually query the database (as implemented below).
		 * There may be other ways, but this is working.
		 *
		 * TODO: Find a better way to resolve this hack!
		 */
		
		// $all_supervisors 		=	CPD_Users::get_supervisors();
		// $all_participants 		=	CPD_Users::get_participants();

		$all_supervisors 		=	array();
		$all_participants 		=	array();
		$mu_users 				= 	$wpdb->get_results( "SELECT ID, user_nicename FROM $wpdb->users" );

		foreach( $mu_users as $mu_user ) {

			$mu_cpd_role 			= 	get_user_meta( $mu_user->ID, 'cpd_role', TRUE );

			if( $mu_cpd_role == 'participant' ) {
				$all_participants[] = $mu_user;
			}

			if( $mu_cpd_role == 'supervisor' ) {
				$all_supervisors[] = $mu_user;
			}
		}

		/** 
		 * END HACK ALERT
		 */

		$num_supervisors		=	count( $all_supervisors );
		$num_participants		=	count( $all_participants );

		if( isset( $_GET['cpd_role'] ) && $_GET['cpd_role'] == 'supervisor' ) {
			$class 					= 	'class="current"';
		}
		$views['supervisors'] 	= 	"<a href='" . network_admin_url('users.php?cpd_role=supervisor') . "'$class>" . sprintf( _n( 'Supervisors <span class="count">(%s)</span>', 'Supervisors <span class="count">(%s)</span>', $num_supervisors ), number_format_i18n( $num_supervisors ) ) . '</a>';
		$class 					=	'';

		if( isset( $_GET['cpd_role'] ) && $_GET['cpd_role'] == 'participant' ) {
			$class 					= 	'class="current"';
		}
		$views['pariticpants'] 	= 	"<a href='" . network_admin_url('users.php?cpd_role=participant') . "'$class>" . sprintf( _n( 'Participants <span class="count">(%s)</span>', 'Participants <span class="count">(%s)</span>', $num_participants ), number_format_i18n( $num_participants ) ) . '</a>';
		$class 					=	'';

		return $views;
	}

	/**
	 * Filter the CPD Role Column
	 * 
	 * @param  object $query      	The sort query
	 */
	public function filter_column_cpd_role( $query )
	{
		global $wpdb;
		$vars = $query->query_vars;

		if( isset( $_GET['cpd_role'] ) ){
			$query->query_from .= " LEFT JOIN ".$wpdb->prefix."usermeta n ON (".$wpdb->prefix."users.ID = n.user_id  AND n.meta_key = 'cpd_role')"; 				
			$query->query_where.=$wpdb->prepare(" AND n.meta_value =%s", $_GET['cpd_role']);
		} else if( $vars['orderby'] === 'cpd_role' ) {
			$query->query_from .= " LEFT JOIN ".$wpdb->prefix."usermeta m ON (".$wpdb->prefix."users.ID = m.user_id  AND m.meta_key = '{$vars['orderby']}')"; 
			$query->query_orderby = "ORDER BY m.meta_value ".$vars['order'];
		} 
	}

	/**
	 * Add the Assessment Status Column
	 * 
	 * @param  array $columns      	Any array of columns
	 * 
	 * @return array $columns      	Any array of columns
	 */
	public function add_column_assessment_status( $columns ) {
		$columns['assessment_status'] = __( 'Status', $this->text_domain );
		return $columns;
	}

	/**
	 * Manage the Assessment Status Column
	 * 
	 * @param  string $column_name 	The Column Name
	 * @param  int $id          	Post ID
	 * 
	 * @return string assessment_status of user
	 */
	public function manage_column_assessment_status( $column_name, $id ) {
		if( 'assessment_status' == $column_name ) {
			$submitted = get_post_meta( $id, '_cpd_submit', TRUE );
			$complete  = get_post_meta( $id, '_cpd_complete', TRUE );
			if( !$submitted && !$complete ) {
				echo 'In progress';
			} else if( $submitted && !$complete ) {
				echo 'Submited';
			} else if( $complete ) {
				echo 'Completed';
			}
		}
	}
}
}