<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Base/class-gupshup-gs-base-controller.php';

/**
 * Callback methods of app sub-menus
 */
class GupshupGSAdminCallbacks extends GupshupGSBaseController {

	/**
	 * Callback method of Setting Page
	 *
	 * @return void
	 */
	public function gupshupGSSetting() {
		return require_once( "$this->plugin_path/templates/template-gupshup-gs-setting.php" );
	}

	/**
	 * Callback method of Workflow Page
	 *
	 * @return void
	 */
	public function gupshupGSWorkflow() {
		return require_once( "$this->plugin_path/templates/template-gupshup-gs-workflow-admin.php" );
	}

	/**
	 * Callback method of Dashboard Page
	 *
	 * @return void
	 */
	public function gupshupGSDashboard() {
		return require_once( "$this->plugin_path/templates/template-gupshup-gs-dashboard.php" );
	}

	
	
}
