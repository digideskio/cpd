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
	 * The HTML of the field
	 */
	public function html() { 

		$defaults = array();

		echo $this->value;
		
	}
}
}