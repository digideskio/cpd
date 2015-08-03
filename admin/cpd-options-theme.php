<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Options_Theme' ) ) {

/**
 * Copy Assignments
 *
 * Functionality to copy assignments
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Options_Theme {


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

	public function init_options_page() {
		
		/* Register Settings */
		register_setting( 'cpd_settings_theme_group', 'cpd_theme_parent' );
		register_setting( 'cpd_settings_theme_group', 'cpd_theme' );

		/* Set defaults */
		$cpd_theme_parent = get_option( 'cpd_theme_parent', NULL );
		$cpd_theme = get_option( 'cpd_theme', NULL );
		
		if( $cpd_theme_parent == NULL ) {
			
			add_option( 'cpd_theme_parent', 'twentyfifteen' );

			$cpd_theme_parent = get_option( 'cpd_theme_parent' );
		}

		if( $cpd_theme == NULL ) {
			
			add_option( 'cpd_theme', 'cpd-theme' );

			$cpd_theme = get_option( 'cpd-theme' );
		}
		
		/* Add sections */
		add_settings_section( 'cpd_theme_defaults', __('Theme defaults', $this->text_domain), array( $this, 'cpd_theme_defaults_callback' ), 'cpd_settings_theme' );
	
    	/* Add fields to a section */
		add_settings_field( 'cpd_theme_parent', __('Parent Theme', $this->text_domain), array( $this, 'cpd_theme_parent_callback' ), 'cpd_settings_theme', 'cpd_theme_defaults' );
		add_settings_field( 'cpd_theme', __('Theme', $this->text_domain), array( $this, 'cpd_theme_callback' ), 'cpd_settings_theme', 'cpd_theme_defaults' );
	}

	/**
	 * Show the section message
	 */
	public function cpd_theme_defaults_callback() {
		?>
		<p>
			<?php _e('Add the default theme settings for the CPD system.', $this->text_domain);?>
		</p>
		<?php
	}

	/**
	 * Render the field
	 */
	public function cpd_theme_parent_callback() {
		$cpd_theme_parent 	= 	get_option( 'cpd_theme_parent' );
		?>
		<input type="text" name="cpd_theme_parent" id="cpd_theme_parent" value="<?php echo $cpd_theme_parent; ?>"/>
		<?php
	}

	/**
	 * Render the field
	 */
	public function cpd_theme_callback() {
		$cpd_theme 	= 	get_option( 'cpd_theme' );
		?>
		<input type="text" name="cpd_theme" id="cpd_theme" value="<?php echo $cpd_theme; ?>"/>
		<?php
	}

	/**
	 * Add the options page
	 */
	public function add_options_page() {
		add_submenu_page( 'settings.php', __('CPD Theme Settings', $this->text_domain), __('CPD Theme Settings', $this->text_domain), 'manage_options', 'cpd_settings_theme', array( $this, 'render_options_page' ) );
	}

	/**
	 * Render the options page
	 */
	public function render_options_page(){ 
		?>
		<div class="wrap cpd-settings cpd-settings-theme">  
			<h2><?php _e('Theme Settings', $this->text_domain);?></h2> 
			<form action="/wp-admin/options.php" method="POST">
	            <?php settings_fields( 'cpd_settings_theme_group' ); ?>
	            <?php do_settings_sections( 'cpd_settings_theme' ); ?>
	            <?php submit_button(); ?>
	        </form>
		</div> 
	<?php
	}
}
}