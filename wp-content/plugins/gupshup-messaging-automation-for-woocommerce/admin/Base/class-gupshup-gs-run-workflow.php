<?php 

include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Base/class-gupshup-gs-base-controller.php';
include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Api/GupshupAPI/class-gupshup-gs-api-manager.php';
class GupshupGSRunWorkflow extends GupshupGSBaseController {

	public $woocommerce_hooks;
	public $workflow_table_name;
	public $action_table_name;
	public $gupshup_api_manager;
	public $woocommerce_session_table_name;

	public function register() { 
		global $wpdb;
		$this->workflow_template_table_name = $wpdb->prefix . GUPSHUP_GS_WORKFLOW_TEMPLATE_TABLE;
		$this->action_table_name = $wpdb->prefix . GUPSHUP_GS_ACTION_TABLE;
		$this->abandoned_cart_table_name = $wpdb->prefix . GUPSHUP_GS_ABANDONED_CART_TABLE;
		$this->woocommerce_session_table_name = $wpdb->prefix . GUPSHUP_GS_WOOCOMMERCE_SESSION_TABLE;
		$this->woocommerce_hooks=$this->get_woocommerce_hooks();
		$gupshup_api_manager = new GupshupGSApiManager();
		$this->gupshup_api_manager = $gupshup_api_manager;

		// adding hooks to tract order creation, order status change and order delete in woocommerce
		add_action( 'woocommerce_new_order', array( $this, 'new_order_added' ), 10);
		add_action( 'woocommerce_order_status_changed', array( $this, 'order_status_updated' ), 10, 3);
		add_action( 'woocommerce_order_status_completed', array( $this, 'order_completed' ), 10);
		add_action( 'woocommerce_order_status_cancelled', array( $this, 'order_cancelled' ), 10);
		add_action( 'woocommerce_order_status_processing', array( $this, 'order_processing' ), 10);
		add_action( 'woocommerce_order_status_refunded', array( $this, 'order_refunded' ), 10);
		add_action( 'woocommerce_order_status_on-hold', array( $this, 'order_on_hold' ), 10);
		add_action( 'before_delete_post', array( $this, 'order_deleted' ), 10);
		add_action( 'gupshup_gs_abandoned_cart_trigger_action', array( $this, 'gupshup_scheduled_method' ) );
	}

	/**
	 * New Order is created in woocommerce
	 *
	 * @param [type] $order_id
	 * @return void
	 */
	public function new_order_added( $order_id) {
		$trigger_name = 'gupshup_gs_new_order_created';
		$this->send_order_workflow_message($order_id, $trigger_name);
	}

	public function send_order_workflow_message( $order_id, $trigger_name) {
		global $wpdb;
		$results_workflow = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s as action_table JOIN %2s as workflow_table ON action_table.workflow_id = workflow_table.id WHERE workflow_table.is_activated=1 AND workflow_table.is_scheduled=0 AND workflow_table.trigger_type=%s', $this->action_table_name, $this->workflow_template_table_name, $this->woocommerce_hooks[$trigger_name] )); 
		$phone = $this->formatPhoneNumberWithCountryCode(get_post_meta($order_id, '_billing_phone', true), get_post_meta($order_id, '_billing_country', true));
		$workflow_id_sent_message_map=array();
		foreach ( $results_workflow as $action ) {
			if (isset($workflow_id_sent_message_map[$action->workflow_id])) {
				$workflow_id_sent_message_map[$action->workflow_id]++;
			} else {
				$workflow_id_sent_message_map[$action->workflow_id]=1;
			}
			$action = $this->update_template_with_variables($order_id, $action);
			if (get_option('gupshup_channel_type')===GUPSHUP_GS_ENTERPRISE) {
				$this->gupshup_api_manager->send_enterprise_message($phone, $action, $trigger_name);
			}
			if (get_option('gupshup_channel_type')===GUPSHUP_GS_SELF_SERVE) {
				$this->gupshup_api_manager->send_selfserve_message($phone, $action, $trigger_name);
			}
		}
		if (count($workflow_id_sent_message_map)>0) {
			$this->gupshup_api_manager->save_workflow_log($workflow_id_sent_message_map);
		}
	}

