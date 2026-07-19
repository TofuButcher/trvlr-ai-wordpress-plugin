<?php

if (!defined('ABSPATH')) {
	exit;
}

$hero_image_id = get_post_thumbnail_id($post_id);
$hero_image = wp_get_attachment_image($hero_image_id, 'full');
$highlights_out = trvlr_highlights($post_id);
$inclusions_out = trvlr_inclusions($post_id);
$gallery_out = trvlr_gallery($post_id, array('type' => 'slider', 'variant' => 'theme-3'));
$has_gallery = $gallery_out !== '';
$loc_out = trvlr_locations($post_id);
$add_info_out = trvlr_additional_info($post_id);
$price_value = get_trvlr_advertised_price_value($post_id);
$price_type = get_trvlr_advertised_price_type($post_id);
$attraction_id = get_trvlr_id($post_id);
$group_id = get_trvlr_group_id($post_id);
$user_icon = '<svg class="trvlr-suitable-ages__icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M15.8333 17.5003V15.8337C15.8333 15.1706 15.5698 14.5349 15.1009 14.0661C14.6321 13.5972 13.9964 13.3337 13.3333 13.3337H6.66667C6.00363 13.3337 5.36793 13.5972 4.89909 14.0661C4.43025 14.5349 4.16667 15.1706 4.16667 15.8337V17.5003C4.16667 17.9606 3.79357 18.3337 3.33333 18.3337C2.8731 18.3337 2.5 17.9606 2.5 17.5003V15.8337C2.5 14.7286 2.9393 13.6691 3.7207 12.8877C4.5021 12.1063 5.5616 11.667 6.66667 11.667H13.3333C14.4384 11.667 15.4979 12.1063 16.2793 12.8877C17.0607 13.6691 17.5 14.7286 17.5 15.8337V17.5003C17.5 17.9606 17.1269 18.3337 16.6667 18.3337C16.2064 18.3337 15.8333 17.9606 15.8333 17.5003Z" fill="currentColor"/><path d="M12.4999 5.83366C12.4999 4.45295 11.3806 3.33366 9.99992 3.33366C8.61921 3.33366 7.49992 4.45295 7.49992 5.83366C7.49992 7.21437 8.61921 8.33366 9.99992 8.33366C11.3806 8.33366 12.4999 7.21437 12.4999 5.83366ZM14.1666 5.83366C14.1666 8.13485 12.3011 10.0003 9.99992 10.0003C7.69873 10.0003 5.83325 8.13485 5.83325 5.83366C5.83325 3.53247 7.69873 1.66699 9.99992 1.66699C12.3011 1.66699 14.1666 3.53247 14.1666 5.83366Z" fill="currentColor"/></svg>';
$dollar_icon = '<svg class="trvlr-sa4__summary-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M9.16675 19.1667V0.833333C9.16675 0.373096 9.53984 0 10.0001 0C10.4603 0 10.8334 0.373096 10.8334 0.833333V19.1667C10.8334 19.6269 10.4603 20 10.0001 20C9.53984 20 9.16675 19.6269 9.16675 19.1667Z" fill="currentColor"/><path d="M14.1667 12.9163C14.1667 12.3638 13.9471 11.8341 13.5564 11.4434C13.1657 11.0527 12.6359 10.833 12.0834 10.833H7.91675C6.92219 10.833 5.96864 10.4376 5.26538 9.73438C4.56212 9.03111 4.16675 8.07757 4.16675 7.08301C4.16675 6.08845 4.56212 5.1349 5.26538 4.43164C5.96864 3.72838 6.92219 3.33301 7.91675 3.33301H14.1667C14.627 3.33301 15.0001 3.7061 15.0001 4.16634C15.0001 4.62658 14.627 4.99967 14.1667 4.99967H7.91675C7.36421 4.99967 6.83447 5.21933 6.44377 5.61003C6.05307 6.00073 5.83341 6.53047 5.83341 7.08301C5.83341 7.63554 6.05307 8.16529 6.44377 8.55599C6.83447 8.94669 7.36421 9.16634 7.91675 9.16634H12.0834C13.078 9.16634 14.0315 9.56171 14.7348 10.265C15.438 10.9682 15.8334 11.9218 15.8334 12.9163C15.8334 13.9109 15.438 14.8644 14.7348 15.5677C14.0315 16.271 13.078 16.6663 12.0834 16.6663H5.00008C4.53984 16.6663 4.16675 16.2932 4.16675 15.833C4.16675 15.3728 4.53984 14.9997 5.00008 14.9997H12.0834C12.636 14.9997 13.1657 14.78 13.5564 14.3893C13.9471 13.9986 14.1667 13.4689 14.1667 12.9163Z" fill="currentColor"/></svg>';
$map_pin_icon = '<svg class="trvlr-simple-location__icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M10.4623 19.86C10.1824 20.0466 9.81776 20.0466 9.53784 19.86L10.0001 19.1667L10.4623 19.86ZM16.6667 8.33333C16.6667 6.56522 15.9647 4.86922 14.7144 3.61898C13.4642 2.36874 11.7682 1.66667 10.0001 1.66667C8.23197 1.66667 6.53597 2.36874 5.28573 3.61898C4.03548 4.86922 3.33341 6.56522 3.33341 8.33333C3.33341 10.915 5.01291 13.4204 6.85474 15.3646C7.75862 16.3187 8.66574 17.0996 9.34741 17.6424C9.60068 17.8441 9.82276 18.011 10.0001 18.1421C10.1774 18.011 10.3995 17.8441 10.6528 17.6424C11.3344 17.0996 12.2415 16.3187 13.1454 15.3646C14.9873 13.4204 16.6667 10.915 16.6667 8.33333ZM18.3334 8.33333C18.3334 11.585 16.2629 14.4963 14.3547 16.5104C13.3837 17.5354 12.4157 18.3692 11.6912 18.9461C11.3284 19.235 11.025 19.4611 10.8106 19.6159C10.7036 19.6932 10.6183 19.7531 10.5592 19.7941C10.5297 19.8145 10.5062 19.8303 10.49 19.8413C10.4819 19.8468 10.4751 19.8512 10.4705 19.8543C10.4682 19.8558 10.4661 19.8575 10.4648 19.8584L10.4631 19.8592V19.86C10.4629 19.8602 10.4623 19.86 10.0001 19.1667C9.53783 19.86 9.53727 19.8602 9.53703 19.86V19.8592L9.5354 19.8584C9.53403 19.8575 9.53194 19.8558 9.5297 19.8543C9.5251 19.8512 9.51829 19.8468 9.51017 19.8413C9.49396 19.8303 9.47047 19.8145 9.441 19.7941C9.38182 19.7531 9.29659 19.6932 9.18953 19.6159C8.97518 19.4611 8.67178 19.235 8.309 18.9461C7.58444 18.3692 6.61649 17.5354 5.64543 16.5104C3.73727 14.4963 1.66675 11.585 1.66675 8.33333C1.66675 6.1232 2.54454 4.0034 4.10734 2.44059C5.67014 0.877789 7.78994 0 10.0001 0C12.2102 0 14.33 0.877789 15.8928 2.44059C17.4556 4.0034 18.3334 6.1232 18.3334 8.33333Z" fill="currentColor"/><path d="M11.6667 8.33333C11.6667 7.41286 10.9206 6.66667 10.0001 6.66667C9.07961 6.66667 8.33341 7.41286 8.33341 8.33333C8.33341 9.25381 9.07961 10 10.0001 10C10.9206 10 11.6667 9.25381 11.6667 8.33333ZM13.3334 8.33333C13.3334 10.1743 11.841 11.6667 10.0001 11.6667C8.15913 11.6667 6.66675 10.1743 6.66675 8.33333C6.66675 6.49238 8.15913 5 10.0001 5C11.841 5 13.3334 6.49238 13.3334 8.33333Z" fill="currentColor"/></svg>';
$cancellation_icon = '<svg class="trvlr-sa4__summary-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M17.4999 9.99967C17.4999 5.85754 14.1421 2.49967 9.99992 2.49967C5.85778 2.49967 2.49992 5.85754 2.49992 9.99967C2.49992 14.1418 5.85778 17.4997 9.99992 17.4997C14.1421 17.4997 17.4999 14.1418 17.4999 9.99967ZM19.1666 9.99967C19.1666 15.0623 15.0625 19.1663 9.99992 19.1663C4.93731 19.1663 0.833252 15.0623 0.833252 9.99967C0.833252 4.93706 4.93731 0.833008 9.99992 0.833008C15.0625 0.833008 19.1666 4.93706 19.1666 9.99967Z" fill="currentColor"/><path d="M11.9108 6.91107C12.2363 6.58563 12.7638 6.58563 13.0892 6.91107C13.4146 7.23651 13.4146 7.76402 13.0892 8.08946L8.08921 13.0895C7.76377 13.4149 7.23626 13.4149 6.91083 13.0895C6.58539 12.764 6.58539 12.2365 6.91083 11.9111L11.9108 6.91107Z" fill="currentColor"/><path d="M6.91083 6.91107C7.23626 6.58563 7.76377 6.58563 8.08921 6.91107L13.0892 11.9111C13.4146 12.2365 13.4146 12.764 13.0892 13.0895C12.7638 13.4149 12.2363 13.4149 11.9108 13.0895L6.91083 8.08946C6.58539 7.76402 6.58539 7.23651 6.91083 6.91107Z" fill="currentColor"/></svg>';

