<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_CMB_Plugin_Render' ) ) {

/**
 * Admin Scripts
 *
 * Load Admin Scripts
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_CMB_Plugin_Render extends CMB_Field {

	/**
	 * Return the default args for the WYSIWYG field.
	 *
	 * @return array $args
	 */
	public function get_default_args() {
		return array_merge(
			parent::get_default_args(),
			array(
				'options' => array(),
			)
		);
	}

	function enqueue_scripts() {

		parent::enqueue_scripts();

		wp_enqueue_script( 'cmb-wysiwyg', trailingslashit( CMB_URL ) . 'js/field-wysiwyg.js', array( 'jquery', 'cmb-scripts' ) );
	}

	public function html() {

		$id   = $this->get_the_id_attr();
		$name = $this->get_the_name_attr();

		$field_id = $this->get_js_id();

		printf( '<div class="cmb-wysiwyg screen-reader-text" data-id="%s" data-name="%s" data-field-id="%s">', $id, $name, $field_id );

		if ( $this->is_placeholder() ) 	{

			// For placeholder, output the markup for the editor in a JS var.
			ob_start();
			$this->args['options']['textarea_name'] = 'cmb-placeholder-name-' . $field_id;
			wp_editor( '', 'cmb-placeholder-id-' . $field_id, $this->args['options'] );
			$editor = ob_get_clean();
			$editor = str_replace( array( "\n", "\r" ), "", $editor );
			$editor = str_replace( array( "'" ), '"', $editor );

			?>

			<script>
				if ( 'undefined' === typeof( cmb_wysiwyg_editors ) )
					var cmb_wysiwyg_editors = {};
				cmb_wysiwyg_editors.<?php echo $field_id; ?> = '<?php echo $editor; ?>';
			</script>

			<?php

		} else {

			$this->args['options']['textarea_name'] = $name;
			echo wp_editor( $this->get_value(), $id, $this->args['options'] );

		}

		echo '</div>';
		echo wpautop( $this->value );

	}
	/**
	 * Check if this is a placeholder field.
	 * Either the field itself, or because it is part of a repeatable group.
	 *
	 * @return bool
	 */
	public function is_placeholder() {

		if ( isset( $this->parent ) && ! is_int( $this->parent->field_index ) )
			return true;

		else return ! is_int( $this->field_index );

	}
}
}