	public function order_completed( $order_id) {
		$trigger_name = 'gupshup_gs_order_updated_completed';
		$this->send_order_workflow_message($order_id, $trigger_name);
	}

	public function order_cancelled( $order_id) {
		$trigger_name = 'gupshup_gs_order_updated_cancelled';
		$this->send_order_workflow_message($order_id, $trigger_name);
	}

	public function order_processing( $order_id) {
		$trigger_name = 'gupshup_gs_order_updated_processing';
		$this->send_order_workflow_message($order_id, $trigger_name);
	}

	public function order_refunded( $order_id) {
		$trigger_name = 'gupshup_gs_order_updated_refunded';
		$this->send_order_workflow_message($order_id, $trigger_name);
	}
	public function order_on_hold( $order_id) {
		$trigger_name = 'gupshup_gs_order_updated_on_hold';
		$this->send_order_workflow_message($order_id, $trigger_name);
	}

	/**
	 * Order Status is updated in woocommerce
	 *
	 * @param [type] $order_id
	 * @param [type] $old_status
	 * @param [type] $new_status
	 * @return void
	 */
	public function order_status_updated( $order_id, $old_status, $new_status) {
		$order_data = get_post($order_id);
		if (1!=$order_data->comment_count) {
			$trigger_name = 'gupshup_gs_order_status_updated';
			$this->send_order_workflow_message($order_id, $trigger_name);
		}
	}

	/**
	 * Order is deleted in woocommerce
	 *
	 * @param [type] $order_id
	 * @return void
	 */
	public function order_deleted( $order_id) {
		$post_type = get_post_type($order_id);
		if ('shop_order' == $post_type ) {
			$trigger_name = 'gupshup_gs_order_deleted';
			$this->send_order_workflow_message($order_id, $trigger_name);
		}
	}

