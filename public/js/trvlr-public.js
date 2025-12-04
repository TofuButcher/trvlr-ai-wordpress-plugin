(function ($) {
	'use strict';

	$(document).ready(function () {
		// Initialize Splide galleries
		$('.attraction-slider__wrap').each(function () {
			var $wrap = $(this);
			var $main = $wrap.find('.attraction-slider').not('.attraction-slider__controls');
			var $nav = $wrap.find('.attraction-slider__controls');

			if ($main.length && $nav.length && typeof Splide !== 'undefined') {

				var mainSlider = new Splide($main[0], {
					type: 'slide',
					direction: 'ltr',
					pagination: true,
					arrows: false,
					gap: '11px',
					perPage: 1,
					perMove: 1,
					speed: 400,
					interval: 3000
				});

				var navSlider = new Splide($nav[0], {
					type: 'slide',
					direction: 'ttb',
					height: '460px', // Approximate height for vertical layout
					fixedHeight: '100px',
					fixedWidth: '140px',
					gap: '11px',
					pagination: false,
					arrows: false,
					isNavigation: true,
					wheel: true,
					breakpoints: {
						768: {
							direction: 'ltr',
							height: 'auto',
							fixedWidth: '100px',
							fixedHeight: '70px'
						}
					}
				});

				mainSlider.sync(navSlider);
				mainSlider.mount();
				navSlider.mount();
			}
		});
		const simpleAccordions = document.querySelectorAll('.simple-accordion');
		simpleAccordions.forEach(accordion => {
			new SimpleAccordion(accordion);
		});
	});


})(jQuery);

/**
 * Simple Accordion Controller
 * Minimal accordion functionality with accessibility features
 */
class SimpleAccordion {
	constructor(element, options = {}) {
		this.accordion = element;
		this.options = {
			expandFirst: true,
			independentToggle: true,
			animationDuration: 300,
			...options
		};

		this.items = [];
		this.init();
	}

	init() {
		const items = this.accordion.querySelectorAll('.accordion__item');

		items.forEach((item, index) => {
			const trigger = item.querySelector('.accordion__trigger');
			const content = item.querySelector('.accordion__content');

			if (!trigger || !content) return;

			const accordionItem = {
				item,
				trigger,
				content,
				index,
				isOpen: false
			};

			this.setupItem(accordionItem);
			this.items.push(accordionItem);
		});

		if (this.options.expandFirst && this.items.length > 0) {
			this.open(this.items[0], false);
		}
	}

	setupItem(accordionItem) {
		const { trigger, content, index } = accordionItem;

		const triggerId = trigger.id || `accordion__trigger-${index}`;
		const contentId = content.id || `accordion__content-${index}`;

		trigger.id = triggerId;
		content.id = contentId;

		trigger.setAttribute('aria-controls', contentId);
		trigger.setAttribute('aria-expanded', 'false');
		trigger.setAttribute('role', 'button');
		trigger.setAttribute('tabindex', '0');

		content.setAttribute('role', 'region');
		content.setAttribute('aria-labelledby', triggerId);
		content.style.height = '0';
		content.style.overflow = 'hidden';

		trigger.addEventListener('click', (e) => {
			e.preventDefault();
			this.toggle(accordionItem);
		});

		trigger.addEventListener('keydown', (e) => {
			if (e.key === 'Enter' || e.key === ' ') {
				e.preventDefault();
				this.toggle(accordionItem);
			}
		});
	}

	toggle(accordionItem) {
		if (accordionItem.isOpen) {
			this.close(accordionItem);
		} else {
			if (!this.options.independentToggle) {
				this.items.forEach(item => {
					if (item !== accordionItem && item.isOpen) {
						this.close(item);
					}
				});
			}
			this.open(accordionItem);
		}
	}

	open(accordionItem, animate = true) {
		const { item, trigger, content } = accordionItem;

		accordionItem.isOpen = true;
		item.classList.add('is-open');
		trigger.setAttribute('aria-expanded', 'true');

		if (animate) {
			content.style.height = 'auto';
			const height = content.scrollHeight + 'px';
			content.style.height = '0';
			content.offsetHeight;
			content.style.height = height;
			setTimeout(() => {
				content.style.height = 'auto';
			}, this.options.animationDuration);
		} else {
			content.style.height = 'auto';
		}
	}

	close(accordionItem, animate = true) {
		const { item, trigger, content } = accordionItem;

		accordionItem.isOpen = false;
		item.classList.remove('is-open');
		trigger.setAttribute('aria-expanded', 'false');

		if (animate) {
			const height = content.scrollHeight + 'px';
			content.style.height = height;
			content.offsetHeight;
			content.style.height = '0';
			setTimeout(() => {
			}, this.options.animationDuration);
		} else {
			content.style.height = '0';
		}
	}

	openItem(index) {
		if (this.items[index]) {
			this.open(this.items[index]);
		}
	}

	closeItem(index) {
		if (this.items[index]) {
			this.close(this.items[index]);
		}
	}

	closeAll() {
		this.items.forEach(item => {
			if (item.isOpen) {
				this.close(item);
			}
		});
	}
}