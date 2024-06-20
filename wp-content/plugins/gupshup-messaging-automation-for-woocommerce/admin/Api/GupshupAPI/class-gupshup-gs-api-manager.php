<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * GupshupGSApiManager class.
 */
class GupshupGSApiManager {

	private static $instance;
	public $gupshup_channel_details;

	public $base_controller;
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 *  Constructor function that initializes required actions and hooks.
	 */
	public function __construct() {
		include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Base/class-gupshup-gs-base-controller.php';
		$base_controller = new GupshupGSBaseController();
		$this->gupshup_channel_details = $base_controller->get_configuration_details();
		$this->base_controller=$base_controller;
	}

	/**
	 * Callback to optin the customer number for self-serve api
	 *
	 * @param [type] $phone
	 * @return void
	 */
	public function selfserve_optin( $phone) {
		$url = GUPSHUP_GS_SELF_SERVE_URL . '/v1/app/opt/in/' . $this->gupshup_channel_details['gupshup_user_id'];
		$body = array(
			'user' =>  $phone,
		);
		$header = array(
			'Content-Type'=> 'application/x-www-form-urlencoded',
			'apikey' => $this->gupshup_channel_details['gupshup_password'],
		);
		$response = wp_remote_post( $url, array(
				'method'      => 'POST',
				'headers'     => $header,
				'body'        => $body,
			)
		);
	}

	/**
	 * Callback to optin the customer number for enterprise api
	 *
	 * @param [type] $phone
	 * @return void
	 */
	public function enterprise_optin( $phone) {
		$url = GUPSHUP_GS_ENTERPRISE_WRAPPER_URL . '/gupshup';
		$body = array(
			'method'=>'OPT_IN',
			'phone_number'=> $phone,
			'userid'=> $this->gupshup_channel_details['gupshup_user_id'],
			'auth_scheme'=> 'plain',
			'password'=>$this->gupshup_channel_details['gupshup_password'],
			'v'=> '1.1',
			'format'=> 'json',
			'channel'=> 'WHATSAPP',
			'apiUrl'=> GUPSHUP_GS_ENTERPRISE_APIURL,
		);
		$header = array(
			'Content-Type'=> 'application/x-www-form-urlencoded',
		);
		$response = wp_remote_post( $url, array(
				'method'      => 'POST',
				'headers'     => $header,
				'body'        => $body,
			)
		);
	}

	/**
	 * Callback to get the templates of self-serve account
	 *
	 * @return array $template_list
	 */
	public function get_selfserve_templates() {

		$url = GUPSHUP_GS_SELF_SERVE_URL . '/v1/template/list/' . $this->gupshup_channel_details['gupshup_user_id'];
		$header = array(
			'apikey' => $this->gupshup_channel_details['gupshup_password'],
		);
		$response = wp_remote_post( $url, array(
				'method'      => 'GET',
				'headers'     => $header,
			)
		);
		
		$response_template_list=array();
		if (isset($response) && null!=( json_decode(( $response['body'] ), true) ) && !( array_key_exists('status', ( json_decode(( $response['body'] ), true) )) && 'error'==( json_decode(( $response['body'] ), true) )['status'] )) {
			$response_template_list =( json_decode(( $response['body'] ), true) )['templates'];
		}
		$template_list = array();
		foreach ($response_template_list as $template) {
			// Added only approved and TEXT templates to template list
			if ('APPROVED'===$template['status']) {
				$temp_array = array(
					'template_id'=>$template['id'],
					'template_name'=>$template['elementName'],
					'template_body'=>$template['data'],
					'template_type'=>$template['templateType'],
					'template_channel_type'=>GUPSHUP_GS_SELF_SERVE
			  );
				if (isset($template['header'])) {
					$temp_array['template_header'] = $template['header'];
				}
				if (isset($template['footer'])) {
					$temp_array['template_footer'] = $template['footer'];
				}
				array_push($template_list, $temp_array);
			}
		}
		return $template_list;
	}

	/**
	 * Callback to get the templates of enterprise account
	 *
	 * @return array $template_list
	 */
	public function get_enterprise_templates() {
		$url = GUPSHUP_GS_ENTERPRISE_WRAPPER_URL . '/gupshup?userid=' . urlencode($this->gupshup_channel_details['gupshup_user_id']) . '&password=' . urlencode($this->gupshup_channel_details['gupshup_password']) . '&method=get_whatsapp_hsm&apiUrl=' . GUPSHUP_GS_ENTERPRISE_TEMPLATE_APIURL . '&limit=1000';
		$header = array(
			'Content-Type'=> 'application/x-www-form-urlencoded',
		);
		$response = wp_remote_post( $url, array(
				'method'      => 'GET',
			)
		);
		$response_template_list=array();
		if (isset($response) && null != ( json_decode(( $response['body'] ), true) ) && !( array_key_exists('status', ( ( json_decode(( $response['body'] ), true) )['data'] )) && 'error'==( ( json_decode(( $response['body'] ), true) )['data'] )['status'] )) {
			$response_template_list =( ( json_decode(( $response['body'] ), true) )['data'] )['data'];
		}
		
		$template_list = array();
		foreach ($response_template_list as $template) {
			// Added only enabled and TEXT templates to template list
			if ('ENABLED'===$template['status']) {
				$temp_array = array(
					'template_id'=>$template['id'],
					'template_button_type'=>$template['button_type'],
					'template_name'=>$template['name'],
					'template_body'=>$template['body'],
					'template_type'=>$template['type'],
					'template_channel_type'=>GUPSHUP_GS_ENTERPRISE
				);
				if (isset($template['header'])) {
					$temp_array['template_header'] = $template['header'];
				}
				if (isset($template['footer'])) {
					$temp_array['template_footer'] = $template['footer'];
				}
				array_push($template_list, $temp_array);
			}
		}
		return $template_list;
	}
	
