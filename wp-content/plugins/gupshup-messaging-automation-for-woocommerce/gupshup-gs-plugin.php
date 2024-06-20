<?php

/** 
 * Plugin Name: Gupshup Messaging Automation for WooCommerce
 * Plugin URI: http://gupshup.io
 * Description: Engage your customers across sales, marketing and support journeys conversationally on their most favourite messaging platform.
 * Version: 2.2.4
 * Requires at least:       6.1.0
 * Tested up to:            6.1.1
 * WC requires at least:    7.4.1
 * WC tested up to:         7.8.0
 * Author: Gupshup
 * Author URI: http://gupshup.io
 * Text Domain: Gupshup-Messaging-Automation-for-WooCommerce
 * Domain Path: /languages
 */

// If this file is called firectly, exit.
defined( 'ABSPATH' ) || exit;

// Defined plugin file location
define( 'GUPSHUP_GS_PLUGIN_FILE', __FILE__ );

// Loader Class
require_once 'admin/Base/class-gupshup-gs-loader.php';
