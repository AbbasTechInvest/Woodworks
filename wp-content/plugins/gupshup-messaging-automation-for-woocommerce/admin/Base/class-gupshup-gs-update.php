<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class GupshupGSUpdate {

	/**
	 * Plugin Update method
	 *
	 * @return void
	 */
	public static function update_plugin() {
		global $wpdb;
		$oldVersion = get_option( 'gupshup_plugin_version', '1.0' );
		$newVersion = GUPSHUP_GS_PLUGIN_VERSION;
		if ( ( version_compare( $newVersion, $oldVersion ) > 0 ) ) {
			include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Base/class-gupshup-gs-activate.php';
			GupshupGSActivate::activate();	
			update_option( 'gupshup_plugin_version', $newVersion );
		}
	}   
}
