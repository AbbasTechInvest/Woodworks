<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 * GupshupGSAbandonedCartTrack class.
 */
class GupshupGSAbandonedCartTrack {



	/*
	 * Member Variable
	 *
	 * @var object instance
	 */
	private static $instance;

	/*
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/*
	 *  Constructor function
	 */
	public function __construct() {
		include_once plugin_dir_path(GUPSHUP_GS_PLUGIN_FILE) . 'admin/Base/class-gupshup-gs-helper.php';
		
			// Add script to track the cart abandonment.
			add_action( 'woocommerce_after_checkout_form', array( $this, 'abandoned_cart_script' ) );

			// Store user details from the current checkout page.
			add_action( 'wp_ajax_gupshup_gs_save_abandoned_cart_data', array( $this, 'save_abandoned_cart_data' ) );
			add_action( 'wp_ajax_nopriv_gupshup_gs_save_abandoned_cart_data', array( $this, 'save_abandoned_cart_data' ) );
	}

		/**
		 *  Initialise all the constants
		 */
	

	
	
	

	/**
	 * Load abandoned cart script.
	 *
	 * @return void
	 */
	public function abandoned_cart_script() {

		wp_enqueue_script(
			'gupshup-gs-abandoned-cart',
			plugin_dir_url(GUPSHUP_GS_PLUGIN_FILE) . 'admin/assets/gupshup-gs-abandoned-cart.js',
			array( 'jquery' ),
			'1.0.0', // version number
			true
		);
		$vars = array(
			'ajaxurl'                   => admin_url( 'admin-ajax.php' ),
			'gupshup_nonce_action'      => wp_create_nonce( 'gupshup-nonce-action' )
		);

		wp_localize_script( 'gupshup-gs-abandoned-cart', 'gupshup_gs_cart_vars', $vars );

	}

	


	

	/**
	 * Sanitize post array.
	 *
	 * @return array
	 */
	public function sanitize_post_data() {

		$input_post_values = array(
			'gup_billing_company'     => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'gup_email'               => array(
				'default'  => '',
				'sanitize' => FILTER_SANITIZE_EMAIL,
			),
			'gup_billing_address_1'   => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'gup_billing_address_2'   => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'gup_billing_state'       => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'gup_billing_postcode'    => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'gup_shipping_first_name' => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'gup_shipping_last_name'  => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'gup_shipping_company'    => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'gup_shipping_country'    => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'gup_shipping_address_1'  => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'gup_shipping_address_2'  => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'gup_shipping_city'       => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'gup_shipping_state'      => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'gup_shipping_postcode'   => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'gup_order_comments'      => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'gup_name'                => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'gup_surname'             => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'gup_phone'               => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'gup_country'             => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'gup_city'                => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
		);
		
		$sanitized_post = array();
		foreach ( $input_post_values as $key => $input_post_value ) {

			if ( isset( $_POST[ $key ] ) && isset( $_REQUEST['_wpnonce']) && wp_verify_nonce(sanitize_text_field( $_REQUEST['_wpnonce']), 'gupshup-nonce-action') ) {
				if ( 'FILTER_SANITIZE_STRING' === $input_post_value['sanitize'] ) {
					$sanitized_post[ $key ] = GupshupGSHelper::get_instance()->sanitize_text_filter( $key, 'POST' );
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
	 * Save cart abandonment customers data and session_key.
	 *
	 * @return void
	 */
	public function save_abandoned_cart_data() {
		$post_data = $this->sanitize_post_data();
		if ( isset( $post_data['gup_phone'] ) ) {
			$user_email = sanitize_email( $post_data['wcf_email'] );
			global $wpdb;
			$abandoned_cart_table = $wpdb->prefix . GUPSHUP_GS_ABANDONED_CART_TABLE;
			$session_key = WC()->session->get_customer_id();
			
			if (isset($session_key)) {
				$session_cart_details =$this->get_abandoned_cart_detail_by_session_key( $session_key );
				$customer_data = $this->get_customer_data( $post_data );
				$cart_total = WC()->cart->total;
				$cart_data = array(
					'customer_phone'=>( new GupshupGSBaseController() )->formatPhoneNumberWithCountryCode($customer_data['gup_phone_number'], $customer_data['gup_billing_country']),
					'customer_data'=>serialize($customer_data),
					'time'=>current_time('mysql'),
					'gupshup_session_key'=>$session_key,
				);
				
				if (isset($cart_total) && $cart_total>0) {
					if (( !is_null($session_key) ) && !is_null($session_cart_details)) {
						$wpdb->update(
							$abandoned_cart_table,
							$cart_data,
							array( 'gupshup_session_key' => $session_key )
						);
					} else {
						// Inserting row into Database.
						$wpdb->insert(
							$abandoned_cart_table,
							$cart_data
						);
					}
				} else {
					$wpdb->delete( $abandoned_cart_table, array( 'gupshup_session_key' => sanitize_key( $session_key ) ) );
				}
			}
		}
		wp_send_json_success();
	}

	/**
	 * Return abandoned cart data using session_key
	 *
	 * @param [type] $session_key
	 * @return array
	 */
	public function get_abandoned_cart_detail_by_session_key( $session_key ) {
		global $wpdb;
		$abandoned_cart_table = $wpdb->prefix . GUPSHUP_GS_ABANDONED_CART_TABLE;
		$result                 = $wpdb->get_row(
			$wpdb->prepare('SELECT * FROM %1s
			WHERE gupshup_session_key = %s',
			$abandoned_cart_table,
			$session_key )
		);
		return $result;
	}
	/**
	 * Add customer data to save in abandoned cart.
	 *
	 * @param array $post_data post data.
	 * @return array
	 */
	public function get_customer_data( $post_data = array() ) {

		
			$customer_data = array(
				'gup_billing_company'     => $post_data['gup_billing_company'],
				'gup_billing_address_1'   => $post_data['gup_billing_address_1'],
				'gup_billing_address_2'   => $post_data['gup_billing_address_2'],
				'gup_billing_state'       => $post_data['gup_billing_state'],
				'gup_billing_postcode'    => $post_data['gup_billing_postcode'],
				'gup_shipping_first_name' => $post_data['gup_shipping_first_name'],
				'gup_shipping_last_name'  => $post_data['gup_shipping_last_name'],
				'gup_shipping_company'    => $post_data['gup_shipping_company'],
				'gup_shipping_country'    => $post_data['gup_shipping_country'],
				'gup_shipping_address_1'  => $post_data['gup_shipping_address_1'],
				'gup_shipping_address_2'  => $post_data['gup_shipping_address_2'],
				'gup_shipping_city'       => $post_data['gup_shipping_city'],
				'gup_shipping_state'      => $post_data['gup_shipping_state'],
				'gup_shipping_postcode'   => $post_data['gup_shipping_postcode'],
				'gup_order_comments'      => $post_data['gup_order_comments'],
				'gup_first_name'          => $post_data['gup_name'],
				'gup_last_name'           => $post_data['gup_surname'],
				'gup_phone_number'        => $post_data['gup_phone'],
				'gup_billing_country'     => $post_data['gup_country'],
				'gup_location'            => $post_data['gup_country'] . ', ' . $post_data['gup_city'],
			);
		
		return $customer_data;
	}

}

GupshupGSAbandonedCartTrack::get_instance();
