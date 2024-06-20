<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Base/class-gupshup-gs-check-woocommerce.php';
$check_woocommerce = new GupshupGSCheckWoocommerce();
if (!$check_woocommerce->check_woocommerce_installed()) {
	return;
}



?>
<div id="gupshup-overlay">
  <div class="gupshup-cv-spinner">
	<span class="gupshup-spinner"></span>
  </div>
</div>
<div class="wrap">
<h1 class ="wp-heading-inline">Dashboard</h1>
	<input type="button" name="gupshup_time_filter_button" id="gupshup_time_filter_button" class="button dashboard_days_filter_button" value="7 days" style="float:right" />
	<input type="button" name="gupshup_time_filter_button" id="gupshup_time_filter_button" class="button dashboard_days_filter_button" value="14 days" style="float:right" />
	<input type="button" name="gupshup_time_filter_button" id="gupshup_time_filter_button" class="button dashboard_days_filter_button" value="30 days" style="float:right" />
	<input type="button" name="gupshup_time_filter_button" id="gupshup_time_filter_button" class="button dashboard_days_filter_button" value="90 days" style="float:right" />

  <div style="display:flex">
	<span style="width:100%">
		<div class="gupshup_chart_box">
			<div class="gupshup_chart_header_box">
				<div id="gupshup_chart_workflow_run_count" class="gupshup_chart_workflow_count_css">0</div>
				<div class="gupshup_legend_box">
					<span class="gupshup_hollow_circle"></span>
					<span>workflows run</span>
				</div>
			</div>
			<div class="gupshup_chart_content_box">
				<canvas id="gupshup_my_chart" ></canvas>
			</div>
		</div>
	</span>
	<span style="width:40%;padding-left:20px">
	<div class="gupshup_chart_box">
			<div class="gupshup_chart_header_box">
				<b>Most Run Workflows</b>
			</div>
			<div class="gupshup_dashboard_box_inside" id="gupshup_most_workflow_run_table">
			</div>
	</div>
	<div class="gupshup_chart_box">
		<div class="gupshup_dashboard_total_messages_sent_div" id="gupshup_dashboard_total_messages_sent">0
		</div>
		<div class="gupshup_dashboard_messages_sent_help_div">
			Messages Submitted
		</div>
	</div>
	</span>
</div>
</div>