	/**
	 * Updating action variable with actual value
	 *
	 * @param [type] $post_id
	 * @param [type] $action
	 * @return array $action
	 */
	public function update_template_with_variables( $post_id, $action) {
		$post_data = get_post($post_id);
		$post_meta_data = get_post_meta($post_id);
		$trigger_post_type_for_variable = $this->get_trigger_post_types()[$action->trigger_type];
		$variable_fields = ( $this->get_variable_fields() )[$trigger_post_type_for_variable];
		$product_details = wc_get_order($post_id);
		$product_data=array();
		foreach ($product_details->get_items() as $item_id => $item) {
			$product_data['gup_cart_product_names'] = $product_data['gup_cart_product_names'] . ( ''!=$product_data['gup_cart_product_names']?', ':'' ) . $item->get_name();
			$product_data['gup_cart_quantity'] = $product_data['gup_cart_quantity'] + intval($item->get_quantity());
		}
		if (!empty($variable_fields)) {
			if (isset($action->template_variable_list) && ''!=$action->template_variable_list) {
				$variable_list = unserialize($action->template_variable_list);
				$variable_value = unserialize($action->template_variable_value);
				$variable_array = unserialize($action->template_variable_array);
				
				foreach ($variable_list as $variableIndex=>$variable_list_value) {
					if (isset($variable_array[$variableIndex]) && ''!=$variable_array[$variableIndex]) {
						$variable_data = $variable_fields[$variable_array[$variableIndex]];
						if ($variable_data['is_meta']) {
							if ('postmeta'==$variable_data['table_name']) {
								$action->template_message = str_replace($variable_list_value, $post_meta_data[( $variable_data['field_name'] )][0], $action->template_message);
							}
						} else {
							if ('post'==$variable_data['table_name']) {
								$field_name = $variable_data['field_name'];
								$action->template_message = str_replace($variable_list_value, $post_data->$field_name, $action->template_message);
							} else if ('product_data'==$variable_data['table_name']) {
								$field_name = $variable_data['field_name'];
								$action->template_message = str_replace($variable_list_value, $product_data[$field_name], $action->template_message);
							}
						}
					} else {
						if (isset($variable_value[$variableIndex]) && ''!=$variable_value[$variableIndex]) {
						   $action->template_message = str_replace($variable_list_value, $variable_value[$variableIndex], $action->template_message);
						}
					}
				}
			}
			
			if (isset($action->template_header_variable_list) && ''!=$action->template_header_variable_list) {
				$header_variable_list = unserialize($action->template_header_variable_list);
				$header_variable_value = unserialize($action->template_header_variable_value);
				$header_variable_array = unserialize($action->template_header_variable_array);
				foreach ($header_variable_list as $headerVariableIndex=>$header_variable_list_value) {
					if (isset($header_variable_array[$headerVariableIndex]) && ''!=$header_variable_array[$headerVariableIndex]) {
						$variable_data = $variable_fields[$header_variable_array[$headerVariableIndex]];
						if ($variable_data['is_meta']) {
							if ('postmeta'==$variable_data['table_name']) {
								$action->template_header = str_replace($header_variable_list_value, $post_meta_data[( $variable_data['field_name'] )][0], $action->template_header);
							}
						} else {
							if ('post'==$variable_data['table_name']) {
								$field_name = $variable_data['field_name'];
								$action->template_header = str_replace($header_variable_list_value, $post_data->$field_name, $action->template_header);
							} else if ('product_data'==$variable_data['table_name']) {
								$field_name = $variable_data['field_name'];
								$action->template_message = str_replace($variable_list_value, $product_data[$field_name], $action->template_message);
							}
						}
					} else {
						if (isset($header_variable_value[$headerVariableIndex]) && ''!=$header_variable_value[$headerVariableIndex]) {
						   $action->template_header = str_replace($header_variable_list_value, $header_variable_value[$headerVariableIndex], $action->template_header);
						}
					}
				}
			}
			
		}
		return $action;
	} 

	public function gupshup_scheduled_method() {
		$this->scheduled_abandoned_cart();
		$this->schedule_update_order_trigger();
		$this->schedule_create_order_trigger();
	}

	public function schedule_create_order_trigger() {
		global $wpdb;
		$cron_time = get_option( 'gupshup_gs_cron_interval', GUPSHUP_GS_DEFAULT_CRON_INTERVAL );
		$workflow_id_sent_message_map=array();
		$trigger_name = 'gupshup_gs_new_order_created';
		
		$results_workflow = $wpdb->get_results($wpdb->prepare(
			"SELECT post_table.id as order_id, workflow_table.trigger_type, action_table.* FROM {$wpdb->prefix}posts as post_table JOIN %1s as action_table JOIN %2s as workflow_table ON action_table.workflow_id = workflow_table.id WHERE workflow_table.is_activated=1 AND workflow_table.is_scheduled=1 AND workflow_table.trigger_type=%s AND DATE_SUB(%s, INTERVAL %d MINUTE) < ADDDATE( post_table.post_date, INTERVAL IF(workflow_table.time_scheduled_after>=4305, workflow_table.time_scheduled_after-15,workflow_table.time_scheduled_after) MINUTE) AND ADDDATE( post_table.post_date, INTERVAL IF(workflow_table.time_scheduled_after>=4305, workflow_table.time_scheduled_after-15,workflow_table.time_scheduled_after) MINUTE)<=%s",
			$this->action_table_name,
			$this->workflow_template_table_name,
			$this->woocommerce_hooks[$trigger_name],
			current_time('mysql'),
			$cron_time,
			current_time('mysql')
		)); 
		foreach ( $results_workflow as $action ) {
			$order_id = $action->order_id;
			$phone = $this->formatPhoneNumberWithCountryCode(get_post_meta($order_id, '_billing_phone', true), get_post_meta($order_id, '_billing_country', true));
			if (isset($workflow_id_sent_message_map[$action->workflow_id])) {
				$workflow_id_sent_message_map[$action->workflow_id]++;
			} else {
				$workflow_id_sent_message_map[$action->workflow_id]=1;
			}
				$action = $this->update_template_with_variables($order_id, $action);
			if (get_option('gupshup_channel_type')===GUPSHUP_GS_ENTERPRISE) {
				$this->gupshup_api_manager->send_enterprise_message($phone, $action, $trigger_name);
			}
			if (get_option('gupshup_channel_type')===GUPSHUP_GS_SELF_SERVE) {
				$this->gupshup_api_manager->send_selfserve_message($phone, $action, $trigger_name);
			}
			
		}
		if (count($workflow_id_sent_message_map)>0) {
			$this->gupshup_api_manager->save_workflow_log($workflow_id_sent_message_map);
		}
	}
	
