(function ($) {
	'use strict';

	$(document).ready(function () {

		$('.trvlr-back-link').on('click', function (e) {
			e.preventDefault();
			window.history.back(-1);
		});

		const simpleAccordions = document.querySelectorAll('.trvlr-accordion');
		simpleAccordions.forEach(accordion => {
			new SimpleAccordion(accordion);
		});

		document.querySelectorAll('[data-trvlr-tabs]').forEach(tabs => {
			new SimpleTabs(tabs);
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
		const items = this.accordion.querySelectorAll('.trvlr-accordion__item');

		items.forEach((item, index) => {
			const trigger = item.querySelector('.trvlr-accordion__trigger');
			const content = item.querySelector('.trvlr-accordion__content');

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

		const triggerId = trigger.id || `trvlr-accordion__trigger-${index}`;
		const contentId = content.id || `trvlr-accordion__content-${index}`;

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

class SimpleTabs {
	constructor(element) {
		this.element = element;
		this.tabs = Array.from(element.querySelectorAll('[role="tab"]'));
		this.panels = Array.from(element.querySelectorAll('[role="tabpanel"]'));
		this.init();
	}

	init() {
		this.tabs.forEach((tab, index) => {
			tab.addEventListener('click', () => this.activate(index));
			tab.addEventListener('keydown', event => this.onKeydown(event, index));
		});
	}

	activate(index, focus = false) {
		this.tabs.forEach((tab, tabIndex) => {
			const isActive = tabIndex === index;
			tab.classList.toggle('is-active', isActive);
			tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
			tab.tabIndex = isActive ? 0 : -1;
			if (focus && isActive) {
				tab.focus();
			}
		});

		this.panels.forEach((panel, panelIndex) => {
			panel.hidden = panelIndex !== index;
		});
	}

	onKeydown(event, index) {
		let nextIndex = index;

		if (event.key === 'ArrowRight') {
			nextIndex = (index + 1) % this.tabs.length;
		} else if (event.key === 'ArrowLeft') {
			nextIndex = (index - 1 + this.tabs.length) % this.tabs.length;
		} else if (event.key === 'Home') {
			nextIndex = 0;
		} else if (event.key === 'End') {
			nextIndex = this.tabs.length - 1;
		} else {
			return;
		}

		event.preventDefault();
		this.activate(nextIndex, true);
	}
}

/**
 * List Overflow Trim
 *
 * Hides list items in .trvlr-list-items-trim that extend past the
 * container's content box (ul padding box), ignoring li margin.
 *
 * Recalculates on layout shifts (ResizeObserver) instead of
 * polling, and batches reads before writes to avoid layout thrashing.
 */
(function () {
	'use strict';

	var EPSILON = 0.5;
	var observedUls = new WeakSet();
	var pending = new Set();
	var rafId = null;

	function getContentBoxBottom(ul) {
		var rect = ul.getBoundingClientRect();
		var styles = window.getComputedStyle(ul);
		var paddingBottom = parseFloat(styles.paddingBottom) || 0;
		return rect.bottom - paddingBottom;
	}

	function getMarginBoxBottom(item) {
		var rect = item.getBoundingClientRect();
		var marginBottom = parseFloat(window.getComputedStyle(item).marginBottom) || 0;
		return rect.bottom + marginBottom;
	}

	function trimList(ul) {
		var items = ul.querySelectorAll(':scope > li');
		if (!items.length) return;

		// reset previously hidden items
		for (var i = 0; i < items.length; i++) {
			if (items[i].style.display === 'none') items[i].style.display = '';
		}

		var contentBoxBottom = getContentBoxBottom(ul);

		// Check last item first
		var last = items[items.length - 1];
		if (getMarginBoxBottom(last) <= contentBoxBottom + EPSILON) {
			return;
		}

		// Binary search for the first overflowing item
		var lo = 0, hi = items.length - 1, firstOverflow = items.length;
		while (lo <= hi) {
			var mid = (lo + hi) >> 1;
			var bottom = getMarginBoxBottom(items[mid]);
			if (bottom > contentBoxBottom + EPSILON) {
				firstOverflow = mid;
				hi = mid - 1;
			} else {
				lo = mid + 1;
			}
		}

		// Batch the writes
		for (var j = firstOverflow; j < items.length; j++) {
			items[j].style.display = 'none';
		}
	}

	function flush() {
		rafId = null;
		pending.forEach(trimList);
		pending.clear();
	}

	function schedule(ul) {
		pending.add(ul);
		if (rafId === null) rafId = requestAnimationFrame(flush);
	}

	var ulToContainerMap = new WeakMap();

	var resizeObserver = new ResizeObserver(function (entries) {
		for (var i = 0; i < entries.length; i++) {
			var ul = ulToContainerMap.get(entries[i].target) || entries[i].target;
			schedule(ul);
		}
	});

	function observe(container) {
		var ul = container.matches('ul') ? container : container.querySelector('ul');
		if (!ul || observedUls.has(ul)) return;
		observedUls.add(ul);

		resizeObserver.observe(ul);
		if (ul.parentElement) {
			resizeObserver.observe(ul.parentElement);
			ulToContainerMap.set(ul.parentElement, ul);
		}

		schedule(ul); // initial run
	}

	function init() {
		document.querySelectorAll('.trvlr-list-items-trim').forEach(observe);
		if (document.body) {
			mo.observe(document.body, { childList: true, subtree: true });
		}
	}

	var mo = new MutationObserver(function (mutations) {
		mutations.forEach(function (m) {
			m.addedNodes.forEach(function (node) {
				if (node.nodeType !== 1) return;
				if (node.matches && node.matches('.trvlr-list-items-trim')) observe(node);
				if (node.querySelectorAll) {
					node.querySelectorAll('.trvlr-list-items-trim').forEach(observe);
				}
			});
		});
	});

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}

	window.trvlrTrimOverflowLists = function () {
		document.querySelectorAll('.trvlr-list-items-trim').forEach(observe);
	};
	document.addEventListener('trvlr:loaded', window.trvlrTrimOverflowLists);
})();
