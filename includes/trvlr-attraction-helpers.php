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

/**
 * Get the TRVLR attraction ID (from API)
 * 
 * @param int|null $post_id Optional post ID
 * @return string TRVLR attraction ID
 */
function get_trvlr_attraction_id($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_id', true);
	return apply_filters('trvlr_attraction_id', $value ?: '', $post_id);
}

/**
 * Get the TRVLR attraction PK
 * 
 * @param int|null $post_id Optional post ID
 * @return string TRVLR PK
 */
function get_trvlr_attraction_pk($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_pk', true);
	return apply_filters('trvlr_pk', $value ?: '', $post_id);
}

/**
 * Get the attraction description (full)
 * 
 * @param int|null $post_id Optional post ID
 * @return string Description HTML
 */
function get_trvlr_attraction_description($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_description', true);
	return apply_filters('trvlr_description', $value ?: '', $post_id);
}

/**
 * Get the attraction short description
 * 
 * @param int|null $post_id Optional post ID
 * @return string Short description HTML
 */
function get_trvlr_attraction_short_description($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_short_description', true);
	return apply_filters('trvlr_short_description', $value ?: '', $post_id);
}

/**
 * Get the attraction duration
 * 
 * @param int|null $post_id Optional post ID
 * @return string Duration text
 */
function get_trvlr_attraction_duration($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_duration', true);
	return apply_filters('trvlr_duration', $value ?: '', $post_id);
}

/**
 * Get the attraction start time
 * 
 * @param int|null $post_id Optional post ID
 * @return string Start time (HH:MM format)
 */
function get_trvlr_attraction_start_time($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_start_time', true);
	return apply_filters('trvlr_start_time', $value ?: '', $post_id);
}

/**
 * Get the attraction end time
 * 
 * @param int|null $post_id Optional post ID
 * @return string End time (HH:MM format)
 */
function get_trvlr_attraction_end_time($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_end_time', true);
	return apply_filters('trvlr_end_time', $value ?: '', $post_id);
}

/**
 * Check if attraction is on sale
 * 
 * @param int|null $post_id Optional post ID
 * @return bool True if on sale
 */
function get_trvlr_attraction_is_on_sale($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_is_on_sale', true);
	$is_on_sale = $value === '1' || $value === 1;
	return apply_filters('trvlr_is_on_sale', $is_on_sale, $post_id);
}

/**
 * Get the Advertised Price Value
 * 
 * @param int|null $post_id Optional post ID
 * @return string Advertised Price Value
 */
