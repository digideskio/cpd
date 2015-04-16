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
class CPD_Journal_Options{

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $instance       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct() {
		
	}

	public function add_options_page() {
		add_submenu_page( 'index.php', 'CPD settings', 'CPD settings', 'manage_network_options', 'cpd_settings', array( $this, 'render_options_page' ) );
	}

	public function render_options_page(){ 
		?>
		<div class="wrap">  
			<h2>CPD Settings</h2>  
			<form method="post" action="edit.php?action=update_cpd_settings">  
				<table class="form-table">
					<tr valign="top">
					<th scope="row">
						<label for="cpd_new_blog_options">New participant blog default options</label>
					</th>
					<td>
						<textarea name="cpd_new_blog_options" id="cpd_new_blog_options" cols="40" rows="8"><?php echo get_option('cpd_new_blog_options'); ?></textarea>
						<br />
						<br />
						These options are used to set up a new blog for a CPD participant. For example, they can be used to set a default theme.
						<ul>
							<li>Enter any valid blog meta name/value pairs.</li>
							<li>Enter one pair per line separated by any whitespace characters.</li>
						</ul>
			        </td>
			        </tr>
			    </table>
			    <?php wp_nonce_field('update_cpd_settings') ?>  
			    <input type="hidden" name="action" value="update" />  
				<input type="hidden" name="page_options" value="cpd_new_blog_options" />  
			    <?php submit_button(); ?>
			</form>  
		</div> 
	<?php
	}

	function update_options_page(){

		check_admin_referer( 'update_cpd_settings' );

		if(!current_user_can('manage_network_options')) 
		{
			wp_die( 'You do not have permission to do this' );
		}

		update_option( 'cpd_new_blog_options', $_POST['cpd_new_blog_options'] );

		$cpd_settings 	= 	get_option( 'cpd_new_blog_options' );
		$cpd_settings 	= 	preg_replace( '/[\n\r]+/', '&', $cpd_settings );
		$cpd_settings 	= 	preg_replace( '/[\s\:]+/', '=', $cpd_settings );
		parse_str( $cpd_settings, $update_options );

		wp_redirect( add_query_arg( array( 'page' => 'cpd_settings', 'updated' => 'true' ), network_admin_url( 'index.php' ) ) );
		exit;  
	}
}