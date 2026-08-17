<?php

if (!defined('ABSPATH')) {
	exit;
}

class Trvlr_Template_Registry
{

	private static $bootstrapped = false;

	private static $card_templates = array();

	private static $single_templates = array();

	private static $presentation_themes = array();

	private static $presentation_state_initialized = false;

	/** @var string|null Request-scoped presentation theme slug override (null = none). */
	private static $request_presentation_theme_override = null;

	public static function bootstrap()
	{
		if (self::$bootstrapped) {
			return;
		}
		self::register_builtin_templates();
		do_action('trvlr_register_templates');
		self::register_builtin_presentation_themes();
		do_action('trvlr_register_presentation_themes');
		self::$bootstrapped = true;
	}

	private static function register_builtin_templates()
	{
		$dir = TRVLR_PLUGIN_DIR;
		self::register_card(
			'card-1',
			array(
				'label' => __('Card 1', 'trvlr'),
				'file' => $dir . 'public/templates/cards/card-template-1.php',
			)
		);
		self::register_card(
			'card-2',
			array(
				'label' => __('Card 2', 'trvlr'),
				'file' => $dir . 'public/templates/cards/card-template-2.php',
			)
		);
		self::register_card(
			'card-3',
			array(
				'label' => __('Card 3', 'trvlr'),
				'file' => $dir . 'public/templates/cards/card-template-3.php',
			)
		);
		self::register_card(
			'card-4',
			array(
				'label' => __('Card 4', 'trvlr'),
				'file' => $dir . 'public/templates/cards/card-template-4.php',
			)
		);
		self::register_single(
			'page-1',
			array(
				'label' => __('Page 1', 'trvlr'),
				'file' => $dir . 'public/templates/single-attraction/single-template-1.php',
			)
		);
		self::register_single(
			'page-2',
			array(
				'label' => __('Page 2', 'trvlr'),
				'file' => $dir . 'public/templates/single-attraction/single-template-2.php',
			)
		);
		self::register_single(
			'page-3',
			array(
				'label' => __('Page 3', 'trvlr'),
				'file' => $dir . 'public/templates/single-attraction/single-template-3.php',
			)
		);
		self::register_single(
			'page-4',
			array(
				'label' => __('Page 4', 'trvlr'),
				'file' => $dir . 'public/templates/single-attraction/single-template-4.php',
			)
		);
	}

	private static function register_builtin_presentation_themes()
	{
		self::register_presentation_theme(
			'theme-1',
			array(
				'label' => __('Theme 1', 'trvlr'),
				'card' => 'card-1',
				'single' => 'page-1',
				'stylesheet' => 'themes/variant-1.css',
			)
		);
		self::register_presentation_theme(
			'theme-2',
			array(
				'label' => __('Theme 2', 'trvlr'),
				'card' => 'card-2',
				'single' => 'page-2',
				'stylesheet' => 'themes/variant-2.css',
			)
		);
		self::register_presentation_theme(
			'theme-3',
			array(
				'label' => __('Theme 3', 'trvlr'),
				'card' => 'card-3',
				'single' => 'page-3',
				'stylesheet' => 'themes/variant-3.css',
			)
		);
		self::register_presentation_theme(
			'theme-4',
			array(
				'label' => __('Theme 4', 'trvlr'),
				'card' => 'card-4',
				'single' => 'page-4',
				'stylesheet' => 'themes/variant-4.css',
			)
		);
	}

	/**
	 * Allow nested asset paths like themes/variant-4.css while blocking traversal.
	 *
	 * @param string $path
	 * @return string
	 */
	private static function sanitize_asset_rel_path($path)
	{
		$path = str_replace('\\', '/', (string) $path);
		$path = ltrim($path, '/');
		if ($path === '' || str_contains($path, '..')) {
			return '';
		}

		return $path;
	}

