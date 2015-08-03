<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Dashboard_Widget_User_Development_Categories' ) ) {

/**
 * User Posts Dashboard Widget
 *
 * Changes the default functionality of the admin bar
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Dashboard_Widget_User_Development_Categories {

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
	 * @param      string    $instance       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
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
	 * Add the dashboard widget
	 */
	public function add_dashboard_widget() {
		
		$categories_by_participant_title  	= __('All development categories by user', $this->text_domain );

		$current_user 					= wp_get_current_user();
		$roles 							= $current_user->roles;

		if( in_array( 'supervisor', $roles ) ) {
			$categories_by_participant_title  	= __('All your participants development categories', $this->text_domain );
		}

		if( in_array( 'supervisor', $roles ) || is_super_admin( $current_user->ID ) ) {
			wp_add_dashboard_widget(
				'cpd_dashboard_widget_user_development_categories',
				'<span class="cpd-dashboard-widget-title dashicons-before dashicons-tag"></span> ' . $categories_by_participant_title,
				array( $this, 'render_dashboard_widget' ),
				array( $this, 'config_dashboard_widget')
			);
		}
	}

	/**
	 * Render the dashboard widget
	 */
	public function render_dashboard_widget(){
		
		$template_name 						= 	'cpd-dashboard-widget-user-development-categories';
		$template_path 						= 	CPD_Templates::get_template_path( $template_name );

		if( $template_path !== FALSE ) {
			include $template_path;
		}
	}

	/**
	 * Options for the dashboard widget
	 */
	public function config_dashboard_widget() {

		$count 	= 	intval( get_option( 'development_categories_by_participants_barchart_widget_count' ) );
		$order 	= 	get_option( 'development_categories_by_participants_barchart_widget_order' ) == 'asc' ? 'asc' : 'desc';
		
		if( empty( $count ) ) {
			$count = 0;
		}

		if( !isset( $_POST['development_categories_by_participants_barchart_widget_count'] ) ) {
			?>
			<input type="hidden" name="categories_by_participants_barchart_widget_count" value="1">
			<label for="count"><?php _e('Ammount of categories to show', $this->text_domain );?></label>
			<select name="count" id="count">
				<option <?php echo $count == 0 		? 'selected' : '';?> value="0"><?php _e('All', $this->text_domain );?></option>
				<option <?php echo $count == 10 	? 'selected' : '';?> value="10"><?php _e('10', $this->text_domain );?></option>
				<option <?php echo $count == 20 	? 'selected' : '';?> value="20"><?php _e('20', $this->text_domain );?></option>
				<option <?php echo $count == 30 	? 'selected' : '';?> value="30"><?php _e('30', $this->text_domain );?></option>
			</select>
			<br/>
			<label for="order"><?php _e('Order by', $this->text_domain );?></label>
			<select name="order" id="order">
				<option <?php echo $order == 'desc' 	? 'selected' : '';?> value="desc"><?php _e('Most categories', $this->text_domain );?></option>
				<option <?php echo $order == 'asc' 		? 'selected' : '';?> value="asc"><?php _e('Least categories', $this->text_domain );?></option>
			</select>
			<br/><br/>
			<?php
		} 
		else {
			update_option( 'development_categories_by_participants_barchart_widget_count', $_POST['count'] );
			update_option( 'development_categories_by_participants_barchart_widget_order', $_POST['order'] == 'desc' ? 'desc' : 'asc' );
		}
	}
}
}