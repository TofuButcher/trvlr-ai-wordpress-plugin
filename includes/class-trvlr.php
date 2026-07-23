<?php

/**
 * Core plugin class — wires dependencies and registers admin/public hooks.
 *
 * @package    Trvlr
 * @subpackage Trvlr/includes
 */

class Trvlr
{

	/**
	 * @since    1.0.0
	 * @access   protected
	 * @var      Trvlr_Loader    $loader
	 */
	protected $loader;

	/**
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name
	 */
	protected $plugin_name;

	/**
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version
	 */
	protected $version;

	/**
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('TRVLR_VERSION')) {
			$this->version = TRVLR_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'trvlr';

		$this->load_dependencies();
		$this->define_core_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-loader.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/trvlr-feature-flags.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'core/class-trvlr-attraction.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-logger.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-async.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-scheduler.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-notifier.php';

		// Hashing/sync meta depends on transforms; load before field map.
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-data-transform.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-field-map.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-custom-edits.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-theme-config.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-template-registry.php';
		Trvlr_Template_Registry::bootstrap();

		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-trvlr-admin.php';

		// require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-trvlr-admin-app.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-rest-api.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-trvlr-public.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/trvlr-attraction-helpers.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/trvlr-template-functions.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/trvlr-shortcodes.php';

		$this->loader = new Trvlr_Loader();
	}

	/**
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_core_hooks()
	{

		$plugin_attraction = new Trvlr_Attraction();

		$this->loader->add_action('init', $plugin_attraction, 'register_post_type');
		$this->loader->add_action('init', $plugin_attraction, 'register_taxonomy');

		$this->loader->add_action('trvlr_daily_log_cleanup', 'Trvlr_Logger', 'run_daily_cleanup');
		$this->loader->add_action('trvlr_scheduled_sync', 'Trvlr_Scheduler', 'run_scheduled_sync');
		$this->loader->add_action('trvlr_process_sync_batch', 'Trvlr_Scheduler', 'run_sync_batch');
		$this->loader->add_action('trvlr_weekly_summary', 'Trvlr_Notifier', 'send_weekly_summary');

		$this->loader->add_filter('cron_schedules', 'Trvlr_Scheduler', 'add_cron_schedules');

		$this->loader->add_action('plugins_loaded', 'Trvlr_Custom_Edits', 'maybe_migrate');
		$this->loader->add_action('admin_notices', 'Trvlr_Custom_Edits', 'maybe_admin_notice');
		$this->loader->add_filter('wp_insert_post_data', 'Trvlr_Custom_Edits', 'filter_insert_post_data', 10, 2);
		$this->loader->add_filter('update_post_metadata', 'Trvlr_Custom_Edits', 'filter_thumbnail_meta', 10, 3);
		$this->loader->add_filter('delete_post_metadata', 'Trvlr_Custom_Edits', 'filter_thumbnail_meta', 10, 3);
	}

	/**
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		$plugin_admin = new Trvlr_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
		$this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
		$this->loader->add_action('admin_head', $plugin_admin, 'add_admin_google_fonts');

		$rest_api = new Trvlr_REST_API();
		$this->loader->add_action('rest_api_init', $rest_api, 'register_routes');
		$this->loader->add_action('rest_api_init', $plugin_admin, 'register_theme_rest_routes');
		$this->loader->add_action('admin_init', $plugin_admin, 'init_meta_boxes');

		$this->loader->add_action('wp_ajax_trvlr_manual_sync', $plugin_admin, 'ajax_manual_sync');
		$this->loader->add_action('wp_ajax_trvlr_sync_single', $plugin_admin, 'ajax_sync_single');
		$this->loader->add_action('wp_ajax_trvlr_delete_all_data', $plugin_admin, 'ajax_delete_all_data');
		$this->loader->add_action('wp_ajax_trvlr_delete_posts_only', $plugin_admin, 'ajax_delete_posts_only');
		$this->loader->add_action('wp_ajax_trvlr_create_payment_page', $plugin_admin, 'ajax_create_payment_page');
		$this->loader->add_action('wp_ajax_trvlr_set_field_edit_mode', $plugin_admin, 'ajax_set_field_edit_mode');
		$this->loader->add_action('wp_ajax_trvlr_clear_all_custom_edits', $plugin_admin, 'ajax_clear_all_custom_edits');
		$this->loader->add_action('wp_ajax_trvlr_clear_old_logs', $plugin_admin, 'ajax_clear_old_logs');
		$this->loader->add_action('wp_ajax_trvlr_save_schedule_settings', $plugin_admin, 'ajax_save_schedule_settings');
		$this->loader->add_action('wp_ajax_trvlr_save_notifications', $plugin_admin, 'ajax_save_notifications');
		$this->loader->add_action('wp_ajax_trvlr_send_test_email', $plugin_admin, 'ajax_send_test_email');

		$this->loader->add_action('admin_init', $plugin_admin, 'handle_export_logs');
		$this->loader->add_action('wp_ajax_trvlr_clear_all_logs', $plugin_admin, 'ajax_clear_all_logs');
	}

	/**
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{

		$plugin_public = new Trvlr_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

		$this->loader->add_filter('template_include', $plugin_public, 'load_attraction_template');

		$this->loader->add_action('wp_footer', $plugin_public, 'add_global_svg_icons');

		$this->loader->add_action('wp_head', $plugin_public, 'add_google_fonts');

		$this->loader->add_action('wp_head', $plugin_public, 'output_theme_css_variables');

		$this->loader->add_filter('trvlr_duration', $plugin_public, 'filter_trvlr_duration', 10, 2);
		$this->loader->add_filter('trvlr_start_time', $plugin_public, 'filter_trvlr_time', 10, 2);
		$this->loader->add_filter('trvlr_end_time', $plugin_public, 'filter_trvlr_time', 10, 2);
		$this->loader->add_filter('trvlr_pricing', $plugin_public, 'filter_trvlr_pricing', 10, 2);

		$this->loader->add_filter('body_class', $plugin_public, 'add_payment_page_body_class');
		$this->loader->add_filter('redirect_canonical', $plugin_public, 'disable_redirect_for_payment_page', 10, 2);
		$this->loader->add_filter('the_content', $plugin_public, 'render_payment_confirmation_content');
	}

	/**
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * @since     1.0.0
	 * @return    string
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * @since     1.0.0
	 * @return    Trvlr_Loader
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * @since     1.0.0
	 * @return    string
	 */
	public function get_version()
	{
		return $this->version;
	}
}
