<?php
/**
 * The content blocks
 *
 * @link       http://makedo.in
 * @since      2.0.0
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Dashboard_Widget_User_Posts' ) ) {

/**
 * The content blocks
 *
 * Changes the default functionality of the admin bar
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Dashboard_Widget_User_Posts {

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
	 * @var      string    $instance       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct() {
		
	}

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

	/**
	 * Add the dashboard widget
	 *
	 * @since    2.0.0
	 */
	public function add_dashboard_widget() {
		
		$posts_by_participant_title  	= 'All posts by user';

		$current_user 					= wp_get_current_user();
		$roles 							= $current_user->roles;

		if( in_array( 'supervisor', $roles ) ) {
			$posts_by_participant_title  	= 'All your participants posts';
		}

		if( in_array( 'supervisor', $roles ) || is_super_admin( $current_user->ID ) ) {
			wp_add_dashboard_widget(
				'cpd_dashboard_widget_user_posts',
				'<span class="cpd-dashboard-widget-title dashicons-before dashicons-chart-bar"></span> ' . $posts_by_participant_title,
				array( $this, 'render_dashboard_widget' ),
				array( $this, 'config_dashboard_widget')
			);
		}
	}

	/**
	 * Render the dashboard widget
	 *
	 * @since    2.0.0
	 */
	public function render_dashboard_widget(){
		
		$template_name 						= 	'cpd-dashboard-widget-user-posts';
		$template_path 						= 	CPD_Templates::get_template_path( $template_name );

		if( $template_path !== FALSE ) {
			include $template_path;
		}
	}

	/**
	 * Options for the dashboard widget
	 *
	 * @since    2.0.0
	 */
	public function config_dashboard_widget() {

		$count 	= 	intval( get_option( 'posts_by_participants_barchart_widget_count' ) );
		$order 	= 	get_option( 'posts_by_participants_barchart_widget_order' ) == 'asc' ? 'asc' : 'desc';
		
		if( empty( $count ) ) {
			$count = 0;
		}

		if( !isset( $_POST['posts_by_participants_barchart_widget_count'] ) ) {
			?>
			<input type="hidden" name="posts_by_participants_barchart_widget_count" value="1">
			<label for="count">Ammount of participants to show</label>
			<select name="count" id="count">
				<option <?php echo $count == 0 		? 'selected' : '';?> value="0">All</option>
				<option <?php echo $count == 10 	? 'selected' : '';?> value="10">10</option>
				<option <?php echo $count == 20 	? 'selected' : '';?> value="20">20</option>
				<option <?php echo $count == 30 	? 'selected' : '';?> value="30">30</option>
			</select>

			<label for="order">Order by</label>
			<select name="order" id="order">
				<option <?php echo $order == 'desc' 	? 'selected' : '';?> value="desc">Most posts</option>
				<option <?php echo $order == 'asc' 		? 'selected' : '';?> value="asc">Least posts</option>
			</select>
			<?php
		} 
		else {
			update_option( 'posts_by_participants_barchart_widget_count', $_POST['count'] );
			update_option( 'posts_by_participants_barchart_widget_order', $_POST['order'] == 'desc' ? 'desc' : 'asc' );
		}
	}
}
}