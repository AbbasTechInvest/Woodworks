<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class GupshupGSWorkflowTemplates {

	private $wpdb;

	private static $instance;

	public $workflow_template_table_name;
	public $woocommerce_hooks;
	public $action_table_name;
	public $trigger_post_types_data;
	public $variable_fields_data;
	public $trigger_help_text;
	public $scheduling_triggers;
	

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor function that initializes required actions and hooks
	 */
	public function __construct() {
		include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Base/class-gupshup-gs-workflow-templates-table.php';
		global $wpdb;
		$this->workflow_template_table_name = $wpdb->prefix . GUPSHUP_GS_WORKFLOW_TEMPLATE_TABLE;
		$this->action_table_name = $wpdb->prefix . GUPSHUP_GS_ACTION_TABLE;
		$this->wpdb = $wpdb;

		include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Base/class-gupshup-gs-base-controller.php';
		$base_controller = new GupshupGSBaseController();
		$this->woocommerce_hooks = $base_controller->get_woocommerce_hooks();
		$this->trigger_help_text = $base_controller->get_trigger_help_text();
		$this->trigger_post_types_data = $base_controller->get_trigger_post_types();
		$this->variable_fields_data = $base_controller->get_variable_fields();
		$this->scheduling_triggers = $base_controller->get_scheduling_type_trigger();
		include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Api/GupshupAPI/class-gupshup-gs-api-manager.php';
	}

	/*
	 *  Show success messages for workflow.
	 */
	public function show_messages() {

		$helper_class = GupshupGSHelper::get_instance();
		$gupshup_gs_workflow_created  = $helper_class->sanitize_text_filter( 'gupshup_gs_workflow_created', 'GET' );
		$gupshup_gs_workflow_cloned   = $helper_class->sanitize_text_filter( 'gupshup_gs_workflow_cloned', 'GET' );
		$gupshup_gs_workflow_deleted  = $helper_class->sanitize_text_filter( 'gupshup_gs_workflow_deleted', 'GET' );
		$gupshup_gs_workflow_updated  = $helper_class->sanitize_text_filter( 'gupshup_gs_workflow_updated', 'GET' );
		
		?>
		<?php if ( 'YES' === $gupshup_gs_workflow_created ) { ?>
		<div id="message" class="notice notice-success is-dismissible">
			<p>
				<strong>
					<?php echo esc_html('The Workflow has been successfully added.'); ?>
				</strong>
			</p>
		</div>
	<?php } ?>

		<?php if ( 'YES' === $gupshup_gs_workflow_cloned ) { ?>
		<div id="message" class="notice notice-success is-dismissible">
			<p>
				<strong>
					<?php echo esc_html('The Workflow has been cloned successfully.'); ?>
				</strong>
			</p>
		</div>
	<?php } ?>

		<?php if ( 'YES' === $gupshup_gs_workflow_deleted ) { ?>
		<div id="message" class="notice notice-success is-dismissible">
			<p>
				<strong>
					<?php echo esc_html('The Workflow has been successfully deleted.'); ?>
				</strong>
			</p>
		</div>
	<?php } ?>
		<?php if ( 'YES' === $gupshup_gs_workflow_updated ) { ?>
		<div id="message" class="notice notice-success is-dismissible">
			<p>
				<strong>
					<?php echo esc_html('The Workflow has been successfully updated.'); ?>
				</strong>
			</p>
		</div>
	<?php } ?>

	<?php

	}

	/*
	 *  Delete bulk workflow.
	 */
	public function delete_bulk_workflow_templates() {
		$gupshup_workflow_template_list = new GupshupGSWorkflowTemplatesTable();
		$gupshup_workflow_template_list->process_bulk_action();
		$sub_action     = GupshupGSHelper::get_instance()->sanitize_text_filter( 'sub_action', 'GET' );
		$action     = GupshupGSHelper::get_instance()->sanitize_text_filter( 'action', 'GET' );
		$param = array(
			'page'                    => GUPSHUP_GS_WORKFLOW_PAGE_NAME,
			'action'                  => GUPSHUP_GS_WORKFLOW_TEMPLATES,
			'_wpnonce' => wp_create_nonce( 'gupshup-nonce-action' )
		);
		if ( GUPSHUP_GS_SUB_ACTION_DELETE_BULK_WORKFLOW === $sub_action && GUPSHUP_GS_WORKFLOW_TEMPLATES===$action ) {
			$param['gupshup_gs_workflow_deleted'] = 'YES';
		}
		$redirect_url =  add_query_arg( $param, admin_url( '/admin.php' ) );
		wp_safe_redirect( $redirect_url );
	}


	/**
	 *  Delete workflow.
	 */
	public function delete_single_workflow_template() {
		$id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
		if ( $id ) {
			$this->wpdb->delete(
				$this->workflow_template_table_name,
				array( 'id' => $id ),
				'%d'
			);
			$this->wpdb->delete(
				$this->action_table_name,
				array( 'workflow_id' => $id ),
				'%d'
			);
			$param = array(
				'page'                    => GUPSHUP_GS_WORKFLOW_PAGE_NAME,
				'action'                  => GUPSHUP_GS_WORKFLOW_TEMPLATES,
				'gupshup_gs_workflow_deleted' => 'YES',
				'_wpnonce' => wp_create_nonce( 'gupshup-nonce-action' )
			);
			$redirect_url = add_query_arg( $param, admin_url( '/admin.php' ) );
			wp_safe_redirect( $redirect_url );
		}
	}

	/**
	 *  Get workflow by Id
	 */
	public function get_workflow_by_id( $workflow_id ) {
		global $wpdb;
		return $wpdb->get_row($wpdb->prepare('SELECT  *  FROM %1s WHERE id = %d', $this->workflow_template_table_name, $workflow_id)); 

	}

	/**
	 *  Get action by workflow_id
	 */
	public function get_action_by_workflow_id( $workflow_id ) {
		global $wpdb;
		return $wpdb->get_row($wpdb->prepare('SELECT  *  FROM %1s WHERE workflow_id = %d ', $this->action_table_name, $workflow_id)); 

	}

	/**
	 *  Render Workflow add/edit form.
	 *
	 * @param string $sub_action sub_action.
	 */
	public function render_workflow_form( $sub_action = GUPSHUP_GS_SUB_ACTION_ADD_WORKFLOW_TEMPLATES ) {
		$id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
		
		if ( $id ) {
			$results = $this->get_workflow_by_id( $id );
		}
		if ($id && $results) {
			$action_result = $this->get_action_by_workflow_id( $id );
		}
		$gupshup_api_manager = new GupshupGSApiManager();
		$template_data=array();
		if (get_option('gupshup_channel_type')===GUPSHUP_GS_SELF_SERVE) {
			$template_data = $gupshup_api_manager->get_selfserve_templates();
		} else if (get_option('gupshup_channel_type')===GUPSHUP_GS_ENTERPRISE) {
			$template_data = $gupshup_api_manager->get_enterprise_templates();
		} else {
			?>
			
			<div id="message" class="notice notice-error is-dismissible">
				<p>
					<strong>
						<?php echo esc_html('No Channel has been Configured. Please configure the channel'); ?>
					</strong>
				</p>
			</div>
		<?php
		}
		?>

		<div id="content">

			<?php
			$param             = array(
				'page'       => GUPSHUP_GS_WORKFLOW_PAGE_NAME,
				'action'     => GUPSHUP_GS_WORKFLOW_TEMPLATES,
				'sub_action' => GUPSHUP_GS_SUB_ACTION_SAVE_WORKFLOW,
				'_wpnonce' => wp_create_nonce( 'gupshup-nonce-action' ),
			);
			$save_template_url = esc_url(  add_query_arg( $param, admin_url( '/admin.php' ) ), 'gupshup-nonce-action' );
			?>

			<form method="post" action="<?php echo esc_attr( $save_template_url ); ?>">
				<input type="hidden" name="sub_action" value="<?php echo esc_attr( $sub_action ); ?>"/>
				<?php
				$id_by = '';
				if ( isset( $id ) ) {
					$id_by = $id;
				}
				?>
				<input type="hidden" name="id" value="<?php echo esc_attr( $id_by ); ?>"/>
				<?php

				$button_sub_action = 'save';
				$display_message   = 'Add New Workflow';

				if ( GUPSHUP_GS_SUB_ACTION_EDIT_WORKFLOW === $sub_action ) {
					$button_sub_action = 'update';
					$display_message   = 'Edit Workflow';
				}
				print '<input type="hidden" name="gupshup_setting_form" value="' . esc_attr( $button_sub_action ) . '">';
				?>
				<?php $nonce = wp_create_nonce( 'gupshup-action' ); ?>
				<input type="hidden" name="message-send" value="<?php echo esc_attr($nonce); ?>" />
				<div id="poststuff">
					<div> 
						<h3><?php esc_html_e($display_message); ?></h3>
						<div class="postbox panel">
							<div class="postbox-header">
								<h2 class="hndle ui-sortable-handle">Workflow Details</h2>
							</div>
							<div class="inside">
								<table class="form-table">
									
									<tr>
										<th>
											<label for="gupshup_workflow_name"><b><?php echo esc_html('Title *'); ?></b></label>
										</th>
										<td>
											<?php
											$workflow_name = '';
											if ( GUPSHUP_GS_SUB_ACTION_EDIT_WORKFLOW === $sub_action && $results && isset( $results->workflow_name ) ) {
												$workflow_name = $results->workflow_name;
											}
											print '<input type="text" required pattern="([A-Za-z0-9_ ]+)" name="gupshup_workflow_name" id="gupshup_workflow_name" class="gupshup-gs-trigger-input" value="' . esc_attr( $workflow_name ) . '">';
											print '<span class="help-block">' . esc_html('Give your workflow a name. Workflow names can only contain letters, numbers and underscores.') . '</span>';

											?>
										</td>
									</tr>

									<tr>
										<th>
											<label for="gupshup_trigger"><b><?php echo esc_html('Trigger *'); ?></b></label>
										</th>
										<td>
											<?php
												$gupshup_trigger_type = '';
											if ( GUPSHUP_GS_SUB_ACTION_EDIT_WORKFLOW === $sub_action && $results && isset( $results->trigger_type ) ) {
												$gupshup_trigger_type = stripslashes( $results->trigger_type );
											}
											?>
												<select required name="gupshup_trigger_type" id="gupshup_trigger_type" style="width:100%">
												<option value=""> --Select Trigger-- </option>
												<?php
												foreach ( $this->woocommerce_hooks as $key => $value ) {
													echo '<option value="' . esc_attr($value) . '"' . selected( $gupshup_trigger_type, $value ) . ' >' . esc_html($value) . '</option>';
												} 
												?>
											</select>
											<?php
												print '<span id ="gupshup_trigger_span_help_text" class="help-block">';
											if (null!==$gupshup_trigger_type && ''!==$gupshup_trigger_type) {
												print esc_html($this->trigger_help_text[$gupshup_trigger_type]);
											}
												print '</span>';
											?>
										</td>
									</tr>
									
									<tr id="gupshup_workflow_time_option_block" style="display:<?php echo ( isset($results) && isset( $results->trigger_type ) && in_array($results->trigger_type, array_values($this->scheduling_triggers)) )?'':'none'; ?>" >
										<th>
											<label for="gupshup_workflow_timing_label"><b><?php echo esc_html('Timing '); ?></b></label>
										</th>
										<td>
										<?php
												$gupshup_is_scheduled = false;
										if ( GUPSHUP_GS_SUB_ACTION_EDIT_WORKFLOW === $sub_action && $results && isset( $results->is_scheduled ) ) {
											$gupshup_is_scheduled = stripslashes( $results->is_scheduled);
										}
										?>
												<select name="gupshup_is_scheduled" id="gupshup_is_scheduled" style="width:100%">
												<?php
												$scheduling_array=array(
													'Run Immediately'=>false,
													'Run After'=>true,
												);
												foreach ( $scheduling_array as $key => $value ) {
													echo '<option value="' . esc_attr($value) . '"' . selected( $gupshup_is_scheduled, $value ) . ' >' . esc_html($key) . '</option>';
												} 
												?>
											</select>
											<?php
												print '<span id ="gupshup_is_scheculed_span_help_text" class="help-block">';
												print 'Schedule your trigger';
												print '</span>';
											?>
													
										</td>
									</tr>
									<tr id="gupshup_workflow_timing_block" style="display:<?php echo ( isset($results) && ( ( isset( $results->trigger_type ) && 'Abandoned Cart'==$results->trigger_type ) ) || ( ( isset( $results->is_scheduled) && $results->is_scheduled ) ) )?'':'none'; ?>" >
										<th>
											<label for="gupshup_workflow_timing_label"><b><?php echo esc_html('Age '); ?></b></label>
										</th>
										<td>
											<?php
												$gupshup_workflow_timing = 15;
												$max = 4320;
												$gupshup_workflow_timing_unit='minutes';
											if ( GUPSHUP_GS_SUB_ACTION_EDIT_WORKFLOW === $sub_action && $results && isset( $results->time_scheduled_after ) && isset( $results->time_schedule_unit ) ) {
												$gupshup_workflow_timing = stripslashes( $results->time_scheduled_after);
												if (stripslashes( $results->time_schedule_unit) =='days') {
													$max = 3;
													$gupshup_workflow_timing = floor($gupshup_workflow_timing/1440);
												} else if (stripslashes( $results->time_schedule_unit) =='hours') {
													$max = 72;
													$gupshup_workflow_timing = floor($gupshup_workflow_timing/60);
												}
												$gupshup_workflow_timing_unit = stripslashes( $results->time_schedule_unit);
											}
												printf(
													'<input type="number" id="gupshup_workflow_timing" min="1" max="%d" name="gupshup_workflow_timing" value="%s" required/>',
													$max, isset( $gupshup_workflow_timing ) ? esc_attr( $gupshup_workflow_timing ) : ''
													);
											?>
												<select name="gupshup_workflow_timing_unit" id="gupshup_workflow_timing_unit">
												<?php
												$age_units_list=array('minutes','hours','days');
												foreach ( $age_units_list as $value) {
													echo '<option value="' . esc_attr($value) . '"' . selected( $gupshup_workflow_timing_unit, $value ) . ' >' . esc_html($value) . '</option>';
												} 
												?>
												</select>
												<?php
												echo '<span id ="gupshup_age_span_help_text" class="help-block">Age of selected trigger (In ' . esc_html( $gupshup_workflow_timing_unit ) . '). min 1 and max ' . esc_html( $max ) . ' ' . esc_html( $gupshup_workflow_timing_unit ) . '</span>';
												?>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<div class="postbox panel">
						<div class="postbox-header">
							<h2 class="hndle ui-sortable-handle">Actions</h2>
						</div>
							<div class="inside">
								<table class="form-table">
									<tr>
										<th>
											<label for="gupshup_workflow_action_template"><b><?php echo esc_html('Template Type *'); ?></b></label>
										</th>
										<td>
											<?php
												$template_category = '';
												$template_category_list = array();
												$template_map = array();
												$temp_template_categories = array();
											if (null!=$template_data && count($template_data)>0) {
												foreach ( $template_data as $template ) {
													array_push($temp_template_categories, $template['template_type']);
													$temp_template = array($template);
													if (!isset($template_map[$template['template_type']])) {
														$template_map[$template['template_type']] = array();
													}
													$template_map[$template['template_type']] = array_merge($temp_template, $template_map[$template['template_type']]);  
												}
											}
												$template_category_list = ( array_unique($temp_template_categories) );
												$vars = array(
													'get_data_php_url'              => plugin_dir_url(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Api/Callbacks/class-gupshup-gs-ajax-data-callback.php',
													'template_data'                  => $template_data,
													'trigger_post_types_data'              => $this->trigger_post_types_data,
													'variable_fields_data'                 => $this->variable_fields_data,
													'trigger_help_text'                 => $this->trigger_help_text,
													'scheduling_triggers'				=> array_values($this->scheduling_triggers),
												);
												wp_localize_script( 'gupshup-workflow-jquey', 'gupshup_gs_action_vars', $vars );
												if ( GUPSHUP_GS_SUB_ACTION_EDIT_WORKFLOW === $sub_action && $action_result && isset( $action_result->template_type ) ) {
													$template_category = stripslashes( $action_result->template_type );
												}
												?>
										
											<select name="gupshup_template_category" id="gupshup_template_category" required style="width:100%">
											<option value=""> --Select Category-- </option>
											<?php 
											foreach ( $template_category_list as $category ) {
												echo '<option value="' . esc_attr( $category ) . '"' . selected( $template_category, $category ) . '> ' . esc_html( $category ) . ' </option>';
											} 
											?>
											</select>
										</td>
									</tr>

									<tr>
										<th>
											<label for="gupshup_workflow_action_template"><b>Template Name *</b></label>
										</th>
										<td>
											<select class="form-control" name="gupshup_template_id" id="gupshup_template_id" required style="width:100%">
												<option value=""> --Select Template-- </option>
												<?php
													$is_template_available = false;
												if ( GUPSHUP_GS_SUB_ACTION_EDIT_WORKFLOW === $sub_action && $action_result && isset( $action_result->template_type ) ) {

													$template_id = stripslashes( $action_result->template_id );
													foreach ( $template_map[$template_category] as $template ) {
														echo '<option value="' . esc_attr( $template['template_id'] ) . '"' . selected( $template_id, $template['template_id'] ) . ' >' . esc_html( $template['template_name'] ) . '</option>';
														if ( $template['template_id'] == $template_id ) {
															$is_template_available = true;
														}
													}
												}
												?>
											</select>
										</td>
									</tr>
																		
									<tr id="gupshup_action_media_url_block" style="display:<?php echo ( isset($action_result) && isset( $action_result->template_type ) && ''!==$action_result->template_type && 'TEXT'!==$action_result->template_type )?'':'none'; ?>" >
										<th>
											<label for="gupshup_action_media_url_label"><b><?php echo esc_html('Media URL'); ?></b></label>
										</th>
										<td>
											<?php
											$pattern = '';
											$title = '';
											$template_media_url = '';
											
											if ( GUPSHUP_GS_SUB_ACTION_EDIT_WORKFLOW === $sub_action && $action_result && isset( $action_result->template_media_url ) ) {
												switch ($action_result->template_type) {
													case 'IMAGE':
														$pattern = '(http|https):\/\/.*\.(jpg|jpeg|png)(\?[\w\-@?^=%&/~\+#]*)?$';
														$title = 'Insert any .jpg, .jpeg or .png Image URL';
														break;
													case 'DOCUMENT':
														$pattern = '(http|https):\/\/.*\.(pdf)(\?[\w\-@?^=%&/~\+#]*)?$';
														$title = 'Insert any .pdf Document URL';
														break;
													case 'VIDEO':
														$pattern = '(http|https):\/\/.*\.(mp4)(\?[\w\-@?^=%&/~\+#]*)?$';
														$title = 'Insert any .mp4 Document URL';
														break;
													default:
														$pattern = '^(http|https):\/\/.*$';
														break;
												}
												$template_media_url = $action_result->template_media_url;
											}
											$media_url_required=( isset($action_result) && isset( $action_result->template_type ) && ''!==$action_result->template_type && 'TEXT'!==$action_result->template_type )?'required ':'';
											print '<input type="url" name="gupshup_template_media_url" id="gupshup_template_media_url" ' . esc_attr( $media_url_required ) . 'value="' . esc_attr( $template_media_url ) . '" pattern="' . esc_attr( $pattern ) . '" title="' . esc_attr( $title ) . '" class="gupshup-gs-trigger-input" >';
											print '<span class="help-block">' . esc_html('In case of media, please provide Media URL') . '</span>';

											?>
										</td>
									</tr>

									<tr>
										<th>
											<label for="gupshup_workflow_action_template"><b>Message</b></label>
										</th>
										<td>
										<?php 
										if ( GUPSHUP_GS_SUB_ACTION_EDIT_WORKFLOW === $sub_action && $action_result && isset( $action_result->template_header) && $is_template_available ) {
											$template_header = $action_result->template_header;
											$gupshup_template_header_detail = $action_result->template_header;
											$header_variable_list = unserialize($action_result->template_header_variable_list);
											$header_variable_value = unserialize($action_result->template_header_variable_value);
											$header_variable_array = unserialize($action_result->template_header_variable_array);
											if (isset($header_variable_list) && !empty($header_variable_list)) {
												foreach ($header_variable_list as $headerVariableIndex=>$header_variable_list_value) {
													if (''!=$header_variable_value[$headerVariableIndex]) {
														$gupshup_template_header_detail = str_replace($header_variable_list_value, $header_variable_value[$headerVariableIndex], $gupshup_template_header_detail);
													}
												}
											}
										}
										if ( GUPSHUP_GS_SUB_ACTION_EDIT_WORKFLOW === $sub_action && $action_result && isset( $action_result->template_footer) && $is_template_available ) {
											$template_footer = $action_result->template_footer;
										}
										if ( GUPSHUP_GS_SUB_ACTION_EDIT_WORKFLOW === $sub_action && $action_result && isset( $action_result->template_button_type) && $is_template_available ) {
											$template_button_type = $action_result->template_button_type;
										}
										if ( GUPSHUP_GS_SUB_ACTION_EDIT_WORKFLOW === $sub_action && $action_result && isset( $action_result->template_message) && $is_template_available ) {
											$template_message = $action_result->template_message;
											$gupshup_template_message_detail = $action_result->template_message;
											$variable_list = unserialize($action_result->template_variable_list);
											$variable_value = unserialize($action_result->template_variable_value);
											$variable_array = unserialize($action_result->template_variable_array);
											if (isset($variable_list) && !empty($variable_list)) {
												foreach ($variable_list as $variableIndex=>$variable_list_value) {
													if (''!=$variable_value[$variableIndex]) {
														$gupshup_template_message_detail = str_replace($variable_list_value, $variable_value[$variableIndex], $gupshup_template_message_detail);
													}
												}
											}
										}
										?>
											<input class="form-control" type="hidden" name="gupshup_template_header" id="gupshup_template_header" value="<?php echo esc_attr(isset($template_header)?$template_header:''); ?>"/>
											<input class="form-control" type="hidden" name="gupshup_template_footer" id="gupshup_template_footer" value="<?php echo esc_attr(isset($template_footer)?$template_footer:''); ?>"/>
											<input class="form-control" type="hidden" name="gupshup_template_message" id="gupshup_template_message" value="<?php echo esc_attr(isset($template_message)?$template_message:''); ?>"/>
											<input class="form-control" type="hidden" name="gupshup_template_button_type" id="gupshup_template_button_type" value="<?php echo esc_attr(isset($template_button_type)?$template_button_type:''); ?>"/>
											<div class="form-control message-preview" name="gupshup_template_detail" id="gupshup_template_detail" style="min-height:60px; width:50%">
												<div class="form-control" name="gupshup_template_header_detail" id="gupshup_template_header_detail"><b><?php echo isset($gupshup_template_header_detail)?esc_html($gupshup_template_header_detail):''; ?></b></div>
												<div class="form-control" name="gupshup_template_message_detail" id="gupshup_template_message_detail"><?php echo isset($gupshup_template_message_detail)?esc_html($gupshup_template_message_detail):''; ?></div>
												<div class="form-control" name="gupshup_template_footer_detail" id="gupshup_template_footer_detail"><?php echo isset($template_footer)?esc_html($template_footer):''; ?></div>
											</div>
										</td>
									</tr>
								</table>
								<table class="form-table" name="gupshup_workflow_table" id="gupshup_workflow_table">
								<?php
								if (isset($header_variable_list) && count($header_variable_list)>0) {
								echo '<tr>';
								echo '<th>Header *</th>';
								echo '<td><div class="variable-table-td-div">';
									foreach ($header_variable_list as $headerVariableIndex=>$header_variable_list_value) {
										echo '<span class="variable-table-span-variable-name">' . esc_html($header_variable_list_value) . ' </span>';
										echo '<input class="form-control" type="hidden" name="gupshup_template_header_variable_name[]" id="gupshup_template_header_variable_name" data-index=' . esc_attr($headerVariableIndex) . ' value="' . esc_attr($header_variable_list_value) . '" />';
										echo '<span class="variable-table-span-variable-text"><input class="variable-table-input-variable-text" required type="text" name="gupshup_template_header_variable_text[]" id="gupshup_template_header_variable_text" data-index=' . esc_attr($headerVariableIndex) . ' value="' . esc_attr($header_variable_value[$headerVariableIndex]) . '" placeholder="Enter content for ' . esc_attr($header_variable_list_value) . '"></span>';
										echo '<span class="variable-table-span-or"> or </span>';
											echo '<span class="variable-table-span-variable-dropdown">';
												echo '<select class="variable-table-input-variable-dropdown" name="gupshup_template_header_variable_dropdown[]" id="gupshup_template_header_variable_dropdown" data-index=' . esc_attr($headerVariableIndex) . '>';
													echo '<option value=""> --Select Variable-- </option>';
													$variableFieldArray = array();
										if (isset($gupshup_trigger_type) && ''!=$gupshup_trigger_type) {
											$triggerPostType = $this->trigger_post_types_data[$gupshup_trigger_type];
											$variableFieldArray = $this->variable_fields_data[$triggerPostType];
										}
										foreach ($variableFieldArray as $variableDropdown =>$variableDropdownValue) {
											echo '<option value="' . esc_attr($variableDropdown) . '"' . selected( $header_variable_array[$headerVariableIndex], $variableDropdown ) . ' >' . esc_html($variableDropdown) . '</option>';
										}
												echo '</select>';
											echo '</span>';
										echo '<br/>';
									}
								echo '</div></td>';
								echo '</tr>';
								
								}
								?>

								<?php
								if (isset($variable_list) && count($variable_list)>0) {
								echo '<tr>';
								echo '<th>Body *</th>';
								echo '<td><div class="variable-table-td-div">';
									foreach ($variable_list as $variableIndex=>$variable_list_value) {
										echo '<span class="variable-table-span-variable-name">' . esc_html($variable_list_value) . ' </span>';
										echo '<input class="form-control" type="hidden" name="gupshup_template_variable_name[]" id="gupshup_template_variable_name" data-index=' . esc_attr($variableIndex) . ' value="' . esc_attr($variable_list_value) . '" />';
										echo '<span class="variable-table-span-variable-text"><input class="variable-table-input-variable-text" required type="text" name="gupshup_template_variable_text[]" id="gupshup_template_variable_text" data-index=' . esc_attr($variableIndex) . ' value="' . esc_attr($variable_value[$variableIndex]) . '" placeholder="Enter content for ' . esc_attr($variable_list_value) . '"></span>';
										echo '<span class="variable-table-span-or"> or </span>';
											echo '<span class="variable-table-span-variable-dropdown">';
												echo '<select class="variable-table-input-variable-dropdown" name="gupshup_template_variable_dropdown[]" id="gupshup_template_variable_dropdown" data-index=' . esc_attr($variableIndex) . '>';
													echo '<option value=""> --Select Variable-- </option>';
													$variableFieldArray = array();
										if (isset($gupshup_trigger_type) && ''!=$gupshup_trigger_type) {
											$triggerPostType = $this->trigger_post_types_data[$gupshup_trigger_type];
											$variableFieldArray = $this->variable_fields_data[$triggerPostType];
										}
										foreach ($variableFieldArray as $variableDropdown =>$variableDropdownValue) {
											echo '<option value="' . esc_attr($variableDropdown) . '"' . selected( $variable_array[$variableIndex], $variableDropdown ) . ' >' . esc_html($variableDropdown) . '</option>';
										}
												echo '</select>';
											echo '</span>';
										echo '<br/>';
									}
								echo '</div></td>';
									echo '</tr>';
								
								}
								?>
									
								
								</table>
							</div>
						</div>    
					</div>
					<p class="submit">
					
					<?php
					if ( GUPSHUP_GS_SUB_ACTION_ADD_WORKFLOW_TEMPLATES === $sub_action ) {
						print '<input type="submit" name="gupshup_workflow_submit" id="gupshup_workflow_submit" class="button-primary" value="Save and Activate"/>';
					}
					?>
					<?php
					if ( GUPSHUP_GS_SUB_ACTION_ADD_WORKFLOW_TEMPLATES === $sub_action ) {	
						print '<input type="submit" name="gupshup_workflow_submit" id="gupshup_workflow_submit" class="button-primary" value="Save"/>';
					} else if ( GUPSHUP_GS_SUB_ACTION_EDIT_WORKFLOW === $sub_action ) {
						print '<input type="submit" name="gupshup_workflow_submit" id="gupshup_workflow_submit" class="button-primary" value="Save" onclick="return confirm(\'Are you sure you want to update workflow ?\');" />';
					}
						$param       				  = array(
							'page'                    => GUPSHUP_GS_WORKFLOW_PAGE_NAME,
							'_wpnonce' => wp_create_nonce( 'gupshup-nonce-action' ),
						);
						$redirect_url = add_query_arg( $param, admin_url( '/admin.php' ));
						?>
						<a href="<?php echo esc_url( $redirect_url ); ?>" class="button-primary cancel-custom-button">Cancel</a>
					</p>
				</form>
			</div>
		<?php
	}

	/**
	 * Sanitize post array.
	 *
	 * @return array
	 */

	public function sanitize_workflow_post_data() {

		$input_post_values = array(
			'gupshup_trigger_type'            => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'gupshup_workflow_timing_unit'            => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'gupshup_workflow_name'            => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'gupshup_activate_workflow'  => array(
				'default'  => 0,
				'sanitize' => FILTER_SANITIZE_NUMBER_INT,
			),
			'gupshup_template_message'            => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING_TEXTAREA',
			),
			'gupshup_template_header'            => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING_TEXTAREA',
			),
			'gupshup_template_footer'            => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING_TEXTAREA',
			),
			'gupshup_template_button_type'            => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'gupshup_workflow_submit'            => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),

			'gupshup_template_variable_name'            => array(
				'default'  => serialize(array()),
				'sanitize' => 'FILTER_SANITIZE_ARRAY',
			),
			'gupshup_template_variable_text'            => array(
				'default'  => serialize(array()),
				'sanitize' => 'FILTER_SANITIZE_ARRAY',
			),
			'gupshup_template_variable_dropdown'            => array(
				'default'  => serialize(array()),
				'sanitize' => 'FILTER_SANITIZE_ARRAY',
			),
			'gupshup_template_header_variable_name'            => array(
				'default'  => serialize(array()),
				'sanitize' => 'FILTER_SANITIZE_ARRAY',
			),
			'gupshup_template_header_variable_text'            => array(
				'default'  => serialize(array()),
				'sanitize' => 'FILTER_SANITIZE_ARRAY',
			),
			'gupshup_template_header_variable_dropdown'            => array(
				'default'  => serialize(array()),
				'sanitize' => 'FILTER_SANITIZE_ARRAY',
			),
			
			'gupshup_template_id'            => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'gupshup_template_category'            => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'gupshup_workflow_timing'    => array(
				'default'  => 0,
				'sanitize' => FILTER_SANITIZE_NUMBER_INT,
			),
			'gupshup_is_scheduled'  => array(
				'default'  => 0,
				'sanitize' => FILTER_SANITIZE_NUMBER_INT,
			),
			'gupshup_template_media_url'  => array(
				'default'  => '',
				'sanitize' => FILTER_SANITIZE_URL,
			),
			'id'                           => array(
				'default'  => null,
				'sanitize' => FILTER_SANITIZE_NUMBER_INT,
			),
			
		);

		$sanitized_post = array();
		foreach ( $input_post_values as $key => $input_post_value ) {
			if ( isset( $_POST[ $key ] ) && isset( $_REQUEST['_wpnonce']) && wp_verify_nonce(sanitize_text_field( $_REQUEST['_wpnonce']), 'gupshup-nonce-action') ) { 
				if ( 'FILTER_SANITIZE_STRING' === $input_post_value['sanitize'] ) {
					$sanitized_post[ $key ] = GupshupGSHelper::get_instance()->sanitize_text_filter( $key, 'POST' );
				} else if ('FILTER_SANITIZE_ARRAY' === $input_post_value['sanitize']) {
					$sanitized_post[ $key ] = GupshupGSHelper::get_instance()->sanitize_text_array_filter( $key, 'POST' );
				} else if ('FILTER_SANITIZE_STRING_TEXTAREA' === $input_post_value['sanitize']) {
					$sanitized_post[ $key ] = GupshupGSHelper::get_instance()->sanitize_textarea_filter( $key, 'POST' );
				} else {
					$sanitized_post[ $key ] = filter_input( INPUT_POST, $key, $input_post_value['sanitize'] );
				}
			} else {
				$sanitized_post[ $key ] = $input_post_value['default'];
			}
		}
		return $sanitized_post;
	}

	/**
	 * Adding workflow in db
	 */
	public function add_workflow_template() {
		$sanitized_post = $this->sanitize_workflow_post_data();
		$workflow_timing = $sanitized_post['gupshup_workflow_timing'];
		if ('days'==$sanitized_post['gupshup_workflow_timing_unit']) {
			$workflow_timing=$workflow_timing*1440;
		} else if ('hours'==$sanitized_post['gupshup_workflow_timing_unit']) {
			$workflow_timing = $workflow_timing*60;
		}
		$workflow_data_array = array(
			'workflow_name'  => $sanitized_post['gupshup_workflow_name'],
			'trigger_type'  => $sanitized_post['gupshup_trigger_type'],
			'time_scheduled_after'  => $workflow_timing,
			'time_schedule_unit'  => $sanitized_post['gupshup_workflow_timing_unit'],
			'is_scheduled' => $sanitized_post['gupshup_is_scheduled'],
		);
		$workflow_data_type_array = array( '%s', '%s','%d', '%s');
		$action_data_array= array(
			'template_message'  =>$sanitized_post['gupshup_template_message'],
			'template_header'  =>$sanitized_post['gupshup_template_header'],
			'template_footer'  =>$sanitized_post['gupshup_template_footer'],
			'template_button_type'  =>$sanitized_post['gupshup_template_button_type'],
			'template_type'  => $sanitized_post['gupshup_template_category'],
			'template_id'  => $sanitized_post['gupshup_template_id'],
			'template_variable_list'  => $sanitized_post['gupshup_template_variable_name'],
			'template_variable_value'  => $sanitized_post['gupshup_template_variable_text'],
			'template_variable_array'  => $sanitized_post['gupshup_template_variable_dropdown'],
			'template_header_variable_list'  => $sanitized_post['gupshup_template_header_variable_name'],
			'template_header_variable_value'  => $sanitized_post['gupshup_template_header_variable_text'],
			'template_header_variable_array'  => $sanitized_post['gupshup_template_header_variable_dropdown'],
			'template_media_url'  => $sanitized_post['gupshup_template_media_url'],
		);
		$action_data_type_array = array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');
		if ('Save and Activate' == $sanitized_post['gupshup_workflow_submit']) {
			$workflow_data_array['is_activated']=1;
			array_push($workflow_data_type_array, '%d');
			$this->wpdb->insert(
				$this->workflow_template_table_name,
				$workflow_data_array,
				$workflow_data_type_array
			);
			$workflow_id = $this->wpdb->insert_id;
			$action_data_array['workflow_id'] = $workflow_id;
			array_push($action_data_type_array, '%d');
			$this->wpdb->insert(
				$this->action_table_name,
				$action_data_array,
				$action_data_type_array
			);
		} else if ('Save' == $sanitized_post['gupshup_workflow_submit']) {
			$this->wpdb->insert(
				$this->workflow_template_table_name,
				$workflow_data_array,
				$workflow_data_type_array
			);
			$workflow_id = $this->wpdb->insert_id;
			$action_data_array['workflow_id'] = $workflow_id;
			array_push($action_data_type_array, '%d');
			$this->wpdb->insert(
				$this->action_table_name,
				$action_data_array,
				$action_data_type_array
			);
		}

		$param        = array(
			'page'                    => GUPSHUP_GS_WORKFLOW_PAGE_NAME,
			'_wpnonce' => wp_create_nonce( 'gupshup-nonce-action' ),
		);
		$redirect_url =  add_query_arg( $param, admin_url( '/admin.php' ) );
		wp_safe_redirect( $redirect_url );
	}

	/**
	 * Updating workflow in db
	 */
	public function edit_workflow_template() {
		$sanitized_post    = $this->sanitize_workflow_post_data();
		if ('Save' == $sanitized_post['gupshup_workflow_submit']) {
		$workflow_id = $sanitized_post['id'];
		$workflow_timing = $sanitized_post['gupshup_workflow_timing'];
			if ('days' == $sanitized_post['gupshup_workflow_timing_unit']) {
				$workflow_timing=$workflow_timing*1440;
			} else if ('hours' == $sanitized_post['gupshup_workflow_timing_unit']) {
				$workflow_timing = $workflow_timing*60;
			}
		$this->wpdb->update(
			$this->workflow_template_table_name,
			array(
				'workflow_name'  => $sanitized_post['gupshup_workflow_name'],
				'trigger_type'  => $sanitized_post['gupshup_trigger_type'],
				'time_scheduled_after'  => $workflow_timing,
				'time_schedule_unit'  => $sanitized_post['gupshup_workflow_timing_unit'],
				'is_scheduled' => $sanitized_post['gupshup_is_scheduled'],
			),
			array( 'id' => $workflow_id ),
			array( '%s', '%s', '%d', '%s'),
			array( '%d' )
		);
		
		$action_data_array= array(
			'template_message'  =>$sanitized_post['gupshup_template_message'],
			'template_header'  =>$sanitized_post['gupshup_template_header'],
			'template_footer'  =>$sanitized_post['gupshup_template_footer'],
			'template_button_type'  =>$sanitized_post['gupshup_template_button_type'],
			'template_type'  => $sanitized_post['gupshup_template_category'],
			'template_id'  => $sanitized_post['gupshup_template_id'],
			'template_variable_list'  => $sanitized_post['gupshup_template_variable_name'],
			'template_variable_value'  => $sanitized_post['gupshup_template_variable_text'],
			'template_variable_array'  => $sanitized_post['gupshup_template_variable_dropdown'],
			'template_header_variable_list'  => $sanitized_post['gupshup_template_header_variable_name'],
			'template_header_variable_value'  => $sanitized_post['gupshup_template_header_variable_text'],
			'template_header_variable_array'  => $sanitized_post['gupshup_template_header_variable_dropdown'],
			'template_media_url'  => $sanitized_post['gupshup_template_media_url'],
			'workflow_id'  => $workflow_id,
		);
		$action_data_type_array = array('%s','%s','%s','%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d');
		$action_result = $this->get_action_by_workflow_id( $workflow_id );
		$action_id = $action_result->id;
			if (isset($action_id)) {
				$this->wpdb->update(
				$this->action_table_name,
				$action_data_array,
				array('id'=>$action_id),
				$action_data_type_array,
				array( '%d' )
				);
			} else {
				$this->wpdb->insert(
				$this->action_table_name,
				$action_data_array,
				$action_data_type_array
				);
			}
		}

		$param        = array(
			'page'                    => GUPSHUP_GS_WORKFLOW_PAGE_NAME,
			'_wpnonce' => wp_create_nonce( 'gupshup-nonce-action' )
		);
		$redirect_url = add_query_arg( $param, admin_url( '/admin.php' ) );
		wp_safe_redirect( $redirect_url );
	}

	

	/**
	 *  Render workflow grid.
	 */
	public function show_workflow_template_data_table() {
		$gupshup_gs_workflow_list = new GupshupGSWorkflowTemplatesTable();
		$gupshup_gs_workflow_list->prepare_items();
		$page='';
		if (isset( $_GET[ 'page' ] ) ) {
			$page = sanitize_text_field( wp_unslash( $_GET[ 'page' ] ) );
		}
		 
		?>
		<div class="wrap">
			<form id="gupshup-gs-workflow-template-table" method="GET">
				<input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>"/>
				<input type="hidden" name="action" value="<?php echo esc_attr( GUPSHUP_GS_WORKFLOW_TEMPLATES ); ?>"/>
				<input type="hidden" name="sub_action" value="<?php echo esc_attr( GUPSHUP_GS_SUB_ACTION_DELETE_BULK_WORKFLOW ); ?>"/>
				<?php 
				$gupshup_gs_workflow_list->display();
				wp_nonce_field( 'gupshup-nonce-action' ); 
				?>
			</form>
		</div>
		<?php
	}

	/**
	 *  Render 'Add Workflow button'.
	 */
	public function show_add_new_workflow_button() {
		global $wpdb;
		$param = array(
			'page'       => GUPSHUP_GS_WORKFLOW_PAGE_NAME,
			'action'     => GUPSHUP_GS_WORKFLOW_TEMPLATES,
			'sub_action' => GUPSHUP_GS_SUB_ACTION_ADD_WORKFLOW_TEMPLATES,
			'_wpnonce' => wp_create_nonce( 'gupshup-nonce-action' ),
		);

		$add_new_template_url = add_query_arg( $param, admin_url( '/admin.php' ) );
		
		$workflow_count_result = $wpdb->get_row($wpdb->prepare('SELECT count(*) as total_count, count(if(is_activated=true,1,null)) as active_count FROM %1s', $this->workflow_template_table_name));
		?>
		<div class="gupshup-gs-workflow-count-box">
			<div>Active Workflows</div>
			<div id="gupshup_active_workflow_count" name="gupshup_active_workflow_count" style="text-align:center"><?php echo esc_html($workflow_count_result->active_count); ?></div>
		</div>
		<div class="gupshup-gs-workflow-count-box">
			<div>Total Workflows</div>
			<div id="gupshup_total_workflow_count" id="gupshup_total_workflow_count" style="text-align:center"><?php echo esc_html($workflow_count_result->total_count); ?></div>
		</div>
		<a style="cursor: pointer" href="<?php echo esc_url( $add_new_template_url ); ?>" class="page-title-action"><?php echo 'Create New Workflow'; ?></a>
		<?php
	}

}

GupshupGSWorkflowTemplates::get_instance();
