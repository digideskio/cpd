<?php
/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://makedo.in
 * @since      1.0.0
 *
 * @package    CPD
 * @subpackage CPD/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Journal_Columns{

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $instance       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct() {
		
	}

	/* user admin table stuff */ 
	public function add_cpd_role_column($columns) {
		$columns['cpd_role'] = 'CPD Role';
		return $columns;
	}

	public function cpd_role_column($value, $column_name, $id) {
		if($column_name=='cpd_role') {
			return get_user_meta($id,'cpd_role', true);
		}
	}

	public function add_cpd_role_column_sort($columns)
	{
		$columns['cpd_role'] = 'cpd_role'; 
		return $columns; 
	}

	public function filter_and_order_by_cpd_column($query)
	{
		global $wpdb; 
		$vars = $query->query_vars;
		if(isset($_GET['cpd_role']) ){
			$query->query_from .= " LEFT JOIN ".$wpdb->prefix."usermeta n ON (".$wpdb->prefix."users.ID = n.user_id  AND n.meta_key = 'cpd_role')"; 				
			$query->query_where.=$wpdb->prepare(" AND n.meta_value =%s", $_GET['cpd_role']);
		} elseif($vars['orderby'] === 'cpd_role') {
			$query->query_from .= " LEFT JOIN ".$wpdb->prefix."usermeta m ON (".$wpdb->prefix."users.ID = m.user_id  AND m.meta_key = '{$vars['orderby']}')"; 
			$query->query_orderby = "ORDER BY m.meta_value ".$vars['order'];
		} 

	}

	public function add_cpd_role_views($views) {
		global $wpdb; 
		$all_supervisors 	=	array();
		$all_participants 	=	array();
		$mu_users 			= 	$wpdb->get_results( "SELECT ID, user_nicename FROM $wpdb->users" );
		$class 				= 	'';
		foreach( $mu_users as $mu_user ) {

			$mu_cpd_role 			= 	get_user_meta( $mu_user->ID, 'cpd_role', TRUE );

			if( $mu_cpd_role == 'participant' ) {
				$all_participants[] = $mu_user;
			}

			if( $mu_cpd_role == 'supervisor' ) {
				$all_supervisors[] = $mu_user;
			}
		}
		$num_supervisors=count($all_supervisors);
		$num_participants=count($all_participants);
		$views['supervisors'] = "<a href='" . network_admin_url('users.php?cpd_role=supervisor') . "'$class>" . sprintf( _n( 'Supervisors <span class="count">(%s)</span>', 'Supervisors <span class="count">(%s)</span>', $num_supervisors ), number_format_i18n( $num_supervisors ) ) . '</a>';
		$views['pariticpants'] = "<a href='" . network_admin_url('users.php?cpd_role=participant') . "'$class>" . sprintf( _n( 'Participants <span class="count">(%s)</span>', 'Participants <span class="count">(%s)</span>', $num_participants ), number_format_i18n( $num_participants ) ) . '</a>';
		return $views;
	}
}