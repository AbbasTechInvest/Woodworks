<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class GupshupGSDeactivate {

	/**
	 * Decativation method
	 *
	 * @return void
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook( 'gupshup_gs_abandoned_cart_trigger_action' );
		include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Api/GupshupAPI/class-gupshup-gs-api-manager.php';
		self::gupshup_activation_analytics();
	}

	/**
	 * Callback for deactivation analytics
	 *
	 * @return void
	 */
	public static function gupshup_activation_analytics() {
		$gupshup_unique_id = uniqid(wp_rand()) . '-' . current_time('timestamp');
		if ( get_option( 'gupshup_wp_uuid' ) !=null && get_option( 'gupshup_wp_uuid' )!='') {
			$gupshup_unique_id = get_option('gupshup_wp_uuid');
		} else {
			update_option('gupshup_wp_uuid', $gupshup_unique_id);
		}
		GupshupGSApiManager::get_instance()->call_install_track_api($gupshup_unique_id, 'deactivated');
	}
}
