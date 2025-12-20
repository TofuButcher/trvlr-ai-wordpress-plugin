<?php

/**
 * Helper functions for accessing attraction custom field data
 * 
 * All functions follow WordPress conventions:
 * - Accept optional $post_id parameter, defaults to global $post
 * - Return empty string for non-existent fields (allows !function() conditionals)
 * - Apply filters for customization: trvlr_{field_name}
 * 
 * @package Trvlr
 */

if (! defined('ABSPATH')) exit;

/**
 * Get the Organisation ID
 * 
 * @return string Organisation ID
 */
function get_trvlr_organisation_id()
{
	return get_option('trvlr_organisation_id', '');
}

/**
 * Get the Base Domain for TRVLR API/Iframes
 * 
 * @param string|null $org_id Optional Organisation ID
 * @return string Base domain URL
 */
function get_trvlr_base_domain($org_id = null)
{
	if (empty($org_id)) {
		$org_id = get_trvlr_organisation_id();
	}

	if (empty($org_id)) {
		return '';
	}

	// Check if org_id already contains http
	if (strpos($org_id, 'http') !== false) {
		return $org_id;
	}

	// Build domain
	return 'https://' . $org_id . '.trvlr.ai';
}

function get_trvlr_id($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_id', true);
	return apply_filters('trvlr_id', $value ?: '', $post_id);
}

function get_trvlr_attraction_id($post_id = null)
{
	return get_trvlr_id($post_id);
}

function get_trvlr_title($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	return get_the_title($post_id);
}

function get_trvlr_description($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_description', true);
	return apply_filters('trvlr_description', $value ?: '', $post_id);
}

function get_trvlr_short_description($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_short_description', true);
	return apply_filters('trvlr_short_description', $value ?: '', $post_id);
}

function get_trvlr_duration($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_duration', true);
	return apply_filters('trvlr_duration', $value ?: '', $post_id);
}

function get_trvlr_start_time($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_start_time', true);
	return apply_filters('trvlr_start_time', $value ?: '', $post_id);
}

function get_trvlr_end_time($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_end_time', true);
	return apply_filters('trvlr_end_time', $value ?: '', $post_id);
}

function get_trvlr_is_on_sale($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_is_on_sale', true);
	$is_on_sale = $value === '1' || $value === 1;
	return apply_filters('trvlr_is_on_sale', $is_on_sale, $post_id);
}

function get_trvlr_sale_description($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_sale_description', true);
	return apply_filters('trvlr_sale_description', $value ?: '', $post_id);
}

function get_trvlr_attraction_description($post_id = null)
{
	return get_trvlr_description($post_id);
}

function get_trvlr_attraction_short_description($post_id = null)
{
	return get_trvlr_short_description($post_id);
}

function get_trvlr_attraction_duration($post_id = null)
{
	return get_trvlr_duration($post_id);
}

function get_trvlr_attraction_start_time($post_id = null)
{
	return get_trvlr_start_time($post_id);
}

function get_trvlr_attraction_end_time($post_id = null)
{
	return get_trvlr_end_time($post_id);
}

function get_trvlr_attraction_is_on_sale($post_id = null)
{
	return get_trvlr_is_on_sale($post_id);
}

function get_trvlr_advertised_price_value($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_advertised_price_value', true);
	if (empty($value)) {
		$pricing = get_trvlr_pricing($post_id);
		if (empty($pricing)) {
			return apply_filters('trvlr_advertised_price_value', '', $post_id);
		}

		$adult_price = array_values(array_filter($pricing, function ($price) {
			return stripos($price['type'], 'adult') !== false ||
				stripos($price['type'], 'per person') !== false;
		}));

		if (!empty($adult_price) && isset($adult_price[0]['price'])) {
			$value = $adult_price[0]['price'];
		} elseif (!empty($pricing[0]['price'])) {
			$value = $pricing[0]['price'];
		}
	}
	return apply_filters('trvlr_advertised_price_value', $value ?: '', $post_id);
}

function get_trvlr_advertised_price_type($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_advertised_price_type', true);
	if (empty($value)) {
		$pricing = get_trvlr_pricing($post_id);
		if (empty($pricing)) {
			return apply_filters('trvlr_advertised_price_type', '', $post_id);
		}

		$adult_type = array_values(array_filter($pricing, function ($price) {
			return stripos($price['type'], 'adult') !== false ||
				stripos($price['type'], 'per person') !== false;
		}));

		if (!empty($adult_type) && isset($adult_type[0]['type'])) {
			$value = $adult_type[0]['type'];
		} elseif (!empty($pricing[0]['type'])) {
			$value = $pricing[0]['type'];
		}
	}
	return apply_filters('trvlr_advertised_price_type', $value ?: '', $post_id);
}

function get_trvlr_advertised_price($post_id = null)
{
	$value = get_trvlr_advertised_price_value($post_id);
	$type = get_trvlr_advertised_price_type($post_id);
	return $value ? "\${$value} {$type}" : '';
}

function get_trvlr_inclusions($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_inclusions', true);
	return apply_filters('trvlr_inclusions', $value ?: '', $post_id);
}

