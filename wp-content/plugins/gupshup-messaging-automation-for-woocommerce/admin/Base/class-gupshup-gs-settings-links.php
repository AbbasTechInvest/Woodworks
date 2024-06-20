<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Base/class-gupshup-gs-base-controller.php';

class GupshupGSSettingsLinks extends GupshupGSBaseController {

	public function register() { 
		add_filter( "plugin_action_links_$this->plugin", array( $this, 'settings_link' ) );
	}

	/**
	 * Showing setting link on wordpress installed plugin list
	 */
	public function settings_link( $links ) { 
		$settings_link = '<a href="admin.php?page=gupshup_gs_plugin_setting">Settings</a>';
		array_push( $links, $settings_link );
		return $links;
	}
}
