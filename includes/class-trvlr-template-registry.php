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
				'stylesheet' => 'themes-variant-1.css',
			)
		);
		self::register_presentation_theme(
			'theme-2',
			array(
				'label' => __('Theme 2', 'trvlr'),
				'card' => 'card-2',
				'single' => 'page-2',
				'stylesheet' => 'themes-variant-2.css',
			)
		);
		self::register_presentation_theme(
			'theme-3',
			array(
				'label' => __('Theme 3', 'trvlr'),
				'card' => 'card-3',
				'single' => 'page-3',
				'stylesheet' => 'themes-variant-3.css',
				'script' => 'variant-3.js',
			)
		);
		self::register_presentation_theme(
			'theme-4',
			array(
				'label' => __('Theme 4', 'trvlr'),
				'card' => 'card-4',
				'single' => 'page-4',
				'stylesheet' => 'themes-variant-4.css',
				'script' => 'variant-4.js',
			)
		);
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
			$stylesheet = basename($args['stylesheet']);
		} elseif (preg_match('/^theme-(.+)$/u', $slug, $m)) {
			$sfx = sanitize_key($m[1]);
			if ($sfx !== '') {
				$stylesheet = 'themes-variant-' . $sfx . '.css';
			}
		}
		$script = '';
		if (isset($args['script']) && is_string($args['script']) && $args['script'] !== '') {
			$script = basename($args['script']);
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
						return array(
							'slug' => $t['slug'],
							'label' => $t['label'],
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

	public static function get_active_presentation_theme_slug()
	{
		self::ensure_presentation_theme_state();
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
