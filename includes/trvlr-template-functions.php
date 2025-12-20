<?php

if (!defined('ABSPATH')) exit;

function trvlr_title($post_id = null, $level = 1)
{
	$post_id = $post_id ?: get_the_ID();
	$title = get_trvlr_title($post_id);
	$tag = 'h' . absint($level);

	$output = "<{$tag} class=\"trvlr-title\">" . esc_html($title) . "</{$tag}>";
	return apply_filters('trvlr_title', $output, $post_id, $level);
}

function trvlr_duration($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$duration = get_trvlr_duration($post_id);

	if (!$duration || $duration === '0' || $duration === '0-0-0') {
		return '';
	}

	ob_start();
?>
	<div class="trvlr-duration">
		<svg class="trvlr-duration__icon">
			<use href="#icon-clock"></use>
		</svg>
		<span class="trvlr-duration__value"><?php echo esc_html($duration); ?></span>
	</div>
<?php
	return apply_filters('trvlr_duration', ob_get_clean(), $post_id);
}

function trvlr_sale($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$is_on_sale = get_trvlr_is_on_sale($post_id);

	if (!$is_on_sale) {
		return '';
	}

	$sale_description = get_trvlr_sale_description($post_id);

	ob_start();
?>
	<div class="trvlr-sale">
		<?php echo trvlr_sale_badge($post_id); ?>
		<?php if ($sale_description) : ?>
			<?php echo trvlr_sale_description($post_id); ?>
		<?php endif; ?>
	</div>
<?php
	return apply_filters('trvlr_sale', ob_get_clean(), $post_id);
}

function trvlr_popular_badge($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$attraction_tags = get_trvlr_attraction_tags($post_id);

	$is_popular = false;
	if (is_array($attraction_tags)) {
		foreach ($attraction_tags as $term) {
			if (is_object($term) && isset($term->slug) && $term->slug === 'popular') {
				$is_popular = true;
				break;
			}
		}
	}

	if ($is_popular) {
		return '<div class="trvlr-popular-badge">
			<svg class="trvlr-icon trvlr-popular-badge__icon">
				<use href="#icon-star"></use>
			</svg>
			<span class="trvlr-popular-badge__text">Popular</span>
		</div>';
	}
	return '';
}

function trvlr_sale_badge($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$is_on_sale = get_trvlr_is_on_sale($post_id);

	if (!$is_on_sale) {
		return '';
	}

	$output = '<div class="trvlr-sale__badge"><span>% Special Deal</span></div>';
	return apply_filters('trvlr_sale_badge', $output, $post_id);
}

function trvlr_sale_description($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$description = get_trvlr_sale_description($post_id);

	if (!$description) {
		return '';
	}

	$output = '<span class="trvlr-sale__description">' . esc_html($description) . '</span>';
	return apply_filters('trvlr_sale_description', $output, $post_id);
}

function trvlr_gallery($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$media_ids = get_trvlr_media($post_id, true);
	$gallery_ids = array_unique(array_filter($media_ids));

	if (empty($gallery_ids)) {
		return '';
	}

	if (count($gallery_ids) === 1) {
		$output = '<div class="trvlr-gallery trvlr-gallery--single">' . wp_get_attachment_image($gallery_ids[0], 'large') . '</div>';
		return apply_filters('trvlr_gallery', $output, $post_id, $gallery_ids);
	}

	ob_start();
	$main_id = 'trvlr-main-slider-' . $post_id;
	$nav_id = 'trvlr-nav-slider-' . $post_id;
?>
	<div class="trvlr-gallery trvlr-gallery--slider">
		<div id="<?php echo esc_attr($main_id); ?>" class="trvlr-gallery__main splide">
			<div class="splide__track">
				<ul class="splide__list">
					<?php foreach ($gallery_ids as $image_id) : ?>
						<li class="splide__slide">
							<?php echo wp_get_attachment_image($image_id, 'large'); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<div id="<?php echo esc_attr($nav_id); ?>" class="trvlr-gallery__nav splide">
			<div class="splide__track">
				<ul class="splide__list">
					<?php foreach ($gallery_ids as $image_id) : ?>
						<li class="splide__slide">
							<?php echo wp_get_attachment_image($image_id, 'thumbnail'); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
<?php
	return apply_filters('trvlr_gallery', ob_get_clean(), $post_id, $gallery_ids);
}

function trvlr_short_description($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$content = get_trvlr_short_description($post_id);

	if (!$content) {
		return '';
	}

	$output = '<div class="trvlr-short-description">' . $content . '</div>';
	return apply_filters('trvlr_short_description', $output, $post_id);
}

function trvlr_description($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$content = get_trvlr_description($post_id);

	if (!$content) {
		return '';
	}

	$output = '<div class="trvlr-description">' . $content . '</div>';
	return apply_filters('trvlr_description', $output, $post_id);
}

