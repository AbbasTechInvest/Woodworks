<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Base/class-gupshup-gs-base-controller.php';
include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Api/class-gupshup-gs-settings-api.php';
include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Api/Callbacks/class-gupshup-gs-admin-callbacks.php';
include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Api/Callbacks/class-gupshup-gs-manager-callbacks.php';

/**
 * GupshupGSAdmin class
 */
class GupshupGSAdmin extends GupshupGSBaseController {

	public $settings;
	public $callbacks;
	public $callbacks_mngr;

	public $pages = array();
	public $subpages = array();

	public function register() { 
		$this->settings = new GupshupGSSettingApi();
		$this->callbacks = new GupshupGSAdminCallbacks();
		$this->callbacks_mngr = new GupshupGSManagerCallbacks();

		$this->setPages();
		$this->setSubpages();
		$this->setSettings();
		$this->setSections();
		$this->setFields();
		
		$this->settings->addPages( $this->pages )->withSubPage( 'Dashboard' )->addSubPages( $this->subpages )->register();
	}

	public function setPages() { 
		$this->pages = array(
			array(
				'page_title' => 'Gupshup', 
				'menu_title' => 'Gupshup', 
				'capability' => 'manage_options', 
				'menu_slug' => 'gupshup_gs_plugin', 
				'callback' => array( $this->callbacks, 'gupshupGSDashboard' ), 
				'icon_url' => plugin_dir_url( GUPSHUP_GS_PLUGIN_FILE ) . 'admin/assets/images/gupshuplogo.svg', 
				'position' => 110
			)
		);
	}

	public function setSubpages() {
		$this->subpages = array(
			array(
				'parent_slug' => 'gupshup_gs_plugin', 
				'page_title' => 'Workflow', 
				'menu_title' => 'Workflow', 
				'capability' => 'manage_options', 
				'menu_slug' => GUPSHUP_GS_WORKFLOW_PAGE_NAME, 
				'callback' => array( $this->callbacks, 'gupshupGSWorkflow' )
			),
			array(
				'parent_slug' => 'gupshup_gs_plugin', 
				'page_title' => 'Settings', 
				'menu_title' => 'Settings', 
				'capability' => 'manage_options', 
				'menu_slug' => 'gupshup_gs_plugin_setting', 
				'callback' => array( $this->callbacks, 'gupshupGSSetting' )
			),
			
		);
	}

	public function setSettings() {
		$args = array(
			array(
				'option_group' => 'gupshup_gs_plugin_setting_fields',
				'option_name' => 'gupshup_channel_name',
			),
			array(
				'option_group' => 'gupshup_gs_plugin_setting_fields',
				'option_name' => 'gupshup_user_id'
			),
			array(
				'option_group' => 'gupshup_gs_plugin_setting_fields',
				'option_name' => 'gupshup_password'
			),
			array(
				'option_group' => 'gupshup_gs_plugin_setting_fields',
				'option_name' => 'gupshup_business_no'
			),
			array(
				'option_group' => 'gupshup_gs_plugin_setting_fields',
				'option_name' => 'gupshup_channel_type'
			),
			array(
				'option_group' => 'gupshup_gs_plugin_setting_fields',
				'option_name' => null
			)
		);

		$this->settings->setSettings( $args );
	}

	public function setSections() {
		$args = array(
			array(
				'id' => 'gupshup_gs_setting_channel_setting_section',
				'title' => null,
				'callback' => array( $this->callbacks_mngr, 'gupshupChannelSettingSectionManager' ),
				'page' => 'gupshup_gs_plugin'
			),
		);

		$this->settings->setSections( $args );
	}
	
	public function setFields() {
		$args = array(
			array(
				'id' => 'gupshup_channel_type',
				'title' => 'Account Type *',
				'callback' => array( $this->callbacks_mngr, 'gupshupChannelType' ),
				'page' => 'gupshup_gs_plugin',
				'section' => 'gupshup_gs_setting_channel_setting_section',
				'args' => array(
					'label_for' => 'gupshup_channel_type',
					'class' => 'example-class'
				)
			),
			array(
				'id' => 'gupshup_channel_name',
				'title' => 'Channel Name *',
				'callback' => array( $this->callbacks_mngr, 'gupshupChannelName' ),
				'page' => 'gupshup_gs_plugin',
				'section' => 'gupshup_gs_setting_channel_setting_section',
				'args' => array(
					'label_for' => 'gupshup_channel_name',
					'class' => 'example-class'
				)
			),
			array(
				'id' => 'gupshup_user_id',
				'title' => get_option('gupshup_channel_type')=='enterprise'?'User ID *':'App name *',
				'desc'  => 'The street address for your business location.',
				'callback' => array( $this->callbacks_mngr, 'gupshupUserId' ),
				'page' => 'gupshup_gs_plugin',
				'section' => 'gupshup_gs_setting_channel_setting_section',
				'desc_tip' => true,
				'args' => array(
					'label_for' => 'gupshup_user_id',
					'class' => 'example-class'
				)
			),
			array(
				'id' => 'gupshup_password',
				'title' => get_option('gupshup_channel_type')=='enterprise'?'Password *':'API key *',
				'callback' => array( $this->callbacks_mngr, 'gupshupPassword' ),
				'page' => 'gupshup_gs_plugin',
				'section' => 'gupshup_gs_setting_channel_setting_section',
				'args' => array(
					'label_for' => 'gupshup_password',
					'class' => 'example-class'
				)
			),
			array(
				'id' => 'gupshup_business_no',
				'title' => 'Business No. *',
				'callback' => array( $this->callbacks_mngr, 'gupshupBusinessNo' ),
				'page' => 'gupshup_gs_plugin',
				'section' => 'gupshup_gs_setting_channel_setting_section',
				'args' => array(
					'label_for' => 'gupshup_business_no',
					'class' => 'example-class'
				)
			),
			array(
				'id' => 'gupshup_register_link',
				'title' => '',
				'callback' => array( $this->callbacks_mngr, 'gupshupRegisterLink' ),
				'page' => 'gupshup_gs_plugin',
				'section' => 'gupshup_gs_setting_channel_setting_section',
			)
		);

		$this->settings->setFields( $args );
	}

}
