<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Trvlr
 * @subpackage Trvlr/public
 */

class Trvlr_Public
{

	private $plugin_name;
	private $version;
	private $dev_instance;

	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->init_dev_environment();
	}

	/**
	 * Initialize dev environment if dev class exists
	 */
	private function init_dev_environment()
	{

		$dev_class_file = TRVLR_PLUGIN_DIR . '~dev/dev-class-trvlr-public.php';

		if (file_exists($dev_class_file)) {
			require_once $dev_class_file;

			if (class_exists('Trvlr_Public_Dev')) {
				$this->dev_instance = new Trvlr_Public_Dev($this->plugin_name, $this->version, $this);
				$this->dev_instance->init();
			}
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_styles()
	{
		if (trvlr_is_vite_hot()) {
			wp_register_style('trvlr-attraction-filter', false);
			return;
		}

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'dist/css/trvlr-public.css', array(), $this->version, 'all');

		wp_enqueue_style('trvlr-cards-styles', plugin_dir_url(__FILE__) . 'dist/css/trvlr-cards.css', array(), $this->version, 'all');
		wp_enqueue_style('trvlr-gallery-styles', plugin_dir_url(__FILE__) . 'dist/css/trvlr-gallery.css', array(), $this->version, 'all');

		if (class_exists('Trvlr_Template_Registry')) {
			$presentation_theme_css = Trvlr_Template_Registry::get_active_presentation_theme_stylesheet_basename();
			if ($presentation_theme_css !== '') {
				$presentation_theme_path = plugin_dir_path(__FILE__) . 'dist/css/' . $presentation_theme_css;
				if (is_readable($presentation_theme_path)) {
					wp_enqueue_style(
						'trvlr-presentation-theme',
						plugin_dir_url(__FILE__) . 'dist/css/' . $presentation_theme_css,
						array(),
						filemtime($presentation_theme_path) ? (string) filemtime($presentation_theme_path) : $this->version,
						'all'
					);
				}
			}
		}

		wp_register_style(
			'trvlr-attraction-filter',
			plugin_dir_url(__FILE__) . 'dist/css/trvlr-attraction-filter.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Output CSS variables from theme settings
	 * Uses Trvlr_Theme_Config for consistency
	 */
	public function output_theme_css_variables()
	{
		$user_settings = get_option('trvlr_theme_settings', array());
		$settings = Trvlr_Theme_Config::merge_with_defaults($user_settings);

		$css = Trvlr_Theme_Config::generate_css_variables($settings);
		echo '<style id="trvlr-theme-variables">:root{' . $css . '}</style>';
	}

	/**
	 * Load custom template for single attractions
	 */
	public function load_attraction_template($template)
	{
		if (!is_singular('trvlr_attraction')) {
			return $template;
		}

		$use_plugin_partial = apply_filters('trvlr_use_plugin_single_attraction_template', true, $template);
		if (!$use_plugin_partial) {
			return $template;
		}

		$plugin_template = plugin_dir_path(__FILE__) . 'partials/single-trvlr_attraction.php';
		if (file_exists($plugin_template)) {
			return $plugin_template;
		}

		return $template;
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 */
	public function enqueue_scripts()
	{
		if (! function_exists('trvlr_is_frontend_booking_disabled')) {
			require_once TRVLR_PLUGIN_DIR . 'includes/trvlr-feature-flags.php';
		}

		trvlr_enqueue_vite_hmr();
		$this->register_gallery_assets();

		if (trvlr_is_vite_hot()) {
			$this->enqueue_scripts_vite();
			return;
		}

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'dist/js/trvlr-public.js', array('jquery'), $this->version, false);

		if (class_exists('Trvlr_Template_Registry')) {
			$presentation_theme_script = Trvlr_Template_Registry::get_active_presentation_theme_script_basename();
			if ($presentation_theme_script !== '') {
				$presentation_theme_script_path = plugin_dir_path(__FILE__) . 'dist/js/' . $presentation_theme_script;
				if (is_readable($presentation_theme_script_path)) {
					wp_enqueue_script(
						'trvlr-presentation-theme',
						plugin_dir_url(__FILE__) . 'dist/js/' . $presentation_theme_script,
						array('jquery'),
						filemtime($presentation_theme_script_path) ? (string) filemtime($presentation_theme_script_path) : $this->version,
						true
					);
				}
			}
		}

		wp_register_script(
			'trvlr-query-manager',
			plugin_dir_url(__FILE__) . 'dist/js/trvlr-query-manager.js',
			array(),
			$this->version,
			true
		);
		wp_localize_script('trvlr-query-manager', 'trvlrQueryManagerConfig', array(
			'apiUrl' => rest_url('trvlr/v1/cards'),
		));

		wp_register_script(
			'trvlr-attraction-filter',
			plugin_dir_url(__FILE__) . 'dist/js/trvlr-attraction-filter.js',
			array('trvlr-query-manager'),
			$this->version,
			true
		);

		if (trvlr_is_frontend_booking_disabled()) {
			return;
		}

		wp_enqueue_script('trvlr-bookings-script', plugin_dir_url(__FILE__) . 'dist/js/trvlr-bookings.js', array(), $this->version, true);

		wp_localize_script('trvlr-bookings-script', 'trvlrConfig', array(
			'baseIframeUrl' => $this->get_trvlr_base_domain(),
			'homeUrl' => home_url()
		));
	}

	private function enqueue_scripts_vite()
	{
		$boot = trvlr_vite_boot_handle();

		wp_enqueue_script_module(
			'trvlr',
			trvlr_vite_url('public/src/scripts/trvlr-public.js'),
			array(),
			null
		);

		if (class_exists('Trvlr_Template_Registry')) {
			$presentation_theme_script = Trvlr_Template_Registry::get_active_presentation_theme_script_basename();
			if ($presentation_theme_script !== '') {
				$src_rel = 'public/src/scripts/' . preg_replace('/\.js$/i', '.js', $presentation_theme_script);
				if (is_readable(TRVLR_PLUGIN_DIR . $src_rel)) {
					wp_enqueue_script_module(
						'trvlr-presentation-theme',
						trvlr_vite_url($src_rel),
						array(),
						null
					);
				}
			}
		}

		wp_register_script_module(
			'trvlr-query-manager',
			trvlr_vite_url('public/src/scripts/trvlr-query-manager.js')
		);
		wp_add_inline_script(
			$boot,
			'window.trvlrQueryManagerConfig = ' . wp_json_encode(array(
				'apiUrl' => rest_url('trvlr/v1/cards'),
			)) . ';',
			'before'
		);

		wp_register_script_module(
			'trvlr-attraction-filter',
			trvlr_vite_url('public/src/scripts/trvlr-attraction-filter.js'),
			array('trvlr-query-manager')
		);

		if (trvlr_is_frontend_booking_disabled()) {
			return;
		}

		wp_enqueue_script_module(
			'trvlr-bookings-script',
			trvlr_vite_url('public/src/scripts/trvlr-bookings.js'),
			array(),
			null
		);
		wp_add_inline_script(
			$boot,
			'window.trvlrConfig = ' . wp_json_encode(array(
				'baseIframeUrl' => $this->get_trvlr_base_domain(),
				'homeUrl' => home_url(),
			)) . ';',
			'before'
		);
	}

	/**
	 * Add custom body class for payment confirmation page
	 */
	public function add_payment_page_body_class($classes)
	{
		$payment_page_id = get_option('trvlr_payment_page_id');

		if ($payment_page_id && is_page($payment_page_id)) {
			$classes[] = 'trvlr-payment-confirmation-page';
		}

		return $classes;
	}

	public function add_presentation_theme_body_class($classes)
	{
		if (!class_exists('Trvlr_Template_Registry')) {
			return $classes;
		}

		$slug = Trvlr_Template_Registry::get_active_presentation_theme_slug();
		if ($slug !== '') {
			$classes[] = 'trvlr--' . sanitize_html_class($slug);
		}

		return $classes;
	}

	/**
	 * Remove trailing slash redirect for payment page with query params
	 */
	public function disable_redirect_for_payment_page($redirect_url, $requested_url)
	{
		if (! is_page()) {
			return $redirect_url;
		}

		$payment_page_id = get_option('trvlr_payment_page_id');

		if (! $payment_page_id || get_queried_object_id() !== (int) $payment_page_id) {
			return $redirect_url;
		}

		// If there are query parameters, prevent redirect
		if (! empty($_GET)) {
			return false;
		}

		return $redirect_url;
	}

	/**
	 * Render payment confirmation page content
	 */
	public function render_payment_confirmation_content($content)
	{
		$payment_page_id = get_option('trvlr_payment_page_id');

		if (! $payment_page_id || ! is_page($payment_page_id) || ! is_main_query()) {
			return $content;
		}

		if (has_shortcode($content, 'trvlr_payment_confirmation')) {
			return $content;
		}

		return trvlr_payment_confirmation_markup();
	}

	/**
	 * Get base domain from Organisation ID
	 * Wrapper for global helper function
	 */
	public function get_trvlr_base_domain($org_id = null)
	{
		return get_trvlr_base_domain($org_id);
	}

	/**
	 * Inject Booking Modal
	 */
	public function inject_booking_modal()
	{
		$base_iframe_url = $this->get_trvlr_base_domain();
?>
		<dialog id="trvlr-booking-modal" class="modal-dialog">
			<div id="trvlr-booking-modal-content" class="iframe-cont">
			</div>
		</dialog>

		<div id="checkout-modal-iframe" style="display: none">
			<iframe src="<?php echo esc_url($base_iframe_url); ?>/checkout-modal/index.html" frameborder="0" title="Checkout Modal"
				class="iframe-cont" id="checkout-modal-btn-iframe">
			</iframe>
		</div>
	<?php
	}

	/**
	 * Inject Google Fonts
	 */
	public function add_google_fonts()
	{
	?>
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Montserrat:ital,wght@0,700;1,700&family=Rethink+Sans:ital,wght@0,400..800;1,400..800&display=swap" rel="stylesheet">
	<?php
	}


	public function add_global_svg_icons()
	{
		if (class_exists('Trvlr_Icons')) {
			Trvlr_Icons::print_sprite();
		}
	}

	/**
	 * Filter: Format duration string
	 * Input: "0-5-15" (days-hours-mins)
	 * Output: "5 hours 15 mins"
	 */
	public function filter_trvlr_duration($duration, $post_id)
	{
		if (empty($duration) || !is_string($duration)) {
			return $duration;
		}

		// Check for "d-h-m" format
		if (preg_match('/^(\d+)-(\d+)-(\d+)$/', $duration, $matches)) {
			$days = intval($matches[1]);
			$hours = intval($matches[2]);
			$minutes = intval($matches[3]);

			$parts = array();

			if ($days > 0) {
				$parts[] = $days . ' ' . _n('day', 'days', $days, 'trvlr');
			}

			if ($hours > 0) {
				$parts[] = $hours . ' ' . _n('hour', 'hours', $hours, 'trvlr');
			}

			if ($minutes > 0) {
				$parts[] = $minutes . ' ' . _n('min', 'mins', $minutes, 'trvlr');
			}

			if (!empty($parts)) {
				return implode(' ', $parts);
			}
		}

		return $duration;
	}

	/**
	 * Filter: Format time string
	 * Input: "08:00" (24h)
	 * Output: "8:00 am"
	 */
	public function filter_trvlr_time($time, $post_id)
	{
		if (empty($time)) {
			return $time;
		}

		// Try to parse time
		$timestamp = strtotime($time);
		if ($timestamp !== false) {
			return date('g:i a', $timestamp);
		}

		return $time;
	}

	/**
	 * Filter: Format pricing array
	 */
	public function filter_trvlr_pricing($pricing, $post_id)
	{
		if (empty($pricing) || !is_array($pricing)) {
			return $pricing;
		}

		$adult_price_types = array(
			'adult',
			'adults',
			'adult - udw',
			'single supplement',
			'quantity',
		);

		foreach ($pricing as $key => $row) {
			if (empty($row['type'])) {
				continue;
			}

			$type = strtolower(trim($row['type']));

			if (in_array($type, $adult_price_types, true)) {
				$pricing[$key]['type'] = __('per person', 'trvlr');
			} elseif ($type === 'child 5-16 udw') {
				$pricing[$key]['type'] = __('per child', 'trvlr');
			}
		}

		return $pricing;
	}

	private function register_gallery_assets()
	{
		static $registered = false;
		if ($registered) {
			return;
		}
		$registered = true;

		if (trvlr_is_vite_hot()) {
			wp_register_script_module(
				'trvlr-gallery-slider',
				trvlr_vite_url('public/src/scripts/trvlr-gallery-slider.js')
			);
			wp_register_style('trvlr-gallery-slider', false);
			wp_register_script_module(
				'trvlr-gallery-masonry',
				trvlr_vite_url('public/src/scripts/trvlr-gallery-masonry.js')
			);
			wp_register_style('trvlr-gallery-masonry', false);
			return;
		}

		$slider_js = plugin_dir_path(__FILE__) . 'dist/js/trvlr-gallery-slider.js';
		$slider_css = plugin_dir_path(__FILE__) . 'dist/css/trvlr-gallery-slider.css';
		$slider_ver = is_readable($slider_js) ? (string) filemtime($slider_js) : $this->version;

		wp_register_script(
			'trvlr-gallery-slider',
			plugin_dir_url(__FILE__) . 'dist/js/trvlr-gallery-slider.js',
			array(),
			$slider_ver,
			true
		);

		wp_register_style(
			'trvlr-gallery-slider',
			plugin_dir_url(__FILE__) . 'dist/css/trvlr-gallery-slider.css',
			array(),
			is_readable($slider_css) ? (string) filemtime($slider_css) : $slider_ver,
			'all'
		);

		$masonry_js = plugin_dir_path(__FILE__) . 'dist/js/trvlr-gallery-masonry.js';
		$masonry_css = plugin_dir_path(__FILE__) . 'dist/css/trvlr-gallery-masonry.css';
		$masonry_ver = is_readable($masonry_js) ? (string) filemtime($masonry_js) : $this->version;

		wp_register_script(
			'trvlr-gallery-masonry',
			plugin_dir_url(__FILE__) . 'dist/js/trvlr-gallery-masonry.js',
			array(),
			$masonry_ver,
			true
		);

		wp_register_style(
			'trvlr-gallery-masonry',
			plugin_dir_url(__FILE__) . 'dist/css/trvlr-gallery-masonry.css',
			array(),
			is_readable($masonry_css) ? (string) filemtime($masonry_css) : $masonry_ver,
			'all'
		);
	}
}
