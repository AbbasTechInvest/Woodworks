<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class GupshupGSSettingApi {

	public $admin_pages = array();

	public $admin_subpages = array();

	public $settings = array();

	public $sections = array();

	public $fields = array();

	public $meta_boxes = array();

	/**
	 * Register method to wp actions
	 *
	 * @return void
	 */
	public function register() {
		include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Base/class-gupshup-gs-helper.php';
		include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Base/class-gupshup-gs-base-controller.php';

		// registering admin menu if there are any
		if ( ! empty($this->admin_pages) ) {
			add_action( 'admin_menu', array( $this, 'addAdminMenu' ) );
		}
		
		// registering setting menu custom fields if there are any
		if ( !empty($this->settings) ) {
			add_action( 'admin_init', array( $this, 'registerCustomFields' ) );
		}
		
		// registering setting menu custom fields if there are any
		if ( !empty($this->meta_boxes) ) {
			add_action( 'admin_init', array( $this, 'registerMetaBoxes' ) );
		}

		//registering activation and deactivation hook when activation toggle has been clicked on workflow table.
		add_action( 'wp_ajax_activate_workflow_on_table', array( $this, 'update_workflow_activation_toggle_button' ) );

		//Deactivating all workflows if gupshup channel type, gupshup user id, gupshup password, gupshup business no. has been changed
		add_action( 'update_option_gupshup_channel_type', array( $this, 'deactivate_all_workflows' ), 10, 3 );
		add_action( 'update_option_gupshup_user_id', array( $this, 'deactivate_all_workflows' ), 10, 3 );
		add_action( 'update_option_gupshup_password', array( $this, 'deactivate_all_workflows' ), 10, 3 );
		add_action( 'update_option_gupshup_business_no', array( $this, 'deactivate_all_workflows' ), 10, 3 );
		
		add_action( 'wp_ajax_gupshup_workflow_run_chart_action', array( $this, 'get_workflow_run_chart_data' ) );
	}

	/**
	 * Adding admin pages
	 *
	 * @param array $pages
	 * @return void
	 */
	public function addPages( array $pages ) {
		$this->admin_pages = $pages;
		return $this;
	}

	/**
	 * Adding admin pages having a subpage
	 *
	 * @param [type] $title
	 * @return void
	 */
	public function withSubPage( $title = null ) { 
		if ( empty($this->admin_pages) ) {
			return $this;
		}

		$admin_page = $this->admin_pages[0];

		$subpage = array(
			array(
				'parent_slug' => $admin_page['menu_slug'], 
				'page_title' => $admin_page['page_title'], 
				'menu_title' => ( $title ) ? $title : $admin_page['menu_title'], 
				'capability' => $admin_page['capability'], 
				'menu_slug' => $admin_page['menu_slug'], 
				'callback' => $admin_page['callback']
			)
		);

		$this->admin_subpages = $subpage;

		return $this;
	}

	/**
	 * Adding sub pages
	 *
	 * @param array $pages
	 * @return void
	 */
	public function addSubPages( array $pages ) {
		$this->admin_subpages = array_merge( $this->admin_subpages, $pages );

		return $this;
	}

	/**
	 * Adding menu and sub menu pages
	 *
	 * @return void
	 */
	public function addAdminMenu() {
		foreach ( $this->admin_pages as $page ) {
			add_menu_page( $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'], $page['icon_url'], $page['position'] );
		}

		foreach ( $this->admin_subpages as $page ) {
			add_submenu_page( $page['parent_slug'], $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], ( isset( $page['callback'] ) ? $page['callback'] : '' ));
		}
		
	}

	public function setMetaBoxes( array $meta_boxes ) {
		$this->meta_boxes = $meta_boxes;
		return $this;
	}

	public function setSettings( array $settings ) {
		$this->settings = $settings;
		return $this;
	}

	public function setSections( array $sections ) {
		$this->sections = $sections;
		return $this;
	}

	public function setFields( array $fields ) {
		$this->fields = $fields;
		return $this;
	}

	public function registerCustomFields() {
		// register setting
		foreach ( $this->settings as $setting ) {
			register_setting( $setting['option_group'], $setting['option_name'], ( isset( $setting['callback'] ) ? $setting['callback'] : '' ) );
		}

		// add settings section
		foreach ( $this->sections as $section ) {
			add_settings_section( $section['id'], $section['title'], ( isset( $section['callback'] ) ? $section['callback'] : '' ), $section['page'] );
		}

		// add settings field
		foreach ( $this->fields as $field ) {
			add_settings_field( $field['id'], $field['title'], ( isset( $field['callback'] ) ? $field['callback'] : '' ), $field['page'], $field['section'], ( isset( $field['args'] ) ? $field['args'] : '' ) );
		}
	}

	/**
	 * Updating toggle button in workflow table grid
	 *
	 * @return void
	 */ 
	public function update_workflow_activation_toggle_button() {
		global $wpdb;
		$gupshup_gs_workflow_table_name = $wpdb->prefix . GUPSHUP_GS_WORKFLOW_TEMPLATE_TABLE;
		$gupshup_gs_action_table_name = $wpdb->prefix . GUPSHUP_GS_ACTION_TABLE;
		$id = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );
		$is_activated = GupshupGSHelper::get_instance()->sanitize_text_filter( 'state', 'POST' );
		$response = 'Something went wrong';
		if ( ! isset( $is_activated ) || ! isset( $id ) ) {
			$response_array = array(
				'toggle_response'=>$response,
			);
			wp_send_json_error( $response_array );
		}

		$action_result = $wpdb->get_row( $wpdb->prepare( 'SELECT *
		FROM %1s
		WHERE workflow_id = %d ',
		$gupshup_gs_action_table_name,
		$id )); 
		if (!isset($action_result) && $is_activated && 'off'===$is_activated) {
			$response = 'Please add action';
			$response_array = array(
				'toggle_response'=>$response,
			);
			wp_send_json_error( $response_array );
		}
		
		if ( $is_activated && 'on' === $is_activated ) {
			$is_activated = 0;
			$response     = 'Deactivated';
		} else {
			$is_activated = 1;
			$response     ='Activated';
		}
		$wpdb->query( $wpdb->prepare( 'UPDATE %1s
		SET is_activated = %d
		WHERE id = %d ',
		$gupshup_gs_workflow_table_name,
		$is_activated,
		$id )
		, ARRAY_A );
		
		$workflow_count_result = $wpdb->get_row($wpdb->prepare('SELECT Count(*) as total_count, Count(if(is_activated=true,1,null)) as active_count 
		FROM %1s',
		$gupshup_gs_workflow_table_name));
		$response_array = array(
			'toggle_response'=>$response,
			'workflow_count_response'=>$workflow_count_result
		);
		wp_send_json_success( $response_array );

	}

	/**
	 * Deactivating all workflows if gupshup setting credentials are changed
	 *
	 * @param [type] $old_value
	 * @param [type] $value
	 * @param [type] $option
	 * @return void
	 */
	public function deactivate_all_workflows( $old_value, $value, $option) {
		global $wpdb;
		$base_controller = new GupshupGSBaseController();
			$gupshup_gs_workflow_table_name = $wpdb->prefix . GUPSHUP_GS_WORKFLOW_TEMPLATE_TABLE;
			$gupshup_gs_action_table_name = $wpdb->prefix . GUPSHUP_GS_ACTION_TABLE;
			$wpdb->query( $wpdb->prepare( 'UPDATE %1s SET is_activated = %d ', $gupshup_gs_workflow_table_name, 0 ), ARRAY_A );
			$wpdb->query( $wpdb->prepare( 'TRUNCATE TABLE %1s', $gupshup_gs_action_table_name));
	}
	public function get_workflow_run_chart_data() {
		global $wpdb;
		$filter_days = filter_input( INPUT_POST, 'days', FILTER_VALIDATE_INT );
		$gupshup_gs_workflow_log_table_name = $wpdb->prefix . GUPSHUP_GS_WORKFLOW_LOG_TABLE;
		$gupshup_gs_workflow_table_name = $wpdb->prefix . GUPSHUP_GS_WORKFLOW_TEMPLATE_TABLE;
		
		$workflow_count_result = $wpdb->get_results($wpdb->prepare("SELECT Count(*) as workflow_run_count, DATE_FORMAT(time, '%%e %%b') as time, SUM(messages_sent) as messages_sent 
		FROM %1s
		WHERE time >= DATE_SUB(%s, INTERVAL %d DAY) GROUP BY DATE(time)",
		$gupshup_gs_workflow_log_table_name,
		current_time('mysql'),
		$filter_days)); 

		$most_run_workflow_result = $wpdb->get_results($wpdb->prepare('SELECT workflow_log_table.workflow_id, TIMESTAMPDIFF(HOUR, max(workflow_log_table.time), %s) AS last_run, workflow_table.workflow_name, COUNT(*) 
		FROM %1s as workflow_log_table JOIN %2s as workflow_table ON workflow_log_table.workflow_id=workflow_table.id
		WHERE workflow_log_table.time >= DATE_SUB( %s , INTERVAL %d DAY) GROUP BY workflow_log_table.workflow_id ORDER BY COUNT(*) DESC LIMIT 3', current_time('mysql'), $gupshup_gs_workflow_log_table_name, $gupshup_gs_workflow_table_name, current_time('mysql'), $filter_days)); 
		
		foreach ($most_run_workflow_result as $workflow_data) {
			$redirect_url = add_query_arg(
				array(
					'action'     => GUPSHUP_GS_WORKFLOW_TEMPLATES,
					'sub_action' => GUPSHUP_GS_SUB_ACTION_EDIT_WORKFLOW,
					'id'         => $workflow_data->workflow_id,
					'_wpnonce' => wp_create_nonce( 'gupshup-nonce-action' ),
				),
				admin_url( 'admin.php?page=' . GUPSHUP_GS_WORKFLOW_PAGE_NAME . '&action=' . GUPSHUP_GS_WORKFLOW_TEMPLATES )
			);
			$workflow_data->redirect_workflow_url=$redirect_url;
		}
		
		$total_messages_sent_result = $wpdb->get_row($wpdb->prepare('SELECT SUM(messages_sent) as total_messages_sent
		FROM %1s
		WHERE time >= DATE_SUB(%s, INTERVAL %d DAY)', $gupshup_gs_workflow_log_table_name, current_time('mysql'), $filter_days));

		$dashboard_result=array(
			'workflow_count_data'=>$workflow_count_result,
			'most_run_workflow_data'=>$most_run_workflow_result,
			'total_messages_sent_data'=>$total_messages_sent_result->total_messages_sent,
		);
		wp_send_json_success( $dashboard_result );
	}
	
	
}
