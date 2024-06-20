<?php

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;



global $wpdb;

$gupshup_table_to_drop = array(
	'gupshup_gs_workflow',
	'gupshup_gs_action',
	'gupshup_gs_workflow_log',
	'gupshup_gs_abandoned_cart',
);

// Clear Database stored data
foreach ( $gupshup_table_to_drop as $table ) {
	$wpdb->get_results( $wpdb->prepare('DROP TABLE IF EXISTS %1s', ( $wpdb->prefix ) . ( $table )));
}


//Callback for uninstalling analytics
$gupshup_unique_id = uniqid(wp_rand()) . '-' . current_time('timestamp');
if ( get_option( 'gupshup_wp_uuid' ) !=null && get_option( 'gupshup_wp_uuid' )!='') {
		$gupshup_unique_id = get_option('gupshup_wp_uuid');
}
wp_clear_scheduled_hook( 'gupshup_gs_abandoned_cart_trigger_action' );
$user_data = array(
			'app'=>'woocommerce',
			'orgId'=>$gupshup_unique_id,
			'orgName'=>get_bloginfo('name'),
			'orgDomain'=>get_bloginfo('url'),
			'email'=>get_bloginfo('admin_email'),
			'status'=>'uninstalled'
		);
		$response = wp_remote_post( 'https://integration-apis.gupshup.io/wrapperServices/v1', array(
			'method'      => 'POST',
			'body'        => $user_data,
			)
		);
		$options = array(
			'gupshup_channel_name',
			'gupshup_user_id',
			'gupshup_password',
			'gupshup_business_no',
			'gupshup_channel_type',
			'gupshup_plugin_version',
		);
		
		// Delete all options data.
		foreach ( $options as $index => $key ) {
			delete_option( $key );
		}




