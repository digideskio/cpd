<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Dashboard_Widget_Latest_Posts' ) ) {

/**
 * Latest Posts Dashboard Widget
 *
 * Show latest posts in the dashboard
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Dashboard_Widget_Latest_Posts {

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
		
		$latest_posts_title  			= 'All posts by week';

		$current_user 					= wp_get_current_user();
		$roles 							= $current_user->roles;

		if( in_array( 'supervisor', $roles ) ) {
			$latest_posts_title  			= 'Your participants posts by week';
		}
		else if( in_array( 'participant', $roles ) ) {
			$latest_posts_title  			= 'Your posts by week';
		}

		wp_add_dashboard_widget(
			'cpd_dashboard_widget_latest_posts',
			'<span class="cpd-dashboard-widget-title dashicons-before dashicons-chart-bar"></span> ' . $latest_posts_title,
			array( $this, 'render_dashboard_widget' ),
			array( $this, 'config_dashboard_widget')
		);
	}

	/**
	 * Render the dashboard widget
	 */
	public function render_dashboard_widget(){
		
		$template_name 						= 	'cpd-dashboard-widget-latest-posts';
		$template_path 						= 	CPD_Templates::get_template_path( $template_name );

		if( $template_path !== FALSE ) {
			include $template_path;
		}
	}

	/**
	 * Options for the dashboard widget
	 */
	public function config_dashboard_widget() {

		$weeks 	= 	intval( get_option( 'latest_posts_histogram_widget_weeks' ) );
		
		if( empty( $weeks ) ) {
			$weeks = 4;
		}

		if( !isset( $_POST['latest_posts_histogram_widget_config'] ) ) {
			?>
			<input type="hidden" name="latest_posts_histogram_widget_config" value="1">
			<label for="weeks">Ammount of weeks shown</label>
			<select name="weeks" id="weeks">
				<option <?php echo $weeks == 4 	? 'selected' : '';?> value="4">4</option>
				<option <?php echo $weeks == 8 	? 'selected' : '';?> value="8">8</option>
				<option <?php echo $weeks == 12 ? 'selected' : '';?> value="12">12</option>
				<option <?php echo $weeks == 24 ? 'selected' : '';?> value="24">24</option>
			</select>
			<?php
		} 
		else {
			update_option( 'latest_posts_histogram_widget_weeks', $_POST['weeks'] );
		}
	}
}
}