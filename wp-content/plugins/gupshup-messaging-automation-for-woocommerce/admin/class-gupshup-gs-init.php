<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


final class GupshupGSInit {

	/*
	 * Store all the classes inside an array
	 * @return array Full list of classes
	 */
	public static function get_services() {
		include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Pages/class-gupshup-gs-admin.php';
		include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Base/class-gupshup-gs-enqueue-script.php';
		include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Base/class-gupshup-gs-settings-links.php';
		include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Base/class-gupshup-gs-run-workflow.php';
		return [
			GupshupGSAdmin::class,
			GupshupGSEnqueue::class,
			GupshupGSSettingsLinks::class,
			GupshupGSRunWorkflow::class,
		];
	}

	/*
	 * Loop through the classes, initialize them, 
	 * and call the register() method if it exists
	 */
	public static function register_classes() { 
		foreach ( self::get_services() as $class ) {
			$service = self::instantiate( $class );
			if ( method_exists( $service, 'register' ) ) {
				$service->register();
			}
		}
	}

	/*
	 * Initialize the class
	 */
	private static function instantiate( $class ) {
		$service = new $class();
		return $service;
	}
	
}