function get_trvlr_attraction_advertised_price_value($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_advertised_price_value', true);
	if (empty($value)) {
		$pricing = get_trvlr_attraction_pricing($post_id);
		if (empty($pricing)) {
			return apply_filters('trvlr_advertised_price_value', '', $post_id);
		}

		// Find first price containing "adult" or "per person" (case insensitive)
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

/**
 * Get the Advertised Price Type
 * 
 * @param int|null $post_id Optional post ID
 * @return string Advertised Price Type
 */
function get_trvlr_attraction_advertised_price_type($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_advertised_price_type', true);
	if (empty($value)) {
		$pricing = get_trvlr_attraction_pricing($post_id);
		if (empty($pricing)) {
			return apply_filters('trvlr_advertised_price_type', '', $post_id);
		}

		// Find first type containing "adult" or "per person" (case insensitive)
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

/**
 * Get the sale description
 * 
 * @param int|null $post_id Optional post ID
 * @return string Sale description text
 */
function get_trvlr_attraction_sale_description($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_sale_description', true);
	return apply_filters('trvlr_sale_description', $value ?: '', $post_id);
}

/**
 * Get the attraction inclusions
 * 
 * @param int|null $post_id Optional post ID
 * @return string Inclusions HTML
 */
function get_trvlr_attraction_inclusions($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_inclusions', true);
	return apply_filters('trvlr_inclusions', $value ?: '', $post_id);
}

/**
 * Get the attraction highlights
 * 
 * @param int|null $post_id Optional post ID
 * @return string Highlights HTML
 */
function get_trvlr_attraction_highlights($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_highlights', true);
	return apply_filters('trvlr_highlights', $value ?: '', $post_id);
}

/**
 * Get the attraction additional info
 * 
 * @param int|null $post_id Optional post ID
 * @return string Additional info HTML
 */
function get_trvlr_attraction_additional_info($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_additional_info', true);
	return apply_filters('trvlr_additional_info', $value ?: '', $post_id);
}

/**
 * Get the attraction pricing (repeater field)
 * 
 * @param int|null $post_id Optional post ID
 * @return array Array of pricing rows, each with 'type', 'price', 'sale_price'
 */
function get_trvlr_attraction_pricing($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_pricing', true);
	$pricing = is_array($value) ? $value : array();
	return apply_filters('trvlr_pricing', $pricing, $post_id);
}

/**
 * Get the attraction locations (repeater field)
 * 
 * @param int|null $post_id Optional post ID
 * @return array Array of location rows, each with 'type', 'address', 'lat', 'lng'
 */
function get_trvlr_attraction_locations($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_locations', true);
	$locations = is_array($value) ? $value : array();
	return apply_filters('trvlr_locations', $locations, $post_id);
}

/**
 * Get the attraction media gallery (array of attachment IDs)
 * 
 * @param int|null $post_id Optional post ID
 * @return array Array of attachment IDs
 */
function get_trvlr_attraction_media($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_media', true);
	$media = is_array($value) ? $value : array();
	return apply_filters('trvlr_media', $media, $post_id);
}

/**
 * Get the attraction gallery IDs (legacy/alternate field)
 * 
 * @param int|null $post_id Optional post ID
 * @return array Array of attachment IDs
 */
function get_trvlr_attraction_gallery_ids($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_gallery_ids', true);
	$gallery = is_array($value) ? $value : array();
	return apply_filters('trvlr_gallery_ids', $gallery, $post_id);
}

/**
 * Get the raw API data (stored JSON)
 * 
 * @param int|null $post_id Optional post ID
 * @return array Decoded API data array
 */
function get_trvlr_attraction_raw_data($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_raw_data', true);

	// If it's a JSON string, decode it
	if (is_string($value) && ! empty($value)) {
		$decoded = json_decode($value, true);
		$value = $decoded ?: array();
	} elseif (! is_array($value)) {
		$value = array();
	}

	return apply_filters('trvlr_raw_data', $value, $post_id);
}

/**
 * Get the product type
 * 
 * @param int|null $post_id Optional post ID
 * @return string Product type
 */
function get_trvlr_attraction_product_type($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_post_meta($post_id, 'trvlr_product_type', true);
	return apply_filters('trvlr_product_type', $value ?: '', $post_id);
}

// ===================================
// Convenience / Utility Functions
// ===================================

/**
 * Check if the current post is a TRVLR attraction
 * 
 * @param int|null $post_id Optional post ID
 * @return bool True if attraction post type
 */
function is_trvlr_attraction($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	return get_post_type($post_id) === 'trvlr_attraction';
}

/**
 * Get the first/primary location
 * 
 * @param int|null $post_id Optional post ID
 * @return array|null First location array or null
 */
function get_trvlr_attraction_primary_location($post_id = null)
{
	$locations = get_trvlr_attraction_locations($post_id);
	return ! empty($locations) ? $locations[0] : null;
}

/**
 * Get the lowest price from pricing options
 * 
 * @param int|null $post_id Optional post ID
 * @return float|null Lowest price or null
 */
function get_trvlr_attraction_lowest_price($post_id = null)
{
	$pricing = get_trvlr_attraction_pricing($post_id);

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

/**
 * Get formatted price with currency (from first pricing option)
 * 
 * @param int|null $post_id Optional post ID
 * @return string Formatted price string
 */
function get_trvlr_attraction_formatted_price($post_id = null)
{
	$pricing = get_trvlr_attraction_pricing($post_id);

	if (empty($pricing)) {
		return '';
	}

	$first_price = $pricing[0];
	$price = isset($first_price['price']) ? floatval($first_price['price']) : 0;

	// Simple formatting (can be enhanced with currency symbols)
	return apply_filters('trvlr_formatted_price', '$' . number_format($price, 2), $post_id, $price);
}

/**
 * Get all attraction data as an associative array
 * Useful for JSON output or data exports
 * 
 * @param int|null $post_id Optional post ID
 * @return array All attraction data
 */
function get_trvlr_attraction_all_data($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();

	$data = array(
		'id' => $post_id,
		'title' => get_the_title($post_id),
		'slug' => get_post_field('post_name', $post_id),
		'permalink' => get_permalink($post_id),
		'featured_image' => get_post_thumbnail_id($post_id),
		'trvlr_id' => get_trvlr_attraction_id($post_id),
		'trvlr_pk' => get_trvlr_attraction_pk($post_id),
		'description' => get_trvlr_attraction_description($post_id),
		'short_description' => get_trvlr_attraction_short_description($post_id),
		'duration' => get_trvlr_attraction_duration($post_id),
		'start_time' => get_trvlr_attraction_start_time($post_id),
		'end_time' => get_trvlr_attraction_end_time($post_id),
		'is_on_sale' => get_trvlr_attraction_is_on_sale($post_id),
		'sale_description' => get_trvlr_attraction_sale_description($post_id),
		'inclusions' => get_trvlr_attraction_inclusions($post_id),
		'highlights' => get_trvlr_attraction_highlights($post_id),
		'additional_info' => get_trvlr_attraction_additional_info($post_id),
		'pricing' => get_trvlr_attraction_pricing($post_id),
		'locations' => get_trvlr_attraction_locations($post_id),
		'media' => get_trvlr_attraction_media($post_id),
		'gallery_ids' => get_trvlr_attraction_gallery_ids($post_id),
		'product_type' => get_trvlr_attraction_product_type($post_id),
	);

	return apply_filters('trvlr_all_data', $data, $post_id);
}
