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
		$screen = get_current_screen();
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/trvlr-admin.css', array(), $this->version, 'all');

		if ($screen && $screen->id === 'toplevel_page_trvlr_settings') {
			wp_enqueue_style('trvlr-public', plugin_dir_url(dirname(__FILE__)) . 'public/css/trvlr-public.css', array(), $this->version, 'all');
			wp_enqueue_style('trvlr-cards', plugin_dir_url(dirname(__FILE__)) . 'public/css/trvlr-cards.css', array(), $this->version, 'all');
		}
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

		$screen = get_current_screen();

		// Enqueue React settings components on settings page
		if ($screen && $screen->id === 'toplevel_page_trvlr_settings') {
			// React app
			$theme_asset = include(plugin_dir_path(__FILE__) . 'build/trvlr-admin-root.jsx.asset.php');

			// Force cache busting during development
			$version = $theme_asset['version'] . '-' . time();

			wp_enqueue_script(
				'trvlr-admin-root',
				plugin_dir_url(__FILE__) . 'build/trvlr-admin-root.jsx.js',
				$theme_asset['dependencies'],
				$version,
				true
			);

			// Enqueue React app styles
			wp_enqueue_style(
				'trvlr-admin-root',
				plugin_dir_url(__FILE__) . 'build/trvlr-admin-root.jsx.css',
				array(),
				$theme_asset['version']
			);

			// Get initial data
			$initial_data = $this->get_initial_data();

			// Debug logging
			error_log('TRVLR Admin: Enqueueing scripts');
			error_log('TRVLR Admin: REST Nonce: ' . substr($initial_data['restNonce'], 0, 10) . '...');
			error_log('TRVLR Admin: REST Root: ' . $initial_data['restRoot']);
			error_log('TRVLR Admin: User ID: ' . get_current_user_id());
			error_log('TRVLR Admin: User can manage_options: ' . (current_user_can('manage_options') ? 'yes' : 'no'));

			// Set up WordPress REST API settings for apiFetch
			// This is the correct way to configure wp.apiFetch
			wp_localize_script('trvlr-admin-root', 'wpApiSettings', array(
				'root' => esc_url_raw(rest_url()),
				'nonce' => wp_create_nonce('wp_rest'),
				'versionString' => 'wp/v2/'
			));

			// Localize all initial data for React app
			wp_localize_script('trvlr-admin-root', 'trvlrInitialData', $initial_data);

			wp_enqueue_style('wp-components');
		}
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
	 * Output SVG icons for attraction card preview
	 */
	public function output_admin_svg_icons()
	{
		$screen = get_current_screen();
		if ($screen && $screen->id === 'toplevel_page_trvlr_settings') {
?>
			<svg style="display: none;">
				<symbol id="icon-star" viewBox="0 0 18 18">
					<path d="M9.00002 0.5C9.38064 0.5 9.72803 0.716313 9.8965 1.05762L11.9805 5.28027L16.6446 5.96289C17.0211 6.01793 17.3338 6.28252 17.4512 6.64453C17.5684 7.00643 17.4698 7.40351 17.1973 7.66895L13.8242 10.9531L14.6211 15.5957C14.6855 15.9709 14.5307 16.3505 14.2227 16.5742C13.9148 16.7978 13.5067 16.8273 13.1699 16.6504L9.00002 14.457L4.8301 16.6504C4.49331 16.8273 4.0852 16.7978 3.77736 16.5742C3.46939 16.3505 3.31458 15.9709 3.37893 15.5957L4.17482 10.9531L0.802754 7.66895C0.530236 7.40351 0.431671 7.00643 0.548848 6.64453C0.666226 6.28252 0.978929 6.01793 1.35549 5.96289L6.01857 5.28027L8.10354 1.05762L8.17482 0.935547C8.35943 0.665559 8.66699 0.5 9.00002 0.5Z" />
				</symbol>
				<symbol id="icon-clock" viewBox="0 0 18 18">
					<g clip-path="url(#clip0_133_223)">
						<path d="M15.5 9C15.5 5.41015 12.5899 2.5 9 2.5C5.41015 2.5 2.5 5.41015 2.5 9C2.5 12.5899 5.41015 15.5 9 15.5C12.5899 15.5 15.5 12.5899 15.5 9ZM17.5 9C17.5 13.6944 13.6944 17.5 9 17.5C4.30558 17.5 0.5 13.6944 0.5 9C0.5 4.30558 4.30558 0.5 9 0.5C13.6944 0.5 17.5 4.30558 17.5 9Z" />
						<path d="M8 4.5C8 3.94772 8.44772 3.5 9 3.5C9.55228 3.5 10 3.94772 10 4.5V8.38184L12.4473 9.60547C12.9412 9.85246 13.1415 10.4533 12.8945 10.9473C12.6475 11.4412 12.0467 11.6415 11.5527 11.3945L8.55273 9.89453C8.21395 9.72514 8 9.37877 8 9V4.5Z" />
					</g>
					<defs>
						<clipPath id="clip0_133_223">
							<rect width="18" height="18" />
						</clipPath>
					</defs>
				</symbol>
				<symbol id="icon-arrow-right" viewBox="0 0 21 21">
					<path d="M9.83496 4.29285C10.2255 3.90241 10.8585 3.90236 11.249 4.29285L16.791 9.83484C16.7969 9.84072 16.8019 9.84741 16.8076 9.8534C16.8194 9.86578 16.8307 9.87851 16.8418 9.89148C16.8509 9.90206 16.8596 9.91284 16.8682 9.92371C16.879 9.93742 16.8893 9.95142 16.8994 9.9657C17.1465 10.3148 17.143 10.7848 16.8896 11.1307C16.8847 11.1375 16.8801 11.1446 16.875 11.1512C16.8612 11.1691 16.8462 11.1859 16.8311 11.203C16.8259 11.2089 16.8208 11.2148 16.8154 11.2206C16.807 11.2297 16.7999 11.2401 16.791 11.2489L11.249 16.7899C10.8585 17.1804 10.2255 17.1804 9.83496 16.7899C9.44461 16.3994 9.44449 15.7663 9.83496 15.3759L13.668 11.5419H5C4.4478 11.5419 4.00013 11.094 4 10.5419C4 9.98959 4.44772 9.54187 5 9.54187H13.6699L9.83496 5.70691C9.44444 5.31639 9.44444 4.68337 9.83496 4.29285Z" />
				</symbol>
			</svg>
		<?php
		}
	}

	/**
	 * Inject Google Fonts
	 */
	public function add_admin_google_fonts()
	{
		?>
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Montserrat:ital,wght@0,700;1,700&family=Rethink+Sans:ital,wght@0,400..800;1,400..800&display=swap" rel="stylesheet">
<?php
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
	 * Get all initial data for React app (localized to avoid multiple API calls)
	 */
	private function get_initial_data()
	{
		// Get sync statistics
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

		// Get schedule settings
		$sync_enabled = Trvlr_Scheduler::is_sync_enabled();
		$sync_frequency = Trvlr_Scheduler::get_sync_frequency();
		$next_sync = Trvlr_Scheduler::get_next_sync_time();

		// Get system status
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

		return array(
			'settings' => array(
				'theme' => Trvlr_Theme_Config::merge_with_defaults(get_option('trvlr_theme_settings', array())),
				'connection' => array(
					'organisation_id' => get_option('trvlr_organisation_id', ''),
				),
				'notifications' => get_option('trvlr_notification_settings', array()),
			),
			'sync' => array(
				'stats' => array(
					'total_attractions' => $total_attractions,
					'synced_count' => $total_attractions - $custom_edit_count,
					'custom_edit_count' => $custom_edit_count,
				),
				'schedule' => array(
					'enabled' => $sync_enabled,
					'frequency' => $sync_frequency,
					'next_sync' => $next_sync ? date('Y-m-d H:i:s', $next_sync) : null,
				),
				'custom_edits_count' => $custom_edit_count,
			),
			'system' => array(
				'payment_page' => array(
					'exists' => $payment_page_exists,
					'url' => $payment_page_url,
					'id' => $payment_page_id,
				),
				'api_connection' => array(
					'tested' => false,
					'status' => 'not_tested',
				),
			),
			'nonce' => wp_create_nonce('trvlr_admin_nonce'),
			'restNonce' => wp_create_nonce('wp_rest'),
			'restRoot' => esc_url_raw(rest_url()),
		);
	}

	/**
	 * Register custom REST API endpoints for settings
	 */
	public function register_theme_rest_routes()
	{
		// Theme settings
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

		// Connection settings
		register_rest_route('trvlr/v1', '/connection-settings', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_connection_settings_rest'),
			'permission_callback' => function () {
				return current_user_can('manage_options');
			},
		));

		register_rest_route('trvlr/v1', '/connection-settings', array(
			'methods' => 'POST',
			'callback' => array($this, 'update_connection_settings_rest'),
			'permission_callback' => function () {
				return current_user_can('manage_options');
			},
		));

		// Notification settings
		register_rest_route('trvlr/v1', '/notification-settings', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_notification_settings_rest'),
			'permission_callback' => function () {
				return current_user_can('manage_options');
			},
		));

		register_rest_route('trvlr/v1', '/notification-settings', array(
			'methods' => 'POST',
			'callback' => array($this, 'update_notification_settings_rest'),
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
	 * REST API: Get connection settings
	 */
	public function get_connection_settings_rest($request)
	{
		$settings = array(
			'organisation_id' => get_option('trvlr_organisation_id', ''),
			'api_key' => get_option('trvlr_api_key', ''),
		);
		return rest_ensure_response($settings);
	}

	/**
	 * REST API: Update connection settings
	 */
	public function update_connection_settings_rest($request)
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

	/**
	 * REST API: Get notification settings
	 */
	public function get_notification_settings_rest($request)
	{
		$settings = get_option('trvlr_notification_settings', array());
		return rest_ensure_response($settings);
	}

	/**
	 * REST API: Update notification settings
	 */
	public function update_notification_settings_rest($request)
	{
		$settings = $request->get_json_params();

		if (empty($settings) || !is_array($settings)) {
			return new WP_Error('invalid_data', 'Invalid settings data', array('status' => 400));
		}

		update_option('trvlr_notification_settings', $settings);

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
