<?php

if (!defined('ABSPATH')) exit;

function trvlr_shortcode_title($atts)
{
	$atts = shortcode_atts(array(
		'id' => get_the_ID(),
		'level' => 1,
	), $atts, 'trvlr_title');
	
	return trvlr_title($atts['id'], $atts['level']);
}
add_shortcode('trvlr_title', 'trvlr_shortcode_title');

function trvlr_shortcode_duration($atts)
{
	$atts = shortcode_atts(array(
		'id' => get_the_ID(),
	), $atts, 'trvlr_duration');
	
	return trvlr_duration($atts['id']);
}
add_shortcode('trvlr_duration', 'trvlr_shortcode_duration');

function trvlr_shortcode_sale($atts)
{
	$atts = shortcode_atts(array(
		'id' => get_the_ID(),
	), $atts, 'trvlr_sale');
	
	return trvlr_sale($atts['id']);
}
add_shortcode('trvlr_sale', 'trvlr_shortcode_sale');

function trvlr_shortcode_sale_badge($atts)
{
	$atts = shortcode_atts(array(
		'id' => get_the_ID(),
	), $atts, 'trvlr_sale_badge');
	
	return trvlr_sale_badge($atts['id']);
}
add_shortcode('trvlr_sale_badge', 'trvlr_shortcode_sale_badge');

function trvlr_shortcode_sale_description($atts)
{
	$atts = shortcode_atts(array(
		'id' => get_the_ID(),
	), $atts, 'trvlr_sale_description');
	
	return trvlr_sale_description($atts['id']);
}
add_shortcode('trvlr_sale_description', 'trvlr_shortcode_sale_description');

function trvlr_shortcode_gallery($atts)
{
	$atts = shortcode_atts(array(
		'id' => get_the_ID(),
	), $atts, 'trvlr_gallery');
	
	return trvlr_gallery($atts['id']);
}
add_shortcode('trvlr_gallery', 'trvlr_shortcode_gallery');

function trvlr_shortcode_short_description($atts)
{
	$atts = shortcode_atts(array(
		'id' => get_the_ID(),
	), $atts, 'trvlr_short_description');
	
	return trvlr_short_description($atts['id']);
}
add_shortcode('trvlr_short_description', 'trvlr_shortcode_short_description');

function trvlr_shortcode_description($atts)
{
	$atts = shortcode_atts(array(
		'id' => get_the_ID(),
	), $atts, 'trvlr_description');
	
	return trvlr_description($atts['id']);
}
add_shortcode('trvlr_description', 'trvlr_shortcode_description');

function trvlr_shortcode_accordion($atts)
{
	$atts = shortcode_atts(array(
		'id' => get_the_ID(),
	), $atts, 'trvlr_accordion');
	
	return trvlr_accordion($atts['id']);
}
add_shortcode('trvlr_accordion', 'trvlr_shortcode_accordion');

function trvlr_shortcode_inclusions($atts)
{
	$atts = shortcode_atts(array(
		'id' => get_the_ID(),
	), $atts, 'trvlr_inclusions');
	
	return trvlr_inclusions($atts['id']);
}
add_shortcode('trvlr_inclusions', 'trvlr_shortcode_inclusions');

function trvlr_shortcode_locations($atts)
{
	$atts = shortcode_atts(array(
		'id' => get_the_ID(),
	), $atts, 'trvlr_locations');
	
	return trvlr_locations($atts['id']);
}
add_shortcode('trvlr_locations', 'trvlr_shortcode_locations');

function trvlr_shortcode_additional_info($atts)
{
	$atts = shortcode_atts(array(
		'id' => get_the_ID(),
	), $atts, 'trvlr_additional_info');
	
	return trvlr_additional_info($atts['id']);
}
add_shortcode('trvlr_additional_info', 'trvlr_shortcode_additional_info');

function trvlr_shortcode_advertised_price($atts)
{
	$atts = shortcode_atts(array(
		'id' => get_the_ID(),
	), $atts, 'trvlr_advertised_price');
	
	return trvlr_advertised_price($atts['id']);
}
add_shortcode('trvlr_advertised_price', 'trvlr_shortcode_advertised_price');

function trvlr_shortcode_booking_calendar($atts)
{
	$atts = shortcode_atts(array(
		'id' => get_the_ID(),
		'width' => '450px',
		'height' => '600px',
	), $atts, 'trvlr_booking_calendar');
	
	$args = array(
		'width' => $atts['width'],
		'height' => $atts['height'],
	);
	
	return trvlr_booking_calendar($atts['id'], $args);
}
add_shortcode('trvlr_booking_calendar', 'trvlr_shortcode_booking_calendar');

