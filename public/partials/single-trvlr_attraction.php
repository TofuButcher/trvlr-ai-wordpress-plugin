<?php

/**
 * Single Attraction Template
 * 
 * This template is used for single trvlr_attraction posts.
 * 
 * @package Trvlr
 */

get_header();

while (have_posts()) : the_post();

	$post_id = get_the_ID();
	$post_class = implode(' ', get_post_class('trvlr-single-attraction'));
	$title = get_the_title($post_id);
	$description = get_trvlr_attraction_description($post_id);
	$short_description = get_trvlr_attraction_short_description($post_id);
	$duration = get_trvlr_attraction_duration($post_id);
	$start_time = get_trvlr_attraction_start_time($post_id);
	$end_time = get_trvlr_attraction_end_time($post_id);
	$is_on_sale = get_trvlr_attraction_is_on_sale($post_id);
	$sale_description = get_trvlr_attraction_sale_description($post_id);
	$advertised_price_value = get_trvlr_attraction_advertised_price_value($post_id);
	$advertised_price_value_string = 'from $' . $advertised_price_value;
	$advertised_price_type = get_trvlr_attraction_advertised_price_type($post_id);
	$booking_calendar = trvlr_render_booking_calendar();

	// Use new gallery function instead of raw media array
	$media_html = trvlr_get_attraction_gallery($post_id);

	$content = get_the_content(null, false, $post_id); // Correct way to get content outside global post context if needed, or just get_the_content()

	$time_string = '';
	if ($duration) {
		$time_string = $duration;
		if ($start_time) {
			$time_string .= ', starts ' . $start_time;
		}
	} elseif ($start_time) {
		$time_string = 'starts ' . $start_time;
	}


	$single_attraction_html_top = <<<HTML
	<article id="attraction-{$post_id}" class="{$post_class}">
		<section class="trvlr-attraction__section">
			<div class="trvlr-attraction__section-inner">
				<div class="trvlr-attraction__content">
					<a class="trvlr-attraction-back-link trvlr-back-link" href="/">
						<!-- <svg>
							<use href="#icon-arrow-left"></use>
						</svg> -->
						Back
					</a>
					<h1 class="trvlr-attraction__title">{$title}</h1>
					<div class="trvlr-attraction__columns">
HTML;


	$single_attraction_time = <<<HTML
	<div class="trvlr-attraction__time trvlr-icon-text">
		<svg>
			<use href="#icon-clock"></use>
		</svg>
		<span class="trvlr-attraction__icon-text-value">{$time_string}</span>
	</div>
HTML;

	$single_attraction_sale = '';
	if ($is_on_sale) {
		$single_attraction_sale = <<<HTML
		<div class="trvlr-attraction__sale">
			<div class="trvlr-sale-badge">
				<span>
					% Special Deal
				</span>
			</div>
			<span class="trvlr-attraction__sale-description">{$sale_description}</span>
		</div>
HTML;
	}

	$single_attraction_media = <<<HTML
	<div class="trvlr-attraction__media">
		{$media_html}
	</div>
HTML;

	$single_attraction_text_content = <<<HTML
	<div class="trvlr-attraction__text-content">
		<div class="trvlr-attraction__short-description">
			{$short_description}
		</div>
		<div class="trvlr-attraction__description">
			{$description}
		</div>
		<div class="trvlr-attraction__post-content">
			{$content}
		</div>
	</div>
	HTML;

	$single_attraction_accordion_html = trvlr_get_attraction_accordion($post_id);

	$single_attraction_content_column = <<<HTML
	<div class="trvlr-attraction__content-column">
		{$single_attraction_time}
		{$single_attraction_sale}
		{$single_attraction_media}
		{$single_attraction_text_content}
		{$single_attraction_accordion_html}
	</div>
	HTML;

	$single_attraction_booking_column = <<<HTML
	<div class="trvlr-attraction__booking-column">
		<div class="trvlr-attraction__booking-column-inner">
			<div class="trvlr-attraction__price">
				{$advertised_price_value_string}
				<span class="trvlr-attraction__price-type">{$advertised_price_type}</span>
			</div>
			{$booking_calendar}
		</div>
	</div>
	HTML;

	$single_attraction_html_bottom = <<<HTML
					</div>
				</div>
			</div>
		</section>
	</article>
	HTML;

	// Concatenate all parts
	echo $single_attraction_html_top .
		$single_attraction_content_column .
		$single_attraction_booking_column .
		$single_attraction_html_bottom;

endwhile;

get_footer();