	public static function register_presentation_theme($slug, $args)
	{
		$slug = sanitize_key($slug);
		if ($slug === '') {
			return;
		}
		$card = isset($args['card']) ? sanitize_key((string) $args['card']) : '';
		$single = isset($args['single']) ? sanitize_key((string) $args['single']) : '';
		if ($card === '' || $single === '' || !isset(self::$card_templates[$card]) || !isset(self::$single_templates[$single])) {
			return;
		}
		$stylesheet = '';
		if (isset($args['stylesheet']) && is_string($args['stylesheet']) && $args['stylesheet'] !== '') {
			$stylesheet = self::sanitize_asset_rel_path($args['stylesheet']);
		} elseif (preg_match('/^theme-(.+)$/u', $slug, $m)) {
			$sfx = sanitize_key($m[1]);
			if ($sfx !== '') {
				$stylesheet = 'themes/variant-' . $sfx . '.css';
			}
		}
		$script = '';
		if (isset($args['script']) && is_string($args['script']) && $args['script'] !== '') {
			$script = self::sanitize_asset_rel_path($args['script']);
		}
		self::$presentation_themes[$slug] = array(
			'slug' => $slug,
			'label' => isset($args['label']) ? $args['label'] : $slug,
			'card' => $card,
			'single' => $single,
			'stylesheet' => $stylesheet,
			'script' => $script,
		);
	}

	public static function register_card($slug, $args)
	{
		$slug = sanitize_key($slug);
		if ($slug === '') {
			return;
		}
		$file = isset($args['file']) ? $args['file'] : '';
		if (!is_string($file) || $file === '') {
			return;
		}
		self::$card_templates[$slug] = array(
			'slug' => $slug,
			'label' => isset($args['label']) ? $args['label'] : $slug,
			'file' => $file,
		);
	}

	public static function register_single($slug, $args)
	{
		$slug = sanitize_key($slug);
		if ($slug === '') {
			return;
		}
		$file = isset($args['file']) ? $args['file'] : '';
		if (!is_string($file) || $file === '') {
			return;
		}
		self::$single_templates[$slug] = array(
			'slug' => $slug,
			'label' => isset($args['label']) ? $args['label'] : $slug,
			'file' => $file,
		);
	}

	public static function get_card_templates()
	{
		return self::$card_templates;
	}

	public static function get_single_templates()
	{
		return self::$single_templates;
	}

	public static function get_presentation_themes()
	{
		return self::$presentation_themes;
	}

	public static function get_template_choices_for_admin()
	{
		return array(
			'cards' => array_values(
				array_map(
					function ($t) {
						return array(
							'slug' => $t['slug'],
							'label' => $t['label'],
						);
					},
					self::$card_templates
				)
			),
			'singles' => array_values(
				array_map(
					function ($t) {
						return array(
							'slug' => $t['slug'],
							'label' => $t['label'],
						);
					},
					self::$single_templates
				)
			),
			'presentationThemes' => array_values(
				array_map(
					function ($t) {
						$stylesheet = isset($t['stylesheet']) ? (string) $t['stylesheet'] : '';
						$stylesheet_url = '';
						if ($stylesheet !== '') {
							$path = TRVLR_PLUGIN_DIR . 'public/dist/css/' . $stylesheet;
							if (is_readable($path)) {
								$stylesheet_url = TRVLR_PLUGIN_URL . 'public/dist/css/' . $stylesheet;
								$mtime = filemtime($path);
								if ($mtime) {
									$stylesheet_url = add_query_arg('ver', (string) $mtime, $stylesheet_url);
								}
							}
						}

						return array(
							'slug' => $t['slug'],
							'label' => $t['label'],
							'stylesheetUrl' => $stylesheet_url,
						);
					},
					self::$presentation_themes
				)
			),
		);
	}

	public static function get_active_presentation_theme_stylesheet_basename()
	{
		$pt = self::get_active_presentation_theme_slug();
		if ($pt === '' || !isset(self::$presentation_themes[$pt])) {
			return '';
		}
		$sheet = isset(self::$presentation_themes[$pt]['stylesheet']) ? (string) self::$presentation_themes[$pt]['stylesheet'] : '';

		return $sheet;
	}

	public static function get_active_presentation_theme_script_basename()
	{
		$pt = self::get_active_presentation_theme_slug();
		if ($pt === '' || !isset(self::$presentation_themes[$pt])) {
			return '';
		}
		$script = isset(self::$presentation_themes[$pt]['script']) ? (string) self::$presentation_themes[$pt]['script'] : '';

		return $script;
	}

	public static function get_default_card_slug()
	{
		if (empty(self::$card_templates)) {
			return '';
		}

		return array_key_first(self::$card_templates);
	}

	public static function get_default_single_slug()
	{
		if (empty(self::$single_templates)) {
			return '';
		}

		return array_key_first(self::$single_templates);
	}

	public static function get_default_presentation_theme_slug()
	{
		if (empty(self::$presentation_themes)) {
			return '';
		}

		return array_key_first(self::$presentation_themes);
	}

