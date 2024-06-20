<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	include_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Workflow templates table class.
 */
class GupshupGSWorkflowTemplatesTable extends WP_List_Table {

	public $base_url;

	/**
	 *  Constructor function.
	 */
	public function __construct() {
		global $status, $page;

		parent::__construct(
			array(
				'singular' => 'id',
				'plural'   => 'ids',
			)
		);
		$this->base_url = admin_url( 'admin.php?page=' . GUPSHUP_GS_WORKFLOW_PAGE_NAME . '&action=' . GUPSHUP_GS_WORKFLOW_TEMPLATES );
		
		
	}

	public function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	/**
	 * This is how id column renders.
	 *
	 * @param  object $item item.
	 * @return HTML
	 */
	public function column_workflow_name( $item ) {

		$row_actions['edit'] = '<a href="' . 
			add_query_arg(
				array(
					'action'     => GUPSHUP_GS_WORKFLOW_TEMPLATES,
					'sub_action' => GUPSHUP_GS_SUB_ACTION_EDIT_WORKFLOW,
					'id'         => $item['id'],
					'_wpnonce' => wp_create_nonce( 'gupshup-nonce-action' ),
				),
				$this->base_url
			)
		. '">Edit</a>';

		$row_actions['delete'] = '<a onclick="return confirm(\'Are you sure to delete this workflow?\');" href="' . 
			add_query_arg(
				array(
					'action'     => GUPSHUP_GS_WORKFLOW_TEMPLATES,
					'sub_action' => GUPSHUP_GS_SUB_ACTION_DELETE_WORKFLOW,
					'id'         => $item['id'],
					'_wpnonce' => wp_create_nonce( 'gupshup-nonce-action' )
				),
				$this->base_url
			)
		. '">Delete</a>';

		return sprintf( '%s %s', esc_html( $item['workflow_name'] ), $this->row_actions( $row_actions ) );
	}

	/**
	 * This is how checkbox column renders.
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="id[]" value="%s" />', esc_html( $item['id'] ) );
	}

	public function get_bulk_actions() {
		$actions = array(
			GUPSHUP_GS_WORKFLOW_TEMPLATES => 'Delete',
		);
		return $actions;
	}

	/**
	 * Whether the table has items to display or not
	 *
	 * @return bool
	 */
	public function has_items() {
		return ! empty( $this->items );
	}

	/**
	 * Fetch data from the database to render on view.
	 */
	public function prepare_items() {
		global $wpdb;
		$workflow_template_table_name = $wpdb->prefix . GUPSHUP_GS_WORKFLOW_TEMPLATE_TABLE;
		$workflow_log_table_name = $wpdb->prefix . GUPSHUP_GS_WORKFLOW_LOG_TABLE;

		$per_page = 10;

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->process_bulk_action();

		$total_items = $wpdb->get_var( $wpdb->prepare('SELECT COUNT(id) FROM %1s', $workflow_template_table_name )); 
		$paged        = filter_input( INPUT_GET, 'paged', FILTER_SANITIZE_NUMBER_INT );
		$helper_class = GupshupGSHelper::get_instance();
		$orderby      = $helper_class->sanitize_text_filter( 'orderby', 'GET' );
		$order        = $helper_class->sanitize_text_filter( 'order', 'GET' );

		$orderby = strtolower( str_replace( ' ', '_', $orderby ) );

		$paged   = $paged ? max( 0, $paged - 1 ) : 0;
		$orderby = ( $orderby && in_array( $orderby, array_keys( $this->get_sortable_columns() ), true ) ) ? $orderby : 'id';
		$order   = ( $order && in_array( $order, array( 'asc', 'desc' ), true ) ) ? $order : 'desc';

		// configure pagination
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
		$this->items = $wpdb->get_results(
			$wpdb->prepare('SELECT id, workflow_name, trigger_type, is_activated, (Select Count(*) From %1s as workflow_log_table where workflow_log_table.workflow_id=workflow_table.id) as workflow_run_count FROM %2s as workflow_table ORDER BY %s %s LIMIT %d OFFSET %d', $workflow_log_table_name, $workflow_template_table_name, $orderby, $order, $per_page, $paged * $per_page ),
			ARRAY_A
		);
	}

