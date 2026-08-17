<?php

/**
 * Admin area: enqueue, settings REST, meta boxes, AJAX handlers.
 *
 * @package    Trvlr
 * @subpackage Trvlr/admin
 */

class Trvlr_Admin
{

	/** @var string */
	private $plugin_name;

	/** @var string */
	private $version;

	/** @var Trvlr_Admin_Dev|null */
	private $dev_instance;

	/**
	 * @param string $plugin_name
	 * @param string $version
	 */
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->init_dev_environment();
	}

	/**
	 * @return void
	 */
	private function init_dev_environment()
	{
		$dev_class_file = TRVLR_PLUGIN_DIR . '~dev/dev-class-trvlr-admin.php';

		if (file_exists($dev_class_file)) {
			require_once $dev_class_file;

			if (class_exists('Trvlr_Admin_Dev')) {
				$this->dev_instance = new Trvlr_Admin_Dev($this->plugin_name, $this->version, $this);
				$this->dev_instance->init();
			}
		}
	}

	/**
	 * @return void
	 */
	public function enqueue_styles()
	{
		$screen = get_current_screen();

		if (trvlr_is_vite_hot()) {
			trvlr_enqueue_vite_client();
			wp_enqueue_script_module(
				'trvlr-admin-styles',
				trvlr_vite_url('admin/src/admin-styles.js'),
				array(),
				null
			);
			if ($screen && $screen->id === 'toplevel_page_trvlr_settings') {
				trvlr_enqueue_admin_public_vite_styles();
			}
			return;
		}

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/trvlr-admin.css', array(), $this->version, 'all');

		if ($screen && $screen->id === 'toplevel_page_trvlr_settings') {
			wp_enqueue_style('trvlr-public', plugin_dir_url(dirname(__FILE__)) . 'public/dist/css/trvlr-public.css', array(), $this->version, 'all');
			wp_enqueue_style('trvlr-cards', plugin_dir_url(dirname(__FILE__)) . 'public/dist/css/trvlr-cards.css', array(), $this->version, 'all');

			if (class_exists('Trvlr_Template_Registry')) {
				$presentation_theme_css = Trvlr_Template_Registry::get_active_presentation_theme_stylesheet_basename();
				if ($presentation_theme_css !== '') {
					$presentation_theme_path = plugin_dir_path(dirname(__FILE__)) . 'public/dist/css/' . $presentation_theme_css;
					if (is_readable($presentation_theme_path)) {
						wp_enqueue_style(
							'trvlr-presentation-theme',
							plugin_dir_url(dirname(__FILE__)) . 'public/dist/css/' . $presentation_theme_css,
							array('trvlr-cards'),
							filemtime($presentation_theme_path) ? (string) filemtime($presentation_theme_path) : $this->version,
							'all'
						);
					}
				}
			}
		}
	}

	/**
	 * @return void
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/trvlr-admin.js', array('jquery'), $this->version, false);

		wp_localize_script($this->plugin_name, 'trvlr_admin_vars', array(
			'nonce' => wp_create_nonce('trvlr_admin_nonce')
		));

		$screen = get_current_screen();

		if ($screen && in_array($screen->base, array('post', 'post-new'), true) && $screen->post_type === 'trvlr_attraction') {
			$post_id = isset($_GET['post']) ? absint($_GET['post']) : 0;
			$has_trvlr_id = $post_id && get_post_meta($post_id, 'trvlr_id', true);
			if ($has_trvlr_id) {
				$custom_fields = trvlr_get_custom_edit_fields($post_id);
				$labels = class_exists('Trvlr_Field_Map') ? Trvlr_Field_Map::get_field_labels() : array();

				wp_enqueue_script(
					'trvlr-field-sync-ui',
					plugin_dir_url(__FILE__) . 'js/trvlr-field-sync-ui.js',
					array('jquery'),
					$this->version,
					true
				);

				wp_localize_script('trvlr-field-sync-ui', 'trvlrFieldSyncUI', array(
					'ajaxUrl' => admin_url('admin-ajax.php'),
					'nonce' => wp_create_nonce('trvlr_field_edit_mode'),
					'postId' => $post_id,
					'customFields' => $custom_fields,
					'i18n' => array(
						'badgeSynced' => __('Synced to Traveloris', 'trvlr'),
						'badgeCustom' => __('Not Synced - WP Edit', 'trvlr'),
						'customEdit' => __('Custom Edit', 'trvlr'),
						'enableSync' => __('Enable Traveloris Sync', 'trvlr'),
						'confirmEnableSync' => __('Enable Traveloris sync for this field? The next sync will restore Traveloris content.', 'trvlr'),
						'error' => __('Could not update field sync mode.', 'trvlr'),
						'titleLabel' => isset($labels['post_title']) ? $labels['post_title'] : __('Title', 'trvlr'),
						'featuredLabel' => isset($labels['_thumbnail_id']) ? $labels['_thumbnail_id'] : __('Featured Image', 'trvlr'),
					),
				));
			}
		}

		if ($screen && $screen->id === 'toplevel_page_trvlr_settings') {
			$initial_data = $this->get_initial_data();

			if (trvlr_enqueue_admin_vite_hmr($initial_data)) {
				return;
			}

			$asset_file = plugin_dir_path(__FILE__) . 'build/trvlr-admin-root.jsx.asset.php';

			if (!file_exists($asset_file)) {
				add_action('admin_notices', function () {
					echo '<div class="notice notice-error"><p><strong>Traveloris:</strong> Build files are missing. The admin interface requires compiled assets (admin/build/). Please reinstall the plugin from a release build.</p></div>';
				});
				return;
			}

			$theme_asset = include($asset_file);

			$version = $theme_asset['version'] . '-' . time();

			wp_enqueue_script(
				'trvlr-admin-root',
				plugin_dir_url(__FILE__) . 'build/trvlr-admin-root.jsx.js',
				$theme_asset['dependencies'],
				$version,
				true
			);

			$admin_css = plugin_dir_path(__FILE__) . 'build/trvlr-admin-root.jsx.css';
			if (is_readable($admin_css)) {
				wp_enqueue_style(
					'trvlr-admin-root',
					plugin_dir_url(__FILE__) . 'build/trvlr-admin-root.jsx.css',
					array(),
					$theme_asset['version']
				);
			}

			wp_localize_script('trvlr-admin-root', 'wpApiSettings', array(
				'root' => esc_url_raw(rest_url()),
				'nonce' => wp_create_nonce('wp_rest'),
				'versionString' => 'wp/v2/'
			));

			wp_localize_script('trvlr-admin-root', 'trvlrInitialData', $initial_data);

			wp_enqueue_style('wp-components');
		}
	}

	/**
	 * @return void
	 */
	public function add_plugin_admin_menu()
	{
		$menu_icon = TRVLR_PLUGIN_URL . 'media/traveloris_emblem.svg';

		add_menu_page(
			__('Traveloris', 'trvlr'),
			__('Traveloris', 'trvlr'),
			'manage_options',
			'trvlr_settings',
			array($this, 'display_plugin_settings_page'),
			$menu_icon,
			30
		);

		add_submenu_page(
			'trvlr_settings',
			__('Settings', 'trvlr'),
			__('Settings', 'trvlr'),
			'manage_options',
			'trvlr_settings'
		);
	}

	/**
	 * SVG sprite + icon mask CSS vars for attraction card preview on the settings page.
	 *
	 * @return void
	 */
	public function output_admin_svg_icons()
	{
		$screen = get_current_screen();
		if (!$screen || $screen->id !== 'toplevel_page_trvlr_settings') {
			return;
		}

		if (class_exists('Trvlr_Icons')) {
			Trvlr_Icons::print_admin_assets();
		}
	}

	/**
	 * @return void
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
	 * @return void
	 */
	public function display_plugin_settings_page()
	{
		include_once 'partials/trvlr-settings-main.php';
	}

	/**
	 * @return void
	 */
	public function init_meta_boxes()
	{
		require_once plugin_dir_path(__FILE__) . 'term-meta-faqs.php';

		if (function_exists('trvlr_is_attraction_post_type_disabled') && trvlr_is_attraction_post_type_disabled()) {
			return;
		}

		require_once plugin_dir_path(__FILE__) . 'meta-fields.php';
	}

	/**
	 * @return void
	 */
	public function register_settings()
	{
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

		register_setting('trvlr_settings_group', 'trvlr_disable_attraction_post_type', array(
			'type' => 'boolean',
			'default' => false,
			'show_in_rest' => true,
		));

		register_setting('trvlr_settings_group', 'trvlr_disable_attraction_sync', array(
			'type' => 'boolean',
			'default' => false,
			'show_in_rest' => true,
		));

		register_setting('trvlr_settings_group', 'trvlr_disable_frontend_booking', array(
			'type' => 'boolean',
			'default' => false,
			'show_in_rest' => true,
		));

		register_setting('trvlr_settings_group', 'trvlr_disable_attraction_seo_schema', array(
			'type' => 'boolean',
			'default' => false,
			'show_in_rest' => true,
		));

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

		register_setting('trvlr_theme_settings', 'trvlr_theme_settings', array(
			'type' => 'object',
			'default' => array(),
			'sanitize_callback' => array($this, 'sanitize_theme_settings'),
			'show_in_rest' => array(
				'name' => 'trvlr_theme_settings',
				'schema' => array(
					'type' => 'object',
					'properties' => array(
						'primaryColor' => array('type' => 'string', 'default' => 'hsl(165, 100%, 39%)'),
						'secondaryColor' => array('type' => 'string', 'default' => 'hsl(329, 66%, 75%)'),
						'accentColor' => array('type' => 'string', 'default' => 'hsl(157, 100%, 49%)'),
						'alertColor' => array('type' => 'string', 'default' => 'hsl(0, 90%, 65%)'),
					),
				),
			),
		));
	}

	/**
	 * @param mixed $settings
	 * @return array
	 */
	public function sanitize_theme_settings($settings)
	{
		if (!is_array($settings)) {
			return array();
		}

		return $settings;
	}

	/**
	 * Bootstrap payload localized to the React admin app (avoids multiple round-trips on load).
	 *
	 * @return array
	 */
	private function get_initial_data()
	{
		if (! function_exists('trvlr_get_connection_settings_array')) {
			require_once plugin_dir_path(dirname(__FILE__)) . 'includes/trvlr-feature-flags.php';
		}

		$post_counts = wp_count_posts('trvlr_attraction');
		$total_attractions = $post_counts->publish + $post_counts->draft + $post_counts->pending + $post_counts->private;

		$custom_edit_posts = get_posts(array(
			'post_type' => 'trvlr_attraction',
			'meta_key' => '_trvlr_has_custom_edits',
			'meta_value' => '1',
			'fields' => 'ids',
			'posts_per_page' => -1,
			'post_status' => 'any'
		));
		$custom_edit_count = count($custom_edit_posts);

		$sync_enabled = Trvlr_Scheduler::is_sync_enabled();
		$sync_frequency = Trvlr_Scheduler::get_sync_frequency();
		$next_sync = Trvlr_Scheduler::get_next_sync_time();

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

		$theme_stored = get_option('trvlr_theme_settings', array());
		$theme_merged = Trvlr_Theme_Config::merge_with_defaults(is_array($theme_stored) ? $theme_stored : array());
		if (class_exists('Trvlr_Template_Registry')) {
			$theme_merged['presentationTheme'] = Trvlr_Template_Registry::get_active_presentation_theme_slug();
			$theme_merged['cardTemplate'] = Trvlr_Template_Registry::get_active_card_slug();
			$theme_merged['attractionPageTemplate'] = Trvlr_Template_Registry::get_active_single_slug();
		}

		return array(
			'settings' => array(
				'theme' => $theme_merged,
				'connection' => trvlr_get_connection_settings_array(),
				'notifications' => get_option('trvlr_notification_settings', array()),
			),
			'themeConfig' => Trvlr_Theme_Config::get_config(),
			'templateChoices' => class_exists('Trvlr_Template_Registry')
				? Trvlr_Template_Registry::get_template_choices_for_admin()
				: array(
					'cards' => array(),
					'singles' => array(),
					'presentationThemes' => array(),
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
	 * @return void
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

		register_rest_route('trvlr/v1', '/sync/progress', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_sync_progress_rest'),
			'permission_callback' => function () {
				return current_user_can('manage_options');
			},
		));
	}

	/**
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_theme_settings_rest($request)
	{
		return rest_ensure_response(Trvlr_Theme_Config::get_merged_settings_for_response());
	}

	/**
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response|WP_Error
	 */
	public function update_theme_settings_rest($request)
	{
		$settings = $request->get_json_params();

		if (empty($settings) || !is_array($settings)) {
			return new WP_Error('invalid_data', 'Invalid settings data', array('status' => 400));
		}

		$returned = Trvlr_Theme_Config::save_settings($settings);

		return rest_ensure_response(array(
			'success' => true,
			'settings' => $returned,
		));
	}

	/**
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_connection_settings_rest($request)
	{
		if (! function_exists('trvlr_get_connection_settings_array')) {
			require_once plugin_dir_path(dirname(__FILE__)) . 'includes/trvlr-feature-flags.php';
		}
		return rest_ensure_response(trvlr_get_connection_settings_array());
	}

	/**
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function update_connection_settings_rest($request)
	{
		if (! function_exists('trvlr_update_connection_settings_from_request')) {
			require_once plugin_dir_path(dirname(__FILE__)) . 'includes/trvlr-feature-flags.php';
		}

		$prev_pt = (bool) get_option('trvlr_disable_attraction_post_type', false);

		$data = $request->get_json_params();
		if (! is_array($data)) {
			$data = array();
		}
		trvlr_update_connection_settings_from_request($data);

		if (function_exists('trvlr_is_attraction_sync_disabled') && trvlr_is_attraction_sync_disabled()) {
			Trvlr_Scheduler::unschedule_sync();
		}

		$new_pt = (bool) get_option('trvlr_disable_attraction_post_type', false);
		if ($prev_pt !== $new_pt) {
			flush_rewrite_rules(false);
		}

		return rest_ensure_response(array(
			'success' => true,
			'settings' => trvlr_get_connection_settings_array(),
		));
	}

	/**
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_notification_settings_rest($request)
	{
		$settings = get_option('trvlr_notification_settings', array());
		return rest_ensure_response($settings);
	}

	/**
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response|WP_Error
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
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_sync_progress_rest($request)
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'core/class-trvlr-sync.php';

		$syncer = new Trvlr_Sync();
		return rest_ensure_response($syncer->get_progress_status());
	}

	/**
	 * @return void
	 */
	public function ajax_manual_sync()
	{
		check_ajax_referer('trvlr_admin_nonce', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error('Insufficient permissions.');
		}

		if (function_exists('trvlr_is_attraction_sync_disabled') && trvlr_is_attraction_sync_disabled()) {
			wp_send_json_error(__('Attraction syncing is disabled in Traveloris settings.', 'trvlr'));
		}

		try {
			require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-field-map.php';
			require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-logger.php';
			require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-notifier.php';
			require_once plugin_dir_path(dirname(__FILE__)) . 'core/class-trvlr-sync.php';

			$syncer = new Trvlr_Sync();
			$result = $syncer->start_sync();

			if ($result['success']) {
				wp_send_json_success($result);
			} else {
				wp_send_json_error($result['message']);
			}
		} catch (Exception $e) {
			if (class_exists('Trvlr_Logger')) {
				Trvlr_Logger::log('error', 'Manual sync failed: ' . $e->getMessage(), array(
					'trace' => $e->getTraceAsString()
				));
			}
			error_log('TRVLR Manual Sync Error: ' . $e->getMessage());
			wp_send_json_error('Sync failed: ' . $e->getMessage());
		}
	}

	/**
	 * @return void
	 */
	public function ajax_delete_all_data()
	{
		$this->process_deletion(true);
	}

	/**
	 * @return void
	 */
	public function ajax_delete_posts_only()
	{
		$this->process_deletion(false);
	}

	/**
	 * @param bool $delete_media When true, also deletes featured image and gallery attachments.
	 * @return void
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

		wp_send_json_success('Data deleted.');
	}

	/**
	 * @return void
	 */
	public function ajax_sync_single()
	{
		check_ajax_referer('trvlr_sync_single', 'nonce');

		if (!current_user_can('edit_posts')) {
			wp_send_json_error(array('message' => 'Insufficient permissions'));
		}

		if (function_exists('trvlr_is_attraction_sync_disabled') && trvlr_is_attraction_sync_disabled()) {
			wp_send_json_error(array('message' => __('Attraction syncing is disabled in Traveloris settings.', 'trvlr')));
		}

		$post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;

		if (!$post_id) {
			wp_send_json_error(array('message' => 'Invalid post ID'));
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-field-map.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-logger.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-notifier.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'core/class-trvlr-sync.php';

		$sync_engine = new Trvlr_Sync();
		$result = $sync_engine->sync_single($post_id);

		if ($result['success']) {
			wp_send_json_success($result);
		} else {
			wp_send_json_error($result);
		}
	}

	/**
	 * @return void
	 */
	public function ajax_create_payment_page()
	{
		check_ajax_referer('trvlr_admin_nonce', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error('Insufficient permissions.');
		}

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
	 * Toggle Synced / Custom Edit for one attraction field.
	 *
	 * @return void
	 */
	public function ajax_set_field_edit_mode()
	{
		check_ajax_referer('trvlr_field_edit_mode', 'nonce');

		$post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
		$field = isset($_POST['field']) ? sanitize_text_field(wp_unslash($_POST['field'])) : '';
		$enabled = !empty($_POST['enabled']) && $_POST['enabled'] !== '0';

		if (!$post_id || get_post_type($post_id) !== 'trvlr_attraction') {
			wp_send_json_error(array('message' => __('Invalid attraction.', 'trvlr')));
		}

		if (!current_user_can('edit_post', $post_id)) {
			wp_send_json_error(array('message' => __('Insufficient permissions.', 'trvlr')));
		}

		if (!Trvlr_Field_Map::is_syncable($field)) {
			wp_send_json_error(array('message' => __('Invalid field.', 'trvlr')));
		}

		trvlr_set_custom_edit($post_id, $field, $enabled);

		$custom_fields = trvlr_get_custom_edit_fields($post_id);
		$labels = Trvlr_Field_Map::get_field_labels();

		wp_send_json_success(array(
			'custom_fields' => $custom_fields,
			'labels' => $labels,
			'field' => $field,
			'enabled' => $enabled,
		));
	}

	/**
	 * Clear Custom Edit flags for all attractions.
	 *
	 * @return void
	 */
	public function ajax_clear_all_custom_edits()
	{
		check_ajax_referer('trvlr_admin_nonce', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error('Insufficient permissions.');
		}

		$count = Trvlr_Custom_Edits::clear_all_sitewide();

		wp_send_json_success(array(
			'message' => sprintf(__('Cleared custom edits from %d attraction(s).', 'trvlr'), $count)
		));
	}

	/**
	 * @return void
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
	 * @return void
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
	 * @return void
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
	 * @return void
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
	 * @return void
	 */
	public function ajax_send_test_email()
	{
		check_ajax_referer('trvlr_admin_nonce', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error('Insufficient permissions.');
		}

		$admin_email = get_option('trvlr_notification_email', get_option('admin_email'));
		$subject = '[Traveloris] Test Notification - ' . get_bloginfo('name');
		$message = '<h2>Traveloris Test Notification</h2>';
		$message .= '<p>This is a test email to verify your notification settings are working correctly.</p>';
		$message .= '<p><strong>Time:</strong> ' . current_time('Y-m-d H:i:s') . '</p>';
		$message .= '<p><a href="' . admin_url('admin.php?page=trvlr_settings') . '">View Traveloris Settings</a></p>';

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
	 * Stream sync logs as a CSV download when `trvlr_export_logs` is present in the query.
	 *
	 * @return void
	 */
	public function handle_export_logs()
	{
		if (!isset($_GET['trvlr_export_logs']) || !isset($_GET['_wpnonce'])) {
			return;
		}

		if (!wp_verify_nonce($_GET['_wpnonce'], 'trvlr_export_logs')) {
			wp_die('Security check failed');
		}

		if (!current_user_can('manage_options')) {
			wp_die('Insufficient permissions');
		}

		$limit = isset($_GET['limit']) ? absint($_GET['limit']) : null;
		$type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : null;
		$date_from = isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : null;
		$date_to = isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : null;

		$csv = Trvlr_Logger::export_to_csv($limit, $type, $date_from, $date_to);
		$filename = Trvlr_Logger::get_csv_filename($type);

		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Pragma: no-cache');
		header('Expires: 0');

		echo $csv;
		exit;
	}
}