	/**
	 * Callback to send template messages from enterprise account
	 *
	 * @param [type] $phone
	 * @param [type] $action
	 * @param [type] $trigger_name
	 * @return void
	 */
	public function send_enterprise_message( $phone, $action, $trigger_name) {
		$optin_response = $this->enterprise_optin($phone);
		$url = GUPSHUP_GS_ENTERPRISE_WRAPPER_URL . '/gupshup';
		$toBeReplaced = ['\r','\"','\\\'','&amp;'];
		$replacingWith = ['','"','\'','&'];
		$body = array(
			'send_to'=> $phone,
			'msg_type' => $action->template_type,
			'userid'=> urlencode($this->gupshup_channel_details['gupshup_user_id']),
			'auth_scheme'=> 'plain',
			'password'=> $this->gupshup_channel_details['gupshup_password'],
			'v'=> '1.1',
			'format'=> 'json',
			'apiUrl'=>GUPSHUP_GS_ENTERPRISE_APIURL,
			'extra'=>'woocommerce'
		);
		if ('TEXT'===$action->template_type) {
			$body['method']='SendMessage';
			$body['msg']=str_replace($toBeReplaced, $replacingWith, $action->template_message);
			if (isset($action->template_button_type) && 'NONE'!=$action->template_button_type) {
				$body['isTemplate']='true';
			}
		} else if (( 'IMAGE'===$action->template_type || 'VIDEO'===$action->template_type || 'DOCUMENT'===$action->template_type )) {
			$body['method'] = 'SendMediaMessage';
			$body['caption'] = str_replace($toBeReplaced, $replacingWith, $action->template_message);
			$body['media_url'] = $action->template_media_url;
			$body['filename'] = basename($action->template_media_url);
			if (isset($action->template_button_type) && 'NONE'!==$action->template_button_type) {
				$body['isTemplate'] = 'true';
				
			}
			$body['isHSM'] = 'true';
		}
		if (isset($action->template_header) && ''!==$action->template_header) {
			$body['header']=$action->template_header;
		}
		if (isset($action->template_footer) && ''!==$action->template_footer) {
			$body['footer']=$action->template_footer;
		}
		$header = array(
			'Content-Type'=> 'application/x-www-form-urlencoded',
		);
		$response = wp_remote_post( $url, array(
				'method'      => 'POST',
				'headers'     => $header,
				'body'        => $body,
			)
		);
		$message_response_body = json_decode(( $response['body'] ), true);
		if (isset($message_response_body) && isset(( $message_response_body['data'] )['response']) && 'success' == ( ( $message_response_body['data'] )['response'] )['status']) {
			$message_data_response = ( $message_response_body['data'] )['response'];
			$message_payload = $this->create_message_payload($action);
			$this->save_messages($message_data_response['id'], $message_payload, $action->template_type, $phone, $message_data_response['status'], 'enterprise', $trigger_name);
		}
	}

	/**
	 * Callback to send template messages from self-serve account
	 *
	 * @param [type] $phone
	 * @param [type] $action
	 * @param [type] $trigger_name
	 * @return void
	 */
	public function send_selfserve_message( $phone, $action, $trigger_name) {
		$opt_in_response = $this->selfserve_optin($phone);
		$url = GUPSHUP_GS_SELF_SERVE_URL . '/v1/msg';
		$body = array();
		$message ='';
		switch ($action->template_type) {
			case 'TEXT':
				$message = '{ type: "text", text: "' . str_replace("\r", '', $action->template_message) . '" }';
				break;
			case 'IMAGE':
				$message = '{ type: "image", "originalUrl": "' . $action->template_media_url . '", caption: "' . str_replace("\r", '', $action->template_message) . '" }';
				break;
			case 'VIDEO':
				$message = '{ type: "video", "url": "' . $action->template_media_url . '", caption: "' . str_replace("\r", '', $action->template_message) . '" }';
				break;
			case 'DOCUMENT':
				$message = '{ type: "file", "url": "' . $action->template_media_url . '", filename:"' . basename($action->template_media_url) . '", caption: "' . str_replace("\r", '', $action->template_message) . '" }';
				break;
			default:
				break;
		}

		$body = array(
			'channel' => 'whatsapp',
			'source' => $this->gupshup_channel_details['gupshup_business_no'],
			'message' => $message,
			'src.name' => $this->gupshup_channel_details['gupshup_user_id'],
			'destination' => $phone,
		);
		$header = array(
			'Accept'=> 'application/json',
			'Content-Type'=> 'application/x-www-form-urlencoded',
			'apikey' => $this->gupshup_channel_details['gupshup_password'],
		);
		$response = wp_remote_post( $url, array(
				'method'      => 'POST',
				'headers'     => $header,
				'body'        => $body,
			)
		);
		$message_response_body = json_decode(( $response['body'] ), true);
		if (isset($message_response_body) && ( $message_response_body['status'] )=='submitted') {
			$message_data_response = $message_response_body;
			$message_payload = $this->create_message_payload($action);
			$this->save_messages($message_data_response['messageId'], $message_payload, $action->template_type, $phone, $message_data_response['status'], 'selfserve', $trigger_name);
		}
		
	}
	
