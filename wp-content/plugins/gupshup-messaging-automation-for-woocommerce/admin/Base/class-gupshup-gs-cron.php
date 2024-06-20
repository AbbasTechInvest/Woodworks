<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * GupshupGSCron class
 */
class GupshupGSCron extends GupshupGSBaseController {

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
	 *  Constructor 
	 */
	public function __construct() {
		add_filter( 'cron_schedules', array( $this, 'gupshup_gs_abandoned_cart_trigger_schedule' ) ); 

		// Schedule an action if it's not already scheduled.
		if ( ! wp_next_scheduled( 'gupshup_gs_abandoned_cart_trigger_action' ) ) {
			wp_schedule_event( time(), 'every_fifteen_minutes', 'gupshup_gs_abandoned_cart_trigger_action' );
		}
	}

		/**
		 * Create custom schedule.
		 *
		 * @param array $schedules schedules.
		 * @return mixed
		 */
	public function gupshup_gs_abandoned_cart_trigger_schedule( $schedules ) {

		/**
		 * Add filter to update cron interval time.
		 */
		$cron_time = get_option( 'gupshup_gs_cron_interval', GUPSHUP_GS_DEFAULT_CRON_INTERVAL );
		$schedules['every_fifteen_minutes'] = array(
			'interval' => intval( $cron_time ) * MINUTE_IN_SECONDS,
			'display'  => 'Every Fifteen Minutes',
		);

		return $schedules;
	}

}
GupshupGSCron::get_instance();
