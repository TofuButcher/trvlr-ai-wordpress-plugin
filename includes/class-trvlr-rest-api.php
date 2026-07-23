<?php

/**
 * REST API controller for TRVLR admin and public card endpoints.
 *
 * @package    Trvlr
 * @subpackage Trvlr/includes
 */

class Trvlr_REST_API
{
	private $namespace = 'trvlr/v1';

	public function register_routes()
	{
		$this->register_settings_routes();
		$this->register_sync_routes();
		$this->register_logs_routes();
		$this->register_setup_routes();
		$this->register_cards_routes();
	}

	private function register_settings_routes()
	{
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

	private function register_sync_routes()
	{
		register_rest_route($this->namespace, '/sync/stats', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_sync_stats'),
			'permission_callback' => array($this, 'check_admin_permission'),
		));

		register_rest_route($this->namespace, '/sync/manual', array(
			'methods' => 'POST',
			'callback' => array($this, 'trigger_manual_sync'),
			'permission_callback' => array($this, 'check_admin_permission'),
		));

		register_rest_route($this->namespace, '/sync/manual-no-media', array(
			'methods' => 'POST',
			'callback' => array($this, 'trigger_manual_sync_no_media'),
			'permission_callback' => array($this, 'check_admin_permission'),
		));

		register_rest_route($this->namespace, '/sync/cancel', array(
			'methods' => 'POST',
			'callback' => array($this, 'cancel_sync'),
			'permission_callback' => array($this, 'check_admin_permission'),
		));

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

		register_rest_route($this->namespace, '/sync/custom-edits', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_custom_edits'),
			'permission_callback' => array($this, 'check_admin_permission'),
		));

		register_rest_route($this->namespace, '/sync/clear-custom-edits', array(
			'methods' => 'POST',
			'callback' => array($this, 'clear_custom_edits'),
			'permission_callback' => array($this, 'check_admin_permission'),
		));

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

	private function register_logs_routes()
	{
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

		register_rest_route($this->namespace, '/logs/clear-old', array(
			'methods' => 'POST',
			'callback' => array($this, 'clear_old_logs'),
			'permission_callback' => array($this, 'check_admin_permission'),
		));

		register_rest_route($this->namespace, '/logs/clear-all', array(
			'methods' => 'POST',
			'callback' => array($this, 'clear_all_logs'),
			'permission_callback' => array($this, 'check_admin_permission'),
		));

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

	private function register_setup_routes()
	{
		register_rest_route($this->namespace, '/setup/status', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_system_status'),
			'permission_callback' => array($this, 'check_admin_permission'),
		));

		register_rest_route($this->namespace, '/setup/payment-page', array(
			'methods' => 'POST',
			'callback' => array($this, 'create_payment_page'),
			'permission_callback' => array($this, 'check_admin_permission'),
		));

		register_rest_route($this->namespace, '/setup/test-connection', array(
			'methods' => 'POST',
			'callback' => array($this, 'test_api_connection'),
			'permission_callback' => array($this, 'check_admin_permission'),
		));
	}

	private function register_cards_routes()
	{
		$string_param = array('type' => 'string', 'sanitize_callback' => 'sanitize_text_field');
		$relation_param = array('type' => 'string', 'enum' => array('AND', 'OR'));

		$args = array(
			'posts_per_page'    => array('type' => 'integer', 'default' => 16, 'minimum' => -1, 'maximum' => 100),
			'paged'             => array('type' => 'integer', 'default' => 1, 'minimum' => 1),
			'orderby'           => array_merge($string_param, array('default' => 'date')),
			'order'             => array('type' => 'string', 'default' => 'DESC', 'enum' => array('ASC', 'DESC', 'asc', 'desc'), 'sanitize_callback' => static function ($v) { return strtoupper($v); }),
			'ids'               => $string_param,
			'exclude'           => $string_param,
			'tag'               => $string_param,
			'tag_id'            => $string_param,
			'tag_slug'          => $string_param,
			'tag_relation'      => $relation_param,
			'category'          => $string_param,
			'category_id'       => $string_param,
			'category_slug'     => $string_param,
			'category_relation' => $relation_param,
			'trvlr_tag'         => $string_param,
			'trvlr_tag_id'      => $string_param,
			'trvlr_tag_slug'    => $string_param,
			'trvlr_tag_relation' => $relation_param,
			'trvlr_sort'        => $string_param,
			'meta_key'          => $string_param,
			'meta_value'        => $string_param,
			'meta_compare'      => $string_param,
			'card_variant'      => $string_param,
		);

		register_rest_route($this->namespace, '/cards', array(
			array(
				'methods'             => 'GET',
				'callback'            => array($this, 'get_cards'),
				'permission_callback' => '__return_true',
				'args'                => $args,
			),
			array(
				'methods'             => 'POST',
				'callback'            => array($this, 'get_cards'),
				'permission_callback' => '__return_true',
			),
		));
	}

