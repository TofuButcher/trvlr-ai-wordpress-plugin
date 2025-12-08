<?php

/**
 * REST API Controller
 * 
 * Handles all REST API endpoints for the TRVLR plugin.
 * Organized by feature area for better maintainability.
 *
 * @package    Trvlr
 * @subpackage Trvlr/includes
 */

class Trvlr_REST_API
{
	private $namespace = 'trvlr/v1';

	/**
	 * Register all REST API routes
	 */
	public function register_routes()
	{
		$this->register_settings_routes();
		$this->register_sync_routes();
		$this->register_logs_routes();
		$this->register_setup_routes();
	}

	/**
	 * Settings Routes
	 */
	private function register_settings_routes()
	{
		// Theme settings
		register_rest_route($this->namespace, '/settings/theme', array(
			array(
				'methods' => 'GET',
				'callback' => array($this, 'get_theme_settings'),
				'permission_callback' => array($this, 'check_admin_permission'),
			),
			array(
				'methods' => 'POST',
				'callback' => array($this, 'update_theme_settings'),
				'permission_callback' => array($this, 'check_admin_permission'),
			),
		));

		// Connection settings
		register_rest_route($this->namespace, '/settings/connection', array(
			array(
				'methods' => 'GET',
				'callback' => array($this, 'get_connection_settings'),
				'permission_callback' => array($this, 'check_admin_permission'),
			),
			array(
				'methods' => 'POST',
				'callback' => array($this, 'update_connection_settings'),
				'permission_callback' => array($this, 'check_admin_permission'),
			),
		));

		// Notification settings
		register_rest_route($this->namespace, '/settings/notifications', array(
			array(
				'methods' => 'GET',
				'callback' => array($this, 'get_notification_settings'),
				'permission_callback' => array($this, 'check_admin_permission'),
			),
			array(
				'methods' => 'POST',
				'callback' => array($this, 'update_notification_settings'),
				'permission_callback' => array($this, 'check_admin_permission'),
			),
		));
	}

	/**
	 * Sync Routes
	 */
	private function register_sync_routes()
	{
		// Get sync statistics
		register_rest_route($this->namespace, '/sync/stats', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_sync_stats'),
			'permission_callback' => array($this, 'check_admin_permission'),
		));

		// Manual sync
		register_rest_route($this->namespace, '/sync/manual', array(
			'methods' => 'POST',
			'callback' => array($this, 'trigger_manual_sync'),
			'permission_callback' => array($this, 'check_admin_permission'),
		));

		// Schedule settings
		register_rest_route($this->namespace, '/sync/schedule', array(
			array(
				'methods' => 'GET',
				'callback' => array($this, 'get_schedule_settings'),
				'permission_callback' => array($this, 'check_admin_permission'),
			),
			array(
				'methods' => 'POST',
				'callback' => array($this, 'update_schedule_settings'),
				'permission_callback' => array($this, 'check_admin_permission'),
			),
		));