	public function schedule_update_order_trigger() {
		global $wpdb;
		$cron_time = get_option( 'gupshup_gs_cron_interval', GUPSHUP_GS_DEFAULT_CRON_INTERVAL );
		$workflow_id_sent_message_map=array();
		$trigger_type_status_map=array(
			$this->woocommerce_hooks['gupshup_gs_order_updated_processing']=>'wc-processing',
			$this->woocommerce_hooks['gupshup_gs_order_updated_on_hold']=>'wc-on-hold',
			$this->woocommerce_hooks['gupshup_gs_order_updated_completed']=>'wc-completed',
			$this->woocommerce_hooks['gupshup_gs_order_updated_cancelled']=>'wc-cancelled',
			$this->woocommerce_hooks['gupshup_gs_order_updated_refunded']=>'wc-refunded',
		);
		$like_pattern = 'Order status changed from%';
		$results_workflow = $wpdb->get_results($wpdb->prepare(
			"SELECT post_table.id as order_id, workflow_table.trigger_type, action_table.* FROM {$wpdb->prefix}posts as post_table JOIN %1s as action_table JOIN %2s as workflow_table ON action_table.workflow_id = workflow_table.id WHERE workflow_table.is_activated=1 AND workflow_table.is_scheduled=1 AND workflow_table.trigger_type IN ('Order Status Changed','Order Processing','Order On-Hold','Order Completed','Order Cancelled','Order Refunded') AND (SELECT comment_date FROM {$wpdb->prefix}comments where comment_post_ID=post_table.id AND comment_agent='WooCommerce' AND comment_type='order_note' AND comment_content LIKE %s AND DATE_SUB(%s, INTERVAL %d MINUTE) < ADDDATE( comment_date, INTERVAL IF(workflow_table.time_scheduled_after>=4305, workflow_table.time_scheduled_after-15,workflow_table.time_scheduled_after) MINUTE) AND ADDDATE( comment_date, INTERVAL IF(workflow_table.time_scheduled_after>=4305, workflow_table.time_scheduled_after-15,workflow_table.time_scheduled_after) MINUTE)<=%s ORDER BY comment_date DESC LIMIT 1) >= (SELECT comment_date FROM {$wpdb->prefix}comments where comment_post_ID=post_table.id AND comment_agent='WooCommerce' AND comment_type='order_note' AND comment_content LIKE %s ORDER BY comment_date DESC LIMIT 1)",
			$this->action_table_name,
			$this->workflow_template_table_name,
			$like_pattern,
			current_time('mysql'),
			$cron_time,
			current_time('mysql'),
			$like_pattern
		));
		foreach ( $results_workflow as $action ) {
			$order_id = $action->order_id;
			$trigger_name = $this->get_rev_woocommerce_hooks($action->trigger_type);
			$trigger_value = $action->trigger_type;
			$phone = $this->formatPhoneNumberWithCountryCode(get_post_meta($order_id, '_billing_phone', true), get_post_meta($order_id, '_billing_country', true));
			$status = get_post_status($order_id);
			if (( $trigger_value == $this->woocommerce_hooks['gupshup_gs_order_status_updated'] ) || ( $trigger_type_status_map[$trigger_value] == $status )) {
				if (isset($workflow_id_sent_message_map[$action->workflow_id])) {
					$workflow_id_sent_message_map[$action->workflow_id]++;
				} else {
					$workflow_id_sent_message_map[$action->workflow_id]=1;
				}
				$action = $this->update_template_with_variables($order_id, $action);
				if (get_option('gupshup_channel_type')===GUPSHUP_GS_ENTERPRISE) {
					$this->gupshup_api_manager->send_enterprise_message($phone, $action, $trigger_name);
				}
				if (get_option('gupshup_channel_type')===GUPSHUP_GS_SELF_SERVE) {
					$this->gupshup_api_manager->send_selfserve_message($phone, $action, $trigger_name);
				}
			}
		}
		if (count($workflow_id_sent_message_map)>0) {
			$this->gupshup_api_manager->save_workflow_log($workflow_id_sent_message_map);
		}
	}
	/**
	 * Send scheduled Abandoned Cart 
	 *
	 * @return void
	 */
	public function scheduled_abandoned_cart() {
		global $wpdb;
		$trigger_name = 'gupshup_gs_abandoned_cart';
		$cron_time = get_option( 'gupshup_gs_cron_interval', GUPSHUP_GS_DEFAULT_CRON_INTERVAL );
		$collateName = explode('COLLATE ', $wpdb->get_charset_collate())[1];
		$ss = $wpdb->query($wpdb->prepare('DELETE FROM %1s WHERE gupshup_session_key NOT IN (SELECT session_key COLLATE %2s FROM %3s)', $this->abandoned_cart_table_name, $collateName, $this->woocommerce_session_table_name ));
		$results_workflow = $wpdb->get_results($wpdb->prepare(
			'SELECT abandoned_table.*, action_table.*, (SELECT session_value COLLATE %1s FROM %2s as session_table where session_key COLLATE %3s = abandoned_table.gupshup_session_key) as cart_content FROM %4s as abandoned_table JOIN %5s as action_table JOIN %6s as workflow_table ON action_table.workflow_id = workflow_table.id WHERE workflow_table.is_activated=1 AND workflow_table.trigger_type=%s AND DATE_SUB(%s, INTERVAL %d MINUTE) < ADDDATE( abandoned_table.time, INTERVAL IF(workflow_table.time_scheduled_after>=4305, workflow_table.time_scheduled_after-15,workflow_table.time_scheduled_after) MINUTE) AND ADDDATE( abandoned_table.time, INTERVAL IF(workflow_table.time_scheduled_after>=4305, workflow_table.time_scheduled_after-15,workflow_table.time_scheduled_after) MINUTE)<=%s',
			$collateName,
			$this->woocommerce_session_table_name,
			$collateName,
			$this->abandoned_cart_table_name,
			$this->action_table_name,
			$this->workflow_template_table_name,
			$this->woocommerce_hooks[$trigger_name],
			current_time('mysql'),
			$cron_time,
			current_time('mysql')
		));
		$workflow_id_sent_message_map=array();
		foreach ( $results_workflow as $action ) {
			if (isset($action->cart_content) && unserialize(unserialize($action->cart_content)['cart']) !=null && count(unserialize(unserialize($action->cart_content)['cart']))>0) {
				if (isset($workflow_id_sent_message_map[$action->workflow_id])) {
					$workflow_id_sent_message_map[$action->workflow_id]++;
				} else {
					$workflow_id_sent_message_map[$action->workflow_id]=1;
				}
				$phone = $action->customer_phone;
				$action = $this->update_template_with_variables_abandoned_cart($action, 'abandoned_cart');
				if (get_option('gupshup_channel_type')===GUPSHUP_GS_ENTERPRISE) {
					$this->gupshup_api_manager->send_enterprise_message($phone, $action, $trigger_name);
				}
				if (get_option('gupshup_channel_type')===GUPSHUP_GS_SELF_SERVE) {
					$this->gupshup_api_manager->send_selfserve_message($phone, $action, $trigger_name);
				}
			}
		}
		if (count($workflow_id_sent_message_map)>0) {
			$this->gupshup_api_manager->save_workflow_log($workflow_id_sent_message_map);
		}
	}

