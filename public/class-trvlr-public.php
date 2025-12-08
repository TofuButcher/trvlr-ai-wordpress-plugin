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

	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/trvlr-public.css', array(), $this->version, 'all');
		wp_enqueue_style('splide', plugin_dir_url(__FILE__) . 'dist/splide.min.css', array(), '4.1.3', 'all');
		wp_enqueue_style('trvlr-modal-styles', plugin_dir_url(__FILE__) . 'css/trvlr-modal-styles.css', array(), $this->version, 'all');
	}

	/**
	 * Output CSS variables from theme settings
	 * Uses Trvlr_Theme_Config for consistency
	 */
	public function output_theme_css_variables()
	{
		$user_settings = get_option('trvlr_theme_settings', array());
		$settings = Trvlr_Theme_Config::merge_with_defaults($user_settings);

		echo '<style id="trvlr-theme-variables">:root{';
		echo esc_attr(Trvlr_Theme_Config::generate_css_variables($settings));
		echo '}</style>';
	}

	/**
	 * Load custom template for single attractions
	 */
	public function load_attraction_template($template)
	{
		if (is_singular('trvlr_attraction')) {
			$plugin_template = plugin_dir_path(__FILE__) . 'partials/single-trvlr_attraction.php';
			if (file_exists($plugin_template)) {
				return $plugin_template;
			}
		}
		return $template;
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script('splide', plugin_dir_url(__FILE__) . '/dist/splide.min.js', array(), '4.1.3', true);
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/trvlr-public.js', array('jquery', 'splide'), $this->version, false);

		wp_enqueue_script('trvlr-bookings-script', plugin_dir_url(__FILE__) . 'js/trvlr-bookings.js', array(), $this->version, true);

		wp_localize_script('trvlr-bookings-script', 'trvlrConfig', array(
			'baseIframeUrl' => $this->get_trvlr_base_domain(),
			'homeUrl' => home_url()
		));
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

		// Get Organisation ID
		$org_id = $this->get_trvlr_organisation_id();

		if (empty($org_id)) {
			return '<p>' . __('Sorry. This site has not been connected to trvlr.ai properly...  Please contact support.', 'trvlr') . '</p>';
		}

		// Build base domain
		$base_domain = $this->get_trvlr_base_domain($org_id);

		ob_start();
?>
		<div id="trvlr-payment-confirmation-container" class="trvlr-payment-wrapper">
			<iframe
				id="trvlr-payment-confirmation-iframe"
				src="<?php echo esc_url($base_domain . '/payment/confirmation.html'); ?>"
				title="<?php esc_attr_e('Payment Confirmation', 'trvlr'); ?>"
				frameborder="0"></iframe>
		</div>
		<script>
			(function() {
				window.addEventListener('message', function(event) {
					console.log('Payment confirmation message received:', event.data);

					if (event.data.type === 'REFRESH_PAGE') {
						console.log('Setting refresh page flag in localStorage');
						localStorage.setItem('isRefreshPage', 'true');

						setTimeout(function() {
							window.location.href = '<?php echo esc_url(home_url()); ?>';
						}, 2000);
					}
				});
			})();
		</script>
	<?php
		return ob_get_clean();
	}

	/**
	 * Get Organisation ID / Trvlr ID
	 * Wrapper for global helper function
	 */
	private function get_trvlr_organisation_id()
	{
		return get_trvlr_organisation_id();
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


	/**
	 * Add global svg icons to the head
	 */
	public function add_global_svg_icons()
	{
	?>
		<svg style="display: none;">
			<symbol id="icon-star" viewBox="0 0 18 18">
				<path d="M9.00002 0.5C9.38064 0.5 9.72803 0.716313 9.8965 1.05762L11.9805 5.28027L16.6446 5.96289C17.0211 6.01793 17.3338 6.28252 17.4512 6.64453C17.5684 7.00643 17.4698 7.40351 17.1973 7.66895L13.8242 10.9531L14.6211 15.5957C14.6855 15.9709 14.5307 16.3505 14.2227 16.5742C13.9148 16.7978 13.5067 16.8273 13.1699 16.6504L9.00002 14.457L4.8301 16.6504C4.49331 16.8273 4.0852 16.7978 3.77736 16.5742C3.46939 16.3505 3.31458 15.9709 3.37893 15.5957L4.17482 10.9531L0.802754 7.66895C0.530236 7.40351 0.431671 7.00643 0.548848 6.64453C0.666226 6.28252 0.978929 6.01793 1.35549 5.96289L6.01857 5.28027L8.10354 1.05762L8.17482 0.935547C8.35943 0.665559 8.66699 0.5 9.00002 0.5ZM7.57912 6.6377C7.43357 6.93249 7.15248 7.13702 6.82717 7.18457L3.64748 7.64844L5.94729 9.88867C6.18316 10.1184 6.29103 10.4499 6.23537 10.7744L5.6924 13.9365L8.5342 12.4424C8.82558 12.2891 9.17446 12.2891 9.46584 12.4424L12.3067 13.9365L11.7647 10.7744C11.709 10.4499 11.8169 10.1184 12.0528 9.88867L14.3516 7.64844L11.1729 7.18457C10.8476 7.13702 10.5665 6.93249 10.4209 6.6377L9.00002 3.75781L7.57912 6.6377Z" />
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
			<symbol id="icon-plus" viewBox="0 0 18 18">
				<path d="M8 14.25V3.75C8 3.19772 8.44772 2.75 9 2.75C9.55228 2.75 10 3.19772 10 3.75V14.25C10 14.8023 9.55228 15.25 9 15.25C8.44772 15.25 8 14.8023 8 14.25Z" />
				<path d="M14.25 8C14.8023 8 15.25 8.44772 15.25 9C15.25 9.55228 14.8023 10 14.25 10H3.75C3.19772 10 2.75 9.55228 2.75 9C2.75 8.44772 3.19772 8 3.75 8H14.25Z" />
			</symbol>
			<symbol id="icon-minus" viewBox="0 0 18 18">
				<path d="M14.25 8C14.8023 8 15.25 8.44772 15.25 9C15.25 9.55228 14.8023 10 14.25 10H3.75C3.19772 10 2.75 9.55228 2.75 9C2.75 8.44772 3.19772 8 3.75 8H14.25Z" />
			</symbol>
		</svg>
<?php
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

		foreach ($pricing as $key => $row) {
			if (empty($row['type'])) {
				continue;
			}

			if ($row['type'] === 'Adult - UDW') {
				$pricing[$key]['type'] = __('per person', 'trvlr');
			} elseif ($row['type'] === 'Child 5-16 UDW') {
				$pricing[$key]['type'] = __('per child', 'trvlr');
			}
		}

		return $pricing;
	}
}