	/**
	 * Ordered presentation theme slugs (registration order).
	 *
	 * @return string[]
	 */
	public static function get_presentation_theme_slugs()
	{
		return array_keys(self::$presentation_themes);
	}

	/**
	 * Whether ?trvlr_theme_variant=none (base styles only — no presentation theme).
	 *
	 * @param string|null $raw Raw query value; reads $_GET when null.
	 */
	public static function is_presentation_theme_variant_none($raw = null)
	{
		if ($raw === null) {
			if (!isset($_GET['trvlr_theme_variant']) || $_GET['trvlr_theme_variant'] === '') {
				return false;
			}
			$raw = wp_unslash($_GET['trvlr_theme_variant']);
		}

		return is_string($raw) && strtolower(trim($raw)) === 'none';
	}

	/**
	 * Resolve ?trvlr_theme_variant= to a registered theme slug.
	 * Accepts a theme slug, or a 1-based index (wraps). Empty when unset/invalid/none.
	 *
	 * @param string|null $raw Raw query value; reads $_GET when null.
	 */
	public static function resolve_presentation_theme_variant_param($raw = null)
	{
		if ($raw === null) {
			if (!isset($_GET['trvlr_theme_variant']) || $_GET['trvlr_theme_variant'] === '') {
				return '';
			}
			$raw = wp_unslash($_GET['trvlr_theme_variant']);
		}

		if (!is_string($raw) && !is_numeric($raw)) {
			return '';
		}

		$raw = trim((string) $raw);
		if ($raw === '' || self::is_presentation_theme_variant_none($raw) || empty(self::$presentation_themes)) {
			return '';
		}

		$slug = sanitize_key($raw);
		if ($slug !== '' && isset(self::$presentation_themes[$slug])) {
			return $slug;
		}

		if (!is_numeric($raw)) {
			return '';
		}

		$slugs = array_keys(self::$presentation_themes);
		$count = count($slugs);
		$index = ((int) $raw - 1) % $count;
		if ($index < 0) {
			$index += $count;
		}

		return $slugs[$index];
	}

	/**
	 * 1-based index of the active presentation theme, or 0 if none.
	 */
	public static function get_active_presentation_theme_index()
	{
		$slug = self::get_active_presentation_theme_slug();
		if ($slug === '') {
			return 0;
		}
		$slugs = array_keys(self::$presentation_themes);
		$pos = array_search($slug, $slugs, true);

		return $pos === false ? 0 : $pos + 1;
	}

	/**
	 * Adjacent theme slug wrapping around the registered list.
	 *
	 * @param int $delta +1 next, -1 previous
	 */
	public static function get_adjacent_presentation_theme_slug($delta = 1)
	{
		$slugs = array_keys(self::$presentation_themes);
		$count = count($slugs);
		if ($count === 0) {
			return '';
		}

		$current = self::get_active_presentation_theme_slug();
		$pos = array_search($current, $slugs, true);
		if ($pos === false) {
			$pos = 0;
		}

		$next = ($pos + (int) $delta) % $count;
		if ($next < 0) {
			$next += $count;
		}

		return $slugs[$next];
	}

	/**
	 * Temporarily force the active presentation theme for the current request
	 * (e.g. admin theme preview). Pass empty string / null to clear.
	 *
	 * @param string|null $slug
	 * @return void
	 */
	public static function set_request_presentation_theme_override($slug)
	{
		if ($slug === null || $slug === '') {
			self::$request_presentation_theme_override = null;

			return;
		}

		$slug = sanitize_key((string) $slug);
		self::$request_presentation_theme_override = isset(self::$presentation_themes[$slug]) ? $slug : null;
	}

	public static function get_active_presentation_theme_slug()
	{
		self::ensure_presentation_theme_state();

		if (self::is_presentation_theme_variant_none()) {
			return '';
		}

		if (self::$request_presentation_theme_override !== null) {
			return self::$request_presentation_theme_override;
		}

		$override = self::resolve_presentation_theme_variant_param();
		if ($override !== '') {
			return $override;
		}

		$opt = get_option('trvlr_presentation_theme', '');
		$slug = is_string($opt) ? sanitize_key($opt) : '';
		if ($slug !== '' && isset(self::$presentation_themes[$slug])) {
			return $slug;
		}

		return self::get_default_presentation_theme_slug();
	}

