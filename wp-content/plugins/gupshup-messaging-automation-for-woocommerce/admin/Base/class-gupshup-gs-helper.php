<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * GupshupGSHelper class.
 */
class GupshupGSHelper {

	private static $instance;
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
	}

	/**
	 * Sanitizing text
	 *
	 * @param [type] $key
	 * @param string $method
	 * @return array
	 */
	public function sanitize_text_filter( $key, $method = 'POST' ) {
		$sanitized_value = '';
		
		if ( 'POST' === $method && isset( $_POST[ $key ] ) && isset( $_REQUEST['_wpnonce']) && wp_verify_nonce(sanitize_text_field( $_REQUEST['_wpnonce']), 'gupshup-nonce-action') ) {
			$sanitized_value = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
		}

		if ( 'GET' === $method && isset( $_GET[ $key ] ) && isset( $_REQUEST['_wpnonce']) && wp_verify_nonce(sanitize_text_field( $_REQUEST['_wpnonce']), 'gupshup-nonce-action')) {
			$sanitized_value = sanitize_text_field( wp_unslash( $_GET[ $key ] ) );
		}

		return $sanitized_value;
	}

	public function sanitize_textarea_filter( $key, $method = 'POST' ) {
		$sanitized_value = '';
		if ( 'POST' === $method && isset( $_POST[ $key ] ) && isset( $_REQUEST['_wpnonce']) && wp_verify_nonce(sanitize_text_field( $_REQUEST['_wpnonce']), 'gupshup-nonce-action') ) { 
			$sanitized_value = wp_kses_post(wp_unslash( $_POST[ $key ] )); 
		}

		if ( 'GET' === $method && isset( $_GET[ $key ] ) && isset( $_REQUEST['_wpnonce']) && wp_verify_nonce(sanitize_text_field( $_REQUEST['_wpnonce']), 'gupshup-nonce-action') ) {
			$sanitized_value = wp_kses_post( wp_unslash( $_GET[ $key ] ) );
		}

		return $sanitized_value;
	}

	public function sanitize_text_array_filter( $key, $method = 'POST' ) {
		$sanitized_array = array();
		if ( 'POST' === $method && isset( $_POST[ $key ] ) && isset( $_REQUEST['_wpnonce']) && wp_verify_nonce(sanitize_text_field( $_REQUEST['_wpnonce']), 'gupshup-nonce-action') ) {
			$arr_length = count( $_POST[ $key ] );
			for ( $i = 0; $i < $arr_length; $i++ ) {
				if ( sanitize_text_field( wp_unslash( ( $_POST[ $key ] )[ $i ] ) ) !== null ) {
						$sanitized_array[ $i ] = sanitize_text_field( wp_unslash( ( $_POST[ $key ] )[ $i ] ) );
				} else {
					$sanitized_array[ $i ] = '';
				}
			}
		}

		if ( 'GET' === $method && isset( $_GET[ $key ] )&& isset( $_REQUEST['_wpnonce']) && wp_verify_nonce(sanitize_text_field( $_REQUEST['_wpnonce']), 'gupshup-nonce-action') ) {
			$arr_length = count( $_GET[ $key ] );
			for ( $i = 0; $i < $arr_length; $i++ ) {
				if ( sanitize_text_field( wp_unslash( ( $_GET[ $key ] )[ $i ] ) ) !== null ) {
						$sanitized_array[ $i ] = sanitize_text_field( wp_unslash( ( $_GET[ $key ] )[ $i ] ) );
				} else {
					$sanitized_array[ $i ] = '';
				}
			}
		}

		return serialize( $sanitized_array );
	}

}

GupshupGSHelper::get_instance();
