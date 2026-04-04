<?php

if (! defined('ABSPATH')) {
	exit;
}

function trvlr_is_attraction_post_type_disabled(): bool
{
	return (bool) get_option('trvlr_disable_attraction_post_type', false);
}

function trvlr_is_attraction_sync_disabled(): bool
{
	if (trvlr_is_attraction_post_type_disabled()) {
		return true;
	}
	return (bool) get_option('trvlr_disable_attraction_sync', false);
}

function trvlr_is_frontend_booking_disabled(): bool
{
	return (bool) get_option('trvlr_disable_frontend_booking', false);
}

function trvlr_get_connection_settings_array(): array
{
	$disable_pt = trvlr_is_attraction_post_type_disabled();
	$disable_sync_stored = (bool) get_option('trvlr_disable_attraction_sync', false);

	return array(
		'organisation_id' => get_option('trvlr_organisation_id', ''),
		'api_key' => get_option('trvlr_api_key', ''),
		'disable_attraction_post_type' => $disable_pt,
		'disable_attraction_sync' => $disable_pt ? true : $disable_sync_stored,
		'disable_frontend_booking' => trvlr_is_frontend_booking_disabled(),
	);
}

function trvlr_update_connection_settings_from_request(array $data): void
{
	if (array_key_exists('organisation_id', $data)) {
		update_option('trvlr_organisation_id', sanitize_text_field((string) $data['organisation_id']));
	}
	if (array_key_exists('api_key', $data)) {
		update_option('trvlr_api_key', sanitize_text_field((string) $data['api_key']));
	}
	if (array_key_exists('disable_attraction_post_type', $data)) {
		update_option('trvlr_disable_attraction_post_type', (bool) $data['disable_attraction_post_type']);
	}
	if (array_key_exists('disable_attraction_sync', $data)) {
		update_option('trvlr_disable_attraction_sync', (bool) $data['disable_attraction_sync']);
	}
	if (array_key_exists('disable_frontend_booking', $data)) {
		update_option('trvlr_disable_frontend_booking', (bool) $data['disable_frontend_booking']);
	}

	if (trvlr_is_attraction_post_type_disabled()) {
		update_option('trvlr_disable_attraction_sync', true);
	}
}
