<?php

if (!defined('ABSPATH')) {
	exit;
}

$trvlr_card_variant = (isset($trvlr_card_variant) && is_string($trvlr_card_variant) && $trvlr_card_variant !== '') ? $trvlr_card_variant : 'default';
$trvlr_card_supported_variants = array('default', 'expanded');
if (!in_array($trvlr_card_variant, $trvlr_card_supported_variants, true)) {
	$trvlr_card_variant = 'default';
}

$permalink = get_permalink($post_id);
$title = get_trvlr_title($post_id);
$price = get_trvlr_advertised_price_value($post_id);
$popular_markup = trvlr_popular_badge($post_id, array('icon' => false));
$user_icon = '<svg class="trvlr-suitable-ages__icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M15.8333 17.5003V15.8337C15.8333 15.1706 15.5698 14.5349 15.1009 14.0661C14.6321 13.5972 13.9964 13.3337 13.3333 13.3337H6.66667C6.00363 13.3337 5.36793 13.5972 4.89909 14.0661C4.43025 14.5349 4.16667 15.1706 4.16667 15.8337V17.5003C4.16667 17.9606 3.79357 18.3337 3.33333 18.3337C2.8731 18.3337 2.5 17.9606 2.5 17.5003V15.8337C2.5 14.7286 2.9393 13.6691 3.7207 12.8877C4.5021 12.1063 5.5616 11.667 6.66667 11.667H13.3333C14.4384 11.667 15.4979 12.1063 16.2793 12.8877C17.0607 13.6691 17.5 14.7286 17.5 15.8337V17.5003C17.5 17.9606 17.1269 18.3337 16.6667 18.3337C16.2064 18.3337 15.8333 17.9606 15.8333 17.5003Z" fill="currentColor"/><path d="M12.4999 5.83366C12.4999 4.45295 11.3806 3.33366 9.99992 3.33366C8.61921 3.33366 7.49992 4.45295 7.49992 5.83366C7.49992 7.21437 8.61921 8.33366 9.99992 8.33366C11.3806 8.33366 12.4999 7.21437 12.4999 5.83366ZM14.1666 5.83366C14.1666 8.13485 12.3011 10.0003 9.99992 10.0003C7.69873 10.0003 5.83325 8.13485 5.83325 5.83366C5.83325 3.53247 7.69873 1.66699 9.99992 1.66699C12.3011 1.66699 14.1666 3.53247 14.1666 5.83366Z" fill="currentColor"/></svg>';

?>
<div class="trvlr-card trvlr-card--attraction trvlr-card--theme-4<?php echo $trvlr_card_variant !== 'default' ? ' trvlr-card--variant-' . esc_attr($trvlr_card_variant) : ''; ?>">
	<div class="trvlr-card__main-content">
		<div class="trvlr-card__image-wrap">
			<?php if (has_post_thumbnail($post_id)) : ?>
				<!-- <a class="trvlr-card__image-link" href="<?php echo esc_url($permalink); ?>" aria-label="<?php echo esc_attr($title); ?>"> -->
					<?php echo get_the_post_thumbnail($post_id, 'image-480', array('class' => 'trvlr-card__image')); ?>
				<!-- </a> -->
				<?php if ($popular_markup !== '' && $trvlr_card_variant !== 'expanded') : ?>
					<div class="trvlr-card__badge-on-image">
						<?php echo $popular_markup; ?>
					</div>
				<?php endif; ?>
				<?php if ($trvlr_card_variant === 'expanded') : ?>
					<div class="trvlr-card__badge-on-image">
						<div class="trvlr-popular-badge">
							<span class="trvlr-popular-badge__text">
								Private Bespoke Tour
							</span>
						</div>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<div class="trvlr-card__content">
			<h3 class="trvlr-card__title">
				<a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
			</h3>
			<div class="trvlr-card__meta">
				<?php echo trvlr_duration($post_id); ?>
				<?php echo trvlr_suitable_ages($post_id, array('icon_element' => $user_icon)); ?>
			</div>
			<?php if ($price) : ?>
				<div class="trvlr-card__price">
					<?php echo esc_html__('starts at', 'trvlr') . ' <strong>A$' . esc_html($price) . '</strong>'; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php $trvlr_highlights_html = trvlr_highlights($post_id); ?>
	<?php if ($trvlr_highlights_html) : ?>
		<div class="trvlr-card__extended-content">
			<?php echo wp_kses_post($trvlr_highlights_html); ?>
			<?php echo trvlr_booking_button($post_id, array('label' => __('Check availability', 'trvlr'), 'class' => ' trvlr-card__cta')); ?>
		</div>
	<?php endif; ?>
</div>