	/**
	 * Update template variables of abandoned cart records
	 *
	 * @param [type] $action
	 * @param [type] $post_type
	 * @return array
	 */
	public function update_template_with_variables_abandoned_cart( $action, $post_type) {
		$variable_fields = ( $this->get_variable_fields() )[$post_type];
		$abandoned_cart_customer_data = unserialize($action->customer_data);
		$abandoned_cart_customer_data['gup_checkout_url'] = wc_get_checkout_url();
		foreach (( unserialize(unserialize($action->cart_content)['cart']) ) as $product_index => $product_data) {
			$abandoned_cart_customer_data['gup_cart_product_names'] = $abandoned_cart_customer_data['gup_cart_product_names'] . ( ''!=$abandoned_cart_customer_data['gup_cart_product_names']?', ':'' ) . get_the_title($product_data['product_id']);
			$abandoned_cart_customer_data['gup_cart_total'] = $abandoned_cart_customer_data['gup_cart_total'] + intval($product_data['line_total']);
			$abandoned_cart_customer_data['gup_cart_quantity'] = $abandoned_cart_customer_data['gup_cart_quantity'] + intval($product_data['quantity']);
		}
		
		if (!empty($variable_fields)) {
			if (isset($action->template_variable_list) && ''!=$action->template_variable_list) {
				$variable_list = unserialize($action->template_variable_list);
				$variable_value = unserialize($action->template_variable_value);
				$variable_array = unserialize($action->template_variable_array);
				
				foreach ($variable_list as $variableIndex=>$variable_list_value) {
					if (isset($variable_array[$variableIndex]) && ''!=$variable_array[$variableIndex]) {
						$variable_data = $variable_fields[$variable_array[$variableIndex]];
						if ('customer_data'==$variable_data['table_name']) {
							$field_name = $variable_data['field_name'];
							if (isset($abandoned_cart_customer_data[$field_name]) && ''!=$abandoned_cart_customer_data[$field_name]) {
								$action->template_message = str_replace($variable_list_value, $abandoned_cart_customer_data[$field_name], $action->template_message);
							}
						}
					} else {
						if (isset($variable_value[$variableIndex]) && ''!=$variable_value[$variableIndex]) {
						   $action->template_message = str_replace($variable_list_value, $variable_value[$variableIndex], $action->template_message);
						}
					}
				}
			}
			
			if (isset($action->template_header_variable_list) && ''!=$action->template_header_variable_list) {
				$header_variable_list = unserialize($action->template_header_variable_list);
				$header_variable_value = unserialize($action->template_header_variable_value);
				$header_variable_array = unserialize($action->template_header_variable_array);
				foreach ($header_variable_list as $headerVariableIndex=>$header_variable_list_value) {
					if (isset($header_variable_array[$headerVariableIndex]) && ''!=$header_variable_array[$headerVariableIndex]) {
						$variable_data = $variable_fields[$header_variable_array[$headerVariableIndex]];
						if ('customer_data'==$variable_data['table_name']) {
							$field_name = $variable_data['field_name'];
							if (isset($abandoned_cart_customer_data[$field_name]) && ''!=$abandoned_cart_customer_data[$field_name]) {
								$action->template_message = str_replace($variable_list_value, $abandoned_cart_customer_data[$field_name], $action->template_message);
							}
						}
					} else {
						if (isset($header_variable_value[$headerVariableIndex]) && ''!=$header_variable_value[$headerVariableIndex]) {
						   $action->template_header = str_replace($header_variable_list_value, $header_variable_value[$headerVariableIndex], $action->template_header);
						}
					}
				}
			}
			
		}
		return $action;
	}
}
