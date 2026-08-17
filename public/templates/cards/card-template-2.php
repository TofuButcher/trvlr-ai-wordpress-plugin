<?php

if (!defined('ABSPATH')) {
	exit;
}

$post_id = isset($post_id) ? $post_id : get_the_ID();
$post_id = absint($post_id);
$permalink = get_permalink($post_id);
$title = get_trvlr_title($post_id);
$primary_term = get_trvlr_primary_term($post_id);
$price = get_trvlr_advertised_price_value($post_id);
$short_raw = get_trvlr_short_description($post_id);
$has_excerpt = $short_raw !== '' && $short_raw !== null;
$popular_markup = trvlr_popular_badge($post_id);

?>

<div class="trvlr-card trvlr-card--attraction">
	<div class="trvlr-card__main-content">
		<?php if ($popular_markup !== '') : ?>
			<div class="trvlr-card__badge-on-image">
				<?php echo $popular_markup; ?>
			</div>
		<?php endif; ?>
		<div class="trvlr-card__image-wrap">
			<?php if (has_post_thumbnail($post_id)) : ?>
				<?php echo get_the_post_thumbnail($post_id, 'image-480', array('class' => 'trvlr-card__image')); ?>
			<?php endif; ?>
		</div>
		<div class="trvlr-card__content">
			<h3 class="trvlr-card__title keep-hover">
				<a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
			</h3>
			<div class="trvlr-card__meta keep-hover">
			<?php if ($primary_term) : ?>
				<div class="trvlr-card__primary-term">
					<?php echo esc_html($primary_term->name); ?>
				</div>
			<?php endif; ?>
			</div>
			<div class="trvlr-card__footer">
				<?php echo trvlr_duration($post_id, array('icon' => false, 'class' => 'trvlr-card__duration')); ?>
				<?php echo trvlr_sale_badge($post_id, false); ?>
				<?php if ($price) : ?>
					<div class="trvlr-card__price">
						<?php echo esc_html__('From ', 'trvlr') . ' A$' . esc_html($price); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<div class="trvlr-card__extended-content trvlr-card__hover-content">
		<div class="trvlr-card__hover-content-inner">
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
		</div>
		<?php
			echo trvlr_booking_button($post_id, array(
			'label' => __('Book Now', 'trvlr'),
			'class' => ' trvlr-card__cta',
			'icon' => "arrow-right")
			);
		?>
	</div>
</div>