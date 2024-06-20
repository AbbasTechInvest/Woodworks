<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Base/class-gupshup-gs-check-woocommerce.php';
$check_woocommerce = new GupshupGSCheckWoocommerce();
if (!$check_woocommerce->check_woocommerce_installed()) {
	return;
}
include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Base/class-gupshup-gs-helper.php';
include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Base/class-gupshup-gs-workflow.php';
?>
<div class="wrap">
	<h1 class ="wp-heading-inline"><?php echo esc_html('Workflow'); ?></h1>
	<?php
		$helper_class = GupshupGSHelper::get_instance();
		$main_action  = $helper_class->sanitize_text_filter( 'action', 'GET' );
		$sub_action   = $helper_class->sanitize_text_filter( 'sub_action', 'GET' );
	
		$workflow_template_class_instance = GupshupGSWorkflowTemplates::get_instance();
		$workflow_template_class_instance->show_messages();
	switch ( $sub_action ) {
		case GUPSHUP_GS_SUB_ACTION_DELETE_BULK_WORKFLOW:
			$workflow_template_class_instance->delete_bulk_workflow_templates();
			break;
		case GUPSHUP_GS_SUB_ACTION_DELETE_WORKFLOW:
			$workflow_template_class_instance->delete_single_workflow_template();
			break;
		case GUPSHUP_GS_SUB_ACTION_ADD_WORKFLOW_TEMPLATES:
		case GUPSHUP_GS_SUB_ACTION_EDIT_WORKFLOW:
			$workflow_template_class_instance->render_workflow_form( $sub_action );
			break;
		case GUPSHUP_GS_SUB_ACTION_SAVE_WORKFLOW:
			$gupshup_setting_form = GupshupGSHelper::get_instance()->sanitize_text_filter( 'gupshup_setting_form', 'POST' );
			$action_id        = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT );
			if ( 'save' === $gupshup_setting_form ) {
				$workflow_template_class_instance->add_workflow_template();
			} elseif ( 'update' === $gupshup_setting_form && $action_id ) {
				$workflow_template_class_instance->edit_workflow_template();
			}
			break;
		default:
			$workflow_template_class_instance->show_add_new_workflow_button();
			$workflow_template_class_instance->show_workflow_template_data_table();
			break;
	}

	?>
</div>
