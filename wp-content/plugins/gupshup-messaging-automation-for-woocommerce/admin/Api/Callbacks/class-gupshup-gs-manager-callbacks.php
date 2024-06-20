<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Base/class-gupshup-gs-base-controller.php';

/**
 * Class having callback method of setting page
 */
class GupshupGSManagerCallbacks extends GupshupGSBaseController {

	public $test_text=array();
	public function checkboxSanitize( $input ) {
		return $input;
	}

	public function gupshupChannelSettingSectionManager() {
		echo '<div><b>' . esc_html('Note: Updating account information may affect existing workflows. Please proceed with caution.') . '</b></div>';
	}

	
	public function gupshupChannelName() {
		$value = get_option( 'gupshup_channel_name' ) ;
		echo '<input type="text" required pattern="([A-Za-z0-9_]+)" class="regular-text" name="gupshup_channel_name" id="gupshup_channel_name" value="' . esc_attr($value) . '">';
		echo '<span class="help-block">' . esc_html('Give your channel a name. channel name can only contain letters, numbers and underscores.') . '</span>';
	}
	public function gupshupUserId() {
		$value = get_option( 'gupshup_user_id' ) ;
		echo '<input type="text" required class="regular-text" name="gupshup_user_id" id="gupshup_user_id" value="' . esc_attr($value) . '">';
		if (get_option( 'gupshup_channel_type' )=='enterprise') {
			echo '<span id="gupshup_user_id_help_text" class="help-block">' . esc_html('The HSM user ID of your gupshup enterprise account.') . '</span>';
		} else {
			echo '<span id="gupshup_user_id_help_text" class="help-block">' . esc_html('The name of the app created in your Gupshup account. E.g., demoapp. If you don’t have an app created yet, log in to your Gupshup.io account and navigate to Dashboard > WhatsApp. Then, click on the + icon and click the Access API button. Enter the name of the app without spaces or any special characters and finish the rest of the procedures.') . '</span>';
		}
	}

	public function gupshupPassword() {
		$value = get_option( 'gupshup_password' ) ;
		echo '<input type="password" required class="regular-text" name="gupshup_password" id="gupshup_password" value="' . esc_attr($value) . '">';
		if (get_option( 'gupshup_channel_type' )=='enterprise') {
			echo '<span id="gupshup_password_help_text" class="help-block">' . esc_html('The password associated with the HSM user ID.') . '</span>';
		} else {
			echo '<span id="gupshup_password_help_text" class="help-block">' . esc_html('To get the API key of the app specified,  log in to your Gupshup.io account  and navigate to Dashboard and click the Settings icon beside the app name. Scroll down the window and look for the API key under the Request code snippet.') . '</span>';
		}
	}
	public function gupshupBusinessNo() {
		$value = get_option( 'gupshup_business_no' ) ;
		echo '<input type="text" pattern="([+]?\d{1,2})?(\d{10})" required class="regular-text" name="gupshup_business_no" id="gupshup_business_no" value="' . esc_attr($value) . '">';
		echo '<span class="help-block">' . esc_html( 'Enter whatsapp business number associated with your gupshup account i.e. country code + 10 digit gupshup whatsapp business number.' ) . '</span>';
	}
	public function gupshupChannelType() {
		$value = esc_attr( get_option( 'gupshup_channel_type' ) );
		echo '<select name="gupshup_channel_type" required id="gupshup_channel_type">';
		echo '<option ' . selected($value, esc_attr(GUPSHUP_GS_SELF_SERVE), false) . ' value="' . esc_attr(GUPSHUP_GS_SELF_SERVE) . '">' . esc_html('Self-serve') . '</option>';
		echo '<option ' . selected($value, esc_attr(GUPSHUP_GS_ENTERPRISE), false) . ' value="' . esc_attr(GUPSHUP_GS_ENTERPRISE) . '">' . esc_html('Enterprise') . '</option>';
		echo '</select>';
		echo '<span class="help-block">' . esc_html('Select your gupshup account type.') . '</span>';
	}
	public function gupshupRegisterLink() {
		echo '<span>Not a Gupshup User yet?  <span><span><a href="' . esc_url('https://www.gupshup.io/whatsapp/dashboard') . '" target="_blank">Register Now</a></span>';
	}
	
}