	/**
	 * Table columns.
	*/
	public function get_columns() {
		$columns = array(
			'cb'            => '<input type="checkbox" />',
			'workflow_name' => 'Title',
			'trigger_type'  => 'Trigger',
			'workflow_run_count'  => 'Run Count',
			'is_activated' => 'Status'
		);
		return $columns;
	}

	protected function column_is_activated( $item ) {
		global $wpdb;
		if ( isset( $item['id'] ) ) {
			$id = $item['id'];
		}
		$is_activated  = '';
		$active_status = 0;
		if ( $item && isset( $item['is_activated'] ) ) {
			$active_status = stripslashes( $item['is_activated'] );
			$is_activated  = $active_status ? 'on' : 'off';

		}
		print '<button type="button" id="' . esc_attr( $id ) . '" class="gupshup-gs-switch gupshup-toggle-workflow-status gupshup-switch-grid"  gupshup-gs-workflow-switch="' . esc_attr( $is_activated ) . '"> ' . esc_attr( $is_activated ) . ' </button>';
		print '<input type="hidden" name="gupshup_activate_workflow" id="gupshup_activate_workflow" value="' . esc_attr( $active_status ) . '" />';
	}
	/**
	 * Table sortable columns.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable = array(
			'id'            => array( 'id', true ),
			'workflow_name' => array( 'Workflow Name', true ),
			'workflow_run_count'  => array( 'Run Count', true ),
			'trigger_type' => array( 'Trigger Name', true ),
		);
		return $sortable;
	}
	
	/**
	 * Processes bulk actions
	 *
	 * @return void
	 */
	public function process_bulk_action() {

		global $wpdb;
		$workflow_table_name = $wpdb->prefix . GUPSHUP_GS_WORKFLOW_TEMPLATE_TABLE;
		$action_table_name = $wpdb->prefix . GUPSHUP_GS_ACTION_TABLE;
		$sub_action     = GupshupGSHelper::get_instance()->sanitize_text_filter( 'sub_action', 'GET' );
		$action     = GupshupGSHelper::get_instance()->sanitize_text_filter( 'action', 'GET' );
		if ( GUPSHUP_GS_SUB_ACTION_DELETE_BULK_WORKFLOW === $sub_action && GUPSHUP_GS_WORKFLOW_TEMPLATES===$action ) {
			$ids        = array();
			$key ='id';
			$sanitized_ids_array = array();
			if (isset( $_REQUEST[ $key ] ) ) {
				$ids_arr_length = count( $_REQUEST[ $key ] );
				for ( $i = 0; $i < $ids_arr_length; $i++ ) {
					if ( sanitize_text_field( wp_unslash( ( $_REQUEST[ $key ] )[ $i ] ) ) !== null ) {
							$sanitized_ids= sanitize_text_field( wp_unslash( ( $_REQUEST[ $key ] )[ $i ] ) );
							$wpdb->query( $wpdb->prepare('DELETE FROM %1s WHERE id=%d', $workflow_table_name, $sanitized_ids));
							$wpdb->query( $wpdb->prepare('DELETE FROM %1s WHERE workflow_id=%d', $action_table_name, $sanitized_ids));
					}
				}
			}
			/*$ids = implode( ',', $sanitized_ids_array );
			if ( ! empty( $ids ) ) {
				$wpdb->query( $wpdb->prepare("DELETE FROM %1s WHERE id IN($ids)", $workflow_table_name));
				$wpdb->query( $wpdb->prepare("DELETE FROM %1s WHERE workflow_id IN($ids)", $action_table_name));
			}*/
		}

	}
	
}
