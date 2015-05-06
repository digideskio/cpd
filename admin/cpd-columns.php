<?php
/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://makedo.in
 * @since      2.0.0
 *
 * @package    CPD
 * @subpackage CPD/admin
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Columns' ) ) {

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the admin settings
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
	 *
	 * @since    2.0.0
	 */
	public function __construct() {
		
	}

	/**
	 * Hide columns
	 *
	 * @param  string $user_login User login name
	 * @param  object $user       User Object
	 *
	 * @hook 	filter_cpd_hide_columns 	Filter columns that get hidden when a user logs in
	 * 
	 * @since    2.0.0
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
	 * 
	 * @since    2.0.0
	 */
	public function remove_column_filters() {
		
		global $wpseo_metabox;

		if ( $wpseo_metabox ) {
			remove_action( 'restrict_manage_posts', array( $wpseo_metabox, 'posts_filter_dropdown' ) );
		}
	}

	/** TODO: OLD CODE NEEDS REFACTORING */

	/**
	 * Initialize the class and set its properties.
	 *
	 * @var      string    $text_domain       The text domain of the plugin.
	 *
	 * @since    2.0.0
	 **/
	public function set_text_domain( $text_domain ) { 
		$this->text_domain = $text_domain;
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
}