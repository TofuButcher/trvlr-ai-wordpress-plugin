/**
 * Theme 4 — Overflow Trim
 *
 * The .trvlr-highlights container inside collapsed theme-4 cards is bound to a
 * max-height.  This script finds any li children that visually extend
 * beyond that clipped box and hides them, so they don't consume DOM space or
 * create accessibility issues.
 *
 * Triggered on DOMContentLoaded.  Exposed as window.trvlrTheme4Trim so it can
 * be re-run after dynamic content updates.
 */
(function () {
	'use strict';

	function trimOverflowingItems() {
		var containers = document.querySelectorAll(
			'.trvlr-card--theme-4:not(.trvlr-card--variant-expanded) .trvlr-highlights'
		);

		Array.prototype.forEach.call(containers, function (container) {
			var list = container.querySelector('ul');
			if (!list) return;

			var items = list.children;

			// Reset any previously hidden items so we can re-measure accurately
			Array.prototype.forEach.call(items, function (li) {
				li.style.display = '';
			});

			var containerRect = container.getBoundingClientRect();
			var containerBottom = containerRect.top + containerRect.height;

			Array.prototype.forEach.call(items, function (li) {
				var liRect = li.getBoundingClientRect();
				if (liRect.bottom > containerBottom) {
					li.style.display = 'none';
				}
			});
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', trimOverflowingItems);
	} else {
		trimOverflowingItems();
	}
    document.addEventListener('trvlr:loaded', trimOverflowingItems);

	window.trvlrTheme4Trim = trimOverflowingItems;
})();
