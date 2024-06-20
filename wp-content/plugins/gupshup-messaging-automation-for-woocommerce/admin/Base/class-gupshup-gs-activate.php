<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class GupshupGSActivate {

	public static function activate() {
		include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Api/GupshupAPI/class-gupshup-gs-api-manager.php';
		self::gupshup_activation_analytics();
		self::create_workflow_table();
		self::create_action_table();
		self::create_workflow_log_table();
		self::create_abandoned_cart_table();
	}

	/**
	 * Tracking activation of the plugin for analytics
	 */
	public static function gupshup_activation_analytics() {
		$timestamp=current_time('timestamp');
		$gupshup_unique_id = uniqid(wp_rand()) . '-' . $timestamp;
		if ( get_option( 'gupshup_wp_uuid' ) !=null && get_option( 'gupshup_wp_uuid' )!='') {
			$gupshup_unique_id = get_option('gupshup_wp_uuid');
		} else {
			update_option('gupshup_wp_uuid', $gupshup_unique_id);
		}
		
		GupshupGSApiManager::get_instance()->call_install_track_api($gupshup_unique_id, 'activated', $timestamp);
	}

	/**
	 * Creating workflow table
	 */
	public static function create_workflow_table() {
		global $wpdb;
		$gupshup_workflow_db= $wpdb->prefix . GUPSHUP_GS_WORKFLOW_TEMPLATE_TABLE ;
		include_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta($wpdb->prepare("CREATE TABLE %1s(
				`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
				`is_activated`  boolean DEFAULT 0,
				`trigger_type` VARCHAR(255) NOT NULL,
            	`workflow_name` VARCHAR(255) NOT NULL,
				`time_scheduled_after` BIGINT(5) NOT NULL,
				`time_schedule_unit` VARCHAR(20) NOT NULL,
				`is_scheduled` boolean DEFAULT 0,
				PRIMARY KEY (`id`)) {$wpdb->get_charset_collate()};", $gupshup_workflow_db));
	}

	/**
	 * Creating workflow log table
	 */
	public static function create_workflow_log_table() {
		global $wpdb;
		$gupshup_workflow_log_db= $wpdb->prefix . GUPSHUP_GS_WORKFLOW_LOG_TABLE ;
		include_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($wpdb->prepare("CREATE TABLE %1s (`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
			`workflow_id` BIGINT(20) NOT NULL,
			`status` VARCHAR(255) NOT NULL,
            `time` VARCHAR(255) NOT NULL,
			`messages_sent` BIGINT(20) NOT NULL,
			PRIMARY KEY  (`id`)) {$wpdb->get_charset_collate()};", $gupshup_workflow_log_db));
	}

	/**
	 * Creating workflow action table
	 */
	public static function create_action_table() {
		global $wpdb;
		$gupshup_action_db = $wpdb->prefix . GUPSHUP_GS_ACTION_TABLE ;
		include_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta($wpdb->prepare( "CREATE TABLE %1s (
				`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
				`template_message` VARCHAR(4000) NOT NULL,
				`template_id` VARCHAR(255) NOT NULL,
        	    `template_type` VARCHAR(255) NOT NULL,
				`template_header` VARCHAR(255) NOT NULL,
				`template_footer` VARCHAR(255) NOT NULL,
				`template_button_type` VARCHAR(255) NOT NULL,
				`template_variable_value` VARCHAR(20000) NOT NULL,
				`template_variable_array` VARCHAR(255) NOT NULL,
				`template_variable_list` VARCHAR(255) NOT NULL,
				`template_header_variable_value` VARCHAR(60) NOT NULL,
				`template_header_variable_array` VARCHAR(255) NOT NULL,
				`template_header_variable_list` VARCHAR(60) NOT NULL,
				`template_media_url` VARCHAR(4000) NOT NULL,
				`workflow_id` BIGINT(20) NOT NULL,
            	PRIMARY KEY  (`id`)) {$wpdb->get_charset_collate()};", $gupshup_action_db));
		
	}

	public static function create_abandoned_cart_table() {
		global $wpdb;
		$gupshup_abandoned_cart_db = $wpdb->prefix . GUPSHUP_GS_ABANDONED_CART_TABLE ;
		include_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($wpdb->prepare( "CREATE TABLE %1s (
			`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
			`time` DATETIME,
			`gupshup_session_key` CHAR(32) NOT NULL,
            `status` VARCHAR(255) NOT NULL,
			`customer_data` LONGTEXT NOT NULL,
			`customer_phone` VARCHAR(14) NOT NULL,
			PRIMARY KEY  (`id`, `gupshup_session_key`)) {$wpdb->get_charset_collate()};", $gupshup_abandoned_cart_db));
	}

}