?>
<article
	id="attraction-<?php echo esc_attr((string) $post_id); ?>"
	class="trvlr-single-attraction trvlr-sa4">
	<div class="trvlr-sa4__inner">
		<div class="trvlr-sa4__main">
			<section class="trvlr-sa4__hero">
				<?php if ($hero_image) : ?>
					<div class="trvlr-sa4__hero-image">
						<?php echo $hero_image; ?>
					</div>
				<?php endif; ?>
				<div class="trvlr-sa4__hero-inner">
					<a class="trvlr-sa4__back trvlr-back-link" href="<?php echo esc_url(home_url('/')); ?>">
						<svg class="trvlr-sa4__back-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
							<path d="M3.33991 10.3399C3.30941 10.3094 3.28177 10.277 3.25704 10.2432C3.24984 10.2334 3.2424 10.2237 3.23564 10.2135C3.23002 10.2051 3.22572 10.1959 3.22044 10.1873C3.21202 10.1736 3.20321 10.1601 3.19558 10.1459C3.16305 10.0851 3.14001 10.0208 3.12377 9.95528C3.10802 9.8917 3.09826 9.82554 3.09822 9.7571C3.09816 9.67529 3.11069 9.59363 3.13482 9.51472C3.1475 9.47333 3.16373 9.43359 3.18246 9.39526C3.22151 9.3152 3.27339 9.23941 3.33991 9.1729L9.17285 3.33996C9.49501 3.0178 10.0177 3.0178 10.3398 3.33996C10.6619 3.66213 10.662 4.18484 10.3398 4.50697L5.91491 8.93191H15.5893C16.0449 8.93191 16.4145 9.30149 16.4145 9.7571C16.4142 10.2123 16.0452 10.5814 15.59 10.5816H5.91422L10.3392 15.0065C10.6613 15.3286 10.6618 15.8507 10.3398 16.1728C10.0177 16.495 9.49501 16.495 9.17285 16.1728L3.33991 10.3399Z" fill="currentColor" />
						</svg>
						<?php esc_html_e('Back', 'trvlr'); ?>
					</a>
					<h1 class="trvlr-sa4__title"><?php echo esc_html(get_trvlr_title($post_id)); ?></h1>
				</div>
				<div class="trvlr-sa4__summary-container">
					<div class="trvlr-sa4__summary">
						<?php if ($price_value) : ?>
							<div class="trvlr-sa4__summary-item trvlr-sa4__summary-item--price">
								<?php echo trvlr_icon_element_kses($dollar_icon); ?>
								<span><?php echo esc_html__('from', 'trvlr'); ?> A$<?php echo esc_html($price_value); ?></span>
							</div>
						<?php endif; ?>
						<?php echo trvlr_duration($post_id); ?>
						<?php echo trvlr_simple_location($post_id, array('icon_element' => $map_pin_icon)); ?>
						<?php echo trvlr_suitable_ages($post_id, array('icon_element' => $user_icon)); ?>
						<?php echo trvlr_cancellation_policy($post_id, array('icon_element' => $cancellation_icon)); ?>
					</div>
				</div>
			</section>
			<div class="trvlr-sa4__content">
				<div class="trvlr-sa4__content-container">
					<?php if ($has_gallery) : ?>
						<section class="trvlr-sa4__gallery">
							<?php echo $gallery_out; ?>
						</section>
					<?php endif; ?>
					<section class="trvlr-sa4__intro">
						<?php echo trvlr_short_description($post_id); ?>
					</section>
					<?php if ($highlights_out !== '') : ?>
						<section class="trvlr-sa4__section">
							<h2><?php esc_html_e('Highlights', 'trvlr'); ?></h2>
							<?php echo $highlights_out; ?>
						</section>
					<?php endif; ?>
					<?php if ($inclusions_out !== '') : ?>
						<section class="trvlr-sa4__section">
							<h2><?php esc_html_e('Inclusions', 'trvlr'); ?></h2>
							<?php echo $inclusions_out; ?>
						</section>
					<?php endif; ?>
					<section class="trvlr-sa4__section">
						<h2><?php esc_html_e('Description', 'trvlr'); ?></h2>
						<?php echo trvlr_description($post_id); ?>
						<?php the_content(); ?>
					</section>
					<?php if ($loc_out !== '' || $add_info_out !== '') : ?>
						<section class="trvlr-sa4__section">
							<h2><?php esc_html_e('Additional Information', 'trvlr'); ?></h2>
							<?php echo $loc_out; ?>
							<?php echo $add_info_out; ?>
						</section>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<aside class="trvlr-sa4__sidebar" aria-label="<?php esc_attr_e('Booking', 'trvlr'); ?>">
			<div class="trvlr-sa4__sidebar-inner">
				<h2><?php esc_html_e('Check availability', 'trvlr'); ?></h2>
				<?php echo trvlr_booking_calendar($post_id); ?>
			</div>
		</aside>
	</div>
	<button
		type="button"
		class="trvlr-sa4__mobile-availability trvlr-check-availability"
		attraction-id="<?php echo esc_attr($attraction_id); ?>"
		<?php if ($group_id) : ?>attraction-group-id="<?php echo esc_attr($group_id); ?>" <?php endif; ?>>
		<span><?php esc_html_e('Check availability', 'trvlr'); ?></span>
		<svg aria-hidden="true">
			<use href="#icon-arrow-right"></use>
		</svg>
	</button>
</article>