function get_trvlr_additional_info($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_additional_info', true);
	return apply_filters('trvlr_additional_info', $value ?: '', $post_id);
}

function get_trvlr_pricing($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_pricing', true);
	$pricing = is_array($value) ? $value : array();
	return apply_filters('trvlr_pricing', $pricing, $post_id);
}

function get_trvlr_locations($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_locations', true);
	$locations = is_array($value) ? $value : array();
	return apply_filters('trvlr_locations', $locations, $post_id);
}

function get_trvlr_media($post_id = null, $include_featured = true)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_media', true);
	$media = is_array($value) ? $value : array();

	if ($include_featured) {
		$featured_id = get_post_thumbnail_id($post_id);
		if ($featured_id) {
			array_unshift($media, $featured_id);
			$media = array_unique($media);
		}
	}

	return apply_filters('trvlr_media', $media, $post_id);
}

function get_trvlr_attraction_tags($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$terms = get_the_terms($post_id, 'trvlr_attraction_tag');

	if (is_wp_error($terms) || empty($terms)) {
		return apply_filters('trvlr_attraction_tags', array(), $post_id);
	}

	return apply_filters('trvlr_attraction_tags', $terms, $post_id);
}

function get_trvlr_attraction_advertised_price_value($post_id = null)
{
	return get_trvlr_advertised_price_value($post_id);
}

function get_trvlr_attraction_advertised_price_type($post_id = null)
{
	return get_trvlr_advertised_price_type($post_id);
}

function get_trvlr_attraction_sale_description($post_id = null)
{
	return get_trvlr_sale_description($post_id);
}

function get_trvlr_attraction_inclusions($post_id = null)
{
	return get_trvlr_inclusions($post_id);
}

function get_trvlr_attraction_additional_info($post_id = null)
{
	return get_trvlr_additional_info($post_id);
}

function get_trvlr_attraction_pricing($post_id = null)
{
	return get_trvlr_pricing($post_id);
}

function get_trvlr_attraction_locations($post_id = null)
{
	return get_trvlr_locations($post_id);
}

function get_trvlr_attraction_media($post_id = null)
{
	return get_trvlr_media($post_id, true);
}

function get_trvlr_attraction_gallery_ids($post_id = null)
{
	return get_trvlr_media($post_id, false);
}

function get_trvlr_attraction_raw_data($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_raw_data', true);

	if (is_string($value) && ! empty($value)) {
		$decoded = json_decode($value, true);
		$value = $decoded ?: array();
	} elseif (! is_array($value)) {
		$value = array();
	}

	return apply_filters('trvlr_raw_data', $value, $post_id);
}

function get_trvlr_attraction_product_type($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_product_type', true);
	return apply_filters('trvlr_product_type', $value ?: '', $post_id);
}

function is_trvlr_attraction($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	return get_post_type($post_id) === 'trvlr_attraction';
}

function get_trvlr_attraction_primary_location($post_id = null)
{
	$locations = get_trvlr_locations($post_id);
	return ! empty($locations) ? $locations[0] : null;
}

function get_trvlr_attraction_lowest_price($post_id = null)
{
	$pricing = get_trvlr_pricing($post_id);

	if (empty($pricing)) {
		return null;
	}

	$prices = array();
	foreach ($pricing as $price_option) {
		if (! empty($price_option['price'])) {
			$prices[] = floatval($price_option['price']);
		}
	}

	return ! empty($prices) ? min($prices) : null;
}

function get_trvlr_attraction_formatted_price($post_id = null)
{
	$pricing = get_trvlr_pricing($post_id);

	if (empty($pricing)) {
		return '';
	}

	$first_price = $pricing[0];
	$price = isset($first_price['price']) ? floatval($first_price['price']) : 0;

	return apply_filters('trvlr_formatted_price', '$' . number_format($price, 2), $post_id, $price);
}

function get_trvlr_attraction_all_data($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();

	$data = array(
		'id' => $post_id,
		'title' => get_trvlr_title($post_id),
		'slug' => get_post_field('post_name', $post_id),
		'permalink' => get_permalink($post_id),
		'featured_image' => get_post_thumbnail_id($post_id),
		'trvlr_id' => get_trvlr_id($post_id),
		'description' => get_trvlr_description($post_id),
		'short_description' => get_trvlr_short_description($post_id),
		'duration' => get_trvlr_duration($post_id),
		'start_time' => get_trvlr_start_time($post_id),
		'end_time' => get_trvlr_end_time($post_id),
		'is_on_sale' => get_trvlr_is_on_sale($post_id),
		'sale_description' => get_trvlr_sale_description($post_id),
		'inclusions' => get_trvlr_inclusions($post_id),
		'additional_info' => get_trvlr_additional_info($post_id),
		'pricing' => get_trvlr_pricing($post_id),
		'locations' => get_trvlr_locations($post_id),
		'media' => get_trvlr_media($post_id),
		'product_type' => get_trvlr_attraction_product_type($post_id),
	);

	return apply_filters('trvlr_all_data', $data, $post_id);
}