		// Custom edits
		register_rest_route($this->namespace, '/sync/custom-edits', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_custom_edits'),
			'permission_callback' => array($this, 'check_admin_permission'),
		));

		// Force sync settings
		register_rest_route($this->namespace, '/sync/force-sync', array(
			'methods' => 'POST',
			'callback' => array($this, 'save_force_sync_settings'),
			'permission_callback' => array($this, 'check_admin_permission'),
		));

		// Clear force sync
		register_rest_route($this->namespace, '/sync/clear-force-sync', array(
			'methods' => 'POST',
			'callback' => array($this, 'clear_force_sync_settings'),
			'permission_callback' => array($this, 'check_admin_permission'),
		));

		// Delete data
		register_rest_route($this->namespace, '/sync/delete', array(
			'methods' => 'POST',
			'callback' => array($this, 'delete_data'),
			'permission_callback' => array($this, 'check_admin_permission'),
			'args' => array(
				'include_media' => array(
					'type' => 'boolean',
					'default' => false,
				),
			),
		));
	}

	/**
	 * Logs Routes
	 */
	private function register_logs_routes()
	{
		// Get logs (grouped by sync session)
		register_rest_route($this->namespace, '/logs', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_logs'),
			'permission_callback' => array($this, 'check_admin_permission'),
			'args' => array(
				'limit' => array(
					'type' => 'integer',
					'default' => 50,
				),
				'grouped' => array(
					'type' => 'boolean',
					'default' => true,
				),
			),
		));

		// Clear old logs
		register_rest_route($this->namespace, '/logs/clear-old', array(
			'methods' => 'POST',
			'callback' => array($this, 'clear_old_logs'),
			'permission_callback' => array($this, 'check_admin_permission'),
		));

		// Clear all logs
		register_rest_route($this->namespace, '/logs/clear-all', array(
			'methods' => 'POST',
			'callback' => array($this, 'clear_all_logs'),
			'permission_callback' => array($this, 'check_admin_permission'),
		));

		// Export logs (returns CSV data for download)
		register_rest_route($this->namespace, '/logs/export', array(
			'methods' => 'GET',
			'callback' => array($this, 'export_logs'),
			'permission_callback' => array($this, 'check_admin_permission'),
			'args' => array(
				'limit' => array('type' => 'integer'),
				'type' => array('type' => 'string'),
				'date_from' => array('type' => 'string'),
				'date_to' => array('type' => 'string'),
			),
		));
	}

	/**
	 * Setup/Status Routes
	 */
	private function register_setup_routes()
	{
		// Get system status
		register_rest_route($this->namespace, '/setup/status', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_system_status'),
			'permission_callback' => array($this, 'check_admin_permission'),
		));

		// Create payment page
		register_rest_route($this->namespace, '/setup/payment-page', array(
			'methods' => 'POST',
			'callback' => array($this, 'create_payment_page'),
			'permission_callback' => array($this, 'check_admin_permission'),
		));

		// Test API connection
		register_rest_route($this->namespace, '/setup/test-connection', array(
			'methods' => 'POST',
			'callback' => array($this, 'test_api_connection'),
			'permission_callback' => array($this, 'check_admin_permission'),
		));
	}

	/**
	 * Permission Callback
	 */
	public function check_admin_permission()
	{
		$can_manage = current_user_can('manage_options');
		$user_id = get_current_user_id();

		// Log detailed request info
		error_log('TRVLR REST API: === Permission Check ===');
		error_log('TRVLR REST API: User ID: ' . $user_id);
		error_log('TRVLR REST API: Can manage: ' . ($can_manage ? 'yes' : 'no'));
		error_log('TRVLR REST API: Request URI: ' . $_SERVER['REQUEST_URI']);
		error_log('TRVLR REST API: Request Method: ' . $_SERVER['REQUEST_METHOD']);

		// Log nonce header
		$nonce_header = isset($_SERVER['HTTP_X_WP_NONCE']) ? $_SERVER['HTTP_X_WP_NONCE'] : 'NOT SET';
		error_log('TRVLR REST API: X-WP-Nonce header: ' . substr($nonce_header, 0, 10) . '...');

		// Log cookies
		$cookie_nonce = isset($_COOKIE['wordpress_logged_in_' . COOKIEHASH]) ? 'SET' : 'NOT SET';
		error_log('TRVLR REST API: WordPress cookie: ' . $cookie_nonce);

		// Verify nonce if present
		if ($nonce_header !== 'NOT SET') {
			$nonce_check = wp_verify_nonce($nonce_header, 'wp_rest');
			error_log('TRVLR REST API: Nonce verification: ' . ($nonce_check ? 'VALID' : 'INVALID'));
		}

		if (!$can_manage) {
			error_log('TRVLR REST API: Permission denied for user ' . $user_id);
		}

		return $can_manage;
	}

	// ========================================
	// SETTINGS ENDPOINTS
	// ========================================

	public function get_theme_settings($request)
	{
		$settings = get_option('trvlr_theme_settings', array());
		return rest_ensure_response($settings);
	}

	public function update_theme_settings($request)
	{
		$settings = $request->get_json_params();
		if (empty($settings) || !is_array($settings)) {
			return new WP_Error('invalid_data', 'Invalid settings data', array('status' => 400));
		}
		update_option('trvlr_theme_settings', $settings);
		return rest_ensure_response(array('success' => true, 'settings' => $settings));
	}

	public function get_connection_settings($request)
	{
		return rest_ensure_response(array(
			'organisation_id' => get_option('trvlr_organisation_id', ''),
			'api_key' => get_option('trvlr_api_key', ''),
		));
	}

	public function update_connection_settings($request)
	{
		$data = $request->get_json_params();
		if (isset($data['organisation_id'])) {
			update_option('trvlr_organisation_id', sanitize_text_field($data['organisation_id']));
		}
		if (isset($data['api_key'])) {
			update_option('trvlr_api_key', sanitize_text_field($data['api_key']));
		}
		return rest_ensure_response(array(
			'success' => true,
			'settings' => array(
				'organisation_id' => get_option('trvlr_organisation_id', ''),
				'api_key' => get_option('trvlr_api_key', ''),
			),
		));
	}

	public function get_notification_settings($request)
	{
		$settings = get_option('trvlr_notification_settings', array());
		return rest_ensure_response($settings);
	}

	public function update_notification_settings($request)
	{
		$settings = $request->get_json_params();
		if (empty($settings) || !is_array($settings)) {
			return new WP_Error('invalid_data', 'Invalid settings data', array('status' => 400));
		}
		update_option('trvlr_notification_settings', $settings);
		return rest_ensure_response(array('success' => true, 'settings' => $settings));
	}

	// ========================================
	// SYNC ENDPOINTS
	// ========================================

	public function get_sync_stats($request)
	{
		$total_attractions = wp_count_posts('trvlr_attraction')->publish;
		$custom_edit_posts = get_posts(array(
			'post_type' => 'trvlr_attraction',
			'meta_key' => '_trvlr_has_custom_edits',
			'meta_value' => '1',
			'fields' => 'ids',
			'posts_per_page' => -1,
			'post_status' => 'any'
		));
		$custom_edit_count = count($custom_edit_posts);

		return rest_ensure_response(array(
			'total_attractions' => $total_attractions,
			'synced_count' => $total_attractions - $custom_edit_count,
			'custom_edit_count' => $custom_edit_count,
		));
	}

	public function trigger_manual_sync($request)
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-field-map.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-logger.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-notifier.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'core/class-trvlr-sync.php';

		$syncer = new Trvlr_Sync();
		$syncer->sync_all();

		return rest_ensure_response(array(
			'success' => true,
			'message' => 'Sync completed successfully.',
		));
	}

	public function get_schedule_settings($request)
	{
		$sync_enabled = Trvlr_Scheduler::is_sync_enabled();
		$sync_frequency = Trvlr_Scheduler::get_sync_frequency();
		$next_sync = Trvlr_Scheduler::get_next_sync_time();

		return rest_ensure_response(array(
			'enabled' => $sync_enabled,
			'frequency' => $sync_frequency,
			'next_sync' => $next_sync ? date('Y-m-d H:i:s', $next_sync) : null,
		));
	}

	public function update_schedule_settings($request)
	{
		$data = $request->get_json_params();
		$enabled = isset($data['enabled']) && $data['enabled'];
		$frequency = isset($data['frequency']) ? sanitize_text_field($data['frequency']) : 'daily';

		if ($enabled) {
			Trvlr_Scheduler::schedule_sync($frequency);
			$next_sync = Trvlr_Scheduler::get_next_sync_time();
			return rest_ensure_response(array(
				'success' => true,
				'message' => 'Scheduled sync enabled',
				'next_sync' => $next_sync ? date('Y-m-d H:i:s', $next_sync) : null,
			));
		} else {
			Trvlr_Scheduler::unschedule_sync();
			return rest_ensure_response(array(
				'success' => true,
				'message' => 'Scheduled sync disabled',
				'next_sync' => null,
			));
		}
	}

	public function get_custom_edits($request)
	{
		$posts = get_posts(array(
			'post_type' => 'trvlr_attraction',
			'meta_key' => '_trvlr_has_custom_edits',
			'meta_value' => '1',
			'posts_per_page' => -1,
			'post_status' => 'any',
			'orderby' => 'modified',
			'order' => 'DESC'
		));

		$field_labels = Trvlr_Field_Map::get_field_labels();
		$results = array();

		foreach ($posts as $post) {
			$edited_fields = get_post_meta($post->ID, '_trvlr_edited_fields', true);
			$force_sync_fields = get_post_meta($post->ID, '_trvlr_force_sync_fields', true);

			if (!is_array($edited_fields)) $edited_fields = array();
			if (!is_array($force_sync_fields)) $force_sync_fields = array();

			$results[] = array(
				'id' => $post->ID,
				'title' => html_entity_decode(get_the_title($post->ID), ENT_QUOTES, 'UTF-8'),
				'edit_url' => get_edit_post_link($post->ID, 'raw'),
				'modified' => get_the_modified_date('Y-m-d H:i', $post->ID),
				'edited_fields' => $edited_fields,
				'force_sync_fields' => $force_sync_fields,
				'edited_fields_labels' => array_map(function ($field) use ($field_labels) {
					return isset($field_labels[$field]) ? $field_labels[$field] : $field;
				}, $edited_fields),
				'force_sync_fields_labels' => array_map(function ($field) use ($field_labels) {
					return isset($field_labels[$field]) ? $field_labels[$field] : $field;
				}, $force_sync_fields),
			);
		}

		return rest_ensure_response($results);
	}

	public function save_force_sync_settings($request)
	{
		$data = $request->get_json_params();
		$force_sync_fields = isset($data['force_sync_fields']) ? $data['force_sync_fields'] : array();

		// Clear all force sync fields first
		$all_edited = get_posts(array(
			'post_type' => 'trvlr_attraction',
			'meta_key' => '_trvlr_has_custom_edits',
			'meta_value' => '1',
			'fields' => 'ids',
			'posts_per_page' => -1,
			'post_status' => 'any'
		));

		foreach ($all_edited as $pid) {
			delete_post_meta($pid, '_trvlr_force_sync_fields');
		}

		// Set force sync fields for selected posts
		$count = 0;
		foreach ($force_sync_fields as $post_id => $fields) {
			$post_id = absint($post_id);
			$fields = array_map('sanitize_text_field', $fields);

			if (!empty($fields)) {
				update_post_meta($post_id, '_trvlr_force_sync_fields', $fields);
				$count++;
			}
		}

		return rest_ensure_response(array(
			'success' => true,
			'message' => sprintf('%d attraction(s) configured for field-level sync.', $count),
		));
	}

	public function clear_force_sync_settings($request)
	{
		$all_edited = get_posts(array(
			'post_type' => 'trvlr_attraction',
			'meta_key' => '_trvlr_has_custom_edits',
			'fields' => 'ids',
			'posts_per_page' => -1,
			'post_status' => 'any'
		));

		$count = 0;
		foreach ($all_edited as $pid) {
			if (get_post_meta($pid, '_trvlr_force_sync_fields', true)) {
				delete_post_meta($pid, '_trvlr_force_sync_fields');
				$count++;
			}
		}

		return rest_ensure_response(array(
			'success' => true,
			'message' => 'Cleared force sync settings from ' . $count . ' attraction(s).',
		));
	}

	public function delete_data($request)
	{
		$include_media = $request->get_param('include_media');

		$posts = get_posts(array(
			'post_type'   => 'trvlr_attraction',
			'numberposts' => -1,
			'post_status' => 'any'
		));

		foreach ($posts as $post) {
			if ($include_media) {
				$thumb_id = get_post_thumbnail_id($post->ID);
				if ($thumb_id) {
					wp_delete_attachment($thumb_id, true);
				}

				$gallery_ids = get_post_meta($post->ID, 'trvlr_gallery_ids', true);
				if (is_array($gallery_ids)) {
					foreach ($gallery_ids as $att_id) {
						wp_delete_attachment($att_id, true);
					}
				}
			}

			wp_delete_post($post->ID, true);
		}

		return rest_ensure_response(array(
			'success' => true,
			'message' => 'Data deleted successfully.',
		));
	}

	// ========================================
	// LOGS ENDPOINTS
	// ========================================

	public function get_logs($request)
	{
		$limit = $request->get_param('limit');
		$grouped = $request->get_param('grouped');

		if ($grouped) {
			$logs = Trvlr_Logger::get_grouped_logs($limit);
		} else {
			$type = $request->get_param('type');
			$logs = Trvlr_Logger::get_logs($limit, $type);
		}

		return rest_ensure_response($logs);
	}

	public function clear_old_logs($request)
	{
		$deleted = Trvlr_Logger::clear_old_logs(30);
		return rest_ensure_response(array(
			'success' => true,
			'message' => 'Deleted ' . $deleted . ' old log entries.',
		));
	}

	public function clear_all_logs($request)
	{
		$deleted = Trvlr_Logger::clear_all_logs();
		return rest_ensure_response(array(
			'success' => true,
			'message' => 'All logs cleared.',
		));
	}

	public function export_logs($request)
	{
		$limit = $request->get_param('limit');
		$type = $request->get_param('type');
		$date_from = $request->get_param('date_from');
		$date_to = $request->get_param('date_to');

		$csv = Trvlr_Logger::export_to_csv($limit, $type, $date_from, $date_to);

		return rest_ensure_response(array(
			'success' => true,
			'csv' => $csv,
			'filename' => Trvlr_Logger::get_csv_filename($type),
		));
	}

	// ========================================
	// SETUP ENDPOINTS
	// ========================================

	public function get_system_status($request)
	{
		$payment_page_id = get_option('trvlr_payment_page_id');
		$payment_page_exists = false;
		$payment_page_url = '';

		if ($payment_page_id) {
			$page = get_post($payment_page_id);
			if ($page && $page->post_status === 'publish') {
				$payment_page_exists = true;
				$payment_page_url = get_permalink($payment_page_id);
			}
		}

		return rest_ensure_response(array(
			'payment_page' => array(
				'exists' => $payment_page_exists,
				'url' => $payment_page_url,
				'id' => $payment_page_id,
			),
			'api_connection' => array(
				'tested' => false,
				'status' => 'not_tested',
			),
		));
	}

	public function create_payment_page($request)
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-activator.php';
		$page_id = Trvlr_Activator::create_payment_confirmation_page();

		if ($page_id) {
			return rest_ensure_response(array(
				'success' => true,
				'message' => 'Payment confirmation page created successfully.',
				'page_id' => $page_id,
				'page_url' => get_permalink($page_id),
			));
		} else {
			return new WP_Error('creation_failed', 'Failed to create payment page.', array('status' => 500));
		}
	}

	public function test_api_connection($request)
	{
		// TODO: Implement actual API connection test
		return rest_ensure_response(array(
			'success' => true,
			'message' => 'API connection test not yet implemented.',
			'status' => 'pending',
		));
	}
}