	/**
	 * Create message payload for self-serve account
	 *
	 * @param [type] $action
	 * @return array $data
	 */
	public function create_message_payload( $action) {
		$data;
		switch ($action->template_type) {
			case 'TEXT':
				$data =array('text'=>str_replace('\r', '', $action->template_message));
				break;
			case 'IMAGE':
				$data =array(
					'caption'=>str_replace('\r', '', $action->template_message),
					'url'=>$action->template_media_url
				);
				break;
			case 'DOCUMENT':
				$data =array(
					'caption'=>str_replace('\r', '', $action->template_message),
					'name'=>$action->template_media_url
				);
				break;
			case 'VIDEO':
				$data =array(
					'caption'=>str_replace('\r', '', $action->template_message),
					'url'=>$action->template_media_url
				);
				break;
			default:
				break;
		}
		return $data;
	}

	/**
	 * Callback to save message for analytics of sent messages
	 *
	 * @param [type] $message_id
	 * @param [type] $message_payload
	 * @param [type] $message_type
	 * @param [type] $receiver
	 * @param [type] $status
	 * @param [type] $account_type
	 * @param [type] $trigger_name
	 * @return void
	 */
	public function save_messages( $message_id, $message_payload, $message_type, $receiver, $status, $account_type, $trigger_name) {
		
		$send_data = array(
			'messageId'=> $message_id,
			'application'=> GUPSHUP_GS_APP_NAME,
			'messagePayload'=> $message_payload,
			'messageType'=>$message_type,
			'receiver'=>$receiver,
			'status'=>'submitted',
			'timestamp'=> time(),
			'accountType'=> $account_type,
			'action'=>'outgoing',
			'trigger'=>$trigger_name,
		);
		if (get_option('gupshup_wp_uuid')!=null && get_option('gupshup_wp_uuid')!='') {
			$send_data['orgId'] = get_option('gupshup_wp_uuid');
		}
		$response = wp_remote_post( GUPSHUP_GS_ENTERPRISE_WRAPPER_URL . '/chat', array(
			'method'      => 'POST',
			'body'        => $send_data,
			)
		);
	}

	/**
	 * Save Workflow Logs
	 *
	 * @param [type] $workflow_id
	 * @return void
	 */
	public function save_workflow_log( $workflow_id_sent_message_map) {
		global $wpdb;
		$workflow_log_table_name = ( $wpdb->prefix ) . GUPSHUP_GS_WORKFLOW_LOG_TABLE;
		$values = array();
		$place_holders = array();
		$query = 'INSERT INTO ' . $workflow_log_table_name . ' (workflow_id, time, messages_sent) VALUES ';
		foreach ( $workflow_id_sent_message_map as $workflow_id => $messages_sent ) {
			$wpdb->query( $wpdb->prepare('INSERT INTO  %1s SET workflow_id=%d, time=%s, messages_sent=%d', $workflow_log_table_name, $workflow_id, current_time( 'mysql' ), $messages_sent));
			array_push( $values, $workflow_id, current_time( 'mysql' ), $messages_sent);
			$place_holders[] = '(%d, %s, %d)';
		}
		$query .= implode( ', ', $place_holders );
		
		//$wpdb->query( $wpdb->prepare( $query, $values ) );
	}
	
	// Callback for tracking installation for analytics
	public static function call_install_track_api( $gupshup_unique_id, $status, $timestamp = null) {
		$user_data = array(
			'app'=>'woocommerce',
			'orgId'=>$gupshup_unique_id,
			'orgName'=>get_bloginfo('name'),
			'orgDomain'=>get_bloginfo('url'),
			'email'=>get_bloginfo('admin_email'),
			'status'=>$status
		);
		if (isset($timestamp)) {
			$user_data['timestamp']=$timestamp;
		}
		$response = wp_remote_post( GUPSHUP_GS_ENTERPRISE_WRAPPER_URL . '/app/config', array(
			'method'      => 'POST',
			'body'        => $user_data,
			)
		);
	}
}

GupshupGSApiManager::get_instance();
