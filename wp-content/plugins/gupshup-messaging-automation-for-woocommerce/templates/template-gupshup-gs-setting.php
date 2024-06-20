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

<div class="wrap">
	<h1>Settings</h1>
	<?php settings_errors(); ?>

	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab-1">Configure Gupshup Account</a></li>
	</ul>

	<div class="tab-content">
		<div id="tab-1" class="tab-pane active">
			<form method="post" action="options.php">
				<?php
				// creating gupshup setting page
					settings_fields( 'gupshup_gs_plugin_setting_fields' );
					do_settings_sections( 'gupshup_gs_plugin' );
					submit_button();
				?>
			</form>
			
		</div>

	</div>
</div>
