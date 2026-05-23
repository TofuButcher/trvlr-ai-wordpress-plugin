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
		'id'   => get_the_ID(),
		'type' => 'slider',
	), $atts, 'trvlr_gallery');

	return trvlr_gallery($atts['id'], array('type' => $atts['type']));
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

function trvlr_shortcode_booking_button($atts)
{
	$atts = shortcode_atts(array(
		'id' => get_the_ID(),
		'class' => '',
		'label' => 'Book Now',
	), $atts, 'trvlr_booking_button');

	return trvlr_booking_button($atts['id'], array('class' => $atts['class'], 'label' => $atts['label']));
}
add_shortcode('trvlr_booking_button', 'trvlr_shortcode_booking_button');

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
	$atts = shortcode_atts(array(
		'posts_per_page' => '',
		'orderby'        => '',
		'order'          => '',
		'ids'            => '',
		'id'             => '',
	), $atts ?? array(), 'trvlr_cards');

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

	if (!empty($atts['id'])) {
		$query_args['grid_id'] = $atts['id'];
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
		'posts_per_page'     => -1,
		'orderby'            => 'date',
		'order'              => 'DESC',
		'ids'                => '',
		'exclude'            => '',
		'tag'                => '',
		'tag_id'             => '',
		'tag_slug'           => '',
		'tag_relation'       => '',
		'category'           => '',
		'category_id'        => '',
		'category_slug'      => '',
		'category_relation'  => '',
		'trvlr_tag'          => '',
		'trvlr_tag_id'       => '',
		'trvlr_tag_slug'     => '',
		'trvlr_tag_relation' => '',
		'trvlr_sort'         => '',
		'meta_key'           => '',
		'meta_value'         => '',
		'meta_compare'       => '=',
		'id'                 => '',
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

	if ($atts['tag_relation'] !== '') {
		$query_args['tag_relation'] = sanitize_text_field($atts['tag_relation']);
	}

	if (!empty($atts['category'])) {
		$query_args['category'] = sanitize_text_field($atts['category']);
	}

	if (!empty($atts['category_id'])) {
		$query_args['category_id'] = sanitize_text_field($atts['category_id']);
	}

	if (!empty($atts['category_slug'])) {
		$query_args['category_slug'] = sanitize_text_field($atts['category_slug']);
	}

	if ($atts['category_relation'] !== '') {
		$query_args['category_relation'] = sanitize_text_field($atts['category_relation']);
	}

	if (!empty($atts['trvlr_tag'])) {
		$query_args['trvlr_tag'] = sanitize_text_field($atts['trvlr_tag']);
	}

	if (!empty($atts['trvlr_tag_id'])) {
		$query_args['trvlr_tag_id'] = sanitize_text_field($atts['trvlr_tag_id']);
	}

	if (!empty($atts['trvlr_tag_slug'])) {
		$query_args['trvlr_tag_slug'] = sanitize_text_field($atts['trvlr_tag_slug']);
	}

	if ($atts['trvlr_tag_relation'] !== '') {
		$query_args['trvlr_tag_relation'] = sanitize_text_field($atts['trvlr_tag_relation']);
	}

	if ($atts['trvlr_sort'] !== '') {
		$query_args['trvlr_sort'] = sanitize_key($atts['trvlr_sort']);
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

	if (!empty($atts['id'])) {
		$query_args['grid_id'] = $atts['id'];
	}

	$inherits_archive = (is_post_type_archive() || is_tax() || is_category() || is_tag())
		&& $atts['ids'] === ''
		&& $atts['exclude'] === ''
		&& $atts['tag'] === ''
		&& $atts['tag_id'] === ''
		&& $atts['tag_slug'] === ''
		&& $atts['tag_relation'] === ''
		&& $atts['category'] === ''
		&& $atts['category_id'] === ''
		&& $atts['category_slug'] === ''
		&& $atts['category_relation'] === ''
		&& $atts['trvlr_tag'] === ''
		&& $atts['trvlr_tag_id'] === ''
		&& $atts['trvlr_tag_slug'] === ''
		&& $atts['trvlr_tag_relation'] === ''
		&& $atts['trvlr_sort'] === ''
		&& $atts['meta_key'] === ''
		&& $atts['id'] === ''
		&& (int) $atts['posts_per_page'] === -1
		&& $atts['orderby'] === 'date'
		&& $atts['order'] === 'DESC'
		&& $atts['meta_compare'] === '=';

	if ($inherits_archive && $atts['meta_value'] === '') {
		return trvlr_cards(array());
	}

	return trvlr_cards($query_args);
}
add_shortcode('trvlr_attraction_cards', 'trvlr_shortcode_attraction_cards');

function trvlr_shortcode_attraction_filter($atts)
{
	return trvlr_attraction_filter($atts ?? array());
}
add_shortcode('trvlr_attraction_filter', 'trvlr_shortcode_attraction_filter');

function trvlr_shortcode_attraction_sort($atts)
{
	return trvlr_attraction_sort($atts ?? array());
}
add_shortcode('trvlr_attraction_sort', 'trvlr_shortcode_attraction_sort');
add_shortcode('trvlr_sort', 'trvlr_shortcode_attraction_sort');

function trvlr_shortcode_attraction_gallery($atts)
{
	$atts = shortcode_atts(array(
		'id'   => get_the_ID(),
		'type' => 'slider',
	), $atts, 'trvlr_attraction_gallery');

	return trvlr_gallery($atts['id'], array('type' => $atts['type']));
}
add_shortcode('trvlr_attraction_gallery', 'trvlr_shortcode_attraction_gallery');

function trvlr_shortcode_attraction_template($atts)
{
	$atts = shortcode_atts(array(
		'id' => '',
		'template' => '',
	), $atts, 'trvlr_attraction_template');

	$post_id = $atts['id'] !== '' ? absint($atts['id']) : null;
	$template = $atts['template'] !== '' ? $atts['template'] : null;

	return trvlr_single_attraction_markup($post_id, $template);
}
add_shortcode('trvlr_attraction_template', 'trvlr_shortcode_attraction_template');

function trvlr_shortcode_payment_confirmation($atts)
{
	return trvlr_payment_confirmation_markup();
}
add_shortcode('trvlr_payment_confirmation', 'trvlr_shortcode_payment_confirmation');