function trvlr_inclusions($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$content = get_trvlr_inclusions($post_id);

	if (!$content) {
		return '';
	}

	$output = '<div class="trvlr-inclusions">' . $content . '</div>';
	return apply_filters('trvlr_inclusions', $output, $post_id);
}

function trvlr_locations($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$locations = get_trvlr_locations($post_id);
	$start_time = get_trvlr_start_time($post_id);
	$end_time = get_trvlr_end_time($post_id);

	if (empty($locations)) {
		return '';
	}

	ob_start();
?>
	<div class="trvlr-locations">
		<?php foreach ($locations as $loc) : ?>
			<?php
			$addr = isset($loc['address']) ? $loc['address'] : '';
			$type = isset($loc['type']) ? $loc['type'] : '';

			if (!$addr) continue;

			$time_suffix = '';
			if (stripos($type, 'Start') !== false && $start_time) {
				$time_suffix = ' at ' . $start_time;
			} elseif (stripos($type, 'End') !== false && $end_time) {
				$time_suffix = ' at ' . $end_time;
			}

			$label = $type ? '<strong>' . esc_html($type) . ':</strong> ' : '';
			?>
			<div class="trvlr-locations__item">
				<?php echo $label . esc_html($addr) . esc_html($time_suffix); ?>
			</div>
		<?php endforeach; ?>
	</div>
<?php
	return apply_filters('trvlr_locations', ob_get_clean(), $post_id);
}

function trvlr_additional_info($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$content = get_trvlr_additional_info($post_id);

	if (!$content) {
		return '';
	}

	$output = '<div class="trvlr-additional-info">' . $content . '</div>';
	return apply_filters('trvlr_additional_info', $output, $post_id);
}

function trvlr_accordion($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$inclusions = get_trvlr_inclusions($post_id);
	$locations = trvlr_locations($post_id);
	$additional_info = get_trvlr_additional_info($post_id);

	$accordion_items = [];

	if ($inclusions) {
		$accordion_items[] = [
			'title' => 'Inclusions',
			'content' => $inclusions,
		];
	}
	if ($locations) {
		$accordion_items[] = [
			'title' => 'Start & End Locations',
			'content' => $locations,
		];
	}
	if ($additional_info) {
		$accordion_items[] = [
			'title' => 'Additional Info',
			'content' => $additional_info,
		];
	}

	if (empty($accordion_items)) {
		return '';
	}

	ob_start();
?>
	<div class="trvlr-accordion">
		<?php foreach ($accordion_items as $item) : ?>
			<div class="trvlr-accordion__item">
				<button class="trvlr-accordion__trigger">
					<span class="trvlr-accordion__title"><?php echo esc_html($item['title']); ?></span>
					<div class="trvlr-accordion__icon">
						<svg class="trvlr-accordion__icon-open">
							<use href="#icon-plus"></use>
						</svg>
						<svg class="trvlr-accordion__icon-close">
							<use href="#icon-minus"></use>
						</svg>
					</div>
				</button>
				<div class="trvlr-accordion__content">
					<?php echo $item['content']; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
<?php
	return apply_filters('trvlr_accordion', ob_get_clean(), $post_id);
}

function trvlr_advertised_price($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_trvlr_advertised_price_value($post_id);
	$type = get_trvlr_advertised_price_type($post_id);

	if (!$value) {
		return '';
	}

	ob_start();
?>
	<div class="trvlr-price">
		<span class="trvlr-price__value">from $<?php echo esc_html($value); ?></span>
		<?php if ($type) : ?>
			<span class="trvlr-price__type"><?php echo esc_html($type); ?></span>
		<?php endif; ?>
	</div>
<?php
	return apply_filters('trvlr_advertised_price', ob_get_clean(), $post_id);
}

function trvlr_booking_calendar($post_id = null, $args = array())
{
	$post_id = $post_id ?: get_the_ID();
	$base_iframe_url = get_trvlr_base_domain();

	$defaults = array(
		'width' => '450px',
		'height' => '600px',
		'attraction_id' => get_trvlr_id($post_id),
	);
	$args = wp_parse_args($args, $defaults);

	if (empty($base_iframe_url)) {
		return '<p>Please configure the trvlr organisation ID in the plugin settings.</p>';
	}

	if (empty($args['attraction_id'])) {
		return '<p>Sorry this attraction is not available for booking. No Trvlr AI ID found.</p>';
	}

	ob_start();
?>
	<div class="trvlr-booking-calendar">
		<iframe
			class="trvlr-booking-calendar__iframe"
			style="width: <?php echo esc_attr($args['width']); ?>; height: <?php echo esc_attr($args['height']); ?>;"
			frameborder="0"
			src="<?php echo esc_url($base_iframe_url); ?>/date-picker2/index.html?attr_id=<?php echo esc_attr($args['attraction_id']); ?>"
			title="Booking Calendar"></iframe>
	</div>
<?php
	return apply_filters('trvlr_booking_calendar', ob_get_clean(), $post_id, $args);
}

