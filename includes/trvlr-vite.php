<?php

/**
 * Vite HMR helpers for frontend + admin asset loading.
 *
 * @package Trvlr
 */

/**
 * @return string|null Vite origin (e.g. https://localhost:5175) when `hot` exists.
 */
function trvlr_hot_server()
{
	$hot = TRVLR_PLUGIN_DIR . 'hot';

	if (!is_file($hot)) {
		return null;
	}

	return rtrim(trim((string) file_get_contents($hot)), '/');
}

/**
 * @return bool
 */
function trvlr_is_vite_hot()
{
	return trvlr_hot_server() !== null;
}

/**
 * @param string $relative Path from plugin root using forward slashes.
 * @return string
 */
function trvlr_vite_url($relative)
{
	$hot = trvlr_hot_server();
	if ($hot === null) {
		return '';
	}

	return $hot . '/' . ltrim(str_replace('\\', '/', $relative), '/');
}

/**
 * Ensure Vite client is enqueued once per request.
 *
 * @return void
 */
function trvlr_enqueue_vite_client()
{
	static $done = false;
	if ($done || !trvlr_is_vite_hot()) {
		return;
	}
	$done = true;

	wp_enqueue_script_module('trvlr-vite-client', trvlr_vite_url('@vite/client'), array(), null);
}

/**
 * Classic boot handle for wp_localize / inline globals used by Vite modules.
 *
 * @return string
 */
function trvlr_vite_boot_handle()
{
	static $handle = null;
	if ($handle !== null) {
		return $handle;
	}

	$handle = 'trvlr-vite-boot';
	wp_register_script($handle, false, array('jquery'), null, true);
	wp_enqueue_script($handle);

	return $handle;
}

/**
 * @param string $abs_dir Absolute directory to scan.
 * @param string $plugin_rel_prefix Plugin-relative prefix (e.g. public/src/styles).
 * @param array  $denylist Basenames to skip.
 * @return array<string,string> handle-suffix => plugin-relative path
 */
function trvlr_vite_discover_scss($abs_dir, $plugin_rel_prefix, $denylist = array())
{
	$found = array();
	if (!is_dir($abs_dir)) {
		return $found;
	}

	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($abs_dir, FilesystemIterator::SKIP_DOTS)
	);

	$deny = array_fill_keys($denylist, true);
	$prefix = rtrim(str_replace('\\', '/', $plugin_rel_prefix), '/');

	foreach ($iterator as $file) {
		if (!$file->isFile()) {
			continue;
		}
		$name = $file->getFilename();
		if (!str_ends_with(strtolower($name), '.scss') || str_starts_with($name, '_')) {
			continue;
		}
		if (isset($deny[$name])) {
			continue;
		}

		$abs = $file->getPathname();
		$rel_from_root = ltrim(str_replace('\\', '/', substr($abs, strlen(TRVLR_PLUGIN_DIR))), '/');
		$rel_from_prefix = ltrim(substr($rel_from_root, strlen($prefix)), '/');
		$slug = preg_replace('/\.scss$/i', '', $rel_from_prefix);
		$slug = str_replace('/', '-', $slug);
		$found[$slug] = $rel_from_root;
	}

	return $found;
}

/**
 * Enqueue Vite client + public SCSS (active theme only) for frontend HMR.
 *
 * @return void
 */
function trvlr_enqueue_vite_hmr()
{
	if (!trvlr_is_vite_hot()) {
		return;
	}

	trvlr_enqueue_vite_client();

	$sheets = trvlr_vite_discover_scss(
		TRVLR_PLUGIN_DIR . 'public/src/styles',
		'public/src/styles',
		array('trvlr-theme-colors.scss')
	);

	$active_theme_suffix = null;
	if (class_exists('Trvlr_Template_Registry')) {
		$slug = Trvlr_Template_Registry::get_active_presentation_theme_slug();
		if ($slug !== '' && preg_match('/^theme-(\d+)$/', $slug, $m)) {
			$active_theme_suffix = 'themes-variant-' . $m[1];
		}
	}

	foreach ($sheets as $handle_suffix => $rel) {
		if (str_starts_with($handle_suffix, 'themes-')) {
			if ($active_theme_suffix === null || $handle_suffix !== $active_theme_suffix) {
				continue;
			}
		}

		wp_enqueue_script_module(
			'trvlr-hmr-' . $handle_suffix,
			trvlr_vite_url($rel),
			array(),
			null
		);
	}
}

/**
 * Enqueue a front script from Vite (hot) or the classic WP handle (prod).
 *
 * @param string $handle Script handle registered for both modes.
 * @return void
 */
function trvlr_enqueue_front_script($handle)
{
	if (trvlr_is_vite_hot()) {
		wp_enqueue_script_module($handle);
		return;
	}

	wp_enqueue_script($handle);
}

/**
 * Admin settings: React + admin SCSS via Vite HMR.
 *
 * @param array $initial_data trvlrInitialData payload.
 * @return bool True when hot path handled enqueue.
 */
function trvlr_enqueue_admin_vite_hmr($initial_data)
{
	if (!trvlr_is_vite_hot()) {
		return false;
	}

	trvlr_enqueue_vite_client();

	$boot = trvlr_vite_boot_handle();
	wp_add_inline_script(
		$boot,
		'window.wpApiSettings = ' . wp_json_encode(array(
			'root' => esc_url_raw(rest_url()),
			'nonce' => wp_create_nonce('wp_rest'),
			'versionString' => 'wp/v2/',
		)) . '; window.trvlrInitialData = ' . wp_json_encode($initial_data) . ';',
		'before'
	);

	wp_enqueue_script_module(
		'trvlr-react-refresh',
		trvlr_vite_url('vite-shims/react-refresh-preamble.js'),
		array(),
		null
	);

	wp_enqueue_script_module(
		'trvlr-admin-root',
		trvlr_vite_url('admin/src/trvlr-admin-root.jsx'),
		array('trvlr-react-refresh'),
		null
	);

	wp_enqueue_style('wp-components');

	return true;
}

/**
 * Public CSS for admin theme preview under Vite HMR.
 *
 * @return void
 */
function trvlr_enqueue_admin_public_vite_styles()
{
	if (!trvlr_is_vite_hot()) {
		return;
	}

	trvlr_enqueue_vite_client();

	foreach (array('trvlr-public', 'trvlr-cards') as $sheet) {
		wp_enqueue_script_module(
			'trvlr-admin-hmr-' . $sheet,
			trvlr_vite_url('public/src/styles/' . $sheet . '.scss'),
			array(),
			null
		);
	}

	if (!class_exists('Trvlr_Template_Registry')) {
		return;
	}

	$slug = Trvlr_Template_Registry::get_active_presentation_theme_slug();
	if ($slug === '' || !preg_match('/^theme-(\d+)$/', $slug, $m)) {
		return;
	}

	wp_enqueue_script_module(
		'trvlr-admin-hmr-theme',
		trvlr_vite_url('public/src/styles/themes/variant-' . $m[1] . '.scss'),
		array(),
		null
	);
}
