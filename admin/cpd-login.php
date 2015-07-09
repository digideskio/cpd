<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'CPD_Login' ) ) {

	/**
	 * Admin
	 *
	 * Defines the admin settings
	 *
	 * @package    CPD
	 * @subpackage CPD/admin
	 * @author     Make Do <hello@makedo.in>
	 */
	class CPD_Login {

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
		 * @param string  $text_domain The text domain of the plugin.
		 */
		public function set_text_domain( $text_domain ) {
			$this->text_domain = $text_domain;
		}

		public function add_login_logo() {

			$custom_logo_location = plugin_dir_url( __FILE__ ) . '../templates/img/login-logo.png';

			?>
			    <style type="text/css">
			        .login h1 a {
			            background-image: url(<?php echo $custom_logo_location; ?>);
			            padding-bottom: 30px;
			            width: 150px;
			            height: 180px;
			            background-size: 150px;
			        }
			    </style>
			<?php
		}

	}
}