	/**
	 * GET|POST /trvlr/v1/cards
	 *
	 * Public endpoint — returns rendered attraction card HTML plus pagination metadata.
	 * Accepts the same named params as `trvlr_attraction_cards` shortcode via GET query
	 * string. POST may include a `query_args` object for a full WP_Query override
	 * (same as `trvlr_cards()` / `trvlr_build_query_args()`).
	 *
	 * The `trvlr_ajax_query_args` filter fires before execution.
	 *
	 * Response shape:
	 *   html         string  Inner `<div class="trvlr-cards">` element.
	 *   found_posts  int     Total matching posts (ignores pagination).
	 *   max_pages    int     Total pages for the current posts_per_page.
	 *   current_page int     Page rendered in this response.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_cards($request)
	{
		$named_params = array(
			'posts_per_page', 'paged', 'orderby', 'order',
			'ids', 'exclude',
			'tag', 'tag_id', 'tag_slug', 'tag_relation',
			'category', 'category_id', 'category_slug', 'category_relation',
			'trvlr_tag', 'trvlr_tag_id', 'trvlr_tag_slug', 'trvlr_tag_relation',
			'trvlr_sort',
			'meta_key', 'meta_value', 'meta_compare',
			'card_variant',
		);

		$args = array();
		foreach ($named_params as $param) {
			$value = $request->get_param($param);
			if ($value !== null && $value !== '') {
				$args[$param] = $value;
			}
		}

		if (isset($args['posts_per_page'])) {
			$posts_per_page = intval($args['posts_per_page']);
			$args['posts_per_page'] = $posts_per_page === -1 ? -1 : min(100, max(1, $posts_per_page));
		}
		if (isset($args['paged'])) {
			$args['paged'] = max(1, intval($args['paged']));
		}

		$supported_card_variants = array('default', 'expanded');
		$card_variant = 'default';
		if (!empty($args['card_variant']) && in_array($args['card_variant'], $supported_card_variants, true)) {
			$card_variant = $args['card_variant'];
		}
		unset($args['card_variant']);

		$body = $request->get_json_params();
		if (!empty($body['query_args']) && is_array($body['query_args'])) {
			$args['query_args'] = $body['query_args'];
		}
		// card_variant may also be sent at the top level of a POST JSON body.
		if (!empty($body['card_variant']) && in_array($body['card_variant'], $supported_card_variants, true)) {
			$card_variant = $body['card_variant'];
		}

		$query_args = trvlr_build_query_args($args);

		$query_args = apply_filters('trvlr_ajax_query_args', $query_args, $args, $request);

		$query_args['post_type']   = 'trvlr_attraction';
		$query_args['post_status'] = 'publish';

		$result = trvlr_build_cards_result($query_args, $card_variant);

		return rest_ensure_response($result);
	}

	public function check_admin_permission()
	{
		$can_manage = current_user_can('manage_options');

		// Only log denials when WP_DEBUG is on — progress polls hit this every ~2s.
		if (!$can_manage && defined('WP_DEBUG') && WP_DEBUG) {
			error_log('TRVLR REST API: Permission denied for user ' . get_current_user_id() . ' on ' . ($_SERVER['REQUEST_URI'] ?? ''));
		}

		return $can_manage;
	}

	public function get_theme_settings($request)
	{
		$stored = get_option('trvlr_theme_settings', array());
		$merged = Trvlr_Theme_Config::merge_with_defaults(is_array($stored) ? $stored : array());
		if (class_exists('Trvlr_Template_Registry')) {
			$merged['presentationTheme'] = Trvlr_Template_Registry::get_active_presentation_theme_slug();
			$merged['cardTemplate'] = Trvlr_Template_Registry::get_active_card_slug();
			$merged['attractionPageTemplate'] = Trvlr_Template_Registry::get_active_single_slug();
		}
		return rest_ensure_response($merged);
	}

	public function update_theme_settings($request)
	{
		$settings = $request->get_json_params();
		if (empty($settings) || !is_array($settings)) {
			return new WP_Error('invalid_data', 'Invalid settings data', array('status' => 400));
		}

		if (class_exists('Trvlr_Template_Registry')) {
			if (array_key_exists('presentationTheme', $settings)) {
				$pt = sanitize_key((string) $settings['presentationTheme']);
				if ($pt !== '' && isset(Trvlr_Template_Registry::get_presentation_themes()[$pt])) {
					Trvlr_Template_Registry::set_active_presentation_theme($pt);
				}
			}
		}

		$theme_only = $settings;
		unset(
			$theme_only['presentationTheme'],
			$theme_only['cardTemplate'],
			$theme_only['attractionPageTemplate']
		);
		update_option('trvlr_theme_settings', $theme_only);

		$returned = Trvlr_Theme_Config::merge_with_defaults($theme_only);
		if (class_exists('Trvlr_Template_Registry')) {
			$returned['presentationTheme'] = Trvlr_Template_Registry::get_active_presentation_theme_slug();
			$returned['cardTemplate'] = Trvlr_Template_Registry::get_active_card_slug();
			$returned['attractionPageTemplate'] = Trvlr_Template_Registry::get_active_single_slug();
		}

		return rest_ensure_response(array('success' => true, 'settings' => $returned));
	}

	public function get_connection_settings($request)
	{
		if (! function_exists('trvlr_get_connection_settings_array')) {
			require_once plugin_dir_path(__FILE__) . 'trvlr-feature-flags.php';
		}
		return rest_ensure_response(trvlr_get_connection_settings_array());
	}

	public function update_connection_settings($request)
	{
		if (! function_exists('trvlr_update_connection_settings_from_request')) {
			require_once plugin_dir_path(__FILE__) . 'trvlr-feature-flags.php';
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

	public function get_sync_stats($request)
	{
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

		return rest_ensure_response(array(
			'total_attractions' => $total_attractions,
			'synced_count' => $total_attractions - $custom_edit_count,
			'custom_edit_count' => $custom_edit_count,
		));
	}

	public function trigger_manual_sync($request)
	{
		if (! function_exists('trvlr_is_attraction_sync_disabled')) {
			require_once plugin_dir_path(__FILE__) . 'trvlr-feature-flags.php';
		}
		if (trvlr_is_attraction_sync_disabled()) {
			return new WP_Error(
				'sync_disabled',
				__('Attraction syncing is disabled in TRVLR settings.', 'trvlr'),
				array('status' => 403)
			);
		}

		try {
			require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-field-map.php';
			require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-logger.php';
			require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-notifier.php';
			require_once plugin_dir_path(dirname(__FILE__)) . 'core/class-trvlr-sync.php';

			$syncer = new Trvlr_Sync();
			$result = $syncer->start_sync();

			if (!$result['success']) {
				return new WP_Error(
					'sync_start_failed',
					$result['message'],
					array('status' => 400)
				);
			}

			return rest_ensure_response($result);
		} catch (Exception $e) {
			if (class_exists('Trvlr_Logger')) {
				Trvlr_Logger::log('error', 'Manual sync failed: ' . $e->getMessage(), array(
					'trace' => $e->getTraceAsString()
				));
			}
			error_log('TRVLR Manual Sync Error: ' . $e->getMessage());

			return new WP_Error(
				'sync_failed',
				'Sync failed: ' . $e->getMessage(),
				array('status' => 500)
			);
		}
	}

	public function trigger_manual_sync_no_media($request)
	{
		if (! function_exists('trvlr_is_attraction_sync_disabled')) {
			require_once plugin_dir_path(__FILE__) . 'trvlr-feature-flags.php';
		}
		if (trvlr_is_attraction_sync_disabled()) {
			return new WP_Error(
				'sync_disabled',
				__('Attraction syncing is disabled in TRVLR settings.', 'trvlr'),
				array('status' => 403)
			);
		}

		try {
			require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-field-map.php';
			require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-logger.php';
			require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-notifier.php';
			require_once plugin_dir_path(dirname(__FILE__)) . 'core/class-trvlr-sync.php';

			$syncer = new Trvlr_Sync();
			$result = $syncer->start_sync_no_media();

			if (!$result['success']) {
				return new WP_Error(
					'sync_start_failed',
					$result['message'],
					array('status' => 400)
				);
			}

			return rest_ensure_response($result);
		} catch (Exception $e) {
			if (class_exists('Trvlr_Logger')) {
				Trvlr_Logger::log('error', 'Manual sync (no media) failed: ' . $e->getMessage(), array(
					'trace' => $e->getTraceAsString()
				));
			}
			error_log('TRVLR Manual Sync (no media) Error: ' . $e->getMessage());

			return new WP_Error(
				'sync_failed',
				'Sync failed: ' . $e->getMessage(),
				array('status' => 500)
			);
		}
	}

	public function cancel_sync($request)
	{
		try {
			require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-logger.php';
			require_once plugin_dir_path(dirname(__FILE__)) . 'core/class-trvlr-sync.php';

			$syncer = new Trvlr_Sync();
			$result = $syncer->cancel_sync();

			if (!$result['success']) {
				return new WP_Error(
					'cancel_failed',
					$result['message'],
					array('status' => 400)
				);
			}

			return rest_ensure_response($result);
		} catch (Exception $e) {
			error_log('TRVLR Cancel Sync Error: ' . $e->getMessage());
			return new WP_Error(
				'cancel_failed',
				'Cancel failed: ' . $e->getMessage(),
				array('status' => 500)
			);
		}
	}

	public function get_schedule_settings($request)
	{
		if (! function_exists('trvlr_is_attraction_sync_disabled')) {
			require_once plugin_dir_path(__FILE__) . 'trvlr-feature-flags.php';
		}
		if (trvlr_is_attraction_sync_disabled()) {
			return rest_ensure_response(array(
				'enabled' => false,
				'frequency' => Trvlr_Scheduler::get_sync_frequency(),
				'next_sync' => null,
			));
		}

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
		if (! function_exists('trvlr_is_attraction_sync_disabled')) {
			require_once plugin_dir_path(__FILE__) . 'trvlr-feature-flags.php';
		}
		if (trvlr_is_attraction_sync_disabled()) {
			return new WP_Error(
				'sync_disabled',
				__('Attraction syncing is disabled in TRVLR settings.', 'trvlr'),
				array('status' => 403)
			);
		}

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

	/**
	 * List attractions that currently have Custom Edit fields.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
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
			$edited_fields = trvlr_get_custom_edit_fields($post->ID);

			$results[] = array(
				'id' => $post->ID,
				'title' => html_entity_decode(get_the_title($post->ID), ENT_QUOTES, 'UTF-8'),
				'edit_url' => get_edit_post_link($post->ID, 'raw'),
				'modified' => get_the_modified_date('Y-m-d H:i', $post->ID),
				'edited_fields' => $edited_fields,
				'edited_fields_labels' => array_values(array_map(function ($field) use ($field_labels) {
					return isset($field_labels[$field]) ? $field_labels[$field] : $field;
				}, $edited_fields)),
			);
		}

		return rest_ensure_response($results);
	}

	/**
	 * Clear Custom Edit fields for one post, selected fields, or the whole site.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response|WP_Error
	 */
	public function clear_custom_edits($request)
	{
		$data = $request->get_json_params();
		if (!is_array($data)) {
			$data = array();
		}

		$post_id = isset($data['post_id']) ? absint($data['post_id']) : 0;
		$fields = isset($data['fields']) && is_array($data['fields'])
			? array_map('sanitize_text_field', $data['fields'])
			: array();

		if ($post_id) {
			if (get_post_type($post_id) !== 'trvlr_attraction') {
				return new WP_Error('invalid_post', 'Invalid attraction.', array('status' => 400));
			}

			Trvlr_Custom_Edits::clear_fields($post_id, $fields);

			return rest_ensure_response(array(
				'success' => true,
				'message' => empty($fields)
					? __('Cleared all custom edits for this attraction.', 'trvlr')
					: sprintf(
						_n('Cleared %d custom edit field.', 'Cleared %d custom edit fields.', count($fields), 'trvlr'),
						count($fields)
					),
				'edited_fields' => trvlr_get_custom_edit_fields($post_id),
			));
		}

		$count = Trvlr_Custom_Edits::clear_all_sitewide();

		return rest_ensure_response(array(
			'success' => true,
			'message' => sprintf(__('Cleared custom edits from %d attraction(s).', 'trvlr'), $count),
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