	public static function set_active_presentation_theme($slug)
	{
		$slug = sanitize_key((string) $slug);
		if ($slug === '' || !isset(self::$presentation_themes[$slug])) {
			return false;
		}
		update_option('trvlr_presentation_theme', $slug);
		self::sync_legacy_options_from_presentation_theme($slug);

		return true;
	}

	/**
	 * @return void
	 */
	private static function ensure_presentation_theme_state()
	{
		if (self::$presentation_state_initialized) {
			return;
		}
		self::$presentation_state_initialized = true;
		$opt = get_option('trvlr_presentation_theme', '');
		$slug = is_string($opt) ? sanitize_key($opt) : '';
		if ($slug !== '' && isset(self::$presentation_themes[$slug])) {
			self::sync_legacy_options_from_presentation_theme($slug);

			return;
		}

		$card = sanitize_key((string) get_option('trvlr_card_template', ''));
		$page = sanitize_key((string) get_option('trvlr_single_attraction_template', ''));
		$resolved = '';
		foreach (self::$presentation_themes as $s => $def) {
			if ($def['card'] === $card && $def['single'] === $page) {
				$resolved = $s;
				break;
			}
		}
		if ($resolved === '') {
			$resolved = self::get_default_presentation_theme_slug();
		}
		if ($resolved !== '') {
			update_option('trvlr_presentation_theme', $resolved);
		}
		self::sync_legacy_options_from_presentation_theme($resolved);
	}

	/**
	 * @param string $slug Presentation theme slug.
	 * @return void
	 */
	private static function sync_legacy_options_from_presentation_theme($slug)
	{
		if (!isset(self::$presentation_themes[$slug])) {
			return;
		}
		$def = self::$presentation_themes[$slug];
		$cur_card = (string) get_option('trvlr_card_template', '');
		$cur_page = (string) get_option('trvlr_single_attraction_template', '');
		if ($cur_card !== $def['card']) {
			update_option('trvlr_card_template', $def['card']);
		}
		if ($cur_page !== $def['single']) {
			update_option('trvlr_single_attraction_template', $def['single']);
		}
	}

	public static function get_active_card_slug()
	{
		$pt = self::get_active_presentation_theme_slug();
		if ($pt !== '' && isset(self::$presentation_themes[$pt])) {
			$c = self::$presentation_themes[$pt]['card'];
			if (isset(self::$card_templates[$c])) {
				return $c;
			}
		}

		return self::get_default_card_slug();
	}

	public static function get_active_single_slug()
	{
		$pt = self::get_active_presentation_theme_slug();
		if ($pt !== '' && isset(self::$presentation_themes[$pt])) {
			$s = self::$presentation_themes[$pt]['single'];
			if (isset(self::$single_templates[$s])) {
				return $s;
			}
		}

		return self::get_default_single_slug();
	}

	public static function get_card_template_path($slug = null)
	{
		if ($slug === null) {
			$slug = self::get_active_card_slug();
		} else {
			$slug = sanitize_key($slug);
		}
		if ($slug === '' || !isset(self::$card_templates[$slug])) {
			$slug = self::get_default_card_slug();
		}
		if ($slug === '' || !isset(self::$card_templates[$slug])) {
			return self::get_first_readable_template_file(self::$card_templates);
		}
		$path = self::$card_templates[$slug]['file'];
		if (!is_readable($path)) {
			$fallback = self::get_first_readable_template_file(self::$card_templates);
			if ($fallback !== '') {
				return $fallback;
			}
		}
		return $path;
	}

	public static function get_single_template_path($slug = null)
	{
		if ($slug === null) {
			$slug = self::get_active_single_slug();
		} else {
			$slug = sanitize_key($slug);
		}
		if ($slug === '' || !isset(self::$single_templates[$slug])) {
			$slug = self::get_default_single_slug();
		}
		if ($slug === '' || !isset(self::$single_templates[$slug])) {
			return self::get_first_readable_template_file(self::$single_templates);
		}
		$path = self::$single_templates[$slug]['file'];
		if (!is_readable($path)) {
			$fallback = self::get_first_readable_template_file(self::$single_templates);
			if ($fallback !== '') {
				return $fallback;
			}
		}
		return $path;
	}

	private static function get_first_readable_template_file(array $templates)
	{
		foreach ($templates as $tpl) {
			if (!empty($tpl['file']) && is_readable($tpl['file'])) {
				return $tpl['file'];
			}
		}

		return '';
	}
}
