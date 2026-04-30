<?php

if (!defined('ABSPATH')) {
	exit;
}

$permalink = get_permalink($post_id);
$title = get_trvlr_title($post_id);
$trvlr_id = get_trvlr_id($post_id);
$duration = get_trvlr_duration($post_id);
$start_time = get_trvlr_start_time($post_id);
$is_on_sale = get_trvlr_is_on_sale($post_id);

$time_string = '';
if ($duration) {
	$time_string = $duration;
	if ($start_time) {
		$time_string .= ', starts ' . $start_time;
	}
} elseif ($start_time) {
	$time_string = 'starts ' . $start_time;
}

?>
<div class="trvlr-card trvlr-card--attraction trvlr-card--template-<?php echo esc_attr($trvlr_card_template_slug); ?>" data-trvlr-card-template="<?php echo esc_attr($trvlr_card_template_slug); ?>">
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