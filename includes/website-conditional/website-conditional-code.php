<?php

if (!defined('ABSPATH')) {
	exit;
}

add_action('init', 'trvlr_run_website_conditional_code', 5);

function trvlr_run_website_conditional_code()
{
	$org_id = function_exists('get_trvlr_organisation_id')
		? get_trvlr_organisation_id()
		: get_option('trvlr_organisation_id', '');
	$org_id = strtolower(preg_replace('/[^a-z0-9_-]/i', '', (string) $org_id));

	if ($org_id === '') {
		return;
	}

	$file = __DIR__ . '/trvlr-id-' . $org_id . '.php';
	if (is_readable($file)) {
		require_once $file;
	}
}

function trvlr_website_conditional_active_theme_is($slug)
{
	return class_exists('Trvlr_Template_Registry')
		&& Trvlr_Template_Registry::get_active_presentation_theme_slug() === $slug;
}

function trvlr_website_conditional_enqueue_css($css)
{
	$handle = wp_style_is('trvlr-presentation-theme', 'enqueued')
		? 'trvlr-presentation-theme'
		: 'trvlr-public';

	if (wp_style_is($handle, 'enqueued')) {
		wp_add_inline_style($handle, $css);
		return;
	}

	wp_register_style('trvlr-website-conditional', false, array(), defined('TRVLR_VERSION') ? TRVLR_VERSION : '1.0.0');
	wp_enqueue_style('trvlr-website-conditional');
	wp_add_inline_style('trvlr-website-conditional', $css);
}
