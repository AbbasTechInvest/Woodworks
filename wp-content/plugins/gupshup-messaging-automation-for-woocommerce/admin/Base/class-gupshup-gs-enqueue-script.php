<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Base/class-gupshup-gs-base-controller.php';

class GupshupGSEnqueue extends GupshupGSBaseController {

	public function register() {
		add_action( 'admin_enqueue_scripts', array( $this, 'gupshupEnqueue' ) );
	}
	
	public function gupshupEnqueue() {
		// enqueue all our scripts
		wp_enqueue_script( 'gupshup-chart-js', $this->plugin_url . 'admin/assets/gupshup-chartjs.min.js', array(), '1.0.0' );
		wp_enqueue_script( 'gupshup-gs-dashoard-analytics', $this->plugin_url . 'admin/assets/gupshup-gs-dashoard-analytics.js', array( 'jquery' ), '1.0.0', true);
		wp_enqueue_style( 'gupshup-gs', $this->plugin_url . 'admin/assets/gupshup-gs.css', array(), '1.0.0');
		wp_enqueue_script( 'gupshup-gs', $this->plugin_url . 'admin/assets/gupshup-gs.js', array(), '1.0.0' );
		wp_enqueue_script( 'gupshup-workflow-jquey', $this->plugin_url . 'admin/assets/gupshup-workflow-jquey.js', array( 'jquery' ), '1.0.0' , true);
		wp_enqueue_script( 'activate-pluging-jquery', plugin_dir_url( GUPSHUP_GS_PLUGIN_FILE ) . 'admin/assets/activate-pluging-jquery.js', array( 'jquery' ), '1.0.0', false);
		wp_localize_script( 'activate-pluging-jquery', 'gupshup_gs_activation_vars', array('guphsup_nonce_action'=> wp_create_nonce( 'gupshup-nonce-action') ));
	}
	

}
