<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'GupshupGSLoader' ) ) {

	/**
	 * Class GupshupGSLoader.
	 */
	final class GupshupGSLoader {

		private static $instance;
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}
		/**
		 * Constructor
		 */
		public function __construct() {

			// Defining Constants
			$this->define_constants();

			// Activation hook.
			register_activation_hook( GUPSHUP_GS_PLUGIN_FILE, array( $this, 'activate_gupshup_gs_plugin' ) );

			// DeActivation hook.
			register_deactivation_hook( GUPSHUP_GS_PLUGIN_FILE, array( $this, 'deactivate_gupshup_gs_plugin' ) );

			// Load core files    
			add_action( 'plugins_loaded', array( $this, 'load_gupshup_plugin' ), 99 );

		}
		
		/**
		 * Defining Constants
		 *
		 * @return void
		 */
		public function define_constants() {
			define( 'GUPSHUP_GS_PLUGIN_VERSION', '2.2.3' );
			define( 'GUPSHUP_GS_ENTERPRISE_WRAPPER_URL', 'https://integration-apis.gupshup.io/wrapperServices/v1' );
			define( 'GUPSHUP_GS_SELF_SERVE_URL', 'https://api.gupshup.io/sm/api');
			define( 'GUPSHUP_GS_ENTERPRISE_TEMPLATE_APIURL', 'https://wamedia.smsgupshup.com/GatewayAPI/rest' );
			define( 'GUPSHUP_GS_ENTERPRISE_APIURL', 'https://media.smsgupshup.com/GatewayAPI/rest' );
			define( 'GUPSHUP_GS_WORKFLOW_PAGE_NAME', 'template-gupshup-gs-workflow-admin' );
			define( 'GUPSHUP_GS_PLUGIN_FILE_DIR', plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) );
			define( 'GUPSHUP_GS_PLUGIN_NAME', 'Gupshup Messaging Automation for WooCommerce' );
			define( 'GUPSHUP_GS_WORKFLOW_TEMPLATES', 'workflow_tmpl' );
			define( 'GUPSHUP_GS_SUB_ACTION_ADD_WORKFLOW_TEMPLATES', 'add_workflow_tmpl' );
			define( 'GUPSHUP_GS_WORKFLOW_TEMPLATE_TABLE', 'gupshup_gs_workflow' );
			define( 'GUPSHUP_GS_WORKFLOW_LOG_TABLE', 'gupshup_gs_workflow_log' );
			define( 'GUPSHUP_GS_ACTION_TABLE', 'gupshup_gs_action' );
			define( 'GUPSHUP_GS_ABANDONED_CART_TABLE', 'gupshup_gs_abandoned_cart' );
			define( 'GUPSHUP_GS_SUB_ACTION_SAVE_WORKFLOW', 'save_workflow' );
			define( 'GUPSHUP_GS_SUB_ACTION_EDIT_WORKFLOW', 'edit_workflow' );
			define( 'GUPSHUP_GS_SUB_ACTION_DELETE_WORKFLOW', 'delete_workflow' );
			define( 'GUPSHUP_GS_SUB_ACTION_DELETE_BULK_WORKFLOW', 'delete_bulk_workflow_tmpl' );
			define( 'GUPSHUP_GS_SUB_ACTION_CLONE_WORKFLOW', 'clone_workflow_tmpl' );
			define( 'GUPSHUP_GS_APP_NAME', 'woocommerce' );
			define( 'GUPSHUP_GS_SELF_SERVE', 'self-serve' );
			define( 'GUPSHUP_GS_ENTERPRISE', 'enterprise' );
			define( 'GUPSHUP_GS_DEFAULT_CRON_INTERVAL', 15 );
			define( 'GUPSHUP_GS_WOOCOMMERCE_SESSION_TABLE', 'woocommerce_sessions');
		}

		public function activate_gupshup_gs_plugin() {
			include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Base/class-gupshup-gs-activate.php';
			GupshupGSActivate::activate();
		}
		
		public function deactivate_gupshup_gs_plugin() {
			include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Base/class-gupshup-gs-deactivate.php';
			GupshupGSDeactivate::deactivate();
		}
		
		/**
		 * Initialize all the core classes of the plugin
		 */
		public function load_gupshup_plugin () {
			include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Base/class-gupshup-gs-update.php';
			GupshupGSUpdate::update_plugin();

			include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/class-gupshup-gs-init.php';
			if ( class_exists( 'GupshupGSInit' ) ) {
				GupshupGSInit::register_classes();
			}
			include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Base/class-gupshup-gs-cron.php';
			include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Base/class-gupshup-gs-abandoned-cart-track.php';
		}

	}
	GupshupGSLoader::get_instance();
}