function trvlr_card($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();

	if (!$post_id || get_post_type($post_id) !== 'trvlr_attraction') {
		return '';
	}

	$permalink = get_permalink($post_id);
	$title = get_trvlr_title($post_id);
	$trvlr_id = get_trvlr_id($post_id);
	$duration = get_trvlr_duration($post_id);
	$start_time = get_trvlr_start_time($post_id);
	$is_on_sale = get_trvlr_is_on_sale($post_id);
	$advertised_price_value = get_trvlr_advertised_price_value($post_id);
	$advertised_price_type = get_trvlr_advertised_price_type($post_id);

	$time_string = '';
	if ($duration) {
		$time_string = $duration;
		if ($start_time) {
			$time_string .= ', starts ' . $start_time;
		}
	} elseif ($start_time) {
		$time_string = 'starts ' . $start_time;
	}

	ob_start();
?>
	<div class="trvlr-card trvlr-card--attraction">
		<div class="trvlr-card__image-wrap">
			<?php if (has_post_thumbnail($post_id)) : ?>
				<?php echo get_the_post_thumbnail($post_id, 'medium', array('class' => 'trvlr-card__image')); ?>
				<?php echo trvlr_popular_badge($post_id); ?>
			<?php endif; ?>
		</div>
		<div class="trvlr-card__content">
			<h3 class="trvlr-title trvlr-card__title">
				<a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
			</h3>
			<?php if ($time_string) : ?>
				<div class="trvlr-card__meta">
					<?php echo trvlr_duration($post_id); ?>
				</div>
			<?php endif; ?>
			<div class="trvlr-card__footer">
				<div class="trvlr-card__price">
					<?php if ($is_on_sale) : ?>
						<?php echo trvlr_sale_badge($post_id); ?>
					<?php endif; ?>
					<?php echo trvlr_advertised_price($post_id); ?>
				</div>
				<button class="trvlr-card__button trvlr-book-now" attraction-id="<?php echo esc_attr($trvlr_id); ?>">
					<span>Book Now</span>
					<svg>
						<use href="#icon-arrow-right"></use>
					</svg>
				</button>
			</div>
		</div>
	</div>
<?php
	return apply_filters('trvlr_card', ob_get_clean(), $post_id);
}

function trvlr_cards($args = array())
{
	$use_main_query = false;

	if (empty($args) && (is_post_type_archive() || is_tax() || is_category() || is_tag())) {
		$use_main_query = true;
	}

	ob_start();

	if ($use_main_query) {
		echo '<div class="trvlr-cards-container"><div class="trvlr-cards">';
		if (have_posts()) {
			while (have_posts()) {
				the_post();
				echo trvlr_card(get_the_ID());
			}
		} else {
			echo '<p>' . esc_html__('No posts found.', 'trvlr') . '</p>';
		}
		echo '</div></div>';
	} else {
		$defaults = array(
			'post_type' => 'trvlr_attraction',
			'posts_per_page' => 16,
			'post_status' => 'publish',
		);

		$query_args = wp_parse_args($args, $defaults);
		$query = new WP_Query($query_args);

		echo '<div class="trvlr-cards-container"><div class="trvlr-cards">';
		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				echo trvlr_card(get_the_ID());
			}
			wp_reset_postdata();
		} else {
			echo '<p>' . esc_html__('No posts found.', 'trvlr') . '</p>';
		}
		echo '</div></div>';
	}

	return apply_filters('trvlr_cards', ob_get_clean(), $args);
}

function trvlr_render_booking_calendar($atts = array())
{
	return trvlr_booking_calendar(null, $atts);
}

function trvlr_get_attraction_card($post_id = null)
{
	return trvlr_card($post_id);
}

function trvlr_attraction_card($post_id = null)
{
	echo trvlr_card($post_id);
}

function trvlr_get_attraction_cards($args = array())
{
	return trvlr_cards($args);
}

function trvlr_attraction_cards($args = array())
{
	echo trvlr_cards($args);
}

function trvlr_get_attraction_gallery($post_id = null)
{
	return trvlr_gallery($post_id);
}

function trvlr_attraction_gallery($post_id = null)
{
	echo trvlr_gallery($post_id);
}

function trvlr_get_formatted_attraction_locations($post_id = null)
{
	return trvlr_locations($post_id);
}

function trvlr_get_attraction_accordion($post_id = null)
{
	return trvlr_accordion($post_id);
}
