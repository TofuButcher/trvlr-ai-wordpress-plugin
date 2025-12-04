<?php

/**
 * Fired during plugin activation
 *
 * @package    Trvlr
 * @subpackage Trvlr/includes
 */
class Trvlr_Activator
{

	/**
	 * Fired during plugin activation.
	 *
	 * This class defines all code necessary to run during the plugin's activation.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		// Register the post type so flush_rewrite_rules works
		require_once plugin_dir_path(dirname(__FILE__)) . 'core/class-trvlr-attraction.php';

		$attraction = new Trvlr_Attraction();
		$attraction->register_post_type();

		// Create database tables
		self::create_database_tables();

		// Create payment confirmation page
		self::create_payment_confirmation_page();

		// Schedule log cleanup
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-logger.php';
		Trvlr_Logger::schedule_cleanup();

		// Schedule weekly summary if notifications enabled
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-notifier.php';
		Trvlr_Notifier::schedule_weekly_summary();

		flush_rewrite_rules();
	}

	/**
	 * Create database tables for the plugin
	 *
	 * @since    1.0.0
	 */
	public static function create_database_tables()
	{
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'trvlr_sync_logs';

		$sql = "CREATE TABLE $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			log_type varchar(50) NOT NULL,
			message text NOT NULL,
			details longtext,
			created_at datetime NOT NULL,
			user_id bigint(20) DEFAULT 0,
			PRIMARY KEY  (id),
			KEY log_type (log_type),
			KEY created_at (created_at)
		) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	/**
	 * Create the payment confirmation page.
	 *
	 * @since    1.0.0
	 */
	public static function create_payment_confirmation_page()
	{
		// Check if page already exists
		$existing_page = get_page_by_path('payments');

		if ($existing_page) {
			// Store the page ID for reference
			update_option('trvlr_payment_page_id', $existing_page->ID);
			return $existing_page->ID;
		}

		// Create the page
		$page_data = array(
			'post_title'     => __('Payment Confirmation', 'trvlr'),
			'post_name'      => 'payments',
			'post_content'   => '',
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_author'    => 1,
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		);

		$page_id = wp_insert_post($page_data);

		if ($page_id && ! is_wp_error($page_id)) {
			// Store the page ID
			update_option('trvlr_payment_page_id', $page_id);

			// Mark this page as a special TRVLR page
			update_post_meta($page_id, '_trvlr_payment_confirmation_page', '1');

			return $page_id;
		}

		return false;
	}
}
