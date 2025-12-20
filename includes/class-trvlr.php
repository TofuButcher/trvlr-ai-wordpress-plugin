<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package    Trvlr
 * @subpackage Trvlr/includes
 */

class Trvlr
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Trvlr_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
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
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Trvlr_Loader. Orchestrates the hooks of the plugin.
	 * - Trvlr_Attraction. Registers the CPT and Taxonomies.
	 * - Trvlr_Admin. Defines all hooks for the admin area.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-loader.php';

		/**
		 * The class responsible for defining the CPT and business logic for Attractions.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'core/class-trvlr-attraction.php';

		/**
		 * The logger class for sync operations
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-logger.php';

		/**
		 * The scheduler class for automated sync
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-scheduler.php';

		/**
		 * The notifier class for email notifications
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-notifier.php';

		/**
		 * The field map class for centralized field configuration
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-field-map.php';

		/**
		 * The edit tracker class for real-time edit detection
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-edit-tracker.php';

		/**
		 * The theme configuration class
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-theme-config.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-trvlr-admin.php';

		/**
		 * The class responsible for React-based admin app functionality.
		 */
		// require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-trvlr-admin-app.php';

		/**
		 * REST API Controller for all plugin endpoints
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-rest-api.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing side.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-trvlr-public.php';

		/**
		 * Helper functions for attraction data access
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/trvlr-attraction-helpers.php';

		/**
		 * Template functions for displaying attractions
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/trvlr-template-functions.php';

		/**
		 * Shortcodes
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/trvlr-shortcodes.php';

		$this->loader = new Trvlr_Loader();
	}

	/**
	 * Register all of the hooks related to the core functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_core_hooks()
	{

		$plugin_attraction = new Trvlr_Attraction();

		$this->loader->add_action('init', $plugin_attraction, 'register_post_type');
		$this->loader->add_action('init', $plugin_attraction, 'register_taxonomy');

		// Cron jobs
		$this->loader->add_action('trvlr_daily_log_cleanup', 'Trvlr_Logger', 'run_daily_cleanup');
		$this->loader->add_action('trvlr_scheduled_sync', 'Trvlr_Scheduler', 'run_scheduled_sync');
		$this->loader->add_action('trvlr_weekly_summary', 'Trvlr_Notifier', 'send_weekly_summary');

		// Add custom cron schedules
		$this->loader->add_filter('cron_schedules', 'Trvlr_Scheduler', 'add_cron_schedules');

		// Track edits in real-time (Priority 20 ensures it runs after meta fields are saved)
		$this->loader->add_action('save_post', 'Trvlr_Edit_Tracker', 'track_attraction_save', 20, 3);
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
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

		// Register REST API routes
		$rest_api = new Trvlr_REST_API();
		$this->loader->add_action('rest_api_init', $rest_api, 'register_routes');
		// Initialize Meta Boxes
		$this->loader->add_action('admin_init', $plugin_admin, 'init_meta_boxes');

		// AJAX Hooks
		$this->loader->add_action('wp_ajax_trvlr_manual_sync', $plugin_admin, 'ajax_manual_sync');
		$this->loader->add_action('wp_ajax_trvlr_delete_all_data', $plugin_admin, 'ajax_delete_all_data');
		$this->loader->add_action('wp_ajax_trvlr_delete_posts_only', $plugin_admin, 'ajax_delete_posts_only');
		$this->loader->add_action('wp_ajax_trvlr_create_payment_page', $plugin_admin, 'ajax_create_payment_page');
		$this->loader->add_action('wp_ajax_trvlr_save_force_sync_settings', $plugin_admin, 'ajax_save_force_sync_settings');
		$this->loader->add_action('wp_ajax_trvlr_clear_all_custom_edits', $plugin_admin, 'ajax_clear_all_custom_edits');
		$this->loader->add_action('wp_ajax_trvlr_clear_old_logs', $plugin_admin, 'ajax_clear_old_logs');
		$this->loader->add_action('wp_ajax_trvlr_save_schedule_settings', $plugin_admin, 'ajax_save_schedule_settings');
		$this->loader->add_action('wp_ajax_trvlr_save_notifications', $plugin_admin, 'ajax_save_notifications');
		$this->loader->add_action('wp_ajax_trvlr_send_test_email', $plugin_admin, 'ajax_send_test_email');

		// CSV Export handler (non-AJAX for download)
		$this->loader->add_action('admin_init', $plugin_admin, 'handle_export_logs');
		$this->loader->add_action('wp_ajax_trvlr_clear_all_logs', $plugin_admin, 'ajax_clear_all_logs');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{

		$plugin_public = new Trvlr_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

		// Template loading
		$this->loader->add_filter('template_include', $plugin_public, 'load_attraction_template');

		// SVG icons in footer
		$this->loader->add_action('wp_footer', $plugin_public, 'add_global_svg_icons');

		// Google Fonts
		$this->loader->add_action('wp_head', $plugin_public, 'add_google_fonts');

		// Theme CSS Variables
		$this->loader->add_action('wp_head', $plugin_public, 'output_theme_css_variables');

		$this->loader->add_filter('trvlr_duration', $plugin_public, 'filter_trvlr_duration', 10, 2);
		$this->loader->add_filter('trvlr_start_time', $plugin_public, 'filter_trvlr_time', 10, 2);
		$this->loader->add_filter('trvlr_end_time', $plugin_public, 'filter_trvlr_time', 10, 2);
		$this->loader->add_filter('trvlr_pricing', $plugin_public, 'filter_trvlr_pricing', 10, 2);

		// Payment page functionality
		$this->loader->add_filter('body_class', $plugin_public, 'add_payment_page_body_class');
		$this->loader->add_filter('redirect_canonical', $plugin_public, 'disable_redirect_for_payment_page', 10, 2);
		$this->loader->add_filter('the_content', $plugin_public, 'render_payment_confirmation_content');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Trvlr_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
