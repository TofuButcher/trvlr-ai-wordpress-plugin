<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Trvlr
 * @subpackage Trvlr/admin
 */

class Trvlr_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version           The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/trvlr-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/trvlr-admin.js', array('jquery'), $this->version, false);

		wp_localize_script($this->plugin_name, 'trvlr_admin_vars', array(
			'nonce' => wp_create_nonce('trvlr_admin_nonce')
		));
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu()
	{

		/*
		 * Add a top-level settings page for TRVLR
		 */
		add_menu_page(
			__('TRVLR Settings', 'trvlr'),       // Page title
			__('TRVLR', 'trvlr'),                // Menu title
			'manage_options',                       // Capability
			'trvlr_settings',                       // Menu slug
			array($this, 'display_plugin_settings_page'), // Callback function
			'dashicons-location-alt',               // Icon
			30                                      // Position
		);

		// Add Settings submenu (same page, just for better UX)
		add_submenu_page(
			'trvlr_settings',
			__('Settings', 'trvlr'),
			__('Settings', 'trvlr'),
			'manage_options',
			'trvlr_settings'
		);
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_settings_page()
	{
		include_once 'partials/trvlr-settings-main.php';
	}

	/**
	 * Register meta boxes (Delegates to meta-fields.php which we include here)
	 */
	public function init_meta_boxes()
	{
		require_once plugin_dir_path(__FILE__) . 'meta-fields.php';
		// The require executes the add_action calls inside meta-fields.php
	}

	/**
	 * Register settings, sections and fields.
	 *
	 * @since    1.0.0
	 */
	public function register_settings()
	{
		// Core settings (traditional form + REST API)
		register_setting('trvlr_settings_group', 'trvlr_api_key', array(
			'type' => 'string',
			'default' => '',
			'show_in_rest' => true,
		));

		register_setting('trvlr_settings_group', 'trvlr_organisation_id', array(
			'type' => 'string',
			'default' => '',
			'show_in_rest' => true,
		));

		// Notification settings for React component
		register_setting('trvlr_settings_group', 'trvlr_notification_settings', array(
			'type' => 'object',
			'default' => array(),
			'show_in_rest' => array(
				'schema' => array(
					'type' => 'object',
					'properties' => array(
						'email' => array('type' => 'string'),
						'notify_errors' => array('type' => 'boolean'),
						'notify_complete' => array('type' => 'boolean'),
						'notify_weekly' => array('type' => 'boolean'),
					),
				),
			),
		));

		// Theme settings for React component (must be registered with proper REST schema)
		register_setting('trvlr_theme_settings', 'trvlr_theme_settings', array(
			'type' => 'object',
			'default' => array(),
			'sanitize_callback' => array($this, 'sanitize_theme_settings'),
			'show_in_rest' => array(
				'name' => 'trvlr_theme_settings',
				'schema' => array(
					'type' => 'object',
					'properties' => array(
						'primaryColor' => array('type' => 'string', 'default' => 'hsl(245, 90%, 50%)'),
						'primaryActiveColor' => array('type' => 'string', 'default' => 'hsl(245, 100%, 40%)'),
						'accentColor' => array('type' => 'string', 'default' => 'hsl(57, 100%, 50%)'),
						'textMutedColor' => array('type' => 'string', 'default' => 'hsl(0, 0%, 40%)'),
						'headingColor' => array('type' => 'string', 'default' => 'hsl(0, 0%, 0%)'),
						'cardBackground' => array('type' => 'string', 'default' => 'transparent'),
						'headingLetterSpacing' => array('type' => 'number', 'default' => -0.04),
						'attractionGridGap' => array('type' => 'number', 'default' => 40),
						'attractionGridRowGap' => array('type' => 'number', 'default' => 80),
						'cardPadding' => array('type' => 'number', 'default' => 4),
						'cardBorderRadius' => array('type' => 'number', 'default' => 8),
						'cardImageBorderRadius' => array('type' => 'number', 'default' => 8),
						'popularBadgeColor' => array('type' => 'string', 'default' => '#fff'),
						'popularBadgeBackground' => array('type' => 'string', 'default' => '#000'),
						'popularBadgeFontSize' => array('type' => 'number', 'default' => 16),
					),
				),
			),
		));
	}

	/**
	 * Sanitize theme settings
	 */
	public function sanitize_theme_settings($settings)
	{
		if (!is_array($settings)) {
			return array();
		}

		return $settings;
	}

	/**
	 * Register custom REST API endpoints for theme settings
	 */
	public function register_theme_rest_routes()
	{
		register_rest_route('trvlr/v1', '/theme-settings', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_theme_settings_rest'),
			'permission_callback' => function () {
				return current_user_can('manage_options');
			},
		));

		register_rest_route('trvlr/v1', '/theme-settings', array(
			'methods' => 'POST',
			'callback' => array($this, 'update_theme_settings_rest'),
			'permission_callback' => function () {
				return current_user_can('manage_options');
			},
		));
	}

	/**
	 * REST API: Get theme settings
	 */
	public function get_theme_settings_rest($request)
	{
		$settings = get_option('trvlr_theme_settings', array());
		return rest_ensure_response($settings);
	}

	/**
	 * REST API: Update theme settings
	 */
	public function update_theme_settings_rest($request)
	{
		$settings = $request->get_json_params();

		if (empty($settings) || !is_array($settings)) {
			return new WP_Error('invalid_data', 'Invalid settings data', array('status' => 400));
		}

		update_option('trvlr_theme_settings', $settings);

		return rest_ensure_response(array(
			'success' => true,
			'settings' => $settings,
		));
	}

	/**
	 * AJAX Handler: Manual Sync
	 */
	public function ajax_manual_sync()
	{
		check_ajax_referer('trvlr_admin_nonce', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error('Insufficient permissions.');
		}

		// Load dependencies
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-field-map.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-logger.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-notifier.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'core/class-trvlr-sync.php';

		$syncer = new Trvlr_Sync();
		$syncer->sync_all();

		wp_send_json_success('Sync completed successfully.');
	}

	/**
	 * AJAX Handler: Delete All Data (Posts + Images)
	 */
	public function ajax_delete_all_data()
	{
		$this->process_deletion(true); // True = Delete Images too
	}

	/**
	 * AJAX Handler: Delete Posts Only (Keep Images)
	 */
	public function ajax_delete_posts_only()
	{
		$this->process_deletion(false); // False = Keep Images
	}

	/**
	 * Helper for deletion
	 */
	private function process_deletion($delete_media = false)
	{
		check_ajax_referer('trvlr_admin_nonce', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error('Insufficient permissions.');
		}

		$posts = get_posts(array(
			'post_type'   => 'trvlr_attraction',
			'numberposts' => -1,
			'post_status' => 'any'
		));

		foreach ($posts as $post) {
			if ($delete_media) {
				// Find all media attached to this post (Featured + Gallery)
				// 1. Featured Image
				$thumb_id = get_post_thumbnail_id($post->ID);
				if ($thumb_id) {
					wp_delete_attachment($thumb_id, true);
				}

				// 2. Gallery Images (from our meta)
				$gallery_ids = get_post_meta($post->ID, 'trvlr_gallery_ids', true);
				if (is_array($gallery_ids)) {
					foreach ($gallery_ids as $att_id) {
						wp_delete_attachment($att_id, true);
					}
				}
			}

			wp_delete_post($post->ID, true);
		}

		wp_send_json_success('Data deleted.');
	}

	/**
	 * AJAX Handler: Create Payment Confirmation Page
	 */
	public function ajax_create_payment_page()
	{
		check_ajax_referer('trvlr_admin_nonce', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error('Insufficient permissions.');
		}

		// Use the same method from activator
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-activator.php';

		$page_id = Trvlr_Activator::create_payment_confirmation_page();

		if ($page_id) {
			wp_send_json_success(array(
				'message' => 'Payment confirmation page created successfully.',
				'page_id' => $page_id,
				'page_url' => get_permalink($page_id)
			));
		} else {
			wp_send_json_error('Failed to create payment page.');
		}
	}

	/**
	 * AJAX Handler: Save Force Sync Settings
	 */
	public function ajax_save_force_sync_settings()
	{
		check_ajax_referer('trvlr_admin_nonce', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error('Insufficient permissions.');
		}

		$force_sync_fields = isset($_POST['force_sync_fields']) ? $_POST['force_sync_fields'] : array();

		// First, clear all force sync fields
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

		wp_send_json_success(array(
			'message' => sprintf(__('%d attraction(s) configured for field-level sync.', 'trvlr'), $count)
		));
	}

	/**
	 * AJAX Handler: Clear All Force Sync Settings
	 */
	public function ajax_clear_all_custom_edits()
	{
		check_ajax_referer('trvlr_admin_nonce', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error('Insufficient permissions.');
		}

		$all_edited = get_posts(array(
			'post_type' => 'trvlr_attraction',
			'meta_key' => '_trvlr_has_custom_edits',
			'fields' => 'ids',
			'posts_per_page' => -1,
			'post_status' => 'any'
		));

		$count = 0;
		foreach ($all_edited as $pid) {
			// Only clear force sync settings, NOT the edit flags themselves
			if (get_post_meta($pid, '_trvlr_force_sync_fields', true)) {
				delete_post_meta($pid, '_trvlr_force_sync_fields');
				$count++;
			}
		}

		wp_send_json_success(array(
			'message' => 'Cleared force sync settings from ' . $count . ' attraction(s).'
		));
	}

	/**
	 * AJAX Handler: Clear Old Logs
	 */
	public function ajax_clear_old_logs()
	{
		check_ajax_referer('trvlr_admin_nonce', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error('Insufficient permissions.');
		}

		$deleted = Trvlr_Logger::clear_old_logs(30);

		wp_send_json_success(array(
			'message' => 'Deleted ' . $deleted . ' old log entries.'
		));
	}

	/**
	 * AJAX Handler: Clear All Logs
	 */
	public function ajax_clear_all_logs()
	{
		check_ajax_referer('trvlr_admin_nonce', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error('Insufficient permissions.');
		}

		$deleted = Trvlr_Logger::clear_all_logs();

		wp_send_json_success(array(
			'message' => 'All logs cleared.'
		));
	}

	/**
	 * AJAX Handler: Save Schedule Settings
	 */
	public function ajax_save_schedule_settings()
	{
		check_ajax_referer('trvlr_admin_nonce', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error('Insufficient permissions.');
		}

		$enabled = isset($_POST['enabled']) && $_POST['enabled'] === 'true';
		$frequency = sanitize_text_field($_POST['frequency']);

		if ($enabled) {
			Trvlr_Scheduler::schedule_sync($frequency);
			$next_sync = Trvlr_Scheduler::get_next_sync_time();
			wp_send_json_success(array(
				'message' => 'Scheduled sync enabled',
				'next_sync' => $next_sync ? date('Y-m-d H:i:s', $next_sync) : ''
			));
		} else {
			Trvlr_Scheduler::unschedule_sync();
			wp_send_json_success(array(
				'message' => 'Scheduled sync disabled',
				'next_sync' => ''
			));
		}
	}

	/**
	 * AJAX Handler: Save Notification Settings
	 */
	public function ajax_save_notifications()
	{
		check_ajax_referer('trvlr_admin_nonce', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error('Insufficient permissions.');
		}

		$email = sanitize_email($_POST['email']);
		$enabled_types = isset($_POST['enabled_types']) ? array_map('sanitize_text_field', $_POST['enabled_types']) : array();

		update_option('trvlr_notification_email', $email);
		update_option('trvlr_enabled_notifications', $enabled_types);

		// Schedule/unschedule weekly summary based on setting
		if (in_array('weekly_summary', $enabled_types)) {
			Trvlr_Notifier::schedule_weekly_summary();
		} else {
			Trvlr_Notifier::unschedule_weekly_summary();
		}

		wp_send_json_success(array(
			'message' => 'Notification settings saved'
		));
	}

	/**
	 * AJAX Handler: Send Test Email
	 */
	public function ajax_send_test_email()
	{
		check_ajax_referer('trvlr_admin_nonce', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error('Insufficient permissions.');
		}

		$admin_email = get_option('trvlr_notification_email', get_option('admin_email'));
		$subject = '[TRVLR] Test Notification - ' . get_bloginfo('name');
		$message = '<h2>TRVLR Test Notification</h2>';
		$message .= '<p>This is a test email to verify your notification settings are working correctly.</p>';
		$message .= '<p><strong>Time:</strong> ' . current_time('Y-m-d H:i:s') . '</p>';
		$message .= '<p><a href="' . admin_url('admin.php?page=trvlr-settings') . '">View TRVLR Settings</a></p>';

		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
		);

		$sent = wp_mail($admin_email, $subject, $message, $headers);

		if ($sent) {
			wp_send_json_success(array(
				'message' => 'Test email sent to ' . $admin_email
			));
		} else {
			wp_send_json_error('Failed to send test email. Please check your email configuration.');
		}
	}

	/**
	 * Handle CSV export download
	 */
	public function handle_export_logs()
	{
		// Verify nonce and permissions
		if (!isset($_GET['trvlr_export_logs']) || !isset($_GET['_wpnonce'])) {
			return;
		}

		if (!wp_verify_nonce($_GET['_wpnonce'], 'trvlr_export_logs')) {
			wp_die('Security check failed');
		}

		if (!current_user_can('manage_options')) {
			wp_die('Insufficient permissions');
		}

		// Get filter parameters
		$limit = isset($_GET['limit']) ? absint($_GET['limit']) : null;
		$type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : null;
		$date_from = isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : null;
		$date_to = isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : null;

		// Generate CSV
		$csv = Trvlr_Logger::export_to_csv($limit, $type, $date_from, $date_to);
		$filename = Trvlr_Logger::get_csv_filename($type);

		// Set headers for download
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Pragma: no-cache');
		header('Expires: 0');

		// Output CSV
		echo $csv;
		exit;
	}
}
