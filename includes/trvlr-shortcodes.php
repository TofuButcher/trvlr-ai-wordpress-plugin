<?php

/**
 * Shortcodes for TRVLR plugin
 * 
 * @package Trvlr
 */

if (!defined('ABSPATH')) exit;

/**
 * Shortcode: Single Attraction Card
 * Usage: [trvlr_attraction_card id="123"]
 * 
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function trvlr_shortcode_attraction_card($atts)
{
	$atts = shortcode_atts(array(
		'id' => get_the_ID(),
	), $atts, 'trvlr_attraction_card');

	return trvlr_get_attraction_card($atts['id']);
}
add_shortcode('trvlr_attraction_card', 'trvlr_shortcode_attraction_card');

/**
 * Shortcode: Multiple Attraction Cards
 * Usage: [trvlr_attraction_cards posts_per_page="10" orderby="date"]
 * 
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function trvlr_shortcode_attraction_cards($atts)
{
	$atts = shortcode_atts(array(
		'posts_per_page' => -1,
		'orderby' => 'date',
		'order' => 'DESC',
		'ids' => '', // Comma-separated post IDs
	), $atts, 'trvlr_attraction_cards');

	$query_args = array(
		'posts_per_page' => intval($atts['posts_per_page']),
		'orderby' => sanitize_text_field($atts['orderby']),
		'order' => sanitize_text_field($atts['order']),
	);

	// Handle specific IDs
	if (!empty($atts['ids'])) {
		$ids = array_map('intval', explode(',', $atts['ids']));
		$query_args['post__in'] = $ids;
		$query_args['orderby'] = 'post__in';
	}

	return trvlr_get_attraction_cards($query_args);
}
add_shortcode('trvlr_attraction_cards', 'trvlr_shortcode_attraction_cards');

/**
 * Shortcode: Attraction Gallery
 * Usage: [trvlr_attraction_gallery id="123"]
 * 
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function trvlr_shortcode_attraction_gallery($atts)
{
	$atts = shortcode_atts(array(
		'id' => get_the_ID(),
	), $atts, 'trvlr_attraction_gallery');

	return trvlr_get_attraction_gallery($atts['id']);
}
add_shortcode('trvlr_attraction_gallery', 'trvlr_shortcode_attraction_gallery');

/**
 * Shortcode: Booking Calendar
 * Usage: [trvlr_booking_calendar width="100%" height="600px"]
 * 
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function trvlr_shortcode_booking_calendar($atts)
{
	$atts = shortcode_atts(array(
		'width' => '100%',
		'height' => '600px',
		'attraction_id' => get_trvlr_attraction_id(),
	), $atts, 'trvlr_booking_calendar');

	return trvlr_render_booking_calendar($atts);
}
add_shortcode('trvlr_booking_calendar', 'trvlr_shortcode_booking_calendar');
