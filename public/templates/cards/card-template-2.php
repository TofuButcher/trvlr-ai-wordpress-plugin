<?php

if (!defined('ABSPATH')) {
	exit;
}

$permalink = get_permalink($post_id);
$title = get_trvlr_title($post_id);
$trvlr_id = get_trvlr_id($post_id);
$duration_raw = get_trvlr_duration($post_id);
$duration_display = apply_filters('trvlr_duration', $duration_raw, $post_id);
$is_on_sale = get_trvlr_is_on_sale($post_id);
$category = get_trvlr_primary_term($post_id);
$price = get_trvlr_advertised_price_value($post_id);
$short_raw = get_trvlr_short_description($post_id);
$has_excerpt = $short_raw !== '' && $short_raw !== null;
$popular_markup = trvlr_popular_badge($post_id);

?>
<div class="trvlr-card trvlr-card--attraction">
	<div class="trvlr-card__image-wrap">
		<?php if (has_post_thumbnail($post_id)) : ?>
			<?php echo get_the_post_thumbnail($post_id, 'medium', array('class' => 'trvlr-card__image')); ?>
			<?php if ($popular_markup !== '') : ?>
				<div class="trvlr-card__badge-on-image">
					<?php echo $popular_markup; ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<div class="trvlr-card__content">
		<?php if ($popular_markup !== '') : ?>
			<div class="trvlr-card__popular-inline trvlr-card__hover-only" aria-hidden="true">
				<?php echo $popular_markup; ?>
			</div>
		<?php endif; ?>
		<h3 class="trvlr-card__title">
			<a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
		</h3>
		<?php if ($category) : ?>
			<div class="trvlr-card__tag">
				<?php echo esc_html($category->name); ?>
			</div>
		<?php endif; ?>
		<?php if ($has_excerpt) : ?>
			<div class="trvlr-card__excerpt trvlr-card__hover-only">
				<div class="trvlr-card__excerpt-inner">
					<?php echo wp_kses_post($short_raw); ?>
				</div>
			</div>
			<a class="trvlr-card__read-more trvlr-card__hover-only" href="<?php echo esc_url($permalink); ?>">
				<?php esc_html_e('Read more', 'trvlr'); ?>
			</a>
		<?php endif; ?>
		<div class="trvlr-card__footer">
			<?php if ($duration_display !== '' && $duration_display !== null) : ?>
				<div class="trvlr-card__duration">
					<?php echo esc_html($duration_display); ?>
				</div>
			<?php endif; ?>
			<div class="trvlr-card__price">
				<?php if ($is_on_sale) : ?>
					<?php echo trvlr_sale_badge($post_id, false, false); ?>
				<?php endif; ?>
				<?php if ($price) : ?>
					<?php echo 'from A$' . esc_html($price); ?>
				<?php endif; ?>
			</div>
		</div>
		<button type="button" class="trvlr-card__book-bar trvlr-card__hover-only trvlr-book-now" attraction-id="<?php echo esc_attr($trvlr_id); ?>">
			<span class="trvlr-card__book-bar-label"><?php esc_html_e('Book Now', 'trvlr'); ?></span>
			<span class="trvlr-card__book-bar-icon" aria-hidden="true">
				<svg>
					<use href="#icon-arrow-right"></use>
				</svg>
			</span>
		</button>
	</div>
</div>