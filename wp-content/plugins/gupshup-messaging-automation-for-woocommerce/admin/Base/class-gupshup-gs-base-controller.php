<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class GupshupGSBaseController {

	public $plugin_path;

	public $plugin_url;

	public $plugin;

	public function __construct() {
		$this->plugin_path = plugin_dir_path( GUPSHUP_GS_PLUGIN_FILE );
		$this->plugin_url = plugin_dir_url( GUPSHUP_GS_PLUGIN_FILE );
		$this->plugin = plugin_basename( GUPSHUP_GS_PLUGIN_FILE );
	}

	/**
	 * Hooks list used in creation of workflow
	 *
	 * @return array
	 */
	public function get_woocommerce_hooks() {
		return array(
			'gupshup_gs_new_order_created' => 'Order Create',
			'gupshup_gs_order_status_updated' => 'Order Status Changed',
			'gupshup_gs_order_updated_processing'=>'Order Processing',
			'gupshup_gs_order_updated_on_hold'=>'Order On-Hold',
			'gupshup_gs_order_updated_completed'=>'Order Completed',
			'gupshup_gs_order_updated_cancelled'=>'Order Cancelled',
			'gupshup_gs_order_updated_refunded'=>'Order Refunded',
			'gupshup_gs_order_deleted' => 'Order Delete',
			'gupshup_gs_abandoned_cart' => 'Abandoned Cart',
		);
	}

	public function get_rev_woocommerce_hooks() {
		$woocommerce_hooks = $this->get_woocommerce_hooks();
		$rev_woocommerce_hooks=array();
		foreach ($woocommerce_hooks as $key=>$value) {
			$rev_woocommerce_hooks[$value]=$key;
		}
		return $rev_woocommerce_hooks;
	}

	public function get_trigger_help_text() {
		return array(
			'Order Create'=>'Triggered when new order is created',
			'Order Status Changed'=>'Triggered when order status is changed from and to status selected by you',
			'Order Delete'=>'Triggered when new order is deleted',
			'Abandoned Cart'=>'Triggered when cart is marked abandoned',
			'Order Completed'=>'Triggered when order status is set to completed',
			'Order Cancelled'=>'Triggered when order status is set to cancelled',
			'Order Processing'=>'Triggered when order status is set to processing',
			'Order Refunded'=>'Triggered when order status is set to refunded',
			'Order On-Hold'=>'Triggered when order status is set to on hold',
		);
	}

	/**
	 * Getting gupshup cred data
	 *
	 * @return array
	 */
	public function get_configuration_details() {
		$configuration = array(
						'gupshup_channel_type' => get_option('gupshup_channel_type'),
						'gupshup_channel_name' => get_option('gupshup_channel_name'),
						'gupshup_user_id' => get_option('gupshup_user_id'),
						'gupshup_password' => get_option('gupshup_password'),
						'gupshup_business_no' => get_option('gupshup_business_no'),
						);
		return $configuration;
		
	}

	/**
	 * Formatting phone number of customer
	 *
	 * @param [type] $data
	 * @return string
	 */
	public function formatPhoneNumber( $data) {
		if (isset($data)) {
			return str_replace('+', '', $data);
		}
	}

	public function formatPhoneNumberWithCountryCode( $phone, $country_name) {
		$phone_pattern_without_country_code = '/^(?:\d{3}|\(\d{3}\))[ -]?\d{3}[ -]?\d{4}$/';
		$phone_pattern_with_country_code= '/^(?:\+?\d{1,3}[\s-]?)?(?:\(\d{3}\)|\d{3})[\s-]?\d{3}[\s-]?\d{4}$/';
		if (isset($phone) && preg_match($phone_pattern_with_country_code, $phone)) {
			if (preg_match($phone_pattern_without_country_code, $phone)) {
				if (isset($country_name)) {
					$country_code = WC()->countries->get_country_calling_code($country_name);
					return $country_code . $phone;
				}
			} else {
				return $phone;
			}	
		}
		return null;
	}

	/**
	 * Getting post type of triggers
	 *
	 * @return array
	 */
	public function get_trigger_post_types() {
		$trigger_post_type=array(
			'Order Create'=>'shop_order_create',
			'Order Status Changed'=>'shop_order_update',
			'Order Delete'=>'shop_order_delete',
			'Abandoned Cart'=>'abandoned_cart',
			'Order Completed'=>'shop_order_update',
			'Order Cancelled'=>'shop_order_update',
			'Order Processing'=>'shop_order_update',
			'Order Refunded'=>'shop_order_update',
			'Order On-Hold'=>'shop_order_update',
		);
		return $trigger_post_type;
	}

	/**
	 * Getting scheduler type triggers
	 *
	 * @return array
	 */
	public function	get_scheduling_type_trigger() {
		$scheduling_trigger = $this->get_update_order_scheduling_trigger();
		$scheduling_trigger['gupshup_gs_new_order_created']='Order Create';
		return $scheduling_trigger;
	}
	public function get_update_order_scheduling_trigger() {
		$scheduling_update_order_triggers=array(
			'gupshup_gs_order_status_updated' => 'Order Status Changed',
			'gupshup_gs_order_updated_processing'=> 'Order Processing',
			'gupshup_gs_order_updated_on_hold'=> 'Order On-Hold',
			'gupshup_gs_order_updated_completed'=> 'Order Completed',
			'gupshup_gs_order_updated_cancelled'=> 'Order Cancelled',
			'gupshup_gs_order_updated_refunded'=> 'Order Refunded',
		);
		return $scheduling_update_order_triggers;
	}

	/**
	 * Getting variable fields data list to show in creation of workflow.
	 *
	 * @return array
	 */
	public function get_variable_fields() {
		$shop_order_meta_key = array(
			'Payment Method Title'=>array(
				'table_name'=>'postmeta',
				'is_meta'=>true,
				'field_name'=>'_payment_method_title'
			),
			'Order ID'=>array(
				'table_name'=>'post',
				'field_name'=>'ID'
			),
			'Payment Method'=>array(
				'table_name'=>'postmeta',
				'is_meta'=>true,
				'field_name'=>'_payment_method'
			),
			'Billing First Name'=>array(
				'table_name'=>'postmeta',
				'is_meta'=>true,
				'field_name'=>'_billing_first_name'
			),
			'Billing Last Name'=>array(
				'table_name'=>'postmeta',
				'is_meta'=>true,
				'field_name'=>'_billing_last_name'
			),
			'Shipping First Name'=>array(
				'table_name'=>'postmeta',
				'is_meta'=>true,
				'field_name'=>'_shipping_first_name'
			),
			'Shipping Last Name'=>array(
				'table_name'=>'postmeta',
				'is_meta'=>true,
				'field_name'=>'_shipping_last_name'
			),
			'Shipping Address 1'=>array(
				'table_name'=>'postmeta',
				'is_meta'=>true,
				'field_name'=>'_shipping_address_1'
			),
			'Shipping Address 2'=>array(
				'table_name'=>'postmeta',
				'is_meta'=>true,
				'field_name'=>'_shipping_address_2'
			),
			'Shipping City'=>array(
				'table_name'=>'postmeta',
				'is_meta'=>true,
				'field_name'=>'_shipping_city'
			),
			'Shipping State'=>array(
				'table_name'=>'postmeta',
				'is_meta'=>true,
				'field_name'=>'_shipping_state'
			),
			'Shipping Postcode'=>array(
				'table_name'=>'postmeta',
				'is_meta'=>true,
				'field_name'=>'_shipping_postcode'
			),
			'Shipping Country'=>array(
				'table_name'=>'postmeta',
				'is_meta'=>true,
				'field_name'=>'_shipping_country'
			),
			'Billing Email'=>array(
				'table_name'=>'postmeta',
				'is_meta'=>true,
				'field_name'=>'_billing_email'
			),
			'Billing Phone'=>array(
				'table_name'=>'postmeta',
				'is_meta'=>true,
				'field_name'=>'_billing_phone'
			),
			'Order Total'=>array(
				'table_name'=>'postmeta',
				'is_meta'=>true,
				'field_name'=>'_order_total'
			),
			'Order Created'=>array(
				'table_name'=>'post',
				'field_name'=>'post_date'
			),
			'Order Status'=>array(
				'table_name'=>'post',
				'field_name'=>'post_status'
			),
		);
		$shop_order_meta_key_update = $shop_order_meta_key;
		$shop_order_meta_key_update['Total Order Quantity'] = array(
			'table_name'=>'product_data',
			'field_name'=>'gup_cart_quantity'
		);
		$shop_order_meta_key_update['Product Names'] = array(
			'table_name'=>'product_data',
			'field_name'=>'gup_cart_product_names'
		);
	
		$abandoned_cart_meta_key = array(
			'First Name'=>array(
				'table_name'=>'customer_data',
				'field_name'=>'gup_first_name'
			),
			'Last Name'=>array(
				'table_name'=>'customer_data',
				'field_name'=>'gup_last_name'
			),
			'Phone Number'=>array(
				'table_name'=>'customer_data',
				'field_name'=>'gup_phone_number'
			),
			'Location'=>array(
				'table_name'=>'customer_data',
				'field_name'=>'gup_location'
			),
			'Billing Company'=>array(
				'table_name'=>'customer_data',
				'field_name'=>'gup_billing_company'
			),
			'Billing Address 1'=>array(
				'table_name'=>'customer_data',
				'field_name'=>'gup_billing_address_1'
			),
			'Billing Address 2'=>array(
				'table_name'=>'customer_data',
				'field_name'=>'gup_billing_address_2'
			),
			'Billing State'=>array(
				'table_name'=>'customer_data',
				'field_name'=>'gup_billing_state'
			),
			'Billing Postcode'=>array(
				'table_name'=>'customer_data',
				'field_name'=>'gup_billing_postcode'
			),
			'Shipping First Name'=>array(
				'table_name'=>'customer_data',
				'field_name'=>'gup_shipping_first_name'
			),
			'Shipping Last Name'=>array(
				'table_name'=>'customer_data',
				'field_name'=>'gup_shipping_last_name'
			),
			'Shipping Company'=>array(
				'table_name'=>'customer_data',
				'field_name'=>'gup_shipping_company'
			),
			'Shipping Country'=>array(
				'table_name'=>'customer_data',
				'field_name'=>'gup_shipping_country'
			),
			'Shipping Address 1'=>array(
				'table_name'=>'customer_data',
				'field_name'=>'gup_shipping_address_1'
			),
			'Shipping Address 2'=>array(
				'table_name'=>'customer_data',
				'field_name'=>'gup_shipping_address_2'
			),
			'Shipping City'=>array(
				'table_name'=>'customer_data',
				'field_name'=>'gup_shipping_city'
			),
			'Shipping State'=>array(
				'table_name'=>'customer_data',
				'field_name'=>'gup_shipping_state'
			),
			'Shipping Postcode'=>array(
				'table_name'=>'customer_data',
				'field_name'=>'gup_shipping_postcode'
			),
			'Order Comments'=>array(
				'table_name'=>'customer_data',
				'field_name'=>'gup_order_comments'
			),
			'Checkout URL'=>array(
				'table_name'=>'customer_data',
				'field_name'=>'gup_checkout_url'
			),
			'Cart Total'=>array(
				'table_name'=>'customer_data',
				'field_name'=>'gup_cart_total'
			),
			'Total Cart Quantity'=>array(
				'table_name'=>'customer_data',
				'field_name'=>'gup_cart_quantity'
			),
			'Cart Product Names'=>array(
				'table_name'=>'customer_data',
				'field_name'=>'gup_cart_product_names'
			),
		);

		$all_meta_keys = array(
			'shop_order_create'=>$shop_order_meta_key,
			'shop_order_delete'=>$shop_order_meta_key,
			'shop_order_update'=>$shop_order_meta_key_update,
			'abandoned_cart'=>$abandoned_cart_meta_key
		);
		return $all_meta_keys;
	}
	 

}