function trvlr_shortcode_card($atts)
{
	$atts = shortcode_atts(array(
		'id' => get_the_ID(),
	), $atts, 'trvlr_card');
	
	return trvlr_card($atts['id']);
}
add_shortcode('trvlr_card', 'trvlr_shortcode_card');

function trvlr_shortcode_cards($atts)
{
	if (empty($atts)) {
		return trvlr_cards(array());
	}
	
	$atts = shortcode_atts(array(
		'posts_per_page' => '',
		'orderby' => '',
		'order' => '',
		'ids' => '',
	), $atts, 'trvlr_cards');
	
	$query_args = array();
	
	if ($atts['posts_per_page'] !== '') {
		$query_args['posts_per_page'] = intval($atts['posts_per_page']);
	}
	
	if ($atts['orderby'] !== '') {
		$query_args['orderby'] = sanitize_text_field($atts['orderby']);
	}
	
	if ($atts['order'] !== '') {
		$query_args['order'] = sanitize_text_field($atts['order']);
	}
	
	if (!empty($atts['ids'])) {
		$ids = array_map('intval', explode(',', $atts['ids']));
		$query_args['post__in'] = $ids;
		$query_args['orderby'] = 'post__in';
	}
	
	return trvlr_cards($query_args);
}
add_shortcode('trvlr_cards', 'trvlr_shortcode_cards');

function trvlr_shortcode_attraction_card($atts)
{
	$atts = shortcode_atts(array(
		'id' => get_the_ID(),
	), $atts, 'trvlr_attraction_card');
	
	return trvlr_card($atts['id']);
}
add_shortcode('trvlr_attraction_card', 'trvlr_shortcode_attraction_card');

function trvlr_shortcode_attraction_cards($atts)
{
	$atts = shortcode_atts(array(
		'posts_per_page' => -1,
		'orderby' => 'date',
		'order' => 'DESC',
		'ids' => '',
		'exclude' => '',
		'tag' => '',
		'tag_id' => '',
		'tag_slug' => '',
		'tag_relation' => 'AND',
		'meta_key' => '',
		'meta_value' => '',
		'meta_compare' => '=',
	), $atts, 'trvlr_attraction_cards');
	
	$query_args = array(
		'posts_per_page' => intval($atts['posts_per_page']),
		'orderby' => sanitize_text_field($atts['orderby']),
		'order' => sanitize_text_field($atts['order']),
	);
	
	if (!empty($atts['ids'])) {
		$ids = array_map('intval', explode(',', $atts['ids']));
		$query_args['post__in'] = $ids;
		$query_args['orderby'] = 'post__in';
	}
	
	if (!empty($atts['exclude'])) {
		$query_args['exclude'] = sanitize_text_field($atts['exclude']);
	}
	
	if (!empty($atts['tag'])) {
		$query_args['tag'] = sanitize_text_field($atts['tag']);
	}
	
	if (!empty($atts['tag_id'])) {
		$query_args['tag_id'] = sanitize_text_field($atts['tag_id']);
	}
	
	if (!empty($atts['tag_slug'])) {
		$query_args['tag_slug'] = sanitize_text_field($atts['tag_slug']);
	}
	
	if (!empty($atts['tag_relation'])) {
		$query_args['tag_relation'] = sanitize_text_field($atts['tag_relation']);
	}
	
	if (!empty($atts['meta_key'])) {
		$query_args['meta_key'] = sanitize_text_field($atts['meta_key']);
		if (!empty($atts['meta_value'])) {
			$query_args['meta_value'] = sanitize_text_field($atts['meta_value']);
		}
		if (!empty($atts['meta_compare'])) {
			$query_args['meta_compare'] = sanitize_text_field($atts['meta_compare']);
		}
	}
	
	return trvlr_cards($query_args);
}
add_shortcode('trvlr_attraction_cards', 'trvlr_shortcode_attraction_cards');

function trvlr_shortcode_attraction_gallery($atts)
{
	$atts = shortcode_atts(array(
		'id' => get_the_ID(),
	), $atts, 'trvlr_attraction_gallery');
	
	return trvlr_gallery($atts['id']);
}
add_shortcode('trvlr_attraction_gallery', 'trvlr_shortcode_attraction_gallery');
